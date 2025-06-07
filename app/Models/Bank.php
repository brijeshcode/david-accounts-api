<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    /** @use HasFactory<\Database\Factories\BankFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'starting_balance',
        'balance',
        'address',
        'account_no',
        'note',
        'active',
        'created_by_id',
        'created_by_ip',
        'created_by_agent',     
    ];
}
