<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class BankIDPNotification extends Notification
{
    use Queueable;

    protected $namaJenjang;
    protected $namaLG;

    /**
     * Create a new notification instance.
     *
     * @param string $namaJenjang Nama jenjang yang menjadi target IDP bank
     * @param string|null $namaLG Nama Learning Group (boleh null jika tidak spesifik)
     */
    public function __construct($namaJenjang, $namaLG = null)
    {
        $this->namaJenjang = $namaJenjang;
        $this->namaLG = $namaLG;
    }

    /**
     * Tentukan channel pengiriman notifikasi.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // Tambahkan 'mail' jika ingin kirim email juga
    }

    /**
     * Format data untuk notifikasi berbasis database.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'title' => 'Kesempatan Pengembangan Karir Tersedia',
            'message' => 'Tersedia Bank IDP untuk jenjang ' . $this->namaJenjang .
                ' dan Learning Group ' . ($this->namaLG ?: 'Semua') .
                '. Silakan tinjau dan pilih rencana pengembangan yang sesuai dengan aspirasi Anda.',
            'link' => route('karyawan.IDP.bankIdp'),
        ]);
    }
}
