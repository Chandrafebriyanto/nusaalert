<?php

namespace App\Console\Commands;

use App\Models\Bencana;
use App\Models\Lokasi;
use App\Models\Alert;
use App\Jobs\SendDisasterAlertJob;
use App\Services\BmkgService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PollBmkgCommand extends Command
{
    protected $signature = 'app:poll-bmkg';
    protected $description = 'Poll data gempa dari BMKG dan kirim alert jika ada yang baru';

    public function handle(BmkgService $bmkg)
    {
        $this->info('Memulai polling data BMKG...');

        $bencanaBaruCount = 0;
        $alertCount = 0;

        // ── Source 1: Gempa Dirasakan ──
        $gempaBaruList = $bmkg->getGempaDirasakan();

        if (!empty($gempaBaruList)) {
            $this->info('Memproses ' . count($gempaBaruList) . ' data gempa dirasakan...');

            foreach ($gempaBaruList as $gempa) {
                $result = $this->processGempa($gempa, $bmkg);
                $bencanaBaruCount += $result['bencana'];
                $alertCount += $result['alerts'];
            }
        } else {
            $this->info('Tidak ada data gempa dirasakan dari BMKG saat ini.');
        }

        // ── Source 2: Gempa M5+ (gempaterkini.json) ──
        $gempaM5List = $bmkg->getGempaM5();

        if (!empty($gempaM5List)) {
            $this->info('Memproses ' . count($gempaM5List) . ' data gempa M5+...');

            foreach ($gempaM5List as $gempa) {
                $result = $this->processGempa($gempa, $bmkg);
                $bencanaBaruCount += $result['bencana'];
                $alertCount += $result['alerts'];
            }
        } else {
            $this->info('Tidak ada data gempa M5+ dari BMKG saat ini.');
        }

        $this->info("Polling selesai. $bencanaBaruCount bencana baru ditemukan, $alertCount alert dikirim.");
        Log::info("BMKG Polling: $bencanaBaruCount bencana baru, $alertCount alerts dikirim.");
    }

    /**
     * Process a single gempa entry from BMKG.
     *
     * @return array{bencana: int, alerts: int}
     */
    private function processGempa(array $gempa, BmkgService $bmkg): array
    {
        $bencanaCount = 0;
        $alertCount = 0;

        // BMKG event id from Datetime
        $eventId = 'bmkg-' . md5($gempa['DateTime'] . $gempa['Coordinates']);

        // Cek apakah sudah ada di database
        if (Bencana::where('event_id', $eventId)->exists()) {
            return ['bencana' => 0, 'alerts' => 0];
        }

        // Parsing koordinat (Format "2.95 LS, 118.01 BT")
        $coords = explode(',', $gempa['Coordinates']);
        $lat = $bmkg->parseLatitude(trim($coords[0]));
        $lng = $bmkg->parseLongitude(trim($coords[1] ?? '0'));

        // Simpan Bencana Baru
        $bencana = Bencana::create([
            'event_id' => $eventId,
            'jenis_bencana' => 'gempa',
            'magnitude' => (float) $gempa['Magnitude'],
            'kedalaman_km' => (float) preg_replace('/[^0-9.\-]/', '', $gempa['Kedalaman']),
            'latitude' => $lat,
            'longitude' => $lng,
            'wilayah' => $gempa['Wilayah'],
            'sumber_api' => 'bmkg',
            'raw_data' => $gempa,
            'terjadi_pada' => $bmkg->parseDateTime($gempa['Tanggal'], $gempa['Jam']),
        ]);

        $bencanaCount++;
        $this->info("Bencana baru disimpan: {$bencana->wilayah} (M{$bencana->magnitude})");

        // Proses Alerting ke User
        // Ambil semua lokasi aktif
        $lokasiAktif = Lokasi::where('is_active', true)->get();

        foreach ($lokasiAktif as $lokasi) {
            $jarak = $bmkg->haversineDistance($lat, $lng, $lokasi->latitude, $lokasi->longitude);

            // Jika jarak gempa masuk dalam radius lokasi user
            if ($jarak <= $lokasi->radius_km) {
                $alert = Alert::create([
                    'user_id' => $lokasi->user_id,
                    'bencana_id' => $bencana->id,
                    'lokasi_id' => $lokasi->id,
                    'jarak_km' => $jarak,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
                $alertCount++;

                // Dispatch notification via queue
                SendDisasterAlertJob::dispatch($alert);
            }
        }

        return ['bencana' => $bencanaCount, 'alerts' => $alertCount];
    }
}
