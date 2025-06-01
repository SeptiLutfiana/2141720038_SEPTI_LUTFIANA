<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class PengerjaanBaruNotification extends Notification
{
    protected $pengerjaan;

    public function __construct($pengerjaan)
    {
        $this->pengerjaan = $pengerjaan;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        Log::info('Pengerjaan notification data:', $this->pengerjaan);

        return [
            'id_idp' => $this->pengerjaan['id_idp'] ?? null,
            'id_idpKom' => $this->pengerjaan['id_idpKom'] ?? null,
            'id_idpKomPeng' => $this->pengerjaan['id_idpKomPeng'] ?? null,
            'nama_karyawan' => $this->pengerjaan['nama_karyawan'],
            'message' => 'Ada pengerjaan IDP yang perlu dinilai',
        ];
    }
}
