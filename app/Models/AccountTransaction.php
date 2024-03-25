<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    use HasFactory;
    protected $table = 'account_transactions';
    protected $guarded = [
        'id',
    ];

    public function account()
    {
       return $this->belongsTo(Account::class);
    }

    public function user()
    {
       return $this->belongsTo(User::class);
    }

    public function payment()
    {
       return $this->belongsTo(TransactionPayment::class);
    }
}
