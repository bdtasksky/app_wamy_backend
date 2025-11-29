<?php

namespace Modules\Accounts\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Purchase\Entities\PurchasePayment;

class AccCoa extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'acc_coas';
    const CACHE_KEY = 'AccCoa';

    // Define the fields that are fillable in the model
    protected $fillable = [
        'account_code',
        'account_name',
        'head_level',
        'parent_id',
        'acc_type_id',
        'is_cash_nature',
        'is_bank_nature',
        'is_budget',
        'is_depreciation',
        'depreciation_rate',
        'is_subtype',
        'subtype_id',
        'is_stock',
        'is_fixed_asset_schedule',
        'note_no',
        'asset_code',
        'dep_code',
        'is_active',
        'is_wallet',
    ];

    //Boot method for auto UUID and created_by and updated_by field value
    protected static function boot()
    {
        parent::boot();
        if (Auth::check()) {
            self::creating(function ($model) {
                $model->uuid = (string) Str::uuid();
                $model->created_by = Auth::id();
                // self::resetCacheInfo(self::CACHE_KEY.get_company_db());
            });

            self::updating(function ($model) {
                $model->updated_by = Auth::id();
                // self::resetCacheInfo(self::CACHE_KEY.get_company_db());
            });

            self::deleted(function ($model) {
                $model->updated_by = Auth::id();
                $model->save();
                // self::resetCacheInfo(self::CACHE_KEY.get_company_db());
            });
        }

        self::created(function ($model) {
            $model->account_code = str_pad($model->id, 4, '0', STR_PAD_LEFT);
            $model->save();
            // self::resetCacheInfo(self::CACHE_KEY.get_company_db());
        });
    }

    public static function getCacheInfoIsActive()
    {
        // $data = Cache::rememberForever(self::CACHE_KEY.get_company_db(), function () {
        //     $info = AccCoa::where('is_active', 1);

        //     return $info;
        // });

        $data =  AccCoa::where('is_active', 1);
        return $data;
    }

    public static function resetCacheInfo($prefix)
    {
        Cache::forget($prefix);
    }

    //Relationship with AccTransaction: Each AccCoa has multiple AccTransactions
    public function accTransactions()
    {
        return $this->hasMany(AccTransaction::class);
    }

    //Relationship with AccVoucher: Each AccCoa has multiple AccVouchers
    public function accVouchers()
    {
        return $this->hasMany(AccVoucher::class);
    }

    // Relationship with AccSubtype: Each AccCoa belongs to one AccSubtype
    public function subtype(): BelongsTo
    {
        return $this->belongsTo(AccSubtype::class, 'subtype_id', 'id');
    }

    // Relationship with parent AccCoa: Each AccCoa belongs to a parent AccCoa (hierarchical structure)
    public function parentName(): BelongsTo
    {
        return $this->belongsTo(AccCoa::class, 'parent_id', 'id')->select('id', 'account_name');
    }

    // Relationship with child AccCoa (head_level = 2)
    public function secondChild()
    {
        return $this->hasMany(AccCoa::class, 'parent_id', 'id')->where('head_level', 2);
    }

    // Relationship with child AccCoa (head_level = 3)
    public function thirdChild()
    {
        return $this->hasMany(AccCoa::class, 'parent_id', 'id')->where('head_level', 3);
    }

    // Relationship with child AccCoa (head_level = 4)
    public function fourthChild()
    {
        return $this->hasMany(AccCoa::class, 'parent_id', 'id')->where('head_level', 4);
    }

    // Scope to filter parent AccCoa
    public function scopeParent($query)
    {
        return $query->where('parent_id', 0)->where('head_level', 1);
    }

    // Scope to filter active AccCoa
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function purchasePayment(): BelongsTo
    {
        return $this->belongsTo(PurchasePayment::class, 'id', 'payment_id');
    }
    public function children()
    {
        return $this->hasMany(AccCoa::class, 'parent_id');
    }

}