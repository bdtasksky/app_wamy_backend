<?php

namespace Modules\Accounts\Entities;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounts\Entities\AccTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccSubcode extends Model
{
    protected $table = 'acc_subcode'; 
    public $timestamps = false;
    protected $fillable = ['name', 'subTypeID', 'refCode'];
    public function accSubtype() {
        return $this->hasOne(AccSubtype::class, 'id', 'subTypeID');
    }
}
