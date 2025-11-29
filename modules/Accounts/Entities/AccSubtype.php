<?php

namespace Modules\Accounts\Entities;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Modules\Accounts\Entities\AccSubcode;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class AccSubtype extends Model
{
    use HasFactory;

    protected $table = 'acc_subtype';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        if (Auth::check()) {
            self::creating(function($model) {
                $model->uuid = (string) Str::uuid();
                $model->created_by = Auth::id();

                
            });

            self::updating(function($model) {
                $model->updated_by = Auth::id();
                
            });
        }

        static::addGlobalScope('sortByLatest', function (Builder $builder) {
            $builder->orderByDesc('id');
        });
    }

    public static function getCacheInfo(){
        $data = AccSubtype::get();
        return $data;
    }
    public static function getCacheInfoAll(){
        $data =  AccSubtype::get();
        return $data;
    }



    public function accSubCode()
    {
        return $this->hasMany(AccSubcode::class,'subTypeID');
    }




}
