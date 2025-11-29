<?php

namespace Modules\Accounts\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;

class AccReportController extends Controller
{
	public function __construct()
    {
        $this->middleware(['permission:read_account_reports'])->only(['financial_report', 'sub_ledger_report', 'sub_ledger_merged_report', 'trial_balance_financial_report', 'profit_loss_report', 'balance_sheet_report', 'received_payment_report']);
		$this->middleware(['permission:create_account_reports'])->only(['generalLedgerReportSearch', 'subLedgerReportSearch', 'subLedgerMergedReportSearch', 'trialBalanceReportSearch', 'profitLossReportSearch', 'balanceSheetReportSearch', 'received_payment_report_search']);

    }
	// general ledger 
	public function financial_report(){
		$data['general_ledger'] = DB::table('acc_coas')->where('head_level', 4)->whereNull('deleted_at')->orderBy('account_name', 'asc')->get();
		$data['financialyears']= DB::table('acc_financialyear')->where('is_active', 1)->first();
		$data['financial_years'] = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();
		$data['title'] = __('language.general_ledger');
		return view('accounts::reports.financial_report', $data);
	}
    public function generalLedgerReportSearch(Request $request)
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
		$general_ledger = DB::table('acc_coas')->where('head_level', 4)->whereNull('deleted_at')->orderBy('account_name', 'asc')->get();
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
		return view('accounts::reports.general_ledger_report_after_search', $data);
	}

	// sub ledger
	public function sub_ledger_report(){
		$data['subtypes'] = DB::table('acc_subtype as a')->where('a.id', '!=', 1)->orderByDesc('a.id')->get();
		$data['financialyears']= DB::table('acc_financialyear')->where('is_active', 1)->first();
		$data['financial_years'] = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();
		$data['title'] = __('language.sub_ledger');
		$data['page']   = "reports/sub_ledger_report";
		return view('accounts::reports.sub_ledger_report', $data);
	}
	public function subLedgerReportSearch(Request $request)
    {
        // Get the input data from the request
        $subtype_id = $request->input('subtype_id');
        $acc_coa_id = $request->input('acc_coa_id');
        $acc_subcode_id = $request->input('acc_subcode_id', 0);  // Default to 0 if not set
        $dtpFromDate = $request->input('dtpFromDate');
        $dtpToDate = $request->input('dtpToDate');
        $row = $request->input('row');
        $page_n = $request->input('page');
        $branch_id = 0;

        // if acc_subcode_id is array, then convert to comma separated string
        $converted_subcode_id = is_array($acc_subcode_id)
            ? implode(',', $acc_subcode_id)
            : (string) $acc_subcode_id;


        // Fetch account name, subtype name, and subcode name
        $account_name = DB::table('acc_coas')->where('id', $acc_coa_id)->value('account_name');
        $subtype_name = DB::table('acc_subtype')->where('id', $subtype_id)->value('name');
        // if converted_subcode_id is greater than 1, then the subcode_name is comma separated string
        $subcode_name = is_array($acc_subcode_id)
            ? DB::table('acc_subcode')->whereIn('id', $acc_subcode_id)->pluck('name')->implode(', ')
            : DB::table('acc_subcode')->where('id', $acc_subcode_id)->value('name');


        // Set up data array for the view
        $data = [
            'account_name' => $account_name,
            'subtype_name' => $subtype_name,
            'subcode' => $subcode_name,
            'row' => $row,
            'page_n' => $page_n,
            'dtpFromDate' => $dtpFromDate,
            'dtpToDate' => $dtpToDate,
            'subtype_id' => $subtype_id,
            'acc_coa_id' => $acc_coa_id,
            'acc_subcode_id' => $converted_subcode_id,
            'branch_id' => 0,
            'branch_name' => '',
            'dtpYear' => $request->input('dtpYear'),
        ];

        // Call the stored procedure for Ledger SubCode Paging if converted_subcode_id is greater than 1
        if (count($acc_subcode_id) > 1) {
            $results = DB::select("CALL GetGroupLedgerSubCodeView(?, ?, ?, ?, ?)", [
                $branch_id,
                $acc_coa_id,
                $converted_subcode_id,
                $dtpFromDate,
                $dtpToDate,
            ]);
        } else {
            $results = DB::select("CALL GetLedgerSubCodePaging(?, ?, ?, ?, ?, ?, ?, @op_total_row)", [
                $branch_id,
                $acc_coa_id,
                $converted_subcode_id,
                $dtpFromDate,
                $dtpToDate,
                $row,
                $page_n
            ]);
        }

        // Retrieve the total row count from the stored procedure output variable
        $data['totalRow'] = $totalRow = DB::selectOne("SELECT @op_total_row AS total_row")->total_row;

        // Calculate page number
        $page_no = ceil($totalRow / $row);

        // Additional data
        $data['ledger_data'] = $results;
        $data['page_no'] = $page_no;

        // Fetch voucher types
        $vouchartypes = DB::table('acc_vouchartype')->get()->pluck('id', 'name')->toArray();
        $data['vouchartypes'] = $vouchartypes;

        // Get subtype information excluding ID 1, ordered by descending ID
        $data['subtypes'] = DB::table('acc_subtype as a')
            ->where('a.id', '!=', 1)
            ->orderByDesc('a.id')
            ->get();

        // Financial year data
        $data['financialyears'] = DB::table('acc_financialyear')
            ->where('is_active', 1)
            ->first();
        $data['financial_years'] = DB::table('acc_financialyear')
            ->whereIn('is_active', [1, 2])
            ->get();

        // Dropdowns for COA and subcode
        $data['accDropdown'] = DB::table('acc_coas')->where('head_level', 4)->where('is_active', 1)->where('subtype_id', $subtype_id)->where('is_subtype', 1)->get();
        $data['subcodeDropdown'] = DB::table('acc_subcode')->where('subTypeID', $subtype_id)->get();

        // Settings and currency information
        $setting = app_setting();
        // $currencyinfo = DB::table('currency')
        // 	->where('currencyid', $setting->currency)
        // 	->first();

        // Additional data for the view
        $data['setting'] = $setting;
        // $data['currency'] = $currencyinfo->curr_icon;
        $data['title'] = __('language.sub_ledger');  // Using a translation function for the title

        // Return the view with the data
        // if converted_subcode_id is greater than 1, then return the view with the data
        if (count($acc_subcode_id) > 1) {
            return view('accounts::reports.sub_ledger_report_search_all', $data);
        } else {
            return view('accounts::reports.sub_ledger_report_search', $data);
        }
    }

	// sub ledger merged
	public function sub_ledger_merged_report(){

		$data['subtypes'] = DB::table('acc_subtype as a')
		->where('a.id', '!=', 1)
		->orderByDesc('a.id')
		->get();

		$data['financialyears'] = DB::table('acc_financialyear')
			->where('is_active', 1)
			->first();

		$data['financial_years'] = DB::table('acc_financialyear')
			->whereIn('is_active', [1, 2])
			->get();


		$data['title'] = __('language.sub_ledger_merged');
		
		return view('accounts::reports.sub_ledger_merged_report', $data);		

	}
	public function subLedgerMergedReportSearch(Request $request)
	{
		$subtype_id = $request->input('subtype_id');
		$acc_subcode_id = $request->input('acc_subcode_id');
		$dtpFromDate = $request->input('dtpFromDate');
		$dtpToDate = $request->input('dtpToDate');
		$data['row'] = $row = $request->input('row');
		$data['page_n'] = $page_n = $request->input('page');
		$data['branch_id'] = $branch_id = 0;
		$data['branch_name'] = '';

		// Get the subtype name
		$data['subtype_name'] = DB::table('acc_subtype')
			->where('id', $subtype_id)
			->value('name');

		// Get the subcode name
		$data['subcode'] = DB::table('acc_subcode')
			->where('id', $acc_subcode_id)
			->value('name');

		$acc_subcode_id = $acc_subcode_id ? $acc_subcode_id : 0;

		// Calling the stored procedure for ledger data
		$query = DB::select("
			CALL GetLedgerBySubCodeMargePaging(1, ?, ?, ?, ?, ?, @op_total_row)
		", [$acc_subcode_id, $dtpFromDate, $dtpToDate, $row, $page_n]);



		$data['ledger_data'] = $query;


		$data['totalRow'] = $totalRow = DB::selectOne("SELECT @op_total_row AS total_row")->total_row;

		// Fetch voucher types
		$vouchartypes_res = DB::table('acc_vouchartype')->get()->toArray();

		$vouchartypes = [];
		foreach ($vouchartypes_res as $row) {
			$vouchartypes[$row->id] = $row;
		}
		$data['vouchartypes'] = $vouchartypes;

		// Fetch subtypes
		$data['subtypes'] = DB::table('acc_subtype as a')
			->where('a.id', '!=', 1)
			->orderByDesc('a.id')
			->get();

		// Fetch financial years
		$data['financialyears'] = DB::table('acc_financialyear')
			->where('is_active', 1)
			->first();

		// Fetch all financial years
		$data['financial_years'] = DB::table('acc_financialyear')
			->whereIn('is_active', [1, 2])
			->get();

		// Date filters
		$data['dtpFromDate'] = $dtpFromDate;
		$data['dtpToDate'] = $dtpToDate;
		$data['dtpYear'] = $request->input('dtpYear');
		$data['subtype_id'] = $subtype_id;
		$data['acc_subcode_id'] = $acc_subcode_id;

		// Fetch COA dropdown data
		$data['accDropdown'] = DB::table('acc_coas')
			->where('head_level', 4)
			->where('subtype_id', $subtype_id)
			->where('is_subtype', 1)
			->whereNull('deleted_at')
			->get();

		// Fetch Subcode dropdown data
		$data['subcodeDropdown'] = DB::table('acc_subcode')
			->where('subTypeID', $subtype_id)
			->get();

		// Fetch settings and currency info
		$setting = app_setting();
		// $currencyinfo = DB::table('currency')
		// 	->where('currencyid', $setting->currency)
		// 	->first();

		$data['setting'] = $setting;
		// $data['currency'] = $currencyinfo->curr_icon;

		// Return view with data
		return view('accounts::reports.sub_ledger_merged_report_search', $data);
	}




	public function getCoaFromSubtype($subtype) {

		$accDropdown = DB::table('acc_coas')
        ->where('head_level', 4)
        ->where('subtype_id', $subtype)
        ->where('is_subtype', 1)
		->whereNull('deleted_at')
        ->get(); // Changed to get() to return all results, not just first

		// Assuming you have some logic to get 'subcode' based on the subtype or some other parameter.
		$subcode = DB::table('acc_subcode')
			->where('subTypeID', $subtype)  // Adjust according to your schema
			->get();

		return response()->json([
			'coaDropDown' => $accDropdown,
			'subcode' => $subcode
		]);
    }
    public function getsubcode($subtypeid) {
        $subcodeDropdown = DB::table('acc_subcode')
		->where('subTypeID', $subtypeid)
		->get();

        echo json_encode([
            'subcode' => $subcodeDropdown
        ]);
    }


	public function generalLedgerReportByLink(Request $request)
	{
		// Get the input data from the request
		$cmbCode = $request->input('cmbCode');
		$dtpFromDate = $request->input('dtpFromDate');
		$dtpToDate = $request->input('dtpToDate');

		$row = $request->input('row')??10;
		$page_n = $request->input('page')??1;
		$branch_id = 0;

		$account_name = DB::table('acc_coas')->where('id', $cmbCode)->whereNull('deleted_at')->value('account_name');

		$query = DB::select("CALL GetLedgerPaging(?, ?, ?, ?, ?, ?, @op_total_row)", [
			$branch_id, $cmbCode, $dtpFromDate, $dtpToDate, $row, $page_n
		]);
		$result = $query;

		$totalRow = DB::selectOne("SELECT @op_total_row AS total_row")->total_row;

		$page_no = ceil($totalRow / $row);

		// Get additional data
		$general_ledger = DB::table('acc_coas')->where('head_level', 4)->whereNull('deleted_at')->orderBy('account_name', 'asc')->get();
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
		
		// Return the view with the data
		return view('accounts::reports.general-ledger-report-by-link', $data);
	}
    // trial_balance_financial_report
	public function trial_balance_financial_report(Request $request){
		
		$data['title']  = __('language.trial_balance');
		$data['financialyears']= DB::table('acc_financialyear')->where('is_active', 1)->first();
		$data['financial_years'] = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();
		
		
		$data['software_info'] = '';
		$data['dtpFromDate'] = $dtpFromDate = $request->input('dtpFromDate')??$data['financialyears']->start_date;
		$data['dtpToDate'] = $dtpToDate = $request->input('dtpToDate')??$data['financialyears']->end_date;
		$data['dtpYear'] = $request->input('dtpYear')??$data['financialyears']->title;
	
	
		$data['branch_id'] = $branch_id =  0;
		$data['branch_name'] = '';


		$query = DB::select("CALL GetBalanceSheet(?, ?, ?)", [$branch_id, $dtpFromDate, $dtpToDate]);
		$result = $query;
		$uniqueNatureIds = [];
		$uniqueData = collect($result)->filter(function($item) use (&$uniqueNatureIds) {
			if (!in_array($item->nature_id, $uniqueNatureIds)) {
				$uniqueNatureIds[] = $item->nature_id;
				return true;
			}
			return false;
		});

		$sum = $uniqueData->map(function($item) {
			return [
				'nature_id' => $item->nature_id,
				'nature_name' => $item->nature_name,
				'total_amount' => $item->nature_amount_debit + $item->nature_amount_credit
			];
		});
		$data['sum'] = $sum;


		// Fetching the trial balance data
		$query = DB::select("CALL GetTrilBalance(?, ?, ?)", [$branch_id, $dtpFromDate, $dtpToDate]);
		$result = $query;
		$trial_balance_data = [];
		foreach ($result as $value) {
			if (isset($trial_balance_data[$value->nacc_name])) {
				$trial_balance_data[$value->nacc_name][] = $value;
			} else {
				$trial_balance_data[$value->nacc_name][] = $value;
			}
		}
		$data['trial_balance_data'] = $trial_balance_data;
		$data['dtpFromDate'] = $dtpFromDate;
		$data['dtpToDate'] = $dtpToDate;
		// Fetching the setting and currency info
		$setting = app_setting();
		$data['financial_years'] = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();
		$data['setting'] = $setting;

		return view("accounts::reports.trial_balance_financial_report", $data);
	}

	
	public function trialBalanceReportSearch(Request $request)
	{

		$data = [];
		$data['software_info'] = '';
		$data['dtpFromDate'] = $dtpFromDate = $request->input('dtpFromDate');
		$data['dtpToDate'] = $dtpToDate = $request->input('dtpToDate');
		$data['dtpYear'] = $request->input('dtpYear');
	
	
		$data['branch_id'] = $branch_id =  0;
		$data['branch_name'] = '';


		$withDetails = $request->input('withDetails');
		$data['withDetails'] = $withDetails;

		

		if ($withDetails == 1){

			$query = DB::select("CALL GetBalanceSheet(?, ?, ?)", [$branch_id, $dtpFromDate, $dtpToDate]);
			$result = $query;

			$uniqueNatureIds = [];
			$uniqueData = collect($result)->filter(function($item) use (&$uniqueNatureIds) {
				if (!in_array($item->nature_id, $uniqueNatureIds)) {
					$uniqueNatureIds[] = $item->nature_id;
					return true;
				}
				return false;
			});

			$sum = $uniqueData->map(function($item) {
				return [
					'nature_id' => $item->nature_id,
					'nature_name' => $item->nature_name,
					'total_amount' => $item->nature_amount_debit + $item->nature_amount_credit
				];
			});
			$data['sum'] = $sum;

		}elseif($withDetails == 2){

			$query = DB::select("CALL GetBalanceSheet(?, ?, ?)", [$branch_id, $dtpFromDate, $dtpToDate]);
			$result = $query;

			$uniqueNatureIds = [];
			$uniqueData = collect($result)->filter(function($item) use (&$uniqueNatureIds) {
				if (!in_array($item->nature_id, $uniqueNatureIds)) {
					$uniqueNatureIds[] = $item->nature_id;
					return true;
				}
				return false;
			});

			$sum = $uniqueData->map(function($item) {
				return [
					'nature_id' => $item->nature_id,
					'nature_name' => $item->nature_name,
					'total_amount' => $item->nature_amount_debit + $item->nature_amount_credit
				];
			});
			$data['sum'] = $sum;

		} else {

			$query = DB::select("CALL GetTrialFullBalance(?, ?, ?)", [$branch_id, $dtpFromDate, $dtpToDate]);
			$result = $query;

			$uniqueNatureIds = [];
			$uniqueData = collect($result)->filter(function($item) use (&$uniqueNatureIds) {
				if (!in_array($item->nature_id, $uniqueNatureIds)) {
					$uniqueNatureIds[] = $item->nature_id;
					return true;
				}
				return false;
			});

			$sum = $uniqueData->map(function($item) {
				return [
					'nature_id' => $item->nature_id,
					'nature_name' => $item->nature_name,
					'total_amount' => $item->nature_amount_debit + $item->nature_amount_credit
				];
			});
			$data['sum'] = $sum;
		}

		

		if ($withDetails == 1) {
			$query = DB::select("CALL GetTrialFullBalance(?, ?, ?)", [$branch_id, $dtpFromDate, $dtpToDate]);
			$result = $query;

			$trial_balance_data = [];
			
			foreach ($result as $row) {
				$nature_name = $row->nature_name;
				$group_name = $row->group_name;
				$sub_group_name = $row->sub_group_name;
				$ledger_name = $row->ledger_name;

				// Initialize nature if not already set
				if (!isset($trial_balance_data[$nature_name])) {
					$trial_balance_data[$nature_name] = [
						'nature_amount_debit' => $row->nature_amount_debit,
						'nature_amount_credit' => $row->nature_amount_credit,
						'groups' => []
					];
				}

				if (!isset($trial_balance_data[$nature_name]['groups'][$group_name])) {
					$trial_balance_data[$nature_name]['groups'][$group_name] = [
						'group_amount_debit' => $row->group_amount_debit,
						'group_amount_credit' => $row->group_amount_credit,
						'sub_groups' => []
					];
				}

				if (!isset($trial_balance_data[$nature_name]['groups'][$group_name]['sub_groups'][$sub_group_name])) {
					$trial_balance_data[$nature_name]['groups'][$group_name]['sub_groups'][$sub_group_name] = [
						'sub_group_amount_debit' => $row->sub_group_amount_debit,
						'sub_group_amount_credit' => $row->sub_group_amount_credit,
						'ledgers' => []
					];
				}

				$trial_balance_data[$nature_name]['groups'][$group_name]['sub_groups'][$sub_group_name]['ledgers'][] = [
					'ledger_name' => $ledger_name,
					'debit' => $row->debit,
					'credit' => $row->credit
				];
			}

			
		}elseif($withDetails == 2){

			$result = DB::select("CALL GetTrialFullBalance_OTCB(?, ?, ?)", [0, $dtpFromDate, $dtpToDate]);

			$trial_balance_data = [];

			foreach ($result as $row) {
				$natureName = $row->nature_name;

				if (isset($trial_balance_data[$natureName])) {
					$trial_balance_data[$natureName][] = (array) $row;
				} else {
					$trial_balance_data[$natureName] = [(array) $row];
				}
			}
		}elseif($withDetails == 3){

			$result = DB::select("CALL GetMonthlyTrialBalance(?, ?, ?)", [0, $dtpFromDate, $dtpToDate]);

			$trial_balance_data = [];

			foreach ($result as $row) {
				$natureName = $row->nacc_name ?? 'Unknown';

				// Convert stdClass to array
				$rowArray = (array) $row;

				// Initialize group if not exists
				if (!isset($trial_balance_data[$natureName])) {
					$trial_balance_data[$natureName] = [];
				}

				$trial_balance_data[$natureName][] = $rowArray;
			}

		} else {
			$query = DB::select("CALL GetTrilBalance(?, ?, ?)", [$branch_id, $dtpFromDate, $dtpToDate]);
			$result = $query;

			$trial_balance_data = [];
			
			foreach ($result as $value) {
				if (isset($trial_balance_data[$value->nacc_name])) {
					$trial_balance_data[$value->nacc_name][] = $value;
				} else {
					$trial_balance_data[$value->nacc_name][] = $value;
				}
			}

		}

		$data['trial_balance_data'] = $trial_balance_data;
		$data['dtpFromDate'] = $dtpFromDate;
		$data['dtpToDate'] = $dtpToDate;

		// Fetching the setting and currency info
		$setting = app_setting();
		// $currencyinfo = DB::table('currency')->where('currencyid', $setting->currency)->first();
		$data['financial_years'] = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();

		$data['setting'] = $setting;
		// $data['currency'] = $currencyinfo->curr_icon;

		$data['pdf'] = "";
		$data['title'] = __('language.Trial Balance Report');


		

		if ($withDetails == 1) {
			return view('accounts::reports.trial_balance_report_search_details', $data);
		}elseif($withDetails == 2){
			return view('accounts::reports.trial_balance_report_search_details_with_opening_balance', $data);
		}elseif($withDetails == 3){
			return view('accounts::reports.trial_balance_report_search_with_monthly', $data);
		}else{
			return view('accounts::reports.trial_balance_report_search', $data);
		}
	}



	// profit loss
public function profit_loss_report(Request $request){

        $data['title'] = __('language.profit_loss');
		$data['financialyears']= DB::table('acc_financialyear')->where('is_active', 1)->first();
		$data['financial_years'] = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();
		

		// Retrieve the request parameters
		$startDate = $request->input('dtpFromDate')??$data['financialyears']->start_date;
		$endDate = $request->input('dtpToDate')??$data['financialyears']->end_date;
		$withDetails = $request->input('withDetails')??0;
		
		$data['branches'] = $branches = 0;
		$data['branch_id'] = $branch_id =  0;
		$data['branch_name'] = $branch_name = '';


		// Fetch settings and currency information
		$setting = app_setting();
		$getProfitLossData = DB::select("CALL GetProfitLoass(?, ?, ?)", [$branch_id, $startDate, $endDate]);
		
		
		// Initialize variables for totals
		$totalDebit_3 = 0;
		$totalCredit_3 = 0;
		$totalDebit_4 = 0;
		$totalCredit_4 = 0;

		// Group collections by nature_id
		$nature3 = collect($getProfitLossData)->where('nature_id', 3);
		$nature4 = collect($getProfitLossData)->where('nature_id', 2);


		// Get first balances for income and expenses
		$firstIncomeBalance = $nature3->first();
		$firstExpenseBalance = $nature4->first();

		

		// Prepare income and expense balances
		$incomeBalance = $firstIncomeBalance ? [
			'nature_amount_debit' => $firstIncomeBalance->nature_amount_debit,
			'nature_amount_credit' => $firstIncomeBalance->nature_amount_credit,
		] : ['nature_amount_debit' => 0, 'nature_amount_credit' => 0];

		$expenseBalance = $firstExpenseBalance ? [
			'nature_amount_debit' => $firstExpenseBalance->nature_amount_debit,
			'nature_amount_credit' => $firstExpenseBalance->nature_amount_credit,
		] : ['nature_amount_debit' => 0, 'nature_amount_credit' => 0];

		// Calculate final balances
		$finalIncomeBalance = $incomeBalance['nature_amount_credit'] - $incomeBalance['nature_amount_debit'];
		$finalExpenseBalance = $expenseBalance['nature_amount_debit'] - $expenseBalance['nature_amount_credit'];

		// Determine profit or loss
		if ($finalIncomeBalance > $finalExpenseBalance) {
			$profit = $finalIncomeBalance - $finalExpenseBalance;
			$resultProfitLoss = ['profit' => $profit, 'loss' => 0];
		} elseif ($finalIncomeBalance < $finalExpenseBalance) {
			$loss = $finalExpenseBalance - $finalIncomeBalance;
			$resultProfitLoss = ['profit' => 0, 'loss' => $loss];
		} else {
			$resultProfitLoss = ['profit' => 0, 'loss' => 0];
		}

		// Prepare grouped balances for nature 3 and 4
		if($withDetails == 2) {
			$getProfitLossNature_3_Balances = $this->groupBalances1($nature3, $totalDebit_3, $totalCredit_3);
			$getProfitLossNature_4_Balances = $this->groupBalances1($nature4, $totalDebit_4, $totalCredit_4);

		}else{
			$getProfitLossNature_3_Balances = $this->groupBalances($nature3, $totalDebit_3, $totalCredit_3);
			$getProfitLossNature_4_Balances = $this->groupBalances($nature4, $totalDebit_4, $totalCredit_4);

		}

		// Append total balances
		$getProfitLossNature_3_Balances[] = (object) [
			'name' => 'Total Income',
			'debit' => $totalDebit_3,
			'credit' => $totalCredit_3,
			'level' => 0,
		];

		$getProfitLossNature_4_Balances[] = (object) [
			'name' => 'Total Expense',
			'debit' => $totalDebit_4,
			'credit' => $totalCredit_4,
			'level' => 0,
		];

		// Append net profit or loss
		if ($resultProfitLoss['profit'] > 0 || $resultProfitLoss['loss'] > 0) {
			$getProfitLossNature_4_Balances[] = (object) [
				'name' => $resultProfitLoss['profit'] > 0 ? 'Net Profit' : 'Net Loss',
				'debit' => $resultProfitLoss['profit'] > 0 ? $resultProfitLoss['profit'] : $resultProfitLoss['loss'],
				'credit' => $resultProfitLoss['profit'] > 0 ? $resultProfitLoss['profit'] : $resultProfitLoss['loss'],
				'level' => 0,
				'class' => $resultProfitLoss['profit'] > 0 ? 'text-success' : 'text-danger',
			];
		} else {
			$getProfitLossNature_4_Balances[] = (object) [
				'name' => '',
				'debit' => 0,
				'credit' => 0,
				'level' => 0,
			];
		}

		// Format dates based on input or fiscal year
		$fromDate = $startDate;
		$toDate = $endDate;
		$financial_years = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();
	

		// Load the view with prepared data
		$data = [
			'getProfitLossNature_3_Balances' => $getProfitLossNature_3_Balances,
			'getProfitLossNature_4_Balances' => $getProfitLossNature_4_Balances,
			'dtpFromDate' => $fromDate,
			'dtpToDate' => $toDate,
			'withDetails' => $withDetails,
			'date' => "$fromDate to $toDate",
			'title' => __('language.Profit Loss'),
			'module' => "accounts",
			'setting' => $setting,
			'branches' => '',
			'branch_id' => 0,
			'branch_name' => '',
			'financialyears' => $data['financialyears'],
			'financial_years' => $financial_years,
			'dtpYear' => $request->input('dtpYear', true),
		];
		
		return view('accounts::reports.profit_loss_report', $data);
    }

	public function profitLossReportSearch(Request $request)
    {
        // Check user permission (you can use Gates or Policies for permissions in Laravel)

        try {
            // Retrieve the request parameters
            $startDate = $request->input('dtpFromDate');
            $endDate = $request->input('dtpToDate');
            $withDetails = $request->input('withDetails');
            
			$data['branches'] = $branches = 0;
			$data['branch_id'] = $branch_id =  0;
			$data['branch_name'] = $branch_name = '';


            // Fetch settings and currency information
			$setting = app_setting();
			// $currencyinfo = DB::table('currency')->where('currencyid', $setting->currency)->first();

            // Call the stored procedure using Laravel's DB facade
			if($withDetails == 2) {
				$getProfitLossData = DB::select("CALL GetProfitLoass_OTCB(?, ?, ?)", [$branch_id, $startDate, $endDate]);
			}else {
				$getProfitLossData = DB::select("CALL GetProfitLoass(?, ?, ?)", [$branch_id, $startDate, $endDate]);
			}

			
            // Initialize variables for totals
            $totalDebit_3 = 0;
            $totalCredit_3 = 0;
            $totalDebit_4 = 0;
            $totalCredit_4 = 0;

            // Group collections by nature_id
            $nature3 = collect($getProfitLossData)->where('nature_id', 3);
            $nature4 = collect($getProfitLossData)->where('nature_id', 2);


            // Get first balances for income and expenses
            $firstIncomeBalance = $nature3->first();
            $firstExpenseBalance = $nature4->first();

			

            // Prepare income and expense balances
            $incomeBalance = $firstIncomeBalance ? [
                'nature_amount_debit' => $firstIncomeBalance->nature_amount_debit,
                'nature_amount_credit' => $firstIncomeBalance->nature_amount_credit,
            ] : ['nature_amount_debit' => 0, 'nature_amount_credit' => 0];

            $expenseBalance = $firstExpenseBalance ? [
                'nature_amount_debit' => $firstExpenseBalance->nature_amount_debit,
                'nature_amount_credit' => $firstExpenseBalance->nature_amount_credit,
            ] : ['nature_amount_debit' => 0, 'nature_amount_credit' => 0];

            // Calculate final balances
            $finalIncomeBalance = $incomeBalance['nature_amount_credit'] - $incomeBalance['nature_amount_debit'];
            $finalExpenseBalance = $expenseBalance['nature_amount_debit'] - $expenseBalance['nature_amount_credit'];

            // Determine profit or loss
            if ($finalIncomeBalance > $finalExpenseBalance) {
                $profit = $finalIncomeBalance - $finalExpenseBalance;
                $resultProfitLoss = ['profit' => $profit, 'loss' => 0];
            } elseif ($finalIncomeBalance < $finalExpenseBalance) {
                $loss = $finalExpenseBalance - $finalIncomeBalance;
                $resultProfitLoss = ['profit' => 0, 'loss' => $loss];
            } else {
                $resultProfitLoss = ['profit' => 0, 'loss' => 0];
            }

            // Prepare grouped balances for nature 3 and 4
			if($withDetails == 2) {
				$getProfitLossNature_3_Balances = $this->groupBalances1($nature3, $totalDebit_3, $totalCredit_3);
				$getProfitLossNature_4_Balances = $this->groupBalances1($nature4, $totalDebit_4, $totalCredit_4);
	
			}else{
				$getProfitLossNature_3_Balances = $this->groupBalances($nature3, $totalDebit_3, $totalCredit_3);
				$getProfitLossNature_4_Balances = $this->groupBalances($nature4, $totalDebit_4, $totalCredit_4);
	
			}

            // Append total balances
            $getProfitLossNature_3_Balances[] = (object) [
                'name' => 'Total Income',
                'debit' => $totalDebit_3,
                'credit' => $totalCredit_3,
                'level' => 0,
            ];

            $getProfitLossNature_4_Balances[] = (object) [
                'name' => 'Total Expense',
                'debit' => $totalDebit_4,
                'credit' => $totalCredit_4,
                'level' => 0,
            ];

            // Append net profit or loss
            if ($resultProfitLoss['profit'] > 0 || $resultProfitLoss['loss'] > 0) {
                $getProfitLossNature_4_Balances[] = (object) [
                    'name' => $resultProfitLoss['profit'] > 0 ? 'Net Profit' : 'Net Loss',
                    'debit' => $resultProfitLoss['profit'] > 0 ? $resultProfitLoss['profit'] : $resultProfitLoss['loss'],
                    'credit' => $resultProfitLoss['profit'] > 0 ? $resultProfitLoss['profit'] : $resultProfitLoss['loss'],
                    'level' => 0,
                    'class' => $resultProfitLoss['profit'] > 0 ? 'text-success' : 'text-danger',
                ];
            } else {
                $getProfitLossNature_4_Balances[] = (object) [
                    'name' => '',
                    'debit' => 0,
                    'credit' => 0,
                    'level' => 0,
                ];
            }

            // Format dates based on input or fiscal year
            $fromDate = date('Y-m-d', strtotime($startDate));
            $toDate = date('Y-m-d', strtotime($endDate));
			$financial_years = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();
        

			// Load the view with prepared data
            $data = [
                'getProfitLossNature_3_Balances' => $getProfitLossNature_3_Balances,
                'getProfitLossNature_4_Balances' => $getProfitLossNature_4_Balances,
                'dtpFromDate' => $fromDate,
                'dtpToDate' => $toDate,
                'withDetails' => $withDetails,
                'date' => "$fromDate to $toDate",
                'title' => __('language.Profit Loss'),
                'module' => "accounts",
                'setting' => $setting,
                'branches' => '',
                'branch_id' => 0,
                'branch_name' => '',
                'financial_years' => $financial_years,
                'dtpYear' => $request->input('dtpYear', true),
            ];

	
			if($withDetails == 1) {
				$view = 'accounts::reports.profit_loss_report_search_details';
			}elseif($withDetails == 2) {
				$view = 'accounts::reports.profit_loss_report_search_with_opening_balance';
			}else{
				$view = 'accounts::reports.profit_loss_report_search';
			}
            return view($view, $data);


        } catch (\Exception $e) {
            // Handle errors gracefully
			dd($e->getMessage());
            session()->flash('error', __('language.No data found'));
            // return redirect()->route('reports.profit_loss_report');
        }
    }



// balance sheet
	public function balance_sheet_report(Request $request){

        $data['title'] = __('language.balance_sheet');
		$financialyears= DB::table('acc_financialyear')->where('is_active', 1)->first();
		$financial_years = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();
		
		// Retrieve the request parameters
		$startDate = $financialyears->start_date;
		$endDate = $financialyears->end_date;
		$t_shape = $request->input('t_shape');
		$with_cogs = $request->input('with_cogs');


		$branches = 0;
		$branch_id =  0;
		$branch_name = '';


		// Retrieve settings and currency info

		$CPLcode = DB::table('acc_predefined as p')
		->join('acc_predefined_seeting as ps', 'ps.predefined_id', '=', 'p.id', 'left')
		->where('p.predefined_name', 'CurrentYearProfitLoss')
		->select('ps.acc_coa_id as CPLcode')
		->first();
		$cplCode = $CPLcode->CPLcode ?? null;
		$setting = app_setting();
		// $currencyinfo = DB::table('currency')->where('currencyid', $setting->currency)->first();

		/*
		if ($with_cogs) {
			// Query stock valuation sums for raw ingredients and finished goods
			$raw_ingredients_stock = $this->AccReport->ingredientreportrow($startDate, $endDate, null, 1, null);
			$stockValuationSum1 = collect($raw_ingredients_stock)->sum('stockvaluation');

			$finish_goods_stock = $this->AccReport->productreportitem($startDate, $endDate, null, null);
			$stockValuationSum2 = collect($finish_goods_stock)->sum('stockvaluation');

			$stockValuationSum = $stockValuationSum1 + $stockValuationSum2;

			// Call the stored procedure with stock valuation sum
			$getBalanceSheetData = DB::select("CALL GetBalanceSheet(?, ?, ?, ?)", [0, $startDate, $endDate, $stockValuationSum]);
		} else {
		 */
			// Call the stored procedure without stock valuation sum
			$getBalanceSheetData = DB::select("CALL GetBalanceSheet(?, ?, ?)", [$branch_id, $startDate, $endDate]);
			// CALL GetBalanceSheet(1, '2024-07-01', '2025-06-30', NULL)
		// }


		// Group collections by nature_id using collections
		$assets = collect($getBalanceSheetData)->where('nature_id', 1)->all();
		$liabilities = collect($getBalanceSheetData)->where('nature_id', 4)->all();
		$equity = collect($getBalanceSheetData)->where('nature_id', 5)->all();

		// Initialize totals
		$totalDebitAssets = $totalCreditAssets = $totalDebitLiabilities = $totalCreditLiabilities = $totalDebitEquity = $totalCreditEquity = 0;

		// Group balances using a helper function (you may need to adapt this for your use case)
		$getBalanceSheetAssets = $this->groupBalances($assets, $totalDebitAssets, $totalCreditAssets);
		$getBalanceSheetLiabilities = $this->groupBalances($liabilities, $totalDebitLiabilities, $totalCreditLiabilities);
		$getBalanceSheetEquity = $this->groupBalances($equity, $totalDebitEquity, $totalCreditEquity);

		// Append totals
		$getBalanceSheetAssets[] = (object)[
			'name' => 'Total Assets',
			'debit' => $totalDebitAssets,
			'credit' => $totalCreditAssets,
			'level' => 0,
		];

		$getBalanceSheetLiabilities[] = (object)[
			'name' => 'Total Liabilities',
			'debit' => $totalDebitLiabilities,
			'credit' => $totalCreditLiabilities,
			'level' => 0,
		];

		$getBalanceSheetEquity[] = (object)[
			'name' => 'Total Equity',
			'debit' => $totalDebitEquity,
			'credit' => $totalCreditEquity,
			'level' => 0,
		];

		// Calculate overall balance (Assets - (Liabilities + Equity))
		$netBalance = $totalCreditLiabilities + $totalCreditEquity;

		$getBalanceSheetEquity[] = (object)[
			'name' => 'Total Liability & Owner Equity',
			'debit' => 0,
			'credit' => $netBalance,
			'level' => 0,
		];


		
		// Prepare the view data
		$data = [
			'financialyears' 				=> $financialyears,
			'financial_years' 				=> $financial_years,
			'getBalanceSheetAssets' 		=> $getBalanceSheetAssets,
			'getBalanceSheetAssets' 		=> $getBalanceSheetAssets,
			'getBalanceSheetLiabilities' 	=> $getBalanceSheetLiabilities,
			'getBalanceSheetEquity' 		=> $getBalanceSheetEquity,
			'dtpFromDate' 					=> $startDate,
			'dtpToDate' 					=> $endDate,
			't_shape' 						=> $t_shape,
			'with_cogs' 					=> 1,
			'date' 							=> $startDate .' to '. $endDate,
			'title' 						=> 'Balance Sheet Report',
			'module' 						=> "accounts",
			'branches' 						=> 0,
			'branch_id' 					=> 0,
			'branch_name' 					=> '',
			'setting' 						=> $setting,
			'CPLcode' 						=> $CPLcode,
			'dtpYear' 						=> $financialyears->title,
		];

		
		
		return view('accounts::reports.balance_sheet_report', $data);
    }

    public function balanceSheetReportSearch(Request $request) {

		try {
			// Retrieve the request parameters
			$startDate = $request->input('dtpFromDate');
			$endDate = $request->input('dtpToDate');
			$t_shape = $request->input('t_shape');
			$with_cogs = $request->input('with_cogs');
			$type = $request->input('type');
			
			$branches = 0;
			$branch_id =  0;
			$branch_name = '';
			// Retrieve settings and currency info

			$CPLcode = DB::table('acc_predefined as p')
			->join('acc_predefined_seeting as ps', 'ps.predefined_id', '=', 'p.id', 'left')
			->where('p.predefined_name', 'CurrentYearProfitLoss')
			->select('ps.acc_coa_id as CPLcode')
			->first();
			$cplCode = $CPLcode->CPLcode ?? null;
			$setting = app_setting();
			// $currencyinfo = DB::table('currency')->where('currencyid', $setting->currency)->first();

			/*
			if ($with_cogs) {
				// Query stock valuation sums for raw ingredients and finished goods
				$raw_ingredients_stock = $this->AccReport->ingredientreportrow($startDate, $endDate, null, 1, null);
				$stockValuationSum1 = collect($raw_ingredients_stock)->sum('stockvaluation');

				$finish_goods_stock = $this->AccReport->productreportitem($startDate, $endDate, null, null);
				$stockValuationSum2 = collect($finish_goods_stock)->sum('stockvaluation');

				$stockValuationSum = $stockValuationSum1 + $stockValuationSum2;

				// Call the stored procedure with stock valuation sum
				$getBalanceSheetData = DB::select("CALL GetBalanceSheet(?, ?, ?, ?)", [0, $startDate, $endDate, $stockValuationSum]);
			} else {
			 */
				// Call the stored procedure without stock valuation sum
				$getBalanceSheetData = DB::select("CALL GetBalanceSheet(?, ?, ?)", [$branch_id, $startDate, $endDate]);
				// CALL GetBalanceSheet(1, '2024-07-01', '2025-06-30', NULL)
			// }


			// Group collections by nature_id using collections
			$assets = collect($getBalanceSheetData)->where('nature_id', 1)->all();
			$liabilities = collect($getBalanceSheetData)->where('nature_id', 4)->all();
			$equity = collect($getBalanceSheetData)->where('nature_id', 5)->all();

			// Initialize totals
			$totalDebitAssets = $totalCreditAssets = $totalDebitLiabilities = $totalCreditLiabilities = $totalDebitEquity = $totalCreditEquity = 0;

			// Group balances using a helper function (you may need to adapt this for your use case)
			$getBalanceSheetAssets = $this->groupBalances($assets, $totalDebitAssets, $totalCreditAssets);
			$getBalanceSheetLiabilities = $this->groupBalances($liabilities, $totalDebitLiabilities, $totalCreditLiabilities);
			$getBalanceSheetEquity = $this->groupBalances($equity, $totalDebitEquity, $totalCreditEquity);

			// Append totals
			$getBalanceSheetAssets[] = (object)[
				'name' => 'Total Assets',
				'debit' => $totalDebitAssets,
				'credit' => $totalCreditAssets,
				'level' => 0,
			];

			$getBalanceSheetLiabilities[] = (object)[
				'name' => 'Total Liabilities',
				'debit' => $totalDebitLiabilities,
				'credit' => $totalCreditLiabilities,
				'level' => 0,
			];

			$getBalanceSheetEquity[] = (object)[
				'name' => 'Total Equity',
				'debit' => $totalDebitEquity,
				'credit' => $totalCreditEquity,
				'level' => 0,
			];

			// Calculate overall balance (Assets - (Liabilities + Equity))
			$netBalance = $totalCreditLiabilities + $totalCreditEquity;

			$getBalanceSheetEquity[] = (object)[
				'name' => 'Total Liability & Owner Equity',
				'debit' => 0,
				'credit' => $netBalance,
				'level' => 0,
			];

			// Format dates
			$fromDate = date('Y-m-d', strtotime($startDate));
			$toDate = date('Y-m-d', strtotime($endDate));
			$financial_years = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();

			// Prepare the view data
			$data = [
				'getBalanceSheetAssets' => $getBalanceSheetAssets,
				'getBalanceSheetLiabilities' => $getBalanceSheetLiabilities,
				'getBalanceSheetEquity' => $getBalanceSheetEquity,
				'dtpFromDate' => $fromDate,
				'dtpToDate' => $toDate,
				// 't_shape' => $t_shape,
				// 'with_cogs' => $with_cogs,
				'type' => $type,
				'date' => "$fromDate to $toDate",
				'title' => 'Balance Sheet Report',
				'module' => "accounts",
				'branches' => 0,
				'branch_id' => 0,
				'branch_name' => '',
				'setting' => $setting,
				'CPLcode' => $CPLcode,
				'financial_years' => $financial_years,
				'dtpYear' => $request->input('dtpYear'),
			];

			if($type == 1){
				return view('accounts::reports.balance_sheet_report_search', $data);
			}else{ 
				return view('accounts::reports.balance_sheet_report_search_t', $data);
			}
			
		} catch (\Exception $e) {
			// Handle errors gracefully
			// return redirect()->route('reports.balance_sheet_report')
							// ->with('error', 'No data found');
		}
	}


	// Function to group balances
    private function groupBalances($items, &$totalDebit, &$totalCredit)
    {
        $result = [];
        $groupedByNature = $this->groupBy($items, 'nature_name');

        foreach ($groupedByNature as $natureName => $natureItems) {
            $nature = reset($natureItems);
            $result[] = (object) [
                'name' => $natureName,
                'debit' => $nature->nature_amount_debit,
                'credit' => $nature->nature_amount_credit,
                'level' => 1,
            ];

            $groupedByGroup = $this->groupBy($natureItems, 'group_name');
			

            foreach ($groupedByGroup as $groupName => $groupItems) {
                $group = reset($groupItems);
                $result[] = (object) [
                    'name' => $groupName,
                    'debit' => $group->group_amount_debit,
                    'credit' => $group->group_amount_credit,
                    'level' => 2,
                ];


                $groupedBySubGroup = $this->groupBy($groupItems, 'sub_group_name');
                foreach ($groupedBySubGroup as $subGroupName => $subGroupItems) {

					
                    $subGroup = reset($subGroupItems);
                    $result[] = (object) [
                        'name' => $subGroupName,
                        'debit' => $subGroup->sub_group_amount_debit,
                        'credit' => $subGroup->sub_group_amount_credit,
                        'level' => 3,
                    ];
					
                    foreach ($subGroupItems as $ledger) {
						
                        $result[] = (object) [
                            'id'	=> $ledger->ledger_id,
							'name' 	=> $ledger->ledger_name,
                            'debit' => @$ledger?->debit,
                            'credit' => @$ledger?->credit,
                            'level' => 4,
                        ];
						
                        $totalDebit += @$ledger->debit;
                        $totalCredit += @$ledger->credit;
                    }
                }
            }
        }
        return $result;
    }
	private function groupBalances1($items, &$totalDebit, &$totalCredit)
    {
        $result = [];
        $groupedByNature = $this->groupBy($items, 'nature_name');

        foreach ($groupedByNature as $natureName => $natureItems) {
            $nature = reset($natureItems);
            $result[] = (object) [
                'name' => $natureName,
                'debit' => $nature->nature_amount_debit,
                'credit' => $nature->nature_amount_credit,
                'level' => 1,
            ];

            $groupedByGroup = $this->groupBy($natureItems, 'group_name');
			

            foreach ($groupedByGroup as $groupName => $groupItems) {
                $group = reset($groupItems);
                $result[] = (object) [
                    'name' => $groupName,
                    'debit' => $group->group_amount_debit,
                    'credit' => $group->group_amount_credit,
                    'level' => 2,
                ];


                $groupedBySubGroup = $this->groupBy($groupItems, 'sub_group_name');
                foreach ($groupedBySubGroup as $subGroupName => $subGroupItems) {

					
                    $subGroup = reset($subGroupItems);
                    $result[] = (object) [
                        'name' => $subGroupName,
                        'debit' => $subGroup->sub_group_amount_debit,
                        'credit' => $subGroup->sub_group_amount_credit,
                        'level' => 3,
                    ];
					
                    foreach ($subGroupItems as $ledger) {
						
                        $result[] = (object) [
                            'id'	=> $ledger->ledger_id,
							'name' 	=> $ledger->ledger_name,
                            'o_debit' => @$ledger?->o_debit,
                            'o_credit' => @$ledger?->o_credit,
                            't_debit' => @$ledger?->t_debit,
                            't_credit' => @$ledger?->t_credit,
                            'c_debit' => @$ledger?->c_debit,
                            'c_credit' => @$ledger?->c_credit,
                            'level' => 4,
                        ];
						
                        $totalDebit += @$ledger->debit;
                        $totalCredit += @$ledger->credit;
                    }
                }
            }
        }
        return $result;
    }

	// income statement
	public function income_statement(Request $request)
	{
		$data['title'] = __('language.income_statement');

		$data['financialyears'] = DB::table('acc_financialyear')
									->where('is_active', 1)
									->first();

		$data['financial_years'] = DB::table('acc_financialyear')
									->whereIn('is_active', [1, 2])
									->get();


		$data['branch_id'] = $branch_id =  0;

		$data['branch_name'] = $branch_name = '';


		//$data['setting'] = app_setting();

		if ($request->isMethod('post')) {
			$startDate = $request->input('dtpFromDate');
			$endDate = $request->input('dtpToDate');
			$data['date'] = "$startDate to $endDate";

			$stockValuationSum = 0; // You can replace this with the logic you need if applicable

			$query = DB::select("CALL GetIncomeStatement(?, ?, ?, ?)", [$branch_id, $startDate, $endDate, 0.00]);

			$data['income_statement'] = $query;

		}

		return view('accounts::reports.income_statement', $data);
	}
    // Helper function to group arrays by a specified property
    private function groupBy($items, $key)
    {
        $result = [];
        foreach ($items as $item) {
            $result[$item->$key][] = $item;
        }
        return $result;
    }

	// profit loss
		public function received_payment_report(Request $request){

        $data['title'] = __('language.profit_loss');
		$data['financialyears'] = DB::table('acc_financialyear')->where('is_active', 1)->first();
		$data['financial_years'] = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();
		

		// Retrieve the request parameters
		$startDate = $request->input('dtpFromDate')??$data['financialyears']->start_date;
		$endDate = $request->input('dtpToDate')??$data['financialyears']->end_date;
		$row = $request->input('row')??10;
		$page_n = $request->input('page')??1;
		$branch_id = 0;
		// Fetch settings and currency information
		$setting = app_setting();
		// $currencyinfo = DB::table('currency')->where('currencyid', $setting->currency)->first();
		// Call the stored procedure using Laravel's DB facade
		$query = DB::select("CALL GetCashReceivedPayment(?, ?, ?,?, ?, @op_total_row)", [$branch_id, $startDate, $endDate,$row, $page_n]);
		$result = $query;
		$totalRow = DB::selectOne("SELECT @op_total_row AS total_row")->total_row;
		$page_no = ceil($totalRow / $row);

		// Format dates based on input or fiscal year
		$fromDate = $startDate;
		$toDate = $endDate;

		
		$financial_years = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();
		$vouchartypes = DB::table('acc_vouchartype')->get()->toArray();
		// Load the view with prepared data
		$data = [
			'ledger_data' => $result,
			'vouchartypes' => $vouchartypes,
			'totalRow' => $totalRow,
			'row' => $row,
			'page_no' => $page_no,
			'page_n' => $page_n,
			'dtpFromDate' => $fromDate,
			'dtpToDate' => $toDate,
			'date' => "$fromDate to $toDate",
			'title' => __('language.receive_payment_report'),
			'module' => "accounts",
			'setting' => $setting,
			'branches' => '',
			'branch_id' => 0,
			'branch_name' => '',
			'financialyears' => $data['financialyears'],
			'financial_years' => $financial_years,
			'dtpYear' => $data['financialyears']->title,
		];
		
		// dd($data);

		return view('accounts::reports.received_payment_report', $data);
    }

	public function received_payment_report_search(Request $request)
    {
        // Check user permission (you can use Gates or Policies for permissions in Laravel)
        try {
            
			// Retrieve the request parameters
            $startDate = $request->input('dtpFromDate');
            $endDate = $request->input('dtpToDate');
			$row = $request->input('row');
			$page_n = $request->input('page');
			$branch_id = 0;
            // Fetch settings and currency information
			$setting = app_setting();
			// $currencyinfo = DB::table('currency')->where('currencyid', $setting->currency)->first();
            // Call the stored procedure using Laravel's DB facade
            $query = DB::select("CALL GetCashReceivedPayment(?, ?, ?,?, ?, @op_total_row)", [$branch_id, $startDate, $endDate,$row, $page_n]);
			$result = $query;
			$totalRow = DB::selectOne("SELECT @op_total_row AS total_row")->total_row;
			$page_no = ceil($totalRow / $row);

            // Format dates based on input or fiscal year
            $fromDate = date('Y-m-d', strtotime($startDate));
            $toDate = date('Y-m-d', strtotime($endDate));
			$financial_years = DB::table('acc_financialyear')->where('is_active', 1)->orWhere('is_active', 2)->get();
			$vouchartypes = DB::table('acc_vouchartype')->get()->toArray();

		
			
            // Load the view with prepared data
            $data = [
				'ledger_data' => $result,
				'vouchartypes' => $vouchartypes,
				'totalRow' => $totalRow,
				'row' => $row,
				'page_no' => $page_no,
				'page_n' => $page_n,
                'dtpFromDate' => $fromDate,
                'dtpToDate' => $toDate,
                'date' => "$fromDate to $toDate",
                'title' => __('language.receive_payment_report'),
                'module' => "accounts",
                'setting' => $setting,
                'branches' => '',
                'branch_id' => 0,
                'branch_name' => '',
                'financial_years' => $financial_years,
                'dtpYear' => $request->input('dtpYear', true),
            ];

			
            // Choose the appropriate view
            $view = 'accounts::reports.received_payment_report_search';
            return view($view, $data);

        } catch (\Exception $e) {
            // Handle errors gracefully
			dd($e->getMessage());
            session()->flash('error', __('language.No data found'));
            // return redirect()->route('reports.profit_loss_report');
        }
    }
	public function cash_flow_report(){

		return view('accounts::reports.cash_flow_report');
    }
	public function cash_flow_report_search(Request $request)
    {
        $request->validate([
			'type'=>'required',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);
        
       
        try {
			$type = trim($request->input('type'));
            $fromDate = trim($request->input('from_date'));
            $toDate = trim($request->input('to_date'));

            $from_date = Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('m/d/Y', $toDate)->format('Y-m-d');
    
            // Call the stored procedure
            $results = DB::select('CALL GetCashReceivedPayment_Report(?, 0, ?, ?)', [$type, $from_date, $to_date]);
        // dd($results);
    
            return response()->json(['data' => $results], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
