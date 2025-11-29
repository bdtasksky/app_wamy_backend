@extends('backend.layouts.app')
@section('title', __('language.profit_loss_in_details'))
@push('css')
@endpush
@section('content')

<div class="row">
    <div class="col-sm-12 col-md-12">
        {{-- @include('accounts::reports.financial_report_header') --}}
        <div class="card">

            <div class="card-header">
                <div class="card-title">
                    <h4>{{ __('language.profit_loss_report') }}
                    {{-- <div class="btn-group pull-right form-inline">
                        <button id="hideUnhideButton" type="button" class="btn btn-success">Hide/Unhide Zero Value Rows</button>
                    </div> --}}
                    </h4>
                </div>
            </div>

            <div class="card-body">
                {!! Form::open(['route' => 'account.report.profit.loss.report.search', 'class' => 'form-inline', 'method' => 'post']) !!}
                
               <div class="row">
                    <div class="col-sm-2 form-group form-group-new empdropdown">
                        <label for="dtpFromDate">{{ __('language.financial_year') }} :</label>
                        <select id="financial_year" class="form-control" name="dtpYear">
                            <option value="">{{ __('language.Select_financial_year') }}</option>
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
                        <label for="dtpFromDate">{{ __('language.from_date') }} :</label>
                        <input type="text" name="dtpFromDate" value="{{ old('dtpFromDate', $dtpFromDate ?? '') }}" class="form-control" id="from_date"/>
                    </div>

                    <div class="col-sm-2 form-group form-group-new">
                        <label for="dtpToDate">{{ __('language.to_date') }} :</label>
                        <input type="text" class="form-control" name="dtpToDate" value="{{ old('dtpToDate', $dtpToDate ?? '') }}" id="to_date"/>
                    </div>

                    <div class="col-sm-4 form-group form-group-new mt-4">
                        <div class="i-check">
                            <input tabindex="8" type="radio" id="withOpeningBalance1" name="withDetails" value="0"  {{ $withDetails == 0 ? 'checked' : '' }}>
                            <label for="withOpeningBalance1">Basic</label>

                            <input tabindex="7" type="radio" id="withDetails" name="withDetails" value="1" {{ $withDetails == 1 ? 'checked' : '' }}>
                            <label for="withDetails">{{ __('language.with_details') }}</label>
                        
                            <input tabindex="8" type="radio" id="withOpeningBalance" name="withDetails" value="2"  {{ $withDetails == 2 ? 'checked' : '' }}>
                            <label for="withOpeningBalance">With Opening Balance</label>


                        </div>
                    </div>

                    <div class="col-sm-2">
                        <button style="margin-top:10%" type="submit" class="btn btn-success">{{ __('language.search') }}</button>
                        <button style="margin-top:10%" type="button" class="btn btn-success" id="hideUnhideButton">{{ __('language.hide_zero_balance') }}</button>
                    </div>
                </div>
                {!! Form::close() !!}
                
            </div>
        </div>
    </div>
</div>

