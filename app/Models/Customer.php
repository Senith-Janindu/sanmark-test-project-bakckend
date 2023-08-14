<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_number',
        'reading_date',
        'reading_value',
        'fixed_charge',
        'first_range_amount',
        'second_range_amount',
        'third_range_amount',
        'total_amount'
    ];
}
