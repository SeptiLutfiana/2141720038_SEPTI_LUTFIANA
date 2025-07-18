<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateIDPNotification extends Notification
{
    use Queueable;

    protected $id_idp;
    protected $untuk_role;

    public function __construct($id_idp, $untuk_role)
    {
        $this->id_idp = $id_idp;
        $this->untuk_role = $untuk_role;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'IDP Telah Diperbarui',
            'message' => 'IDP telah diperbarui dan menunggu persetujuan Anda.',
            'id_idp' => $this->id_idp,
            'untuk_role' => $this->untuk_role,
            'link' => route(
                $this->untuk_role === 'mentor' ? 'mentor.IDP.mentor.idp.show' : 'supervisor.IDP.showSupervisor',
                ['id' => $this->id_idp]
            ),
        ];
    }
}
