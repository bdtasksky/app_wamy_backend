<?php

namespace Modules\Project\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Project\Entities\Zone;
use Modules\Project\Entities\Project;
use Modules\Menu\Entities\MenuContent;
use Illuminate\Support\Facades\Storage;
use Modules\Localize\Entities\Language;
use Illuminate\Contracts\Support\Renderable;
use Modules\Project\DataTables\ProjectDataTable;

class ProjectController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:read_project')->only('index');
    //     $this->middleware('permission:create_project')->only(['create', 'store']);
    //     $this->middleware('permission:update_project')->only(['edit', 'update']);
    //     $this->middleware('permission:delete_project')->only('destroy');

    //     $this->middleware('demo')->only(['saveProjectImgStatus','update', 'destroy']);
    // }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(ProjectDataTable $dataTable)
    {
        return $dataTable->render('project::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $projects = Project::all();
        $languages = Language::all();
        $zones= Zone::all();
        
        return view('project::create', compact('projects', 'zones','languages'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'project_type' => 'required',
            'status' => 'required',
            'location'=>'required',
            'target_amount' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'description' => 'nullable|string',
        ]);


        try {

            Project::create([
                'name' => $request->name,
                'project_type' => $request->project_type,
                'status' => $request->status,
                'location' => $request->location,
                'target_amount' => $request->target_amount,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description,
            ]);

            return response()->json(['error' => false, 'msg' => localize('data_saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'msg' => 'Failed to save data: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('project::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Project $project)
    {
        $project = Project::findOrFail($project->id);
        $languages = Language::all();
        $zones = Zone::all();

        return view('project::edit', compact('project','zones', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {

        $project = Project::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'project_type' => 'required',
            'status' => 'required',
            'location'=>'required',
            'target_amount' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'description' => 'nullable|string',
        ]);



        try {

            $project_up = $project->update([
                'name'    => $request->name,
                'project_type'    => $request->project_type,
                'status'=>$request->status,
                'location'=>$request->location,
                'target_amount' => $request->target_amount,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description,
            ]);

            // If the creation was successful, redirect with success message
            return response()->json(['error' => false, 'msg' => localize('data_updated_successfully')]);
        } catch (\Exception $e) {
            // If an exception occurs (e.g., validation error, database error), handle it here
            // You can customize the error message based on the type of exception
            return response()->json(['error' => true, 'msg' => 'Failed to update data: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Project $project)
    {
        Project::where('id', $project->id)->delete();

        return response()->json(['success' => 'success']);
    }

}
