@extends('backend.layouts.app')
@section('title', __('language.deferred_report'))
@push('css')
@endpush
@section('content')

    <style>
        .pagination>.active>a,
        .pagination>.active>a:focus,
        .pagination>.active>a:hover,
        .pagination>.active>span,
        .pagination>.active>span:focus,
        .pagination>.active>span:hover {
            z-index: 3;
            color: #fff;
            cursor: default;
            background-color: #37a000;
            border-color: #37a000;
        }

        .pagination>li>a,
        .pagination>li>span {
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


    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-17 fw-semi-bold mb-0">{{ __('language.deferred_report') }}</h6>
                </div>
                <div class="text-end">
                    <div class="actions">
                        <button type="button" class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne"> <i class="fas fa-filter"></i> {{__('language.filter')}}</button>
                        @can('accounts.read')
                                <a href="{{ route('accounts.voucher.deferredList') }}" class="btn btn-primary btn-md pull-right">
                                    <i class="fa fa-list" aria-hidden="true"></i> {{ __('language.deferred_vouchers') }}
                                </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">
                    <div id="flush-collapseOne" class="accordion-collapse collapse bg-white mb-4"
                        aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                        <!-- Flex Container with justify-content-between to keep the button at the end -->
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Form -->
                            <div class="w-100 row g-3 d-flex flex-nowrap" action="{{ route('accounts.voucher.getList') }}">

                                <!-- Financial Year Dropdown -->
                                <div class="col form-group form-group-new">
                                    <label for="dtpFromDate">{{ __('language.financial_year') }} :</label>
                                    <select id="financial_year" class="form-control select-basic-single" name="dtpYear">
                                        <option value="">{{ __('language.select_financial_year') }}</option>
                                        @foreach ($financialYears as $year)
                                            <option value="{{ $year->title }}" data-start_date="{{ $year->start_date }}"
                                                data-end_date="{{ $year->end_date }}"
                                                @isset($activeFinancialYear)
                        @if ($activeFinancialYear->title == $year->title) selected @endif
                    @endisset>
                                                {{ $year->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- From Date -->
                                <div class="col form-group form-group-new">
                                    <label for="dtpFromDate">{{ __('language.from_date') }} :</label>
                                    <input type="text" name="dtpFromDate"
                                        value="{{ isset($activeFinancialYear) ? $activeFinancialYear->start_date : '' }}"
                                        class="form-control" id="from_date" />
                                </div>

                                <!-- To Date -->
                                <div class="col form-group form-group-new">
                                    <label for="dtpToDate">{{ __('language.to_date') }} :</label>
                                    <input type="text" class="form-control" name="dtpToDate"
                                        value="{{ isset($activeFinancialYear) ? $activeFinancialYear->end_date : '' }}"
                                        id="to_date" />
                                </div>

                                <!-- Voucher Type -->
                                <div class="col form-group form-group-new">
                                    <label>{{ __('language.voucher_type') }} :</label>
                                    <select name="voucher_type" id="voucher_type" class="form-control select-basic-single"
                                        required>
                                        <option value="-1" selected>{{ __('language.all_vouchers') }}</option>
                                        @foreach ($voucher_types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                        <option value="0">{{ __('language.error_vouchers') }}</option>
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="col form-group form-group-new">
                                    <label for="status">{{ __('language.status') }} :</label>
                                    <select name="status" id="status" class="form-control select-basic-single" required>
                                        <option value="-1" selected>{{ __('language.both_status') }}</option>
                                        <option value="0">{{ __('language.pending') }}</option>
                                        <option value="1">{{ __('language.approved') }}</option>
                                    </select>
                                </div>
                                <div class="col form-group form-group-new">
                                    <label for="status">{{ __('language.voucher_no') }} :</label>
                                    <input type="text" class="form-control"
                                        placeholder="{{ __('language.voucher_no') }}" id="voucher_no">
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

                                <!-- Rows per page -->
                                <div class="col form-group form-group-new">
                                    <label for="row">{{ __('language.row') }} :</label>
                                    <select id="row" class="form-control select-basic-single" name="row">
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="500">500</option>
                                        <option value="-1">{{ __('language.all') }}</option>
                                    </select>
                                </div>

                                <!-- Submit Button -->
                                <div class="col d-flex align-items-end">
                                    <button type="button" id="filter-form" class="btn btn-success"
                                        onclick="loadTransactions(1)">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            @include('backend.layouts.common.validation')
            @include('backend.layouts.common.message')
            <div class="table-responsive">
                <table width="100%" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('language.sl') }}</th>
                            <th>{{ __('language.voucher_no') }}</th>
                            <th>{{ __('language.date') }}</th>
                            <th>{{ __('language.remark') }}</th>
                            <th class="text-end">{{ __('language.amount') }}</th>
                            <th class="text-center">{{ __('language.status') }}</th>
                            <th class="text-center">{{ __('language.action') }}</th>
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
    @include('accounts::modal.voucher_details')
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="attachmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Attachment Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="attachmentPreview"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        window.appData = {
            getListUrl: "{{ route('accounts.voucher.getDeferredReport') }}",
            voucherDetailsUrl: "{{ route('accounts.voucher.details') }}",
            voucherDetailsChildrenUrl: "{{ route('accounts.voucher.detailChildren') }}",
        };
    </script>
    <script src="{{ asset('public/backend/assets/sweetalert.js') }}"></script>
    <script src="{{ module_asset('Accounts/js/deferred-voucher-report.js?v=' . time()) }}"></script> <!-- Your custom script -->
    <script>
        $(document).on("click", ".view-attachment", function() {
            var fileUrl = $(this).data("url");
            var fileType = $(this).data("type").toLowerCase();
            var previewHtml = "";

            if (["jpg", "jpeg", "png", "gif", "webp"].includes(fileType)) {
                previewHtml = `<img src="${fileUrl}" class="img-fluid" style="max-height:500px;">`;
            } else if (fileType === "pdf") {
                previewHtml = `<iframe src="${fileUrl}" width="100%" height="500px"></iframe>`;
            } else {
                previewHtml =
                    `<p>Unsupported file type. <a href="${fileUrl}" target="_blank">Click here to download</a>.</p>`;
            }

            $("#attachmentPreview").html(previewHtml);
            $("#attachmentModal").modal("show");
        });
    </script>
@endpush
