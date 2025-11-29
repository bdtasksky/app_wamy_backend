<?php

namespace Modules\Accounts\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccInstallmentRecord extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'acc_installment_schedules';   

    const STATUS = [
        'Unpaid',
        'Paid',
        'Processing',
        'Adjusted',
        'Unadjusted',
    ];


    public function accInstallment()
    {
        return $this->belongsTo(AccInstallment::class);
    }

    // public function employee()
    // {
    //     return $this->belongsTo(Employee::class);
    // }
    
}
