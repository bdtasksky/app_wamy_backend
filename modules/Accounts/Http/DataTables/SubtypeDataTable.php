<?php

namespace Modules\Accounts\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\Accounts\Entities\AccSubtype;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SubtypeDataTable extends DataTable
{
   
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                if($row->status == 0){
                    return '<span class="badge bg-success">'.__('language.active').'</span>';
                }else{
                    return '<span class="badge bg-danger">'.__('language.inactive').'</span>';
                }
            })
            ->addColumn('isSystem', function ($row) {
                if($row->isSystem == 1){
                    return '<span class="badge bg-success">Yes</span>';
                }else{
                    return '<span class="badge bg-danger">No</span>';
                }
            })
            ->addColumn('action', function ($row) {

                    $button = '';

                    if (auth()->user()->can('update_subtype', $row) && $row->isSystem == 0) {
                      $button .= '<a href="#" class="btn btn-primary-soft btn-sm me-1 edit-subtype" data-url="'.route('subtypes.edit',$row->id).'" title="Edit"><i class="fa fa-edit"></i></a>';
                    }
                    if($row->id > 3){
                        if (auth()->user()->can('delete_subtype', $row) && $row->isSystem == 0) {
                            $button .= '<a href="javascript:void(0)" class="btn btn-danger-soft btn-sm delete-confirm" data-bs-toggle="tooltip" title="Delete" data-route="'. route('subtypes.destroy',$row->id) .'" data-csrf="'.csrf_token().'"><i class="fa fa-trash"></i></a>';
                        }
                    }
                    return $button;

            })

            ->rawColumns(['isSystem','status', 'action']);

    }

  
    public function query(AccSubtype $model): QueryBuilder
    {
        return $model->newQuery()
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
            //addIndexColumn is added here
            Column::make('DT_RowIndex')
                ->title(__('language.sl'))
                ->addClass('text-center column-sl')
                ->searchable(false)
                ->orderable(false)
                ->width(10),
            Column::make('name')
                ->title(__('language.name'))
                ->searchable(true),
            Column::make('isSystem')
                ->title(__('language.is_system'))
                ->addClass('text-center')
                ->width(100)
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
