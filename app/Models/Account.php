<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $table = 'accounts';
    protected $guarded = [
        'id',
    ];

    public function owner() {
        return $this->beLongsTo(User::class, 'owner_id');
    }

    static public function createNewAccount($details) {
        
        return Account::create([
            'account_name' => $details['account_name'],
            'account_number' => Account::generateReferenceNumber(),
            'owner_id' => $details['owner_id'],
        ]);
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class);
    }
    
    static public function generateReferenceNumber()
    {
        $prefix = 'ACC';
        $date = date('Ymd'); // Current date in YYYYMMDD format
        $randomNumber = mt_rand(10000, 99999); // Generate a random 4-digit number
        $accountNumber = $prefix . $date . $randomNumber;
        return $accountNumber;
    }
}
