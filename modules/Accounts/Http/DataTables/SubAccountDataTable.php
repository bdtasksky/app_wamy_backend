<?php

namespace Modules\Accounts\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\Accounts\Entities\AccSubcode;
use Modules\Accounts\Entities\AccSubtype;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SubAccountDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('sub_type_name', function ($row) {
                return $row?->accSubtype->name??'';
            })
            ->addColumn('status', function ($row) {
                if($row->status == 0){
                    return '<span class="badge bg-success">'.__('language.active').'</span>';
                }else{
                    return '<span class="badge bg-danger">'.__('language.inactive').'</span>';
                }
            })
            ->addColumn('action', function ($row) {

                    $button = '';
                    if (auth()->user()->can('edit_sub_account', $row) && $row->refCode==null) {
                      $button .= '<a href="#" class="btn btn-primary-soft btn-sm me-1 edit-SubCode" data-url="'.route('subcodes.edit',$row->id).'" title="Edit"><i class="fa fa-edit"></i></a>';
                    }

                    if (auth()->user()->can('delete_sub_account', $row) && $row->refCode==null) {
                      $button .= '<a href="javascript:void(0)" class="btn btn-danger-soft btn-sm delete-subcode" data-bs-toggle="tooltip" title="Delete" data-route="'. route('subcodes.destroy',$row->id) .'" data-csrf="'.csrf_token().'"><i class="fa fa-trash"></i></a>';
                    }

                    return $button;

            })

            ->rawColumns(['sub_type_name', 'status', 'action']);

    }

    public function query(AccSubcode $model): QueryBuilder
    {
        return $model->newQuery()
        ->with('accSubType')
        ->latest();

    }

 
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('purchase-return-table')
            ->setTableAttribute('class', 'table table-hover table-bordered align-middle')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->selectStyleSingle()
            ->dom("<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>rt<'bottom'<'row'<'col-md-6'i><'col-md-6'p>>><'clear'>")
            ->buttons([

            ]);
    }

  
    public function getColumns(): array
    {

        return [
            Column::make('DT_RowIndex')
                ->title(__('language.sl'))
                ->addClass('text-center column-sl')
                ->searchable(false)
                ->orderable(false)
                ->width(10),
            Column::make('name')
                ->title(__('language.sub_account_name'))
                ->searchable(true),
            Column::make('sub_type_name')
                ->title(__('language.sub_type_name'))
                ->searchable(true),
            Column::make('status')
                ->title(__('language.status'))
                ->addClass('text-center')
                ->width(100)
                ->searchable(true),
            Column::make('action')
                ->title(__('language.action'))->addClass('column-sl')->orderable(false)
                ->addClass('text-start')
                ->width(50)
                ->searchable(false)
                ->printable(false)
                ->exportable(false),
        ];
    }

    protected function filename(): string
    {
        return 'Purchase_Return_' . date('YmdHis');
    }

}
