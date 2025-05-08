<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NordigenController;
use \App\Http\Controllers\BankController;
use \App\Http\Controllers\LangController;
use \App\Http\Controllers\MonthController;

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
    // This is the default route for the application
    return redirect()->route('bank.index');
});

Route::get('/lang/{locale}',[LangController::class, 'index'])->name('lang.switch');
Route::get('/month/{month}',[MonthController::class, 'index'])->name('month.index');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/accounts/order', [ProfileController::class, 'reorder'])->name('profile.accounts.reorder');
    Route::patch('/profile/schedule', [ProfileController::class, 'schedule'])->name('profile.accounts.schedule');
    Route::post('/profile/schedule/check', [ProfileController::class, 'scheduleTasks'])->name('profile.accounts.scheduleTasks');
    Route::patch('/profile/bank', [ProfileController::class, 'bankUpdate'])->name('profile.bank.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bank routes
    Route::get('/accounts', [BankController::class, 'index'])->name('bank.index');
    Route::get('/accounts/{id}', [BankController::class, 'show'])->name('bank.show');
    Route::get('/history', [BankController::class, 'history'])->name('bank.history');
    Route::get('/clock', [BankController::class, 'clock'])->name('bank.clock');
    Route::get('/configuration', [BankController::class, 'configuration'])->name('bank.configuration');

    // Nordigen routes
    Route::get('/connect', [NordigenController::class, 'authenticate'])->name('nordigen.auth');
    Route::get('/create-requisition', [NordigenController::class, 'createRequisition'])->name('nordigen.create-requisition');
    Route::get('/callback', [NordigenController::class, 'callback'])->name('nordigen.callback');

    Route::get('/transactions/{accountId}', [NordigenController::class, 'transactions'])->name('nordigen.transactions');
    Route::get('/balances/{accountId}', [NordigenController::class, 'balances'])->name('nordigen.balances');

    Route::get('/update/{accountId}', [NordigenController::class, 'update'])->name('nordigen.all');
    Route::get('/update', [NordigenController::class, 'updateAll'])->name('nordigen.all_accounts');

    Route::post('/institutions', [NordigenController::class, 'insertInstitutions'])->name('nordigen.institutions');
});


require __DIR__.'/auth.php';
