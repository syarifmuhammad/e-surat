<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LetterTemplateController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ReferenceNumberSettingController;
use App\Http\Controllers\SuratKeteranganKerjaController;
use App\Http\Controllers\SuratKeputusanRotasiKepegawaianController;
use App\Http\Controllers\SuratKeputusanPemberhentianController;
use App\Http\Controllers\SuratKeputusanPemberhentianDanPengangkatanController;
use App\Http\Controllers\SuratKeputusanPengangkatanController;
use App\Http\Controllers\SuratPerjanjianKerjaMagangController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthenticationController::class, 'me']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);
    Route::apiResource('positions', PositionController::class);
    Route::prefix('employees')->group(function () {
        Route::get('/{nip}/rekening', [EmployeeController::class, 'rekening']);
        Route::post('/{nip}/rekening', [EmployeeController::class, 'store_rekening']);
        Route::put('/{nip}/roles', [EmployeeController::class, 'update_roles']);
        Route::apiResource('', EmployeeController::class);
    });

    Route::group(['prefix' => 'reference-number-settings'], function () {
        Route::get('', [ReferenceNumberSettingController::class, 'get_reference_number']);
        Route::put('', [ReferenceNumberSettingController::class, 'update']);
        Route::get('/letter-types', [ReferenceNumberSettingController::class, 'get_letter_types']);
    });

    Route::prefix('outcoming-letters')->group(function () {
        Route::prefix('/templates')->group(function () {
            Route::get('/', [LetterTemplateController::class, 'index']);
            Route::get('/{id}/download', [LetterTemplateController::class, 'download']);
            Route::put('/{id}', [LetterTemplateController::class, 'update']);
        });
        Route::prefix('surat-keterangan-kerja')->group(function () {
            Route::get('', [SuratKeteranganKerjaController::class, 'index']);
            Route::post('', [SuratKeteranganKerjaController::class, 'store']);
            Route::get('/{id}/download', [SuratKeteranganKerjaController::class, 'download']);
            Route::get('/{id}', [SuratKeteranganKerjaController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratKeteranganKerjaController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratKeteranganKerjaController::class, 'update']);
            Route::delete('/{id}', [SuratKeteranganKerjaController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratKeteranganKerjaController::class, 'give_reference_number']);
            Route::post('/{id}/sign', [SuratKeteranganKerjaController::class, 'sign']);
        });
        Route::prefix('surat-keputusan-rotasi-kepegawaian')->group(function () {
            Route::get('', [SuratKeputusanRotasiKepegawaianController::class, 'index']);
            Route::post('', [SuratKeputusanRotasiKepegawaianController::class, 'store']);
            Route::get('/{id}/download', [SuratKeputusanRotasiKepegawaianController::class, 'download']);
            Route::get('/{id}', [SuratKeputusanRotasiKepegawaianController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratKeputusanRotasiKepegawaianController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratKeputusanRotasiKepegawaianController::class, 'update']);
            Route::delete('/{id}', [SuratKeputusanRotasiKepegawaianController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratKeputusanRotasiKepegawaianController::class, 'give_reference_number']);
            Route::post('/{id}/sign', [SuratKeputusanRotasiKepegawaianController::class, 'sign']);
        });

        Route::prefix('surat-keputusan-pemberhentian')->group(function () {
            Route::get('', [SuratKeputusanPemberhentianController::class, 'index']);
            Route::post('', [SuratKeputusanPemberhentianController::class, 'store']);
            Route::get('/{id}/download', [SuratKeputusanPemberhentianController::class, 'download']);
            Route::get('/{id}', [SuratKeputusanPemberhentianController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratKeputusanPemberhentianController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratKeputusanPemberhentianController::class, 'update']);
            Route::delete('/{id}', [SuratKeputusanPemberhentianController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratKeputusanPemberhentianController::class, 'give_reference_number']);
            Route::post('/{id}/sign', [SuratKeputusanPemberhentianController::class, 'sign']);
        });

        Route::prefix('surat-keputusan-pengangkatan')->group(function () {
            Route::get('', [SuratKeputusanPengangkatanController::class, 'index']);
            Route::post('', [SuratKeputusanPengangkatanController::class, 'store']);
            Route::get('/{id}/download', [SuratKeputusanPengangkatanController::class, 'download']);
            Route::get('/{id}', [SuratKeputusanPengangkatanController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratKeputusanPengangkatanController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratKeputusanPengangkatanController::class, 'update']);
            Route::delete('/{id}', [SuratKeputusanPengangkatanController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratKeputusanPengangkatanController::class, 'give_reference_number']);
            Route::post('/{id}/sign', [SuratKeputusanPengangkatanController::class, 'sign']);
        });

        Route::prefix('surat-keputusan-pemberhentian-dan-pengangkatan')->group(function () {
            Route::get('', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'index']);
            Route::post('', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'store']);
            Route::get('/{id}/download', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'download']);
            Route::get('/{id}', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'update']);
            Route::delete('/{id}', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'give_reference_number']);
            Route::post('/{id}/sign', [SuratKeputusanPemberhentianDanPengangkatanController::class, 'sign']);
        });

        Route::prefix('surat-perjanjian-kerja-magang')->group(function () {
            Route::get('', [SuratPerjanjianKerjaMagangController::class, 'index']);
            Route::post('', [SuratPerjanjianKerjaMagangController::class, 'store']);
            Route::get('/{id}/download', [SuratPerjanjianKerjaMagangController::class, 'download']);
            Route::get('/{id}', [SuratPerjanjianKerjaMagangController::class, 'show']);
            Route::put('/{id}/upload-signed-file', [SuratPerjanjianKerjaMagangController::class, 'upload_signed_file']);
            Route::put('/{id}', [SuratPerjanjianKerjaMagangController::class, 'update']);
            Route::delete('/{id}', [SuratPerjanjianKerjaMagangController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratPerjanjianKerjaMagangController::class, 'give_reference_number']);
            Route::post('/{id}/sign', [SuratPerjanjianKerjaMagangController::class, 'sign']);
        });
    });
});
