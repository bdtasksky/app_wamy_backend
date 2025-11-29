@extends('backend.layouts.app')
@section('title', __('language.sub_ledger_merged'))
@push('css')
@endpush
@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12">
        @include('accounts::reports.financial_report_header')

        <div class="card mb-4 fixed-tab-body">
            <div class="card-header">
                <div class="card-title">
                    <h5>{{ __('language.sub_ledger_merged') }}</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row form-inline align-items-end g-3 g-xl-2 g-xxl-3 row-cols-1 row-cols-md-2 row-cols-lg-4 row-cols-xl-6">
                    <!-- Subtype Field -->
                    <div class="col form-group form-group-new empdropdown">
                        <label for="subtype_id">{{ __('language.subtype') }} <b class="text-danger">*</b>:</label>
                        <select class="form-control select-basic-single select2-hidden-accessible" name="subtype_id" id="subtype_id">
                            <option value="">{{ __('language.select_one') }}</option>
                            @foreach ($subtypes as $subtype)
                            <option value="{{ $subtype->id }}">{{ $subtype->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subcode Field -->
                    <div class="col form-group form-group-new empdropdown">
                        <label for="acc_subcode_id">{{ __('language.subcode') }} <b class="text-danger">*</b>:</label>
                        <select class="form-control select-basic-single select2-hidden-accessible" name="acc_subcode_id" id="acc_subcode_id">
                            <option value="">{{ __('language.select_one') }}</option>
                        </select>
                    </div>

                    <!-- Financial Year Field -->
                    <div class="col form-group form-group-new empdropdown">
                        <label for="financial_year">{{ __('language.financial_year') }} :</label>
                        <select id="financial_year" class="form-control select-basic-single select2-hidden-accessible" id="dtpYear">
                            <option value="">{{ __('language.select_financial_year') }}</option>
                            @foreach ($financial_years as $year)
                            <option
                                value="{{ $year->title }}"
                                data-start_date="{{ $year->start_date }}"
                                data-end_date="{{ $year->end_date }}"
                                {{ (isset($financialyears) && $financialyears->title == $year->title) ? 'selected' : '' }}>
                                {{ $year->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Fields -->
                    <div class="col form-group form-group-new">
                        <label for="from_date">{{ __('language.from_date') }} :</label>
                        <input type="text" value="{{ isset($financialyears) ? $financialyears->start_date : '' }}" class="form-control" id="from_date" />
                    </div>
                    <div class="col form-group form-group-new">
                        <label for="to_date">{{ __('language.to_date') }} :</label>
                        <input type="text" class="form-control" value="{{ isset($financialyears) ? $financialyears->end_date : '' }}" id="to_date" />
                    </div>


                    <!-- Branch Dropdown -->



                    <!-- Row Count Field -->
                    <div class="col form-group form-group-new empdropdown">
                        <label for="row">{{ __('language.row') }} :</label>
                        <select id="row" class="form-control" name="row">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                            <option value="-1">All</option>
                        </select>
                    </div>

                    <div class="col form-group form-group-new empdropdown">
                        <button style="margin-top:22%" class="btn btn-success" onclick="getSubLedgerMerged()">{{ __('language.search') }}</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="getSubLedgerMergedReport"></div>
@include('accounts::modal.voucher_details')
@endsection
@push('js')
<script>
    window.appData = {
        getCoaUrl: "{{ route('account.report.get.coa.from.subtype', ['id' => ':id']) }}",
        voucherDetailsUrl: "{{ route('accounts.voucher.details') }}",

    };
</script>
<script src="{{ module_asset('Accounts/js/reports/sub_ledger_merged_report.js') }}"></script>
<script>
    function getSubLedgerMerged() {
        var subtype_id = $('#subtype_id').find(":selected").val();
        var acc_subcode_id = $('#acc_subcode_id').find(":selected").val();

        const fields = [{
                id: '#subtype_id',
                message: "Please select Sub Type!"
            },
            {
                id: '#acc_subcode_id',
                message: "Please select Sub Code!"
            }
        ];

        for (const field of fields) {
            if ($(field.id).find(":selected").val() == 0 || $(field.id).find(":selected").val() == "") {
                alert(field.message);
                return false;
            }
        }

        var financial_year = $('#financial_year').find(":selected").val();
        var dtpFromDate = $('#from_date').val();
        var dtpToDate = $('#to_date').val();
        var row = $('#row').find(":selected").val();
        var page = 1;
        var branch_id = $('#branch_id').find(":selected").val();
        var csrf = '{{ csrf_token() }}';
        var myurl = '{{ route("account.report.sub.ledger.merged.search") }}';

        var dataString = {
            subtype_id: subtype_id,
            acc_subcode_id: acc_subcode_id,
            dtpYear: financial_year,
            dtpFromDate: dtpFromDate,
            dtpToDate: dtpToDate,
            row: row,
            page: page,
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
                $('#getSubLedgerMergedReport').html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error occurred:", error);
            }
        });
    }



    function changePage(pageNo) {

        var subtype_id = $('#subtype_id').find(":selected").val();
        var acc_subcode_id = $('#acc_subcode_id').find(":selected").val();
        var financial_year = $('#financial_year').find(":selected").val();
        var dtpFromDate = $('#from_date').val();
        var dtpToDate = $('#to_date').val();
        var row = $('#row').find(":selected").val();
        var page = 1;
        var branch_id = $('#branch_id').find(":selected").val();
        var csrf = '{{ csrf_token() }}';
        var myurl = '{{ route("account.report.sub.ledger.merged.search") }}';

        var dataString = {
            subtype_id: subtype_id,
            acc_subcode_id: acc_subcode_id,
            dtpYear: financial_year,
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
                $('#getSubLedgerMergedReport').html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error occurred:", error);
            }
        });

    }
</script>
@endpush