<div class="card col-md-12 mt-5 px-3 py-3">
   

    <div id="printArea">
        <div class="text-center">
            {{-- <img src="{{ $path }}" alt="logo"> --}}
            <h2 class="mb-0">{{ $setting->title }}</h2>
            <h3 class="mt-10">{{ __('language.profit_loss_report') }}</h3>
            <h5>as {{ $date }}</h5>
            <h5>{{ @$branch_name }}</h5>
        </div>

        <div class="table-responsive">
            <table width="100%" class="table table-striped table-bordered table-hover" id="profit-loss-report">
                <thead>
                    <tr class="header-row">
                        <th width="50%"><strong>{{ __('language.particulars') }}</strong></th>
                        <td class="text-end"><strong>{{ __('language.ledger_amount') }} </strong></td>
                        <td class="text-end"><strong>{{ __('language.sub_ledger_amount') }} </strong></td>
                        <td class="text-end"><strong>{{ __('language.group_ledger_amount') }} </strong></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($getProfitLossNature_3_Balances as $data)
                        @php
                            $level = $data->level;
                            $name = $data->name;
                            $credit = number_format($data->credit, 2);
                            $debit = number_format($data->debit, 2);
                        @endphp
                        
                        @switch($level)
                            @case(1)
                                <tr class="voucherList">
                                    <td style=" background: #22376d!important ; color:#fff"><strong>{{ $name }}</strong></td>
                                    <td style=" background: #22376d!important ; color:#fff"></td>
                                    <td style=" background: #22376d!important ; color:#fff"></td>
                                    <td style=" background: #22376d!important ; color:#fff" class="text-end"><strong>{{ $credit }}</strong></td>
                                </tr>
                                @break

                            @case(2)
                                <tr class="voucherList">
                                    <td style="padding-left:40px;">{{ $name }}</td>
                                    <td class="text-end">0.00</td>
                                    <td class="text-end">0.00</td>
                                    <td class="text-end">{{ $credit }}</td>
                                </tr>
                                @break

                            @case(3)
                                <tr class="voucherList">
                                    <td style="padding-left:80px;">{{ $name }}</td>
                                    <td class="text-end">0.00</td>
                                    <td class="text-end">{{ $credit }}</td>
                                    <td class="text-end">0.00</td>
                                </tr>
                                @break

                            @case(4)
                                <tr class="voucherList">
                                    <td style="padding-left:120px;">{{ $name }}</td>
                                    <td class="text-end">{{ $credit }}</td>
                                    <td class="text-end">0.00</td>
                                    <td class="text-end">0.00</td>
                                </tr>
                                @break

                            @default
                                <tr class="voucherList">
                                    <td><strong>{{ $name }}</strong></td>
                                    <td colspan="3" class="text-end"><strong>{{ $credit }}</strong></td>
                                </tr>
                        @endswitch
                    @endforeach

                    @foreach($getProfitLossNature_4_Balances as $data)
                        @php
                            $level = $data->level;
                            $name = $data->name;
                            $debit = number_format($data->debit, 2);
                        @endphp
                        
                        @switch($level)
                            @case(1)
                                <tr class="voucherList">
                                    <td style=" background: #22376d!important ; color:#fff"><strong>{{ $name }}</strong></td>
                                    <td style=" background: #22376d!important ; color:#fff"></td>
                                    <td style=" background: #22376d!important ; color:#fff"></td>
                                    <td style=" background: #22376d!important ; color:#fff" class="text-end"><strong>{{ $debit }}</strong></td>
                                </tr>
                                @break

                            @case(2)
                                <tr class="voucherList">
                                    <td style="padding-left:40px;">{{ $name }}</td>
                                    <td class="text-end">0.00</td>
                                    <td class="text-end">0.00</td>
                                    <td class="text-end">{{ $debit }}</td>
                                </tr>
                                @break

                            @case(3)
                                <tr class="voucherList">
                                    <td style="padding-left:80px;">{{ $name }}</td>
                                    <td class="text-end">0.00</td>
                                    <td class="text-end">{{ $debit }}</td>
                                    <td class="text-end">0.00</td>
                                </tr>
                                @break

                            @case(4)
                                <tr class="voucherList">
                                    <td style="padding-left:120px;">{{ $name }}</td>
                                    <td class="text-end">{{ $debit }}</td>
                                    <td class="text-end">0.00</td>
                                    <td class="text-end">0.00</td>
                                </tr>
                                @break

                            @default
                                <tr class="voucherList">
                                    <td><strong>{{ $name }}</strong></td>
                                    <td colspan="3" class="text-end"><strong>{{ $debit }}</strong></td>
                                </tr>
                        @endswitch
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center" id="print">
        <input type="button" class="btn btn-warning" name="btnPrint" id="btnPrint" value="{{ __('language.print') }}" onclick="printDiv();" />
        <input type="button" class="btn btn-success" value="PDF" onclick="getPDF('printArea');"/>
    </div>

</div>

@endsection

@section('scripts')
    <script src="{{ asset('application/modules/Accounts/assets/reports/profit_loss_report_search_script.js') }}" type="text/javascript"></script>
    <script src="{{ asset('application/modules/Accounts/Resources/assets/js/canvas-pdf/jspdf.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('application/modules/Accounts/Resources/assets/js/canvas-pdf/html2canvas.js') }}" type="text/javascript"></script>
@endsection
@push('js')
    <script>
    $(document).ready(function () {

    // Function to check and hide rows
    function checkAndHideRows() {
        $('#profit-loss-report tr').each(function () {

            // ✅ Skip rows with class 'header-row'
            if ($(this).hasClass('header-row')) {
                return; // continue to next iteration
            }

            var columnsToCheck = [1,2,3]; 
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
            $('#profit-loss-report tr').show(); // Show all rows again
            $(this).text("Hide Zero Value Rows");
        }
    });
});
</script>
@endpush
