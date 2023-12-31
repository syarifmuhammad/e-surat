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
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\KeyPair;

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

// Route::get('/test-key-pair', function (Request $request) {
//     KeyPair::storeKeys(1, 'password');
// });

Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/register', [AuthenticationController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthenticationController::class, 'me']);
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
    });

    Route::group(['prefix' => 'reference-number-settings'], function () {
        Route::get('', [ReferenceNumberSettingController::class, 'get_reference_number']);
        Route::put('', [ReferenceNumberSettingController::class, 'update']);
        Route::get('/letter-types', [ReferenceNumberSettingController::class, 'get_letter_types']);
    });

    Route::get('analytics', [AnalyticsController::class, 'index']);
    
    Route::prefix('incoming-letters')->group(function () {
        Route::prefix('categories')->group(function () {
            Route::get('', [CategoryIncomingLetterController::class, 'index']);
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
        Route::get('reports', [ReportController::class, 'report_outcoming_letter']);
        Route::prefix('/templates')->group(function () {
            Route::get('/', [LetterTemplateController::class, 'index']);
            Route::get('/download_example', [LetterTemplateController::class, 'download_example']);
            Route::get('/{id}', [LetterTemplateController::class, 'show']);
            Route::get('/{id}/download', [LetterTemplateController::class, 'download']);
            Route::post('/', [LetterTemplateController::class, 'store']);
            Route::put('/{id}', [LetterTemplateController::class, 'update']);
        });
        Route::prefix('surat-keterangan-kerja')->group(function () {
            Route::get('', [SuratKeteranganKerjaController::class, 'index']);
            Route::post('', [SuratKeteranganKerjaController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratKeteranganKerjaController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeteranganKerjaController::class, 'download_pdf']);
            Route::get('/{id}', [SuratKeteranganKerjaController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratKeteranganKerjaController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratKeteranganKerjaController::class, 'update']);
            Route::delete('/{id}', [SuratKeteranganKerjaController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratKeteranganKerjaController::class, 'give_reference_number']);
            Route::put('/{id}/sign', [SuratKeteranganKerjaController::class, 'sign']);
        });
        Route::prefix('surat-keputusan-rotasi-kepegawaian')->group(function () {
            Route::get('', [SuratKeputusanRotasiKepegawaianController::class, 'index']);
            Route::post('', [SuratKeputusanRotasiKepegawaianController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratKeputusanRotasiKepegawaianController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeputusanRotasiKepegawaianController::class, 'download_pdf']);
            Route::get('/{id}', [SuratKeputusanRotasiKepegawaianController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratKeputusanRotasiKepegawaianController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratKeputusanRotasiKepegawaianController::class, 'update']);
            Route::delete('/{id}', [SuratKeputusanRotasiKepegawaianController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratKeputusanRotasiKepegawaianController::class, 'give_reference_number']);
            Route::put('/{id}/sign', [SuratKeputusanRotasiKepegawaianController::class, 'sign']);
        });

        Route::prefix('surat-keputusan-pemberhentian')->group(function () {
            Route::get('', [SuratKeputusanPemberhentianController::class, 'index']);
            Route::post('', [SuratKeputusanPemberhentianController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratKeputusanPemberhentianController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeputusanPemberhentianController::class, 'download_pdf']);
            Route::get('/{id}', [SuratKeputusanPemberhentianController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratKeputusanPemberhentianController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratKeputusanPemberhentianController::class, 'update']);
            Route::delete('/{id}', [SuratKeputusanPemberhentianController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratKeputusanPemberhentianController::class, 'give_reference_number']);
            Route::put('/{id}/sign', [SuratKeputusanPemberhentianController::class, 'sign']);
        });

        Route::prefix('surat-keputusan-pengangkatan')->group(function () {
            Route::get('', [SuratKeputusanPengangkatanController::class, 'index']);
            Route::post('', [SuratKeputusanPengangkatanController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratKeputusanPengangkatanController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeputusanPengangkatanController::class, 'download_pdf']);
            Route::get('/{id}', [SuratKeputusanPengangkatanController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratKeputusanPengangkatanController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratKeputusanPengangkatanController::class, 'update']);
            Route::delete('/{id}', [SuratKeputusanPengangkatanController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratKeputusanPengangkatanController::class, 'give_reference_number']);
            Route::put('/{id}/sign', [SuratKeputusanPengangkatanController::class, 'sign']);
        });

        Route::prefix('surat-keputusan-pemberhentian-dan-pengangkatan')->group(function () {
            Route::get('', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'index']);
            Route::post('', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'download_pdf']);
            Route::get('/{id}', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'update']);
            Route::delete('/{id}', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'give_reference_number']);
            Route::put('/{id}/sign', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'sign']);
        });

        Route::prefix('surat-perjanjian-kerja-magang')->group(function () {
            Route::get('', [SuratPerjanjianKerjaMagangController::class, 'index']);
            Route::post('', [SuratPerjanjianKerjaMagangController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratPerjanjianKerjaMagangController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratPerjanjianKerjaMagangController::class, 'download_pdf']);
            Route::get('/{id}', [SuratPerjanjianKerjaMagangController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratPerjanjianKerjaMagangController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratPerjanjianKerjaMagangController::class, 'update']);
            Route::delete('/{id}', [SuratPerjanjianKerjaMagangController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratPerjanjianKerjaMagangController::class, 'give_reference_number']);
            Route::put('/{id}/sign', [SuratPerjanjianKerjaMagangController::class, 'sign']);
        });

        Route::prefix('surat-perjanjian-kerja-dosen-luar-biasa')->group(function () {
            Route::get('', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'index']);
            Route::post('', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'store']);
            Route::get('/{id}/download_docx', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'download_docx']);
            Route::get('/{id}/download_pdf', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'download_pdf']);
            Route::get('/{id}', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'update']);
            Route::delete('/{id}', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'give_reference_number']);
            Route::post('/{id}/sign', [SuratPerjanjianKerjaDosenLuarBiasaController::class, 'sign']);
        });

        Route::prefix('surat-perjanjian-kerja-dosen-full-time')->group(function () {
            Route::get('', [SuratPerjanjianKerjaDosenFullTimeController::class, 'index']);
            Route::post('', [SuratPerjanjianKerjaDosenFullTimeController::class, 'store']);
            Route::get('/{id}/download/docx', [SuratPerjanjianKerjaDosenFullTimeController::class, 'download_docx']);
            Route::get('/{id}/download/pdf', [SuratPerjanjianKerjaDosenFullTimeController::class, 'download_pdf']);
            Route::get('/{id}', [SuratPerjanjianKerjaDosenFullTimeController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratPerjanjianKerjaDosenFullTimeController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratPerjanjianKerjaDosenFullTimeController::class, 'update']);
            Route::delete('/{id}', [SuratPerjanjianKerjaDosenFullTimeController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratPerjanjianKerjaDosenFullTimeController::class, 'give_reference_number']);
            Route::put('/{id}/sign', [SuratPerjanjianKerjaDosenFullTimeController::class, 'sign']);
        });
    });
});
