<?php

namespace App\AI;

use App\Contracts\AiApiInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiApi implements AiApiInterface
{
    public function send(string $prompt): array
    {
        try {
            $response = Http::withToken(config('services.openai.key'))
                ->connectTimeout(10)
                ->timeout(120)
                ->post('https://api.openai.com/v1/responses', [
                'model' => config('services.openai.model'),
                'tools' => [['type' => 'web_search']],
                'input' => $prompt,
            ]);
        } catch (ConnectionException $e) {
            throw new RuntimeException('OpenAI connection timeout — sprawdź DNS/sieć kontenera: ' . $e->getMessage(), 0, $e);
        }

        if ($response->failed()) {
            throw new RuntimeException(
                'OpenAI API error: ' . $response->status() . ' — ' . $response->body()
            );
        }

        $output = $response->json('output', []);

        $message = collect($output)->firstWhere('type', 'message');
        if (!$message || empty($message['content'][0]['text'])) {
            throw new RuntimeException('OpenAI nie zwróciło wiadomości. Output: ' . json_encode($output));
        }
        $output = $message['content'][0]['text'];

        $content = trim($output);

        // Extract just the JSON object — handles markdown fences, leading/trailing text
        $start = strpos($content, '{');
        $end = strrpos($content, '}');
        if ($start === false || $end === false || $end <= $start) {
            throw new RuntimeException('OpenAI nie zwróciło obiektu JSON. Treść: ' . mb_substr($content, 0, 300));
        }
        $content = substr($content, $start, $end - $start + 1);

        // Remove control characters that are illegal in JSON strings
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);

        $output = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if (!array_key_exists('offers', $output)) {
            throw new RuntimeException(
                'OpenAI zwróciło JSON bez klucza "offers". Klucze: [' . implode(', ', array_keys($output)) . ']. Treść: ' . mb_substr($content, 0, 300)
            );
        }

        return $output['offers'];
    }
}
