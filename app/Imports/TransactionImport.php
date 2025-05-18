<?php

namespace App\Imports;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TransactionImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Model|Transaction|null
     */
    public function model(array $row): Model|Transaction|null
    {
        // Check if the account exists for the authenticated user
        $account = auth()->user()->accounts()
            ->where('id', $row['account_id'])
            ->first();

        if ($account) {
            return new Transaction([
                'id' => Str::uuid(),
                'entryReference' => $row['entry_reference'] ?? null,
                'checkId' => $row['check_id'] ?? null,
                'bookingDate' => Date::excelToDateTimeObject($row['booking_date'])->format('d-m-Y H:i:s'),
                'valueDate' => Date::excelToDateTimeObject($row['value_date'])->format('d-m-Y H:i:s'),
                'transactionAmount_amount' => $row['transaction_amount'],
                'transactionAmount_currency' => $row['currency'] ?? null,
                'remittanceInformationUnstructured' => $row['remittance_information'] ?? null,
                'bankTransactionCode' => $row['bank_transaction_code'] ?? null,
                'proprietaryBankTransactionCode' => $row['proprietary_bank_transaction_code'] ?? null,
                'internalTransactionId' => $row['internal_transaction_id'] ?? null,
                'debtorName' => $row['debtor_name'] ?? null,
                'debtorAccount' => $row['debtor_account'] ?? null,
                'account_id' => $account->id,
            ]);
        }

        return null;
    }
}
