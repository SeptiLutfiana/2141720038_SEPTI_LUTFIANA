<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class HasilRekomendasiNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $link = '#';
        if ($this->data['untuk_role'] === 'karyawan') {
            $link = route('karyawan.IDP.RiwayatIDP.showRiwayatIdp', ['id' => $this->data['id_idp']]);
        } elseif ($this->data['untuk_role'] === 'mentor') {
            $link = route('mentor.IDP.RiwayatIDP.showRiwayatIdp', ['id' => $this->data['id_idp']]);
        }

        return [
            'title' => 'Hasil Rekomendasi IDP Tersedia',
            'message' => 'IDP dengan proyeksi karir "' . $this->data['proyeksi_karir'] . '" sudah mendapatkan hasil rekomendasi.',
            'id_idp' => $this->data['id_idp'],
            'untuk_role' => $this->data['untuk_role'],
            'link' => $link,
        ];
    }
}

