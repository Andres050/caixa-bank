<?php

namespace App\Imports;

use App\Models\Balance;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BalanceImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return Model|null
    */
    public function model(array $row)
    {
        // Check if the account exists for the authenticated user
        $account = auth()->user()->accounts()
            ->where('id', $row['account_id'])
            ->first();

        if ($account) {
            return new Balance([
                'amount' => $row['amount'],
                'currency' => $row['currency'],
                'balance_type' => $row['balance_type'],
                'reference_date' => Date::excelToDateTimeObject($row['reference_date'])->format('d-m-Y H:i:s'),
                'account_id' => $account->id,
            ]);
        }

        return null;
    }
}
