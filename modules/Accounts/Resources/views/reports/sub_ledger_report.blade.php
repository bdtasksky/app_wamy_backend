@extends('backend.layouts.app')
@section('title', __('language.sub_ledger'))
@push('css')
    <style>
        .select2-selection--multiple {
            max-height: 300px;
            overflow-y: auto !important;
        }

        @media (min-width: 768px) {
            .widthCustom {
                flex: 0 0 auto;
                width: 11.6666666667%;
            }
        }

        @media (min-width: 768px) {
            .widthCustom {
                flex: 0 0 auto;
                width: 12.6666666667%;
            }
        }
    </style>
@endpush
@section('content')
    <div class="row mb-5">
        <div class="col-sm-12 col-md-12">
            @include('accounts::reports.financial_report_header')
            <div class="card fixed-tab-body">
                <div class="card-header">
                    <div class="card-title">
                        <h5>{{ __('language.sub_ledger') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row form-inline">

                        <div class="col-md-2 form-group form-group-new empdropdown widthCustom2">
                            <label for="subtype_id">{{ __('language.subtype') }} <b class="text-danger">*</b>:</label>
                            <select class="form-control select-basic-single select2-hidden-accessible" name="subtype_id"
                                id="subtype_id">
                                <option value="">{{ __('language.select_one') }}</option>
                                @foreach ($subtypes as $subtype)
                                    <option value="{{ $subtype->id }}">{{ $subtype->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 form-group form-group-new empdropdown widthCustom">
                            <label for="acc_coa_id">{{ __('language.coa_head') }} <b class="text-danger">*</b>:</label>
                            <select class="form-control select-basic-single select2-hidden-accessible" name="acc_coa_id"
                                id="acc_coa_id">
                                <option value="">{{ __('language.select_one') }}</option>
                            </select>
                        </div>

                        <div class="col-md-2 form-group form-group-new empdropdown widthCustom">
                            <label for="acc_subcode_id">{{ __('language.subcode') }} <b class="text-danger">*</b> :</label>
                            <select class="form-control select-basic-single select2-hidden-accessible"
                                name="acc_subcode_id[]" id="acc_subcode_id">
                                <option value="" disabled>{{ __('language.select_one') }}</option>
                            </select>
                        </div>


                        <div class="col-md-1 form-group form-group-new empdropdown">
                            <label for="financial_year">{{ __('language.financial_year') }} :</label>
                            <select id="financial_year" class="form-control select-basic-single select2-hidden-accessible"
                                name="dtpYear">
                                <option value="">{{ __('language.select_financial_year') }}</option>
                                @foreach ($financial_years as $year)
                                    <option value="{{ $year->title }}" data-start_date="{{ $year->start_date }}"
                                        data-end_date="{{ $year->end_date }}"
                                        @if (isset($financialyears) && $financialyears->title == $year->title) selected @endif>
                                        {{ $year->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1 form-group form-group-new widthCustom2">
                            <label for="from_date">{{ __('language.from_date') }} :</label>
                            <input type="text" value="{{ isset($financialyears) ? $financialyears->start_date : '' }}"
                                class="form-control" id="from_date" />
                        </div>

                        <div class="col-md-1 form-group form-group-new widthCustom2">
                            <label for="to_date">{{ __('language.to_date') }} :</label>
                            <input type="text" class="form-control"
                                value="{{ isset($financialyears) ? $financialyears->end_date : '' }}" id="to_date" />
                        </div>

                        <div class="col-md-1 form-group form-group-new empdropdown">
                            <label for="row">{{ __('language.row') }} :</label>
                            <select id="row" class="form-control" name="row">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                                <option value="-1">{{ __('language.all') }}</option>
                            </select>
                        </div>
                        
                        <div class="col-md-1 form-group form-group-new empdropdown" style="width: 6.666667%;">
                            <button type="button" style="margin-top:22%" class="btn btn-success"
                                onclick="getSubLedger()">{{ __('language.search') }}</button>
                        </div>

                        {{-- <div class="col-md-2 form-group form-group-new empdropdown">
                            <button type="button" class="btn btn-primary text-wrap" style="margin-top:8%;"
                                onclick="hideZeroBalance()">
                                {{ __('language.hide_zero_balance') }}
                            </button>
                        </div> --}}

                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="getSubLedgerReport"></div>
    <!-- Include modal for voucher details -->
    @include('accounts::modal.voucher_details')
@endsection


@push('js')
    <script>
        window.appData = {
            getCoaUrl: "{{ route('account.report.get.coa.from.subtype', ['id' => ':id']) }}",
            voucherDetailsUrl: "{{ route('accounts.voucher.details') }}",
            pdfDeleteUrl: "{{ route('accounts.voucher.pdf.delete') }}",
        };
    </script>
    <script src="{{ module_asset('Accounts/js/reports/sub_ledger_report.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#acc_subcode_id').select2({
                placeholder: "Select one or more",
                allowClear: true,
                multiple: true
            });
        });

        function getSubLedger() {
            const fields = [{
                    id: '#subtype_id',
                    message: "{{ __('language.Please select Sub Type!') }}"
                },
                {
                    id: '#acc_coa_id',
                    message: "{{ __('language.Please select COA Head!') }}"
                },
                {
                    id: '#acc_subcode_id',
                    message: "{{ __('language.Please select Sub Code!') }}"
                }
            ];

            for (const field of fields) {
                if ($(field.id).find(":selected").val() == 0 || $(field.id).find(":selected").val() == "") {
                    alert(field.message);
                    return false;
                }
            }

            // if acc_subcode_id is all, then remove it and select all list of subcodes
            if ($('#acc_subcode_id').find(":selected").val() == 'all') {
                // Deselect 'all'
                $('#acc_subcode_id option[value="all"]').prop('selected', false);

                // Select all other options
                $('#acc_subcode_id option').not('[value="all"]').prop('selected', true);

                // Trigger change event if needed
                $('#acc_subcode_id').trigger('change');
            }

            var subtype_id = $('#subtype_id').find(":selected").val();
            var acc_coa_id = $('#acc_coa_id').find(":selected").val();
            var acc_subcode_id = $('#acc_subcode_id').val();
            var financial_year = $('#financial_year').find(":selected").val();
            var dtpFromDate = $('#from_date').val();
            var dtpToDate = $('#to_date').val();
            var row = $('#row').find(":selected").val();
            var page = 1;
            var branch_id = $('#branch_id').find(":selected").val();


            var csrf = '{{ csrf_token() }}';
            let url = '{{ route('account.report.sub.ledger.search') }}';

            var dataString = {
                subtype_id: subtype_id,
                acc_coa_id: acc_coa_id,
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
                url: url,
                data: dataString,
                success: function(data) {
                    $('#getSubLedgerReport').html(data);
                },
                error: function(xhr, status, error) {
                    console.error("Error occurred:", error);
                }
            });
        }


        function changePage(pageNo) {

            var csrf = '{{ csrf_token() }}';
            let url = "{{ route('account.report.sub.ledger.search') }}";
            var subtype_id = $('#subtype_id').find(":selected").val();
            var acc_coa_id = $('#acc_coa_id').find(":selected").val();
            var acc_subcode_id = $('#acc_subcode_id').val();
            var financial_year = $('#financial_year').find(":selected").val();
            var dtpFromDate = $('#from_date').val();
            var dtpToDate = $('#to_date').val();
            var row = $('#row').find(":selected").val();
            var page = 1;
            var branch_id = $('#branch_id').find(":selected").val();


            var dataString = {
                subtype_id: subtype_id,
                acc_coa_id: acc_coa_id,
                acc_subcode_id: acc_subcode_id,
                dtpYear: financial_year,
                dtpFromDate: dtpFromDate,
                dtpToDate: dtpToDate,
                row: row,
                page: pageNo,
                branch_id: branch_id,
                csrf_test_name: csrf,
            };

            $.ajax({
                type: "POST",
                url: url,
                data: dataString,
                headers: {
                    'X-CSRF-TOKEN': csrf // Send CSRF token in the headers
                },
                success: function(data) {
                    $('#getSubLedgerReport').html(data);
                },
                error: function(xhr, status, error) {
                    console.error("Error occurred:", error);
                }
            });
        }

        function hideZeroBalance() {
            // .closing_balance tr hide if value is 0 or 0.00 or 0.000
            $('.closing_balance').each(function() {
                if ($(this).text() == 0 || $(this).text() == '0.00' || $(this).text() == '0.000') {
                    $(this).parent().hide();
                } else {
                    $(this).parent().show();
                }
            });
        }
    </script>
@endpush
