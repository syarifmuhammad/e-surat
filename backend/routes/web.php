<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tes', function() {
    // $payload = [
    //     'letter' => 2,
    //     'employee' => 5,
    //     'letter_type' => 'surat_perjanjian_kerja_dosen_full_time',
    //     'random_key' => Str::random(8),
    // ];
    // $tes = Crypt::encrypt($payload);
    $tes2 = Crypt::decrypt("eyJpdiI6IkMvQjJhSitvUy96OWFPUTk1SFJycGc9PSIsInZhbHVlIjoiNkxtZ3krQTU1aXBjdHdIQWhPdVZrUmJISyt0MFUwRFQxUnpFZ3JPYXZRZWI2bldYYm5nVDVYY1ZBS2pJTG01SjZNYlRjVmxBZGR0VkcrTXhsZE5xcloxa0xxL0RjWFM4cjM2TG02NW1RVllSQnRJeVJkK0FZcVlkckZZbXV3UXJmbGMzbE1XTFBTa0NmczlVZUhhckJsSHBtWkUwenVrK2sxTGtKNTF0RS8rZk5tekJ6ZW52VHhGdE45Y3F6RWd0IiwibWFjIjoiY2NkMjI3YjY3MGM0YjFkMmIzMDM2OWExYjVlNWU0ODhiNWQwZGJkZGY1MDI2MDI1NGQxYWFlZTc5MmFjNGI2ZSIsInRhZyI6IiJ9");
    dd($tes2);
});


Route::get('/outcoming-letters/report', [ReportController::class, 'helper_to_check_without_download']);
