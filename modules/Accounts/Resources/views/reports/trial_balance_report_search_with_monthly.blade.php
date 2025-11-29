@extends('backend.layouts.app')
@section('title', __('language.trial_balance'))
@push('css')
@endpush
@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12">
        @include('accounts::reports.financial_report_header')
        <div class="card fixed-tab-body">
            <div class="card-header">
                <div>
                    <h5>Trial Balance with Monthly</h5>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('account.report.trial.balance.search') }}" method="POST" class="form-inline">
                    @csrf
                    <div class="row">
                        <div class="col-sm-2 form-group form-group-new empdropdown">
                            <label for="dtpFromDate">{{ __("language.financial_year") }} :</label>
                            <select id="financial_year" class="form-control select-basic-single select2-hidden-accessible" name="dtpYear">
                                <option value="">{{ __("language.select_financial_year") }}</option>
                                @foreach ($financial_years as $year)
                                    <option 
                                        value="{{ $year->title }}" 
                                        data-start_date="{{ $year->start_date }}" 
                                        data-end_date="{{ $year->end_date }}"
                                        @if(isset($dtpYear) && $dtpYear == $year->title) selected @endif>
                                        {{ $year->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-2 form-group form-group-new">
                            <label for="dtpFromDate">{{ __("language.from_date") }} :</label>
                            <input type="text" name="dtpFromDate" value="{{ isset($dtpFromDate) ? $dtpFromDate : '' }}" class="form-control" id="from_date" />
                        </div>

                        <div class="col-sm-2 form-group form-group-new">
                            <label for="dtpToDate">{{ __("language.to_date") }} :</label>
                            <input type="text" name="dtpToDate" value="{{ isset($dtpToDate) ? $dtpToDate : '' }}" class="form-control" id="to_date" />
                        </div>

                        <div class="col-sm-4 form-group form-group-new mt-4">
                            <div class="i-check">
                                <input tabindex="8" type="radio" id="withOpeningBalance1" name="withDetails" value="0" {{ $withDetails == 0 ? 'checked' : '' }}>
                                <label for="withOpeningBalance1">Basic</label>
                                
                                <input tabindex="7" type="radio" id="withDetails" name="withDetails" value="1" {{ $withDetails == 1 ? 'checked' : '' }}>
                                <label for="withDetails">{{ __('language.with_details') }}</label>
                            
                                <input tabindex="8" type="radio" id="withOpeningBalance" name="withDetails" value="2"  {{ $withDetails == 2 ? 'checked' : '' }}>
                                <label for="withOpeningBalance">With Opening Balance</label>

                                <input tabindex="8" type="radio" id="withOpeningBalance" name="withDetails" value="3"  {{ @$withDetails == 3 ? 'checked' : '' }}>
                                <label for="withOpeningBalance">With Monthly Balance</label>
                            </div>
                        </div>


                        <div class="col-sm-2 form-group form-group-new">
                            <button style="margin-top:10%" type="submit" class="btn btn-success">{{ __("language.search") }}</button>
                            <button style="margin-top:10%" type="button" class="btn btn-success" id="hideUnhideButton">{{ __('language.hide_zero_balance') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="card">
            @php
                $newFromDate = date("d-M-Y", strtotime($dtpFromDate));
                $newToDate = date("d-M-Y", strtotime($dtpToDate));
            @endphp
            <div id="printArea">
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="mt-10">{{ __("language.trial_balance_report") }}</h3>
                        <h5>as {{ $newFromDate }} {{ __("language.to") }} {{ $newToDate }}</h5>
                        
                    </div>
                    <div class="table-responsive">
                        <table width="99%" align="center" class="table table-bordered table-hover"
                            title="Trial Balance Report {{ $dtpFromDate }} {{ __('language.to_date') }} {{ $dtpToDate }}"
                            id="trial-balance-report">

                            @php
                                // Step 1: Collect all month columns dynamically
                                $monthColumns = [];

                                foreach ($trial_balance_data as $natureData) {
                                    foreach ($natureData as $item) {
                                        $monthKeys = array_filter(array_keys($item), function ($key) {
                                            return preg_match('/^[A-Za-z]{3}-\d{4}$/', $key); // e.g. Jan-2025
                                        });
                                        $monthColumns = array_unique(array_merge($monthColumns, $monthKeys));
                                    }
                                }

                                // Step 2: Sort months chronologically
                                $monthColumns = collect($monthColumns)->sortBy(function ($month) {
                                    return \Carbon\Carbon::createFromFormat('M-Y', $month)->format('Y-m');
                                })->values()->toArray();
                            @endphp

                            {{-- Step 3: Header Row --}}
                            <thead>
                                <tr class="voucherList header-row">
                                    <th style="background: #22376d!important; color:#fff; text-align:left;">
                                        Nature Name
                                    </th>
                                    <th style="background: #22376d!important; color:#fff; text-align:left;">
                                        Ledger Name
                                    </th>
                                    @foreach ($monthColumns as $month)
                                        <th style="background: #22376d!important; color:#fff; text-align:right;">
                                            {{ $month }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            {{-- Step 4: Data Rows --}}
                            <tbody>
                                @foreach ($trial_balance_data as $groupName => $groupItems)
                                    {{-- Group/Nature Header --}}
                                    <tr class="header-row">
                                        <th style="background: #22376d!important; color:#fff;" colspan="{{ count($monthColumns) + 2 }}">
                                            <strong>{{ $groupName }}</strong>
                                        </th>
                                    </tr>

                                    {{-- Group Child Rows --}}
                                    @foreach ($groupItems as $item)
                                        <tr>
                                            <td style="width:10%;"></td> {{-- blank for alignment under group --}}
                                            <td>
                                                <a href="{{ route('account.report.general.ledger.by-link', [
                                                    'dtpYear' => $dtpYear,
                                                    'cmbCode' => $item['acc_id'],
                                                    'dtpFromDate' => $dtpFromDate,
                                                    'dtpToDate' => $dtpToDate
                                                ]) }}" target="_blank">
                                                    {{ $item['acc_name'] }}
                                                </a>
                                            </td>

                                            @foreach ($monthColumns as $month)
                                                <td style="text-align:right;">
                                                    {{ number_format($item[$month] ?? 0, 2) }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="text-center trial_balance_with_opening_btn" id="print">
                <input type="button" class="btn btn-warning" name="btnPrint" id="btnPrint" value="{{ __('language.print') }}" onclick="printDiv();"/>
                <input type="button" class="btn btn-success" value="{{ __('language.pdf') }}" onclick="getPDF('printArea');"/>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{ module_asset('Assets/js/reports/trial_balance.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function () {

    // Function to check and hide rows
    function checkAndHideRows() {
        $('#trial-balance-report tr').each(function () {

            // ✅ Skip rows with class 'header-row'
            if ($(this).hasClass('header-row')) {
                return; // continue to next iteration
            }

            var columnsToCheck = [2,3,4,5,6,7,8,9,10,11,12,13]; 
            var hideRow = true;

            for (var i = 0; i < columnsToCheck.length; i++) {
                var value = parseFloat($(this).find('td:eq(' + columnsToCheck[i] + ')').text()) || 0;

                // ✅ Correct condition to check for non-zero
                if (value !== 0) {
                    hideRow = false;
                    break; // Stop if any column has a non-zero value
                }
            }

            if (hideRow) {
                $(this).hide();
            } else {
                $(this).show(); // Ensure it's visible if previously hidden
            }
        });
    }

    let hide = false;

    // Toggle visibility when button is clicked
    $('#hideUnhideButton').on('click', function () {
        hide = !hide; // Toggle the hide state

        if (hide) {
            checkAndHideRows();
            $(this).text("Show All Rows"); // Optional: Change button text
        } else {
            $('#trial-balance-report tr').show(); // Show all rows again
            $(this).text("Hide Zero Value Rows");
        }
    });
});
</script>    
@endpush