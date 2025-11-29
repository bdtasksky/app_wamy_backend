@extends('backend.layouts.app')
@section('title', __('language.trial_balance'))
@push('css')
@endpush
@section('content')
{{-- <link rel="stylesheet" href="{{ asset('application/modules/Accounts/assets/reports/trial_balance.css') }}"> --}}

<div class="row">
    <div class="col-sm-12 col-md-12">

        @include('accounts::reports.financial_report_header')
        
        <div class="card fixed-tab-body">
            
            <div class="card-header">
                <div>
                    <h5>{{ __("language.trial_balance_with_filter") }}</h5>
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
   
                        {{-- <div class="col-sm-2 form-group form-group-new">
                            <input style="margin-top:14%" type="checkbox" id="withDetails" name="withDetails" {{ $withDetails == 1 ? 'checked' : '' }} value="1">
                            <label for="withDetails">{{ __("language.with_details") }}</label>
                        </div> --}}

                        <div class="col-sm-4 form-group form-group-new mt-4">
                            <div class="i-check">

                                <input tabindex="8" type="radio" id="withOpeningBalance1" name="withDetails" value="0" checked>
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
                        <table width="99%"  class="table table-bordered table-hover"
                            title="TriaBalanceReport{{ $dtpFromDate }}{{ __('to_date') }}{{ $dtpToDate }}"
                            id="trial-balance-report">
                    
                            <tr class="voucherList header-row">
                                <th style=" background: #22376d!important ; color:#fff; text-align:center;">{{ __('Account Name') }}</th>
                                <th style=" background: #22376d!important ; color:#fff; text-align:center;" colspan="2">{{ __('Opening Balance') }}</th>
                                <th style=" background: #22376d!important ; color:#fff; text-align:center;" colspan="2">Transactional Balance</th>
                                <th style=" background: #22376d!important ; color:#fff; text-align:center;" colspan="2">{{ __('Closing Blance') }}</th>
                            </tr>
                    
                            <tr class="header-row">
                                <th style="text-align:center;"></th>
                                <th style="text-align:right;">{{ __('language.debit') }}</th>
                                <th style="text-align:right;">{{ __('language.credit') }}</th>
                                <th style="text-align:right;">{{ __('language.debit') }}</th>
                                <th style="text-align:right;">{{ __('language.credit') }}</th>
                                <th style="text-align:right;">{{ __('language.debit') }}</th>
                                <th style="text-align:right;">{{ __('language.credit') }}</th>
                            </tr>
                    
                            <tbody>
                                @php
                                    $total_o_debit = 0;
                                    $total_o_credit = 0;
                                    $total_t_debit = 0;
                                    $total_t_credit = 0;
                                    $total_c_debit = 0;
                                    $total_c_credit = 0;
                                @endphp
                    
                                @foreach ($trial_balance_data as $arr_key => $t_b_data)
                                    @if ($arr_key)
                                        <tr class="header-row">
                                            <th colspan="7" style="background: #22376d!important"><strong>{{ $arr_key }}</strong></th>
                                        </tr>
                    

                                        @foreach ($t_b_data as $t_b_detail_data)

                                            @php
                                                $total_o_debit  += @$t_b_detail_data['o_debit'];
                                                $total_o_credit += @$t_b_detail_data['o_credit'];
                                                $total_t_debit  += @$t_b_detail_data['t_debit'];
                                                $total_t_credit += @$t_b_detail_data['t_credit'];
                                                $total_c_debit  += @$t_b_detail_data['c_debit'];
                                                $total_c_credit += @$t_b_detail_data['c_credit'];
                                            @endphp
                    
                                            <tr>
                                                <td>
                                                    <form action="{{ url('accounts/AccReportController/general_ledger_report_search_from_trial') }}" method="POST" target="_blank">
                                                        @csrf
                                                        <input type="hidden" name="cmbCode" value="{{ @$t_b_detail_data['ledger_id'] }}">
                                                        <input type="hidden" name="dtpFromDate" value="{{ $dtpFromDate }}">
                                                        <input type="hidden" name="dtpToDate" value="{{ $dtpToDate }}">
                                                        <input type="hidden" name="dtpYear" value="{{ $dtpYear }}">
                                                        <input type="hidden" name="row" value="10">
                                                        <input type="hidden" name="page" value="1">
                                                        <button type="submit" style="border: none;background: transparent; width: 100%; text-align:left">
                                                            {{ @$t_b_detail_data['ledger_name'] }} 
                                                        </button>
                                                    </form>
                                                </td>
                                                <td style="text-align:right;">{{ @$t_b_detail_data['o_debit'] }}</td>
                                                <td style="text-align:right;">{{ @$t_b_detail_data['o_credit'] }}</td>
                                                <td style="text-align:right;">{{ @$t_b_detail_data['t_debit'] }}</td>
                                                <td style="text-align:right;">{{ @$t_b_detail_data['t_credit'] }}</td>
                                                <td style="text-align:right;">{{ @$t_b_detail_data['c_debit'] }}</td>
                                                <td style="text-align:right;">{{ @$t_b_detail_data['c_credit'] }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td style="background: #22376d!important"><strong>{{ $t_b_data[0]['ledger_name'] }}</strong></td>
                                            <td style="background: #22376d!important; text-align:right;">{{ $t_b_data[0]['o_debit'] }}</td>
                                            <td style="background: #22376d!important; text-align:right;">{{ $t_b_data[0]['o_credit'] }}</td>
                                            <td style="background: #22376d!important; text-align:right;">{{ $t_b_data[0]['t_debit'] }}</td>
                                            <td style="background: #22376d!important; text-align:right;">{{ $t_b_data[0]['t_credit'] }}</td>
                                            <td style="background: #22376d!important; text-align:right;">{{ $t_b_data[0]['c_debit'] }}</td>
                                            <td style="background: #22376d!important; text-align:right;">{{ $t_b_data[0]['c_credit'] }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                    
                                <tr style="font-weight:bold; text-align:right; background: #22376d!important">
                                    <td>Total</td>
                                    <td>{{ $total_o_debit }}</td>
                                    <td>{{ $total_o_credit }}</td>
                                    <td>{{ $total_t_debit }}</td>
                                    <td>{{ $total_t_credit }}</td>
                                    <td>{{ $total_c_debit }}</td>
                                    <td>{{ $total_c_credit }}</td>
                                </tr>
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

            var columnsToCheck = [1, 2,3,4,5,6]; 
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