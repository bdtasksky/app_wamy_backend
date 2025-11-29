@extends('backend.layouts.app')
@section('title', __('language.financial_year_list'))
@push('css')
@endpush
@section('content')

<div id="add0" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <strong>{{ __('Add') }}</strong>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="panel panel-default thumbnail">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <h4>{{ $title }}</h4>
                                </div>
                            </div>
                            <div class="panel-body">
                                <form action="{{ route('subtype.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="name" class="col-sm-4 col-form-label">{{ __('Name') }} <i class="text-danger">*</i></label>
                                        <div class="col-sm-8">
                                            <input name="name" class="form-control" type="text" placeholder="Add {{ __('Name') }}" id="name" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="code" class="col-sm-4 col-form-label">{{ __('Code') }} <i class="text-danger">*</i></label>
                                        <div class="col-sm-8">
                                            <input name="code" class="form-control" type="text" placeholder="Add {{ __('Code') }}" id="code" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="isSystem" class="col-sm-4 col-form-label">{{ __('Is System') }}</label>
                                        <div class="col-sm-8">
                                            <select name="isSystem" id="isSystem" class="form-control">
                                                <option value="" selected>{{ __('Select Option') }}</option>
                                                <option value="1">{{ __('Active') }}</option>
                                                <option value="0">{{ __('Inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group text-end">
                                        <button type="reset" class="btn btn-primary w-md m-b-5">{{ __('Reset') }}</button>
                                        <button type="submit" class="btn btn-success w-md m-b-5">{{ __('Add') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="edit" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <strong>{{ __('Edit') }}</strong>
            </div>
            <div class="modal-body editinfo"></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">

        @include('accounts::header.subcode_header')
        
        <div class="panel panel-default thumbnail">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>{{ $title }}
                        <div class="btn-group pull-right form-inline">
                            <div class="form-group">
                               
                                    <button type="button" class="btn btn-primary btn-md" data-target="#add0" data-toggle="modal">
                                        <i class="fa fa-plus-circle" aria-hidden="true"></i> {{ __('Add') }}
                                    </button>
                                
                            </div>
                        </div>
                    </h4>
                </div>
            </div>
            <div class="panel-body">
                <table width="100%" class="datatable table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Sl') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subtypes as $key => $type)
                            <tr class="{{ $loop->odd ? 'odd gradeX' : 'even gradeC' }}">
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $type->name }}</td>
                                <td>{{ $type->code }}</td>
                                <td class="text-center">
                              
                                        <a onclick="subtypeeditinfo('{{ $type->id }}')" class="btn btn-info btn-sm" data-toggle="tooltip" title="Update">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </a>
                               
                                
                                        <a href="{{ route('subtype.destroy', $type->id) }}" onclick="return confirm('{{ __('Are you sure?') }}')" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                                        </a>
                                 
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">{{ __('No records found') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    "use strict";

    function subtypeeditinfo(id) {
        var geturl = $("#url_" + id).val();
        var myurl = geturl + '/' + id;
        var csrf = '{{ csrf_token() }}';
        var dataString = "id=" + id + "&csrf_test_name=" + csrf;

        $.ajax({
            type: "GET",
            url: myurl,
            data: dataString,
            success: function(data) {
                $('.editinfo').html(data);
                $('#edit').modal({
                    backdrop: 'static',
                    keyboard: false
                }, 'show');
            }
        });
    }
</script>

@endsection
