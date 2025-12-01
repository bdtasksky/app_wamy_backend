<?php

namespace Modules\Project\Entities;
use Illuminate\Database\Eloquent\Model;

class MosqueDetail extends Model
{
    protected $table = 'mosque_details';
    protected $fillable = ['project_id','construction_stage','estimated_cost','land_area','main_materials','architect_contact'];
    protected $casts = ['main_materials' => 'array', 'estimated_cost' => 'decimal:2'];
    public function project(){ return $this->belongsTo(Project::class, 'project_id'); }
}
