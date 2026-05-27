<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class DisasterAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Alert $alert
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $bencana = $this->alert->bencana;
        $severity = 'PERINGATAN';
        $isCritical = false;

        if ($bencana->magnitude) {
            if ($bencana->magnitude >= 6) {
                $severity = 'AWAS - KRITIS';
                $isCritical = true;
            } elseif ($bencana->magnitude >= 4) {
                $severity = 'SIAGA';
            }
        }

        if ($bencana->jenis_bencana === 'tsunami') {
            $severity = 'AWAS - TSUNAMI';
            $isCritical = true;
        }

        $message = (new WebPushMessage)
            ->title("{$severity}: " . strtoupper($bencana->jenis_bencana))
            ->icon('/icons/icon-192x192.png')
            ->body("Terdeteksi di {$bencana->wilayah}. Jarak: {$this->alert->jarak_km}km dari {$this->alert->lokasi->nama_lokasi}.")
            ->action('Lihat Detail', url('/dashboard'))
            ->data([
                'action_url' => url('/dashboard'),
                'tag' => 'nusaalert-bencana-' . $this->alert->bencana_id,
                'requireInteraction' => $isCritical,
            ]);

        return $message;
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
