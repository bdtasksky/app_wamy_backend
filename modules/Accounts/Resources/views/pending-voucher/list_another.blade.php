@extends('backend.layouts.app')
@section('title', __('language.pending_voucher_list'))
@section('content')

<style>
    .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
        z-index: 3;
        color: #fff;
        cursor: default;
        background-color: #37a000;
        border-color: #37a000;
    }

    .pagination>li>a, .pagination>li>span {
        position: relative;
        float: left;
        padding: 6px 12px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #37a000;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #ddd;
    }
</style>

<div class="row">
    <div class="col-sm-12 col-md-12">

        <div class="card px-4 py-3">

            <div class="card-heading">
                <div class="card-title">
                    <h4>{{ __('language.voucher_approval') }}</h4>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div action="{{ route('accounts.pending.get.list') }}" class="w-100 mt-4 row g-3 d-flex flex-nowrap">
                        <div class="col form-group form-group-new">
                            <label for="dtpFromDate">Financial Year :</label>
                            <select id="financial_year" class="form-control" name="dtpYear">
                                <option value="">{{ __('language.Select Financial Year') }}</option>
                                @foreach ($financialYears as $year)
                                    <option value="{{ $year->title }}" 
                                        data-start_date="{{ $year->start_date }}" 
                                        data-end_date="{{ $year->end_date }}"
                                        @isset($activeFinancialYear) 
                                            @if($activeFinancialYear->title == $year->title) selected @endif
                                        @endisset>
                                        {{ $year->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                
                        <!-- From Date -->
                        <div class="col form-group form-group-new">
                            <label for="dtpFromDate">{{ __('language.from_date') }} :</label>
                            <input type="date" name="dtpFromDate" value="{{ isset($activeFinancialYear) ? $activeFinancialYear->start_date : '' }}" class="form-control" id="from_date"/>
                        </div>

                        <!-- To Date -->
                        <div class="col form-group form-group-new">
                            <label for="dtpToDate">{{ __('language.to_date') }} :</label>
                            <input type="date" class="form-control" name="dtpToDate" value="{{ isset($activeFinancialYear) ? $activeFinancialYear->end_date : '' }}" id="to_date"/>
                        </div>
                
                        <!-- Voucher Type -->
                        <div class="col form-group form-group-new">
                            <label>{{ __('language.voucher_type') }} :</label>
                            <select name="voucher_type" id="voucher_type" class="form-control" required>
                                <option value="-1" selected>{{ __('language.all_vouchers') }}</option>
                                @foreach ($voucher_types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                                <option value="0">{{ __('language.error_vouchers') }}</option>
                            </select>
                        </div>
                        <div class="col form-group form-group-new">
                            <label for="status">{{ __('language.voucher_no') }} :</label>
                            <input type="text" class="form-control" placeholder="{{ __('language.voucher_no') }}"
                                id="voucher_no">
                        </div>
                        <div class="col form-group form-group-new">
                            <label for="status">{{ __('language.project') }} :</label>
                            <select class="form-control select-basic-single" id="project_id">
                                <option value="" selected>{{ __('language.select_project') }}</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col form-group form-group-new">
                            <label for="status">{{ __('language.schedule_status') }} :</label>
                            <select class="form-control select-basic-single" id="schedule_status">
                                <option value="" selected>{{ __('language.select_one') }}</option>
                                <option value="IsDeferred">is Deferred</option>
                                <option value="IsInstallment">is Installment</option>
                            </select>
                        </div>
                        <div class="col form-group form-group-new">
                            <label for="row">Row :</label>
                            <select id="row" class="form-control" name="row">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                                <option value="-1">All</option>
                            </select>
                        </div>
                
                        <div class="col d-flex align-items-end">
                            <button type="submit" class="btn btn-success" onclick="loadTransactions(1)">Search</button>
                            @can('create_voucher_approval')
                            <button style="margin-left:2%; white-space: nowrap" type="button" class="btn btn-success" onclick = "approveVouchers()">{{ __('language.approved_all_check') }}</button>
                            @endcan
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table width="100%" class="table table-striped table-bordered table-hover" id="pendingvouchers">
                    <thead>
                        <tr>
                            <th width="10%" class="sorting_disabled" rowspan="1" colspan="1" style="width: 163.889px;" aria-label="Check All">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="selectall" autocomplete="off">
                                    <label class="form-check-label" for="selectall">
                                        {{ __('language.check_all') }}
                                    </label>
                                </div>
                            </th>
                            <th>{{ __('language.sl') }}</th>
                            <th>{{ __('language.voucher_no') }}</th>
                            <th>{{ __('language.date') }}</th>
                            <th>{{ __('language.remark') }}</th>
                            <th class="text-end">{{ __('language.amount') }}</th>
                            <th class="text-center">{{ __('language.status') }}</th>
                        </tr>
                    </thead>
                    <tbody id="ledger-body">
                        <tr class="text-center">
                            <td colspan="7">{{ __('language.no_data_found') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav class="text-end" aria-label="Page navigation">
                <ul id="pagination" class="pagination justify-content-center"></ul>
            </nav>
        </div>
    </div>
</div>
@include('accounts::modal.voucher_details')
@endsection
@push('js')
<script>
  window.appData = {
      openBookUrl: "{{ route('accounts.openbook') }}" 
  };
</script>
<script>
    window.appData = {
        getPendingListUrl: "{{ route('accounts.pending.get.list') }}" ,
        voucherDetailsUrl: "{{ route('accounts.voucher.details') }}",
        approveVoucherUrl: "{{ route('accounts.pending.voucher.approve') }}",
        voucherDeleteUrl: "{{ route('accounts.voucher.delete') }}"
    };
var baseUrl = "{{ route('accounts.voucher.edit', ['id' => '__ID__']) }}";

</script>
<script src="{{ module_asset('Accounts/js/voucher_another.js?v=3') }}" type="text/javascript"></script>
@endpush