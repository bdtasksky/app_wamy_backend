<?php

namespace Modules\Accounts\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounts\Entities\AccCoa;
use Modules\Accounts\Entities\AccSubtype;
use Modules\Accounts\Http\Exports\AccCoaExport;

class AccCoaController extends Controller
{
    // Apply middleware for permissions based on actions
    public function __construct()
    {
        $this->middleware(['permission:read_chart_of_accounts'])->only(['index','show']);
        $this->middleware(['permission:create_chart_of_accounts'])->only(['create','store']);
        $this->middleware(['permission:update_chart_of_accounts'])->only(['edit','update']);
        $this->middleware(['permission:delete_chart_of_accounts'])->only('destroy');
    }


    public function index()
    {
        return view('accounts::index');
    }


    public function create()
    {
        $accMainHead = AccCoa::where('head_level', 1)->where('parent_id', 0)->get();
        $accSecondLableHead = AccCoa::where('head_level', 2)->get();
        $accHeadWithoutFandS = AccCoa::whereNot('head_level', 2)->whereNot('head_level', 1)->get();
        $accSubType = AccSubtype::getCacheInfo();

        return view('accounts::coa.create', compact('accMainHead', 'accSecondLableHead', 'accHeadWithoutFandS', 'accSubType'));
    }


    public function store(Request $request)
    {

        $validated = $request->validate([
            'account_name' => 'required',
            'head_level' => 'required',
            'parent_id' => 'required',
            'acc_type_id' => 'required',
            'is_active' => 'required',
        ]);
        if ($request->asset_type == 'is_stock') {
            $validated['is_stock'] = 1;
        }
        if ($request->asset_type == 'is_fixed_asset') {
            $validated['is_fixed_asset_schedule'] = 1;
            $validated['asset_code'] = $request->asset_code;
            $validated['depreciation_rate'] = $request->depreciation_rate;
        }

        if ($request->asset_type == 'is_subtype') {
            $validated['is_subtype'] = 1;
            $validated['subtype_id'] = $request->subtype_id;
        }
        if ($request->asset_type == 'is_cash') {
            $validated['is_cash_nature'] = 1;
        }
        if ($request->asset_type == 'is_bank') {
            $validated['is_bank_nature'] = 1;
        }

        if (($request->head_level == 4) && (($request->acc_type_id == 4) || ($request->acc_type_id == 5))) {
            $validated['dep_code'] = $request->dep_code;
        }
        if ((($request->head_level == 3) || ($request->head_level == 4))){
            $validated['note_no'] = $request->note_no;
            if ($request->wallet_status == 'is_wallet') {
                $validated['is_wallet'] = 1;
            }else {
                $validated['is_wallet'] = 0;
            }
        }
        AccCoa::create($validated);

        return redirect()->route('account.create')->with('success', __('language.data_save'));
    }

    public function show($coa)
    {

        $data = AccCoa::where('id', $coa)->first();

        return response()->json([
            'coaDetail' => $data,
        ]);

    }


    public function edit($coa)
    {
        $coa = AccCoa::where('id', $coa)->first();

        $lablearray = [];
        for ((int) $i = 1; $i < (int) $coa->head_level; $i++) {
            array_push($lablearray, $i);
        }

        return response()->json([
            'coaDetail' => $coa,
            'coaDropDown' => AccCoa::whereIn('head_level', $lablearray)->where('acc_type_id', $coa->acc_type_id)->where('is_active', 1)->get(),
        ]);
    }

