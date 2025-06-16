<?php

namespace App\Console\Commands;
use App\Models\IdpRekomendasi;
use App\Models\IDP;
use Illuminate\Console\Command;

class AutoRekomendasiIdp extends Command
{
   protected $signature = 'idp:autorekom';
    protected $description = 'Beri rekomendasi otomatis Tidak Disarankan untuk IDP yang melewati batas waktu';

    public function handle()
    {
        $expiredIdps = IDP::whereDate('waktu_selesai', '<', now())
            ->doesntHave('rekomendasis')
            ->get();

        $total = 0;

        foreach ($expiredIdps as $idp) {
            IdpRekomendasi::create([
                'id_idp' => $idp->id,
                'hasil_rekomendasi' => 'Tidak Disarankan',
                'deskripsi_rekomendasi' => 'IDP telah melewati batas waktu dan tidak memenuhi target, sehingga tidak direkomendasikan.',
                'nilai_akhir_soft' => null,
                'nilai_akhir_hard' => null,
            ]);
            $total++;
        }

        $this->info("Selesai: $total IDP diberi rekomendasi otomatis.");
    }
}
