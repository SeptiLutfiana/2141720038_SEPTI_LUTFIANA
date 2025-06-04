<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class GivenIDPNotification extends Notification
{
    use Queueable;

    protected $id_idp;

    /**
     * Create a new notification instance.
     *
     * @param int $id_idp ID dari IDP yang baru dibuat
     */
    public function __construct($id_idp)
    {
        $this->id_idp = $id_idp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // Bisa tambahkan 'mail' jika ingin kirim email juga
    }

    /**
     * Get the array representation of the notification for the database.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'title' => 'Rencana Pengembangan Karir Telah Dibuat',
            'message' => 'Admin telah membuatkan IDP (Individual Development Plan) untuk Anda. Silakan tinjau dan mulai perjalanan pengembangan Anda.',
            'id_idp' => $this->id_idp,
            'link' => route('karyawan.IDP.indexKaryawan'), // Atau route detail jika ingin langsung ke IDP tertentu
        ]);
    }
}
