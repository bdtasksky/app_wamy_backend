@extends('backend.layouts.app')
@section('title', __('language.opening_balance'))
@push('css')
<link rel="stylesheet" href="{{ module_asset('Accounts/css/settings_header.css?v_' . date('h_i')) }}">
@endpush
@section('content')
    @include('accounts::settings_header')
    <div class="card  mb-4 fixed-tab-body">
        <div class="card-body">
            <div class="card-title">
                <h4 style="padding: 22px 13px 0px 0px;">
                    <span style="text-align:left!important">{{ __('language.opening_balance') }}</span>
                    <div class="btn-group d-flex justify-content-end form-inline">
                        <div class="form-group">
                            @can('create_opening_balance')
                                <a href="{{ route('accounts.opening-balance.form') }}" class="btn btn-primary btn-md pull-right">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    {{ __('language.add_opening_balance') }}
                                </a>
                            @endcan
                        </div>
                        <div class="form-group">
                            <select name="fiyear_id" class="form-control js-basic-single" id="fiyear_id" tabindex="-1">
                                <option value="">{{ __('language.select_financial_year') }}</option>
                                @foreach ($financialyear as $index => $row)
                                    <option value="{{ $row->fiyear_id }}"
                                        {{ $index === count($financialyear) - 1 ? 'selected' : '' }}>
                                        {{ $row->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group form-group-new empdropdown">
                            <select id="row" class="form-control" name="row">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                                <option value="-1">All</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success"
                                onclick="getOpeningBalance()">{{ __('language.search') }}</button>
                            <button class="btn btn-warning" id="filterordlistrst">{{ __('language.reset') }}</button>
                        </div>
                    </div>
                </h4>
            </div>
            <style>
                #opb_list td:nth-child(7),
                #opb_list td:nth-child(8) {
                    text-align: right;
                }
            </style>
            <div class="row" id="getOpeningBalance"></div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function getOpeningBalance() {
            var fiyear_id = $('#fiyear_id').find(":selected").val();
            var row = $('#row').find(":selected").val();
            var page = 1;
            var csrf = '{{ csrf_token() }}'; // Laravel CSRF Token
            var myurl = '{{ route('accounts.opening-balance.get') }}'; // Laravel route

            var dataString = {
                fiyear_id: fiyear_id,
                row: row,
                page: page,
                _token: csrf // Corrected to use '_token' instead of 'csrf_test_name'
            };

            $.ajax({
                type: "POST",
                url: myurl,
                data: dataString,

                success: function(data) {
                    if (data.htmlContent) {
                        $('#getOpeningBalance').html(data.htmlContent);
                    } else {
                        console.log('No HTML content in response');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error occurred:", error);
                }
            });
        }

        $(document).ready(function() {
            getOpeningBalance(); // Auto-load data
        });
    </script>

@endsection
