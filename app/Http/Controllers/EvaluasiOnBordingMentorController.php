<?php

namespace App\Http\Controllers;

use App\Models\BankEvaluasi;
use App\Models\EvaluasiIdp;
use App\Models\EvaluasiIdpJawaban;
use Illuminate\Http\Request;

class EvaluasiOnBordingMentorController extends Controller
{
    public function indexMentor()
    {
        return view('mentor.EvaluasiIdp.EvaluasiOnBording.index', [
            'type_menu' => 'evaluasi',
        ]);
    }
    public function create(Request $request)
    {
        $id_idp = $request->id_idp;
        $id_user = $request->id_user;

        return view('mentor.EvaluasiIdp.EvaluasiOnBording.create', [
            'id_idp' => $id_idp,
            'id_user' => $id_user,
            'jenis' => 'onboarding',
            'type_menu' => 'evaluasi',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_idp' => 'required|exists:idps,id_idp',
            'id_user' => 'required|exists:users,id',
            'catatan' => 'required|string',
        ]);

        EvaluasiIdp::create([
            'id_idp' => $request->id_idp,
            'id_user' => $request->id_user,
            'jenis_evaluasi' => 'onboarding',
            'tanggal_evaluasi' => now(),
            'sebagai_role' => 'mentor',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('mentor.EvaluasiIdp.EvaluasiOnBording.indexMentor')->with('msg-success', 'Evaluasi onboarding berhasil disimpan.');
    }
}
