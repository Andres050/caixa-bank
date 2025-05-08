<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class BankController extends Controller
{
    public function index(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        // Fetch all banks from the database
        $accounts = auth()->user()->accounts()->sortOrder()->get();

        $currentAccount = $accounts->first();

        // Return the view with the banks data
        return view('pages.banks.index', compact('accounts', 'currentAccount'));
    }

    public function show($id): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $accounts = auth()->user()->accounts()->sortOrder()->get();

        // Fetch the bank details from the database
        $currentAccount = Account::where('id', $id)->first();

        // Return the view with the bank data
        return view('pages.banks.index', compact('accounts', 'currentAccount'));
    }

    public function history(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        // Fetch all banks from the database
        $user = auth()->user();

        $transactions = $user->transactions;

        // Return the view with the banks data
        return view('pages.banks.history', compact('user', 'transactions'));
    }

    public function clock(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        // Fetch all banks from the database
        $schedules = auth()->user()->schedule;

        // Return the view with the banks data
        return view('pages.banks.clock', compact('schedules'));
    }

    public function configuration(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        // Fetch all banks from the database
        $banks = Bank::all();

        // Return the view with the banks data
        return view('pages.banks.configuration', compact('banks'));
    }
}
