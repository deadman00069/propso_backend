<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KYC extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'aadhaar_no',
        'pan_card_no',
        'account_name',
        'account_no',
        'ifsc_code',
        'bank_name',
        'is_verified',
        'uid',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
