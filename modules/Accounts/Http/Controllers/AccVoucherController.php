<?php

namespace Modules\Accounts\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Project\Entities\Project;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AccVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:read_voucher'])->only(['voucher_list', 'getVoucherList', 'voucherDetails']);
        $this->middleware(['permission:create_voucher'])->only(['voucher_form', 'voucher_save']);
        $this->middleware(['permission:update_voucher'])->only(['voucher_edit', 'voucher_save']);
        $this->middleware(['permission:delete_voucher'])->only(['deleteVoucher', 'reverseVoucher']);
    }
    public function voucher_list() 
    {
        
        $voucher_types = DB::table('acc_vouchartype')->get();
        $activeFinancialYear = DB::table('acc_financialyear')->where('is_active', 1)->first();
        $financialYears = DB::table('acc_financialyear')->whereNotIn('is_active', [0])->get();
        $projects = Project::all();
        $expenses = DB::table('acc_coas')->where('acc_type_id', 2)->where('head_level', 4)->where('is_active', true)->whereNull('deleted_at')->get();
        return view('accounts::voucher.list', compact('voucher_types', 'activeFinancialYear', 'financialYears', 'projects','expenses'));
    }
    public function deferred_voucher_list() 
    {
        
        $voucher_types = DB::table('acc_vouchartype')->get();
        $activeFinancialYear = DB::table('acc_financialyear')->where('is_active', 1)->first();
        $financialYears = DB::table('acc_financialyear')->whereNotIn('is_active', [0])->get();
        $projects = Project::all();
        $expenses = DB::table('acc_coas')->where('acc_type_id', 2)->where('head_level', 4)->where('is_active', true)->whereNull('deleted_at')->get();
        return view('accounts::voucher.deferred_list', compact('voucher_types', 'activeFinancialYear', 'financialYears', 'projects','expenses'));
    }
    public function deferred_voucher_report() 
    {
         
        $voucher_types = DB::table('acc_vouchartype')->get();
        $activeFinancialYear = DB::table('acc_financialyear')->where('is_active', 1)->first();
        $financialYears = DB::table('acc_financialyear')->whereNotIn('is_active', [0])->get();
        $projects = Project::all();
        return view('accounts::voucher.deferred_report', compact('voucher_types', 'activeFinancialYear', 'financialYears', 'projects'));
    }
    public function deferredSchedule() 
    {
         
        $voucher_types = DB::table('acc_vouchartype')->get();
        $activeFinancialYear = DB::table('acc_financialyear')->where('is_active', 1)->first();
        $financialYears = DB::table('acc_financialyear')->whereNotIn('is_active', [0])->get();
        $projects = Project::all();
        return view('accounts::voucher.deferred_schedule', compact('voucher_types', 'activeFinancialYear', 'financialYears', 'projects'));
    }

	public function voucher_form() 
    {
        $financialYears = DB::table('acc_financialyear')->where('is_active', 1)->first();
        $voucherTypes = DB::table('acc_vouchartype')->get();
        $accounts = DB::table('acc_coas') ->where('head_level', 4)->where('is_active', true)->whereNull('deleted_at')->get();
        $projects = Project::all();
        return view('accounts::voucher.form', compact('voucherTypes', 'accounts','financialYears', 'projects'));
    }

	public function voucher_edit($id) 
    {
        $financialYears = DB::table('acc_financialyear')->where('is_active', 1)->first();
        $voucherMaster = DB::table('acc_voucher_master')->where('id', $id)->first();
        $voucherDetails = DB::table('acc_voucher_details as vd')
        ->leftJoin('acc_subcode as sc', 'vd.subcode_id', '=', 'sc.id')
        ->leftJoin('acc_coas as ac', 'vd.acc_coa_id', '=', 'ac.id')
        ->where('vd.voucher_master_id', $id)
        ->select('vd.*', 'sc.name', 'ac.account_name')
        ->get();
        $voucherTypes = DB::table('acc_vouchartype')->get();
        $accounts = DB::table('acc_coas') ->where('head_level', 4)->where('is_active', true)->whereNull('deleted_at')->get();
        $accVoucherAttachment=DB::table('acc_voucher_attachments')->where('acc_voucher_id', $id)->get();
        $projects = Project::all();
        return view('accounts::voucher.edit', compact('voucherMaster', 'voucherDetails', 'voucherTypes', 'accounts', 'financialYears','accVoucherAttachment', 'projects'));
    }

	public function voucher_save(Request $request)
    {
        $validated = $request->validate([
            'voucher_type' => 'required',
            'date' => 'required|date',
            'debits' => 'required|array',
            'debits.*.coa_id' => 'required|integer',
            'debits.*.debit' => 'nullable|numeric',
            'debits.*.credit' => 'nullable|numeric',
        ]);
    
        // DB::beginTransaction();
        try {
            $VoucherId = $request->input('id', 0);
            $VoucherNo = $request->input('voucher_no', '');
            $voucher_type = $request->input('voucher_type');
            $date = $request->input('date');
            $remarks = $request->input('remarks');
            $debits = $request->input('debits');
            $project_id = $request->input('project_id');
            $schedule_status = $request->input('schedule_status');
            $branch_id = 0;
            $attachments = $request->file('attachment');
            $attachment_name = $request->attachment_name;
            $exists_files = $request->existing_attachment_ids ?? [];
            $exists_files_name = $request->existing_attachment_names ?? [];
    
            $exist = DB::table('acc_voucher_master')->where('id', $VoucherId)->first();
            $createdBy = auth()->user()->id;
    
            // Prepare attachment data outside the loop
            $attachment_string = [];
            $attachment_name_string = [];
    
            // Existing attachments
            if ($exists_files) {
                foreach ($exists_files as $index => $attachmentId) {
                    $attachment = DB::table('acc_voucher_attachments')->where('id', $attachmentId)->first();
                    if ($attachment) {
                        $attachment_name_string[] = $exists_files_name[$index];
                        $attachment_string[] = $attachment->file_name;
                    }
                }
            }
    
            // New uploads
            if (!empty($attachments)) {
                foreach ($attachments as $k => $file) {
                    if (!empty($file)) {
                        $filePath = uploadVoucherAttachmentDoSpaces('voucheratachment/' . get_company_db() . '/', $file);
                        $attachment_string[] = $filePath;
                        $attachment_name_string[] = $attachment_name[$k] ?? '';
                    }
                }
            }
    
            // Prepare voucher data
            $voucherData = collect($debits)->map(function ($debit) use ($voucher_type, $date, $remarks, $VoucherId, $branch_id, $createdBy, $attachment_string, $attachment_name_string,$project_id,$schedule_status) {
                $subtype = null;
                if (isset($debit['subcode_id'])) {
                    $subtype = DB::table('acc_subcode')->where('id', $debit['subcode_id'])->first();
                }
    
                return [
                    'VoucherId' => $VoucherId,
                    'VoucherNumber' => '',
                    'VoucherDate' => $date,
                    'Companyid' => 0,
                    'BranchId' => $branch_id,
                    'VoucherTypeId' => $voucher_type,
                    'VoucherEventCode' => 'ACC',
                    'VoucherRemarks' => $remarks ?? '',
                    'Createdby' => $createdBy,
                    'acc_coa_id' => $debit['coa_id'],
                    'DrAmount' => number_format($debit['debit'], 3, '.', ''),
                    'CrAmount' => number_format($debit['credit'], 3, '.', ''),
                    'subtype_id' => $subtype->subTypeID ?? '',
                    'subcode_id' => $debit['subcode_id'] ?? '',
                    'LaserComments' => $debit['ledger_comment'] ?? '',
                    'chequeno' => '',
                    'chequeDate' => '',
                    'document_name' => implode(',', $attachment_name_string),
                    'document_url' => implode(',', $attachment_string),
                    'project_id' => $project_id??'',
                    'ScheduleStatus' => $schedule_status??'',
                ];
            })->values();
    
            $jsonData = json_encode($voucherData);
            DB::select("CALL AccVoucherPosting(?, @voucherNumber, @massage)", [$jsonData]);
            $outputResult = DB::select("SELECT @voucherNumber AS VoucherNumber, @massage AS Message");
    
            if ($outputResult) {
                $voucherNumber = $outputResult[0]->VoucherNumber;
                $message = $outputResult[0]->Message;

                // DB::commit();

                return redirect()->route('accounts.voucher.list')->with('success', 'Voucher saved successfully. Voucher Number: ' . $voucherNumber);
            } else {
                // DB::rollBack();
                return redirect()->route('accounts.voucher.list')->with('error', 'Failed to save the voucher.');
            }
        } catch (\Exception $e) {
            // DB::rollBack();
            return redirect()->route('accounts.voucher.list')->with('error', $e->getMessage());
        }
    }
    public function saveDeferredSchedule(Request $request)
    {
  
        try {
            $validatedData = $request->validate([
                'voucher_master_id'      => 'required|integer',
                'expense_head'      => 'required|integer',
                'number_of_installments' => 'required|integer|min:1',
                'effective_date'         => 'required|date_format:Y-m-d',
                'remarks'                => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Invalid data provided.', 'errors' => $e->errors()], 422);
        }

        try {
            // 2. Prepare parameters for the stored procedure
            $voucherId      = $validatedData['voucher_master_id'];
            $expense_head      = $validatedData['expense_head'];
            $installments   = $validatedData['number_of_installments'];
            $effectiveDate  = $validatedData['effective_date'];
            $remarks        = $validatedData['remarks'] ?? ''; // Use empty string if remarks are null

            DB::statement(
                "CALL AccVoucherPosting_DeferredSchedule(?, ?, ?, ?, ?, @msg)",
                [$voucherId, $expense_head, $installments, $effectiveDate, $remarks]
            );

            $output = DB::select("SELECT @msg as response_message");

            $message = $output[0]->response_message ?? 'Procedure executed but no message was returned.';

            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {

            Log::error('Deferred Schedule Save Error: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'An unexpected error occurred while saving the schedule.'], 500);
        }
    }
    public function removeDeferredSchedule(Request $request)
    {
        $validated = $request->validate([
            'voucher_master_id' => 'required|integer',
        ]);

        try {
            $voucherId = $validated['voucher_master_id'];
            $affectedRows = DB::table('acc_voucher_master')
                ->where('id', $voucherId)
                ->update(['ScheduleStatus' => null]);

            if ($affectedRows > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'The deferred schedule has been removed successfully.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not find a matching schedule to remove.'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Remove Deferred Schedule Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.'
            ], 500);
        }
    }  
    

	public function deleteVoucher(Request $request)
    {
        // dd('ok');
        $vno = $request->input('vno');
        $voucherinfo = DB::table('acc_voucher_master')->where('id', $vno)->first();

        if ($voucherinfo) {
            DB::table('acc_voucher_master')->where('id', $vno)->delete();
        
            if (DB::table('acc_voucher_master')->where('id', $vno)->doesntExist()) {
                DB::table('acc_voucher_details')->where('voucher_master_id', $voucherinfo->id)->delete();
            }
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

	public function reverseVoucher(Request $request)
    {

        $vno = $request->input('vno');
        
        $voucherinfo = DB::table('acc_voucher_master')->where('id', $vno)->first();

        $transactioninfo = DB::table('acc_transactions')->where('voucher_master_id', $vno)->first();

        if ($transactioninfo) {
            DB::table('acc_transactions')->where('voucher_master_id', $vno)->delete();
        } else {
            DB::table('acc_transactions')->where('voucher_master_id', $voucherinfo->id)->delete();
        }

        // Get the updated by user and current timestamp
        $updatedBy = auth()->user()->id;  // Using Laravel's Session facade
        $updatedDate = now();  // Laravel helper for current date and time

        $uparray = [
            'IsApprove' => 0,
            'UpdatedBy' => $updatedBy,
            'UpdatedDate' => $updatedDate,
        ];

        // Update the 'acc_voucher_master' table
        $updatedRows = DB::table('acc_voucher_master')->where('id', $vno)->update($uparray);

       
            return response()->json(['success' => 'ok']);

    }

	public function voucherDetails(Request $request)
    {
        $vid = $request->input('vid');
        $vdate = $request->input('vdate');

        $result = DB::select("CALL GetVoucher(?, ?)", [$vid, $vdate]);

        $voucherHead = $result[0] ?? null;

        $settingsInfo = app_setting();
        $accVoucherAttachment=DB::table('acc_voucher_attachments')->where('acc_voucher_id', $vid)->get();

        return response()->json([
            'data' => view('accounts::voucher.show', compact('voucherHead', 'result', 'settingsInfo','accVoucherAttachment'))->render(),
            'pdf' => asset('assets/data/pdf/voucher_details_' . $vid . '.pdf')
        ]);
    }
    public function voucherDetailChildren(Request $request)
    {
        $vid = $request->input('vid');
        $vdate = $request->input('vdate');

        $result = DB::select("CALL GetVoucher(?, ?)", [$vid, $vdate]);

        $voucherHead = $result[0] ?? null;

        $settingsInfo = app_setting();
        $accVoucherAttachment=DB::table('acc_voucher_attachments')->where('acc_voucher_id', $vid)->get();
        $accVoucherChildren=DB::table('acc_voucher_master')->where('DeferredVoucher_id', $vid)->get();
        return response()->json([
            'data' => view('accounts::voucher.show_children', compact('voucherHead', 'result', 'settingsInfo','accVoucherAttachment','accVoucherChildren'))->render(),
            'pdf' => asset('assets/data/pdf/voucher_details_' . $vid . '.pdf')
        ]);
    }

    public function pdfDelete(Request $request)
    {
        $path = $request->input('file_path');
        $filePath = public_path($path);

        if (file_exists($filePath) && unlink($filePath)) {
            return response()->json(['status' => 'success', 'message' => 'File deleted successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to delete the file or file does not exist.']);
    }

    // add branch here
    public function getVoucherList(Request $request)
    {
        // dd($request->all());
        $voucherType = $request->input('voucher_type');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $fromDate = Carbon::parse($fromDate)->format('Y-m-d');
        $toDate = Carbon::parse($toDate)->format('Y-m-d');
        $row = $request->input('row');
        $page = $request->input('page');
        $branch = $request->input('branch'); 
        $voucher_no = $request->input('voucher_no');
        $project_id = $request->input('project_id');
        $schedule_status = $request->input('schedule_status');

        $results = DB::select("CALL GetVoucherListPaging(?, ?, ?, ?, ?, ?, ?,?,?, @op_total_row)", [
            $voucherType, $status, $fromDate, $toDate, $voucher_no,$project_id,$schedule_status,$row, $page
        ]);

        $totalRows = DB::select("SELECT @op_total_row AS total_rows")[0]->total_rows;
      
        return response()->json([
            'transactions' => $results,
            'total_rows' => $totalRows
        ]);
    }
    public function getDeferredVoucherList(Request $request)
    {
        // dd($request->all());
        $voucherType = $request->input('voucher_type');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $fromDate = Carbon::parse($fromDate)->format('Y-m-d');
        $toDate = Carbon::parse($toDate)->format('Y-m-d');
        $row = $request->input('row');
        $page = $request->input('page');
        $branch = $request->input('branch'); 
        $voucher_no = $request->input('voucher_no');
        $project_id = $request->input('project_id');
        $schedule_status = 'IsDeferred';

        $results = DB::select("CALL GetVoucherListPaging(?, ?, ?, ?, ?, ?, ?,?,?, @op_total_row)", [
            $voucherType, $status, $fromDate, $toDate, $voucher_no,$project_id,$schedule_status,$row, $page
        ]);

        $totalRows = DB::select("SELECT @op_total_row AS total_rows")[0]->total_rows;
      
        return response()->json([
            'transactions' => $results,
            'total_rows' => $totalRows
        ]);
    }
    public function getDeferredVoucherReport(Request $request)
    {
        // dd($request->all());
        $voucherType = $request->input('voucher_type');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $fromDate = Carbon::parse($fromDate)->format('Y-m-d');
        $toDate = Carbon::parse($toDate)->format('Y-m-d');
        $row = $request->input('row');
        $page = $request->input('page');
        // $branch = $request->input('branch'); 
        $voucher_no = $request->input('voucher_no');
        $project_id = $request->input('project_id');
        $schedule_status = 'DeferredSchedule';

        $results = DB::select("CALL GetVoucherListPaging(?, ?, ?, ?, ?, ?, ?,?,?, @op_total_row)", [
            $voucherType, $status, $fromDate, $toDate, $voucher_no, $project_id,$schedule_status,$row, $page
        ]);

        $totalRows = DB::select("SELECT @op_total_row AS total_rows")[0]->total_rows;
      
        return response()->json([
            'transactions' => $results,
            'total_rows' => $totalRows
        ]);
    }
    public function getDeferredSchedule(Request $request)
    {
        // dd($request->all());
        $voucherType = $request->input('voucher_type');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $fromDate = Carbon::parse($fromDate)->format('Y-m-d');
        $toDate = Carbon::parse($toDate)->format('Y-m-d');
        $row = $request->input('row');
        $page = $request->input('page');
        // $branch = $request->input('branch'); 
        $voucher_no = $request->input('voucher_no');
        $project_id = $request->input('project_id');
        $schedule_status = '';
        //dd($voucherType, $status, $fromDate, $toDate, $voucher_no, $project_id,$schedule_status,$row, $page);
        $results = DB::select("CALL GetDeferredScheduleVoucherListPaging(?, ?, ?, ?, ?, ?, ?,?,?, @op_total_row)", [
            $voucherType, $status, $fromDate, $toDate, $voucher_no??'', $project_id??-1,$schedule_status,$row, $page
        ]);

        $totalRows = DB::select("SELECT @op_total_row AS total_rows")[0]->total_rows;
      
        return response()->json([
            'transactions' => $results,
            'total_rows' => $totalRows
        ]);
    }
    //deleteVoucher
    public function deleteAttachment($id)
    {

        $accVoucherAttachment = DB::table('acc_voucher_attachments')->where('id', $id)->first();

        if ($accVoucherAttachment) {
            Storage::disk('public')->delete($accVoucherAttachment->file_name);
            DB::table('acc_voucher_attachments')->where('id', $id)->delete(); // Use Query Builder delete
        }

        return   json_encode([
            'success'   => true,
            'title'     =>'Attachment',
            'message'   =>'Deleted Successfully'
        ]);
    }
    public function getDefferedBalance($id)
    {
        DB::select("CALL GetDeferredVoucherExpanceAmount(?, @amount)", [$id]);
		$balance = DB::selectOne("SELECT @amount as amount")->amount;
        return response()->json([
                'success' => true,
                'balance' => $balance??0.00
            ]);
    }
}
