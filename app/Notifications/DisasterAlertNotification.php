<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisasterAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Alert $alert
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $bencana = $this->alert->bencana;
        $lokasi = $this->alert->lokasi;
        $severity = 'PERINGATAN';

        if ($bencana->magnitude) {
            if ($bencana->magnitude >= 6) {
                $severity = '⚠️ AWAS - KRITIS';
            } elseif ($bencana->magnitude >= 4) {
                $severity = '⚠️ SIAGA';
            }
        }

        return (new MailMessage)
            ->subject("[NusaAlert] {$severity}: " . strtoupper($bencana->jenis_bencana) . " di {$bencana->wilayah}")
            ->greeting("Peringatan Bencana!")
            ->line("**{$severity}**: " . strtoupper($bencana->jenis_bencana) . " terdeteksi.")
            ->line("**Lokasi bencana:** {$bencana->wilayah}")
            ->line($bencana->magnitude ? "**Magnitude:** {$bencana->magnitude}" : '')
            ->line($bencana->kedalaman_km ? "**Kedalaman:** {$bencana->kedalaman_km} km" : '')
            ->line("**Jarak dari {$lokasi->nama_lokasi}:** {$this->alert->jarak_km} km")
            ->line("Pastikan keselamatan Anda dan keluarga. Ikuti panduan keselamatan yang berlaku.")
            ->action('Buka Dashboard', url('/dashboard'))
            ->line('Tetap waspada dan pantau terus informasi resmi dari BMKG.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'bencana_id' => $this->alert->bencana_id,
            'jenis_bencana' => $this->alert->bencana->jenis_bencana,
            'wilayah' => $this->alert->bencana->wilayah,
            'magnitude' => $this->alert->bencana->magnitude,
            'jarak_km' => $this->alert->jarak_km,
            'lokasi_nama' => $this->alert->lokasi->nama_lokasi,
        ];
    }
}
