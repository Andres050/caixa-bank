<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        // Fetch all banks from the database
        $accounts = auth()->user()->accounts;

        $currentAccount = $accounts->first();

        // Return the view with the banks data
        return view('pages.banks.index', compact('accounts', 'currentAccount'));
    }

    public function show($id)
    {
        $accounts = auth()->user()->accounts;

        // Fetch the bank details from the database
        $currentAccount = Account::where('id', $id)->first();

        // Return the view with the bank data
        return view('pages.banks.index', compact('accounts', 'currentAccount'));
    }

    public function history()
    {
        // Fetch all banks from the database
        $banks = Bank::all();

        // Return the view with the banks data
        return view('pages.banks.history', compact('banks'));
    }

    public function configuration()
    {
        // Fetch all banks from the database
        $banks = Bank::all();

        // Return the view with the banks data
        return view('pages.banks.configuration', compact('banks'));
    }
}
