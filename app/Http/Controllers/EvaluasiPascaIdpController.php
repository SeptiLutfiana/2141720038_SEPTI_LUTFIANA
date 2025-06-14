<?php

namespace App\Http\Controllers;

use App\Models\BankEvaluasi;
use App\Models\EvaluasiIdp;
use App\Models\EvaluasiIdpJawaban;
use Illuminate\Http\Request;
use App\Models\IDP;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EvaluasiPascaIdpController extends Controller
{
    // admin
    public function index(Request $request)
    {
        $search = $request->query('search');
        $jenisEvaluasi = $request->query('jenis_evaluasi');
        $tipePertanyaan = $request->query('tipe_pertanyaan');

        $evaluasiPasca = EvaluasiIdp::when($search, function ($query, $search) {
            return $query->where('pertanyaan', 'like', "%$search%")
                ->orWhere('jenis_evaluasi', 'like', "%$search%")
                ->orWhere('tipe_pertanyaan', 'like', "%$search%")
                ->orWhere('untuk_role', 'like', "%$search%");
        })
            ->when($jenisEvaluasi, function ($query, $jenisEvaluasi) {
                return $query->where('jenis_evaluasi', $jenisEvaluasi);
            })
            ->when($tipePertanyaan, function ($query, $tipePertanyaan) {
                return $query->where('tipe_pertanyaan', $tipePertanyaan);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        return view('adminsdm.BankEvaluasi.EvaluasiPascaIdp.index', [
            'type_menu' => 'evaluasi',
            'evaluasiPasca' => $evaluasiPasca,
            'search' => $search,
            'jenisEvaluasi' => $jenisEvaluasi,
            'tiperPertanyaan' => $tipePertanyaan,

        ]);
    }
    public function indexKaryawan(Request $request)
    {
        $search = $request->query('search');
        $jenisEvaluasi = $request->query('jenis_evaluasi');
        $tipePertanyaan = $request->query('tipe_pertanyaan');

        $evaluasiPasca = EvaluasiIdp::when($search, function ($query, $search) {
            return $query->where('pertanyaan', 'like', "%$search%")
                ->orWhere('jenis_evaluasi', 'like', "%$search%")
                ->orWhere('tipe_pertanyaan', 'like', "%$search%")
                ->orWhere('untuk_role', 'like', "%$search%");
        })
            ->when($jenisEvaluasi, function ($query, $jenisEvaluasi) {
                return $query->where('jenis_evaluasi', $jenisEvaluasi);
            })
            ->when($tipePertanyaan, function ($query, $tipePertanyaan) {
                return $query->where('tipe_pertanyaan', $tipePertanyaan);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        return view('karyawan.EvaluasiIdp.index', [
            'type_menu' => 'evaluasi',
            'evaluasiPasca' => $evaluasiPasca,
            'search' => $search,
            'jenisEvaluasi' => $jenisEvaluasi,
            'tiperPertanyaan' => $tipePertanyaan,

        ]);
    }
    // public function storeKaryawan(Request $request)
    // {
    //     $request->validate([
    //         'id_idp' => 'required|exists:idps,id_idp',
    //         'id_user' => 'required|exists:users,id',
    //         'jenis_evaluasi' => 'required|in:pasca,onboarding',
    //     ]);

    //     $role = Auth::user()->role;

    //     // Simpan data utama evaluasi
    //     $evaluasi = EvaluasiIdp::create([
    //         'id_idp' => $request->id_idp,
    //         'id_user' => $request->id_user,
    //         'jenis_evaluasi' => $request->jenis_evaluasi,
    //         'untuk_role' => $role,
    //     ]);

    //     // Simpan semua jawaban likert (jika ada)
    //     if ($request->has('jawaban_likert')) {
    //         foreach ($request->jawaban_likert as $id_bank_evaluasi => $nilai) {
    //             EvaluasiIdpJawaban::create([
    //                 'id_evaluasi_idp' => $evaluasi->id,
    //                 'id_bank_evaluasi' => $id_bank_evaluasi,
    //                 'jawaban' => $nilai,
    //             ]);
    //         }
    //     }

    //     // Simpan semua jawaban esai (jika ada)
    //     if ($request->has('jawaban_esai')) {
    //         foreach ($request->jawaban_esai as $id_bank_evaluasi => $isi) {
    //             EvaluasiIdpJawaban::create([
    //                 'id_evaluasi_idp' => $evaluasi->id,
    //                 'id_bank_evaluasi' => $id_bank_evaluasi,
    //                 'jawaban' => $isi,
    //             ]);
    //         }
    //     }

    //     return redirect()
    //         ->route('adminsdm.BankEvaluasi.EvaluasiPascaIdp.index')
    //         ->with('msg-success', 'Evaluasi berhasil disimpan.');
    // }
    public function indexMentor(Request $request)
    {
        $search = $request->query('search');
        $jenisEvaluasi = $request->query('jenis_evaluasi');
        $tipePertanyaan = $request->query('tipe_pertanyaan');

        $evaluasiPasca = EvaluasiIdp::when($search, function ($query, $search) {
            return $query->where('pertanyaan', 'like', "%$search%")
                ->orWhere('jenis_evaluasi', 'like', "%$search%")
                ->orWhere('tipe_pertanyaan', 'like', "%$search%")
                ->orWhere('untuk_role', 'like', "%$search%");
        })
            ->when($jenisEvaluasi, function ($query, $jenisEvaluasi) {
                return $query->where('jenis_evaluasi', $jenisEvaluasi);
            })
            ->when($tipePertanyaan, function ($query, $tipePertanyaan) {
                return $query->where('tipe_pertanyaan', $tipePertanyaan);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        return view('mentor.EvaluasiIdp.index', [
            'type_menu' => 'evaluasi',
            'evaluasiPasca' => $evaluasiPasca,
            'search' => $search,
            'jenisEvaluasi' => $jenisEvaluasi,
            'tiperPertanyaan' => $tipePertanyaan,

        ]);
    }
    public function indexSpv(Request $request)
    {
        $search = $request->query('search');
        $jenisEvaluasi = $request->query('jenis_evaluasi');
        $tipePertanyaan = $request->query('tipe_pertanyaan');

        $evaluasiPasca = EvaluasiIdp::when($search, function ($query, $search) {
            return $query->where('pertanyaan', 'like', "%$search%")
                ->orWhere('jenis_evaluasi', 'like', "%$search%")
                ->orWhere('tipe_pertanyaan', 'like', "%$search%")
                ->orWhere('untuk_role', 'like', "%$search%");
        })
            ->when($jenisEvaluasi, function ($query, $jenisEvaluasi) {
                return $query->where('jenis_evaluasi', $jenisEvaluasi);
            })
            ->when($tipePertanyaan, function ($query, $tipePertanyaan) {
                return $query->where('tipe_pertanyaan', $tipePertanyaan);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        return view('supervisor.EvaluasiIdp.index', [
            'type_menu' => 'evaluasi',
            'evaluasiPasca' => $evaluasiPasca,
            'search' => $search,
            'jenisEvaluasi' => $jenisEvaluasi,
            'tiperPertanyaan' => $tipePertanyaan,

        ]);
    }
    // karywan
    public function create(Request $request)
    {
        $id_idp = $request->query('id_idp');
        $id_user = $request->query('id_user');
        $jenisEvaluasi = $request->query('jenis', 'pasca');
        $sebagaiRole = Auth::user()->roles->pluck('nama_role')->first();

        // Ambil pertanyaan untuk role karyawan
        $pertanyaans = BankEvaluasi::where('jenis_evaluasi', $jenisEvaluasi)
            ->where('untuk_role', 'karyawan')
            ->get();

        return view('karyawan.EvaluasiIdp.create', [
            'id_idp' => $id_idp,
            'id_user' => $id_user,
            'jenisEvaluasi' => $jenisEvaluasi,
            'pertanyaans' => $pertanyaans,
            'sebagai_role' => $sebagaiRole,
            'type_menu' => 'evaluasi',
        ]);
    }
    // karywan
    public function store(Request $request)
    {
        $request->validate([
            'id_idp' => 'required|exists:idps,id_idp',
            'id_user' => 'required|exists:users,id',
            'jenis_evaluasi' => 'required|in:onboarding,pasca',
        ]);
        $sebagaiRole = Auth::user()->roles->pluck('nama_role')->first(); // atau sesuaikan jika struktur berbeda

        $evaluasi = EvaluasiIdp::create([
            'id_idp' => $request->id_idp,
            'id_user' => $request->id_user,
            'jenis_evaluasi' => $request->jenis_evaluasi,
            'tanggal_evaluasi' => now(),
            'sebagai_role' => $sebagaiRole, // Disimpan di sini

        ]);

        if ($request->has('jawaban_likert')) {
            foreach ($request->jawaban_likert as $id_bank => $nilai) {
                EvaluasiIdpJawaban::create([
                    'id_evaluasi_idp' => $evaluasi->id_evaluasi_idp,
                    'id_bank_evaluasi' => $id_bank,
                    'jawaban_likert' => $nilai,
                    'jawaban_esai' => null,
                ]);
            }
        }

        if ($request->has('jawaban_esai')) {
            foreach ($request->jawaban_esai as $id_bank => $teks) {
                EvaluasiIdpJawaban::create([
                    'id_evaluasi_idp' => $evaluasi->id_evaluasi_idp,
                    'id_bank_evaluasi' => $id_bank,
                    'jawaban_likert' => null,
                    'jawaban_esai' => $teks,
                ]);
            }
        }

        return redirect()->route('karyawan.EvaluasiIdp.EvaluasiPascaIdp.indexKaryawan')->with('msg-success', 'Evaluasi berhasil dikirim');
    }
    public function showKaryawan($id)
    {
        $evaluasi = EvaluasiIdp::with(['user', 'jawaban.bankEvaluasi'])->findOrFail($id);

        return view('adminsdm.BankEvaluasi.EvaluasiPascaIdp.detail', [
            'evaluasi' => $evaluasi,
            'type_menu' => 'evaluasi',
        ]);
    }
    // mentor
    public function createMentor(Request $request)
    {
        $id_idp = $request->query('id_idp');
        $id_user = $request->query('id_user');
        $jenisEvaluasi = $request->query('jenis', 'pasca');
        $sebagaiRole = 'Mentor';
        $pertanyaans = BankEvaluasi::where('jenis_evaluasi', $jenisEvaluasi)
            ->where('untuk_role', 'mentor')
            ->get();

        return view('mentor.EvaluasiIdp.create', [
            'id_idp' => $id_idp,
            'id_user' => $id_user,
            'jenisEvaluasi' => $jenisEvaluasi,
            'pertanyaans' => $pertanyaans,
            'sebagai_role' => $sebagaiRole,
            'type_menu' => 'evaluasi',
        ]);
    }
    public function storeMentor(Request $request)
    {
        $request->validate([
            'id_idp' => 'required|exists:idps,id_idp',
            'id_user' => 'required|exists:users,id',
            'jenis_evaluasi' => 'required|in:onboarding,pasca',
        ]);
        $evaluasi = EvaluasiIdp::create([
            'id_idp' => $request->id_idp,
            'id_user' => Auth::id(), // ← PASTIKAN ini dipakai
            'jenis_evaluasi' => $request->jenis_evaluasi,
            'tanggal_evaluasi' => now(),
            'sebagai_role' => 'Mentor',
        ]);

        if ($request->has('jawaban_likert')) {
            foreach ($request->jawaban_likert as $id_bank => $nilai) {
                EvaluasiIdpJawaban::create([
                    'id_evaluasi_idp' => $evaluasi->id_evaluasi_idp,
                    'id_bank_evaluasi' => $id_bank,
                    'jawaban_likert' => $nilai,
                    'jawaban_esai' => null,
                ]);
            }
        }

        if ($request->has('jawaban_esai')) {
            foreach ($request->jawaban_esai as $id_bank => $teks) {
                EvaluasiIdpJawaban::create([
                    'id_evaluasi_idp' => $evaluasi->id_evaluasi_idp,
                    'id_bank_evaluasi' => $id_bank,
                    'jawaban_likert' => null,
                    'jawaban_esai' => $teks,
                ]);
            }
        }

        return redirect()->route('mentor.EvaluasiIdp.EvaluasiPascaIdp.indexMentor')->with('msg-success', 'Evaluasi berhasil dikirim');
    }
    public function createSpv(Request $request)
    {
        $id_idp = $request->query('id_idp');
        $id_user = $request->query('id_user');
        $jenisEvaluasi = $request->query('jenis', 'pasca');
        $sebagaiRole = 'Supervisor';
        $pertanyaans = BankEvaluasi::where('jenis_evaluasi', $jenisEvaluasi)
            ->where('untuk_role', 'supervisor')
            ->get();

        return view('supervisor.EvaluasiIdp.create', [
            'id_idp' => $id_idp,
            'id_user' => $id_user,
            'jenisEvaluasi' => $jenisEvaluasi,
            'pertanyaans' => $pertanyaans,
            'sebagai_role' => $sebagaiRole,
            'type_menu' => 'evaluasi',
        ]);
    }
    public function storeSpv(Request $request)
    {
        $request->validate([
            'id_idp' => 'required|exists:idps,id_idp',
            'id_user' => 'required|exists:users,id',
            'jenis_evaluasi' => 'required|in:onboarding,pasca',
        ]);
        $evaluasi = EvaluasiIdp::create([
            'id_idp' => $request->id_idp,
            'id_user' => Auth::id(), // ← PASTIKAN ini dipakai
            'jenis_evaluasi' => $request->jenis_evaluasi,
            'tanggal_evaluasi' => now(),
            'sebagai_role' => 'Supervisor',
        ]);

        if ($request->has('jawaban_likert')) {
            foreach ($request->jawaban_likert as $id_bank => $nilai) {
                EvaluasiIdpJawaban::create([
                    'id_evaluasi_idp' => $evaluasi->id_evaluasi_idp,
                    'id_bank_evaluasi' => $id_bank,
                    'jawaban_likert' => $nilai,
                    'jawaban_esai' => null,
                ]);
            }
        }

        if ($request->has('jawaban_esai')) {
            foreach ($request->jawaban_esai as $id_bank => $teks) {
                EvaluasiIdpJawaban::create([
                    'id_evaluasi_idp' => $evaluasi->id_evaluasi_idp,
                    'id_bank_evaluasi' => $id_bank,
                    'jawaban_likert' => null,
                    'jawaban_esai' => $teks,
                ]);
            }
        }

        return redirect()->route('supervisor.EvaluasiIdp.indexSpv')->with('msg-success', 'Evaluasi berhasil dikirim');
    }
}
