{{-- <link rel="stylesheet" href="{{ asset('application/modules/Accounts/assets/reports/general_ledger_report_script.css') }}"> --}}
<style>
    .pagination>.active>a,
    .pagination>.active>a:focus,
    .pagination>.active>a:hover,
    .pagination>.active>span,
    .pagination>.active>span:focus,
    .pagination>.active>span:hover {
        z-index: 3;
        color: #fff !important;
        cursor: default;
        background-color: #37a000 !important;
        border-color: #37a000 !important;
    }

    .pagination>li>a,
    .pagination>li>span {
        position: relative;
        float: left;
        padding: 6px 12px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #37a000 !important;
        text-decoration: none;
        background-color: #fff !important;
        border: 1px solid #ddd !important;
    }
</style>

<div class="row mb-3" id="printArea">
    <div class="col-sm-12 col-md-12">
        <div class="card" style="border: 0;">
            @php
                $path = asset(!empty($setting->logo) ? $setting->logo : 'assets/img/icons/mini-logo.png');
                $newformDate = date('d-M-Y', strtotime($dtpFromDate));
                $newToDate = date('d-M-Y', strtotime($dtpToDate));
            @endphp

            <div class="card-body" id="printArea">
                <div class="text-center">
                    {{-- <img src="{{ $path }}" alt="logo"> --}}
                    <h5 class="mb-0">{{ $setting->title }}</h5>
                    <h5 class="mt-10">{{ __('language.sub_ledger') . ' ' . __('language.report') }}</h5>
                    {{-- <h5 class="mt-10">{{ $account_name . ' (' . $subtype_name . '-' . $subcode . ')' }}</h5> --}}
                    <h5>As on {{ $newformDate }} To {{ $newToDate }}</h5>
                    <h5>{{ $branch_name }}</h5>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <strong>{{ __('language.date') }}: {{ now()->format('d-M-Y') }}</strong><br><br>
                    </div>
                </div>

                <div class="table-responsive">
                    <table width="99%" align="center" class="table table-bordered table-hover" cellpadding="5"
                        cellspacing="5" border="2">
                        <tr class="voucherList" align="center">
                            <td colspan="4" style="background: #22376d9c!important">
                                <font size="+1" class="general_ledger_report_fontfamily">
                                    <strong>{{ __('language.general_ledger_of') . ' ' . $account_name . ' on ' . date('d-m-Y', strtotime($dtpFromDate)) . ' To ' . date('d-m-Y', strtotime($dtpToDate)) }}</strong>
                                </font>
                            </td>
                        </tr>

                        <tr class="voucherList">
                            <td style="background: #efefef!important" width="5%">
                                <strong>{{ __('language.sl') }}</strong>
                            </td>
                            <td style="background: #efefef!important" width="10%">
                                <strong>{{ __('language.name') }}</strong>
                            </td>
                            <td style="background: #efefef!important" width="10%" align="right">
                                <strong>{{ __('language.closing_balance') }}</strong>
                            </td>
                        </tr>

                        <tbody>
                            @foreach ($ledger_data as $key => $l_data)
                                @php
                                    $style = $loop->iteration % 2 == 0 ? '#efefef!important' : '';
                                @endphp
                                <tr class="{{ $loop->iteration % 2 == 0 ? '' : 'voucherList' }}">
                                    <td style="background:{{ $style }}">{{ $loop->iteration }}</td>
                                    <td style="background:{{ $style }}">{{ $l_data->name }}</td>
                                    <td style="background:{{ $style }}" align="right" class="closing_balance">
                                        <strong>{{ $l_data->closing_balance }}</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- pagination area -->
<div class="text-right" style="margin-right:2%">
    <nav aria-label="Page navigation">
        <ul class="pagination">

            @php
                $totalPages = ceil($totalRow / $row); // Calculate total pages
                $currentPage = $page_n;
            @endphp

            @if ($totalPages > 1)
                <li class="page-item {{ $currentPage > 1 ? '' : 'disabled' }}">
                    <a class="page-link" href="javascript:void(0);"
                        onclick="changePage({{ $currentPage - 1 }})">Previous</a>
                </li>
                @for ($i = 1; $i <= $totalPages; $i++)
                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                        <a class="page-link" href="javascript:void(0);"
                            onclick="changePage({{ $i }})">{{ $i }}</a>
                    </li>
                @endfor
                <li class="page-item {{ $currentPage < $totalPages ? '' : 'disabled' }}">
                    <a class="page-link" href="javascript:void(0);"
                        onclick="changePage({{ $currentPage + 1 }})">Next</a>
                </li>
            @endif
        </ul>
    </nav>
</div>


<!-- print and pdf -->
<div class="text-center general_ledger_report_btn" id="print">
    <input type="button" class="btn btn-warning" name="btnPrint" id="btnPrint" value="Print" onclick="printDiv();" />
    <input type="button" class="btn btn-success" value="PDF" onclick="getPDF('printArea');" />
</div>
