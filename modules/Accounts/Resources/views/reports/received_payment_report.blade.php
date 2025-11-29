@extends('backend.layouts.app')
@section('title', __('language.received_payment'))
@push('css')
@endpush
@section('content')
    <div class="row">
        <div class="col-sm-12 col-md-12">
            {{-- @include('accounts::reports.financial_report_header') --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h4>{{ __('language.received_payment') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-5 row-cols-xxl-6 g-3 align-items-end">
                        <div class="col-sm form-group form-group-new empdropdown">
                            <label for="dtpFromDate">{{ __('language.financial_year') }} :</label>
                            <select id="financial_year" class="form-control" name="dtpYear">
                                <option value="">{{ __('language.Select Financial Year') }}</option>
                                @foreach ($financial_years as $year)
                                    <option value="{{ $year->title }}" data-start_date="{{ $year->start_date }}"
                                        data-end_date="{{ $year->end_date }}"
                                        @if (isset($financialyears) && $financialyears->title == $year->title) selected @endif>
                                        {{ $year->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm form-group form-group-new">
                            <label for="dtpFromDate">{{ __('language.from_date') }} :</label>
                            <input type="text" name="dtpFromDate"
                                value="{{ isset($financialyears) ? $financialyears->start_date : '' }}" class="form-control"
                                id="from_date" />
                        </div>
                        <div class="col-sm form-group form-group-new">
                            <label for="dtpToDate">{{ __('language.to_date') }} :</label>
                            <input type="text" class="form-control" name="dtpToDate"
                                value="{{ isset($financialyears) ? $financialyears->end_date : '' }}" id="to_date" />
                        </div>

                        <div class="col-sm form-group form-group-new empdropdown">
                            <label for="row">Rows :</label>
                            <select id="row" class="form-control" name="row">
                                <option value="">Select Row</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                                <option value="-1">All</option>
                            </select>
                        </div>

                        <div class="col-sm">

                            <button style="margin-top:22%" class="btn btn-success"
                                onclick="getReceivedPayment()">{{ __('language.search') }}</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5" id="getreceivedpaymentreport">

<!-- print area and whole report -->
<div class="row" id="printArea">
    @php
        // $path = asset(!empty($setting->logo) ? $setting->logo : 'assets/img/icons/mini-logo.png');
        // $type = pathinfo($path, PATHINFO_EXTENSION);
        // $data = file_get_contents(public_path($path));
        // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $newformDate = date("d-M-Y", strtotime($dtpFromDate));
        $newToDate = date("d-M-Y", strtotime($dtpToDate));
       
    @endphp

    <div style="margin-left:10px" class="card px-3 py-3">
        <div class="text-center">
            {{-- <img src="" alt="logo"> --}}
            <h5 class="mb-0">{{ app_setting()->title }}</h5>
            <h5 class="mt-10">{{ __('language.receive_payment_report')}}</h5>
            <h5>As on {{ $newformDate ?? '' }} To {{ $newToDate ?? '' }}</h5>
            <h5>{{ $branch_name }}</h5>
        </div>

        <div class="row">
            <div class="col-xs-12 text-center">
                <strong>{{ __('language.date') }}: {{ now()->format('d-M-Y') }}</strong><br><br>
            </div>
        </div>

        <div class="table-responsive">
            <table width="99%"  class="table table-bordered table-hover text-center" cellpadding="5" cellspacing="5" border="2">
                <tr class="voucherList" >
                    <td colspan="10" style=" background: #22376d!important ; color:#fff">
                        <font size="+1" class="general_ledger_report_fontfamily">
                            <strong>{{ __('language.receive_payment_report') . ' ' .'' . ' on ' . \Carbon\Carbon::parse($dtpFromDate)->format('d-m-Y') . ' To ' . \Carbon\Carbon::parse($dtpToDate)->format('d-m-Y') }}</strong>
                        </font>
                    </td>
                </tr>

                <tr class="voucherList">
                    <td style="background: #efefef!important" width="5%"><strong>{{ __('language.sl') }}</strong></td>
                    <td style="background:#efefef!important" width="10%"><strong>{{ __('language.date') }}</strong></td>
                    <td style="background: #efefef!important" width="10%"><strong>{{ __('language.voucher_no') }}</strong></td>
                    <td style="background: #efefef!important" width="10%"><strong>{{ __('language.ceb') }}</strong></td>
                    <td style="background: #efefef!important"><strong>{{ __('language.account_name') }}</strong></td>
                    <td style="background: #efefef!important" width="12%"><strong>{{ __('language.remarks') }}</strong></td>
                    <td style="background: #efefef!important" width="10%" align="right"><strong>{{ __('language.voucher_type') }}</strong></td>
                    
                    <td style="background: #efefef!important" width="10%" align="right"><strong>{{ __('language.received') }}</strong></td>
                    <td style="background: #efefef!important" width="10%" align="right"><strong>{{ __('language.payment') }}</strong></td>
                    <td style="background: #efefef!important" width="10%" align="right"><strong>{{ __('language.balance') }}</strong></td>
                </tr>

                <tbody id="ledgerTableBody">
                    @foreach($ledger_data as $key => $l_data)
                        @php
                            $style = ($loop->iteration % 2 == 0) ? '#efefef!important' : '';
                        @endphp
                        <tr class="{{ $loop->iteration % 2 == 0 ? 'voucherList' : '' }}">
                            <td style="background:{{ $style }}">{{ $loop->iteration }}</td>
                            <td style="background:{{ $style }}">{{ $l_data->v_date }}</td>
                            <td style="background:{{ $style }}" align="center">
                                <a href="javascript:void(0);" data-id="{{ $l_data->v_voucher_id }}" data-vdate="{{ $l_data->v_date }}" target="_blank" class="v_view" style="margin-right:10px" title="View Voucher">{{ $l_data->v_voucher_no }}</a>
                            </td>
                            <td style="background:{{ $style }}"><strong>{{ $l_data->v_cash_equivalents_account_name }}</strong></td>
                            <td style="background:{{ $style }}">
                                <a href="{{route('account.report.general.ledger.by-link',['dtpYear'=>$dtpYear,'cmbCode'=>@$l_data->id,'dtpFromDate'=>$dtpFromDate,'dtpToDate'=>$dtpToDate])}}" target="_blank" class=""> {{ $l_data->v_rev_acc_name }}</a>

                            </td>
                            <td style="background:{{ $style }}" align="left">{{ $l_data->v_remarks }}</td>
                            <td style="background:{{ $style }}" align="left">
                                <strong>
                                    @if ($l_data->v_voucher_type_id != 0)
                                        @php
                                            $voucher_info = $vouchartypes[$l_data->v_voucher_type_id-1];
                                        @endphp
                                        {{ $voucher_info->name }}
                                    @else
                                        {{ '' }}
                                    @endif
                                </strong>
                            </td>
                            <td style="background:{{ $style }}" align="right"><strong>{{ $l_data->v_debit }}</strong></td>
                            <td style="background:{{ $style }}" align="right"><strong>{{ $l_data->v_credit }}</strong></td>
                            <td style="background:{{ $style }}" align="right"><strong>{{ $l_data->v_balance }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
        
    </div>

</div>

    </div>



    <!-- Include modal for voucher details -->
    @include('accounts::modal.voucher_details')
@endsection
@push('js')
    <script>
        function getReceivedPayment() {

            var dtpYear = $('#financial_year').find(":selected").val();
            var dtpFromDate = $('#from_date').val();
            var dtpToDate = $('#to_date').val();
            var row = $('#row').find(":selected").val();
            var page = 1;
            var csrf = '{{ csrf_token() }}';
            var myurl = '{{ route('account.report.received.payment.report.search') }}';

            var dataString = {
                dtpYear: dtpYear,
                dtpFromDate: dtpFromDate,
                dtpToDate: dtpToDate,
                row: row,
                page: page,
                // branch_id: branch_id,
                csrf_test_name: csrf
            };

            $.ajax({
                type: "POST",
                url: myurl,
                data: dataString,
                success: function(data) {
                    $('#getreceivedpaymentreport').html(data);
                },
                error: function(xhr, status, error) {
                    alert('Please refresh the page to continue');
                    // window.location.reload();
                }
            });
        }

        function changePage(pageNo) {
            var dtpYear = $('#financial_year').find(":selected").val();
            var dtpFromDate = $('#from_date').val();
            var dtpToDate = $('#to_date').val();
            var row = $('#row').find(":selected").val();
            // var branch_id = $('#branch_id').find(":selected").val();
            var csrf = '{{ csrf_token() }}';
            var myurl = '{{ route('account.report.general.ledger.search') }}';

            var dataString = {
                cmbCode: cmbCode,
                dtpYear: dtpYear,
                dtpFromDate: dtpFromDate,
                dtpToDate: dtpToDate,
                row: row,
                page: pageNo,
                _token: csrf
            };

            $.ajax({
                type: "POST",
                url: myurl,
                data: dataString,
                headers: {
                    'X-CSRF-TOKEN': csrf // Send CSRF token in the headers
                },
                success: function(data) {
                    $('#getreceivedpaymentreport').html(data);
                },
                error: function(xhr, status, error) {
                    console.error("Error occurred:", error);
                }
            });
        }
    </script>
@endpush
