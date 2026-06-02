<?php

namespace App\Services;

use App\Contracts\AiApiInterface;

class AiService
{
    public function __construct(
        private readonly AiApiInterface $aiApi,
    ) {}

    public function prepareChunk(array $offers, ?string $additionalInstruction = null): array
    {
        $prompt = $this->buildPrompt($offers, $additionalInstruction);
        return $this->aiApi->send($prompt) ?? [];
    }

    private function buildPrompt(array $offers, ?string $additionalInstruction = null): string
    {
        $offersJson = json_encode($offers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filterSection = $additionalInstruction
            ? "\n\nCRITICAL — MANDATORY FILTER RULES (apply BEFORE processing any offer):\n{$additionalInstruction}\nAny offer that violates the above rules MUST be completely excluded from the output JSON — do NOT include it in the \"offers\" array at all.\n"
            : '';

        return <<<PROMPT
You are a job offer parser. Given the following job offers in raw format, extract structured data and return a JSON object with an "offers" key containing an array of parsed offers.
{$filterSection}
Use web search to enter the given URLs to get and provide the best, meaningful data. Title and description must be in Polish language.

For each offer extract:
- title: job title, eg. "Senior PHP Developer (Laravel, REST API)" (string)
- company: name of the hiring company (the employer), not the recruitment agency (string or null)
- recruitment_company: name of the recruitment/staffing agency conducting the recruitment, if different from the hiring company (string or null)
- description: brief description about the role, requirements, company and its business branch (string, max 500 chars)
- url: the original offer URL, don't change it (string)
- min_salary: minimum salary as integer in PLN, converted from any currency (integer or null)
- max_salary: maximum salary as integer in PLN, converted from any currency (integer or null)
- salary_type: one of "hourly", "monthly", "annually" (string or null)
- status: always set to "new" (string)

Currency conversion rates to use: 1 EUR = 4.25 PLN, 1 USD = 3.95 PLN, 1 GBP = 5.05 PLN, 1 CHF = 4.45 PLN.
If salary is not mentioned set min_salary, max_salary and salary_type to null.

Return ONLY a valid JSON object like: {"offers": [...]}

Raw offers data:
{$offersJson}
PROMPT;
    }
}
