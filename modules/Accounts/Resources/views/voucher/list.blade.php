@extends('backend.layouts.app')
@section('title', __('language.voucher_list'))
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


    <div class="card px-3 py-3">

        <div class="card-heading">
            <div class="card-title">
                <h4>{{ __('language.vouchers') }}</h4>
            </div>
        </div>

        <!-- Flex Container with justify-content-between to keep the button at the end -->
        <div class="d-flex justify-content-between align-items-center">
            <!-- Form -->
            <div class="w-100 mt-4 row g-3 d-flex flex-nowrap" action="{{ route('accounts.voucher.getList') }}">

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
                        value="{{ isset($activeFinancialYear) ? $activeFinancialYear->end_date : '' }}" id="to_date" />
                </div>

                <!-- Voucher Type -->
                <div class="col form-group form-group-new">
                    <label>{{ __('language.voucher_type') }} :</label>
                    <select name="voucher_type" id="voucher_type" class="form-control select-basic-single" required>
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
                    @can('create_vouchers')
                        <a style="margin-left:2%; white-space: nowrap" href="{{ route('accounts.voucher.form') }}"
                            class="btn btn-primary btn-md">
                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                            {{ __('language.create_voucher') }}
                        </a>
                    @endcan
                </div>
            </div>
        </div>

    </div>





    <div class="row">
        <div class="col-sm-12 col-md-12">

            <div class="card mt-5">
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
    <!-- Schedule Status Modal -->
    <div class="modal fade" id="scheduleStatusModal" role="dialog"
    aria-labelledby="scheduleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- modal-lg for wider modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleStatusModalLabel">Deferred Schedule Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="deferredScheduleForm">
                    <!-- Hidden fields to store transaction data -->
                    <input type="hidden" id="modalVoucherMasterId" name="voucher_master_id">
                    <input type="hidden" id="modalTotalAmount" name="total_amount">
                    <input type="hidden" id="save-deferred-schedule-url"
                        value="{{ route('accounts.voucher.save_deferred_schedule') }}">
                    <input type="hidden" id="remove-deferred-status-url"
                        value="{{ route('accounts.voucher.remove_deferred') }}">
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>Voucher No:</strong> <span id="modalVoucherNo"></span></p>
                            <p><strong>Total Amount:</strong> <span id="modalTotalAmountText"></span></p>
                            <p><strong>Date:</strong> <span id="modalVoucherDate"></span></p>
                            <div class="form-group">
                                <label for="numberOfInstallments">Expense Head *</label>
                                <select class="form-control select-basic-single" name="expense_head"
                                    id="expense_head" required>
                                    <option value="" selected disabled>{{ __('language.select_one') }}</option>
                                    @foreach ($expenses as $expense)
                                        <option value="{{ $expense->id }}">{{ $expense->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="numberOfInstallments">Number of Installments (Months) *</label>
                                <input type="number" class="form-control" id="numberOfInstallments"
                                    name="number_of_installments" required min="1">
                            </div>

                            <div class="form-group">
                                <label for="deferredEffectiveDate">Deferred Effective Date*</label>
                                <input type="text" class="form-control effective_date_picker"
                                    id="deferredEffectiveDate" name="effective_date" required>
                            </div>
                            <div class="form-group">
                                <label for="modalRemarks">Description:</label>
                                <textarea class="form-control" id="modalRemarks" name="remarks" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- This div will be populated with the installment details table -->
                    <div id="installmentDetails" class="mt-3"></div>
                </form>
            </div>
            <div class="modal-footer">
                <p id="modalVoucherStatus" class="text-danger">Note: Your Voucher is Pending. Please Apporve it then you can submit</p>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
                <button type="button" id="saveDeferredSchedule" class="btn btn-primary">Save Schedule</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
    <script>
        window.appData = {
            getListUrl: "{{ route('accounts.voucher.getList') }}",
            voucherDetailsUrl: "{{ route('accounts.voucher.details') }}",
            voucherReverseUrl: "{{ route('accounts.voucher.reverse') }}",
            voucherDeleteUrl: "{{ route('accounts.voucher.delete') }}",
            voucherBalanceUrl: "{{ route('accounts.voucher.get_deffered_balance', [':id']) }}",
        };

        var baseUrl = "{{ route('accounts.voucher.edit', ['id' => '__ID__']) }}";
        const canUpdateVoucher = @json(auth()->user()->can('update_voucher'));
        const canDeleteVoucher = @json(auth()->user()->can('delete_voucher'));
    </script>
    <script src="{{ asset('public/backend/assets/sweetalert.js') }}"></script>
    <script src="{{ module_asset('Accounts/js/voucher.js?v=' . time()) }}"></script> <!-- Your custom script -->
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
                previewHtml = `<p>Unsupported file type. <a href="${fileUrl}" target="_blank">Click here to download</a>.</p>`;
            }

            $("#attachmentPreview").html(previewHtml);
            $("#attachmentModal").modal("show");
        });

    </script>
@endpush
