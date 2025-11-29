@extends('backend.layouts.app')
@section('title', __('language.trial_balance_with_details'))
@push('css')
@endpush
@section('content')
<link rel="stylesheet" href="{{ asset('application/modules/Accounts/assets/reports/trial_balance.css') }}">


        <div class="card fixed-tab-body">
            @include('accounts::reports.financial_report_header')
            <div class="card-header">
                <div>
                    <h5>{{ __('language.trial_balance_with_filter') }}</h5>
                    
                </div>
            </div>

            <div class="card-body"> 
                <form action="{{ route('account.report.trial.balance.search') }}" method="POST" class="form-inline">
                    @csrf
                    
                    <div class="row">
                        <div class="col-sm-2 form-group form-group-new empdropdown">
                            <label for="dtpFromDate">{{ __('language.financial_year') }} :</label>
                            <select id="financial_year" class="form-control" name="dtpYear">
                                <option value="">Select Financial Year</option>
                                @foreach ($financial_years as $year)
                                    <option 
                                        value="{{ $year->title }}" 
                                        data-start_date="{{ $year->start_date }}" 
                                        data-end_date="{{ $year->end_date }}"
                                        {{ isset($dtpYear) && $dtpYear == $year->title ? 'selected' : '' }}>
                                        {{ $year->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-2 form-group form-group-new">
                            <label for="dtpFromDate">{{ __('language.from_date') }}:</label>
                            <input type="text" name="dtpFromDate" value="{{ isset($dtpFromDate) ? $dtpFromDate : '' }}" class="form-control" id="from_date" readonly/>
                        </div> 

                        <div class="col-sm-2 form-group form-group-new">
                            <label for="dtpToDate">{{ __('language.to_date') }}:</label>
                            <input type="text" class="form-control" name="dtpToDate" value="{{ isset($dtpToDate) ? $dtpToDate : '' }}" id="to_date" readonly/>
                        </div>

                        {{-- <div class="col-sm-2 form-group form-group-new">
                            <input type="checkbox" id="withDetails" name="withDetails" value="1" {{ $withDetails == 1 ? 'checked' : '' }}>
                            <label for="withDetails">With Details</label>
                        </div>  --}}

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

                        <div class="col-sm-2 ">
                            <button type="submit" class="btn btn-success">{{ __('language.search') }}</button>
                            <button type="button" class="btn btn-success" id="hideUnhideButton">{{ __('language.hide_zero_balance') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <div>
                    <h4>{{ __('language.trial_balance') }}
                        {{-- <div class="btn-group pull-right form-inline">
                            <button id="hideUnhideButton" type="button" class="btn btn-success">Hide/Unhide Zero Value Rows</button>
                        </div> --}}
                    </h4>
                </div>
            </div>

            @php
                // $path = asset($setting->logo ?: 'assets/img/icons/mini-logo.png');
                // $type = pathinfo($path, PATHINFO_EXTENSION);
                // $data = file_get_contents($path);
                // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                $newformDate = isset($dtpFromDate) ? date('d-M-Y', strtotime($dtpFromDate)) : '';
                $newToDate = isset($dtpToDate) ? date('d-M-Y', strtotime($dtpToDate)) : '';
            @endphp

            <div id="printArea">
                <div class="card-body">
                    <div class="text-center">
                        {{-- <img src="{{ $path }}" alt="logo"> --}}
                        <h5 class="mb-0">{{ $setting->title }}</h5>
                        <h5 class="mt-10">{{ __('language.trial_balance_report') }}</h5>
                        <h5>As on {{ $newformDate }} To {{ $newToDate }}</h5>
                        <h5>{{ @$branch_name }}</h5>
                    </div>
                    <div class="table-responsive">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="trial-balance-report">
                            <thead>
                                <tr class="voucherList header-row">
                                    <th width="30%"><strong>Particulars</strong></th>
                                    <th class="text-end"><strong>Ledger Amount</strong></th>
                                    <th class="text-end"><strong>Sub Ledger Amount</strong></th>
                                    <th class="text-end"><strong>Group Ledger Amount</strong></th>
                                    <th class="text-end"><strong>Nature Amount</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_debit = 0;
                                    $total_credit = 0;
                                @endphp

                                @if (!empty($trial_balance_data))

                                    @foreach ($trial_balance_data as $nature_name => $nature_data)
                                    
                                        <!-- Nature level display -->
                                        <tr class="voucherList">
                                            <td style=" background: #22376d!important ; color:#fff">
                                                <strong> {{ $nature_name }} </strong>
                                            </td>

                                            <td style=" background: #22376d!important ; color:#fff" class="text-end"></td>
                                            <td style=" background: #22376d!important ; color:#fff" class="text-end"></td>
                                            <td style=" background: #22376d!important ; color:#fff" class="text-end"></td>
                                            <td style=" background: #22376d!important ; color:#fff" class="text-end">
                                                <strong>
                                                    @if ($nature_name == 'Assets' || $nature_name == 'Expense')
                                                        {{ number_format($nature_data['nature_amount_debit'], 2) }}
                                                    @else
                                                        {{ number_format($nature_data['nature_amount_credit'], 2) }}
                                                    @endif
                                                </strong>
                                            </td>
                                        </tr>

                                        @foreach ($nature_data['groups'] as $group_name => $group_data)
                                            <!-- Group level display -->
                                            <tr class="voucherList">
                                                <td style="padding-left:40px;"><strong>{{ $group_name }}</strong></td>
                                                <td class="text-end"></td>
                                                <td class="text-end"></td>
                                                <td class="text-end">
                                                    <strong>
                                                        @if ($nature_name == 'Assets' || $nature_name == 'Expense')
                                                            {{ number_format($group_data['group_amount_debit'], 2) }}
                                                        @else
                                                            {{ number_format($group_data['group_amount_credit'], 2) }}
                                                        @endif
                                                    </strong>
                                                </td>
                                                <td class="text-end"></td>
                                            </tr>

                                            @foreach ($group_data['sub_groups'] as $sub_group_name => $sub_group_data)
                                                <!-- Sub-group level display -->
                                                <tr class="voucherList">
                                                    <td style="padding-left:80px;"><strong>{{ $sub_group_name }}</strong></td>
                                                    <td class="text-end"></td>
                                                    <td class="text-end">
                                                        <strong>
                                                            @if ($nature_name == 'Assets' || $nature_name == 'Expense')
                                                                {{ number_format($sub_group_data['sub_group_amount_debit'], 2) }}
                                                            @else
                                                                {{ number_format($sub_group_data['sub_group_amount_credit'], 2) }}
                                                            @endif
                                                        </strong>
                                                    </td>
                                                    <td class="text-end"></td>
                                                    <td class="text-end"></td>
                                                </tr>

                                                @foreach ($sub_group_data['ledgers'] as $ledger)
                                                    <!-- Ledger level display -->
                                                    <tr class="voucherList">
                                                        <td style="padding-left:120px;">{{ $ledger['ledger_name'] }}</td>
                                                        <td class="text-end">
                                                            <strong>
                                                                @if ($nature_name == 'Assets' || $nature_name == 'Expense')
                                                                    {{ number_format($ledger['debit'], 2) }}
                                                                    @php $total_debit += $ledger['debit']; @endphp
                                                                @else
                                                                    {{ number_format($ledger['credit'], 2) }}
                                                                    @php $total_credit += $ledger['credit']; @endphp
                                                                @endif
                                                            </strong>
                                                        </td>
                                                        <td class="text-end"></td>
                                                        <td class="text-end"></td>
                                                        <td class="text-end"></td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr class="voucherList">
                                    <td><strong>Total</strong></td>
                                    <td class="text-end"><strong>{{ number_format($total_debit, 2) }}</strong></td>
                                    <td class="text-end"></td>
                                    <td class="text-end"></td>
                                    <td class="text-end"><strong>{{ number_format($total_credit, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-center trial_balance_with_opening_btn" id="print">
                <input type="button" class="btn btn-warning" name="btnPrint" id="btnPrint" value="Print" onclick="printDiv();"/>
                <input type="button" class="btn btn-success" value="PDF" onclick="getPDF('printArea');"/>
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

            var columnsToCheck = [1, 2,3,4]; 
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