<?php

namespace Modules\Accounts\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\Accounts\Entities\AccVoucher;
use Modules\Accounts\Entities\AccVoucherHead;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PendingVoucherDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            ->addIndexColumn()

            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" name="voucher_checkbox[]" class="approvalCheckbox" value="' . $row->id . '">';
            })
            //edit column v_no make it anchor tag

            ->editColumn('v_no', function ($row) {
                return '<a class="vNod" onClick="return showVaucherDetail(' . $row->id . ', ' . $row->acc_voucher_type_id . ');" title="View Voucher">' . $row->v_no . '</a>';
            })

            ->filterColumn('v_no_search', function ($row, $keyword) {
                $row->where('v_no', 'like', "%{$keyword}%");
            })

            ->filterColumn('remarks_search', function ($row, $keyword) {
                $row->where('remars', 'like', "%{$keyword}%");
            })

            ->editColumn('debit', function ($row) {
                $totalDebit = total_debit($row->v_no) ?? 0;
                return '<div class="text-end">' . $totalDebit . '</div>';
            })

            ->editColumn('credit', function ($row) {
                $totalCredit = total_credit($row->v_no) ?? 0;
                return '<div class="text-end">' . $totalCredit . '</div>';
            })

            ->editColumn('branch', function ($row) {
                return ($row->branch->branch_name) ?? 'Main Branch';
            })


            ->editColumn('status', function ($row) {

                if ($row->status == 1) {

                    if ($row->branch == NULL) {
                        $sent = '<span class="btn btn-primary btn-sm rounded-pill">Main Branch</span>';
                    } else {
                        $sent = '<span class="btn btn-danger btn-sm rounded-pill">Not Sent</span>';
                    }

                    return $sent;
                }
                // else{

                //     $sent = '<span class="btn btn-danger btn-sm rounded-pill">Not Sent</span>';
                //     return $sent;

                // }
            })

            ->editColumn('is_approved', function ($row) {
                if($row->is_approved == 1){
                    return '<span class="badge bg-success">Approved</span>';
                }else{
                    return '<span class="badge bg-warning">Pending</span>';
                }
            })

            ->addColumn('action', function ($row) {
                $csrf_token = csrf_token();
                $button = '';
                $button .= '<a href="javascript:void(0)" class="btn btn-primary-soft btn-sm me-1 edit-subtype" onClick="return showVaucherDetail(' . $row->id . ', ' . $row->acc_voucher_type_id . ');" title="View Voucher"><i class="fa fa-eye"></i></a>';
                if (auth()->user()->can('create_voucher_approval')) {
                    $button .= '<button data-route="' . route('transaction.make.approve', $row->id) . '" 
                                  data-id="' . $row->id . '" 
                                  data-token="' . $csrf_token . '"
                                  class="btn btn-success-soft btn-sm me-1 approved_voucher_single" 
                                  onclick="approveSingle(this)"
                                  title="Approve">Approve <i class="fa fa-check"></i></button>';
                }
                return $button;
            })



            ->rawColumns(['checkbox', 'v_no_search', 'remarks_search', 'debit', 'credit', 'branch', 'status', 'action', 'is_approved', 'v_no']);
    }

    /**
     * Get query source of dataTable.
     */
    public function query(AccVoucherHead $model): QueryBuilder
    {
        return $model->newQuery()->where('is_approved', 0)->latest();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('voucher-pending-table')
            ->setTableAttribute('class', 'table table-hover table-bordered align-middle')
            ->lengthMenu([[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']])
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->processing(true) // Enable processing indicator
            ->serverSide(true)  // Enable server-side processing
            ->stateSave(true)
            ->selectStyleSingle()
            ->dom("<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>rt<'bottom'<'row'<'col-md-6'i><'col-md-6'p>>><'clear'>")
            ->buttons([]);
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [

            Column::make('DT_RowIndex')
                ->title(__('language.sl'))
                ->addClass('text-center column-sl')
                ->searchable(false)
                ->orderable(false)
                ->width(10),


            Column::computed('checkbox')
                ->title('<input type="checkbox" id="check_all" onclick="selectAll()"> All')
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->width(40),


            Column::make('v_no')
                ->title(__('language.voucher_no'))
                ->searchable(true),
            Column::make('v_date')
                ->title(__('language.date'))
                ->searchable(true),


            Column::make('remarks')
                ->title('Remarks')
                ->searchable(true),


            Column::make('debit')
                ->title(__('language.debit'))
                ->searchable(false),


            Column::make('credit')
                ->title(__('language.credit'))
                ->searchable(false),

            Column::make('branch')
                ->title(__('language.branch'))
                ->searchable(false),

            Column::make('is_approved')
                ->title(__('language.approved_status'))
                ->searchable(false),

            Column::make('status')
                ->title(__('language.branch_status'))
                ->searchable(false),


            Column::make('action')
                ->title(__('language.action'))
                ->searchable(false),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Pending-Voucher-' . date('YmdHis');
    }
}
