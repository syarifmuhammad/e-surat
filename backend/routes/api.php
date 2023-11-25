<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LetterTemplateController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ReferenceNumberSettingController;
use App\Http\Controllers\SuratKeteranganKerjaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

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

Route::get('/unique', function(Request $request) {
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
        Route::apiResource('', EmployeeController::class);
        Route::put('/{nip}/roles', [EmployeeController::class, 'update_roles']);
    });

    Route::group(['prefix' => 'reference-number-settings'], function () {
        Route::get('', [ReferenceNumberSettingController::class, 'get_reference_number']);
        Route::put('', [ReferenceNumberSettingController::class, 'update']);
        Route::get('/letter-types', [ReferenceNumberSettingController::class, 'get_letter_types']);
    });

    Route::prefix('outcoming-letters')->group(function () {
        Route::get('/letter-templates', [LetterTemplateController::class, 'index']);
        Route::prefix('surat-keterangan-kerja')->group(function () {
            Route::get('', [SuratKeteranganKerjaController::class, 'index']);
            Route::post('', [SuratKeteranganKerjaController::class, 'store']);
            Route::get('/{id}/download', [SuratKeteranganKerjaController::class, 'download']);
            Route::get('/{id}', [SuratKeteranganKerjaController::class, 'show']);
            Route::put('/{id}', [SuratKeteranganKerjaController::class, 'update']);
            Route::delete('/{id}', [SuratKeteranganKerjaController::class, 'destroy']);
            Route::put('/{id}/reference-number', [SuratKeteranganKerjaController::class, 'give_reference_number']);
            Route::post('/{id}/sign', [SuratKeteranganKerjaController::class, 'sign']);
        });
    });
});
