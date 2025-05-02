<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Balance;
use App\Models\Bank;
use App\Models\Institution;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class NordigenController extends Controller
{
    protected $baseUrl = 'https://bankaccountdata.gocardless.com/api/v2';
    protected $secretId;
    protected $secretKey;
    protected $redirectUri;

    public function __construct()
    {
        $this->secretId = env('NORDIGEN_SECRET_ID');
        $this->secretKey = env('NORDIGEN_SECRET_KEY');
        $this->redirectUri = env('NORDIGEN_REDIRECT_URI');
    }

    public function authenticate()
    {
        $response = Http::post("{$this->baseUrl}/token/new/", [
            'secret_id' => $this->secretId,
            'secret_key' => $this->secretKey
        ]);

        session(['access_token' => $response['access']]);

        return redirect()->route('nordigen.create-requisition');
    }

    public function createRequisition()
    {
        $accessToken = session('access_token');
        $user = auth()->user();
        $bank = Bank::where('user_id', $user->id)->first();

        $institutionId = $bank->institution->code;

        $response = Http::withToken($accessToken)->post("{$this->baseUrl}/requisitions/", [
            'redirect' => $this->redirectUri,
            'institution_id' => $institutionId,
            'reference' => uniqid(),
            'user_language' => 'ES'
        ]);

        session(['requisition_id' => $response['id']]);

        session(['callback_url' => $response['link']]);

        return redirect()->route('bank.configuration');
    }

    public function callback(Request $request)
    {
        $accessToken = session('access_token');
        $requisitionId = session('requisition_id');

        // Obtener cuentas asociadas
        $requisition = Http::withToken($accessToken)->get("{$this->baseUrl}/requisitions/{$requisitionId}");
        $accounts = $requisition['accounts'];

        // Obtener institution
        $institution = Institution::where('code', $requisition['institution_id'])->first();

        if (empty($accounts)) {
            return Redirect::route('bank.configuration')->with('status', 'No accounts found');
        }

        // Eliminar cuentas existentes
        if (count(Account::all()) > 0) {
            foreach (Account::all() as $account) {
                $account->delete();
            }
        }

        foreach ($accounts as $i => $accountId) {
            $accountData = Http::withToken($accessToken)->get("{$this->baseUrl}/accounts/{$accountId}")->json();
            $account = Account::where('id', $accountId)->first();

            if ($account) {
                $account->update([
                    'name' => $accountData['name'],
                    'iban' => $accountData['iban'],
                    'bban' => $accountData['bban'],
                    'status' => $accountData['status'],
                    'owner_name' => $accountData['owner_name'],
                    'created' => Carbon::parse($accountData['created'])->format('Y-m-d H:i:s'),
                    'last_accessed' => Carbon::parse($accountData['last_accessed'])->format('Y-m-d H:i:s'),
                    'institution_id' => $institution->id,
                    'user_id' => auth()->user()->id,
                ]);
            } else {
                Account::create([
                    'id' => $accountId,
                    'name' => $accountData['name'],
                    'iban' => $accountData['iban'],
                    'bban' => $accountData['bban'],
                    'status' => $accountData['status'],
                    'owner_name' => $accountData['owner_name'],
                    'created' => Carbon::parse($accountData['created'])->format('Y-m-d H:i:s'),
                    'last_accessed' => Carbon::parse($accountData['last_accessed'])->format('Y-m-d H:i:s'),
                    'institution_id' => $institution->id,
                    'user_id' => auth()->user()->id,
                ]);
            }

        }

        return redirect()->route('bank.configuration')->with('status', 'Accounts and transactions retrieved successfully');
    }

    public function transactions(Request $request, string $accountId)
    {
        $accessToken = session('access_token');

        $account = Account::where('id', $accountId)->first();
        $account->transactions_disabled_date = null;
        $account->save();

        $transactions = Http::withToken($accessToken)->get("{$this->baseUrl}/accounts/{$accountId}/transactions/")->json();

        if (isset($transactions['detail'])) {
            $account->transactions_disabled_date = $this->getSecondsFromString($transactions['detail']);
            $account->save();

            return Redirect::route('bank.configuration');
        }

        $bookedTransactions = $transactions["transactions"]['booked'];
        $pendingTransactions = $transactions["transactions"]['pending'];

        foreach ($bookedTransactions as $transaction) {
            $transactionModel = Transaction::where('id', $transaction['transactionId'])->first();

            if ($transactionModel) {
                $transactionModel->update([
                    'entryReference' => $transaction['entryReference'],
                    'checkId' => $transaction['checkId'] ?? null,
                    'bookingDate' => Carbon::parse($transaction['bookingDate'])->format('Y-m-d H:i:s'),
                    'valueDate' => Carbon::parse($transaction['valueDate'])->format('Y-m-d H:i:s'),
                    'transactionAmount_amount' => $transaction['transactionAmount']['amount'],
                    'transactionAmount_currency' => $transaction['transactionAmount']['currency'],
                    'remittanceInformationUnstructured' => isset($transaction['remittanceInformationUnstructuredArray']) ? json_encode($transaction['remittanceInformationUnstructuredArray']) : null,
                    'bankTransactionCode' => $transaction['bankTransactionCode'] ?? null,
                    'proprietaryBankTransactionCode' => $transaction['proprietaryBankTransactionCode'] ?? null,
                    'internalTransactionId' => $transaction['internalTransactionId'] ?? null,
                    'debtorName' => $transaction['debtorName'] ?? null,
                    'debtorAccount' => $transaction['debtorAccount'] ?? null,
                ]);
            } else {
                Transaction::create([
                    'id' => $transaction['transactionId'],
                    'entryReference' => $transaction['entryReference'],
                    'checkId' => $transaction['checkId'] ?? null,
                    'bookingDate' => Carbon::parse($transaction['bookingDate'])->format('Y-m-d H:i:s'),
                    'valueDate' => Carbon::parse($transaction['valueDate'])->format('Y-m-d H:i:s'),
                    'transactionAmount_amount' => $transaction['transactionAmount']['amount'],
                    'transactionAmount_currency' => $transaction['transactionAmount']['currency'],
                    'remittanceInformationUnstructured' => isset($transaction['remittanceInformationUnstructuredArray']) ? json_encode($transaction['remittanceInformationUnstructuredArray']) : null,
                    'bankTransactionCode' => $transaction['bankTransactionCode'] ?? null,
                    'proprietaryBankTransactionCode' => $transaction['proprietaryBankTransactionCode'] ?? null,
                    'internalTransactionId' => $transaction['internalTransactionId'] ?? null,
                    'debtorName' => $transaction['debtorName'] ?? null,
                    'debtorAccount' => $transaction['debtorAccount'] ?? null,
                    'account_id' => $accountId,
                ]);
            }
        }

        return Redirect::route('bank.configuration');
    }

    public function balances(Request $request, string $accountId)
    {
        $accessToken = session('access_token');

        $account = Account::where('id', $accountId)->first();
        $account->balance_disabled_date = null;
        $account->save();

        $balances = Http::withToken($accessToken)->get("{$this->baseUrl}/accounts/{$accountId}/balances/")->json();

        if (isset($balances['detail'])) {
            $account->balance_disabled_date = $this->getSecondsFromString($balances['detail']);
            $account->save();

            return Redirect::route('bank.configuration');
        }

        foreach ($balances["balances"] as $bal) {
            $balanceModel = Balance::where('account_id', $accountId)
                ->where('balance_type', $bal['balanceType'])
                ->where('reference_date', Carbon::parse($bal['referenceDate'])->format('Y-m-d H:i:s'))
                ->first();

            if ($balanceModel) {
                $balanceModel->update([
                    'amount' => $bal['balanceAmount']['amount'],
                    'currency' => $bal['balanceAmount']['currency'],
                    'reference_date' => Carbon::parse($bal['referenceDate'])->format('Y-m-d H:i:s'),
                ]);
            } else {
                Balance::create([
                    'amount' => $bal['balanceAmount']['amount'],
                    'currency' => $bal['balanceAmount']['currency'],
                    'balance_type' => $bal['balanceType'],
                    'reference_date' => Carbon::parse($bal['referenceDate'])->format('Y-m-d H:i:s'),
                    'account_id' => $accountId,
                ]);
            }
        }

        return Redirect::route('bank.configuration');
    }

    public function getSecondsFromString($string)
    {
        $explode = explode(' ', $string);
        $time = $explode[count($explode) - 2];

        return Carbon::now()->addSeconds(intval($time));
    }

    public function insertInstitutions(Request $request)
    {
        $accessToken = session('access_token');

        if (!$accessToken) {
            $response = Http::post("{$this->baseUrl}/token/new/", [
                'secret_id' => $this->secretId,
                'secret_key' => $this->secretKey
            ]);

            session(['access_token' => $response['access']]);

            $accessToken = $response['access'];
        }

        $response = Http::withToken($accessToken)->get("{$this->baseUrl}/institutions/");

        $institutions = $response->json();

        if ($institutions) {
            // Elimina todas las instituciones existentes
            if (count(Institution::all()) > 0) {
                Institution::truncate();
            }
        } else {
            return Redirect::route('profile.edit')->with('status', 'No institutions found');
        }

        foreach ($institutions as $institution) {
            $institutionData = [
                'code' => $institution['id'],
                'name' => $institution['name'],
                'bic' => $institution['bic'],
                'transaction_total_days' => $institution['transaction_total_days'],
                'countries' => json_encode($institution['countries']),
                'logo' => $institution['logo'],
                'max_access_valid_for_days' => $institution['max_access_valid_for_days'],
            ];

            // Aquí puedes guardar la institución en tu base de datos
            Institution::create($institutionData);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
}
