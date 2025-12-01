<?php

namespace Modules\Project\Entities;

use Modules\Project\Entities\Zone;
use Illuminate\Database\Eloquent\Model;
use Modules\Project\Entities\RehabFamily;
use Modules\Project\Entities\MosqueDetail;
use Modules\Project\Entities\OrphanDetail;
use Modules\Project\Entities\ScholarshipApplicant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'location');
    }
    public function mosque() { return $this->hasOne(MosqueDetail::class, 'project_id'); }
    public function orphans() { return $this->hasMany(OrphanDetail::class, 'project_id'); } // multiple orphan rows
    public function scholarships() { return $this->hasMany(ScholarshipApplicant::class, 'project_id'); }
    public function rehabs() { return $this->hasMany(RehabFamily::class, 'project_id'); }
}
