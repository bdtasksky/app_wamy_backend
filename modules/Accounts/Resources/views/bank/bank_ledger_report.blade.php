@extends('backend.layouts.app')
@section('title', __('language.bank_ledger'))
@push('css')
@endpush
@section('content')

<div class="row">
    <div class="col-sm-12 col-md-12">
        @include('accounts::bank.bank_header')
        <div class="card mb-4 fixed-tab-body">
            <div class="card-header">
                <div class="card-title">
                    <h5>
                        {{ __('language.bank_ledger') }}
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row form-inline g-3 g-xl-2 g-xxl-3 row-cols-1 row-cols-md-2 row-cols-lg-4 row-cols-xl-6">
                    <div class="col form-group form-group-new empdropdown">
                        <label for="employeelist">{{ __('language.transaction_head') }} <b class="text-danger">*</b> :</label>
                        <select class="form-control select-basic-single select2-hidden-accessible" name="cmbCode" id="cmbCode">
                            <option value="">{{ __('language.select_one') }}</option>
                            @foreach ($general_ledger as $g_data)
                            <option value="{{ $g_data->id }}">{{ $g_data->account_name }} @if( $g_data->is_active==0) ({{ __('language.inactive') }}) @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col form-group form-group-new empdropdown">
                        <label for="financial_year">{{ __('language.financial_year') }} :</label>
                        <select id="financial_year" class="form-control select-basic-single select2-hidden-accessible" name="dtpYear">
                            <option value="">Select Financial Year</option>
                            @foreach ($financial_years as $year)
                            <option
                                value="{{ $year->title }}"
                                data-start_date="{{ $year->start_date }}"
                                data-end_date="{{ $year->end_date }}"
                                @if(isset($financialyears) && $financialyears->title == $year->title) selected @endif>
                                {{ $year->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col form-group form-group-new">
                        <label for="from_date">{{ __('language.from_date') }} :</label>
                        <input type="text" value="{{ isset($financialyears) ? $financialyears->start_date : '' }}" class="form-control" id="from_date" />
                    </div>
                    <div class="col form-group form-group-new">
                        <label for="to_date">{{ __('language.to_date') }} :</label>
                        <input type="text" class="form-control" value="{{ isset($financialyears) ? $financialyears->end_date : '' }}" id="to_date" />
                    </div>

                    <!-- Branch Dropdown -->


                    <div class="col form-group form-group-new empdropdown">
                        <label for="row">Rows :</label>
                        <select id="row" class="form-control" name="row">
                            <option value="">Select Row</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                            <option value="-1">All</option>
                        </select>
                    </div>

                    <div class="col form-group form-group-new empdropdown">
                        <button class="mt-4 btn btn-success" onclick="getGeneralLedger()">{{ __('language.search') }}</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="row mt-5" id="getgeneralLedgerreport"></div>
<!-- Include modal for voucher details -->
@include('accounts::modal.voucher_details')
@endsection
@push('js')

<script>
    function getGeneralLedger() {

        var cmbCode = $('#cmbCode').find(":selected").val();

        if (cmbCode == 0 || cmbCode == "") {
            alert("Please select Transaction Head !");
            return false;
        }

        var dtpYear = $('#financial_year').find(":selected").val();
        var dtpFromDate = $('#from_date').val();
        var dtpToDate = $('#to_date').val();
        var row = $('#row').find(":selected").val();
        var branch_id = $('#branch_id').find(":selected").val();
        var page = 1;
        var csrf = '{{ csrf_token() }}';
        var myurl = '{{ route("account.bank.ledger.search") }}';

        var dataString = {
            cmbCode: cmbCode,
            dtpYear: dtpYear,
            dtpFromDate: dtpFromDate,
            dtpToDate: dtpToDate,
            row: row,
            page: page,
            branch_id: branch_id,
            csrf_test_name: csrf
        };

        $.ajax({
            type: "POST",
            url: myurl,
            data: dataString,
            success: function(data) {
                $('#getgeneralLedgerreport').html(data);
            },
            error: function(xhr, status, error) {
                alert('Please refresh the page to continue');
                // window.location.reload();
            }
        });
    }

    function changePage(pageNo) {
        var cmbCode = $('#cmbCode').find(":selected").val();
        var dtpYear = $('#financial_year').find(":selected").val();
        var dtpFromDate = $('#from_date').val();
        var dtpToDate = $('#to_date').val();
        var row = $('#row').find(":selected").val();
        var branch_id = $('#branch_id').find(":selected").val();
        var csrf = '{{ csrf_token() }}';
        var myurl = '{{ route("account.bank.ledger.search") }}';

        var dataString = {
            cmbCode: cmbCode,
            dtpYear: dtpYear,
            dtpFromDate: dtpFromDate,
            dtpToDate: dtpToDate,
            row: row,
            page: pageNo,
            branch_id: branch_id,
            _token: csrf
        };

        $.ajax({
            type: "POST",
            url: myurl,
            data: dataString,
            headers: {
                'X-CSRF-TOKEN': csrf // Send CSRF token in the headers
            },
            success: function(data) {
                $('#getgeneralLedgerreport').html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error occurred:", error);
            }
        });
    }
    "use strict";
    function printDiv() {
        var divName = "printArea";
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
@endpush