    public function update(Request $request)
    {
        if ($request->has('current_head_level')) {
            $validated = $request->validate([
                'account_name' => 'required',
                'id' => 'required',
                'current_head_level' => 'required',
            ]);

            $accData = AccCoa::findOrFail($request->id);
            $accData->account_name = $request->account_name;
            $accData->save();

            return redirect()->route('account.create')->with('update', __('language.data_update'));
        }

        $validated = $request->validate([
            'account_name' => 'required',
            'parent_id' => 'required',
            'is_active' => 'required',
        ]);

        $GetParentCoa = AccCoa::findOrFail($request->parent_id);
        $head_level = (int) $GetParentCoa->head_level + 1;
        $acc_type_id = $GetParentCoa->acc_type_id;
        $validated['acc_type_id'] = $acc_type_id;
        $validated['head_level'] = $head_level;

        if (($acc_type_id == 1) && ($head_level == 3)) {

            if ($request->asset_type == 'is_stock') {
                $validated['is_stock'] = 1;
                $validated['is_fixed_asset_schedule'] = 0;
                $validated['asset_code'] = null;
                $validated['depreciation_rate'] = null;
            }
            if ($request->asset_type == 'is_fixed_asset') {
                $validated['is_stock'] = 0;
                $validated['is_fixed_asset_schedule'] = 1;
                $validated['asset_code'] = null;
                $validated['depreciation_rate'] = null;
            }
        }

        if ((($acc_type_id == 4) || ($acc_type_id == 5)) && ($head_level == 3)) {

            if ($request->asset_type == 'is_fixed_asset') {
                $validated['is_fixed_asset_schedule'] = 1;
            } else {
                $validated['is_fixed_asset_schedule'] = 0;
            }
            $validated['asset_code'] = null;
            $validated['depreciation_rate'] = null;
            $validated['dep_code'] = null;
        }

        if (($acc_type_id == 1) && ($head_level == 4)) {

            if ($request->asset_type == 'is_cash') {

                $validated['is_cash_nature'] = 1;
                $validated['is_bank_nature'] = 0;
                $validated['is_stock'] = 0;
                $validated['is_subtype'] = 0;
                $validated['subtype_id'] = null;
                $validated['is_fixed_asset_schedule'] = 0;
                $validated['asset_code'] = null;
                $validated['depreciation_rate'] = null;
            }
            if ($request->asset_type == 'is_bank') {

                $validated['is_bank_nature'] = 1;
                $validated['is_cash_nature'] = 0;
                $validated['is_stock'] = 0;
                $validated['is_subtype'] = 0;
                $validated['subtype_id'] = null;
                $validated['is_fixed_asset_schedule'] = 0;
                $validated['asset_code'] = null;
                $validated['depreciation_rate'] = null;
            }

            if ($request->asset_type == 'is_stock') {

                $validated['is_stock'] = 1;
                $validated['is_bank_nature'] = 0;
                $validated['is_cash_nature'] = 0;
                $validated['is_subtype'] = 0;
                $validated['subtype_id'] = null;
                $validated['is_fixed_asset_schedule'] = 0;
                $validated['asset_code'] = null;
                $validated['depreciation_rate'] = null;
            }

            if ($request->asset_type == 'is_fixed_asset') {

                $validated['is_fixed_asset_schedule'] = 1;
                $validated['asset_code'] = $request->asset_code;
                $validated['depreciation_rate'] = $request->depreciation_rate;
                $validated['is_stock'] = 0;
                $validated['is_bank_nature'] = 0;
                $validated['is_cash_nature'] = 0;
                $validated['is_subtype'] = 0;
                $validated['subtype_id'] = null;
            }

            if ($request->asset_type == 'is_subtype') {

                $validated['is_subtype'] = 1;
                $validated['subtype_id'] = $request->subtype_id;
                $validated['is_fixed_asset_schedule'] = 0;
                $validated['asset_code'] = null;
                $validated['depreciation_rate'] = null;
                $validated['is_stock'] = 0;
                $validated['is_bank_nature'] = 0;
                $validated['is_cash_nature'] = 0;
            }
        }

        if ((($acc_type_id == 2) || ($acc_type_id == 3)) && ($head_level == 4)) {

            if ($request->asset_type == 'is_subtype') {
                $validated['is_subtype'] = 1;
                $validated['subtype_id'] = $request->subtype_id;
            } else {
                $validated['is_subtype'] = 0;
                $validated['subtype_id'] = null;
            }
            $validated['asset_code'] = null;
            $validated['depreciation_rate'] = null;
            $validated['dep_code'] = null;

            $validated['is_fixed_asset_schedule'] = 0;
            $validated['depreciation_rate'] = null;
            $validated['is_stock'] = 0;
            $validated['is_bank_nature'] = 0;
            $validated['is_cash_nature'] = 0;
            $validated['note_no'] = $request->note_no;
        }

        if ((($acc_type_id == 4) || ($acc_type_id == 5)) && ($head_level == 4)) {
            if ($request->asset_type == 'is_fixed_asset') {
                $validated['is_fixed_asset_schedule'] = 1;
                $validated['dep_code'] = $request->dep_code;

                $validated['is_subtype'] = 0;
                $validated['subtype_id'] = null;
            }
            if ($request->asset_type == 'is_subtype') {
                $validated['is_subtype'] = 1;
                $validated['subtype_id'] = $request->subtype_id;
                $validated['is_fixed_asset_schedule'] = 0;
                $validated['dep_code'] = null;
            }

            $validated['asset_code'] = null;
            $validated['depreciation_rate'] = null;
            $validated['is_stock'] = 0;
            $validated['is_bank_nature'] = 0;
            $validated['is_cash_nature'] = 0;
            $validated['note_no'] = $request->note_no;
        }

        if ((($head_level == 3) || ($head_level == 4))) {
            $validated['note_no'] = $request->note_no;
            if ($request->wallet_status == 'is_wallet') {
                $validated['is_wallet'] = 1;
            }else {
                $validated['is_wallet'] = 0;
            }
        }

        AccCoa::where('id', $request->id)->update($validated);
        $latestCoaUpdate = AccCoa::where('id', $request->id)->first();
        $value = $this->updateActypeAndTreeLable($latestCoaUpdate);

        return redirect()->route('account.create')->with('update', __('language.data_update'));
    }


