<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DatatableController extends Controller
{
    /**
     * Retrieves the user's accounts with pagination, searching, and ordering capabilities.
     *
     * @param Request $request The HTTP request containing pagination and filtering parameters.
     * @return JsonResponse The JSON response containing account data, metadata, and total balances information.
     */
    public function accounts(Request $request): JsonResponse
    {
        $user = Auth::user();

        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $orderDir = $request->input('order_dir', 'desc');

        $validOrderColumns = ['iban', 'owner_name', 'balance'];
        $orderBy = $request->input('order_by', 'iban');

        if (!in_array($orderBy, $validOrderColumns)) {
            $orderBy = 'iban';
        }

        $query = Account::where('user_id', $user->id);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('iban', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        $recordsTotal = Account::where('user_id', $user->id)->count();
        $recordsFiltered = $query->count();

        $totalAmount = number_format($query->with('balances')->get()->sum(function ($account) {;
            return $account->balances()->balanceTypeForward()->lastInstance()->first() ? $account->balances()->balanceTypeForward()->lastInstance()->first()->amount : 0;
        }), 2, ',', '.') . ' €';

        $data = $query->orderBy($orderBy, $orderDir)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data->map(function ($account) {
                $lastInstance = $account->balances()->balanceTypeForward()->lastInstance()->first();
                $color = $lastInstance && $lastInstance->amount > 0 ? 'success' : 'error';

                return [
                    'iban' => $account->iban,
                    'owner_name' => $account->owner_name,
                    'balance' => $this::toColor(number_format($lastInstance ? $lastInstance->amount : 0, 2, ',', '.') . ' €', $color),
                ];
            }),
            'totalAmount' => $totalAmount,
        ]);
    }

    /**
     * Retrieves and filters balance data for the authenticated user's accounts, based on provided parameters.
     *
     * Handles pagination, sorting, and searching functionalities. This method supports optional filtering by account ID
     * and provides formatted data such as amounts with color-coded visual representation.
     *
     * @param Request $request Incoming HTTP request containing parameters such as 'page', 'per_page', 'search', 'order_by', and 'order_dir'.
     * @param string|null $id Optional account ID for filtering balances by a specific account.
     * @return JsonResponse Returns a JSON response containing the filtered balance data, total records, filtered records, and other metadata.
     */
    public function balances(Request $request, string $id = null): JsonResponse
    {
        $user = Auth::user();

        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $orderDir = $request->input('order_dir', 'desc');

        $validOrderColumns = ['iban', 'reference_date', 'balance_type', 'amount', 'balance'];
        $orderBy = $request->input('order_by', 'reference_date');

        if (!in_array($orderBy, $validOrderColumns)) {
            $orderBy = 'reference_date';
        }

        if ($id) {
            $query = Balance::whereHas('account', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('account_id', $id);;
        } else {
            $query = Balance::whereHas('account', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $recordsTotal = $query->count();

        if (!empty($search)) {
            $query->leftJoin('accounts', 'accounts.id', '=', 'balances.account_id');

            $query->where(function ($q) use ($search) {
                $q->where('accounts.iban', 'like', "%{$search}%")
                    ->orWhere('reference_date', 'like', "%{$search}%")
                    ->orWhere('balance_type', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('currency', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $query->count();
        $totalAmount = number_format($query->sum('amount'), 2, ',', '.') . ' €';

        $data = $query->orderBy($orderBy, $orderDir)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data->map(function ($balance) {
                $color = $balance->amount > 0 ? 'success' : 'error';

                return [
                    'iban' => $balance->account->iban,
                    'reference_date' => $balance->reference_date->format('d-m-Y'),
                    'balance_type' => $balance->balance_type,
                    'amount' => $this::toColor(number_format($balance->amount, 2, ',', '.') . ' €', $color),
                    'currency' => $balance->currency,
                    'actions' => view('partials.datatable.balance-actions', [
                        'balance' => $balance,
                        'account' => $balance->account,
                        'user' => $balance->account->user,
                    ])->render(),
                ];
            }),
            'totalAmount' => $totalAmount,
        ]);
    }

    /**
     * Retrieves and filters transaction data for the authenticated user's accounts, based on provided parameters.
     *
     * Handles pagination, sorting, and searching functionalities. This method supports optional filtering by account ID
     * and provides formatted data such as transaction amounts with color-coded indicators. Includes metadata like total
     * and filtered record counts and total transaction amounts.
     *
     * @param Request $request Incoming HTTP request containing parameters such as 'page', 'per_page', 'search', 'order_by', and 'order_dir'.
     * @param string|null $id Optional account ID for filtering transactions by a specific account.
     * @return JsonResponse Returns a JSON response containing the filtered transaction data, total records, filtered records, formatted data, and other metadata.
     */
    public function transactions(Request $request, string $id = null): JsonResponse
    {
        $user = Auth::user();

        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $orderDir = $request->input('order_dir', 'desc');

        $validOrderColumns = ['iban', 'bookingDate', 'debtorName', 'remittanceInformationUnstructured', 'transactionAmount_amount'];
        $orderBy = $request->input('order_by', 'bookingDate');

        if (!in_array($orderBy, $validOrderColumns)) {
            $orderBy = 'bookingDate';
        }

        if ($id) {
            $query = Transaction::whereHas('account', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('account_id', $id);
        } else {
            $query = Transaction::whereHas('account', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $recordsTotal = $query->count();
        $totalAmount = number_format($query->sum('transactionAmount_amount'), 2, ',', '.') . ' €';

        if (!empty($search)) {
            $query->leftJoin('accounts', 'accounts.id', '=', 'transactions.account_id');

            $query->where(function ($q) use ($search) {
                $q->where('accounts.iban', 'like', "%{$search}%")
                    ->orWhere('bookingDate', 'like', "%{$search}%")
                    ->orWhere('debtorName', 'like', "%{$search}%")
                    ->orWhere('remittanceInformationUnstructured', 'like', "%{$search}%")
                    ->orWhere('transactionAmount_amount', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $query->count();

        $data = $query->orderBy($orderBy, $orderDir)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data->map(function ($transaction) {
                $color = $transaction->transactionAmount_amount > 0 ? 'success' : 'error';

                return [
                    'iban' => $transaction->account->iban,
                    'bookingDate' => $transaction->bookingDate->format('d-m-Y'),
                    'debtorName' => $transaction->debtorNameFormat,
                    'remittanceInformationUnstructured' => json_decode($transaction->remittanceInformationUnstructured) ? json_decode($transaction->remittanceInformationUnstructured)[0] : '--',
                    'transactionAmount_amount' => $this::toColor(number_format($transaction->transactionAmount_amount, 2, ',', '.') . ' €', $color),
                    'actions' => view('partials.datatable.transaction-actions', [
                        'transaction' => $transaction,
                        'account' => $transaction->account,
                        'user' => $transaction->account->user,
                    ])->render(),
                ];
            }),
            'totalAmount' => $totalAmount,
        ]);
    }

    /**
     * Applies a color to the text.
     *
     * @param string $text The text to color.
     * @param string $color The color to apply.
     * @return string The colored text.
     */
    public static function toColor(string $text, string $color): string
    {
        return "<span class='text-{$color}'>{$text}</span>";
    }
}
