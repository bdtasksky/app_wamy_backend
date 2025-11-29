<?php

namespace Modules\Accounts\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccOpeningBalance extends Model
{
    use HasFactory;

    protected $table = 'acc_openingbalance';
    protected $gurded = ['created_at', 'updated_at'];
    
}
