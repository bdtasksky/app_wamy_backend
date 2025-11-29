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
                        <th style=" background: #22376d!important ; color:#fff; justify-content: center; align-items: center;" rowspan="2"><strong>Particulers</strong></th>
                        <td style="text-align:center;  background: #22376d!important ; color:#fff;" colspan="2"><b>Opening</b></td>
                        <td style="text-align:center;  background: #22376d!important ; color:#fff;" colspan="2"><b>Transactional</b></td>
                        <td style="text-align:center;  background: #22376d!important ; color:#fff;" colspan="2"><b>Closing</b></td>
                    </tr>
                    <tr>
                        <td class="text-center" style=" background: #22376d!important ; color:#fff;">Debit</td>
                        <td class="text-center" style=" background: #22376d!important ; color:#fff;">Credit</td>
                        <td class="text-center" style=" background: #22376d!important ; color:#fff;">Debit</td>
                        <td class="text-center" style=" background: #22376d!important ; color:#fff;">Credit</td>
                        <td class="text-center" style=" background: #22376d!important ; color:#fff;">Debit</td>
                        <td class="text-center" style=" background: #22376d!important ; color:#fff;">Credit</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_o_debit1 = $total_o_credit1 = 0;
                        $total_t_debit1 = $total_t_credit1 = 0;
                        $total_c_debit1 = $total_c_credit1 = 0;
                    @endphp
    
                    @if (!empty($getProfitLossNature_3_Balances))
                        @foreach ($getProfitLossNature_3_Balances as $data)
                            @if ($data->level == 1)

                                <tr class="voucherList header-row">
                                    <td colspan="7"><strong>{{ $data->name }}</strong></td>
                                </tr>

                            @elseif ($data->level == 4)
                                @php
                                    $total_o_debit1  += @$data->o_debit;
                                    $total_o_credit1 += @$data->o_credit;
                                    $total_t_debit1  += @$data->t_debit;
                                    $total_t_credit1 += @$data->t_credit;
                                    $total_c_debit1  += @$data->c_debit;
                                    $total_c_credit1 += @$data->c_credit;
                                @endphp
                                <tr class="voucherList">
                                    <td style="padding-left:120px;">{{ @$data->name }}</td>
                                    <td class="text-end">{{ number_format(@$data->o_debit, 2) }}</td>
                                    <td class="text-end">{{ number_format(@$data->o_credit, 2) }}</td>
                                    <td class="text-end">{{ number_format(@$data->t_debit, 2) }}</td>
                                    <td class="text-end">{{ number_format(@$data->t_credit, 2) }}</td>
                                    <td class="text-end">{{ number_format(@$data->c_debit, 2) }}</td>
                                    <td class="text-end">{{ number_format(@$data->c_credit, 2) }}</td>
                                </tr>
                            @elseif ($data->level == 0 && property_exists($data, 'class'))
                                <tr class="voucherList {{ $data->class }}">
                                    <td><strong>{{ $data->name }}</strong></td>
                                    <td class="text-end"><strong>{{ number_format($data->credit, 2) }}</strong></td>
                                </tr>
                            @elseif ($data->level == 0)
                                {{-- <tr class="voucherList">
                                    <td><strong>{{ $data->name }}</strong></td>
                                    <td class="text-end"><strong>{{ number_format($data->credit, 2) }}</strong></td>
                                </tr> --}}
                            @endif
                        @endforeach
                    @endif
    
                    <tr class="baby-bold fw-extra-bold" style="text-align: right;  background: #22376d!important ; color:#fff;">
                        <td>Total Income</td>
                        <td>{{  number_format($total_o_debit1,2) }}</td>
                        <td>{{ number_format($total_o_credit1,2) }}</td>
                        <td>{{ number_format($total_t_debit1,2) }}</td>
                        <td>{{ number_format($total_t_credit1,2) }}</td>
                        <td>{{ number_format($total_c_debit1,2) }}</td>
                        <td>{{ number_format($total_c_credit1,2) }}</td>
                    </tr>
    
                    @php
                        $total_o_debit2 = $total_o_credit2 = 0;
                        $total_t_debit2 = $total_t_credit2 = 0;
                        $total_c_debit2 = $total_c_credit2 = 0;
                    @endphp
    
                    @if (!empty($getProfitLossNature_4_Balances))
                        @foreach ($getProfitLossNature_4_Balances as $data)
                            @if ($data->level == 1)
                                <tr class="voucherList header-row">
                                    <td colspan="7"><strong>{{ $data->name }}</strong></td>
                                </tr>
                            @elseif ($data->level == 4)
                                @php
                                    $total_o_debit2  += @$data->o_debit;
                                    $total_o_credit2 += @$data->o_credit;
                                    $total_t_debit2  += @$data->t_debit;
                                    $total_t_credit2 += @$data->t_credit;
                                    $total_c_debit2  += @$data->c_debit;
                                    $total_c_credit2 += @$data->c_credit;
                                @endphp
                                <tr class="voucherList">
                                    <td style="padding-left:120px;">{{ @$data->name }}</td>
                                    <td class="text-end">{{ number_format(@$data->o_debit, 2) }}</td>
                                    <td class="text-end">{{ number_format(@$data->o_credit, 2) }}</td>
                                    <td class="text-end">{{ number_format(@$data->t_debit, 2) }}</td>
                                    <td class="text-end">{{ number_format(@$data->t_credit, 2) }}</td>
                                    <td class="text-end">{{ number_format(@$data->c_debit, 2) }}</td>
                                    <td class="text-end">{{ number_format(@$data->c_credit, 2) }}</td>
                                </tr>
                            @elseif ($data->level == 0 && property_exists($data, 'class'))
                                <tr class="voucherList {{ $data->class }}">
                                    <td><strong>{{ $data->name }}</strong></td>
                                    <td class="text-end"><strong>{{ number_format($data->debit, 2) }}</strong></td>
                                </tr>
                            @elseif ($data->level == 0)
                                {{-- <tr class="voucherList">
                                    <td><strong>{{ $data->name }}</strong></td>
                                    <td class="text-end"><strong>{{ number_format($data->debit, 2) }}</strong></td>
                                </tr> --}}
                            @endif
                        @endforeach
                    @endif
    
                    <tr class="baby-bold fw-extra-bold" style="text-align: right;  background: #22376d!important ; color:#fff;">
                        <td>Total Expense</td>

                        <td>{{  number_format($total_o_debit2,2) }}</td>
                        <td>{{ number_format($total_o_credit2,2) }}</td>
                        <td>{{ number_format($total_t_debit2,2) }}</td>
                        <td>{{ number_format($total_t_credit2,2) }}</td>
                        <td>{{ number_format($total_c_debit2,2) }}</td>
                        <td>{{ number_format($total_c_credit2,2) }}</td>
                        
                    </tr>
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

            var columnsToCheck = [1,2,3,4,5,6]; 
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