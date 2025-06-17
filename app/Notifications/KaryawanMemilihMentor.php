<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class KaryawanMemilihMentor extends Notification
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        Log::info('Mentor notification data:', $this->data);

        return [
            'id_user' => $this->data['id_user'], // ID karyawan
            'nama_karyawan' => $this->data['nama_karyawan'],
            'id_idp' => $this->data['id_idp'],
            'untuk_role' => $this->data['untuk_role'],
            'message' => "telah memilih Anda sebagai mentor",
        ];
    }
}
