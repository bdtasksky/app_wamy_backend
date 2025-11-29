@extends('backend.layouts.app')
@section('title', __('language.edit_voucher'))
@push('css')
@endpush
@section('content')
<link rel="stylesheet" href="{{ asset('application/modules/Accounts/assets/css/bootstrapClass.css') }}">
<div class="row">
    <div class="col-sm-12 col-md-12">
        
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>{{ __('language.edit_voucher') }}
                        <div class="btn-group pull-right form-inline">
                        
                                <div class="form-group">
                                    <a href="{{ route('accounts.voucher.list') }}" class="btn btn-primary btn-md pull-right"><i class="fa fa-list" aria-hidden="true"></i>
                                        {{ __('language.vouchers') }}</a>
                                </div>
                          
                        </div>
                    </h4>
                </div>
            </div>
            <div class="panel-body">
                @if(session('exception'))
                    <div class="alert alert-danger">
                        {{ session('exception') }}
                    </div>
                @endif
                <form action="{{ route('accounts.voucher.save') }}" method="POST">
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
                                <input type="text" class="form-control financialyear" name="date" value="{{ $voucherMaster->VoucherDate ?? date('Y-m-d') }}" readonly="readonly" required>
                            </div>
                        </div>
                        <div class="form-group mb-2 mx-0 row">
                            <label for="acc_coa_id" class="col-sm-3 col-form-label ps-0">{{ __('language.remarks') }}</label>
                            <div class="col-lg-9">
                                <textarea class="form-control" name="remarks">{{ $voucherMaster->Remarks ?? '' }}</textarea>
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
                                        <select name="debits[{{ $key + 1 }}][subcode_id]" id="subtype_{{ $key + 1 }}" class="form-control select-basic-single" {{ empty($voucher->subtype_id) ? 'disabled' : '' }}>
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
</div>
<script>
    window.appData = {
        subTypeUrl: "{{ route('subtype.by-id', ['id' => ':id']) }}",
        subTypeCodeUrl: "{{ route('subtype.by-code', ['id' => ':id'] ) }}"
    };
  </script>
<script src="{{ module_asset('Accounts/js/account-ledger.js') }}" type="text/javascript"></script>
<script src="{{ module_asset('Accounts/js/row-more.js') }}" type="text/javascript"></script>
@endsection