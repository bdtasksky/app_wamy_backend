<!-- Include the CSS -->
{{-- <link rel="stylesheet" href="{{ asset('application/modules/Accounts/assets/reports/general_ledger_report_script.css') }}"> --}}

<style>
    .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
        z-index: 3;
        color: #fff!important;
        cursor: default;
        background-color: #37a000!important;
        border-color: #37a000!important;
    }

    .pagination>li>a, .pagination>li>span {
        position: relative;
        float: left;
        padding: 6px 12px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #37a000!important;
        text-decoration: none;
        background-color: #fff!important;
        border: 1px solid #ddd!important;
    }
</style>

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
                <h5 class="mt-10 inv_no">{{ __('language.bank_ledger') . ' ' . __('language.report') . ' (' . $account_name . ')' }}</h5>
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
                                <strong>{{ __('language.bank_ledger_of') . ' ' .'' . ' on ' . \Carbon\Carbon::parse($dtpFromDate)->format('d-m-Y') . ' To ' . \Carbon\Carbon::parse($dtpToDate)->format('d-m-Y') }}</strong>
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
                                        @if ($l_data->v_voucher_type_id!= 0)
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

