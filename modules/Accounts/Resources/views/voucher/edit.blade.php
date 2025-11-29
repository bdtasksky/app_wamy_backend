@extends('backend.layouts.app')
@section('title', __('language.edit_voucher'))
@push('css')
<link rel="stylesheet" href="{{ asset('application/modules/Accounts/assets/css/bootstrapClass.css') }}">
@endpush
@section('content')


<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fs-17 fw-semi-bold mb-0">{{ __('language.edit_voucher')}}</h6>
            </div>
            <div class="text-end">
                <div class="actions">

                    <a href="{{ route('accounts.voucher.list') }}" class="btn btn-primary btn-md pull-right"><i class="fa fa-list" aria-hidden="true"></i>
                        {{ __('language.vouchers') }}</a>
                  
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="panel-body">
            @if(session('exception'))
                <div class="alert alert-danger">
                    {{ session('exception') }}
                </div>
            @endif
            <form action="{{ route('accounts.voucher.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
            <input type="hidden" id="rev_code" name="rev_code">
            <div class="row">
                <input type="hidden" name="id" value="{{ $voucherMaster->id }}">
                <input type="hidden" name="voucher_no" value="">
                <div class="col-md-6">
                    <div class="form-group mb-2 mx-0 row">
                        <label for="voucher_type" class="col-sm-3 col-form-label ps-0">{{ __('language.voucher_type') }}<span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <select name="voucher_type" id="voucher_type" class="form-control select-basic-single" required>
                                <option value="">{{ __('language.select_one') }}</option>
                                @foreach($voucherTypes as $type)
                                    <option value="{{ $type->id }}" {{ $type->id == $voucherMaster->VoucharTypeId ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-2 mx-0 row">
                        <label for="acc_coa_id" class="col-sm-3 col-form-label ps-0">{{ __('language.date') }}<span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control financialyear" name="date" value="{{ $voucherMaster->VoucherDate ?? date('Y-m-d') }}"  required>
                        </div>
                    </div>
                    <div class="form-group mb-2 mx-0 row">
                        <label for="acc_coa_id" class="col-sm-3 col-form-label ps-0">{{ __('language.project') }}</label>
                        <div class="col-lg-9">
                            <select class="form-control select-basic-single py-2 rounded-8" name="project_id">
                                <option value="" selected disabled>{{ __('language.select_project') }}</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" {{ ($voucherMaster->project_id==$project->id?'selected':'') }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-2 mx-0 row">
                        <label for="acc_coa_id" class="col-sm-3 col-form-label ps-0">{{ __('language.schedule_status') }}</label>
                        <div class="col-lg-9">
                            <select class="form-control select-basic-single py-2 rounded-8" name="schedule_status">
                                <option value="" selected disabled>{{ __('language.select_one') }}</option>
                                <option value="IsDeferred" {{ ($voucherMaster->ScheduleStatus=='IsDeferred'?'selected':'') }}>is Deferred</option>
                                <option value="IsInstallment" {{ ($voucherMaster->ScheduleStatus=='IsInstallment'?'selected':'') }}>is Installment</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-2 mx-0 row">
                        <label for="acc_coa_id" class="col-sm-3 col-form-label ps-0">{{ __('language.remarks') }}</label>
                        <div class="col-lg-9">
                            <textarea class="form-control" name="remarks">{{ $voucherMaster->Remarks ?? '' }}</textarea>
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
                        @if ($accVoucherAttachment)
                            @foreach($accVoucherAttachment as $attachment)
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <!-- Existing Attachment Display -->
                                            <input type="hidden" name="existing_attachment_ids[]" value="{{ $attachment->id }}">
                                            <a class="btn btn-info-soft btn-sm m-1" href="{{  asset('public/storage/'.$attachment->file_name) }}" target="__blank" download="{{ basename($attachment->file_name) }}">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <input type="text" class="form-control" name="existing_attachment_names[]" value="{{ $attachment->attachment_name }}" placeholder="Attachment Name">
                                            
                                            <div class="input-group-append deleteAttachment"data-delete-route="{{route('accounts.voucher.delete.attachment',$attachment->id)}}">
                                                <span class="input-group-text btn btn-danger-soft btn-sm remove-file">
                                                    <i class="fa fa-trash"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Add New Attachments Section -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="input-group">
                                <input type="file" class="form-control" name="attachment[]">
                                <input type="text" placeholder="Attachment Name" class="form-control" name="attachment_name[]">
                                <div class="input-group-append">
                                    <span class="input-group-text btn btn-danger-soft btn-sm remove-file">
                                        <i class="fa fa-trash"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                        @php
                            $i = 1;
                            $debit = 0;
                            $credit = 0;
                        @endphp
                        @foreach($voucherDetails as $key => $voucher)
                            @php

                            // dd($voucher);
                                $i++;
                                $debit += $voucher->Dr_Amount;
                                $credit += $voucher->Cr_Amount;
                            @endphp

                            <tr>
                                <td>
                                    <select name="debits[{{ $key + 1 }}][coa_id]" id="cmbCode_{{ $key + 1 }}" required class="form-control select-basic-single account_name" onchange="load_subtypeOpen(this.value,{{ $key + 1 }})">
                                        <option selected disabled>{{ __('language.select_amount') }}</option>

                                        

                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ $account->id == $voucher->acc_coa_id ? 'selected' : '' }}>{{ $account->account_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="debits[{{ $key + 1 }}][subcode_id]" id="subtype_{{ $key + 1 }}" class="form-control select-basic-single" >
                                        <option value="">{{ __('language.select_subtype') }}</option>
                                        @if($voucher->subtype_id)
                                            <option value="{{ $voucher->subcode_id }}" selected>{{ $voucher->name ?? '' }}</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="debits[{{ $key + 1 }}][subtype_id]" id="stype_{{ $key + 1 }}" value="{{ $voucher->subtype_id }}">
                                </td>
                                <td>
                                    <input type="text" name="debits[{{ $key + 1 }}][ledger_comment]" class="form-control text-end" id="ledger_comment" autocomplete="off" value="{{ $voucher->LaserComments }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="debits[{{ $key + 1 }}][debit]" value="{{ $voucher->Dr_Amount }}" class="form-control total_dprice text-end" id="txtDebit_{{ $key + 1 }}" onkeyup="calculationDebtOpen({{ $key + 1 }})" autocomplete="off">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="debits[{{ $key + 1 }}][credit]" value="{{ $voucher->Cr_Amount }}" class="form-control total_cprice text-end" id="txtCredit_{{ $key + 1 }}" onkeyup="calculationCreditOpen({{ $key + 1 }})" autocomplete="off">
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-danger btn-sm" type="button" value="Delete" onclick="deleteRowDebtOpen(this)" autocomplete="off"><i class="fa fa-trash"></i></button>
                                </td>
                                <input type="hidden" name="reversehead_code[]" class="form-control reversehead_code" id="reversehead_code_{{ $key + 1 }}" readonly="">
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <input type="button" id="add_more" class="btn btn-success" name="add_more" onclick="addaccountOpen('creditvoucher');" value="Add More" autocomplete="off">
                            </td>
                            <td colspan="2" class="text-end">
                                <label for="reason" class="col-form-label">{{ __('language.total') }}</label>
                            </td>
                            <td class="text-end">
                                <input type="text" id="grandTotald" class="form-control text-end" name="grand_totald" value="{{ $debit }}" readonly="readonly" autocomplete="off">
                            </td>
                            <td class="text-end">
                                <input type="text" id="grandTotalc" class="form-control text-end" name="grand_totalc" value="{{ $credit }}" readonly="readonly" autocomplete="off">
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success submit_button" id="create_submit">{{ __('language.update') }}</button>
                    <input type="hidden" name="" id="headoption" value="<option value=''> {{ __('language.please_select') }}</option>@foreach($accounts as $acc2)<option value='{{ $acc2->id }}'>{{ $acc2->account_name }}</option>@endforeach">
                </div>
            </div>
            </form>
        </div>
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

    // $('#addmore').on('click', function() {
    //     var fileInput = `
    //         <div class="row mb-3">
    //             <div class="col-md-12">
    //                 <div class="input-group">
    //                     <input type="file" class="form-control" name="attachment[]">
    //                     <input type="text" placeholder="Attachment Name" class="form-control" name="attachment_name[]"><br>
    //                     <div class="input-group-append">
    //                         <span class="input-group-text btn btn-danger-soft btn-sm remove-file">
    //                             <i class="fa fa-trash"></i>
    //                         </span>
    //                     </div>
    //                 </div>
    //             </div>
    //         </div>
    //     `;
    //     $('#fileInputs').append(fileInput);
    // });

    $(document).on('click', '.remove-file', function() {
        $(this).closest('.row').remove();
    });

    $(document).ready(function() {

        $('.deleteAttachment').on('click', function() {

            var submit_url = $(this).attr('data-delete-route');

            // Show SweetAlert2 confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET', // Or 'DELETE' if appropriate
                        url: submit_url,
                        data: {"_token": "{{ csrf_token() }}"},
                        dataType: 'json',
                        success: function(response) {
                            if (response.success == true) {
                                $("#fileInputs").load(" #fileInputs > *");
                                Swal.fire('Deleted!', response.message, 'success');
                            } else if (response.success == 'exist') {
                                Swal.fire('Warning!', response.message, 'warning');
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                        }
                    });
                }
            });
        });
    });


    // var startDate = "{{ $financialYears->start_date }}";
    // var endDate = "{{ $financialYears->end_date }}";
    // $("#voucherdate").datepicker({
    //     dateFormat: "yy-mm-dd",
    //     minDate: startDate,
    //     maxDate: endDate
    // });
</script>
@endpush
