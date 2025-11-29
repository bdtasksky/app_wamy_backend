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
                <div class="card-title">
                    <h4>{{ __('language.trial_balance') }}</h4>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('account.report.trial.balance.search') }}" method="post" class="form-inline" enctype="multipart/form-data">
                    @csrf
                    
                   <div class="row align-items-end">
                        <div class="col-md-6 col-xl-2 form-group form-group-new empdropdown">
                            <label for="dtpFromDate">{{ __('language.financial_year') }} :</label>
                            <select id="financial_year" class="form-control select-basic-single select2-hidden-accessible" name="dtpYear" >
                                <option value="">{{ __('language.select_financial_year') }}</option>
                                @foreach($financial_years as $year)
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
                        
                        <div class="col-md-6 col-xl-3 col-xxl-2 form-group form-group-new">
                            <label for="dtpFromDate">{{ __('language.from_date') }} :</label>
                            <input type="text" name="dtpFromDate" value="{{ isset($financialyears) ? $financialyears->start_date : '' }}" class="form-control" id="from_date"/>
                        </div>

                        <div class="col-md-6 col-xl-3 col-xxl-2 form-group form-group-new">
                            <label for="dtpToDate">{{ __('language.to_date') }} :</label>
                            <input type="text" class="form-control" name="dtpToDate" value="{{ isset($financialyears) ? $financialyears->end_date : '' }}" id="to_date"/>
                        </div>


                        <div class="col-md-6 col-xl-4 form-group form-group-new mt-4">
                            <div class="i-check">
                                <input tabindex="8" type="radio" id="withOpeningBalance1" name="withDetails" value="0" checked>
                                <label for="withOpeningBalance1">Basic</label>
                                <input tabindex="7" type="radio" id="withDetails" name="withDetails" value="1" {{ @$withDetails == 1 ? 'checked' : '' }}>
                                <label for="withDetails">{{ __('language.with_details') }}</label>
                            
                                <input tabindex="8" type="radio" id="withOpeningBalance" name="withDetails" value="2"  {{ @$withDetails == 2 ? 'checked' : '' }}>
                                <label for="withOpeningBalance">With Opening Balance</label>
                                <input tabindex="8" type="radio" id="withOpeningBalance" name="withDetails" value="3"  {{ @$withDetails == 3 ? 'checked' : '' }}>
                                <label for="withOpeningBalance">With Monthly Balance</label>
                            </div>
                        </div>

                        
                        <div class="col-md-6 col-xl-4 col-xxl-2 form-group form-group-new">
                            <button type="submit" class="btn btn-success">{{ __('language.search') }}</button>
                            <button type="button" class="btn btn-success" id="hideUnhideButton">{{ __('language.hide_zero_balance') }}</button>
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
                        {{-- <img src="{{ $path }}" alt="logo"> --}}
                        {{-- <h2 class="mb-0">{{ $setting->title }}</h2> --}}
                        <h3 class="mt-10">{{ __("language.trial_balance_report") }}</h3>
                        <h5>as {{ $newFromDate }} {{ __("language.to") }} {{ $newToDate }}</h5>
                        
                    </div>
                    <div class="table-responsive">
                        <table width="99%" align="center" class="table table-bordered table-hover" title="Trial Balance Report {{ $dtpFromDate }} {{ __('language.to_date') }} {{ $dtpToDate }}" id="trial-balance-report">
                            <tr class="voucherList header-row">
                                <th style=" background: #22376d!important ; color:#fff; text-align:center;">{{ __("language.account_name") }}</th>
                                <th style=" background: #22376d!important ; color:#fff; text-align:center;" colspan="2">{{ __("language.closing_balance") }}</th>
                            </tr>
                            <tr class="header-row">
                                <th style="text-align:center;"></th>
                                <th style="text-align:right;"> {{ __("language.debit") }}</th>
                                <th style="text-align:right;"> {{ __("language.credit") }}</th>
                            </tr>
                            <tbody>

                                @foreach ($trial_balance_data as $arr_key => $t_b_data)

                                    @if($arr_key)
                                        <tr class="header-row">
                                            <th style="background: #22376d!important; color:#fff;">
                                                <strong>
                                                    {{ $arr_key }} ( Total {{ $arr_key }}: 
                                                    @foreach ($sum as $s)
                                                        @if (strpos((string)$s['nature_name'], (string)$arr_key) !== false)
                                                            {{ $s['total_amount'] ? $s['total_amount'] : '0.00' }}
                                                        @endif
                                                    @endforeach
                                                    )
                                                </strong>
                                            </th>
                                            <th style="background: #22376d!important; color:#fff;"></th>
                                            <th style="background: #22376d!important; color:#fff;"></th>
                                        </tr>

                                        
                                        @foreach ($t_b_data as $t_b_detail_data)


                                            <tr>
                                                <td>
                                                    <form action="{{ url('accounts/AccReportController/general_ledger_report_search') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="cmbCode" value="{{ $t_b_detail_data->acc_id }}">
                                                        <input type="hidden" name="dtpFromDate" value="{{ $dtpFromDate }}">
                                                        <input type="hidden" name="dtpToDate" value="{{ $dtpToDate }}">
                                                        <input type="hidden" name="dtpYear" value="{{ $dtpYear }}">

                                                        {{-- <button type="submit" style="border: none;background: transparent; width: 100%; text-align:left"> --}}
                                                        <a href="{{route('account.report.general.ledger.by-link',['dtpYear'=>$dtpYear,'cmbCode'=>$t_b_detail_data->acc_id,'dtpFromDate'=>$dtpFromDate,'dtpToDate'=>$dtpToDate])}}" target="_blank" class=""> {{ $t_b_detail_data->acc_name }}</a>

                                                        {{-- </button> --}}
                                                    </form>
                                                </td>
                                                <td style="text-align:right;">{{ $t_b_detail_data->debit }}</td>
                                                <td style="text-align:right;">{{ $t_b_detail_data->credit }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="header-row">
                                            <td style="background: #22376d!important; color:#fff;"><strong>{{ $t_b_data[0]->nacc_name }}</strong></td>
                                            <td style="background: #22376d!important;text-align:right; color:#fff;">{{ $t_b_data[0]->debit }}</td>
                                            <td style="background: #22376d!important;text-align:right; color:#fff;">{{ $t_b_data[0]->credit }}</td>
                                        </tr>
                                    @endif
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
<script>
    $(document).ready(function () {

    // Function to check and hide rows
    function checkAndHideRows() {
        $('#trial-balance-report tr').each(function () {

            // ✅ Skip rows with class 'header-row'
            if ($(this).hasClass('header-row')) {
                return; // continue to next iteration
            }

            var columnsToCheck = [1, 2]; 
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