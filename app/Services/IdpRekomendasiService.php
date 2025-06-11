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
        try {
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

            // Hitung hasil soft kompetensi dengan aturan bisnis yang BENAR
            $hasilSoft = $this->hitungHasilSoftKompetensi($softs);
            $hasilHard = $this->hitungHasilHardKompetensi($hards);

            // Tentukan hasil final
            $finalHasil = $this->tentukanHasilFinal($hasilSoft, $hasilHard);

            // Hitung nilai rata-rata
            $nilaiHasilSoft = $this->hitungNilaiAkhirSoft($idp);
            $nilaiHasilHard = $this->hitungNilaiAkhirHard($idp);

            Log::info('Final hasil rekomendasi', [
                'hasil_rekomendasi' => $finalHasil['hasil'],
                'deskripsi' => $finalHasil['deskripsi'],
                'nilai_soft' => $nilaiHasilSoft,
                'nilai_hard' => $nilaiHasilHard
            ]);

            $rekomendasi = IdpRekomendasi::updateOrCreate(
                ['id_idp' => $idp->id_idp],
                [
                    'hasil_rekomendasi' => $finalHasil['hasil'],
                    'deskripsi_rekomendasi' => $finalHasil['deskripsi'],
                    'nilai_akhir_soft' => $nilaiHasilSoft,
                    'nilai_akhir_hard' => $nilaiHasilHard,
                ]
            );

            Log::info('=== Selesai proses perhitungan rekomendasi IDP ===', ['id_idp' => $idp->id_idp]);
        } catch (\Exception $e) {
            Log::error('ERROR saat menghitung rekomendasi', [
                'id_idp' => $idp->id_idp,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Hitung hasil soft kompetensi berdasarkan aturan bisnis yang BENAR
     */
    private function hitungHasilSoftKompetensi($softs)
    {
        if (empty($softs)) {
            return [
                'hasil' => 'Menunggu Hasil',
                'deskripsi' => 'Menunggu hasil soft kompetensi.'
            ];
        }

        $totalKompetensi = count($softs);
        $ratings = array_column($softs, 'rating');

        // Hitung berdasarkan rating
        $rating3to5 = array_filter($ratings, fn($r) => $r >= 3 && $r <= 5);
        $rating2 = array_filter($ratings, fn($r) => $r == 2);
        $rating1 = array_filter($ratings, fn($r) => $r == 1);

        $count3to5 = count($rating3to5);
        $count2 = count($rating2);
        $count1 = count($rating1);

        // Cek kompetensi utama dan kunci
        $utamaKurang3 = false;
        $kunciKurang3 = false;
        $utamaAtauKunciRating2 = false;

        foreach ($softs as $soft) {
            if ($soft['peran'] === 'utama' && $soft['rating'] < 3) {
                $utamaKurang3 = true;
                if ($soft['rating'] == 2) {
                    $utamaAtauKunciRating2 = true;
                }
            }
            if (in_array($soft['peran'], ['kunci_core', 'kunci_bisnis', 'kunci_enabler']) && $soft['rating'] < 3) {
                $kunciKurang3 = true;
                if ($soft['rating'] == 2) {
                    $utamaAtauKunciRating2 = true;
                }
            }
        }

        Log::info('Analisis soft kompetensi (ATURAN ADAPTIF)', [
            'total_kompetensi' => $totalKompetensi,
            'rating_3_to_5' => $count3to5,
            'rating_2' => $count2,
            'rating_1' => $count1,
            'utama_kurang_3' => $utamaKurang3,
            'kunci_kurang_3' => $kunciKurang3,
            'utama_atau_kunci_rating_2' => $utamaAtauKunciRating2
        ]);

        // ===== ATURAN BISNIS ADAPTIF =====

        // PRIORITAS PERTAMA: Kondisi yang PASTI TIDAK DISARANKAN
        if (
            $count1 > 0 ||                          // Terdapat rating 1 (langsung tolak)
            ($utamaKurang3 && $kunciKurang3)        // Rating kompetensi utama DAN kunci di bawah 3
        ) {
            return [
                'hasil' => 'Tidak Disarankan',
                'deskripsi' => 'Soft skill tidak memenuhi standar minimum yang diperlukan.'
            ];
        }

        // Untuk kompetensi sedikit (< 6), gunakan logika persentase
        if ($totalKompetensi < 6) {
            return $this->evaluasiKompetensiSedikit($count3to5, $count2, $count1, $utamaKurang3, $kunciKurang3, $utamaAtauKunciRating2, $totalKompetensi);
        }

        // Untuk kompetensi 6-11, gunakan logika proporsional
        if ($totalKompetensi < 12) {
            return $this->evaluasiKompetensiSedang($count3to5, $count2, $count1, $utamaKurang3, $kunciKurang3, $utamaAtauKunciRating2, $totalKompetensi);
        }

        // Untuk kompetensi ≥ 12, gunakan aturan asli
        return $this->evaluasiKompetensiLengkap($count3to5, $count2, $count1, $utamaKurang3, $kunciKurang3, $utamaAtauKunciRating2);
    }

    /**
     * Evaluasi untuk kompetensi sedikit (< 6 kompetensi)
     */
    private function evaluasiKompetensiSedikit($count3to5, $count2, $count1, $utamaKurang3, $kunciKurang3, $utamaAtauKunciRating2, $total)
    {
        $percentage3to5 = $count3to5 / $total;
        $percentage2 = $count2 / $total;

        Log::info('Evaluasi kompetensi sedikit', [
            'total' => $total,
            'percentage_3to5' => $percentage3to5,
            'percentage_2' => $percentage2
        ]);

        // TIDAK DISARANKAN jika:
        // - Ada rating 1 (sudah dicek di atas)
        // - Kompetensi 3-5 kurang dari 50%
        // - Ada kompetensi utama DAN kunci < 3
        if ($percentage3to5 < 0.5) {
            return [
                'hasil' => 'Tidak Disarankan',
                'deskripsi' => 'Soft skill tidak memenuhi standar minimum yang diperlukan.'
            ];
        }

        // DISARANKAN jika:
        // - Semua kompetensi rating 3-5 (100%)
        // - Tidak ada kompetensi utama/kunci < 3
        if ($percentage3to5 == 1.0 && !$utamaKurang3 && !$kunciKurang3) {
            return [
                'hasil' => 'Disarankan',
                'deskripsi' => 'Soft skill memenuhi syarat dengan baik.'
            ];
        }

        // Sisanya DISARANKAN DENGAN PENGEMBANGAN
        return [
            'hasil' => 'Disarankan dengan Pengembangan',
            'deskripsi' => 'Soft skill cukup baik namun masih memerlukan pengembangan.'
        ];
    }

    /**
     * Evaluasi untuk kompetensi sedang (6-11 kompetensi)
     */
    private function evaluasiKompetensiSedang($count3to5, $count2, $count1, $utamaKurang3, $kunciKurang3, $utamaAtauKunciRating2, $total)
    {
        $percentage3to5 = $count3to5 / $total;

        Log::info('Evaluasi kompetensi sedang', [
            'total' => $total,
            'percentage_3to5' => $percentage3to5,
            'count_3to5' => $count3to5
        ]);

        // TIDAK DISARANKAN jika rating 3-5 kurang dari 50%
        if ($percentage3to5 < 0.5) {
            return [
                'hasil' => 'Tidak Disarankan',
                'deskripsi' => 'Soft skill tidak memenuhi standar minimum yang diperlukan.'
            ];
        }

        // DISARANKAN jika rating 3-5 ≥ 80% dan tidak ada kompetensi utama/kunci < 3
        if ($percentage3to5 >= 0.8 && !$utamaKurang3 && !$kunciKurang3) {
            return [
                'hasil' => 'Disarankan',
                'deskripsi' => 'Soft skill memenuhi syarat dengan baik.'
            ];
        }

        // Sisanya DISARANKAN DENGAN PENGEMBANGAN
        return [
            'hasil' => 'Disarankan dengan Pengembangan',
            'deskripsi' => 'Soft skill cukup baik namun masih memerlukan pengembangan.'
        ];
    }

    /**
     * Evaluasi untuk kompetensi lengkap (≥ 12 kompetensi) - Aturan asli
     */
    private function evaluasiKompetensiLengkap($count3to5, $count2, $count1, $utamaKurang3, $kunciKurang3, $utamaAtauKunciRating2)
    {
        // NOT READY/TIDAK DISARANKAN (NR/TD)
        if (
            $count3to5 >= 1 && $count3to5 <= 5 ||  // Rating 3-5 sebanyak 1-5
            $count2 >= 7                            // Rating 2 berjumlah ≥ 7
        ) {
            return [
                'hasil' => 'Tidak Disarankan',
                'deskripsi' => 'Soft skill tidak memenuhi standar minimum yang diperlukan.'
            ];
        }

        // READY NOW/DISARANKAN (R/D)
        if (
            $count3to5 >= 9 &&                      // Minimal 9 kompetensi rating 3-5
            $count2 <= 3 &&                         // Maksimal 3 kompetensi rating 2
            !$utamaKurang3 && !$kunciKurang3        // Tidak ada kompetensi utama/kunci < 3
        ) {
            return [
                'hasil' => 'Disarankan',
                'deskripsi' => 'Soft skill memenuhi syarat dengan baik.'
            ];
        }

        // READY WITH DEVELOPMENT/DISARANKAN DENGAN PENGEMBANGAN (RW/DDP)
        if (
            $count3to5 >= 6 && $count3to5 <= 8 &&   // Rating 3-5 sebanyak 6-8
            $count2 <= 6 &&                         // Maksimal 6 kompetensi rating 2
            $utamaAtauKunciRating2                   // Terdapat kompetensi utama/kunci rating 2
        ) {
            return [
                'hasil' => 'Disarankan dengan Pengembangan',
                'deskripsi' => 'Soft skill cukup baik namun masih memerlukan pengembangan.'
            ];
        }

        // Fallback untuk aturan lengkap
        return [
            'hasil' => 'Disarankan dengan Pengembangan',
            'deskripsi' => 'Soft skill perlu evaluasi lebih lanjut.'
        ];
    }

    /**
     * Hitung hasil hard kompetensi
     */
    private function hitungHasilHardKompetensi($hards)
    {
        if (empty($hards)) {
            return [
                'hasil' => 'Menunggu Hasil',
                'deskripsi' => 'Menunggu hasil hard kompetensi.'
            ];
        }

        $avgHard = array_sum($hards) / count($hards);

        Log::info('Rata-rata hard kompetensi', ['rata_hard' => $avgHard]);

        if ($avgHard > 3) {
            return [
                'hasil' => 'Disarankan',
                'deskripsi' => 'Hard skill memenuhi standar.'
            ];
        } elseif ($avgHard >= 2 && $avgHard <= 3) {
            return [
                'hasil' => 'Disarankan dengan Pengembangan',
                'deskripsi' => 'Hard skill perlu pengembangan.'
            ];
        } else {
            return [
                'hasil' => 'Tidak Disarankan',
                'deskripsi' => 'Hard skill di bawah standar minimum.'
            ];
        }
    }

    /**
     * Tentukan hasil final berdasarkan kombinasi soft dan hard
     */
    private function tentukanHasilFinal($hasilSoft, $hasilHard)
    {
        $soft = $hasilSoft['hasil'];
        $hard = $hasilHard['hasil'];

        // Jika salah satu masih menunggu hasil
        if ($soft === 'Menunggu Hasil' || $hard === 'Menunggu Hasil') {
            return [
                'hasil' => 'Menunggu Hasil',
                'deskripsi' => 'Menunggu hasil kompetensi lengkap.'
            ];
        }

        // Matrix keputusan
        $matrix = [
            'Tidak Disarankan' => [
                'Tidak Disarankan' => ['Tidak Disarankan', 'Soft dan hard skill tidak memenuhi standar.'],
                'Disarankan dengan Pengembangan' => ['Disarankan dengan Pengembangan', 'Soft skill lemah, hard skill perlu pengembangan.'],
                'Disarankan' => ['Disarankan dengan Pengembangan', 'Soft skill lemah, perlu pengembangan intensif.']
            ],
            'Disarankan dengan Pengembangan' => [
                'Tidak Disarankan' => ['Disarankan dengan Pengembangan', 'Hard skill lemah, soft skill perlu pengembangan.'],
                'Disarankan dengan Pengembangan' => ['Disarankan dengan Pengembangan', 'Soft dan hard skill perlu pengembangan.'],
                'Disarankan' => ['Disarankan dengan Pengembangan', 'Soft skill perlu pengembangan.']
            ],
            'Disarankan' => [
                'Tidak Disarankan' => ['Disarankan dengan Pengembangan', 'Hard skill lemah, perlu pengembangan intensif.'],
                'Disarankan dengan Pengembangan' => ['Disarankan dengan Pengembangan', 'Hard skill perlu pengembangan.'],
                'Disarankan' => ['Disarankan', 'Kemampuan teknis dan perilaku memenuhi standar.']
            ]
        ];

        if (isset($matrix[$soft][$hard])) {
            return [
                'hasil' => $matrix[$soft][$hard][0],
                'deskripsi' => $matrix[$soft][$hard][1]
            ];
        }

        // Fallback
        return [
            'hasil' => 'Menunggu Hasil',
            'deskripsi' => 'Kombinasi hasil tidak dikenali.'
        ];
    }

    /**
     * Hitung nilai akhir soft kompetensi
     */
    private function hitungNilaiAkhirSoft(Idp $idp)
    {
        $softRatings = [];

        foreach ($idp->idpKompetensis as $idpKomp) {
            $komp = $idpKomp->kompetensi;

            if ($komp->jenis_kompetensi === 'Soft Kompetensi') {
                $allRatings = $idpKomp->pengerjaans->flatMap(function ($pengerjaan) {
                    return $pengerjaan->nilaiPengerjaanIdp->pluck('rating')->map(fn($r) => (int)$r);
                });

                if ($allRatings->count() > 0) {
                    $softRatings[] = $allRatings->avg();
                }
            }
        }

        if (empty($softRatings)) {
            return 0;
        }

        $nilaiAkhir = array_sum($softRatings) / count($softRatings);
        return round($nilaiAkhir, 2);
    }

    /**
     * Hitung nilai akhir hard kompetensi
     */
    private function hitungNilaiAkhirHard(Idp $idp)
    {
        $hardRatings = [];

        foreach ($idp->idpKompetensis as $idpKomp) {
            $komp = $idpKomp->kompetensi;

            if ($komp->jenis_kompetensi === 'Hard Kompetensi') {
                $allRatings = $idpKomp->pengerjaans->flatMap(function ($pengerjaan) {
                    return $pengerjaan->nilaiPengerjaanIdp->pluck('rating')->map(fn($r) => (int)$r);
                });

                if ($allRatings->count() > 0) {
                    $hardRatings[] = $allRatings->avg();
                }
            }
        }

        if (empty($hardRatings)) {
            return 0;
        }

        $nilaiAkhir = array_sum($hardRatings) / count($hardRatings);
        return round($nilaiAkhir, 2);
    }
}
