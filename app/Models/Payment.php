<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'amortization_id',
        'profile_id',
        'promoter_id',
        'amount',
        'state',
    ];

    public function amortization()
    {
        return $this->belongsTo(Amortization::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function promoter()
    {
        return $this->belongsTo(Promoter::class);
    }
}