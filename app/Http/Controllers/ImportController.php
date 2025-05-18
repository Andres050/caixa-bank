<?php

namespace App\Http\Controllers;

use App\Imports\AccountImport;
use App\Imports\BalanceImport;
use App\Imports\TransactionImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    /**
     * Muestra la vista de importación del perfil del usuario.
     *
     * @return View
     */
    public function show(): View
    {
        if (!auth()->check()) {
            abort(401);
        }

        return view('pages.profile.import', [
            'user' => auth()->user(),
        ]);
    }

    public function accounts(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv_accounts' => ['nullable', 'mimes:csv,txt'],
            'file_xlsx_accounts' => ['nullable', 'mimes:xlsx,xls'],
        ]);

        if (!$request->file('file_csv_accounts') && !$request->file('file_xlsx_accounts')) {
            return Redirect::route('profile.import.edit')->withErrors(['file_csv_accounts' => 'Please upload a CSV file.', 'file_xlsx_accounts' => 'Please upload an XLSX file.']);
        }

        $file_csv = $request->file('file_csv_accounts');
        $file_xlsx = $request->file('file_xlsx_accounts');

        if ($file_csv) {
            try {
                Excel::import(new AccountImport(), $file_csv, null, \Maatwebsite\Excel\Excel::CSV);
            } catch (\Exception $e) {
                return Redirect::route('profile.import.edit')->withErrors(['file_csv_accounts' => 'Error reading CSV file: ' . $e->getMessage()]);
            }
        }

        if ($file_xlsx) {
            try {
                Excel::import(new AccountImport(), $file_xlsx, null, \Maatwebsite\Excel\Excel::XLSX);
            } catch (\Exception $e) {
                return Redirect::route('profile.import.edit')->withErrors(['file_xlsx_accounts' => 'Error reading XLSX file: ' . $e->getMessage()]);
            }
        }

        return Redirect::route('profile.import.edit')->with('success', 'Cuentas importadas correctamente');
    }

    public function transaction(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv_transactions' => ['nullable', 'mimes:csv,txt'],
            'file_xlsx_transactions' => ['nullable', 'mimes:xlsx,xls'],
        ]);

        if (!$request->file('file_csv_transactions') && !$request->file('file_xlsx_transactions')) {
            return Redirect::route('profile.import.edit')->withErrors(['file_csv_transactions' => 'Please upload a CSV file.', 'file_xlsx_transactions' => 'Please upload an XLSX file.']);
        }

        $file_csv = $request->file('file_csv_transactions');
        $file_xlsx = $request->file('file_xlsx_transactions');

        if ($file_csv) {
            try {
                Excel::import(new TransactionImport(), $file_csv, null, \Maatwebsite\Excel\Excel::CSV);
            } catch (\Exception $e) {
                return Redirect::route('profile.import.edit')->withErrors(['file_csv_transactions' => 'Error reading CSV file: ' . $e->getMessage()]);
            }
        }

        if ($file_xlsx) {
            try {
                Excel::import(new TransactionImport(), $file_xlsx, null, \Maatwebsite\Excel\Excel::XLSX);
            } catch (\Exception $e) {
                return Redirect::route('profile.import.edit')->withErrors(['file_xlsx_transactions' => 'Error reading XLSX file: ' . $e->getMessage()]);
            }
        }

        return Redirect::route('profile.import.edit')->with('success', 'account-imported');
    }

    public function balances(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv_balances' => ['nullable', 'mimes:csv,txt'],
            'file_xlsx_balances' => ['nullable', 'mimes:xlsx,xls'],
        ]);

        if (!$request->file('file_csv_balances') && !$request->file('file_xlsx_balances')) {
            return Redirect::route('profile.import.edit')->withErrors(['file_csv_balances' => 'Please upload a CSV file.', 'file_xlsx_balances' => 'Please upload an XLSX file.']);
        }

        $file_csv = $request->file('file_csv_balances');
        $file_xlsx = $request->file('file_xlsx_balances');

        if ($file_csv) {
            try {
                Excel::import(new BalanceImport(), $file_csv, null, \Maatwebsite\Excel\Excel::CSV);
            } catch (\Exception $e) {
                return Redirect::route('profile.import.edit')->withErrors(['file_csv_balances' => 'Error reading CSV file: ' . $e->getMessage()]);
            }
        }

        if ($file_xlsx) {
            try {
                Excel::import(new BalanceImport(), $file_xlsx, null, \Maatwebsite\Excel\Excel::XLSX);
            } catch (\Exception $e) {
                return Redirect::route('profile.import.edit')->withErrors(['file_xlsx_balances' => 'Error reading XLSX file: ' . $e->getMessage()]);
            }
        }

        return Redirect::route('profile.import.edit')->with('success', 'account-imported');
    }
}
