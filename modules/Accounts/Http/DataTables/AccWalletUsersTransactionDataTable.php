<?php

namespace Modules\Accounts\Http\DataTables;

use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Modules\Wallet\Entities\WalletUsersTransaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class AccWalletUsersTransactionDataTable extends DataTable {


    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('posting_date', function ($data) {

                return \Carbon\Carbon::parse($data->posting_date)->format('d/m/Y');
                
            })

            
            ->editColumn('to_wallet_user', function ($data) {

                return ($data->to_wallet_user?$data->to_wallet_user->wallet_user_name:'Accounts HO');
                
            })

            ->editColumn('amount', function ($data) {
                return '<span class="text_yellow">' . ($data->amount ? bt_number_format($data->amount) : '0.000') . '</span>';
            })
            
            ->editColumn('transaction_status', function ($data) {
                return $data->transaction_status == 'Pending' ? '<span class="px-3 py-2 mb-0 d-inline-block text-center rounded-10 bg-yellow-light">'.$data->transaction_status.'</span>' : '<span class="px-3 py-2 mb-0 d-inline-block text-center text_green rounded-10 bg-green-light">'.$data->transaction_status.'</span>';
            })

            ->addColumn('action', function ($data) {
                $actions = '';
                
                    $actions .= '<div class="d-flex gap-2">';

                    $actions .= '<a href="#" class="btn px-2 py-2 d-flex align-items-center fw-semi-bold gap-1 text_yellow rounded-10 bg-ash-light show_balance" title="'.__('language.view').'" onclick="detailsView('.$data->id.')" id="detailsView-'.$data->id.'" data-url="'.route('accounts.wallet.transaction.details', $data->id).'">
                        <svg width="18" height="12" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.8556 5.6C17.7273 5.4 13.877 0 9 0C4.12299 0 0.272727 5.4 0.144385 5.6C-0.0481283 5.86667 -0.0481283 6.13333 0.144385 6.4C0.272727 6.6 4.12299 12 9 12C13.877 12 17.7273 6.6 17.8556 6.4C18.0481 6.13333 18.0481 5.86667 17.8556 5.6ZM9 9.33333C7.20321 9.33333 5.79144 7.86667 5.79144 6C5.79144 4.13333 7.20321 2.66667 9 2.66667C10.7968 2.66667 12.2086 4.13333 12.2086 6C12.2086 7.86667 10.7968 9.33333 9 9.33333Z" fill="#FEAE45" />
                        </svg>
                        View
                    </a>';

                    $actions .= '</div>';

                return $actions;
            })

            ->rawColumns(['amount','transaction_status', 'action']);

    }


    public function query(WalletUsersTransaction $model): QueryBuilder
    {
       return $model->newQuery()
        ->whereNull('from_wallet_users_id')
        ->where('transfer_type', 'head_office_accounts');
    }


    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('wallet-users-transaction-table')
            ->setTableAttribute('class', 'dataTable table table-borderless new-custom-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->language([
                'processing' => '<div class="lds-spinner">
                <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>',
            ])
            ->selectStyleSingle()
            ->lengthMenu([[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']])
            ->dom("<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>rt<'bottom'<'row'<'col-md-6'i><'col-md-6'p>>><'clear'>")
            ->buttons([]);
    }


    public function getColumns(): array
    {
        return [

            Column::make('posting_date')
                ->title(__('language.date'))
                ->searchable(true),
            Column::make('transaction_id')
                ->title(__('language.transaction_no'))
                ->searchable(true),

            Column::make('transaction_type')
                ->title(__('language.transaction_type'))
                ->searchable(true),



            Column::make('to_wallet_user')
                ->title('Wallet User')
                ->searchable(true),

            Column::make('cash_amount')
                ->title(__('language.cash'))
                ->searchable(true),
             Column::make('bank_amount')
                ->title(__('language.bank'))
                ->searchable(true),
            Column::make('amount')
                ->title(__('language.amount'))
                ->searchable(true), 
            Column::make('narration')
                    ->title(__('language.remarks'))
                    ->searchable(true),
            Column::make('transaction_status')
                ->title(__('language.status'))
                ->searchable(true),
                     

            Column::make('action')
                
                ->title(__('language.action'))
                ->searchable(false)
                ->printable(false)
                ->exportable(false),
        ];
    }

    protected function filename(): string
    {
        return 'walletusertransfir-'.date('YmdHis');
    }
}
