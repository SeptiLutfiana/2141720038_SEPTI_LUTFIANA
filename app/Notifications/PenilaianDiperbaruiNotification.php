<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class PenilaianDiperbaruiNotification extends Notification
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
        return new DatabaseMessage([
            'id_idp' => $this->data['id_idp'],
            'id_idpKomPeng' => $this->data['id_idpKomPeng'],
            'status' => $this->data['status'],
            'saran' => $this->data['saran'],
            'nama_mentor' => $this->data['nama_mentor'],
            'untuk_mentor' =>$this->data['untuk_role'],
            'message' => $this->data['message'],
        ]);
    }
}
