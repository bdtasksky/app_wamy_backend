@extends('backend.layouts.app')
@section('title', __('language.sub_account_list'))
@push('css')
<link rel="stylesheet" href="{{ module_asset('Accounts/css/settings_header.css?v_' . date('h_i')) }}">
@endpush
@section('content')


@include('accounts::settings_header')
    @include('backend.layouts.common.validation')
    <div class="card mb-4 fixed-tab-body">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-17 fw-semi-bold mb-0">{{ __('language.sub_account_list')}}</h6>
                </div>
                <div class="text-end">
                    <div class="actions">

                      @can('create_sub_account')
                        <a href="#" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#create-subcode"><i class="fa fa-plus-circle"></i>&nbsp;{{ __('language.add_sub_account')}}</a>
                         @include('accounts::subcode.modal.create')
                      @endcan
                      
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{ $dataTable->table() }}
        </div>


    </div>

    <!--Edit SubCode Modal -->
<div class="modal fade" id="edit-SubCode" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">
             {{ __('language.edit_sub_account') }}
          </h5>
        </div>
        <div id="editSubCodeData">
        </div>
      </div>
    </div>
  </div>

@endsection
@push('js')
{{ $dataTable->scripts(attributes: ['type' => 'module']) }}
<script src="{{ asset('modules/Accounts/Resources/assets/js/subcode.js') }}"></script>
@endpush
