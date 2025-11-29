@extends('backend.layouts.app')
@section('title', __('language.predefined_accounts'))
@push('css')
<link rel="stylesheet" href="{{ module_asset('Accounts/css/settings_header.css?v_' . date('h_i')) }}">
@endpush
@section('content')
@include('accounts::settings_header')
    <input type="hidden" id="csrfhashresarvation" value="{{ csrf_token() }}">
        <div class="card  mb-4 fixed-tab-body">
            <div class="card-title px-4 py-3 d-flex justify-content-between align-items-center">
                <h4>{{ $title }}</h4>
                <div class="btn-group form-inline">
                    <div class="form-group">
                        @can('create_predefine_accounts')
                            <a href="{{ route('accounts.predefined.form') }}" class="btn btn-primary btn-md">
                                <i class="fa fa-plus-circle" aria-hidden="true"></i> {{ __('language.create_predefined') }}
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                @include('backend.layouts.common.validation')
                @include('backend.layouts.common.message')
                <table width="100%" class="table table-striped table-bordered table-hover" id="predefined_list">
                    <thead>
                        <tr>
                            <th>{{ __('language.sl') }}</th>
                            <th>Predefined ID</th>
                            <th>{{ __('language.predefined_name') }}</th>
                            <th>{{ __('language.description') }}</th>
                            <th>{{ __('language.predefined_accounts') }}</th>
                            <th>{{ __('language.coa_head') }}</th>
                            <th>{{ __('language.create_date') }}</th>
                            <th>{{ __('language.status') }}</th>
                            <th>{{ __('language.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- This part will be populated by JavaScript / DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
@endsection
@push('js')
    <script>
        window.appData = {
            getPredefinedSettingList: "{{ route('getPredefinedSettingList') }}",
        };
    </script>

    <script>
        // Define the lang object with the necessary translations
        window.lang = {
            Processingod: "{{ __('language.processing') }}",
            search: "{{ __('language.search') }}",
            sLengthMenu: "{{ __('language.Show _MENU_ entries') }}",
            sInfo: "{{ __('language.Showing _START_ to _END_ of _TOTAL_ entries') }}",
            sInfoEmpty: "{{ __('language.No entries to show') }}",
            sInfoFiltered: "{{ __('language.filtered from _MAX_ total entries') }}",
            sLoadingRecords: "{{ __('language.Loading records...') }}",
            sZeroRecords: "{{ __('language.No records found') }}",
            sEmptyTable: "{{ __('language.No data available in table') }}",
            sFirst: "{{ __('language.First') }}",
            sPrevious: "{{ __('language.previous') }}",
            sNext: "{{ __('language.next') }}",
            sLast: "{{ __('language.last') }}",
            sSortAscending: "{{ __('language.Sort ascending') }}",
            sSortDescending: "{{ __('language.Sort descending') }}",
            _sign: "{{ __('language.sign') }}",
            _0sign: "{{ __('language.No sign') }}",
            _1sign: "{{ __('language.One sign') }}",
            copy: "{{ __('language.Copy') }}",
            csv: "{{ __('language.CSV') }}",
            excel: "{{ __('language.Excel') }}",
            pdf: "{{ __('language.pdf') }}",
            print: "{{ __('language.print') }}",
            colvis: "{{ __('language.Column visibility') }}"
        };
    </script>

    <script src="{{ module_asset('Accounts/js/predefined.js?v=1.2') }}" type="text/javascript"></script>
@endpush
