<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NordigenController;

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
    return redirect()->route('bank.index');
});

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'es'])) {
        session(['locale' => $locale]);
        \Illuminate\Support\Facades\App::setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/bank', [ProfileController::class, 'bankUpdate'])->name('profile.bank.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bank routes
    Route::get('/accounts', [\App\Http\Controllers\BankController::class, 'index'])->name('bank.index');
    Route::get('/accounts/{id}', [\App\Http\Controllers\BankController::class, 'show'])->name('bank.show');
    Route::get('/history', [\App\Http\Controllers\BankController::class, 'history'])->name('bank.history');
    Route::get('/configuration', [\App\Http\Controllers\BankController::class, 'configuration'])->name('bank.configuration');

    // Nordigen routes
    Route::get('/connect', [NordigenController::class, 'authenticate'])->name('nordigen.auth');
    Route::get('/create-requisition', [NordigenController::class, 'createRequisition'])->name('nordigen.create-requisition');
    Route::get('/callback', [NordigenController::class, 'callback'])->name('nordigen.callback');

    Route::get('/transactions/{accountId}', [NordigenController::class, 'transactions'])->name('nordigen.transactions');
    Route::get('/balances/{accountId}', [NordigenController::class, 'balances'])->name('nordigen.balances');

    Route::post('/institutions', [NordigenController::class, 'insertInstitutions'])->name('nordigen.institutions');
});


require __DIR__.'/auth.php';
