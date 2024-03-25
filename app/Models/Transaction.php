<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $guarded = [
        'id',
    ];

    public function payment()
    {
       return $this->hasMany(TransactionPayment::class);
    }

    public function owner()
    {
       return $this->belongsTo(User::class,'created_by');
    }
}
