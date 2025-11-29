@extends('backend.layouts.app')
@section('title', __('language.monthly_installment_report'))
@push('css')
@endpush
@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fs-17 fw-semi-bold mb-0">{{ __('language.monthly_installment_report') }}</h6>
            </div>
            <div class="text-end">
                <div class="actions">
                    @can('accounts.read')
                        <div class="form-group">
                            <a href="{{ route('installments.index') }}" class="btn btn-primary btn-md pull-right">
                                <i class="fa fa-file" aria-hidden="true"></i> {{ __('language.installment') }}
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
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
                <tfoot id="ledger-footer">
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(function() {
    $('#export-excel').on('click', function() {
        exportTableToExcel('table-export', 'Installment-Monthly-Report.xlsx');
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

        // Validate inputs
        if (!dateRange) {
            alert("Please select date range.");
            return;
        }

        let [fromDate, toDate] = dateRange.split(' - ');
        var installmentDetailUrl = "{{ route('installments.show', ['installment' => 'RECORD_ID']) }}";
        $.ajax({
            url: "{{ route('get-installment-report') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                from_date: fromDate,
                to_date: toDate,
            },
            beforeSend: function () {
                $('#ledger-head').html('');
                $('#ledger-body').html('<tr class="text-center"><td colspan="50">Loading...</td></tr>');
            },
            success: function (response) {
            // Helper function to format headers
            const formatHeader = (key) => {
                if (typeof key !== 'string') return '';
                return key
                    .replace(/_/g, ' ')
                    .replace(/\b\w/g, char => char.toUpperCase());
            };

            if (response.data && response.data.length > 0) {
                $('#export-excel').removeClass('d-none');
                let data = response.data;

                // Filter out the 'id' column from the display
                let displayKeys = Object.keys(data[0]).filter(key => key !== 'id');

                // --- Build Header ---
                let headerRow = '<tr>';
                displayKeys.forEach(col => {
                    headerRow += `<th>${formatHeader(col)}</th>`;
                });
                headerRow += '</tr>';
                $('#ledger-head').html(headerRow);

                // --- Initialize Total Variables ---
                let totalInstallment = 0;
                let totalAdjustment = 0;

                // --- Build Body ---
                let bodyHtml = '';
                data.forEach(row => {
                    // ✅ Add to totals in each iteration
                    // Use parseFloat and || 0 to prevent errors with non-numeric data
                    totalInstallment += parseFloat(row.installment_amount) || 0;
                    totalAdjustment += parseFloat(row.adjustment_amount) || 0;

                    bodyHtml += '<tr>';
                    displayKeys.forEach(col => {
                        let val = row[col] ?? '';
                        
                        // Your existing logic for creating links
                        if (col === 'installment_name') { // Assuming this is the column to link
                            let url = installmentDetailUrl.replace('RECORD_ID', row.id);
                            bodyHtml += `<td><a href="${url}" target="_blank">${val}</a></td>`;
                        } else {
                            let align = !isNaN(parseFloat(val)) && isFinite(val) ? 'text-end' : 'text-center';
                            bodyHtml += `<td class="${align}">${val}</td>`;
                        }
                    });
                    bodyHtml += '</tr>';
                });
                $('#ledger-body').html(bodyHtml);
                
                // --- ✅ Build Footer Row ---
                // Find the position of the first column we are summing
                const firstTotalColIndex = displayKeys.indexOf('installment_amount');

                let footerHtml = '<tr>';
                // Add empty cells up to the cell before the "Total:" label
                for (let i = 0; i < firstTotalColIndex - 1; i++) {
                    footerHtml += '<td></td>';
                }

                // Add the "Total:" label, aligned to the right
                footerHtml += '<td class="text-end fw-bold">Total:</td>';
                
                // Add the calculated totals, formatted to 2 decimal places
                footerHtml += `<td class="text-end fw-bold">${totalInstallment.toFixed(2)}</td>`;
                footerHtml += `<td class="text-end fw-bold">${totalAdjustment.toFixed(2)}</td>`;

                // Add any remaining empty cells to keep the table structure
                const remainingCols = displayKeys.length - (firstTotalColIndex + 2); // +2 for the two summed columns
                for (let i = 0; i < remainingCols; i++) {
                    footerHtml += '<td></td>';
                }

                footerHtml += '</tr>';
                
                // Inject the footer row into the <tfoot> element
                $('#ledger-footer').html(footerHtml);

            } else {
                // Clear all parts of the table if no data
                $('#ledger-head').html('');
                $('#ledger-body').html('<tr class="text-center"><td colspan="50">No Data Found</td></tr>');
                $('#ledger-footer').html(''); // Also clear the footer
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