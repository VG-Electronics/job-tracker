<?php

namespace App\Jobs;

use App\Models\AppSetting;
use App\Models\Offer;
use App\Repositories\OffersRepository;
use App\Repositories\WebsitesRepository;
use App\Services\AiService;
use App\Services\FetchOffersLoggerService;
use App\Services\ScrapperService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchNewOffersJob implements ShouldQueue
{
    use Queueable;

    public function handle(
        WebsitesRepository $websitesRepository,
        OffersRepository $offersRepository,
        ScrapperService $scrapperService,
        AiService $aiService,
        FetchOffersLoggerService $logger,
    ): void {
        $logger->init();
        $logger->log('Start pobierania ofert');

        $websites = $websitesRepository->getWebsites();
        $allOffers = [];

        foreach ($websites as $website) {
            try {
                $offers = $scrapperService->getOffers($website['url'], $website['offer_url_part'] ?? null);
                $count = count($offers);
                $allOffers = array_merge($allOffers, $offers);
                $logger->log("Pobrano $count ofert ze strony: {$website['url']}");
            } catch (\Throwable $e) {
                $logger->logError("Błąd pobierania ze strony {$website['url']}: {$e->getMessage()}");
            }
        }

        $total = count($allOffers);
        $logger->log("Łącznie pobrano $total ofert");

        if (empty($allOffers)) {
            $logger->log('Brak ofert do przetworzenia');
            return;
        }

        $offerUrls = array_column($allOffers, 'url');
        $existingUrls = $offersRepository->getExistingUrls($offerUrls);

        $newOffers = array_values(array_filter(
            $allOffers,
            fn(array $offer) => !in_array($offer['url'], $existingUrls),
        ));

        $existingCount = count($existingUrls);
        $newCount = count($newOffers);
        $logger->log("$newCount nowych ofert (pominięto $existingCount już istniejących)");

        if (empty($newOffers)) {
            $logger->log('Brak nowych ofert do zapisania');
            return;
        }

        $chunks = array_chunk($newOffers, 10);
        $totalChunks = count($chunks);
        $savedCount = 0;
        $seenUrls = [];
        $additionalInstruction = AppSetting::getValue('ai_additional_instruction');

        foreach ($chunks as $index => $chunk) {
            $chunkNum = $index + 1;
            $chunkSize = count($chunk);
            $logger->log("Przetwarzanie AI chunk $chunkNum/$totalChunks ($chunkSize ofert)");

            try {
                $preparedOffers = $aiService->prepareChunk($chunk, $additionalInstruction);

                $uniqueOffers = [];
                foreach ($preparedOffers as $offer) {
                    $url = $offer['url'] ?? null;
                    if ($url === null || isset($seenUrls[$url])) {
                        continue;
                    }
                    $seenUrls[$url] = true;
                    $uniqueOffers[] = $offer;
                }

                $aiCount = count($preparedOffers);
                $uniqueCount = count($uniqueOffers);
                $skipped = $aiCount - $uniqueCount;
                $logger->log("AI zwróciło $aiCount ofert dla chunk $chunkNum/$totalChunks" . ($skipped > 0 ? " (pominięto $skipped duplikatów URL)" : ''));

                foreach ($uniqueOffers as $offer) {
                    Offer::create($offer);
                    $savedCount++;
                }

                $logger->log("Zapisano $uniqueCount ofert (łącznie w bazie: $savedCount)");
            } catch (\Throwable $e) {
                $logger->logError("Błąd AI dla chunk $chunkNum/$totalChunks: {$e->getMessage()}");
            }
        }

        $logger->log("Zakończono. Łącznie zapisano $savedCount ofert");
    }
}
