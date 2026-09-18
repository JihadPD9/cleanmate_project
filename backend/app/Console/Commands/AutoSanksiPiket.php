<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JadwalPiket;
use App\Models\BuktiPiket;
use App\Models\SanksiSiswa;
use App\Models\User;
use App\Notifications\SanksiNotification;
use Carbon\Carbon;

class AutoSanksiPiket extends Command
{
    /**
     * Nama command artisan
     */
    protected $signature = 'sanksi:auto-check';

    /**
     * Deskripsi command
     */
    protected $description = 'Otomatis memberikan sanksi default jika kelompok piket belum upload bukti hingga 18:00 WIB';

    public function handle()
    {
        // 1. Dapatkan nama hari ini dalam Bahasa Indonesia
        $dayNames = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];

        $todayEnglish = Carbon::now('Asia/Jakarta')->format('l');
        $todayIndo = $dayNames[$todayEnglish] ?? null;

        // Abaikan jika hari Sabtu atau Minggu (Libur)
        if (in_array($todayIndo, ['Sabtu', 'Minggu'])) {
            $this->info("Hari {$todayIndo} libur piket.");
            return 0;
        }

        // 2. Cek apakah ada bukti piket yang diunggah hari ini
        $hasUploadedToday = BuktiPiket::whereDate('created_at', Carbon::today())->exists();

        // Jika sudah ada yang upload, tidak perlu beri sanksi
        if ($hasUploadedToday) {
            $this->info("Bukti piket hari {$todayIndo} sudah diunggah.");
            return 0;
        }

        // 3. Jika BELUM ada yang upload, ambil semua petugas piket hari ini
        $siswaPiket = JadwalPiket::where('hari', $todayIndo)->get();

        if ($siswaPiket->isEmpty()) {
            $this->info("Tidak ada siswa piket terdaftar untuk hari {$todayIndo}.");
            return 0;
        }

        // Master Sanksi ID Default (Misal ID = 1 untuk Denda / Sanksi Default)
        $defaultMasterSanksiId = 1; 

        foreach ($siswaPiket as $piket) {
            // Cek apakah siswa ini sudah diberi sanksi hari ini agar tidak duplikat
            $alreadyPenalized = SanksiSiswa::where('user_id', $piket->user_id)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if (!$alreadyPenalized) {
                $sanksiSiswa = SanksiSiswa::create([
                    'user_id'             => $piket->user_id,
                    'sanksi_id'           => $defaultMasterSanksiId,
                    'tipe_sanksi'         => 'kelompok',
                    'alasan'              => "Tidak mengunggah bukti piket harian hingga batas waktu 18:00 WIB (Hari {$todayIndo}).",
                    'status_penyelesaian' => 'belum',
                ]);

                // Kirim notifikasi ke inbox siswa
                $user = User::find($piket->user_id);
                if ($user) {
                    $user->notify(new SanksiNotification($sanksiSiswa));
                }
            }
        }

        $this->info("Sanksi otomatis berhasil diberikan kepada kelompok piket hari {$todayIndo}.");
        return 0;
    }
}