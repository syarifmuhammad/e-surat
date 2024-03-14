<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CategoryIncomingLetterController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\IncomingLetterController;
use App\Http\Controllers\LetterTemplateController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ReferenceNumberSettingController;
use App\Http\Controllers\SuratKeteranganKerjaController;
use App\Http\Controllers\SuratKeputusanRotasiKepegawaianController;
use App\Http\Controllers\SuratKeputusanPemberhentianController;
use App\Http\Controllers\SuratKeputusanPemberhentianDanPengangkatanController;
use App\Http\Controllers\SuratKeputusanPengangkatanController;
use App\Http\Controllers\SuratPerjanjianKerjaDosenFullTimeController;
use App\Http\Controllers\SuratPerjanjianKerjaMagangController;
use App\Http\Controllers\SuratPerjanjianKerjaDosenLuarBiasaController;
use App\Http\Controllers\SignatureConfirmationController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/unique', function (Request $request) {
    // $templateProcessor = new TemplateProcessor(storage_path("app/letter_templates/3aed34a1-8e66-4d59-990e-a4b13a918e00.docx"));
    // $tugas = [
    //     ['tugas' => "something task 1"],
    //     ['tugas' => "something task 2"],
    // ];
    // $templateProcessor->cloneBlock('block_tugas', 0, true, false, $tugas);
    // $templateProcessor->saveAs(storage_path("app/tmp/surat_perjanjian_kerja_magang/3aed34a1-8e66-4d59-990e-a4b13a918e00.docx"));
    // return;
    return response()->json([
        'unique' => Str::uuid()
    ]);
});

Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/register', [AuthenticationController::class, 'store']);
Route::get('/verify/{token}', [SignatureConfirmationController::class, 'verify']);
Route::get('/file/{token}', [SignatureConfirmationController::class, 'file']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthenticationController::class, 'me']);
    Route::put('/me', [AuthenticationController::class, 'update_me']);
    Route::put('/change-password', [AuthenticationController::class, 'change_password']);
    Route::get('/signature', [EmployeeController::class, 'signature']);
    Route::put('/signature', [EmployeeController::class, 'update_signature']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);
    Route::apiResource('positions', PositionController::class);
    Route::apiResource('prodi', ProdiController::class);
    Route::apiResource('unit', UnitController::class);
    Route::prefix('employees')->group(function () {
        Route::get('', [EmployeeController::class, 'index']);
        Route::post('', [EmployeeController::class, 'store']);
        Route::get('/{id}', [EmployeeController::class, 'show']);
        Route::get('/{id}/rekening', [EmployeeController::class, 'rekening']);
        Route::post('/{id}/rekening', [EmployeeController::class, 'store_rekening']);
        Route::put('/{id}/roles', [EmployeeController::class, 'update_roles']);
        Route::put('/{id}/signature', [EmployeeController::class, 'update_signature_by_admin']);
        Route::put('/{id}', [EmployeeController::class, 'update']);
        Route::delete('/{id}', [EmployeeController::class, 'destroy']);
    });

    Route::group(['prefix' => 'reference-number-settings'], function () {
        Route::get('', [ReferenceNumberSettingController::class, 'get_reference_number']);
        Route::put('', [ReferenceNumberSettingController::class, 'update']);
        Route::get('/letter-types', [ReferenceNumberSettingController::class, 'get_letter_types']);
    });

    Route::get('analytics', [AnalyticsController::class, 'index']);

    Route::prefix('incoming-letters')->group(function () {

        Route::prefix('surat-keterangan-kerja')->group(function () {
            Route::get('', [SuratKeteranganKerjaController::class, 'incoming']);
            Route::get('/{id}/download/docx', [SuratKeteranganKerjaController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeteranganKerjaController::class, 'download_pdf']);
            Route::put('/{id}/upload-signed-file', [SuratKeteranganKerjaController::class, 'upload_signed_file']);
            Route::put('/{id}/reference-number', [SuratKeteranganKerjaController::class, 'give_reference_number']);
            Route::put('/{id}/approve', [SuratKeteranganKerjaController::class, 'approve']);
            Route::put('/{id}/sign', [SuratKeteranganKerjaController::class, 'sign']);
        });
        Route::prefix('surat-keputusan-rotasi-kepegawaian')->group(function () {
            Route::get('', [SuratKeputusanRotasiKepegawaianController::class, 'incoming']);
            Route::get('/{id}/download/docx', [SuratKeputusanRotasiKepegawaianController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeputusanRotasiKepegawaianController::class, 'download_pdf']);
            Route::put('/{id}/upload-signed-file', [SuratKeputusanRotasiKepegawaianController::class, 'upload_signed_file']);
            Route::put('/{id}/reference-number', [SuratKeputusanRotasiKepegawaianController::class, 'give_reference_number']);
            Route::put('/{id}/approve', [SuratKeputusanRotasiKepegawaianController::class, 'approve']);
            Route::put('/{id}/sign', [SuratKeputusanRotasiKepegawaianController::class, 'sign']);
        });

        Route::prefix('surat-keputusan-pemberhentian')->group(function () {
            Route::get('', [SuratKeputusanPemberhentianController::class, 'incoming']);
            Route::get('/{id}/download/docx', [SuratKeputusanPemberhentianController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeputusanPemberhentianController::class, 'download_pdf']);
            Route::put('/{id}/upload-signed-file', [SuratKeputusanPemberhentianController::class, 'upload_signed_file']);
            Route::put('/{id}/reference-number', [SuratKeputusanPemberhentianController::class, 'give_reference_number']);
            Route::put('/{id}/approve', [SuratKeputusanPemberhentianController::class, 'approve']);
            Route::put('/{id}/sign', [SuratKeputusanPemberhentianController::class, 'sign']);
        });

        Route::prefix('surat-keputusan-pengangkatan')->group(function () {
            Route::get('', [SuratKeputusanPengangkatanController::class, 'incoming']);
            Route::get('/{id}/download/docx', [SuratKeputusanPengangkatanController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeputusanPengangkatanController::class, 'download_pdf']);
            Route::put('/{id}/upload-signed-file', [SuratKeputusanPengangkatanController::class, 'upload_signed_file']);
            Route::put('/{id}/reference-number', [SuratKeputusanPengangkatanController::class, 'give_reference_number']);
            Route::put('/{id}/approve', [SuratKeputusanPengangkatanController::class, 'approve']);
            Route::put('/{id}/sign', [SuratKeputusanPengangkatanController::class, 'sign']);
        });

        Route::prefix('surat-keputusan-pemberhentian-dan-pengangkatan')->group(function () {
            Route::get('', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'incoming']);
            Route::get('/{id}/download/docx', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'download_pdf']);
            Route::put('/{id}/upload-signed-file', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'upload_signed_file']);
            Route::put('/{id}/reference-number', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'give_reference_number']);
            Route::put('/{id}/approve', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'approve']);
            Route::put('/{id}/sign', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'sign']);
        });

        Route::prefix('surat-perjanjian-kerja-magang')->group(function () {
            Route::get('', [SuratPerjanjianKerjaMagangController::class, 'incoming']);
            Route::get('/{id}/download/docx', [SuratPerjanjianKerjaMagangController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratPerjanjianKerjaMagangController::class, 'download_pdf']);
            Route::put('/{id}/upload-signed-file', [SuratPerjanjianKerjaMagangController::class, 'upload_signed_file']);
            Route::put('/{id}/reference-number', [SuratPerjanjianKerjaMagangController::class, 'give_reference_number']);
            Route::put('/{id}/approve', [SuratPerjanjianKerjaMagangController::class, 'approve']);
            Route::put('/{id}/sign', [SuratPerjanjianKerjaMagangController::class, 'sign']);
        });

        Route::prefix('surat-perjanjian-kerja-dosen-luar-biasa')->group(function () {
            Route::get('', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'incoming']);
            Route::get('/{id}/download/docx', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'download_pdf']);
            Route::put('/{id}/upload-signed-file', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'upload_signed_file']);
            Route::put('/{id}/reference-number', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'give_reference_number']);
            Route::put('/{id}/approve', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'approve']);
            Route::put('/{id}/sign', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'sign']);
        });

        Route::prefix('surat-perjanjian-kerja-dosen-full-time')->group(function () {
            Route::get('', [SuratPerjanjianKerjaDosenFullTimeController::class, 'incoming']);
            Route::get('/{id}/download/docx', [SuratPerjanjianKerjaDosenFullTimeController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratPerjanjianKerjaDosenFullTimeController::class, 'download_pdf']);
            Route::put('/{id}/upload-signed-file', [SuratPerjanjianKerjaDosenFullTimeController::class, 'upload_signed_file']);
            Route::put('/{id}/reference-number', [SuratPerjanjianKerjaDosenFullTimeController::class, 'give_reference_number']);
            Route::put('/{id}/approve', [SuratPerjanjianKerjaDosenFullTimeController::class, 'approve']);
            Route::put('/{id}/sign', [SuratPerjanjianKerjaDosenFullTimeController::class, 'sign']);
        });

        Route::prefix('categories')->group(function () {
            Route::get('', [CategoryIncomingLetterController::class, 'incoming']);
            Route::post('', [CategoryIncomingLetterController::class, 'store']);
            Route::put('{id}', [CategoryIncomingLetterController::class, 'update']);
            Route::delete('{id}', [CategoryIncomingLetterController::class, 'destroy']);
        });

        Route::get('', [IncomingLetterController::class, 'index']);
        Route::get('{id}/file', [IncomingLetterController::class, 'file']);
        Route::post('', [IncomingLetterController::class, 'store']);
        Route::put('{id}', [IncomingLetterController::class, 'update']);
        Route::delete('{id}', [IncomingLetterController::class, 'destroy']);
    });

    Route::prefix('outcoming-letters')->group(function () {
        // Route::get('reports', [ReportController::class, 'report_outcoming_letter']);
        Route::prefix('/templates')->group(function () {
            Route::get('/', [LetterTemplateController::class, 'index']);
            Route::get('/download_example', [LetterTemplateController::class, 'download_example']);
            Route::get('/{id}', [LetterTemplateController::class, 'show']);
            Route::get('/{id}/download', [LetterTemplateController::class, 'download']);
            Route::post('/', [LetterTemplateController::class, 'store']);
            Route::put('/{id}/set-active-or-not', [LetterTemplateController::class, 'set_active_or_not']);
            Route::put('/{id}', [LetterTemplateController::class, 'update']);
        });
        Route::prefix('surat-keterangan-kerja')->group(function () {
            Route::get('', [SuratKeteranganKerjaController::class, 'index']);
            Route::get('/graph-in-months/{year}', [SuratKeteranganKerjaController::class, 'graph_in_months']);
            Route::post('', [SuratKeteranganKerjaController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratKeteranganKerjaController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeteranganKerjaController::class, 'download_pdf']);
            Route::get('/{id}', [SuratKeteranganKerjaController::class, 'show']);
            Route::put('/{id}', [SuratKeteranganKerjaController::class, 'update']);
            Route::delete('/{id}', [SuratKeteranganKerjaController::class, 'destroy']);
        });
        Route::prefix('surat-keputusan-rotasi-kepegawaian')->group(function () {
            Route::get('', [SuratKeputusanRotasiKepegawaianController::class, 'index']);
            Route::get('/graph-in-months/{year}', [SuratKeputusanRotasiKepegawaianController::class, 'graph_in_months']);
            Route::post('', [SuratKeputusanRotasiKepegawaianController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratKeputusanRotasiKepegawaianController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeputusanRotasiKepegawaianController::class, 'download_pdf']);
            Route::get('/{id}', [SuratKeputusanRotasiKepegawaianController::class, 'show']);
            Route::put('/{id}', [SuratKeputusanRotasiKepegawaianController::class, 'update']);
            Route::delete('/{id}', [SuratKeputusanRotasiKepegawaianController::class, 'destroy']);
        });

        Route::prefix('surat-keputusan-pemberhentian')->group(function () {
            Route::get('', [SuratKeputusanPemberhentianController::class, 'index']);
            Route::get('/graph-in-months/{year}', [SuratKeputusanPemberhentianController::class, 'graph_in_months']);
            Route::post('', [SuratKeputusanPemberhentianController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratKeputusanPemberhentianController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeputusanPemberhentianController::class, 'download_pdf']);
            Route::get('/{id}', [SuratKeputusanPemberhentianController::class, 'show']);
            Route::put('/{id}', [SuratKeputusanPemberhentianController::class, 'update']);
            Route::delete('/{id}', [SuratKeputusanPemberhentianController::class, 'destroy']);
        });

        Route::prefix('surat-keputusan-pengangkatan')->group(function () {
            Route::get('', [SuratKeputusanPengangkatanController::class, 'index']);
            Route::get('/graph-in-months/{year}', [SuratKeputusanPengangkatanController::class, 'graph_in_months']);
            Route::post('', [SuratKeputusanPengangkatanController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratKeputusanPengangkatanController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeputusanPengangkatanController::class, 'download_pdf']);
            Route::get('/{id}', [SuratKeputusanPengangkatanController::class, 'show']);
            Route::put('/{id}', [SuratKeputusanPengangkatanController::class, 'update']);
            Route::delete('/{id}', [SuratKeputusanPengangkatanController::class, 'destroy']);
        });

        Route::prefix('surat-keputusan-pemberhentian-dan-pengangkatan')->group(function () {
            Route::get('', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'index']);
            Route::get('/graph-in-months/{year}', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'graph_in_months']);
            Route::post('', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'download_pdf']);
            Route::get('/{id}', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'show']);
            Route::put('/{id}', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'update']);
            Route::delete('/{id}', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'destroy']);
        });

        Route::prefix('surat-perjanjian-kerja-magang')->group(function () {
            Route::get('', [SuratPerjanjianKerjaMagangController::class, 'index']);
            Route::get('/graph-in-months/{year}', [SuratPerjanjianKerjaMagangController::class, 'graph_in_months']);
            Route::post('', [SuratPerjanjianKerjaMagangController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratPerjanjianKerjaMagangController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratPerjanjianKerjaMagangController::class, 'download_pdf']);
            Route::get('/{id}', [SuratPerjanjianKerjaMagangController::class, 'show']);
            Route::put('/{id}', [SuratPerjanjianKerjaMagangController::class, 'update']);
            Route::delete('/{id}', [SuratPerjanjianKerjaMagangController::class, 'destroy']);
        });

        Route::prefix('surat-perjanjian-kerja-dosen-luar-biasa')->group(function () {
            Route::get('', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'index']);
            Route::get('/graph-in-months/{year}', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'graph_in_months']);
            Route::post('', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'download_pdf']);
            Route::get('/{id}', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'show']);
            Route::put('/{id}', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'update']);
            Route::delete('/{id}', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'destroy']);
        });

        Route::prefix('surat-perjanjian-kerja-dosen-full-time')->group(function () {
            Route::get('', [SuratPerjanjianKerjaDosenFullTimeController::class, 'index']);
            Route::get('/graph-in-months/{year}', [SuratPerjanjianKerjaDosenFullTimeController::class, 'graph_in_months']);
            Route::post('', [SuratPerjanjianKerjaDosenFullTimeController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratPerjanjianKerjaDosenFullTimeController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratPerjanjianKerjaDosenFullTimeController::class, 'download_pdf']);
            Route::get('/{id}', [SuratPerjanjianKerjaDosenFullTimeController::class, 'show']);
            Route::put('/{id}', [SuratPerjanjianKerjaDosenFullTimeController::class, 'update']);
            Route::delete('/{id}', [SuratPerjanjianKerjaDosenFullTimeController::class, 'destroy']);
        });
    });
});
