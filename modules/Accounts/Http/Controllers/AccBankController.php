<?php

namespace Modules\Accounts\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AccBankController extends Controller
{
    public function bank_ledger_report(){
		//DB::table('acc_coas')->where('head_level', 4)->whereNull('deleted_at')->orderBy('account_name', 'asc')->get();
        $data['general_ledger'] =DB::table('acc_coas')->where('head_level', 4)->where('is_bank_nature', 1)->where('is_active', 1)->whereNull('deleted_at')->orderBy('account_name', 'asc')->get();
		$data['financialyears']= DB::table('acc_financialyear')->where('is_active', 1)->first();
		$data['financial_years'] = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();
		$data['title'] = __('language.general_ledger');
		return view('accounts::bank.bank_ledger_report', $data);
	}
    public function bank_ledger_report_search(Request $request)
	{
		// Get the input data from the request
		$cmbCode = $request->input('cmbCode');
		$dtpFromDate = $request->input('dtpFromDate');
		$dtpToDate = $request->input('dtpToDate');

		$row = $request->input('row');
		$page_n = $request->input('page');
		$branch_id = 0;

		$account_name = DB::table('acc_coas')->where('id', $cmbCode)->whereNull('deleted_at')->value('account_name');

		$query = DB::select("CALL GetLedgerPaging(?, ?, ?, ?, ?, ?, @op_total_row)", [
			$branch_id, $cmbCode, $dtpFromDate, $dtpToDate, $row, $page_n
		]);
		$result = $query;

		$totalRow = DB::selectOne("SELECT @op_total_row AS total_row")->total_row;

		$page_no = ceil($totalRow / $row);

		// Get additional data
		$general_ledger = DB::table('acc_coas')->where('head_level', 4)->where('is_bank_nature', 1)->where('is_active', 1)->whereNull('deleted_at')->orderBy('account_name', 'asc')->get();
		$vouchartypes = DB::table('acc_vouchartype')->get()->toArray();
		$HeadName = DB::table('acc_coas')->where('id', $cmbCode)->whereNull('deleted_at')->get();
		$setting = app_setting();
		// $currencyinfo = DB::table('currency')->where('currencyid', $setting->currency)->first();
		$financial_years = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();


		// Prepare the data array for the view
		$data = [
			'account_name' => $account_name,
			'ledger_data' => $result,
			'totalRow' => $totalRow,
			'row' => $row,
			'page_no' => $page_no,
			'page_n' => $page_n,
			'general_ledger' => $general_ledger,
			'vouchartypes' => $vouchartypes,
			'ledger' => $HeadName,
			'cmbCode' => $cmbCode,
			'dtpFromDate' => $dtpFromDate,
			'dtpToDate' => $dtpToDate,
			'dtpYear' => $request->input('dtpYear'),
			'financial_years' => $financial_years,
			'setting' => $setting,
			'branch_id' => 0,
			'branch_name' => '',
			'title' => __('language.general_ledger_report'),  // Assuming you're using language files for titles
			'module' => 'accounts',
		];
		// dd($data);
		
		// Return the view with the data
		return view('accounts::bank.bank_ledger_report_search', $data);
	}
}