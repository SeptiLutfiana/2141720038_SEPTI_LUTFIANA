<?php

use App\Exports\HardKompetensiExport;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AngkatanPspController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankEvaluasiController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\EvaluasiPascaIdpController;
use App\Http\Controllers\IdpController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JenjangController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KaryawanDashboardController;
use App\Http\Controllers\KompetensiController;
use App\Http\Controllers\LearninggroupController;
use App\Http\Controllers\MentorDashboardController;
use App\Http\Controllers\MetodeBelajarController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\PenempatanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\TambahMentorController;
use App\Http\Controllers\TambahSupervisorController;

// Login & Logout
Route::middleware('web')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::post('/switch-role', [KaryawanController::class, 'switchRole'])->name('switchRole');
});

// Redirect Google Auth
Route::get('/auth/google/redirect', [AuthController::class, 'google_redirect']);
Route::get('/auth/google/callback', [AuthController::class, 'google_callback']);

// Halaman Utama
Route::get('/', function () {
    return view('welcome');
});

Route::post('/switch-role', [AuthenticatedSessionController::class, 'switch'])->name('switchRole');

// Middleware protection
Route::middleware(['auth', 'karyawan:1,4,2,3'])->group(function () {

    // ADMIN SDM
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('adminsdm.dashboard');
    // DATA MASTER
    // DIvisi
    Route::get('/admin/datamaster/divisi', [DivisiController::class, 'index'])->name('adminsdm.data-master.karyawan.divisi.index');
    Route::get('/admin/datamaster/divisi/create', [DivisiController::class, 'create'])->name('adminsdm.data-master.karyawan.divisi.create');
    Route::post('/admin/datamaster/divisi/store', [DivisiController::class, 'store'])->name('adminsdm.data-master.karyawan.divisi.store');
    Route::get('/admin/datamaster/divisi/{id}/edit', [DivisiController::class, 'edit'])->name('adminsdm.data-master.karyawan.divisi.edit');
    Route::put('/admin/datamaster/divisi/{id}/update', [DivisiController::class, 'update'])->name('adminsdm.data-master.karyawan.divisi.update');
    Route::get('/admin/datamaster/divisi/{id}/detail', [DivisiController::class, 'show'])->name('adminsdm.data-master.karyawan.divisi.show');
    Route::delete('/admin/datamaster/divisi/{divisi}', [DivisiController::class, 'destroy'])->name('adminsdm.data-master.karyawan.divisi.destroy');
    Route::resource('divisi', DivisiController::class);
    Route::get('/admin/datamaster/divisi/cetak/pdf', [DivisiController::class, 'printPdf'])->name('adminsdm.data-master.karyawan.divisi.printPdf');
    Route::get('/admin/datamaster/divisi/export/excel', [DivisiController::class, 'exportExcel'])->name('adminsdm.data-master.karyawan.divisi.exportExcel');
    Route::get('/admin/datamaster/divisi/export/csv', [DivisiController::class, 'exportCSV'])->name('admin.data-master.karyawan.divisi.exportCSV');
    Route::get('/admin/datamaster/divisi/export/docx', [DivisiController::class, 'exportDocx'])->name('admin.data-master.karyawan.divisi.exportDocx');

    // Jabatan
    Route::prefix('admin/datamaster/jabatan')->name('adminsdm.data-master.karyawan.jabatan.')->group(function () {
        Route::get('/', [JabatanController::class, 'index'])->name('index');
        Route::get('/create', [JabatanController::class, 'create'])->name('create');
        Route::post('/store', [JabatanController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [JabatanController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [JabatanController::class, 'update'])->name('update');
        Route::get('/{id}/detail', [JabatanController::class, 'show'])->name('show');
        Route::delete('/{jabatan}', [JabatanController::class, 'destroy'])->name('destroy');
        Route::get('/cetak/pdf', [JabatanController::class, 'printPdf'])->name('printPdf');
        Route::get('/export/excel', [JabatanController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export/csv', [JabatanController::class, 'exportCSV'])->name('exportCSV');
        Route::get('/export/docx', [JabatanController::class, 'exportDocx'])->name('exportDocx');
    });
    // Angkatan PSP
    Route::prefix('admin/datamaster/angkatanpsp')->name('adminsdm.data-master.karyawan.angkatan-psp.')->group(function () {
        Route::get('/', [AngkatanPspController::class, 'index'])->name('index');
        Route::get('/create', [AngkatanPspController::class, 'create'])->name('create');
        Route::post('/store', [AngkatanPspController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AngkatanPspController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [AngkatanPspController::class, 'update'])->name('update');
        Route::get('/{id}/detail', [AngkatanPspController::class, 'show'])->name('show');
        Route::delete('/{angkatanpsp}', [AngkatanPspController::class, 'destroy'])->name('destroy');
        Route::get('/cetak/pdf', [AngkatanPspController::class, 'printPdf'])->name('printPdf');
        Route::get('/export/excel', [AngkatanPspController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export/csv', [AngkatanPspController::class, 'exportCSV'])->name('exportCSV');
        Route::get('/export/docx', [AngkatanPspController::class, 'exportDocx'])->name('exportDocx');
    });
    // Penempatan

    Route::prefix('admin/datamaster/penempatan')->name('adminsdm.data-master.karyawan.penempatan.')->group(function () {
        Route::get('/', [PenempatanController::class, 'index'])->name('index');
        Route::get('/create', [PenempatanController::class, 'create'])->name('create');
        Route::post('/store', [PenempatanController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PenempatanController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [PenempatanController::class, 'update'])->name('update');
        Route::get('/{id}/detail', [PenempatanController::class, 'show'])->name('show');
        Route::delete('/{penempatan}', [PenempatanController::class, 'destroy'])->name('destroy');
        Route::get('/cetak/pdf', [PenempatanController::class, 'printPdf'])->name('printPdf');
        Route::get('/export/excel', [PenempatanController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export/csv', [PenempatanController::class, 'exportCSV'])->name('exportCSV');
        Route::get('/export/docx', [PenempatanController::class, 'exportDocx'])->name('exportDocx');
    });
    // jenjang
    Route::prefix('admin/datamaster/jenjang')->name('adminsdm.data-master.karyawan.jenjang.')->group(function () {
        Route::get('/', [JenjangController::class, 'index'])->name('index');
        Route::get('/create', [JenjangController::class, 'create'])->name('create');
        Route::post('/store', [JenjangController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [JenjangController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [JenjangController::class, 'update'])->name('update');
        Route::get('/{id}/detail', [JenjangController::class, 'show'])->name('show');
        Route::delete('/{jenjang}', [JenjangController::class, 'destroy'])->name('destroy');
        Route::get('/cetak/pdf', [JenjangController::class, 'printPdf'])->name('printPdf');
        Route::get('/export/excel', [JenjangController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export/csv', [JenjangController::class, 'exportCSV'])->name('exportCSV');
        Route::get('/export/docx', [JenjangController::class, 'exportDocx'])->name('exportDocx');
    });
    // Learning Group
    Route::prefix('admin/datamaster/learning/group')->name('adminsdm.data-master.karyawan.learning-group.')->group(function () {
        Route::get('/', [LearninggroupController::class, 'index'])->name('index');
        Route::get('/create', [LearninggroupController::class, 'create'])->name('create');
        Route::post('/store', [LearninggroupController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [LearninggroupController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [LearninggroupController::class, 'update'])->name('update');
        Route::get('/{id}/detail', [LearninggroupController::class, 'show'])->name('show');
        Route::delete('/{LG}', [LearninggroupController::class, 'destroy'])->name('destroy');
        Route::get('/cetak/pdf', [LearninggroupController::class, 'printPdf'])->name('printPdf');
        Route::get('/export/excel', [LearninggroupController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export/csv', [LearninggroupController::class, 'exportCSV'])->name('exportCSV');
        Route::get('/export/docx', [LearninggroupController::class, 'exportDocx'])->name('exportDocx');
    });
    // Role
    Route::prefix('admin/datamaster/role')->name('adminsdm.data-master.karyawan.role.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/store', [RoleController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [RoleController::class, 'update'])->name('update');
        Route::get('/{id}/detail', [RoleController::class, 'show'])->name('show');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    });
    // karyawan - User
    Route::prefix('admin/datamaster/karyawan')->name('adminsdm.data-master.karyawan.data-karyawan.')->group(function () {
        Route::get('/', [KaryawanController::class, 'index'])->name('index');
        Route::get('/create', [KaryawanController::class, 'create'])->name('create');
        Route::post('/store', [KaryawanController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [KaryawanController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [KaryawanController::class, 'update'])->name('update');
        Route::get('/{id}/detail', [KaryawanController::class, 'show'])->name('show');
        Route::delete('/{user}', [KaryawanController::class, 'destroy'])->name('destroy');
        Route::get('/cetak/pdf', [KaryawanController::class, 'printPdf'])->name('printPdf');
        Route::get('/export/excel', [KaryawanController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export/csv', [KaryawanController::class, 'exportCSV'])->name('exportCSV');
        Route::get('/export/docx', [KaryawanController::class, 'exportDocx'])->name('exportDocx');
        Route::get('/get-jabatan-by-jenjang/{id}', [KaryawanController::class, 'getJabatanByJenjang']);
    });
    // Semester
    Route::prefix('admin/datamaster/semester')->name('adminsdm.data-master.data-idp.semester.')->group(function () {
        Route::get('/', [SemesterController::class, 'index'])->name('index');
        Route::get('/create', [SemesterController::class, 'create'])->name('create');
        Route::post('/store', [SemesterController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SemesterController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [SemesterController::class, 'update'])->name('update');
        Route::get('/{id}/detail', [SemesterController::class, 'show'])->name('show');
        Route::delete('/{semester}', [SemesterController::class, 'destroy'])->name('destroy');
        Route::get('/cetak/pdf', [SemesterController::class, 'printPdf'])->name('printPdf');
        Route::get('/export/excel', [SemesterController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export/csv', [SemesterController::class, 'exportCSV'])->name('exportCSV');
        Route::get('/export/docx', [SemesterController::class, 'exportDocx'])->name('exportDocx');
    });
    // Metode Belajar
    Route::prefix('admin/datamaster/metode/belajar')->name('adminsdm.data-master.data-idp.metode-belajar.')->group(function () {
        Route::get('/', [MetodeBelajarController::class, 'index'])->name('index');
        Route::get('/create', [MetodeBelajarController::class, 'create'])->name('create');
        Route::post('/store', [MetodeBelajarController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [MetodeBelajarController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [MetodeBelajarController::class, 'update'])->name('update');
        Route::get('/{id}/detail', [MetodeBelajarController::class, 'show'])->name('show');
        Route::delete('/{metodebelajar}', [MetodeBelajarController::class, 'destroy'])->name('destroy');
        Route::get('/cetak/pdf', [MetodeBelajarController::class, 'printPdf'])->name('printPdf');
        Route::get('/export/excel', [MetodeBelajarController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export/csv', [MetodeBelajarController::class, 'exportCSV'])->name('exportCSV');
        Route::get('/export/docx', [MetodeBelajarController::class, 'exportDocx'])->name('exportDocx');
    });
    // Kompetensi
    Route::prefix('admin/datamaster/kompetensi')->name('adminsdm.data-master.kompetensi.')->group(function () {
        Route::get('/soft', [KompetensiController::class, 'indexSoft'])->name('indexSoft');
        Route::get('/hard', [KompetensiController::class, 'indexHard'])->name('indexHard');
        Route::get('/create', [KompetensiController::class, 'create'])->name('create');
        Route::post('/store', [KompetensiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [KompetensiController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [KompetensiController::class, 'update'])->name('update');
        Route::get('/{id}/detail/soft', [KompetensiController::class, 'showSoft'])->name('showSoft');
        Route::get('/{id}/detail/hard', [KompetensiController::class, 'showHard'])->name('showHard');
        Route::delete('/{kompetensi}', [KompetensiController::class, 'destroy'])->name('destroy');
        Route::get('/cetak/soft/kompetensi/pdf', [KompetensiController::class, 'printPdfSoft'])->name('printPdfSoft');
        Route::get('/cetak/hard/kompetensi/pdf', [KompetensiController::class, 'printPdfHard'])->name('printPdfHard');
        Route::get('/export/soft/kompetensi/excel', [KompetensiController::class, 'exportExcelSoft'])->name('exportExcelSoft');
        Route::get('/export/soft/kompetensi/csv', [KompetensiController::class, 'exportCSVSoft'])->name('exportCSVSoft');
        Route::get('/export/hard/kompetensi/excel', [KompetensiController::class, 'exportExcelHard'])->name('exportExcelHard');
        Route::get('/export/hard/kompetensi/csv', [KompetensiController::class, 'exportCSVHard'])->name('exportCSVHard');
        Route::get('/export/soft/kompetensi/docx', [KompetensiController::class, 'exportDocxSoft'])->name('exportDocxSoft');
        Route::get('/export/hard/kompetensi/docx', [KompetensiController::class, 'exportDocxHard'])->name('exportDocxHard');
        Route::get('/get-jabatan-by-jenjang/{id_jenjang}', [KompetensiController::class, 'getJabatanByJenjang'])->name('getJabatanByJenjang');
    });
    // mentor
    Route::prefix('admin/datamaster/mentor')->name('adminsdm.data-master.mentor.')->group(function () {
        Route::get('/', [TambahMentorController::class, 'index'])->name('index');
        Route::get('/create', [TambahMentorController::class, 'create'])->name('create');
        Route::post('/store', [TambahMentorController::class, 'store'])->name('store');
        Route::get('/{id}/detail', [TambahMentorController::class, 'show'])->name('show');
        Route::delete('/{id}', [TambahMentorController::class, 'destroy'])->name('destroy');
        Route::get('/cetak/pdf', [TambahMentorController::class, 'printPdf'])->name('printPdf');
        Route::get('/export/excel', [TambahMentorController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export/csv', [TambahMentorController::class, 'exportCSV'])->name('exportCSV');
        Route::get('/export/docx', [TambahMentorController::class, 'exportWord'])->name('exportWord');
    });

    // SPV
    Route::prefix('admin/datamaster/supervisor')->name('adminsdm.data-master.supervisor.')->group(function () {
        Route::get('/', [TambahSupervisorController::class, 'index'])->name('index');
        Route::get('/create', [TambahSupervisorController::class, 'create'])->name('create');
        Route::post('/store', [TambahSupervisorController::class, 'store'])->name('store');
        Route::get('/{id}/detail', [TambahSupervisorController::class, 'show'])->name('show');
        Route::delete('/{id}', [TambahSupervisorController::class, 'destroy'])->name('destroy');
        Route::get('/cetak/pdf', [TambahSupervisorController::class, 'printPdf'])->name('printPdf');
        Route::get('/export/excel', [TambahSupervisorController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export/csv', [TambahSupervisorController::class, 'exportCSV'])->name('exportCSV');
        Route::get('/export/docx', [TambahSupervisorController::class, 'exportWord'])->name('exportWord');
    });
    // Behavior IDP
    Route::prefix('admin/datamaster/behavior/idp')->name('adminsdm.BehaviorIDP.')->group(function () {
        Route::get('/given/idp', [IdpController::class, 'indexGiven'])->name('indexGiven');
        Route::get('/bank/idp', [IdpController::class, 'indexBankIdp'])->name('ListIDP.indexBankIdp');
        Route::get('/create', [IdpController::class, 'create'])->name('create');
        Route::post('/store', [IdpController::class, 'store'])->name('store');
        Route::get('/{id}/detail/given', [IdpController::class, 'showGiven'])->name('showGiven');
        Route::get('/{id}/detail', [IdpController::class, 'showBank'])->name('ListIDP.showBank');
        Route::get('/get-jabatan-by-jenjang/{id}', [IdpController::class, 'getJabatanByJenjang'])->name('getJabatanByJenjang');
        Route::get('/get-kompetensi-by-jabatan/{id}', [IdpController::class, 'getKompetensiByJabatan'])->name('getKompetensiByJabatan');
        Route::get('/{id}/edit', [IdpController::class, 'editGiven'])->name('editGiven');
        Route::put('/{id}/update', [IdpController::class, 'updateGiven'])->name('updateGiven');
        Route::get('/{id}/edit/bank/idp', [IdpController::class, 'editBank'])->name('ListIDP.editBank');
        Route::put('/{id}/update/bank/idp', [IdpController::class, 'updateBank'])->name('ListIDP.updateBank');
        Route::delete('/{idp}', [IdpController::class, 'destroyGiven'])->name('destroyGiven');
        Route::delete('/{idp}/bank/idp', [IdpController::class, 'destroyBank'])->name('ListIDP.destroyBank');
        Route::get('/riwayat/idp', [AdminDashboardController::class, 'indexRiwayatIdp'])->name('RiwayatIDP.indexRiwayatIdp');
        Route::get('/{id}/riwayat/idp', [AdminDashboardController::class, 'showRiwayatIDP'])->name('RiwayatIDP.showRiwayatIdp');
        Route::get('/idp/cetak/{id}', [AdminDashboardController::class, 'cetakPDF'])->name('adminsdm.idp.cetak');
        Route::get('/idp/cetak-filter', [AdminDashboardController::class, 'cetakFiltered'])->name('cetakFiltered');
        Route::get('/cetak/pdf', [IdpController::class, 'cetakPdf'])->name('ListIDP.cetakPdf');
        Route::get('/get-soft-kompetensi', [IDPController::class, 'getSoftKompetensi'])->name('getSoftKompetensi');
    });
    // Panduan IDP
    Route::prefix('admin/datamaster/panduan/idp')->name('adminsdm.Panduan.')->group(function () {
        Route::get('/', [PanduanController::class, 'index'])->name('index');
        Route::get('/create', [PanduanController::class, 'create'])->name('create');
        Route::post('/store', [PanduanController::class, 'store'])->name('store');
        Route::get('/{id}/detail', [PanduanController::class, 'show'])->name('show');
        Route::delete('/{panduan}', [PanduanController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/edit', [PanduanController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [PanduanController::class, 'update'])->name('update');
    });
    Route::prefix('admin/datamaster/bank/evaluasi')->name('adminsdm.BankEvaluasi.')->group(function () {
        Route::get('/', [BankEvaluasiController::class, 'index'])->name('index');
        Route::get('/create', [BankEvaluasiController::class, 'create'])->name('create');
        Route::post('/store', [BankEvaluasiController::class, 'store'])->name('store');
        Route::get('/{id}/detail', [BankEvaluasiController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BankEvaluasiController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [BankEvaluasiController::class, 'update'])->name('update');
        Route::delete('/{bankEvaluasi}', [BankEvaluasiController::class, 'destroy'])->name('destroy');
        Route::get('/pasca/idp', [EvaluasiPascaIdpController::class, 'index'])->name('EvaluasiPascaIdp.index');
        Route::get('/cetak/pdf', [BankEvaluasiController::class, 'printPdf'])->name('printPdf');
        Route::get('/export/excel', [BankEvaluasiController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export/csv', [BankEvaluasiController::class, 'exportCSV'])->name('exportCSV');
        Route::get('/export/docx', [BankEvaluasiController::class, 'exportDocx'])->name('exportDocx');
        Route::get('/idp/create', [EvaluasiPascaIdpController::class, 'create'])->name('EvaluasiPascaIdp.create');
        Route::post('/idp/store', [EvaluasiPascaIdpController::class, 'store'])->name('EvaluasiPascaIdp.store');
        Route::get('/{id}/detail/jawaban', [EvaluasiPascaIdpController::class, 'showKaryawan'])->name('EvaluasiPascaIdp.showKaryawan');
    });
});
Route::middleware(['auth', 'karyawan:2,3,4'])->group(function () {
    // SUPERVISOR
    Route::get('/supervisor/dashboard', [SupervisorDashboardController::class, 'index'])->name('supervisor.spv-dashboard');
    // IDP
    Route::prefix('supervisor/behavior/idp')->name('supervisor.IDP.')->group(function () {
        Route::get('/', [SupervisorDashboardController::class, 'indexSupervisor'])->name('indexSupervisor');
        Route::get('/detail/{id}', [SupervisorDashboardController::class, 'showSupervisor'])->name('showSupervisor');
        Route::post('/penilaian/store', [SupervisorDashboardController::class, 'store'])->name('store');
        Route::get('/riwayat', [SupervisorDashboardController::class, 'indexRiwayatIdp'])->name('RiwayatIDP.indexRiwayatIdp');
        Route::get('/{id}/riwayat', [SupervisorDashboardController::class, 'showRiwayatIDP'])->name('RiwayatIDP.showRiwayatIdp');
        Route::get('/idp/cetak-filter', [SupervisorDashboardController::class, 'cetakFiltered'])->name('RiwayatIDP.cetakFiltered');
    });
    Route::prefix('supervisor/panduan/idp')->name('supervisor.Panduan.')->group(function () {
        Route::get('/', [PanduanController::class, 'autoShowPanduanSupervisor'])->name('autoShowPanduanSupervisor');
    });
    Route::prefix('supervisor/evaluasi')->name('supervisor.EvaluasiIdp.')->group(function () {
        Route::get('/idp', [EvaluasiPascaIdpController::class, 'indexSpv'])->name('indexSpv');
        Route::get('/idp/create', [EvaluasiPascaIdpController::class, 'createSpv'])->name('createSpv');
        Route::post('/idp/store', [EvaluasiPascaIdpController::class, 'storeSpv'])->name('storeSpv');
    });
});
// MENTOR
Route::middleware(['auth', 'karyawan:4,3,2'])->group(function () {
    // MENTOR
    Route::get('/mentor/dashboard', [MentorDashboardController::class, 'index'])->name('mentor.dashboard-mentor');
    // IDP
    Route::prefix('mentor/behavior/idp')->name('mentor.IDP.')->group(function () {
        Route::get('/', [MentorDashboardController::class, 'indexMentor'])->name('indexMentor');
        Route::get('/detail/{id}', [MentorDashboardController::class, 'showMentor'])->name('mentor.idp.show');
        Route::put('/penilaian/idp/{id}', [MentorDashboardController::class, 'updatePenilaian'])->name('updatePenilaian');
        Route::get('/verifikasi/{id}', [MentorDashboardController::class, 'verifikasi'])->name('verfikasi');
        Route::put('/update/verifikasi/{id}', [MentorDashboardController::class, 'updateVerifikasi'])->name('updateVerifikasi');
        Route::get('/riwayat', [MentorDashboardController::class, 'indexRiwayatIdp'])->name('RiwayatIDP.indexRiwayatIdp');
        Route::get('/{id}/riwayat', [MentorDashboardController::class, 'showRiwayatIDP'])->name('RiwayatIDP.showRiwayatIdp');
        Route::get('/idp/cetak-filter', [MentorDashboardController::class, 'cetakFiltered'])->name('RiwayatIDP.cetakFiltered');
    });
    // PANDUAN IDP MENTOR
    Route::prefix('mentor/panduan/idp')->name('karyawan.Panduan.')->group(function () {
        Route::get('/', [PanduanController::class, 'autoShowPanduanMentor'])->name('autoShowPanduanMentor');
    });
     Route::prefix('mentor/bank/evaluasi')->name('mentor.EvaluasiIdp.')->group(function () {
        Route::get('/idp', [EvaluasiPascaIdpController::class, 'indexMentor'])->name('EvaluasiPascaIdp.indexMentor');
        Route::get('/idp/create', [EvaluasiPascaIdpController::class, 'createMentor'])->name('EvaluasiPascaIdp.createMentor');
        Route::post('/idp/store', [EvaluasiPascaIdpController::class, 'storeMentor'])->name('EvaluasiPascaIdp.storeMentor');
    });
});
// KARYAWAN
Route::middleware(['auth', 'karyawan:4,3,2'])->group(function () {
    // KARYAWAN
    Route::get('/karyawan/dashboard', [KaryawanDashboardController::class, 'index'])->name('karyawan.dashboard-karyawan');
    // IDP
    Route::prefix('karyawan/behavior/idp')->name('karyawan.IDP.')->group(function () {
        Route::get('/', [KaryawanDashboardController::class, 'indexKaryawan'])->name('indexKaryawan');
        Route::get('/{id}/detail', [KaryawanDashboardController::class, 'showKaryawan'])->name('showKaryawan');
        Route::post('/idp/implementasi/soft/{id_idpKom}', [KaryawanDashboardController::class, 'storeImplementasiSoft'])->name('storeImplementasiSoft');
        Route::post('/idp/implementasi/hard/{id_idpKom}', [KaryawanDashboardController::class, 'storeImplementasiHard'])->name('storeImplementasiHard');
        Route::put('/upload-ulang/{id}', [KaryawanDashboardController::class, 'uploadUlang'])->name('uploadUlang');
        Route::get('/bank-idp', [KaryawanDashboardController::class, 'bankIdp'])->name('bankIdp');
        Route::post('/apply-idp/{id}', [KaryawanDashboardController::class, 'applyBankIdp'])->name('applyBankIdp');
        Route::get('/{id}/detail/idp', [KaryawanDashboardController::class, 'detailKaryawan'])->name('detailKaryawan');
        Route::get('/riwayat', [KaryawanDashboardController::class, 'indexRiwayatIdp'])->name('RiwayatIDP.indexRiwayatIdp');
        Route::get('/{id}/riwayat', [KaryawanDashboardController::class, 'showRiwayatIDP'])->name('RiwayatIDP.showRiwayatIdp');
        Route::get('/idp/cetak-filter', [KaryawanDashboardController::class, 'cetakFiltered'])->name('cetakFiltered');
        Route::get('/create', [KaryawanDashboardController::class, 'create'])->name('create');
        Route::post('/store', [KaryawanDashboardController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [KaryawanDashboardController::class, 'editIdp'])->name('editIdp');
        Route::put('/{id}/update', [KaryawanDashboardController::class, 'updateIdp'])->name('updateIdp');
    });
    // PANDUAN IDP KARYWAN
    Route::prefix('karyawan/panduan/idp')->name('karyawan.Panduan.')->group(function () {
        Route::get('/panduan/karyawan', [PanduanController::class, 'autoShowPanduanKaryawan'])->name('autoShowPanduanKaryawan');
    });
    Route::prefix('karyawan/bank/evaluasi')->name('karyawan.EvaluasiIdp.')->group(function () {
        Route::get('/idp', [EvaluasiPascaIdpController::class, 'indexKaryawan'])->name('EvaluasiPascaIdp.indexKaryawan');
        Route::get('/idp/create', [EvaluasiPascaIdpController::class, 'create'])->name('EvaluasiPascaIdp.create');
        Route::post('/idp/store', [EvaluasiPascaIdpController::class, 'store'])->name('EvaluasiPascaIdp.store');
    });
});
// PROFILE
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// Route auth default dari Laravel Breeze/Fortify
require __DIR__ . '/auth.php';
