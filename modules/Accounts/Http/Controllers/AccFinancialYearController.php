<?php

namespace Modules\Accounts\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccFinancialYearController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:read_financial_year'])->only(['fin_yearlist', 'openBook']);
        $this->middleware(['permission:create_financial_year'])->only(['yearEnding']);
        $this->middleware(['permission:update_financial_year'])->only(['singlefinyear_update']);
    }
    public function fin_yearlist()
    {
        $data['title'] = 'financial_year';
        $data['module'] = "accounts";
        $data['yearlist'] = DB::table('acc_financialyear')
        ->where('is_active', 1)
        ->orWhere('is_active', 2)
        ->get();
       return view('accounts::financial-year.financial_year', $data) ;
        
    }

    public function singlefinyear_update()
    {
        // Start a database transaction
        /*DB::beginTransaction();

        try {*/
            // Retrieve data from the request
            $id = request()->post('id');
            $title = request()->post('title');
            $start = request()->post('start');
            $end = request()->post('end');
            $status = request()->post('status');

            // Prepare data for updating the financial year
            $postData = [
                'title'      => $title,
                'start_date' => $start,
                'end_date'   => $end,
                'create_by'  => auth()->user()->id,
            ];

            // Update the financial year
            $update = DB::table('acc_financialyear')
                ->where('fiyear_id', $id)
                ->update($postData);

            // Update the inactive financial year with adjusted dates
            DB::table('acc_financialyear')
                ->where('is_active', 0)
                ->update([
                    'start_date' => now()->parse($postData['start_date'])->subDay()->format('Y-m-d'),
                    'end_date'   => now()->parse($postData['start_date'])->subDay()->format('Y-m-d')
                ]);

            // Get the first inactive year
            $inactive_year = DB::table('acc_financialyear')
                ->where('is_active', 0)
                ->first(); 

            // If an inactive year exists, update the opening balance
            if ($inactive_year) {
                DB::table('acc_openingbalance')
                    ->where('financial_year_id', $inactive_year->fiyear_id)
                    ->update(['open_date' => $inactive_year->end_date]);
            }

            // Commit the transaction if all operations succeed
            // DB::commit();

            // Return success response
            Toastr::success('Financial year updated successfully :)', 'Success');

            return redirect()->route('accounts.financial.yearlist');

        /*} catch (QueryException $e) {
            // Rollback the transaction if there's a database error
            DB::rollBack();
            
            // Return database error response
            Toastr::error('Database error: ' . $e->getMessage(), 'Fail');

           
        } catch (\Exception $e) {
            // Rollback the transaction if there's a general error
            DB::rollBack();

            // Return general error response
            Toastr::error('Database error: ' . $e->getMessage(), 'Fail');

        }
        */
    }

    public function openBook(Request $request)
    {
        // Get year_id and modal_type from the request
        $year_id = $request->post('year_id');
        $modal_type = $request->post('modal_type');
        
        // Fetch financial year data
        $financial_year = DB::table('acc_financialyear')
                            ->where('fiyear_id', $year_id)
                            ->first();

        // Prepare the data array
        $data['financial_year'] = $financial_year;

        if ($modal_type == '2') {
            // Execute stored procedures and retrieve results using DB::select
            $data['trial_balance'] = $this->executeProcedure("CALL GetTrialFullBalanceHeadLebel(?,?,?)", [0, $financial_year->start_date, $financial_year->end_date]);
            $data['voucher_summary'] = $this->executeProcedure("CALL GetVoucherSummary(?,?)", [$financial_year->start_date, $financial_year->end_date]);

            // Calculate new start and end date for next year
            $newStartDate = now()->parse($financial_year->start_date)->addYear()->format('Y-m-d');
            $newEndDate = now()->parse($financial_year->end_date)->addYear()->format('Y-m-d');

            // Get next year's opening balance
            $data['next_year_opening_balance'] = $this->executeProcedure("CALL GetNextYearOpeningBalanceView(?,?,?)", [0, $newStartDate, $newEndDate]);

            // Load the view for trial balance and voucher summary
            $details = view("accounts::financial-year.open_book", $data)->render();
        } else {
            // Load the update year view
            $details = view("accounts::financial-year.update_year", $data)->render();
        }

        // Send the response as JSON
        return response()->json(['data' => $details]);
    }

    /**
     * Helper function to execute a stored procedure and fetch the result.
     * Frees and clears stored results to avoid out-of-sync issues.
     */
    private function executeProcedure($procedure, $params)
    {
        try {
            // Use DB::select to execute stored procedure with parameters
            $results = DB::select($procedure, $params);
    
            // Return the results
            return $results;
    
        } catch (QueryException $e) {
            // Log the error or handle it as needed
            Log::error("Stored Procedure Execution Error: " . $e->getMessage());
            return null;
        }
    }

    public function yearEnding(Request $request)
    {
        
            // Get the input parameters
            $fiyear_id = $request->post('fiyear_id');
            $old_year_form_date = $request->post('old_year_form_date');
            $old_year_to_date = $request->post('old_year_to_date');
            $next_year_title = $request->post('next_year_title');
            $next_year_from_date = $request->post('next_year_from_date');
            $next_year_to_date = $request->post('next_year_to_date');
            $user_id = auth()->user()->id;
        
            // Begin a database transaction
            DB::beginTransaction();
        
            try {
                // Set output message variable in the procedure call
                DB::statement("SET @output_message = '';");
                
                // Call the stored procedure with the parameters
                DB::statement("CALL ProcessYearEnding(?, ?, ?, ?, ?, ?, ?, ?, @output_message)", [
                    $fiyear_id,
                    0,  // Placeholder for the second parameter
                    $old_year_form_date,
                    $old_year_to_date,
                    $next_year_title,
                    $next_year_from_date,
                    $next_year_to_date,
                    $user_id
                ]);
        
                // Fetch the output message
                $output_result = DB::select("SELECT @output_message AS Message");
                return response()->json(['status' => 'success', 'message' => $output_result[0]->Message]);
                // Commit the transaction if everything is successful
                DB::commit();

            } catch (\Exception $e) {
                // Rollback the transaction in case of error
                DB::rollBack();
                Log::error("Error processing year ending: " . $e->getMessage());
                Toastr::error('Database error: ' . $e->getMessage(), 'Fail');
            }
        
    }
}
