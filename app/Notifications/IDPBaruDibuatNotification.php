<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class IDPBaruDibuatNotification extends Notification
{
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
        return [
            'title' => 'IDP Baru Telah Diajukan',
            'message' => "{$this->data['nama_karyawan']} telah mengajukan IDP dan memilih Anda sebagai {$this->data['peran']}.",
            'id_idp' => $this->data['id_idp'],
            'untuk_role' => $this->data['untuk_role'],
            'link' => $this->data['link'],
        ];
    }
}
