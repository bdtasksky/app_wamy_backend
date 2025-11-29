@extends('backend.layouts.app')
@section('title', __('language.chart_of_account'))
@push('css')
<link href="{{ asset('public/backend') }}/assets/plugins/vakata-jstree/dist/themes/default/style.min.css" rel="stylesheet">
<link href="{{module_asset('Accounts/coa/jqueryui/jquery-ui.min.css') }}" rel="stylesheet">
<link href="{{module_asset('Accounts/coa/css/dailog.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ module_asset('Accounts/css/settings_header.css?v_' . date('h_i')) }}">
@endpush

@section('content')



    @include('accounts::settings_header')
    @include('backend.layouts.common.message')
    <div class="card  mb-4 fixed-tab-body">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-17 fw-semi-bold mb-0">{{ __('language.accounts') }}</h6>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-primary" id="importCoa" data-bs-toggle="modal" data-bs-target="#importCoaModal">
                        <i class="fas fa-upload"></i>
                        COA Import
                    </button>

                </div>
            </div>
        </div>



        <div class="card-body">


            @include('accounts::coa.subblade.confirm')
            <div class="row">

                <div class="col-6">
                        <div class="search mb-2">
                             <div class="search__inner tree-search">
                                 <input id="treesearch" type="text" class="form-control search__text" placeholder="Tree Search..." autocomplete="off">
                                 <i class="typcn typcn-zoom-outline search__helper" data-sa-action="search-close"></i>
                             </div>
                        </div>
                    @include('accounts::coa.subblade.coatree')


                </div>
                <div class="col-6">
                    @include('accounts::coa.subblade.coafrom')
                </div>

            </div>



        </div>
        <input type="hidden" id="url" value="{{ url('') }}">
        <input type="hidden" id="accsubType" value="{{json_encode($accSubType) }}">
    </div>
    <div class="modal fade" id="importCoaModal" tabindex="-1" aria-labelledby="importCoaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('account.import-acc-coa') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importCoaModalLabel">Coa Import</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="file" name="upload_csv_file" required>
                        </div>
                        <div class="mb-3">
                            <input type="hidden" name="csv_format" value="0">
                           <input type="checkbox" class="form-check-input" name="csv_format" value="1">
                           <label class="form-check-label" for="file">Is CSV Format</label>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('language.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('language.import') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
@endsection
@push('js')
<script src="{{ asset('public/backend') }}/assets/plugins/vakata-jstree/dist/jstree.min.js?v=1"></script>
<script src="{{ asset('public/backend') }}/assets/dist/js/pages/tree-view.active.js?v=1"></script>
<script src="{{module_asset('Accounts/coa/js/account.js?v=' . date('h_i')) }}"></script>
<script src="{{module_asset('Accounts/coa/jqueryui/jquery-ui.min.js?v=1') }}"></script>
@endpush

