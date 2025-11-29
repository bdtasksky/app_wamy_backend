@extends('backend.layouts.app')
@section('title', __('language.general_ledger'))
@push('css')
@endpush
@section('content')

<div class="row">
    <div class="col-sm-12 col-md-12">
        @include('accounts::reports.financial_report_header')
        <div class="card fixed-tab-body">
            <div class="card-header">
                <div class="card-title">
                    <h5>
                        {{ __('language.general_ledger') }}
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row form-inline">
                    <div class="col-sm-2 form-group form-group-new empdropdown">
                        <label for="employeelist">{{ __('language.transaction_head') }} <b class="text-danger">*</b> :</label>
                        <select class="form-control select-basic-single select2-hidden-accessible" name="cmbCode" id="cmbCode">
                            <option value="">{{ __('language.select_one') }}</option>
                            @foreach ($general_ledger as $g_data)
                                <option value="{{ $g_data->id }}" {{$cmbCode==$g_data->id?'selected':''}}>{{ $g_data->account_name }} @if( $g_data->is_active==0) ({{ __('language.inactive') }}) @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-1 form-group form-group-new empdropdown">
                        <label for="financial_year">{{ __('language.financial_year') }} :</label>
                        <select id="financial_year" class="form-control select-basic-single select2-hidden-accessible" name="dtpYear">
                            <option value="">Select Financial Year</option>
                            @foreach ($financial_years as $year)
                                <option  value="{{ $year->title }}" 
                                    data-start_date="{{ $year->start_date }}" 
                                    data-end_date="{{ $year->end_date }}"
                                    @if(isset($dtpYear) && $dtpYear == $year->title) selected @endif>
                                    {{ $year->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-2 form-group form-group-new">
                        <label for="from_date">{{ __('language.from_date') }} :</label>
                        <input type="text" value="{{ $dtpFromDate ?? '' }}" class="form-control" id="from_date"/>
                    </div>
                    <div class="col-sm-2 form-group form-group-new">
                        <label for="to_date">{{ __('language.to_date') }} :</label>
                        <input type="text" class="form-control"  value="{{ $dtpToDate ?? '' }}" id="to_date"/>
                    </div>

                    <!-- Branch Dropdown -->
                    

                    <div class="col-sm-1 form-group form-group-new empdropdown">
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

                    <div class="col-sm-2 form-group form-group-new empdropdown">
                         <button class="mt-4 btn btn-success" onclick="getGeneralLedger()">{{ __('language.search') }}</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<style>
    .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
        z-index: 3;
        color: #fff!important;
        cursor: default;
        background-color: #22376d!important;
        border-color: #22376d!important;
    }

    .pagination>li>a, .pagination>li>span {
        position: relative;
        float: left;
        padding: 6px 12px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #22376d!important;
        text-decoration: none;
        background-color: #fff!important;
        border: 1px solid #ddd!important;
    }
</style>

<div class="row mt-5" id="getgeneralLedgerreport">
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
                <h5 class="mt-10">{{ __('language.general_ledger') . ' ' . __('language.report') . ' (' . $account_name . ')' }}</h5>
                <h5>As on {{ $newformDate ?? '' }} To {{ $newToDate ?? '' }}</h5>
                <h5>{{ $branch_name }}</h5>
            </div>

            <div class="row">
                <div class="col-xs-12 text-center">
                    <strong>{{ __('language.date') }}: {{ now()->format('d-M-Y') }}</strong><br><br>
                </div>
            </div>

            <div class="table-responsive">
                <table width="99%" align="center" class="table table-bordered table-hover" cellpadding="5" cellspacing="5" border="2">
                    <tr class="voucherList" align="center">
                        <td colspan="10" style="background: #22376d9c!important">
                            <font size="+1" class="general_ledger_report_fontfamily">
                                <strong>{{ __('language.general_ledger_of') . ' ' .'' . ' on ' . \Carbon\Carbon::parse($dtpFromDate)->format('d-m-Y') . ' To ' . \Carbon\Carbon::parse($dtpToDate)->format('d-m-Y') }}</strong>
                            </font>
                        </td>
                    </tr>

                    <tr class="voucherList">
                        <td style="background: #efefef!important" width="5%"><strong>{{ __('language.sl') }}</strong></td>
                        <td style="background:#efefef!important" width="10%"><strong>{{ __('language.date') }}</strong></td>
                        <td style="background: #efefef!important" width="10%"><strong>{{ __('language.voucher_no') }}</strong></td>
                        <td style="background: #efefef!important"><strong>{{ __('language.rev_acc_name') }}</strong></td>
                        <td style="background: #efefef!important" width="12%"><strong>{{ __('language.remarks') }}</strong></td>
                        <td style="background: #efefef!important" width="10%" align="right"><strong>{{ __('language.voucher_type') }}</strong></td>
                        <td style="background: #efefef!important" width="10%" align="right"><strong>{{ __('language.debit') }}</strong></td>
                        <td style="background: #efefef!important" width="10%" align="right"><strong>{{ __('language.credit') }}</strong></td>
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
                                    <a href="javascript:void(0);" data-id="{{ $l_data->v_voucher_id }}" data-vdate="{{ $l_data->v_date }}" class="v_view" style="margin-right:10px" title="View Voucher">{{ $l_data->v_voucher_no }}</a>
                                </td>
                                <td style="background:{{ $style }}">{{ $l_data->v_rev_acc_name }}</td>
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

    <!-- pagination area -->
    <div class="text-end mt-5" style="margin-right:2%">
    <nav aria-label="Page navigation">
        <ul class="pagination">
            @php
                $totalPages = ceil($totalRow / $row); 
                $currentPage = $page_n;
            @endphp
            @if ($currentPage > 1)
                <li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="changePage({{ $currentPage - 1 }})">Previous</a></li>
            @else
                <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
            @endif

            @for ($i = 1; $i <= $totalPages; $i++)
                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                    <a class="page-link" href="javascript:void(0);" onclick="changePage({{ $i }})">{{ $i }}</a>
                </li>
            @endfor

            @if ($currentPage < $totalPages)
                <li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="changePage({{ $currentPage + 1 }})">Next</a></li>
            @else
                <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
            @endif
        </ul>
    </nav>
    </div>

    <!-- print and pdf -->
    <div class="text-center general_ledger_report_btn mb-5" id="print">
    <input type="button" class="btn btn-warning" name="btnPrint" id="btnPrint" value="{{ __('language.print') }}" onclick="printDiv();" />
    <input type="button" class="btn btn-success" value="{{ __('language.pdf') }}" onclick="getPDF('printArea');"/>
    </div>
</div>
<!-- Include modal for voucher details -->
@include('accounts::modal.voucher_details')
@endsection
@push('js')

<script>

    function getGeneralLedger() {

        var cmbCode = $('#cmbCode').find(":selected").val();

        if (cmbCode == 0 || cmbCode == "") {
            alert("Please select Transaction Head !");
            return false;
        }

        var dtpYear = $('#financial_year').find(":selected").val();
        var dtpFromDate = $('#from_date').val();
        var dtpToDate = $('#to_date').val();
        var row = $('#row').find(":selected").val();
        var branch_id = $('#branch_id').find(":selected").val();
        var page = 1;
        var csrf = '{{ csrf_token() }}';
        var myurl = '{{ route("account.report.general.ledger.search") }}';

        var dataString = {
            cmbCode: cmbCode,
            dtpYear: dtpYear,
            dtpFromDate: dtpFromDate,
            dtpToDate: dtpToDate,
            row: row,
            page: page,
            branch_id: branch_id,
            csrf_test_name: csrf
        };

        $.ajax({
            type: "POST",
            url: myurl,
            data: dataString,
            success: function(data) {
                $('#getgeneralLedgerreport').html(data);
            },
            error: function(xhr, status, error) {
                alert('Please refresh the page to continue');
                // window.location.reload();
            }
        });
    }

    function changePage(pageNo) {
        var cmbCode = $('#cmbCode').find(":selected").val();
        var dtpYear = $('#financial_year').find(":selected").val();
        var dtpFromDate = $('#from_date').val();
        var dtpToDate = $('#to_date').val();
        var row = $('#row').find(":selected").val();
        var branch_id = $('#branch_id').find(":selected").val();
        var csrf = '{{ csrf_token() }}';
        var myurl = '{{ route("account.report.general.ledger.search") }}';

        var dataString = {
            cmbCode: cmbCode,
            dtpYear: dtpYear,
            dtpFromDate: dtpFromDate,
            dtpToDate: dtpToDate,
            row: row,
            page: pageNo,
            branch_id: branch_id,
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
                $('#getgeneralLedgerreport').html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error occurred:", error);
            }
        });
    }
</script>
@endpush