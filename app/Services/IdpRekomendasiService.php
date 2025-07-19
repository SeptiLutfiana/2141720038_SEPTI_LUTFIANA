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

            $idp = $idp->fresh([
                'idpKompetensis.kompetensi',
                'idpKompetensis.pengerjaans.nilaiPengerjaanIdp'
            ]);

            $softs = [];
            $hards = [];

            foreach ($idp->idpKompetensis as $idpKomp) {
                $komp = $idpKomp->kompetensi;
                $jenis = $komp->jenis_kompetensi;

                $ratings = collect();

                foreach ($idpKomp->pengerjaans as $pengerjaan) {
                    $ratings = $ratings->merge(
                        $pengerjaan->nilaiPengerjaanIdp()
                            ->whereHas('idpKompetensiPengerjaan.idpKompetensi', function ($query) use ($idp) {
                                $query->where('id_idp', $idp->id_idp);
                            })
                            ->pluck('rating')
                            ->filter()
                            ->map(fn($r) => (float) $r)
                    );
                }

                if ($ratings->isEmpty()) {
                    Log::warning('⛔ Kompetensi dilewati karena tidak ada rating valid', [
                        'kompetensi' => $komp->nama_kompetensi ?? 'unknown'
                    ]);
                    continue;
                }

                $avgRating = $ratings->avg();

                Log::info('Kompetensi ditemukan', [
                    'nama' => $komp->nama_kompetensi ?? '-',
                    'jenis' => $jenis,
                    'rating_rata2' => $avgRating,
                    'rating_detail' => $ratings->toArray(),
                ]);

                if ($jenis === 'Soft Kompetensi') {
                    $softs[] = [
                        'peran' => $idpKomp->peran,
                        'ratings' => $ratings->toArray()
                    ];
                } elseif ($jenis === 'Hard Kompetensi') {
                    $hards[] = $ratings->avg(); // hanya ambil rata-rata per kompetensi
                }
            }

            $hasilSoft = $this->hitungHasilSoftKompetensi($softs);
            $hasilHard = $this->hitungHasilHardKompetensi($hards);
            if (!empty($softs) && empty($hards)) {
    // Hanya ada soft kompetensi
    $finalHasil = $hasilSoft;
} elseif (empty($softs) && !empty($hards)) {
    // Hanya ada hard kompetensi
    $finalHasil = $hasilHard;
} elseif (!empty($softs) && !empty($hards)) {
    // Gabungan soft dan hard → pakai matriks gabungan
    $finalHasil = $this->tentukanHasilFinal($hasilSoft, $hasilHard);
} else {
    // Tidak ada data
    $finalHasil = [
        'hasil' => 'Menunggu Hasil',
        'deskripsi' => 'Belum ada kompetensi yang bisa dievaluasi.'
    ];
}

            $nilaiSoft = $this->hitungNilaiAkhirSoft($idp);
            $nilaiHard = $this->hitungNilaiAkhirHard($idp);

            Log::info('Final hasil rekomendasi', [
                'hasil_rekomendasi' => $finalHasil['hasil'],
                'deskripsi' => $finalHasil['deskripsi'],
                'nilai_soft' => $nilaiSoft,
                'nilai_hard' => $nilaiHard
            ]);

            IdpRekomendasi::updateOrCreate(
                ['id_idp' => $idp->id_idp],
                [
                    'hasil_rekomendasi' => $finalHasil['hasil'],
                    'deskripsi_rekomendasi' => $finalHasil['deskripsi'],
                    'nilai_akhir_soft' => $nilaiSoft,
                    'nilai_akhir_hard' => $nilaiHard,
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

    private function hitungHasilSoftKompetensi($softs)
    {
        if (empty($softs)) {
        Log::info('⛔ Lewati analisis soft kompetensi karena tidak ada data');
        return null; // atau return default seperti ['hasil' => null, 'deskripsi' => 'Tidak ada soft kompetensi']
    }
        $ratings = [];
        $totalKompetensi = count($softs);
        $utamaKurang3 = false;
        $kunciKurang3 = false;
        $utamaAtauKunciRating2 = false;

        foreach ($softs as $soft) {
            foreach ($soft['ratings'] as $r) {
                $ratings[] = intval(round($r));
                if ($soft['peran'] === 'utama' && $r < 3) {
                    $utamaKurang3 = true;
                    if ($r == 2) $utamaAtauKunciRating2 = true;
                }
                if (in_array($soft['peran'], ['kunci_core', 'kunci_bisnis', 'kunci_enabler']) && $r < 3) {
                    $kunciKurang3 = true;
                    if ($r == 2) $utamaAtauKunciRating2 = true;
                }
            }
        }

        $count1 = count(array_filter($ratings, fn($r) => $r == 1));
        $count2 = count(array_filter($ratings, fn($r) => $r == 2));
        $count3to5 = count(array_filter($ratings, fn($r) => $r >= 3 && $r <= 5));

        Log::info('Analisis soft kompetensi', compact(
            'totalKompetensi',
            'count3to5',
            'count2',
            'count1',
            'utamaKurang3',
            'kunciKurang3',
            'utamaAtauKunciRating2'
        ));

        if ($count1 > 0 || ($utamaKurang3 && $kunciKurang3)) {
            return [
                'hasil' => 'Tidak Disarankan',
                'deskripsi' => 'Soft skill tidak memenuhi standar minimum yang diperlukan.'
            ];
        }

        if ($totalKompetensi < 6) {
            return $this->evaluasiKompetensiSedikit($count3to5, $count2, $count1, $utamaKurang3, $kunciKurang3, $utamaAtauKunciRating2, $totalKompetensi);
        }

        if ($totalKompetensi < 12) {
            return $this->evaluasiKompetensiSedang($count3to5, $count2, $count1, $utamaKurang3, $kunciKurang3, $utamaAtauKunciRating2, $totalKompetensi);
        }

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
        $soft = $hasilSoft['hasil'] ?? 'Menunggu Hasil';
        $hard = $hasilHard['hasil'] ?? 'Menunggu Hasil';

        // Jika salah satu masih menunggu hasil
        if ($soft === 'Menunggu Hasil' && $hard !== 'Menunggu Hasil') {
            return $hasilHard;
        } elseif ($hard === 'Menunggu Hasil' && $soft !== 'Menunggu Hasil') {
            return $hasilSoft;
        } elseif ($soft === 'Menunggu Hasil' && $hard === 'Menunggu Hasil') {
            return [
                'hasil' => 'Menunggu Hasil',
                'deskripsi' => 'Belum ada kompetensi yang bisa dievaluasi.'
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
        Log::debug('🔍 Debug kombinasi hasil untuk matrix final', [
            'soft' => $soft,
            'hard' => $hard,
            'soft_original' => $hasilSoft,
            'hard_original' => $hasilHard,
            'matrix_key_exists' => isset($matrix[$soft][$hard]) ? '✅ YES' : '❌ NO',
        ]);
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
        $allSoftRatings = collect();

        foreach ($idp->idpKompetensis as $idpKomp) {
            $komp = $idpKomp->kompetensi;

            if ($komp->jenis_kompetensi === 'Soft Kompetensi') {
                foreach ($idpKomp->pengerjaans as $pengerjaan) {
                    // Pastikan hanya mengambil satu rating per pengerjaan
                    $nilaiPengerjaan = $pengerjaan->nilaiPengerjaanIdp;
                    if ($nilaiPengerjaan && $nilaiPengerjaan->rating !== null) {
                        $allSoftRatings->push((int) $nilaiPengerjaan->rating);
                    }
                }
            }
        }

        Log::info('Debug nilai akhir soft', [
            'all_soft_ratings' => $allSoftRatings->toArray(),
            'count' => $allSoftRatings->count(),
            'avg' => $allSoftRatings->count() > 0 ? $allSoftRatings->avg() : 0
        ]);

        if ($allSoftRatings->count() === 0) return 0;

        return round($allSoftRatings->avg(), 2);
    }

    /**
     * FIX: Hitung nilai akhir hard kompetensi  
     * Pastikan konsisten dengan pengumpulan data
     */
    private function hitungNilaiAkhirHard(Idp $idp)
    {
        $allHardRatings = collect();

        foreach ($idp->idpKompetensis as $idpKomp) {
            $komp = $idpKomp->kompetensi;

            if ($komp->jenis_kompetensi === 'Hard Kompetensi') {
                foreach ($idpKomp->pengerjaans as $pengerjaan) {
                    // Pastikan hanya mengambil satu rating per pengerjaan
                    $nilaiPengerjaan = $pengerjaan->nilaiPengerjaanIdp;
                    if ($nilaiPengerjaan && $nilaiPengerjaan->rating !== null) {
                        $allHardRatings->push((int) $nilaiPengerjaan->rating);
                    }
                }
            }
        }

        Log::info('Debug nilai akhir hard', [
            'all_hard_ratings' => $allHardRatings->toArray(),
            'count' => $allHardRatings->count(),
            'avg' => $allHardRatings->count() > 0 ? $allHardRatings->avg() : 0
        ]);

        if ($allHardRatings->count() === 0) return 0;

        return round($allHardRatings->avg(), 2);
    }
}
