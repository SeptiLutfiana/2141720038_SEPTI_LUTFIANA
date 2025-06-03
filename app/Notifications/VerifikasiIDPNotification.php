<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;


class VerifikasiIDPNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database']; // atau tambahkan 'mail' jika ingin via email
    }

    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'id_idp' => $this->data['id_idp'],
            'status_pengajuan_idp' => $this->data['status_pengajuan_idp'],
            'status_approval_mentor' => $this->data['status_approval_mentor'],
            'saran_idp' => $this->data['saran_idp'],
            'nama_mentor' => $this->data['nama_mentor'],
            'untuk_role' => $this->data['untuk_role'],
            'message' => $this->data['message'],
            'created_at' => now(),
        ]);
    }
}