    public function destroy(Request $request)
    {
        $id = $request->id;

        // Step 1: Check if the COA or its descendants are used in acc_vouchers
        $voucherCount = DB::table('acc_voucher_details')
            ->where('acc_coa_id', $id)
            ->orWhereIn('acc_coa_id', function ($query) use ($id) {
                $query->select('id')->from('acc_coas')->where('parent_id', $id);
            })
            ->orWhereIn('acc_coa_id', function ($query) use ($id) {
                $query->select('id')->from('acc_coas')
                    ->whereIn('parent_id', function ($query) use ($id) {
                        $query->select('id')->from('acc_coas')->where('parent_id', $id);
                    });
            })
            ->count();

        if ($voucherCount > 0) {
            return redirect()->route('account.create')
                ->with('fail', __('language.data_delete_failed_voucher'));
        }

        // Step 2: Check acc_transactions for references to the COA or its descendants (acc_coa_id or reverse_code)
        $transactionCount = DB::table('acc_transactions')
            ->where('acc_coa_id', $id)
            ->orWhere('reverse_acc_coa_id', $id)
            ->orWhereIn('acc_coa_id', function ($query) use ($id) {
                $query->select('id')->from('acc_coas')->where('parent_id', $id);
            })
            ->orWhereIn('reverse_acc_coa_id', function ($query) use ($id) {
                $query->select('id')->from('acc_coas')->where('parent_id', $id);
            })
            ->orWhereIn('acc_coa_id', function ($query) use ($id) {
                $query->select('id')->from('acc_coas')
                    ->whereIn('parent_id', function ($query) use ($id) {
                        $query->select('id')->from('acc_coas')->where('parent_id', $id);
                    });
            })
            ->orWhereIn('reverse_acc_coa_id', function ($query) use ($id) {
                $query->select('id')->from('acc_coas')
                    ->whereIn('parent_id', function ($query) use ($id) {
                        $query->select('id')->from('acc_coas')->where('parent_id', $id);
                    });
            })
            ->count();

        if ($transactionCount > 0) {
            return redirect()->route('account.create')
                ->with('fail', __('language.data_delete_failed_transaction'));
        }

        // Step 3: Proceed to delete the COA
        AccCoa::destroy($id);

        return redirect()->route('account.create')
            ->with('success', __('language.data_delete'));
    }


