@extends('backend.layouts.app')
@section('title', __('language.profit_loss'))
@push('css')
@endpush
@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12">
        {{-- @include('accounts::reports.financial_report_header') --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <h4>{{ __('language.profit_loss') }}</h4>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('account.report.profit.loss.report.search') }}" method="POST" class="form-inline" enctype="multipart/form-data">
                    @csrf
                    <div class="row align-items-end">
                       
                            <div class="col-md-6 col-xl-2 form-group form-group-new empdropdown">
                                <label for="dtpFromDate">{{ __('language.financial_year') }} :</label>
                                <select id="financial_year" class="form-control" name="dtpYear">
                                    <option value="">{{ __('language.Select Financial Year') }}</option>
                                    @foreach($financial_years as $year)
                                        <option value="{{ $year->title }}" 
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
                                <input type="text" name="dtpFromDate" value="{{ isset($financialyears) ? $financialyears->start_date : '' }}" class="form-control" id="from_date" />
                            </div>

                            <div class="col-md-6 col-xl-3 col-xxl-2 form-group form-group-new">
                                <label for="dtpToDate">{{ __('language.to_date') }} :</label>
                                <input type="text" class="form-control" name="dtpToDate" value="{{ isset($financialyears) ? $financialyears->end_date : '' }}" id="to_date" />
                            </div>
   
                            

                            <div class="col-md-6 col-xl-4 form-group form-group-new mt-4">
                                <div class="i-check">
                                    <input tabindex="8" type="radio" id="withOpeningBalance1" name="withDetails" value="0" checked>
                                    <label for="withOpeningBalance1">Basic</label>


                                    <input tabindex="7" type="radio" id="withDetails" name="withDetails" value="1">
                                    <label for="withDetails">{{ __('language.with_details') }}</label>
                                
                                    <input tabindex="8" type="radio" id="withOpeningBalance" name="withDetails" value="2">
                                    <label for="withOpeningBalance">With Opening Balance</label>

                                   
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4 col-xxl-2">
                                <button type="submit" class="btn btn-success">{{ __('language.search') }}</button>
                                <button type="button" class="btn btn-success" id="hideUnhideButton">{{ __('language.hide_zero_balance') }}</button>
                            </div>                           
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="card col-md-12 mt-5 px-3 py-3">


    <div id="printArea">
        <div class="text-center">
            {{-- <img src="{{ $path }}" alt="logo"> --}}
            <h2 class="mb-0">{{ $setting->title }}</h2>
            <h3 class="mt-10">{{ __('language.profit_loss') }} {{ __('language.report') }}</h3>
            <h5>{{ __('language.as_on') }} {{ $date }}</h5>
            <h5>{{ $branch_name }}</h5>
        </div>

        <div class="table-responsive">
            <table width="100%" class="table table-striped table-bordered table-hover" id="profit-loss-report">
                <thead>
                    <tr class="header-row">
                        <th width="50%"><strong>{{ __('language.particulars') }}</strong></th>
                        <td class="text-end">{{ __('language.ledger_amount') }} </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getProfitLossNature_3_Balances ?? [] as $data)
                        @if ($data->level == 1)
                            <tr class="voucherList">
                                <td style=" background: #22376d!important ; color:#fff"><strong>{{ $data->name }}</strong></td>
                                <td style=" background: #22376d!important ; color:#fff" class="text-end">
                                    <strong>{{ number_format($data->credit, 2) }}</strong>
                                </td>
                            </tr>
                        @elseif ($data->level == 4)
                            <tr class="voucherList">
                                <td style="padding-left:120px;">
                                    <a href="{{route('account.report.general.ledger.by-link',['dtpYear'=>$dtpYear,'cmbCode'=>$data->id,'dtpFromDate'=>$dtpFromDate,'dtpToDate'=>$dtpToDate])}}" target="_blank" class=""> {{ $data->name }}</a>

                                </td>
                                <td class="text-end">{{ number_format($data->credit, 2) }}</td>
                            </tr>
                        @elseif ($data->level == 0 && property_exists($data, 'class'))
                            <tr class="voucherList {{ $data->class }}">
                                <td><strong>{{ $data->name }}</strong></td>
                                <td class="text-end"><strong>{{ number_format($data->credit, 2) }}</strong></td>
                            </tr>
                        @elseif ($data->level == 0)
                            <tr class="voucherList">
                                <td><strong>{{ $data->name }}</strong></td>
                                <td class="text-end"><strong>{{ number_format($data->credit, 2) }}</strong></td>
                            </tr>
                        @endif
                    @endforeach

                    @foreach ($getProfitLossNature_4_Balances ?? [] as $data)
                        @if ($data->level == 1)
                            <tr class="voucherList">
                                <td style=" background: #22376d!important ; color:#fff"><strong>{{ $data->name }}</strong></td>
                                <td style=" background: #22376d!important ; color:#fff" class="text-end">
                                    <strong>{{ number_format($data->debit, 2) }}</strong>
                                </td>
                            </tr>
                        @elseif ($data->level == 4)
                            <tr class="voucherList">
                                <td style="padding-left:120px;">
                                    <a href="{{route('account.report.general.ledger.by-link',['dtpYear'=>$dtpYear,'cmbCode'=>$data->id,'dtpFromDate'=>$dtpFromDate,'dtpToDate'=>$dtpToDate])}}" target="_blank" class=""> {{ $data->name }}</a>

                                </td>
                                <td class="text-end">{{ number_format($data->debit, 2) }}</td>
                            </tr>
                        @elseif ($data->level == 0 && property_exists($data, 'class'))
                            <tr class="voucherList {{ $data->class }}">
                                <td><strong>{{ $data->name }}</strong></td>
                                <td class="text-end"><strong>{{ number_format($data->debit, 2) }}</strong></td>
                            </tr>
                        @elseif ($data->level == 0)
                            <tr class="voucherList">
                                <td><strong>{{ $data->name }}</strong></td>
                                <td class="text-end"><strong>{{ number_format($data->debit, 2) }}</strong></td>
                            </tr>
                        @endif
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

            var columnsToCheck = [1]; 
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