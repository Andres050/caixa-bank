<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'bookingDate' => 'datetime',
        'valueDate' => 'datetime',
    ];

    protected $fillable = [
        'id',
        'entryReference',
        'checkId',
        'bookingDate',
        'valueDate',
        'transactionAmount_amount',
        'transactionAmount_currency',
        'remittanceInformationUnstructured',
        'bankTransactionCode',
        'proprietaryBankTransactionCode',
        'internalTransactionId',
        'debtorName',
        'debtorAccount',
        'account_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'code');
    }

    public function getDebtorFullNameAttribute()
    {
        return $this->attributes['debtorName'];
    }

    public function getBankTransactionCodeStringAttribute() : string
    {
        $types = [
            'PMNT-ICDT-ESCT' => 'Domiciliación SEPA',
            'PMNT-IRCT-ESCT' => 'Transferencia recibida',
            'PMNT-ICDT-CCRD' => 'Pago con tarjeta de crédito',
            'PMNT-ICDT-DBIT' => 'Pago con tarjeta de débito',
            'PMNT-ICDT-CHCK' => 'Cheque',
            'PMNT-ICDT-TRF'  => 'Transferencia',
            'PMNT-ICDT-OTHR' => 'Otro tipo de pago',
            // Agrega más códigos según sea necesario
        ];

        return $types[$this->bankTransactionCode] ?? 'Código desconocido';
    }
}
