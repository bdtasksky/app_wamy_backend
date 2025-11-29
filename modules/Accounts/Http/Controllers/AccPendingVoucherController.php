<?php

namespace Modules\Accounts\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Project\Entities\Project;
use Illuminate\Contracts\Support\Renderable;

class AccPendingVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:read_voucher_approval'])->only(['voucher_list', 'getPendingVoucherList']);
        $this->middleware(['permission:create_voucher_approval'])->only('voucherApproved');
    }
    public function voucher_list() 
	{ 
        $data['title']      = __('language.vouchers');    
        $data['voucher_types']= DB::table('acc_vouchartype')->get();
        $data['activeFinancialYear']= DB::table('acc_financialyear')->where('is_active', 1)->first();
		$data['financialYears'] = DB::table('acc_financialyear')->where('is_active', '!=', 0)->get();
        $data['projects'] = Project::all();
        return view('accounts::pending-voucher.list_another', $data);
    }

    // add branch here
	public function getPendingVoucherList(Request $request)
    {
        $voucher_type = $request->input('voucher_type');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $row = $request->input('row');
        $page_n = $request->input('page');
        $branch = 0;
        $voucher_no = $request->input('voucher_no');
        $project_id = $request->input('project_id');
        $schedule_status = $request->input('schedule_status');

        $results = DB::select(
            "CALL GetVoucherListPaging(?, 0, ?, ?, ?, ?, ?,?,?, @op_total_row)",
            [$voucher_type, $from_date, $to_date, $voucher_no,$project_id,$schedule_status, $row, $page_n]
        );

        $total_rows = DB::select("SELECT @op_total_row AS total_rows")[0]->total_rows;

        return response()->json([
            'transactions' => $results,
            'total_rows' => $total_rows
        ]);
    }

	public function voucherApproved(Request $request)
    {
        // Retrieve voucher IDs from the request
        $voucher_ids = $request->input('voucher_ids');
        // Prepare the desired JSON format
        $json_array = array_map(function($id) {
            return ['VoucherId' => $id];
        }, $voucher_ids);

        // Convert to JSON format
        $json_data = json_encode($json_array);

        // Set SQL variables for the stored procedure
        DB::statement("SET @message = '';");

        // Call the stored procedure with the JSON data
        DB::statement("CALL AccVoucherApproveBulk(?, @message);", [$json_data]);

        // Retrieve the output message from the stored procedure
        $output_result = DB::select("SELECT @message AS Msg");

        // Check if we got a result from the stored procedure
        if ($output_result) {
            // Get output parameters
            $message = $output_result[0]->Msg;

            return response()->json([
                'status' => 'success',
                'message' => $message,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving output parameters.',
            ]);
        }
    }
}
