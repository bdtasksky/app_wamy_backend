<?php

namespace Modules\Project\Entities;
use Illuminate\Database\Eloquent\Model;

class OrphanDetail extends Model
{
    protected $table = 'orphan_details';
    protected $fillable = ['project_id','child_name','dob','gender','guardian_name','guardian_contact','medical_needs','education_status','monthly_support_required','image_path'];
    protected $casts = ['medical_needs' => 'array', 'monthly_support_required' => 'decimal:2', 'dob' => 'date'];
    public function project(){ return $this->belongsTo(Project::class, 'project_id'); }
}
