@extends('backend.layouts.app')
@section('title', __('language.balance_sheet'))
@push('css')
@endpush
@section('content')
<link rel="stylesheet" href="{{ asset('application/modules/Accounts/assets/reports/profit_loss_report_search.css') }}">

<div class="row">
    <div class="col-sm-12 col-md-12">
        {{-- @include('accounts::reports.financial_report_header') --}}

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <h5>{{ __('language.balance_sheet') }}
                        {{-- <div class="btn-group pull-right form-inline">
                            <button id="hideUnhideButton" type="button" class="btn btn-success">Hide/Unhide Zero Value Rows</button>
                        </div> --}}
                    </h5>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('account.report.balance.sheet.report.search') }}" method="POST" class="form-inline">
                    @csrf

                   <div class="row">

                        <div class="col-sm-2 form-group form-group-new empdropdown">
                            <label for="dtpFromDate">{{ __('language.financial_year') }} :</label>
                            <select id="financial_year" class="form-control" name="dtpYear">
                                <option value="">{{ __('Select Financial Year') }}</option>
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
                            <input type="text" name="dtpFromDate" value="{{ isset($dtpFromDate) ? $dtpFromDate : '' }}" class="form-control" id="from_date" />
                        </div> 

                        <div class="col-sm-2 form-group form-group-new">
                            <label for="dtpToDate">{{ __('language.to_date') }} :</label>
                            <input type="text" class="form-control" name="dtpToDate" value="{{ isset($dtpToDate) ? $dtpToDate : '' }}" id="to_date" />
                        </div>

                        <div class="col-sm-4 form-group form-group-new mt-4">
                            <div class="i-check">
                                <input tabindex="8" type="radio" id="t_shape" name="type" value="0" {{ $type == 0 ? 'checked' : '' }} >
                                <label for="t_shape">{{ __('language.t_shape') }}</label>
    
                                <input tabindex="7" type="radio" id="with_cogs" name="type" value="1"  {{ $type == 1 ? 'checked' : '' }}>
                                <label for="with_cogs">{{ __('language.with_COGS') }}</label>
                            </div>

                        </div>
                        {{-- <div class="col-sm-1 form-group form-group-new mt-4" style="margin-top:2%">
                            <input type="checkbox" id="t_shape" name="t_shape" value="1" {{ $t_shape == 1 ? 'checked' : '' }}>
                            <label for="t_shape">T-Shape</label>
                        </div>
                        
                        <div class="col-sm-1 form-group form-group-new mt-4" style="margin-top:2%">
                            <input type="checkbox" id="with_cogs" name="with_cogs" value="1" {{ $with_cogs == 1 ? 'checked' : '' }}>
                            <label for="with_cogs">With COGS</label>
                        </div> --}}

                        <div class="col-sm-2 mt-4">
                            <button type="submit" class="btn btn-success">{{ __('language.search') }}</button>
                            <button type="button" class="btn btn-success" id="hideUnhideButton">{{ __('language.hide_zero_balance') }}</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="card mt-5 px-3 py-3 col-md-12">

    @php
        // $path = asset(!empty($setting->logo) ? $setting->logo : 'assets/img/icons/mini-logo.png');
        // $type = pathinfo($path, PATHINFO_EXTENSION);
        // $data = file_get_contents($path);
        // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    @endphp

    <div id="printArea">
        <div class="text-center">
            {{-- <img src="{{ $path }}" alt="logo"> --}}
            {{-- <h2 class="mb-0">{{ $setting->title }}</h2> --}}
            <h3 class="mt-10">{{ __('language.balance_sheet') . ' ' . __('language.report') }}</h3>
            <h5>{{ __('language.as_on') }} {{ $date }}</h5>
            <h5>{{ $branch_name }}</h5>
        </div>

        <div class="container-fluid">
            <div class="row">
                <!-- Liabilities and Equity Section -->
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table width="100%" class="table table-striped table-bordered table-hover balance-sheet-report">
                            <thead>
                                <tr class="header-row">
                                    <th width="50%"><strong>{{ __('language.particulars') }}</strong></th>
                                    <td class="text-end">{{ __('language.ledger_amount') }}</td>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Display Liabilities -->
                                @isset($getBalanceSheetLiabilities)
                                    @foreach ($getBalanceSheetLiabilities as $liability)
                                        @if ($liability->level == 1)
                                            <tr class="voucherList">
                                                <td style="background: #22376d; color:#fff"><strong>{{ $liability->name }}</strong></td>
                                                <td class="text-end" style="background: #22376d; color:#fff">
                                                    <strong>{{ number_format($liability->credit, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @elseif ($liability->level == 4)
                                            <tr class="voucherList">
                                                <td style="padding-left:120px;">{{ $liability->name }}</td>
                                                <td class="text-end">{{ number_format($liability->credit, 2) }}</td>
                                            </tr>
                                        @elseif ($liability->level == 0 && property_exists($liability, 'class'))
                                            <tr class="voucherList {{ $liability->class }}">
                                                <td><strong>{{ $liability->name }}</strong></td>
                                                <td class="text-end"><strong>{{ number_format($liability->credit, 2) }}</strong></td>
                                            </tr>
                                        @elseif ($liability->level == 0)
                                            <tr class="voucherList">
                                                <td><strong>{{ $liability->name }}</strong></td>
                                                <td class="text-end"><strong>{{ number_format($liability->credit, 2) }}</strong></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endisset

                                <!-- Display Equity -->
                                @isset($getBalanceSheetEquity)
                                    @foreach ($getBalanceSheetEquity as $equity)
                                        @if ($equity->level == 1)
                                            <tr class="voucherList">
                                                <td style="background: #22376d; color:#fff"><strong>{{ $equity->name }}</strong></td>
                                                <td class="text-end" style="background: #22376d; color:#fff">
                                                    <strong>{{ number_format($equity->credit, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @elseif ($equity->level == 4)
                                            @php
                                                $ProfitorLossText = '';
                                                $ProfitorLossAmount = '';
                                                if ($equity->id == $CPLcode->CPLcode) {
                                                    if ($equity->credit > 0) {
                                                        $ProfitorLossText = '<span class="text-success"><strong>Net Profit</strong></span>';
                                                        $ProfitorLossAmount = 'text-success';
                                                    } else {
                                                        $ProfitorLossText = '<span class="text-danger"><strong>Net Loss</strong></span>';
                                                        $ProfitorLossAmount = 'text-danger';
                                                    }
                                                }
                                            @endphp
                                            <tr class="voucherList">
                                                <td style="padding-left:120px;">{{ $equity->name }}{!! $equity->id == $CPLcode->CPLcode ? " ({$ProfitorLossText})" : '' !!}</td>
                                                <td class="text-end {{ $equity->id == $CPLcode->CPLcode ? $ProfitorLossAmount : '' }}">
                                                    {{ number_format($equity->credit, 2) }}
                                                </td>
                                            </tr>
                                        @elseif ($equity->level == 0 && property_exists($equity, 'class'))
                                            <tr class="voucherList {{ $equity->class }}">
                                                <td><strong>{{ $equity->name }}</strong></td>
                                                <td class="text-end"><strong>{{ number_format($equity->credit, 2) }}</strong></td>
                                            </tr>
                                        @elseif ($equity->level == 0)
                                            <tr class="voucherList">
                                                <td><strong>{{ $equity->name }}</strong></td>
                                                <td class="text-end"><strong>{{ number_format($equity->credit, 2) }}</strong></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endisset
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="table-responsive">
                        <table width="100%" class="table table-striped table-bordered table-hover balance-sheet-report">
                            <thead>
                                <tr class="header-row">
                                    <th width="50%"><strong>{{ __('language.particulars') }}</strong></th>
                                    <td class="text-end">{{ __('language.ledger_amount') }}</td>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Display Assets -->
                                @isset($getBalanceSheetAssets)
                                    @foreach ($getBalanceSheetAssets as $asset)
                                        @if ($asset->level == 1)
                                            <tr class="voucherList">
                                                <td style="background: #22376d; color:#fff"><strong>{{ $asset->name }}</strong></td>
                                                <td class="text-end" style="background: #22376d; color:#fff">
                                                    <strong>{{ number_format($asset->debit, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @elseif ($asset->level == 4)
                                            <tr class="voucherList">
                                                <td style="padding-left:120px;">{{ $asset->name }}</td>
                                                <td class="text-end">{{ number_format($asset->debit, 2) }}</td>
                                            </tr>
                                        @elseif ($asset->level == 0 && property_exists($asset, 'class'))
                                            <tr class="voucherList {{ $asset->class }}">
                                                <td><strong>{{ $asset->name }}</strong></td>
                                                <td class="text-end"><strong>{{ number_format($asset->debit, 2) }}</strong></td>
                                            </tr>
                                        @elseif ($asset->level == 0)
                                            <tr class="voucherList">
                                                <td><strong>{{ $asset->name }}</strong></td>
                                                <td class="text-end"><strong>{{ number_format($asset->debit, 2) }}</strong></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endisset
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center" id="print">
        <input type="button" class="btn btn-warning" name="btnPrint" id="btnPrint" value="{{ __('language.print') }}" onclick="printDiv();" />
        <input type="button" class="btn btn-success" value="PDF" onclick="getPDF('printArea');"/>
    </div>

</div>

<script src="{{ asset('application/modules/Accounts/assets/reports/balance_sheet_report_search_script.js') }}" type="text/javascript"></script>
<script src="{{ asset('application/modules/Accounts/Resources/assets/js/canvas-pdf/jspdf.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('application/modules/Accounts/Resources/assets/js/canvas-pdf/html2canvas.js') }}" type="text/javascript"></script>
@endsection
@push('js')
    <script>
    $(document).ready(function () {

    // Function to check and hide rows
    function checkAndHideRows() {
        $('.balance-sheet-report tr').each(function () {

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
            $('.balance-sheet-report tr').show(); // Show all rows again
            $(this).text("Hide Zero Value Rows");
        }
    });
});
</script>
@endpush