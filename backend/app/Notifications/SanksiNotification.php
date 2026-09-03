<?php

namespace App\Notifications;

use App\Models\SanksiSiswa;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SanksiNotification extends Notification
{
    use Queueable;

    protected $sanksiSiswa;

    public function __construct(SanksiSiswa $sanksiSiswa)
    {
        $this->sanksiSiswa = $sanksiSiswa;
    }

    // Tentukan channel penyimpanan (Database)
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // Format data pesan inbox yang akan disimpan ke database
    public function toArray(object $notifiable): array
    {
        return [
            'sanksi_siswa_id' => $this->sanksiSiswa->id,
            'judul'           => 'Kamu Menerima Sanksi Piket',
            'pesan'           => "Kamu mendapatkan sanksi ({$this->sanksiSiswa->tipe_sanksi}) karena: {$this->sanksiSiswa->alasan}.",
            'nama_sanksi'     => $this->sanksiSiswa->sanksi->nama_sanksi ?? '-',
            'tanggal'         => now()->toDateTimeString(),
        ];
    }
}