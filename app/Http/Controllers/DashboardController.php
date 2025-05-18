<?php

namespace App\Http\Controllers;

use App\Models\ScheduledTasks;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DashboardController extends Controller
{
    /**
     * Retrieves the authenticated user's accounts, including their balance details,
     * and returns a view displaying the dashboard with the accounts and balance information.
     *
     * @return View The rendered view of the dashboard with account data and balance details.
     *
     * @throws HttpException Thrown when the authenticated user cannot be found (HTTP 401).
     */
    public function index(): View
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        $accounts = $user->accounts()
            ->with(['balances' => function($query) {
                $query->balanceTypeForward()
                      ->lastInstance();
            }])
            ->sortOrder()
            ->get();

        $currentAccount = $accounts->first();
        $balance = $currentAccount?->balances->first();

        return view('pages.dashboard.index', compact('accounts', 'currentAccount', 'balance'));
    }

    /**
     * Displays the dashboard view with user accounts and the current account's balance data.
     *
     * @param string $id The ID of the current account to display.
     * @return View The rendered dashboard view with accounts, current account, and balance details.
     *
     * @throws HttpException If the authenticated user is not found.
     * @throws ModelNotFoundException If the specified account ID is not found.
     */
    public function show(string $id): View
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        $accounts = $user->accounts()->sortOrder()->get();
        $currentAccount = $user->accounts()
            ->with(['balances' => function ($query) {
                $query->balanceTypeForward()->lastInstance();
            }])
            ->findOrFail($id);

        $balance = $currentAccount->balances->first();

        return view('pages.dashboard.index', compact('accounts', 'currentAccount', 'balance'));
    }

    /**
     * Retrieves the authenticated user's transaction history, accounts, and returns a view displaying the data.
     *
     * @return View The rendered view with the user's transactions and accounts information.
     *
     * @throws HttpException Thrown when the authenticated user cannot be found (HTTP 403).
     */
    public function history(): View
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $transactions = $user->transactions;
        $balances = $user->balances;
        $accounts = $user->accounts()
            ->sortOrder()
            ->get();

        return view('pages.dashboard.history', compact('user', 'transactions', 'accounts', 'balances'));
    }

    /**
     * Clocks the user's schedule and returns a view displaying the clock data.
     *
     * @return View The rendered view of the clock page with user's schedule data.
     *
     * @throws HttpException Thrown when the authenticated user cannot be found (HTTP 403).
     */
    public function clock(): View
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $schedules = $user->schedule;

        return view('pages.dashboard.clock', compact('schedules'));
    }


    /**
     * Displays the user's configuration page with account details and settings.
     *
     * @return View The rendered view of the configuration page with user and account data.
     *
     * @throws HttpException Thrown when the authenticated user cannot be found (HTTP 403).
     */
    public function configuration(): View
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $apiAccounts = $user->accounts()->onlyApi()->sortOrder()->get();
        $showUpdateAccounts = $apiAccounts->isNotEmpty() && $apiAccounts->every(function ($account) {
            return !$account->transactionsDisabled
                && !$account->balanceDisabled
                && $account->bankDataSyncTransactionsCount <= ScheduledTasks::$MAX_TIMES
                && $account->bankDataSyncBalancesCount <= ScheduledTasks::$MAX_TIMES;
        });

        $accounts = $user->accounts()->sortOrder()->get();

        return view('pages.dashboard.configuration', compact('user', 'accounts', 'showUpdateAccounts'));
    }
}
