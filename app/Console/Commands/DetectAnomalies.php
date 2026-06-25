<?php

namespace App\Console\Commands;

use App\Services\AnomalyEngine;
use Illuminate\Console\Command;

class DetectAnomalies extends Command
{
    protected $signature = 'fleet:detect-anomalies';
    protected $description = 'Jalankan enjin pengesanan anomali untuk semua kenderaan';

    public function handle(): int
    {
        $engine = new AnomalyEngine();
        $detected = $engine->scan();

        $this->info("Selesai. {$detected->count()} anomali dikesan.");

        foreach ($detected->groupBy('severity') as $severity => $items) {
            $this->line("  {$severity}: {$items->count()}");
        }

        return 0;
    }
}
