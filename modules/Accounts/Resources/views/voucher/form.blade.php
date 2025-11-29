@extends('backend.layouts.app')
@section('title', __('language.transaction_entry_form'))
@push('css')
@endpush
@section('content')


@include('backend.layouts.common.validation')
@include('backend.layouts.common.message')

<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fs-17 fw-semi-bold mb-0">{{ __('language.transaction_entry_form')}}</h6>
            </div>
            <div class="text-end">
                <div class="actions">
                    @can('accounts.read')
                        {{-- <div class="form-group">
                            <a href="{{ route('accounts.voucher.list') }}" class="btn btn-primary btn-md pull-right">
                                <i class="fa fa-list" aria-hidden="true"></i> {{ __('language.vouchers') }}
                            </a>
                        </div> --}}
                    @endcan
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">

        @if (session('exception'))
            <div class="alert alert-danger">
                {{ session('exception') }}
            </div>
        @endif

        <form action="{{ route('accounts.voucher.save') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="rev_code" name="rev_code">

            <div class="row">
                <input type="hidden" name="voucher_no" value="">
                
                <div class="col-md-6">
                    
                    <div class="form-group mb-2 mx-0 row">
                        <label for="voucher_type" class="col-sm-3 col-form-label ps-0">{{ __('language.voucher_type') }}<span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <select name="voucher_type" id="voucher_type" class="form-control select-basic-single" required>
                                <option value="">{{ __('language.select_one') }}</option>
                                @foreach ($voucherTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <input type="hidden" id="financial_year" value="{{ $financialYears->title }}" >
                    <input type="hidden" id="start_date" value="{{ $financialYears->start_date }}" >
                    <input type="hidden" id="end_date" value="{{ $financialYears->end_date }}" >

                    <div class="form-group mb-2 mx-0 row">
                        <label for="acc_coa_id" class="col-sm-3 col-form-label ps-0">{{ __('language.date') }}<span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control " id="voucherdate" name="date" value="{{ now()->format('Y-m-d') }}"  required>
                        </div>
                    </div>
                    <div class="form-group mb-2 mx-0 row">
                        <label for="acc_coa_id" class="col-sm-3 col-form-label ps-0">{{ __('language.project') }}</label>
                        <div class="col-lg-9">
                            <select class="form-control select-basic-single py-2 rounded-8" name="project_id">
                                <option value="" selected disabled>{{ __('language.select_project') }}</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-2 mx-0 row">
                        <label for="acc_coa_id" class="col-sm-3 col-form-label ps-0">{{ __('language.schedule_status') }}</label>
                        <div class="col-lg-9">
                            <select class="form-control select-basic-single py-2 rounded-8" name="schedule_status">
                                <option value="" selected disabled>{{ __('language.select_one') }}</option>
                                <option value="IsDeferred">is Deferred</option>
                                <option value="IsInstallment">is Installment</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-2 mx-0 row">
                        <label for="acc_coa_id" class="col-sm-3 col-form-label ps-0">{{ __('language.remarks') }}</label>
                        <div class="col-lg-9">
                            <textarea class="form-control" name="remarks"></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="acc_coa_id" class="col-form-label ps-1">
                        {{ __('language.attachment') }}
                        <span class="btn btn-success-soft btn-sm" id="addmore">
                            <i class="fa fa-plus"></i> Add More
                        </span>
                    </label>
                
                    <div id="fileInputs">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="file" class="form-control" name="attachment[]">
                                    <input type="text" placeholder="Attachment Name" class="form-control" name="attachment_name[]"><br>
                                    <div class="input-group-append">
                                        <span class="input-group-text btn btn-danger-soft btn-sm remove-file">
                                            <i class="fa fa-trash"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <input type="hidden" value="2" id="rowcount">
                <table class="table table-bordered table-hover" id="debtAccVoucher">
                    <thead>
                        <tr>
                            <th width="15%" class="text-center">{{ __('language.account_name') }}</th>
                            <th width="15%" class="text-center">{{ __('language.subtype') }}</th>
                            <th width="20%" class="text-center">{{ __('language.ledger_comment') }}</th>
                            <th width="10%" class="text-center">{{ __('language.debit') }}</th>
                            <th width="10%" class="text-center">{{ __('language.credit') }}</th>
                            <th width="5%" class="text-center">{{ __('language.action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="creditvoucher">
                        @for ($i = 1; $i <= 2; $i++)
                            <tr>
                                <td>
                                    <select name="debits[{{ $i }}][coa_id]" id="cmbCode_{{ $i }}" required class="form-control select-basic-single account_name" onchange="load_subtypeOpen(this.value, {{ $i }})" >
                                        <option selected disabled>{{ __('language.select_account') }}</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="debits[{{ $i }}][subcode_id]" id="subtype_{{ $i }}" disabled class="form-control select-basic-single" onchnage="get_subtypeCode">
                                        <option value="">{{ __('language.select_subtype') }}</option>
                                    </select>
                                    <input type="hidden" name="debits[{{ $i }}][subtype_id]" id="stype_{{ $i }}">
                                </td>
                                <td>
                                    <input type="text" name="debits[{{ $i }}][ledger_comment]" class="form-control text-end" id="ledger_comment" autocomplete="off">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="debits[{{ $i }}][debit]" value="" class="form-control total_dprice text-end" id="txtDebit_{{ $i }}" onkeyup="calculationDebtOpen({{ $i }})" autocomplete="off">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="debits[{{ $i }}][credit]" value="" class="form-control total_cprice text-end" id="txtCredit_{{ $i }}" onkeyup="calculationCreditOpen({{ $i }})" autocomplete="off">
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-danger btn-sm" type="button" value="Delete" onclick="deleteRowDebtOpen(this)" autocomplete="off"><i class="fa fa-trash"></i></button>
                                </td>
                                <input type="hidden" name="reversehead_code[]" class="form-control reversehead_code" id="reversehead_code_{{ $i }}" readonly="">
                            </tr>
                        @endfor
                    </tbody>

                    <tfoot>
                        <tr>
                            <td>
                                <input type="button" id="add_more" class="btn btn-success" name="add_more" onclick="addaccountOpen('creditvoucher');" value="{{ __('language.add_more') }}" autocomplete="off">
                            </td>
                            <td colspan="2" class="text-end">
                                <label for="reason" class="col-form-label">{{ __('language.total') }}</label>
                            </td>
                            <td class="text-end">
                                <input type="text" id="grandTotald" class="form-control text-end" name="grand_totald" value="" readonly="readonly" autocomplete="off">
                            </td>
                            <td class="text-end">
                                <input type="text" id="grandTotalc" class="form-control text-end" name="grand_totalc" value="" readonly="readonly" autocomplete="off">
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success submit_button" id="create_submit">{{ __('language.save') }}</button>
                    <input type="hidden" name="" id="headoption" value="<option value=''>{{ __('language.Please select') }}</option>@foreach ($accounts as $acc2)<option value='{{ $acc2->id }}'>{{ $acc2->account_name }}</option>@endforeach">
                </div>
            </div>

        </form>
    </div>
</div>

@endsection
@push('js')
<script>
    window.appData = {
        subTypeUrl: "{{ route('subtype.by-id', ['id' => ':id']) }}",
        subTypeCodeUrl: "{{ route('subtype.by-code', ['id' => ':id'] ) }}"
    };
</script>
<script src="{{ module_asset('Accounts/js/account-ledger.js?v=5') }}" type="text/javascript"></script>
<script src="{{ module_asset('Accounts/js/row-more.js?v=5') }}" type="text/javascript"></script>

<script>

    $(document).on('click', '.remove-file', function() {
        $(this).closest('.row').remove();
    });


    var startDate = "{{ $financialYears->start_date }}";
    var endDate = "{{ $financialYears->end_date }}";
    // Reinitialize the datepickers with the new date range
    $("#voucherdate").datepicker({
        dateFormat: "yy-mm-dd",
        minDate: startDate,
        maxDate: endDate
    });
</script> 
@endpush

