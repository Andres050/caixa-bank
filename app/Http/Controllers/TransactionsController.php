<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class TransactionsController extends Controller
{
    /**
     * Display the edit form for a specific manual account and its transactions.
     *
     * This method fetches the account associated with the provided account ID,
     * ensuring the account belongs to the authenticated user and is of manual type.
     * If the account is not manual, redirects back to the accounts edit route
     * with an appropriate error message. If the validation passes, retrieves
     * associated transactions for the account and returns the edit view.
     *
     * @param string $accountId The identifier of the account to be edited.
     * @return View|RedirectResponse The view for editing transactions of the account
     *                               or a redirect response with an error message.
     */
    public function edit(string $accountId): View|RedirectResponse
    {
        $account = Account::where('user_id', Auth::id())
            ->findOrFail($accountId);

        $transactions = $account->transactions()
            ->orderDate()
            ->get();

        return view('pages.profile.transactions.edit', [
            'user' => auth()->user(),
            'account' => $account,
            'transactions' => $transactions
        ]);
    }

    /**
     * Create a new transaction for a manual account.
     *
     * This method validates the incoming request data to ensure all required
     * fields are properly provided and comply with specific rules. If valid,
     * it fetches the associated manual account and creates a new transaction
     * entry in the database. On success, redirects to the transaction edit page
     * with a success message. If an exception occurs, redirects back with an
     * error message.
     *
     * @param Request $request The incoming HTTP request containing transaction details.
     * @return RedirectResponse A redirect to the transaction edit route, carrying
     *                          either a success or an error message.
     */
    public function create(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'account_id' => 'required|exists:accounts,id',
                'bookingDate' => 'required|date',
                'transactionAmount_amount' => 'required|numeric',
                'valueDate' => 'nullable|date',
                'transactionAmount_currency' => 'nullable|string|size:3',
                'entryReference' => 'nullable|string|max:255',
                'checkId' => 'nullable|string|max:255',
                'remittanceInformationUnstructured' => 'nullable|string|max:1000',
                'bankTransactionCode' => 'nullable|string|max:255',
                'proprietaryBankTransactionCode' => 'nullable|string|max:255',
                'internalTransactionId' => 'nullable|string|max:255',
                'debtorName' => 'nullable|string|max:255',
                'debtorAccount' => 'nullable|string|max:255',
            ]);

            $account = Account::onlyManual()->findOrFail($validated['account_id']);

            Transaction::create([
                'id' => Str::uuid(),
                'entryReference' => $validated['entryReference'] ?? null,
                'checkId' => $validated['checkId'] ?? null,
                'bookingDate' => $validated['bookingDate'],
                'valueDate' => $validated['valueDate'] ?? $validated['bookingDate'],
                'transactionAmount_amount' => $validated['transactionAmount_amount'],
                'transactionAmount_currency' => $validated['transactionAmount_currency'] ?? 'EUR',
                'remittanceInformationUnstructured' => $validated['remittanceInformationUnstructured'] ?? null,
                'bankTransactionCode' => $validated['bankTransactionCode'] ?? null,
                'proprietaryBankTransactionCode' => $validated['proprietaryBankTransactionCode'] ?? null,
                'internalTransactionId' => $validated['internalTransactionId'] ?? null,
                'debtorName' => $validated['debtorName'] ?? null,
                'debtorAccount' => $validated['debtorAccount'] ?? null,
                'account_id' => $account->code,
                'category_id' => Transaction::getCategoryId($validated['remittanceInformationUnstructured'] ?? null),
            ]);

            return Redirect::route('profile.transaction.edit', ['id' => $account->code])
                ->with('success', __('status.transactionscontroller.transaction-created'));

        } catch (Exception $e) {
            Log::error('Error creating transaction', [
                'error' => $e->getMessage(),
                'account_id' => $request->input('account_id'),
                'request_data' => $request->all(),
            ]);
            return Redirect::route('profile.accounts.edit')
                ->with('error', __('status.transactionscontroller.transaction-creation-failed'));
        }
    }

    /**
     * Update a specific transaction associated with a manual account.
     *
     * Validates the incoming request data, ensuring the provided transaction and account IDs exist,
     * and that the required fields meet their respective constraints. Once validated, attempts to
     * update the transaction with the provided input data while ensuring the account is of manual type.
     * If the operation succeeds, redirects to the transaction edit page with a success message.
     * In the event of an error, logs the exception and rolls back any changes, redirecting with an error message.
     *
     * @param Request $request The incoming HTTP request containing transaction and account details.
     * @return RedirectResponse A redirection to the transaction edit page with success or error feedback.
     * @throws Throwable
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'account_id' => 'required|exists:accounts,id',
            'bookingDate' => 'required|date',
            'transactionAmount_amount' => 'required|numeric',
            'transactionAmount_currency' => 'nullable|string|size:3',
            'valueDate' => 'nullable|date',
        ]);

        try {
            $account = Account::onlyManual()->findOrFail($request->input('account_id'));
            $transaction = Transaction::findOrFail($request->input('transaction_id'));

            DB::beginTransaction();

            $transaction->update([
                'entryReference' => $request->input('entryReference'),
                'checkId' => $request->input('checkId'),
                'bookingDate' => $request->input('bookingDate'),
                'valueDate' => $request->input('valueDate'),
                'transactionAmount_amount' => $request->input('transactionAmount_amount'),
                'transactionAmount_currency' => $request->input('transactionAmount_currency', 'EUR'),
                'remittanceInformationUnstructured' => $request->input('remittanceInformationUnstructured'),
                'bankTransactionCode' => $request->input('bankTransactionCode'),
                'proprietaryBankTransactionCode' => $request->input('proprietaryBankTransactionCode'),
                'internalTransactionId' => $request->input('internalTransactionId'),
                'debtorName' => $request->input('debtorName'),
                'debtorAccount' => $request->input('debtorAccount'),
                'account_id' => $account->code,
                'category_id' => Transaction::getCategoryId($request->input('remittanceInformationUnstructured')),
            ]);

            DB::commit();

            return Redirect::route('profile.transaction.edit', ['id' => $account->code])
                ->with('success', __('status.transactionscontroller.transaction-updated'));
        } catch (Exception $e) {
            Log::error('Error updating transaction', [
                'error' => $e->getMessage(),
                'transaction_id' => $request->input('transaction_id'),
                'account_id' => $request->input('account_id'),
            ]);
            DB::rollBack();

            return Redirect::route('profile.transaction.edit', ['id' => $request->input('account_id')])
                ->with('error', __('status.transactionscontroller.transaction-update-failed'));
        }
    }

    /**
     * Delete a transaction.
     *
     * This method validates the incoming request data to ensure the transaction ID and account ID are provided
     * and exist in the database. If valid, it fetches the transaction and account,
     * then deletes the transaction within a database transaction.
     * On success, redirects to the transaction edit page with a success message.
     * If an error occurs, logs the exception and redirects back with an error message.
     *
     * @return RedirectResponse A redirection to the transaction edit page with success or error feedback.
     *
     * @throws Throwable
     */
    public function destroy(): RedirectResponse
    {
        $validated = request()->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'account_id' => 'required|exists:accounts,id',
        ]);

        $transaction = Transaction::findOrFail($validated['transaction_id']);
        $account = Account::onlyManual()->findOrFail($validated['account_id']);

        DB::transaction(function () use ($transaction) {
            $transaction->delete();
        });

        return Redirect::route('profile.transaction.edit', ['id' => $account->code])
            ->with('success', __('status.transactionscontroller.transaction-deleted'));
    }
}
