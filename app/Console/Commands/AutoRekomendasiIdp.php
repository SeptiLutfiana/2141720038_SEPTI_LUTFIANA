<?php

namespace App\Console\Commands;

use App\Models\IdpRekomendasi;
use App\Models\IDP;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Console\Scheduling\Schedule;

class AutoRekomendasiIdp extends Command
{
    protected $signature = 'idp:check-rekomendasi';
    protected $description = 'Buat rekomendasi otomatis untuk IDP yang melewati batas waktu dan belum disetujui mentor';

    public function handle()
    {
        $$now = now();

        $idps = IDP::with('idpKompetensis.pengerjaans')
            ->where('waktu_selesai', '<', $now)
            ->doesntHave('rekomendasis')
            ->get();

        $count = 0;

        foreach ($idps as $idp) {
            // Cek apakah semua kompetensi sudah dikerjakan
            $belumDikerjakan = $idp->idpKompetensis->filter(function ($komp) {
                return $komp->pengerjaans->isEmpty();
            });

            // Kasus 1: Semua dikerjakan, disetujui mentor, tapi belum direkomendasi → lewati
            if (
                $belumDikerjakan->isEmpty() &&
                $idp->status_pengerjaan === 'Disetujui Mentor'
            ) {
                continue; // skip, biarkan supervisor yang menilai
            }

            // Kasus 2: Masih ada yang belum dikerjakan, waktu habis → rekomendasi otomatis
            if ($belumDikerjakan->isNotEmpty()) {
                IdpRekomendasi::create([
                    'id_idp' => $idp->id_idp,
                    'hasil_rekomendasi' => 'Tidak Disarankan',
                    'deskripsi_rekomendasi' => 'IDP tidak disarankan karena tidak semua kompetensi dikerjakan hingga batas waktu.',
                    'nilai_akhir_soft' => null,
                    'nilai_akhir_hard' => null,
                ]);
                $count++;
            }
        }
    }
    public function schedule(Schedule $schedule): void
    {
        $schedule->daily(); // <-- ini yang membuatnya otomatis dijalankan harian
    }
}
