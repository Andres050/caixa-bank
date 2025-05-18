<?php

namespace App\Imports;

use App\Models\Account;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AccountImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Model|Account|null
     */
    public function model(array $row): Model|Account|null
    {
        // Check if the account already exists for the authenticated user
        $existingAccount = Account::where('user_id', auth()->id())
            ->where('id', $row['id'])
            ->first();

        if (!$existingAccount) {
            return new Account([
                'id' => $row['id'],
                'name' => $row['name'],
                'iban' => $row['iban'],
                'bban' => $row['bban'] ?? '',
                'status' => $row['status'] ?? '',
                'owner_name' => $row['owner_name'],
                'created' => Date::excelToDateTimeObject($row['created'])->format('d-m-Y H:i:s'),
                'last_accessed' => Date::excelToDateTimeObject($row['last_accessed'])->format('d-m-Y H:i:s'),
                'institution_id' => auth()->user()->bank->institution_id,
                'user_id' => auth()->id(),
                'type' => Account::$accountTypes['manual'],
            ]);
        }

        return null;
    }
}
