<?php

namespace Modules\Accounts\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Accounts\Entities\AccCoa;
use Modules\Wallet\Entities\WalletUser;
use Modules\Wallet\Entities\WalletAdvance;
use Modules\HumanResource\Entities\Employee;
use Modules\Wallet\Entities\WalletUsersTransaction;
use Modules\Accounts\Http\DataTables\AccWalletUsersTransactionDataTable;

class AccWalletUsersTransactionController extends Controller
{
    
    public function index(AccWalletUsersTransactionDataTable $dataTable)
    {
        return $dataTable->render('accounts::wallet_user_transaction.list');
    }

    public function create()
    {
        $login_user=WalletUser::where('user_id',auth()->id())->first(['id','wallet_user_name']);
        $users = WalletUser::all();
        $headuser = WalletUser::where('user_id',auth()->id())->first(['is_headuser']);
        $employees = Employee::where('status',1)->get();
        $projects = $login_user->walletUserProjectPermissions->map(function ($permission) {
            return $permission->project;
        })->unique('id');
        $allheads = AccCoa::where('head_level', 4)->where('is_cash_nature', 1)->orWhere('is_bank_nature', 1)->where('is_active', 1)->whereNull('deleted_at')->get();
        $bill_number=$this->generateTransactionNo();
        return view('accounts::wallet_user_transaction.transfer',compact('bill_number','users','login_user','allheads','headuser','employees','projects'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'transfer_type' => 'required',
            'amount' => 'required',
            'posting_date' => 'required',

        ]);
        DB::beginTransaction();
        try {
        $transaction_no=$this->generateTransactionNo();
        $subcode = DB::table('acc_subcode')
                ->where('subTypeID',7)
                ->where('refCode', $request->to_wallet_users_id)
                ->first();
        

        $cash_amount = $request->to_transaction_method === 'cash' ? $request->amount : 0;
        $bank_amount = $request->to_transaction_method === 'bank' ? $request->amount : 0;
        $payment_type = $request->to_transaction_method;
        

        $data = [
            'transaction_id'        => $transaction_no,
            'transaction_type'      => $request->transaction_type, // Always "Transfer"
            'transfer_type'         => $request->transfer_type,
            // 'from_wallet_users_id'  => $request->from_wallet_users_id,
            'to_wallet_users_id'    => $request->to_wallet_users_id,
            'account_type_coa_id'   => $request->acc_coas_id ?? null,
            'posting_date'          => $request->posting_date,
            'transaction_status'    => 'Pending', // Default status
            'amount'                => $request->amount,
            'cash_amount'           => $cash_amount,
            'bank_amount'           => $bank_amount,
            'payment_type'          => $payment_type,
            'narration'             => $request->narration,
            'created_by'            => auth()->id(),
            'voucher_event_code'    => 'WCR',

            // Conditional assignments
            'employee_id'           => null,
            'project_id'            => null,
            'expenses_id'           => null,
            'subcode_id'            => $subcode->id ?? null,
        ];
        $master=WalletUsersTransaction::create($data);

        DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', $e->getMessage());
        }
        
        toastr()->success('Wallet User Transaction added successfully :)', 'Success');
        return redirect()->route('accounts.wallet.user_transaction.index');
        
    }


    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $transaction = WalletUsersTransaction::findOrFail($id);
            $transaction->transaction_status = 'Received';
            $transaction->save();

            $tr_info=WalletUsersTransaction::where('transaction_id', $transaction->transaction_id)
                ->where('transfer_type', 'balance_transfer')
                ->where('transaction_status', 'Sent Request')
                ->first();

            $tr_info->update(['transaction_status' => 'Accepted']);
            DB::commit();
            
            if (!empty($tr_info)) {
                $msg=voucher_posting($tr_info->id, 'WCT', 0);
                toastr()->success($msg, 'Success'); 
            }

            // Return JSON response for AJAX success handler
            return response()->json([
                'success' => true,
                'message' => 'Transaction approved successfully :)'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
            'success' => true,
            'message' => $e->getMessage()
        ]);
        }
    }


    public function generateTransactionNo()
    {
        $transaction_no = WalletUsersTransaction::max('transaction_id');

        if ($transaction_no != '' && $transaction_no != null) {
            $transaction_no = $transaction_no + 1;
        } else {
            $transaction_no = 1000;
        }

        return $transaction_no;
    }

    public function details($id)
    {
        $data = WalletUsersTransaction::findOrFail($id);

        return view('accounts::wallet_user_transaction.modal.view', compact('data'));
    }
    public function getAccCoaBalance(Request $request)
    {
        $id=$request->id;
        $branch_id=0;
        $from_date=date('Y-m-d');
        $to_date=date('Y-m-d');
        DB::select("CALL GetLedger_OTCB(?,?,?,?, @op_balance,@cl_balance,@tr_balance)", [$branch_id,$id, $from_date, $to_date]);
		$result = DB::selectOne("SELECT @op_balance as op_balance,@cl_balance AS cl_balance")->cl_balance;
        return response()->json([
                'success' => true,
                'result' => $result??0.00
            ]);

    }

}
