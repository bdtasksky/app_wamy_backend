<?php

namespace Modules\Accounts\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Accounts\Http\DataTables\SubtypeDataTable;

class SubTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:read_subtype'])->only('index');
        $this->middleware(['permission:create_subtype'])->only(['create', 'store']);
        $this->middleware(['permission:update_subtype'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_subtype'])->only('destroy');
    }
    public function index(SubtypeDataTable $datatable)
    {
        $subtypes = DB::table('acc_subtype')->get();
        return $datatable->render('accounts::subtype.index', compact('subtypes'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
    
        DB::table('acc_subtype')->insert([
            'name' => $request->input('name'),
            'isSystem'=> $request->input('isSystem')??0,
        ]);
    
        Toastr::success('Subtype Added Successfully :)','Success');
        return redirect()->route('subtypes.index');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);
    
        DB::table('acc_subtype')
            ->where('id', $id)
            ->update([
                'name' => $request->input('name'),
                'isSystem'=> $request->input('isSystem')??0,
            ]);
    
        Toastr::success('Subtype Updated Successfully :)','Success');
        return redirect()->route('subtypes.index');
    }
    
    // Modal data show
    public function edit($id)
    {
        $type = DB::table('acc_subtype')->where('id', $id)->first();
    
        if (!$type) {
            abort(404);
        }
    
        return response()->view('accounts::subtype.modal.edit', compact('type'));
    }
    

    public function destroy($id)
    {
        DB::table('acc_subtype')->where('id', $id)->delete();
    
        Toastr::success('Subtype Deleted Successfully :)','Success');
        return response()->json(['success' => 'success']);
    }
}
