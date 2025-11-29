<?php

namespace Modules\Accounts\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Accounts\Entities\AccCoa;
use Modules\Accounts\Entities\AccInstallment;
use Modules\Accounts\Entities\AccSubcode;
use Modules\Accounts\Http\DataTables\AccInstallmentDataTable;
use Modules\Accounts\Entities\AccInstallmentRecord;

class AccInstallmentController extends Controller
{
    public function index(AccInstallmentDataTable $dataTable)
    {
        $assets = DB::table('acc_coas')->where('acc_type_id', 1)->where('head_level', 4)->where('is_active', true)->whereNull('deleted_at')->get();
        $liabilities = DB::table('acc_coas')->where('acc_type_id', 4)->where('head_level', 4)->where('is_active', true)->whereNull('deleted_at')->get();
        return $dataTable->render('accounts::installment.index',compact('assets','liabilities'));
    }

    public function show($id)
    {
        $installment      = AccInstallment::findOrFail($id);
        $allheads = AccCoa::where('head_level', 4)->where('is_cash_nature', 1)->orWhere('is_bank_nature', 1)->where('is_active', 1)->whereNull('deleted_at')->get();


        return view('accounts::installment.show', compact('installment', 'allheads'));
    }



        public function approveForDisbusment(Request $request, $id)
        {
            DB::beginTransaction();

            try {
                $installment = AccInstallment::findOrFail($id);
                $installment->is_approve = 1;
                $installment->save();

                DB::commit();
                return response()->json([
                    'status' => true,
                    'title'   => 'Updated!',
                    'message' => 'AccInstallment approval status updated successfully.',
                ]);

            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'title'   => 'Not Found',
                    'message' => 'Installment not found.',
                ], 404);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'title'   => 'Error',
                    'message' => 'Failed to update installment status: ' . $e->getMessage(),
                ], 500);
            }
        }


        public function submitAdjustment(Request $request)
        {
            $request->validate([
                'installment_ids' => 'required|array',
                'acc_coas_id' => 'required|string',
            ]);
            foreach ($request->installment_ids as $id) {
                $installment = AccInstallmentRecord::findOrFail($id);
                if ($installment && $installment->status != 'Paid') {
                    $installment->update([
                        'adjustment_amount'=>$installment->installment_amount,
                        'adjustment_date'=>date('Y-m-d'),
                        'adjust_by'=>auth()->user()->id,
                        'status' => 'Paid',
                        'acc_coa_id' => $request->acc_coas_id,
                    ]);
                }
                $voucher_msg=voucher_posting($id,'INSADJUST',0); 
                toastr()->success($voucher_msg, 'Success');  
            }
            return response()->json(['success' => true]);
        }


        public function holdInstallment(Request $request)
        {
            $installment = AccInstallmentRecord::findOrFail($request->installment_id);

            if ($installment->status == 'Paid') {
                return response()->json(['error' => 'Cannot hold a paid installment'], 400);
            }

            DB::transaction(function () use ($installment) {
                // 1. Update current installment as Hold
                $installment->update([
                    'status' => 'Hold',
                ]);

                // 2. Find max installment date for this installment
                $maxDate = AccInstallmentRecord::where('installments_id', $installment->installments_id)
                    ->max('installment_date');
                $maxNumber = AccInstallmentRecord::where('installments_id', $installment->installments_id)
                    ->max('number_of_installment');
               
                $installment_record_installment                        = new AccInstallmentRecord();
                $installment_record_installment->installments_id               = $installment->installments_id;
                $installment_record_installment->number_of_installment = $maxNumber + 1;
                $installment_record_installment->installment_amount    = $installment->installment_amount;
                $installment_record_installment->installment_date      = Carbon::parse($maxDate)->addMonth()->format('Y-m-d');
                $installment_record_installment->status                = 'Unpaid';
                $installment_record_installment->adjustment_amount     = 0;
                $installment_record_installment->sub_code_id           = null;
                $installment_record_installment->save();

            });

            return response()->json(['success' => true]);
        }


        public function hold1(Request $request)
        {
            $installment = AccInstallmentRecord::findOrFail($request->installment_id);

            if ($installment->status != 'Paid') {
                $installment->update([
                    'status' => 'Hold',
                ]);
            }
            return response()->json(['success' => true]);
        }


    public function approve($id)
    {
        $installment      = AccInstallment::findOrFail($id);
        $allheads = AccCoa::where('head_level', 4)->where('is_cash_nature', 1)->orWhere('is_bank_nature', 1)->where('is_active', 1)->whereNull('deleted_at')->get();

        return response()->view('accounts::installment.modal.approve', compact('installment','allheads'));
    }

    public function approveInstallment(Request $request, $id)
    {
        $installment = AccInstallment::findOrFail($id);

        $request->validate([
            'acc_coas_id' => 'required',
        ]);

        DB::beginTransaction();
        $data=[
            'acc_coas_id' => $request->acc_coas_id,
            'is_approve' => 1,
            // 'installment_disbursed_by' => auth()->user()->id,
             'approved_date' => date('Y-m-d'),
            // 'is_installment_disbursed' => 1,
        ];
        try {
            $installment->update($data);
            DB::commit();
            $voucher_msg=voucher_posting($id,'INSTALLMENT',0); 
            toastr()->success($voucher_msg, 'Success');  
            toastr()->success('Installment Approved successfully :)', 'Success');
            return redirect()->route('installments.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            toastr()->error($th->getMessage(), 'Error');
            return redirect()->back();
        }
        
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'installment_type'          => 'required',
            'install_type'          => 'required',
            'installment_head'          => 'required',
            'amount'             => 'required',
            'installment'        => 'required',
            'installment_amount' => 'required',
            'effective_date'     => 'required',
            'is_active'          => 'required',
        ]);

        $this->storeData($request->all());

        

        return redirect()->route('installments.index');
    }

    public function edit($id)
    {
        $installment      = AccInstallment::findOrFail($id);
        $assets = DB::table('acc_coas')->where('acc_type_id', 1)->where('head_level', 4)->where('is_active', true)->whereNull('deleted_at')->get();
        $liabilities = DB::table('acc_coas')->where('acc_type_id', 4)->where('head_level', 4)->where('is_active', true)->whereNull('deleted_at')->get();
        return view('accounts::installment.edit', compact('installment','assets','liabilities'));
    }

    public function update(Request $request, $id)
    {
        $installment = AccInstallment::findOrFail($id);

        $request->validate([
            'installment_type'          => 'required',
            'installType'          => 'required',
            'acc_coa_id_type'          => 'required',
            'amount'             => 'required',
            'installment'        => 'required',
            'installment_amount' => 'required',
            'effective_date'     => 'required',
            'is_active'          => 'required',
        ]);

        DB::beginTransaction();

        try {
            $installment->update($request->except('created_at'));

            if ($request->installment_date) {
                $installment->accInstallmentRecords()->delete();
                foreach ($request->installment_date as $key => $installment_date) {
                    $installment_record_installment                        = new AccInstallmentRecord();
                    $installment_record_installment->installments_id               = $installment->id;
                    $installment_record_installment->number_of_installment = $key + 1;
                    $installment_record_installment->installment_amount    = $request->installment_amount;
                    $installment_record_installment->installment_date      = $installment_date;
                    $installment_record_installment->voucher_event_code      = 'INSADJUST';
                    $installment_record_installment->status                = 'Unpaid';
                    $installment_record_installment->sub_code_id           = null;
                    $installment_record_installment->save();
                }
            }
            DB::commit();
            toastr()->success('AccInstallment Updated successfully :)', 'Success');
            return redirect()->route('installments.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            toastr()->error($th->getMessage(), 'Error');
            return redirect()->back();
        }

        
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $installment = AccInstallment::findOrFail($id);
            $installment->accInstallmentRecords()->delete();
            $installment->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            toastr()->error('Something went wrong! Please try again.', 'Error');
            return redirect()->back();
        }

        toastr()->success('AccInstallment Deleted successfully :)', 'Success');
        return response()->json(['success' => 'AccInstallment Deleted successfully']);
    }


    /**
     * Store a newly created resource in storage.
     * @param mixed $data
     * @return bool|AccInstallment
     */
    public function storeData($data)
    {
        // DB::beginTransaction();
        $installment                     = new AccInstallment();

        try {
            $installment->installment_type          = $data['installment_type'];
            $installment->acc_coa_id_type          = $data['installment_head'];
            $installment->installType          = $data['install_type'];
            $installment->amount             = $data['amount'];
            $installment->installment        = $data['installment'];
            $installment->installment_amount = $data['installment_amount'];
            $installment->effective_date     = $data['effective_date'];
            $installment->is_active          = $data['is_active'];
            $installment->voucher_event_code = 'INSTALLMENT';
            $installment->sub_code_id        = null;
            $installment->created_by         = auth()->user()->id;
            $installment->save();

            foreach ($data['installment_date'] as $key => $installment_date) {
                $installment_record_installment                        = new AccInstallmentRecord();
                $installment_record_installment->installments_id               = $installment->id;
                $installment_record_installment->number_of_installment = $key + 1;
                $installment_record_installment->installment_amount    = $data['installment_amount'];
                $installment_record_installment->installment_date      = $installment_date;
                $installment_record_installment->status                = 'Unpaid';
                $installment_record_installment->voucher_event_code      = 'INSADJUST';
                $installment_record_installment->sub_code_id           = null;
                $installment_record_installment->save();
            }

            // DB::commit();
            toastr()->success('Installment added successfully :)', 'Success');
        } catch (\Exception $e) {
            // DB::rollBack();
            toastr()->error($e->getMessage(), 'fail');
            return false;
        }
        return $installment;
    }
    public function installmentReport()
    {
        return view('accounts::installment.installment_report');
    }

    public function getInstallmentReport(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);
        
        try {
            // Trim whitespace from input
            $fromDate = trim($request->input('from_date'));
            $toDate = trim($request->input('to_date'));

            // Convert date format from m/d/Y to Y-m-d for database comparison
            $from_date = Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('m/d/Y', $toDate)->format('Y-m-d');
    
            // Define the raw SQL query with '?' placeholders for security
            $query = "
                SELECT 
                    i.id, 
                    i.installment_type as installment_name,
                    i.installType as installment_type,
                    s.installment_date,
                    s.installment_amount,
                    s.adjustment_amount,
                    s.status  
                FROM acc_installment_schedules s
                INNER JOIN acc_installments i ON s.installments_id = i.id 
                WHERE s.installment_date BETWEEN ? AND ?
            ";

            // Execute the query using DB::select with bindings
            $results = DB::select($query, [$from_date, $to_date]);
           
            // You can uncomment the line below to debug the results
            // dd($results);
    
            return response()->json(['data' => $results], 200);

        } catch (\Exception $e) {
            // Return a meaningful error message
            return response()->json(['error' => 'An error occurred while fetching the report.', 'details' => $e->getMessage()], 500);
        }
    }

}
