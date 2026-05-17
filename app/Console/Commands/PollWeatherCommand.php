<?php

namespace App\Console\Commands;

use App\Models\Bencana;
use App\Models\Lokasi;
use App\Models\Alert;
use App\Jobs\SendDisasterAlertJob;
use App\Services\OpenWeatherService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PollWeatherCommand extends Command
{
    protected $signature = 'app:poll-weather';
    protected $description = 'Poll cuaca dari OpenWeatherMap dan buat alert jika ada cuaca ekstrem';

    public function handle(OpenWeatherService $owm)
    {
        if (!$owm->isConfigured()) {
            $this->warn('OpenWeatherMap API key belum dikonfigurasi. Set OWM_API_KEY di .env');
            return;
        }

        $this->info('Memulai polling cuaca...');

        // Get unique coordinates from all active locations
        $locations = Lokasi::where('is_active', true)
            ->select('latitude', 'longitude')
            ->distinct()
            ->get();

        $bencanaCount = 0;
        $alertCount = 0;

        foreach ($locations as $loc) {
            $extreme = $owm->checkExtremeWeather((float) $loc->latitude, (float) $loc->longitude);

            if (!$extreme) continue;

            foreach ($extreme['threats'] as $threat) {
                // Generate unique event ID to prevent duplicates
                $eventId = 'owm-' . md5($threat['type'] . '-' . $loc->latitude . '-' . $loc->longitude . '-' . now()->format('Y-m-d-H'));

                if (Bencana::where('event_id', $eventId)->exists()) {
                    continue;
                }

                $bencana = Bencana::create([
                    'event_id' => $eventId,
                    'jenis_bencana' => 'cuaca_ekstrem',
                    'magnitude' => null,
                    'kedalaman_km' => null,
                    'latitude' => $extreme['lat'],
                    'longitude' => $extreme['lng'],
                    'wilayah' => $extreme['location'] . ' - ' . $threat['label'] . ': ' . $threat['value'],
                    'sumber_api' => 'openweathermap',
                    'raw_data' => $extreme['raw_data'],
                    'terjadi_pada' => now(),
                ]);

                $bencanaCount++;
                $this->info("Cuaca ekstrem terdeteksi: {$threat['label']} di {$extreme['location']}");

                // Alert users within radius
                $lokasiAktif = Lokasi::where('is_active', true)->get();

                foreach ($lokasiAktif as $userLoc) {
                    $jarak = \App\Services\BmkgService::haversineDistance(
                        $extreme['lat'], $extreme['lng'],
                        (float) $userLoc->latitude, (float) $userLoc->longitude
                    );

                    if ($jarak <= $userLoc->radius_km) {
                        $alert = Alert::create([
                            'user_id' => $userLoc->user_id,
                            'bencana_id' => $bencana->id,
                            'lokasi_id' => $userLoc->id,
                            'jarak_km' => $jarak,
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]);

                        SendDisasterAlertJob::dispatch($alert);
                        $alertCount++;
                    }
                }
            }
        }

        $this->info("Polling cuaca selesai. {$bencanaCount} event cuaca baru, {$alertCount} alert dikirim.");
        Log::info("Weather Polling: {$bencanaCount} event cuaca baru, {$alertCount} alerts dikirim.");
    }
}
