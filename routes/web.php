<?php

use App\Http\Controllers\LeaveCreditExportController;
use App\Http\Controllers\ServiceCreditExportController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', '/hr');
Route::redirect('/login', '/hr/login')->name('login');
Route::redirect('/personnel/login', '/hr/login');

Route::middleware('auth')->group(function (): void {
    Route::get(
        '/hr/service-credits/{employee}/export/{format}',
        ServiceCreditExportController::class,
    )
        ->whereIn('format', ['pdf', 'excel', 'print'])
        ->name('service-credits.export');

    Route::get(
        '/hr/leave-credits/{employee}/{report}/export/{format}',
        LeaveCreditExportController::class,
    )
        ->whereIn('report', ['leave-history', 'credit-history'])
        ->whereIn('format', ['pdf', 'excel', 'print'])
        ->name('leave-credits.export');
});
