<?php

namespace Modules\Project\Entities;
use Illuminate\Database\Eloquent\Model;

class RehabFamily extends Model
{
    protected $table = 'rehab_families';
    protected $fillable = ['project_id','family_head_name','members_count','vulnerabilities','monthly_expenses','preferred_assistance'];
    protected $casts = ['vulnerabilities' => 'array', 'preferred_assistance' => 'array', 'monthly_expenses' => 'decimal:2'];
    public function project(){ return $this->belongsTo(Project::class, 'project_id'); }
}
