<?php

namespace Modules\Accounts\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AccOpeningBalanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:read_opening_balance'])->only(['opening_balancelist', 'getOpeningBalance']);
        $this->middleware(['permission:create_opening_balance'])->only(['opening_balanceform', 'opening_balance']);
        $this->middleware(['permission:delete_opening_balance'])->only('deleteOpeningBalance');

    }

    public function opening_balancelist() 
    { 
        $data['title'] = __('language.opening_balance');
        $data['financialyear'] = DB::table('acc_financialyear')->get();
        $data['module'] = "accounts";
        return view('accounts::opening-balance.list', $data);
    }

    public function getOpeningBalance(Request $request)
    {
        // Get the inputs
        $fiyear_id = $request->input('fiyear_id');
        $row = $request->input('row');
        $page_n = $request->input('page');

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Query to get opening balance results
            $results = DB::select("CALL GetOpeningBalanceWithTotalPaging(?, ?, ?, @op_total_row)", [$fiyear_id, $row, $page_n]);
            
            // Fetch the total row count
            $totalRow = DB::select("SELECT @op_total_row AS total_row");
            $totalRow = $totalRow[0]->total_row;

            // Calculate total pages
            $page_no = round((float)$totalRow / (float)$row) == 0?1:round((float)$totalRow / (float)$row);
   
            // Fetch settings and currency information
            $setting = app_setting();
            // $currencyInfo = DB::table('currency')->where('currencyid', $setting->currency)->first();

            // Prepare the response data
            $data = [
                'page_n' =>$page_n,
                'row' => (float)$row,
                'totalRow' => $totalRow,
                'page_no' => $page_no,
                'result' => $results,
                'setting' => $setting,
                'title' => __('language.opening_balance'),
                'module' => 'accounts',
            ];
        

            // Commit the transaction
            DB::commit();
          
            return response()->json(['htmlContent' => view('accounts::opening-balance.list_search', $data)->render()]);

            // return view('accounts::opening-balance.list_search', $data);

        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            
            // Return a meaningful error message
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    public function opening_balanceform()
    {
        $data['title'] = __('language.opening_balance');
        
        // Fetch active financial years and accounts
        $data['financial_years'] = DB::table('acc_financialyear')->where('is_active', 0)->get();
        $data['accounts'] = DB::table('acc_coas')->where('head_level', 4)->where('is_active', true)->whereNull('deleted_at')->get();

        // Get inactive and ended financial years
        $inactiveYear = DB::table('acc_financialyear')->where('is_active', 0)->first();
        $data['ended_year'] = DB::table('acc_financialyear')->where('is_active', 2)->first();

        // Fetch opening balance for the inactive year
        $data['opening_balance'] = DB::table('acc_openingbalance as ob')
            ->join('acc_coas as ac', 'ob.acc_coa_id', '=', 'ac.id')
            ->where('ob.financial_year_id', $inactiveYear->fiyear_id)
            ->get();

        $data['module'] = "accounts";

        return view('accounts::opening-balance.ob_form', $data);
    }

    public function opening_balance(Request $request)
    {
   
        $opening_balances = $request->input('opening_balances');

        DB::beginTransaction();

        try {
            // Fetch the inactive financial year
            $inactiveYear = DB::table('acc_financialyear')->where('is_active', 0)->first();

            // Delete previous opening balances for the inactive year
            DB::table('acc_openingbalance')->where('financial_year_id', $inactiveYear->fiyear_id)->delete();

            // Loop through the opening balances and insert them
            foreach ($opening_balances as $balance) {
                $subcode = null;
                if (isset($balance['subcode_id'])) {
                    $subcode = DB::table('acc_subcode')->where('id', $balance['subcode_id'])->first();
                }

                $data = [
                    'financial_year_id' => $inactiveYear->fiyear_id,
                    'open_date' => $inactiveYear->end_date,
                    'acc_coa_id' => $balance['coa_id'],
                    'acc_subtype_id' => $subcode ? $subcode->subTypeID : null,
                    'acc_subcode_id' => isset($balance['subcode_id']) ? $balance['subcode_id'] : null,
                    'debit' => isset($balance['debit']) ? $balance['debit'] : 0.00,
                    'credit' => isset($balance['credit']) ? $balance['credit'] : 0.00
                ];

                // Insert opening balance data
                DB::table('acc_openingbalance')->insert($data);
            }

            // Commit transaction
            DB::commit();

            // Set flash message
            Session::flash('message', __('language.save_successfully'));

            // Redirect to the opening balance list
            return redirect()->route('accounts.opening-balance.list');

        } catch (\Exception $e) {
            // Rollback transaction if error occurs
            DB::rollBack();
            
            // Return error message
            return back()->with('error', 'Database error: ' . $e->getMessage());
        }
    }

    public function getSubtypeByCode($id)
    {
        $htm = '';

        // Find the account with subtype
        $account = DB::table('acc_coas')
            ->where('id', $id)
            ->whereNotNull('subtype_id')
            ->whereNull('deleted_at')
            ->first();

        if ($account) {
            $subcodes = DB::table('acc_subcode')
                ->where('subTypeID', $account->subtype_id)
                ->get();

            foreach ($subcodes as $sc) {
                if ($account->subtype_id == 2) {
                    // Special format for subTypeID = 2
                    $employee = DB::table('employees')
                        ->leftJoin('employee_has_documents as ikama', function ($join) {
                            $join->on('ikama.employee_id', '=', 'employees.id')
                                ->where('ikama.document_type_id', 2);
                        })
                        ->leftJoin('employee_has_documents as passport', function ($join) {
                            $join->on('passport.employee_id', '=', 'employees.id')
                                ->where('passport.document_type_id',1);
                        })
                        ->where('employees.id', $sc->refCode) // adjust field name if different
                        ->select(
                            'employees.id',
                            'employees.employee_id',
                            'employees.name',
                            'ikama.document_no as iqama_no',
                            'passport.document_no as passport_no'
                        )
                        ->first();

                    if ($employee) {
                        $htm .= '<option value="' . $employee->id . '">'
                            . $employee->name
                            . ', IQ:' . ($employee->iqama_no ?? '-')
                            . ', PP:' . ($employee->passport_no ?? '-')
                            . ', EID:' . ($employee->employee_id ?? '-') .
                            '</option>';
                    }
                } else {
                    // Default format
                    $htm .= '<option value="' . $sc->id . '" data-subtype="' . $account->subtype_id . '">' . $sc->name . '</option>';
                }
            }
        }

        return response()->json($htm);
    }


    public function getSubtypeById($id)
    {
        $debitvcode = DB::table('acc_coas')->where('id', $id)->whereNull('deleted_at')->first();
        $data = ['subType' => $debitvcode->subtype_id];

        return response()->json($data);
    }

}
