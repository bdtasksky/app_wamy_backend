<?php

namespace Modules\Project\Entities;
use Illuminate\Database\Eloquent\Model;

class ScholarshipApplicant extends Model
{
    protected $table = 'scholarship_applicants';
    protected $fillable = ['project_id','student_name','university','program','year','gpa','documents','requested_amount'];
    protected $casts = ['documents' => 'array', 'gpa' => 'decimal:2', 'requested_amount' => 'decimal:2'];
    public function project(){ return $this->belongsTo(Project::class, 'project_id'); }
}
