<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Balance;
use App\Models\BankDataSync;
use App\Models\Institution;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class NordigenController extends Controller
{
    protected string $baseUrl = 'https://bankaccountdata.gocardless.com/api/v2';
    protected mixed $secretId;
    protected mixed $secretKey;
    protected mixed $redirectUri;

    public function __construct()
    {
        $this->secretId = env('NORDIGEN_SECRET_ID');
        $this->secretKey = env('NORDIGEN_SECRET_KEY');
        $this->redirectUri = env('NORDIGEN_REDIRECT_URI');
    }

    /**
     * Authenticates the user by sending a POST request to retrieve a new access token
     * and storing it in the session. Redirects the user to the requisition creation route.
     *
     * @return RedirectResponse
     */
    public function authenticate(): RedirectResponse
    {
        $response = Http::post("{$this->baseUrl}/token/new/", [
            'secret_id' => $this->secretId,
            'secret_key' => $this->secretKey
        ]);

        session(['access_token' => $response['access']]);

        return redirect()->route('nordigen.create-requisition');
    }

    /**
     * Creates a new requisition by sending a POST request to the specified endpoint
     * using the access token stored in the session. The requisition-related data is
     * stored in the session for further use. Redirects the user to the bank configuration route.
     *
     * @return RedirectResponse
     */
    public function createRequisition(): RedirectResponse
    {
        $user = Auth::user();
        $accessToken = session('access_token');
        $bank = $user->bank;

        $institutionId = $bank->institution->code;

        $response = Http::withToken($accessToken)->post("{$this->baseUrl}/requisitions/", [
            'redirect' => $this->redirectUri,
            'institution_id' => $institutionId,
            'reference' => uniqid(),
            'user_language' => 'ES'
        ]);

        session(['requisition_id' => $response['id']]);

        session(['callback_url' => $response['link']]);

        return redirect()->route('dashboard.configuration');
    }

    /**
     * Handles the callback process after a successful connection to retrieve user bank accounts and syncs them with the application.
     *
     * This function retrieves the requisition details from an external service using the provided access token.
     * If accounts are successfully retrieved, syncs the fetched account data, including creating or updating accounts as necessary.
     * The function also logs the synchronization process and redirects the user to the configuration page.
     *
     *
     * @return RedirectResponse Redirects to the bank configuration page with a status message.
     */
    public function callback(): RedirectResponse
    {
        $accessToken = session('access_token');
        $requisitionId = session('requisition_id');

        $requisition = Http::withToken($accessToken)->get("{$this->baseUrl}/requisitions/{$requisitionId}");
        $accounts = $requisition['accounts'];
        $user = Auth::user();

        $institution = Institution::where('code', $requisition['institution_id'])->first();

        if (empty($accounts)) {
            return Redirect::route('dashboard.configuration')->with('status', __('status.nordigencontroller.callback-failed'));
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
                    'user_id' => $user->id,
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
                    'user_id' => $user->id,
                ]);
            }

            BankDataSync::create([
                'data_type' => BankDataSync::$ACCOUNT_TYPE,
                'status' => 'success',
                'account_id' => $accountId,
                'last_fetched_at' => Carbon::now(),
            ]);
        }

        return redirect()->route('dashboard.configuration')->with('status', __('status.nordigencontroller.callback-success'));;
    }

    /**
     * Retrieves and synchronizes transactions for a specific bank account with the application.
     *
     * This function checks if transactions are enabled for the given account. If they are,
     * it fetches the latest transaction data from an external service using the account ID and
     * access token. The function either updates existing transaction records or creates new ones,
     * while also handling the status of transactions being disabled and logging the synchronization
     * as successful in the database.
     *
     * @param string $accountId The ID of the bank account whose transactions need to be synchronized.
     *
     * @return RedirectResponse Redirects to the bank configuration page upon completion of the synchronization process.
     */
    public function transactions(string $accountId): RedirectResponse
    {
        $accessToken = session('access_token');

        $account = Account::where('id', $accountId)->onlyApi()->first();
        if ($account && !$account->transactionsDisabled) {
            $account->transactions_disabled_date = null;
            $account->save();

            $transactions = Http::withToken($accessToken)->get("{$this->baseUrl}/accounts/{$accountId}/transactions/")->json();

            if (isset($transactions['detail'])) {
                $account->transactions_disabled_date = $this->getSecondsFromString($transactions['detail']);
                $account->save();

                return Redirect::route('dashboard.configuration');
            }

            $bookedTransactions = $transactions["transactions"]['booked'];

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
                        'category_id' => Transaction::getCategoryId(isset($transaction['remittanceInformationUnstructuredArray']) ? json_encode($transaction['remittanceInformationUnstructuredArray']) : null),
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
                        'category_id' => Transaction::getCategoryId(isset($transaction['remittanceInformationUnstructuredArray']) ? json_encode($transaction['remittanceInformationUnstructuredArray']) : null),
                    ]);
                }
            }

            BankDataSync::create([
                'data_type' => BankDataSync::$TRANSACTIONS_TYPE,
                'status' => 'success',
                'account_id' => $accountId,
                'last_fetched_at' => Carbon::now(),
            ]);
        }

        return Redirect::route('dashboard.configuration');
    }

    /**
     * Synchronizes the balance information of a specific bank account with the application.
     *
     * This method retrieves the current balances of a given account from an external service
     * using the provided access token. It updates the application's database with the fetched
     * balances by creating new records or updating existing ones. If balance retrieval fails,
     * it updates the account's balance disabled date to signify the error.
     * Additionally, it logs the synchronization process for tracking purposes.
     *
     * @param string $accountId The unique identifier of the account whose balances need to be synchronized.
     *
     * @return RedirectResponse Redirects to the bank configuration page.
     */
    public function balances(string $accountId): RedirectResponse
    {
        $accessToken = session('access_token');

        $account = Account::where('id', $accountId)->onlyApi()->first();
        if ($account && !$account->balanceDisabled) {
            $account->balance_disabled_date = null;
            $account->save();

            $balances = Http::withToken($accessToken)->get("{$this->baseUrl}/accounts/{$accountId}/balances/")->json();

            if (isset($balances['detail'])) {
                $account->balance_disabled_date = $this->getSecondsFromString($balances['detail']);
                $account->save();

                return Redirect::route('dashboard.configuration');
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

            BankDataSync::create([
                'data_type' => BankDataSync::$BALANCE_TYPE,
                'status' => 'success',
                'account_id' => $accountId,
                'last_fetched_at' => Carbon::now(),
            ]);
        }

        return Redirect::route('dashboard.configuration');
    }

    /**
     * Extracts a time interval in seconds from a string and adds it to the current timestamp.
     *
     * This method parses a given string to retrieve a numeric value representing a time interval in seconds,
     * which is expected to be the second-to-last element of the string when split by spaces.
     * The retrieved interval is then added to the current date and time to calculate a new timestamp.
     *
     * @param string $string The input string containing the time interval in seconds.
     *
     * @return Carbon The calculated timestamp with the added time interval.
     */
    public function getSecondsFromString(string $string): Carbon
    {
        $explode = explode(' ', $string);
        $time = $explode[count($explode) - 2];

        return Carbon::now()->addSeconds(intval($time));
    }

    /**
     * Updates the specified account's transactions and balances by retrieving the latest data from the external service.
     *
     * This function ensures an active access token for communication with the external service, generating a new token if necessary.
     * It then updates the account's transactions and balances by invoking the appropriate methods.
     * Finally, it redirects the user to the bank configuration page with a success message upon successful update.
     *
     * @param Request $request The incoming HTTP request containing session and user information.
     * @param string $accountId The unique identifier of the account to be updated.
     *
     * @return RedirectResponse Redirects to the bank configuration page with a status message.
     */
    public function update(Request $request, string $accountId): RedirectResponse
    {
        if (!session('access_token')) {
            $response = Http::post("{$this->baseUrl}/token/new/", [
                'secret_id' => $this->secretId,
                'secret_key' => $this->secretKey
            ]);

            session(['access_token' => $response['access']]);
        }

        $this->transactions($accountId);
        $this->balances($accountId);

        return Redirect::route('dashboard.configuration')->with('status', __('status.nordigencontroller.update-account-success'));
    }

    /**
     * Updates all user accounts with the latest transactions and balances.
     *
     * This function checks for a valid access token in the session. If missing, it requests a new token
     * from the external service and stores it in the session. It then retrieves all accounts associated
     * with the authenticated user and updates each account's transactions and balances by invoking
     * the respective methods.
     * Once the process is complete, the user is redirected to the bank configuration page with a status message.
     *
     * @param Request $request The incoming HTTP request containing session and user information.
     *
     * @return RedirectResponse Redirects to the bank configuration page with a status message.
     */
    public function updateAll(Request $request): RedirectResponse
    {
        if (!session('access_token')) {
            $response = Http::post("{$this->baseUrl}/token/new/", [
                'secret_id' => $this->secretId,
                'secret_key' => $this->secretKey
            ]);

            session(['access_token' => $response['access']]);
        }

        $user = Auth::user();
        $accounts = Account::where('user_id', $user->id)->onlyApi()->get();

        if ($accounts->isEmpty()) {
            return Redirect::route('dashboard.configuration')->with('status', __('status.nordigencontroller.schedule-error'));
        }

        foreach ($accounts as $account) {
            $this->transactions($account->code);
            $this->balances($account->code);
        }

        return Redirect::route('dashboard.configuration')->with('status', __('status.nordigencontroller.schedule-updated'));
    }

    /**
     * Fetches institutions from an external service, deletes any existing institutions
     * in the database, and inserts the new institutions fetched from the service.
     *
     * If there isn't an `access_token` in the session, it generates and stores a new one.
     * Then, it uses this token to retrieve a list of institutions via an HTTP request.
     *
     * If institutions are successfully retrieved:
     * - Deletes all existing institution records in the database.
     * - Loops through each retrieved institution, preparing its data and saving it to the database.
     *
     * If no institutions are found, redirects the user to the profile edit page with an error status.
     *
     * Finally, redirects the user to the profile edit page with a success status after processing institutions.
     *
     * @return RedirectResponse Returns a redirect response to the profile edit route.
     */
    public function insertInstitutions(): RedirectResponse
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
            if (count(Institution::all()) > 0) {
                $allInstitutions = Institution::all();

                foreach ($allInstitutions as $institution) {
                    $institution->delete();
                }
            }
        } else {
            return Redirect::route('pages.profile.edit')->with('status', __('status.nordigencontroller.institutions-error'));
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

            Institution::create($institutionData);
        }

        return Redirect::route('pages.profile.edit')->with('status', __('status.nordigencontroller.institutions-updated'));
    }
}
