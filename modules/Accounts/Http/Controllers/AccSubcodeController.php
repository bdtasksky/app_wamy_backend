<?php

namespace Modules\Accounts\Http\Controllers;

// use App\Traits\ChecksPermission;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Accounts\Http\DataTables\SubAccountDataTable;

class AccSubcodeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:read_sub_account'])->only('index');
        $this->middleware(['permission:create_sub_account'])->only(['create', 'store']);
        $this->middleware(['permission:edit_sub_account'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_sub_account'])->only('destroy');
    }
    public function index(SubAccountDataTable $datatable) {
        $subtypes = DB::table('acc_subtype')->where('isSystem',0)->get();
        $subcodes = DB::table('acc_subcode')->paginate(10);
        return $datatable->render('accounts::subcode.index', compact('subtypes', 'subcodes'));
    }
    
    public function store(Request $request) {
        $request->validate([
            'name'           => 'required|unique:acc_subcode|max:255',
            'acc_subtype_id' => 'required',
        ]);
    
        DB::table('acc_subcode')->insert([
            'name' => $request->input('name'),
            'subTypeID' => $request->input('acc_subtype_id'),
        ]);
    
        Toastr::success('Sub Account Added Successfully :)', 'Success');
        return redirect()->route('subcodes.index');
    }
    
    public function update(Request $request, $id) {
        
        $subcode = DB::table('acc_subcode')->where('id', $id)->first();
        if (!$subcode) {
            abort(404); 
        }
        $request->validate([
            'name'           => 'required|unique:acc_subcode,name,' . $id,
            'subTypeID' => 'required',
        ]);
    
        DB::table('acc_subcode')
            ->where('id', $id)
            ->update([
                'name' => $request->input('name'),
                'subTypeID' => $request->input('acc_subtype_id'),
            ]);
    
        Toastr::success('Sub Account Updated Successfully :)', 'Success');
        return redirect()->route('subcodes.index');
    }
    
    public function edit($id) {

        $subcode = DB::table('acc_subcode')->where('id', $id)->first();
        if (!$subcode) {
            abort(404);
        }
    
        $subtypes = DB::table('acc_subtype')->get();
    
        return response()->view('accounts::subcode.modal.edit', [
            'code' => $subcode,
            'subtypes' => $subtypes,
        ]);
    }
    
    public function destroy($id)
    {
        // Fetch subcode information, including subTypeID
        $subcode = DB::table('acc_subcode')->where('id', $id)->first();
        if (!$subcode) {
            return response()->json(['error' => 'Subcode not found.'], 404);
        }

        $subTypeID = $subcode->subTypeID;

        // Check if the subcode or its subtype is referenced in acc_voucher_details or acc_transactions
        $total = DB::table('acc_voucher_details')
            ->where('subcode_id', $id)
            ->orWhere('subtype_id', $subTypeID)
            ->count();

        $total += DB::table('acc_transactions')
            ->where('subcode_id', $id)
            ->orWhere('subtype_id', $subTypeID)
            ->count();

        $total += DB::table('acc_openingbalance')
            ->where('acc_subcode_id', $id)
            ->orWhere('acc_subtype_id', $subTypeID)
            ->count();

        // If subcode exists in acc_voucher_details or acc_transactions, abort deletion
        if ($total > 0) {
            return response()->json([
                'error' => 'Cannot delete this subcode as it or its subtype is referenced in voucher details or transactions or opening balance.'
            ], 400);
        }
        
        DB::table('acc_subcode')->where('id', $id)->delete();
        return response()->json(['success' => 'Sub Account Deleted Successfully']);
        
    }
}
