<?php

namespace Modules\Accounts\Http\DataTables;

use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Modules\Accounts\Entities\AccInstallment;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class AccInstallmentDataTable extends DataTable {
    /**
     * Build DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            ->addColumn('acc_coas_id', function ($accInstallment) {
                return $accInstallment->acc_coa?->account_name??'';
            })


            ->editColumn('is_paid', function ($accInstallment) {
                if($accInstallment->is_paid == 'Paid'){
                    $s='success';
                }elseif($accInstallment->is_paid == 'Processing'){
                    $s='primary';
                }elseif($accInstallment->is_paid == 'Unpaid'){
                    $s='danger';
                }
                return '<span class="badge bg-' . $s  . '">' . $accInstallment->is_paid  . '</span>';
            })

            ->editColumn('is_active', function ($accInstallment) {
                return '<span class="badge bg-' . ($accInstallment->is_active == 1 ? 'success' : 'danger')  . '">' . ($accInstallment->is_active == 1 ? 'Active' : 'Inactive') . '</span>';
            })

            ->editColumn('is_installment_disbursed', function ($accInstallment) {
                if ($accInstallment->is_approve ==1 && $accInstallment->is_installment_disbursed==0) {
                    return '<span class="badge bg-success">Approved</span>';
                }elseif($accInstallment->is_approve ==1 && $accInstallment->is_installment_disbursed==1){
                    return '<span class="badge bg-success">Disbursment</span>';
                }else{
                    return '<span class="badge bg-danger">Pending</span>';
                }
            })
 
            ->addColumn('action', function ($accInstallment) {
                $button = '';
                if ($accInstallment->is_approve==0) {
                    $button .= '<button  id="installmentApprove" data-approve-url="'.route('installments.approve-for-disbusment', $accInstallment->id).'" class="btn btn-info-soft btn-sm me-1" title="Installment Approve"><i class="fa fa-check"></i></button>';
                }
                if ($accInstallment->is_approve ==1 && $accInstallment->voucher_number==Null) {
                    $button .= '<button onclick="approveView('.$accInstallment->id.')" id="approveView-'.$accInstallment->id.'" data-url="'.route('installments.approve', $accInstallment->id).'" class="btn btn-success-soft btn-sm me-1" title="Installment Disbursment"><i class="fa fa-check"></i></button>';
                }

                if (auth()->user()->can(abilities: 'show_installment')) {
                    $button .= '<a href="' . route('installments.show', $accInstallment->id) . '" class="btn btn-success-soft btn-sm me-1" title="View Details"><i class="fa fa-eye"></i></a>';
                }

                $hasPaidDetail = $accInstallment->accInstallmentRecords()->whereIn('status', ['Processing', 'Paid', 'Adjusted'])->exists();
                if(!$hasPaidDetail & $accInstallment->is_paid == 'Unpaid'){
                    if (auth()->user()->can('update_installment') && $accInstallment->is_approve == 0)  {
                        $button .= '<a href="' . route('installments.edit', $accInstallment->id) . '" class="btn btn-success-soft btn-sm me-1" title="Edit"><i class="fa fa-edit"></i></a>';
                    }
    
                    if (auth()->user()->can('delete_installment') && $accInstallment->is_approve == 0) {
                        $button .= '<a href="javascript:void(0)" class="btn btn-danger-soft btn-sm delete-confirm" data-bs-toggle="tooltip" title="Delete" data-route="' . route('installments.destroy', $accInstallment->id) . '" data-csrf="' . csrf_token() . '"><i class="fas fa-trash-alt"></i></a>';
                    }
                }

                return $button;
            })
            ->rawColumns(['is_active', 'is_paid','is_installment_disbursed', 'action']);
    }

    /**
     * Get query source of dataTable.
     */
    public function query(AccInstallment $model, Request $request): QueryBuilder {

        // $employee_id = $request->input('employee_id');
        $q = $model->newQuery();

        // if ($employee_id) {
        //     $q->where('employee_id', $employee_id);
        // }

        return $q;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder {
        return $this->builder()
            ->setTableId('employee-installment-table')
            ->setTableAttribute('class', 'table table-hover table-bordered align-middle')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->language([
                'processing' => '<div class="lds-spinner">
                <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>',
            ])
            ->selectStyleSingle()
            ->lengthMenu([[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']])
            ->dom("<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>rt<'bottom'<'row'<'col-md-6'i><'col-md-6'p>>><'clear'>")
            ->buttons([
                Button::make('csv')
                    ->className('btn btn-secondary buttons-csv buttons-html5 btn-sm prints')
                    ->text('<i class="fa fa-file-csv"></i> CSV'),
                Button::make('excel')
                    ->className('btn btn-secondary buttons-excel buttons-html5 btn-sm prints')
                    ->text('<i class="fa fa-file-excel"></i> Excel'),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array {
        return [

            Column::make('DT_RowIndex')
                ->title(__('language.sl'))
                ->addClass('text-center')
                ->searchable(false)
                ->orderable(false),

            Column::make('installment_type')
                ->title(__('language.installment_type')),
            
            Column::make('acc_coas_id')
                ->title(__('language.coa_head')),

            Column::make('amount')
                ->title(__('language.installment_amount')),

            Column::make('installment')
                ->title(__('language.total_installment')),

            Column::make('installment_amount')
                ->title(__('language.installment_amount')),

            Column::make('installment_cleared')
                ->title(__('language.installment_cleared')),

            Column::make('paid_amount')
                ->title(__('language.paid_amount')),

            Column::make('is_paid')
                ->title(__('language.is_paid')),        

            // Column::make('is_active')
            //     ->title(__('language.is_active')),

            Column::make('is_installment_disbursed')
                ->title(__('language.status')),

            Column::make('action')
                ->title(__('language.action'))->addClass('column-sl')->orderable(false)
                ->searchable(false)
                ->printable(false)
                ->exportable(false)
                ->width(160),
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string {
        return 'accInstallment-' . date('YmdHis');
    }

}
