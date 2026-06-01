<?php

namespace App\Http\Controllers;

use App\Services\FetchOffersLoggerService;
use Illuminate\Http\JsonResponse;

class LogsController extends Controller
{
    public function __construct(
        private readonly FetchOffersLoggerService $logger,
    ) {}

    public function list(): JsonResponse
    {
        return response()->json($this->logger->listLogs());
    }

    public function read(string $filename): JsonResponse
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.log$/', $filename)) {
            return response()->json(['error' => 'Nieprawidłowa nazwa pliku'], 400);
        }

        $content = $this->logger->readLog($filename);

        if ($content === null) {
            return response()->json(['error' => 'Plik nie istnieje'], 404);
        }

        return response()->json(['content' => $content]);
    }
}