    public function updateActypeAndTreeLable($latestCoaUpdate)
    {
        $acc_type_id = $latestCoaUpdate->acc_type_id;
        $FstChildCheck = AccCoa::where('parent_id', $latestCoaUpdate->id)->get();

        if ($FstChildCheck->isNotEmpty()) {

            foreach ($FstChildCheck as $fkey => $fvalue) {

                $fchild['acc_type_id'] = $acc_type_id;
                $fchild['head_level'] = (int) $latestCoaUpdate->head_level + 1;
                AccCoa::where('id', $fvalue->id)->update($fchild);

                $fchild['acc_type_id'] = '';
                $fchild['head_level'] = '';

                $SecondChildCheck = AccCoa::where('parent_id', $fvalue->id)->get();

                if ($SecondChildCheck->isNotEmpty()) {
                    foreach ($SecondChildCheck as $key => $svalue) {

                        $Schild['acc_type_id'] = $acc_type_id;
                        $Schild['head_level'] = (int) $fvalue->head_level + 1;
                        AccCoa::where('id', $svalue->id)->update($Schild);
                        $Schild['acc_type_id'] = '';
                        $Schild['head_level'] = '';

                        $ThirdChildCheck = AccCoa::where('parent_id', $svalue->id)->get();

                        if ($ThirdChildCheck->isNotEmpty()) {

                            foreach ($ThirdChildCheck as $key => $tvalue) {

                                $Tchild['acc_type_id'] = $acc_type_id;
                                $Tchild['head_level'] = (int) $tvalue->head_level + 1;
                                AccCoa::where('id', $tvalue->id)->update($Tchild);
                                $Tchild['acc_type_id'] = '';
                                $Tchild['head_level'] = '';
                            }
                        }
                    }
                }
            }
        } else {

            return true;
        }
    }

    /**
     * Export the specified resource.
     *
     * @param  int  $id
     *
     */
    // public function exportAccCoaToExcel()
    // {
    //     return Excel::download(new AccCoaExport, 'COA.xls');
    // }

        public function importAccCoaFromExcel(Request $request)
        {
            try {
                $csvFormat = $request->input('csv_format');

                $request->validate([
                    'upload_csv_file' => 'required|mimes:xlsx,xls,csv,txt|max:51200', // CSV support added
                ]);

                $filePath = $request->file('upload_csv_file')->getPathname();

                if ($csvFormat != 1) {
                    require_once base_path('vendor/shuchkin/simplexlsx/src/SimpleXLSX.php');

                    $xlsx = \Shuchkin\SimpleXLSX::parse($filePath);

                    if (!$xlsx) {
                        throw new \Exception('Failed to parse Excel file: ' . \Shuchkin\SimpleXLSX::parseError());
                    }

                    $rows = $xlsx->rows();
                    $headers = array_shift($rows);
                    $formattedArray = array_map(function ($row) use ($headers) {
                        return array_combine($headers, $row);
                    }, $rows);

                } else {
                    // Parse CSV manually
                    $formattedArray = [];
                    if (($handle = fopen($filePath, "r")) !== false) {
                        $headers = fgetcsv($handle);
                        while (($data = fgetcsv($handle)) !== false) {
                            if (count($data) === count($headers)) {
                                $formattedArray[] = array_combine($headers, $data);
                            }
                        }
                        fclose($handle);
                    }

                    if (empty($formattedArray)) {
                        throw new \Exception('CSV file parsed but no rows were found.');
                    }
                }

                // Return data as JSON for testing
                return response()->json($formattedArray, 200);

                // Uncomment for DB insert:
                // foreach ($formattedArray as $row) {
                //     DB::table('coa_final')->insert($row);
                // }

                // return redirect()->route('account.create')->with('success', __('language.data_imported'));
            } catch (\Exception $e) {
                // You can also log the error if needed
                \Log::error('Import error: ' . $e->getMessage());

                return redirect()->back()->with('exception', 'Import failed: ' . $e->getMessage());
            }
        }
}