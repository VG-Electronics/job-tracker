<?php

namespace App\Contracts;

interface AiApiInterface
{
    public function send(string $prompt): array;
}
