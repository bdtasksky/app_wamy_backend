@extends('backend.layouts.app')

@section('title', __('language.cash_flow_report'))
@push('css')
<link rel="stylesheet" href="{{ module_asset('HumanResource/Resources/assets/css/fees_report.css?v_' . date('h_i')) }}">
<style>
    @media screen and (max-width: 1400px) {
        .table td, .table th {
            padding: 8px 5px !important;
            font-size: 10px !important;
      
        }
        .filter-form{
            font-size: 10px !important;
        }
        .filter-form .form-control{
            font-size: 10px !important;
                line-height: 25px !important;
        }
        .card-body{
            padding: 12px 25px !important;
        }
    }
</style>
@endpush
@section('content')

    <div class="card mb-4">
        @include('backend.layouts.common.validation')
        @include('backend.layouts.common.message')

        <div class="card-header py-2 py-xxl-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-17 fw-semi-bold mb-0">{{ __('language.cash_flow_report') }}</h6>
                </div>
                {{-- <div class="text-end">
                    <div class="actions">
                        
                    </div>
                </div> --}}
            </div>
        </div>
        <div class="card-body">
            <div class="filter-form">
                <form class="row g-3 validateForm" action="" method="GET">
                    @csrf
                    
                    <div class="col-md-3 col-xl-3 col-12">
                        <label for="date">{{ __('language.date') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control balance-date-range" id="date" name="date"
                            value="" required>

                        @if ($errors->has('date'))
                            <div class="error text-danger m-2">{{ $errors->first('date') }}</div>
                        @endif
                    </div>
                    <div class="col-md-2 col-xl-3 col-12">
                        <label for="date">{{ __('language.type') }} <span class="text-danger">*</span></label>
                        <select id="type" class="select-basic-single" required>
                            <option value="" selected disabled>{{__('language.select_one')}}</option>
                            <option value="0">Is Yearly</option>
                            <option value="1">Is Monthly</option>
                        </select>
                    </div>
                    <div class="col-md-2 align-self-end">
                        <button type="submit" name="filter" id="filter"
                            class="btn btn-success btnSubmit">{{ __('language.find') }}</button>
                        <button type="reset" class="btn btn-danger page-reload">{{ __('language.reset') }}</button>
                        <button type="button" id="export-excel" class="btn btn-info d-none">Export Excel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card mb-4 font-arial" id="print-table">
        <div class="card-body card-body-customize">
            <div class="table-responsive">
                <table class="table display table-bordered align-middle" id="table-export">
                    <thead id="ledger-head" class="text-center"></thead>
                    <tbody id="ledger-body" class="text-center"></tbody>
                </table>
            </div>

        </div>
       
    </div>
@endsection
@push('js')
<script>
$(function() {
    $('#export-excel').on('click', function() {
        exportTableToExcel('table-export', 'Cash-Flow-Report.xlsx');
    });
});
$(document).ready(function () {
    var start = moment().subtract(30, 'days');
    var end = moment();

    function date_range(start, end) {
        $('#date').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
    }

    // ✅ Date range picker setup
    $('.balance-date-range').daterangepicker({
        startDate: start,
        endDate: end,
        locale: { format: 'MM/DD/YYYY' },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, date_range);

    date_range(start, end);

    // ✅ Filter button click
    $('#filter').on('click', function (e) {
        e.preventDefault();

        // Get user input
        let dateRange = $('#date').val();
        let type = $('#type').val();

        // Validate inputs
        if (!type || !dateRange) {
            alert("Please select type and date range.");
            return;
        }

        let [fromDate, toDate] = dateRange.split(' - ');

        $.ajax({
            url: "{{ route('account.report.cash.flow.report.search') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                type: type,
                from_date: fromDate,
                to_date: toDate,
            },
            beforeSend: function () {
                $('#ledger-head').html('');
                $('#ledger-body').html('<tr class="text-center"><td colspan="50">Loading...</td></tr>');
            },
            success: function (response) {
                if (response.data.length > 0) {
                    $('#export-excel').removeClass('d-none');
                    let data = response.data;

                    // ✅ Get all column names dynamically from first row, excluding employee_id
                    let allKeys = Object.keys(data[0]).filter(col => col !== "employee_id");

                    // ✅ Build single header row
                    let headerRow = '<tr>';
                    allKeys.forEach(col => {
                        headerRow += `<th>${col}</th>`;
                    });
                    headerRow += '</tr>';
                    $('#ledger-head').html(headerRow);

                    // ✅ Build table body
                    let bodyHtml = '';
                    data.forEach(row => {
                        bodyHtml += '<tr>';
                        allKeys.forEach(col => {
                            let val = row[col] ?? '';
                            let align = !isNaN(parseFloat(val)) ? 'text-end' : 'text-start';
                            bodyHtml += `<td class="${align}">${val}</td>`;
                        });
                        bodyHtml += '</tr>';
                    });
                    $('#ledger-body').html(bodyHtml);
                } else {
                    $('#ledger-head').html('');
                    $('#ledger-body').html('<tr class="text-center"><td colspan="50">No Data Found</td></tr>');
                     $('#export-excel').addClass('d-none');
                }
            },
            error: function () {
                alert("An error occurred while fetching the report.");
                $('#ledger-body').html('<tr class="text-center"><td colspan="50">Error Loading Data</td></tr>');
            }
        });
    });
});
</script>
@endpush
