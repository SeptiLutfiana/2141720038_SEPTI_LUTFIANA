<?php

namespace App\Services;

use App\Models\Idp;
use App\Models\IdpRekomendasi;
use Illuminate\Support\Facades\Log;
use App\Models\NilaiPengerjaanIdp;

class IdpRekomendasiService
{
    /**
     * Hitung dan update hasil rekomendasi untuk IDP tertentu.
     *
     * @param Idp $idp
     * @return void
     */
    public function hitungRekomendasi(Idp $idp)
    {
        Log::info('=== Mulai proses perhitungan rekomendasi IDP ===', ['id_idp' => $idp->id_idp]);

        $softs = [];
        $hards = [];

        foreach ($idp->idpKompetensis as $idpKomp) {
            $komp = $idpKomp->kompetensi;
            $jenis = $komp->jenis_kompetensi;

            $allRatings = $idpKomp->pengerjaans->flatMap(function ($pengerjaan) {
                return $pengerjaan->nilaiPengerjaanIdp->pluck('rating')->map(fn($r) => (int)$r);
            });


            if ($allRatings->count() == 0) continue;

            $finalRating = $allRatings->avg();

            Log::info('Kompetensi ditemukan', [
                'nama' => $komp->nama_kompetensi ?? '-',
                'jenis' => $jenis,
                'rating_rata2' => $finalRating
            ]);

            if ($jenis === 'Soft Kompetensi') {
                $softs[] = [
                    'peran' => $idpKomp->peran,
                    'rating' => $finalRating,
                ];
            } elseif ($jenis === 'Hard Kompetensi') {
                $hards[] = $finalRating;
            }
        }

        $nilaiUmum = [];
        $nilaiUtama = [];
        $nilaiKunci = [];

        foreach ($softs as $s) {
            if ($s['peran'] === 'umum') {
                $nilaiUmum[] = $s['rating'];
            } elseif ($s['peran'] === 'utama') {
                $nilaiUtama[] = $s['rating'];
            } elseif (in_array($s['peran'], ['kunci_core', 'kunci_bisnis', 'kunci_enabler'])) {
                $nilaiKunci[] = $s['rating'];
            }
        }

        $totalSoftAll = array_merge($nilaiUmum, $nilaiUtama, $nilaiKunci);

        $hasilSoft = null;
        $deskripsiSoft = null;

        if (count($totalSoftAll) > 0) {
            $rataSoft = array_sum($totalSoftAll) / count($totalSoftAll);
            Log::info('Rata-rata soft kompetensi', ['rata_soft' => $rataSoft]);

            $adaRating1 = collect($totalSoftAll)->contains(fn($v) => $v == 1);
            $utamaKurang3 = collect($nilaiUtama)->contains(fn($v) => $v < 3);
            $kunciKurang3 = collect($nilaiKunci)->contains(fn($v) => $v < 3);
            $umumKurang3 = collect($nilaiUmum)->contains(fn($v) => $v < 3);

            Log::info('Soft kompetensi kondisi', compact('adaRating1', 'utamaKurang3', 'kunciKurang3', 'umumKurang3'));

            if ($adaRating1 || ($utamaKurang3 && $kunciKurang3)) {
                $hasilSoft = 'Tidak Disarankan';
                $deskripsiSoft = 'Nilai soft skill sangat rendah.';
            } elseif ($utamaKurang3 || $kunciKurang3) {
                $hasilSoft = 'Disarankan dengan Pengembangan';
                $deskripsiSoft = 'Beberapa peran utama atau kunci perlu ditingkatkan.';
            } elseif ($umumKurang3) {
                $hasilSoft = 'Disarankan dengan Pengembangan';
                $deskripsiSoft = 'Beberapa kompetensi umum perlu pengembangan.';
            } else {
                $hasilSoft = 'Disarankan';
                $deskripsiSoft = 'Soft skill memenuhi syarat.';
            }
        } else {
            $hasilSoft = 'Menunggu Hasil';
            $deskripsiSoft = 'Menunggu hasil soft kompetensi.';
        }

        $hasilHard = null;
        $deskripsiHard = null;

        if (count($hards) > 0) {
            $avgHard = array_sum($hards) / count($hards);
            Log::info('Rata-rata hard kompetensi', ['rata_hard' => $avgHard]);

            if ($avgHard > 3) {
                $hasilHard = 'Disarankan';
                $deskripsiHard = 'Hard skill memenuhi.';
            } elseif ($avgHard >= 2 && $avgHard <= 3) {
                $hasilHard = 'Disarankan dengan Pengembangan';
                $deskripsiHard = 'Beberapa hard skill perlu ditingkatkan.';
            } else {
                $hasilHard = 'Tidak Disarankan';
                $deskripsiHard = 'Hard skill sangat rendah.';
            }
        } else {
            $hasilHard = 'Menunggu Hasil';
            $deskripsiHard = 'Menunggu hasil hard kompetensi.';
        }

        Log::info('Hasil Sementara', compact('hasilSoft', 'hasilHard'));

        $finalHasil = null;
        $finalDeskripsi = null;

        if ($hasilSoft && $hasilHard) {
            if ($hasilSoft === 'Tidak Disarankan' && $hasilHard === 'Tidak Disarankan') {
                $finalHasil = 'Tidak Disarankan';
                $finalDeskripsi = 'Soft dan Hard skill sangat rendah.';
            } elseif ($hasilSoft === 'Tidak Disarankan' && $hasilHard === 'Disarankan') {
                $finalHasil = 'Disarankan dengan Pengembangan';
                $finalDeskripsi = 'Soft skill terlalu lemah, perlu pengembangan.';
            } elseif ($hasilSoft === 'Tidak Disarankan' && $hasilHard === 'Disarankan dengan Pengembangan') {
                $finalHasil = 'Disarankan dengan Pengembangan';
                $finalDeskripsi = 'Soft skill lemah, hard skill sedang perlu penguatan.';
            } elseif ($hasilSoft === 'Disarankan' && $hasilHard === 'Tidak Disarankan') {
                $finalHasil = 'Disarankan dengan Pengembangan';
                $finalDeskripsi = 'Hard skill terlalu lemah, perlu pengembangan.';
            } elseif ($hasilSoft === 'Disarankan' && $hasilHard === 'Disarankan dengan Pengembangan') {
                $finalHasil = 'Disarankan dengan Pengembangan';
                $finalDeskripsi = 'Hard skill perlu penguatan.';
            } elseif ($hasilSoft === 'Disarankan dengan Pengembangan' && $hasilHard === 'Tidak Disarankan') {
                $finalHasil = 'Disarankan dengan Pengembangan';
                $finalDeskripsi = 'Soft skill sedang, hard skill lemah.';
            } elseif ($hasilSoft === 'Disarankan dengan Pengembangan' && $hasilHard === 'Disarankan dengan Pengembangan') {
                $finalHasil = 'Disarankan dengan Pengembangan';
                $finalDeskripsi = 'Soft dan hard skill perlu pengembangan.';
            } elseif ($hasilSoft === 'Disarankan dengan Pengembangan' && $hasilHard === 'Disarankan') {
                $finalHasil = 'Disarankan dengan Pengembangan';
                $finalDeskripsi = 'Soft skill sedang perlu pengembangan.';
            } else {
                $finalHasil = 'Disarankan';
                $finalDeskripsi = 'Kemampuan teknis dan perilaku telah menunjukkan kualitas yang baik.';
            }
        } elseif ($hasilSoft) {
            $finalHasil = $hasilSoft;
            $finalDeskripsi = $deskripsiSoft;
        } elseif ($hasilHard) {
            $finalHasil = $hasilHard;
            $finalDeskripsi = $deskripsiHard;
        } else {
            $finalHasil = 'Menunggu Hasil';
            $finalDeskripsi = 'Menunggu hasil kompetensi.';
        }
        $softRatings = NilaiPengerjaanIdp::whereHas('idpKompetensiPengerjaan', function ($query) use ($idp) {
            $query->whereHas('idpKompetensi', function ($q) use ($idp) {
                $q->where('id_idp', $idp->id_idp)
                    ->whereHas('kompetensi', function ($k) {
                        $k->whereRaw("LOWER(jenis_kompetensi) = 'soft kompetensi'");
                    });
            });
        })->pluck('rating')->map(fn($r) => (int) $r)->toArray();

        $hardRatings = NilaiPengerjaanIdp::whereHas('idpKompetensiPengerjaan', function ($query) use ($idp) {
            $query->whereHas('idpKompetensi', function ($q) use ($idp) {
                $q->where('id_idp', $idp->id_idp)
                    ->whereHas('kompetensi', function ($k) {
                        $k->whereRaw("LOWER(jenis_kompetensi) = 'hard kompetensi'");
                    });
            });
        })->pluck('rating')->map(fn($r) => (int) $r)->toArray();

        // Hitung rata-rata
        $nilaiHasilSoft = count($softRatings) > 0 ? round(array_sum($softRatings) / count($softRatings), 2) : null;
        $nilaiHasilHard = count($hardRatings) > 0 ? round(array_sum($hardRatings) / count($hardRatings), 2) : null;


        Log::info('Final hasil rekomendasi', [
            'hasil_rekomendasi' => $finalHasil,
            'deskripsi' => $finalDeskripsi,
            'nilai_soft' => $nilaiHasilSoft,
            'nilai_hard' => $nilaiHasilHard
        ]);

        IdpRekomendasi::updateOrCreate(
            ['id_idp' => $idp->id_idp],
            [
                'hasil_rekomendasi' => $finalHasil,
                'deskripsi_rekomendasi' => $finalDeskripsi,
                'nilai_akhir_soft' => $nilaiHasilSoft,
                'nilai_akhir_hard' => $nilaiHasilHard,
            ]
        );

        Log::info('=== Selesai proses perhitungan rekomendasi IDP ===', ['id_idp' => $idp->id_idp]);
    }
}
