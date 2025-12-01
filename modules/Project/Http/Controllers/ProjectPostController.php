<?php

namespace Modules\Project\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Project\Entities\Project;
use Illuminate\Support\Facades\Storage;
use Modules\Project\Entities\RehabFamily;
use Modules\Project\Entities\MosqueDetail;
use Modules\Project\Entities\OrphanDetail;
use Illuminate\Contracts\Support\Renderable;
use Modules\Project\Entities\ScholarshipApplicant;

class ProjectPostController extends Controller
{
   

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $projects = Project::all();
        return view('project::project_post.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // Basic master validation
        $masterRules = [
            'project_id' => 'required|integer',
        ];

        $validatedMaster = $request->validate($masterRules);

        // Determine final type slug:
        // If editing an existing project (project_id), prefer its project_type.
        $projectId = $request->input('project_id');
        $type = null;
        if ($projectId) {
            $proj = Project::find($projectId);
            if ($proj) $type = $proj->project_type; // stored slug on project (recommended)
        }
        // if no existing type, take manual selection from form
        if (!$type) {
            $type = $request->input('project_type_manual') ? strtolower($request->input('project_type_manual')) : null;
            if ($type) {
                // normalize common labels
                $t = strtolower($type);
                if (str_contains($t, 'mosque')) $type = 'mosque';
                elseif (str_contains($t, 'orphan')) $type = 'orphan';
                elseif (str_contains($t, 'scholar')) $type = 'scholarship';
                elseif (str_contains($t, 'rehab')) $type = 'rehab';
                else $type = preg_replace('/\s+/', '_', $t);
            }
        }

        if (!$type) {
            return back()->withInput()->withErrors(['project_type' => 'Project type not determined. Select a project or choose a type.']);
        }

        // Type-specific validation rules
        $detailRules = [];
        if ($type === 'mosque') {
            $detailRules = [
                'detail.construction_stage' => 'nullable|string|max:255',
                'detail.estimated_cost' => 'nullable|numeric|min:0',
                'detail.land_area' => 'nullable|string|max:255',
                'detail.main_materials' => 'nullable|array',
                'detail.main_materials.*.name' => 'required_with:detail.main_materials|string',
                'detail.main_materials.*.qty' => 'required_with:detail.main_materials|numeric',
                'detail.architect_contact' => 'nullable|string|max:255',
            ];
        } elseif ($type === 'orphan') {
            // orphan expects detail.orphan as array of rows: detail[orphan][0][child_name]...
            $detailRules = [
                'detail.orphan' => 'nullable|array',
                'detail.orphan.*.child_name' => 'required|string|max:255',
                'detail.orphan.*.dob' => 'nullable|date',
                'detail.orphan.*.gender' => ['nullable', Rule::in(['Male','Female','Other'])],
                'detail.orphan.*.guardian_name' => 'nullable|string|max:255',
                'detail.orphan.*.guardian_contact' => 'nullable|string|max:50',
                'detail.orphan.*.education_status' => 'nullable|string|max:255',
                'detail.orphan.*.medical_needs' => 'nullable|string',
                'detail.orphan.*.monthly_support_required' => 'nullable|numeric|min:0',
                // file inputs are validated per-file below
            ];
        } elseif ($type === 'scholarship') {
            $detailRules = [
                'detail.scholarship' => 'nullable|array',
                'detail.scholarship.*.student_name' => 'required|string|max:255',
                'detail.scholarship.*.university' => 'nullable|string|max:255',
                'detail.scholarship.*.program' => 'nullable|string|max:255',
                'detail.scholarship.*.year' => 'nullable|integer',
                'detail.scholarship.*.gpa' => 'nullable|numeric|min:0|max:4.00',
                'detail.scholarship.*.requested_amount' => 'nullable|numeric|min:0',
            ];
        } elseif ($type === 'rehab') {
            $detailRules = [
                'detail.rehab' => 'nullable|array',
                'detail.rehab.*.family_head_name' => 'required|string|max:255',
                'detail.rehab.*.members_count' => 'nullable|integer|min:0',
                'detail.rehab.*.vulnerabilities' => 'nullable|string',
                'detail.rehab.*.monthly_expenses' => 'nullable|numeric|min:0',
                'detail.rehab.*.preferred_assistance' => 'nullable|string',
            ];
        }

        $request->validate($detailRules);

        // file validation for orphan images: detail[orphan][i][image]
        if ($type === 'orphan' && $request->hasFile('detail')) {
            $filesTree = $request->file('detail');
            // $filesTree may be nested: ['orphan' => [0 => ['image' => UploadedFile], ...]]
            if (is_array($filesTree) && isset($filesTree['orphan']) && is_array($filesTree['orphan'])) {
                foreach ($filesTree['orphan'] as $idx => $fileGroup) {
                    if (isset($fileGroup['image']) && $fileGroup['image']) {
                        $request->validate([
                            "detail.orphan.{$idx}.image" => 'image|mimes:jpg,jpeg,png|max:4096'
                        ]);
                    }
                }
            }
        }

        // create or update inside transaction
        DB::beginTransaction();
        try {
            $project = Project::findOrFail($projectId);

            // Remove existing detail rows for multi-row types on update
            if ($projectId && in_array($type, ['orphan','scholarship','rehab'])) {
                if ($type === 'orphan') OrphanDetail::where('project_id', $project->id)->delete();
                if ($type === 'scholarship') ScholarshipApplicant::where('project_id', $project->id)->delete();
                if ($type === 'rehab') RehabFamily::where('project_id', $project->id)->delete();
            }

            // Save type-specific details
            if ($type === 'mosque') {
                $detail = $request->input('detail', []);
                $mainMaterials = $detail['main_materials'] ?? null;
                MosqueDetail::updateOrCreate(
                    ['project_id' => $project->id],
                    [
                        'construction_stage' => $detail['construction_stage'] ?? null,
                        'estimated_cost' => $detail['estimated_cost'] ?? null,
                        'land_area' => $detail['land_area'] ?? null,
                        'main_materials' => $mainMaterials,
                        'architect_contact' => $detail['architect_contact'] ?? null,
                    ]
                );
            } elseif ($type === 'orphan') {
                $rows = $request->input('detail.orphan', []);
                // files under request()->file('detail')['orphan'][index]['image']
                $filesTree = $request->file('detail') ?? [];
                foreach ($rows as $i => $row) {
                    $imagePath = null;
                    if (isset($filesTree['orphan'][$i]['image']) && $filesTree['orphan'][$i]['image']->isValid()) {
                        $file = $filesTree['orphan'][$i]['image'];
                        $storePath = $file->store("public/projects/{$project->id}/orphans", 'local');
                        // store returns path like 'public/...'; convert to storage path
                        $imagePath = Storage::url(str_replace('public/', '', $storePath));
                    }
                    OrphanDetail::create([
                        'project_id' => $project->id,
                        'child_name' => $row['child_name'] ?? null,
                        'dob' => $row['dob'] ?? null,
                        'gender' => $row['gender'] ?? null,
                        'guardian_name' => $row['guardian_name'] ?? null,
                        'guardian_contact' => $row['guardian_contact'] ?? null,
                        'medical_needs' => $row['medical_needs'] ?? null,
                        'education_status' => $row['education_status'] ?? null,
                        'monthly_support_required' => $row['monthly_support_required'] ?? null,
                        'image_path' => $imagePath,
                    ]);
                }
            } elseif ($type === 'scholarship') {
                $rows = $request->input('detail.scholarship', []);
                foreach ($rows as $row) {
                    ScholarshipApplicant::create([
                        'project_id' => $project->id,
                        'student_name' => $row['student_name'] ?? null,
                        'university' => $row['university'] ?? null,
                        'program' => $row['program'] ?? null,
                        'year' => $row['year'] ?? null,
                        'gpa' => $row['gpa'] ?? null,
                        'documents' => isset($row['documents']) ? array_map('trim', explode(',', $row['documents'])) : null,
                        'requested_amount' => $row['requested_amount'] ?? null,
                    ]);
                }
            } elseif ($type === 'rehab') {
                $rows = $request->input('detail.rehab', []);
                foreach ($rows as $row) {
                    RehabFamily::create([
                        'project_id' => $project->id,
                        'family_head_name' => $row['family_head_name'] ?? null,
                        'members_count' => $row['members_count'] ?? null,
                        'vulnerabilities' => isset($row['vulnerabilities']) ? array_map('trim', explode(',', $row['vulnerabilities'])) : null,
                        'monthly_expenses' => $row['monthly_expenses'] ?? null,
                        'preferred_assistance' => isset($row['preferred_assistance']) ? array_map('trim', explode(',', $row['preferred_assistance'])) : null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', $projectId ? 'Project updated.' : 'Project created.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return redirect()->back()->withInput()->withErrors(['error' => 'Unable to save project: ' . $e->getMessage()]);
        }
     
    }

  
}
