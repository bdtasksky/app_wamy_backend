<?php

namespace Modules\Accounts\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AccPredefinedController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:read_predefine_accounts'])->only(['predefined_accounts', 'getPredefinedSettingList']);
        $this->middleware(['permission:create_predefine_accounts'])->only(['predefined_form', 'predefined_save']);
        $this->middleware(['permission:update_predefine_accounts'])->only(['predefined_edit', 'predefined_update']);

    }
    public function predefined_accounts()
    {
        $data['title']  = __('language.predefined_accounts');
        $data['moduleTitle'] = 'Accounts';
        $data['module'] = "accounts";
        $data['page']   = "predefined_accounts/list"; 

        return view('accounts::predefined_accounts/list', $data);
    }


    public function predefined_form() 
	{ 
		$data['title'] = __('language.create_predefined');  
        $data['predefineCode'] = DB::table('acc_predefined')
        ->where('is_active', 1)
        ->whereNotIn('id', function ($query) {
            $query->select('predefined_id')
                  ->from('acc_predefined_seeting')
                  ->where('is_active', 1);
        })
        ->get();
                                    

        $data['allheads'] = DB::table('acc_coas')->where('head_level', 4)->where('is_active', 1)->whereNull('deleted_at')->get();
		$data['module'] = "accounts";
        $data['page'] = "predefined_accounts/form";   

        return view('accounts::predefined_accounts/form', $data);
    }

    public function predefined_edit($id) 
	{ 
		$data['title']      = __('language.edit_predefined');  
        $data['allheads']   = DB::table('acc_coas')->where('head_level', 4)->where('is_active', 1)->whereNull('deleted_at')->get();
        $data['predefineSettings'] = DB::table('acc_predefined_seeting')->where('id', $id)->where('is_active', 1)->first();
        
        $query = DB::table('acc_predefined')
            ->select('*')
            ->where('is_active', 1);
        $current_predefined_id= $data['predefineSettings']->predefined_id;
        if ($current_predefined_id) {
            // Exclude used `predefined_id` except the one being edited
            $query->whereRaw(
                '(id NOT IN (SELECT predefined_id FROM acc_predefined_seeting) OR id = ?)',
                [$current_predefined_id]
            );
        } else {
            // For new entry, exclude all used `predefined_id`
            $query->whereRaw(
                'id NOT IN (SELECT predefined_id FROM acc_predefined_seeting)'
            );
        }

        $data['predefineCode'] = $query->get();
                            
		$data['module'] = "accounts";
   
        return view('accounts::predefined_accounts/edit', $data);
	}

    public function predefined_save(Request $request)
    {
        // Set validation rules
        $validator = Validator::make($request->all(), [
            'predefined_seeting_name' => 'required',
            'is_active' => 'required',
            'predefined_id' => 'required',
            'acc_coa_id' => 'required',
        ]);

        // Check if validation failed
        if ($validator->fails()) {
            // Validation failed, redirect back with errors
            return redirect()->route('accounts.predefined_form') // Modify route name as needed
                            ->withErrors($validator)
                            ->withInput();
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Collect form data
            $data = [
                'predefined_seeting_name' => $request->input('predefined_seeting_name'),
                'predefined_seeting_description' => $request->input('predefined_seeting_description'),
                'is_active' => $request->input('is_active'),
                'predefined_id' => $request->input('predefined_id'),
                'acc_coa_id' => $request->input('acc_coa_id'),
                'created_by' => auth()->user()->id,
                'created_date' => now(),
            ];

            $inserted = DB::table('acc_predefined_seeting')->insert($data);

            DB::commit();

            session()->flash('success', 'Data saved successfully');

            return redirect()->route('accounts.predefined_accounts');

        } catch (\Exception $e) {
            
            DB::rollBack();

            Log::error("Error in predefined_save: " . $e->getMessage());

            session()->flash('error', 'Failed to save data. Please try again.');

            return redirect()->route('accounts.predefined.form')->withInput();
        }
    }




    public function predefined_update(Request $request, $id)
    {
        $validated = $request->validate([
            'predefined_seeting_name' => 'required',
            'is_active' => 'required',
            'predefined_id' => 'required',
            'acc_coa_id' => 'required',
        ]);
    
        $updated_by = Auth::id();
        $updated_at = now();
    

        DB::beginTransaction();
    
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $newData = [
                'predefined_seeting_name' => $request->input('predefined_seeting_name'),
                'predefined_seeting_description' => $request->input('predefined_seeting_description'),
                'is_active' => $request->input('is_active'),
                'predefined_id' => $request->input('predefined_id'),
                'acc_coa_id' => $request->input('acc_coa_id'),
                'created_by' => $updated_by,
                'created_date' => $updated_at
            ];
    
            $inserted = DB::table('acc_predefined_seeting')->insert($newData);
            if ($inserted) {
                $updateData = [
                    'is_active' => 0,
                    'updated_by' => $updated_by,
                    'updated_date' => $updated_at
                ];
        
                DB::table('acc_predefined_seeting')->where('id', $id)->update($updateData);
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                DB::commit();
                return redirect()->route('accounts.predefined.accounts')->with('success', 'Data updated successfully');
            } else {

                DB::rollBack();
                return redirect()->route('accounts.predefined.accounts')->with('error', 'Failed to update data');
            }
        } catch (\Exception $e) {
           
            DB::rollBack();
            return redirect()->route('accounts.predefined.accounts')->with('fail', 'An error occurred: ' . $e->getMessage());
        }
    }




    public function getPredefinedSettingList(Request $request)
    {
        // Get the start, draw, and other necessary parameters from the request
        $start = $request->input('start', 0);
        $draw = $request->input('draw');
        $length = $request->input('length', 10); // Limit of records per page

        // Fetch predefined settings with pagination, filters, and sorting
        $column_order = ['ps.predefined_seeting_name', 'p.predefined_name', 'c.account_name']; // column fields for order
        $column_search = ['ps.predefined_seeting_name', 'p.predefined_name', 'c.account_name']; // column fields for search
        $order = ['ps.id' => 'desc']; // Default order by

        // Start the base query
        $query = DB::table('acc_predefined_seeting as ps')
            ->select('ps.*', 'p.predefined_name', 'p.id as pre_id', 'c.account_name')
            ->join('acc_predefined as p', 'p.id', '=', 'ps.predefined_id')
            ->join('acc_coas as c', 'c.id', '=', 'ps.acc_coa_id')
            ->where('ps.is_active', 1);

        // Handle searching logic
        if ($request->has('search') && $request->input('search')['value']) {
            $searchValue = $request->input('search')['value'];
            foreach ($column_search as $index => $item) {
                $query->orWhere($item, 'like', '%' . $searchValue . '%');
            }
        }

        // Apply filtering based on active status
        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('ps.is_active', $request->input('is_active'));
        }

        // Get the total number of records without applying limit
        $totalRecords = DB::table('acc_predefined_seeting')->count();

        // Apply sorting
        if ($request->has('order')) {
            $columnIndex = $request->input('order')[0]['column'];
            $orderDirection = $request->input('order')[0]['dir'];
            $query->orderBy($column_order[$columnIndex], $orderDirection);
        } else {
            // Default ordering
            $query->orderBy(key($order), $order[key($order)]);
        }
        if ($length != -1) {
            $query->offset($start)->limit($length);
        }
        // Apply pagination
        $list = $query->get();

     
        $filteredRecords = $query->count();

        // Prepare data
        $data = [];
        $sl = $start;
        foreach ($list as $key => $rowdata) {
            $sl++;

            $view = '<a href="' . route('accounts.predefined.edit', $rowdata->id) . '" class="btn btn-xs btn-success" style="margin-right:10px" title="Edit Voucher"><i class="fa fa-pencil"></i></a>';

            $row = [
                $sl,
                $rowdata->pre_id,  
                $rowdata->predefined_seeting_name,
                $rowdata->predefined_seeting_description,
                $rowdata->predefined_name, // Directly use 'predefined_name'
                $rowdata->account_name,   // Directly use 'account_name'      // Directly use 'fullname'
                $rowdata->created_date,
                $rowdata->is_active == 1 ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>',
                $view
            ];

            $data[] = $row;
        }

        // Return JSON response for DataTables
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }



}
