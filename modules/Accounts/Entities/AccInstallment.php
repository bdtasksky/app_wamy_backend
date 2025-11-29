<?php

namespace Modules\Accounts\Entities;

use Modules\Accounts\Entities\AccCoa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccInstallment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();   

        static::addGlobalScope('sortByLatest', function (Builder $builder) {
            $builder->orderByDesc('id');
        });
    }

    // public function employee()
    // {
    //     return $this->belongsTo(Employee::class);
    // }

    public function accInstallmentRecords()
    {
        return $this->hasMany(AccInstallmentRecord::class,'installments_id','id');
    }
    
    public function acc_coa()
    {
        return $this->belongsTo(AccCoa::class, 'acc_coas_id');
    }
}
