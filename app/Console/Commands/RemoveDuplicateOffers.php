<?php

namespace App\Console\Commands;

use App\Models\Offer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateOffers extends Command
{
    protected $signature = 'offers:remove-duplicates';
    protected $description = 'Strips query parameters from offer URLs and removes duplicate offers';

    private array $statusPriority = [
        'offer'      => 8,
        'interview'  => 7,
        'applied'    => 6,
        'rejected'   => 5,
        'interested' => 4,
        'low_salary' => 3,
        'ignored'    => 2,
        'new'        => 1,
    ];

    public function handle(): int
    {
        $updated = DB::table('offers')
            ->whereRaw("url LIKE '%?%'")
            ->update(['url' => DB::raw("SUBSTRING_INDEX(url, '?', 1)")]);

        $this->info("Zaktualizowano $updated URLi (usunięto parametry query).");

        $duplicateUrls = DB::table('offers')
            ->select('url')
            ->groupBy('url')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('url');

        if ($duplicateUrls->isEmpty()) {
            $this->info('Brak duplikatów do usunięcia.');
            return self::SUCCESS;
        }

        $this->info("Znaleziono {$duplicateUrls->count()} zduplikowanych URLi.");

        $deletedTotal = 0;

        foreach ($duplicateUrls as $url) {
            $offers = Offer::where('url', $url)
                ->select('id', 'status')
                ->orderBy('id')
                ->get();

            $keepId = null;
            $bestPriority = -1;

            foreach ($offers as $offer) {
                $priority = $this->statusPriority[$offer->status->value] ?? 0;
                if ($priority > $bestPriority) {
                    $bestPriority = $priority;
                    $keepId = $offer->id;
                }
            }

            $idsToDelete = $offers->pluck('id')->filter(fn($id) => $id !== $keepId)->values();

            Offer::whereIn('id', $idsToDelete)->delete();
            $deletedTotal += $idsToDelete->count();

            $this->line("  URL: {$url}");
            $this->line("    zachowano ID {$keepId} (status: " . $offers->firstWhere('id', $keepId)->status->value . ')');
            $this->line('    usunięto ID: ' . $idsToDelete->implode(', '));
        }

        $this->info("Zakończono. Usunięto łącznie $deletedTotal duplikatów.");

        return self::SUCCESS;
    }
}
