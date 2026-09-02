<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataScienceService;

class RunDataScienceAnalysis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sijaki:ds-analyze {--force : Paksa kalkulasi ulang data science}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan pipeline analisis Data Science, NLP, dan Machine Learning SIJAKI';

    /**
     * Execute the console command.
     */
    public function handle(DataScienceService $service)
    {
        $this->info('Memulai analisis SIJAKI Data Science & Machine Learning Engine...');

        $force = $this->option('force');
        $startTime = microtime(true);

        $results = $service->getInsights($force);
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        $this->table(
            ['Metrik Analitik', 'Nilai'],
            [
                ['Total Perguruan Tinggi Dianalisis', $results['kpi']['total_pt_analyzed']],
                ['Total Laporan Diproses', $results['kpi']['total_laporan_processed']],
                ['Jumlah Kampus Risiko Tinggi/Kritis', $results['kpi']['high_risk_count']],
                ['Rata-rata Skor Risiko', $results['kpi']['average_risk_score'] . ' / 100'],
                ['Isu Utama Global (NLP)', $results['kpi']['dominant_global_topic']],
                ['Waktu Eksekusi Pipeline', "{$duration} ms"],
            ]
        );

        $this->info('Ringkasan Klaster Kesehatan Kampus (K-Means):');
        foreach ($results['cluster_summary'] as $name => $cData) {
            $this->line(" - <comment>{$name}</comment>: {$cData['count']} Kampus");
        }

        $this->info('Analisis Data Science berhasil diperbarui dan disimpan ke cache!');
        return 0;
    }
}
