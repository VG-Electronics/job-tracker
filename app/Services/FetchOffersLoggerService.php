<?php

namespace App\Services;

class FetchOffersLoggerService
{
    private string $logPath = '';
    private array $lines = [];

    public function init(): void
    {
        $datetime = now()->format('Y-m-d_H-i-s');
        $dir = storage_path('logs/fetch_offers');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->logPath = $dir . DIRECTORY_SEPARATOR . $datetime . '.log';
        $this->lines = [];
        $this->flush();
    }

    public function log(string $message): void
    {
        $this->lines[] = '[' . now()->format('H:i:s') . '] ' . $message;
        $this->flush();
    }

    public function logError(string $message): void
    {
        $this->lines[] = '[' . now()->format('H:i:s') . '] [BŁĄD] ' . $message;
        $this->flush();
    }

    public function listLogs(): array
    {
        $dir = storage_path('logs/fetch_offers');

        if (!is_dir($dir)) {
            return [];
        }

        $files = glob($dir . DIRECTORY_SEPARATOR . '*.log') ?: [];
        rsort($files);

        return array_map(fn(string $path) => basename($path), $files);
    }

    public function readLog(string $filename): ?string
    {
        $path = storage_path('logs/fetch_offers/' . $filename);

        if (!file_exists($path)) {
            return null;
        }

        return file_get_contents($path) ?: '';
    }

    private function flush(): void
    {
        if ($this->logPath === '') {
            return;
        }

        file_put_contents($this->logPath, implode("\n", $this->lines));
    }
}
