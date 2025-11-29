@extends('backend.layouts.app')
@section('title', __('language.add_opening_balance'))
@push('css')
    <link rel="stylesheet" href="{{ module_asset('Accounts/css/settings_header.css?v_' . date('h_i')) }}">
@endpush
@section('content')
    @include('accounts::settings_header')
    <div class="card mb-4 fixed-tab-body">
        <div class="card-header">
            <div class="panel-title">
                <h4>{{ __('language.add_opening_balance') }}</h4>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('accounts.opening-balance.save') }}">
                @csrf
                <div class="row">
                    <table class="table table-bordered table-hover" id="debtAccVoucher">
                        <thead>
                            <tr>
                                <th width="25%" class="text-center">{{ __('language.account_name') }}</th>
                                <th width="25%" class="text-center">{{ __('language.subtype') }}</th>
                                <th width="20%" class="text-center">{{ __('language.debit') }}</th>
                                <th width="20%" class="text-center">{{ __('language.credit') }}</th>
                                <th width="10%" class="text-center">{{ __('language.action') }}</th>
                            </tr>
                        </thead>
                        <tbody id="debitvoucher">
                            @php
                                $count =
                                    !empty($opening_balance) && count($opening_balance) > 0
                                        ? count($opening_balance)
                                        : 1;
                                $debit = 0;
                                $credit = 0;
                            @endphp

                            <input type="hidden" id="countstart" value="{{ $count }}">

                            @if (!empty($opening_balance) && count($opening_balance) > 0)
                                @foreach ($opening_balance as $sl => $item)
                                    <tr>
                                        <td>
                                            <select name="opening_balances[{{ $sl + 1 }}][coa_id]"
                                                id="cmbCode_{{ $sl + 1 }}" class="select-basic-single"
                                                onchange="load_subtypeOpen(this.value, {{ $sl + 1 }})">
                                                <option value="">{{ __('language.select_amount') }}</option>
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ $item->acc_coa_id == $account->id ? 'selected' : '' }}>
                                                        {{ $account->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="opening_balances[{{ $sl + 1 }}][subcode_id]"
                                                id="subtype_{{ $sl + 1 }}" class="select-basic-single">
                                                <option value="">{{ __('language.select_subtype') }}</option>
                                            </select>
                                            <input type="hidden" name="opening_balances[{{ $sl + 1 }}][subtype_id]"
                                                id="stype_{{ $sl + 1 }}">
                                        </td>
                                        <td>
                                            <input type="number" name="opening_balances[{{ $sl + 1 }}][debit]"
                                                value="{{ $item->debit ?? 0 }}" class="form-control total_dprice text-end"
                                                id="txtDebit_{{ $sl + 1 }}"
                                                onkeyup="calculationDebtOpen({{ $sl + 1 }})" autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="number" name="opening_balances[{{ $sl + 1 }}][credit]"
                                                value="{{ $item->credit ?? 0 }}" class="form-control total_cprice text-end"
                                                id="txtCredit_{{ $sl + 1 }}"
                                                onkeyup="calculationCreditOpen({{ $sl + 1 }})" autocomplete="off">
                                            <input type="hidden" name="opening_balances[{{ $sl + 1 }}][is_subtype]"
                                                id="isSubtype_{{ $sl + 1 }}" value="{{ $sl + 1 }}"
                                                autocomplete="off">
                                        </td>
                                        <td>
                                            <button class="btn btn-danger" type="button" value="Delete"
                                                onclick="deleteRowDebtOpen(this)" autocomplete="off">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @php
                                        $debit += $item->debit ?? 0;
                                        $credit += $item->credit ?? 0;
                                    @endphp
                                @endforeach
                            @else
                                <tr>
                                    <td>
                                        <select name="opening_balances[1][coa_id]" id="cmbCode_1"
                                            class="select-basic-single" onchange="load_subtypeOpen(this.value, 1)">
                                            <option value="">{{ __('language.select_amount') }}</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="opening_balances[1][subcode_id]" id="subtype_1"
                                            class="select-basic-single">
                                            <option value="">{{ __('language.select_subtype') }}</option>
                                        </select>
                                        <input type="hidden" name="opening_balances[1][subtype_id]" id="stype_1">
                                    </td>
                                    <td>
                                        <input type="number" name="opening_balances[1][debit]" value=""
                                            class="form-control total_dprice text-end" id="txtDebit_1"
                                            onkeyup="calculationDebtOpen(1)" autocomplete="off">
                                    </td>
                                    <td>
                                        <input type="number" name="opening_balances[1][credit]" value=""
                                            class="form-control total_cprice text-end" id="txtCredit_1"
                                            onkeyup="calculationCreditOpen(1)" autocomplete="off">
                                        <input type="hidden" name="opening_balances[1][is_subtype]" id="isSubtype_1"
                                            value="1" autocomplete="off">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger" type="button" value="Delete"
                                            onclick="deleteRowDebtOpen(this)" autocomplete="off">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    @if (!$ended_year)
                                        <input type="button" id="add_more" class="btn btn-success" name="add_more"
                                            onclick="addaccountOpen('debitvoucher');" value="Add More"
                                            autocomplete="off">
                                    @endif
                                </td>
                                <td colspan="1" class="text-end">
                                    <label for="reason" class="col-form-label">{{ __('language.total') }}</label>
                                </td>
                                <td class="text-end">
                                    <input type="text" id="grandTotald" class="form-control text-end"
                                        name="grand_totald" value="{{ $debit }}" readonly="readonly"
                                        autocomplete="off">
                                </td>
                                <td class="text-end">
                                    <input type="text" id="grandTotalc" class="form-control text-end"
                                        name="grand_totalc" value="{{ $credit }}" readonly="readonly"
                                        autocomplete="off">
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="modal-footer">
                        @if (!$ended_year)
                            <button type="submit" class="btn btn-primary submit_button"
                                id="create_submit">{{ __('language.save') }}</button>
                        @endif
                        <input type="hidden" name="" id="headoption"
                            value="<option value=''> {{ __('language.Please select') }}</option>
@foreach ($accounts as $acc2)
<option value='{{ $acc2->id }}'>{{ $acc2->account_name }}</option>
@endforeach">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select-basic-single').select2();

            function load_subtypeAndSetSelected(id, sl, selectedSubcodeId) {
                // console.log("load_subtypeAndSetSelected triggered", id, sl, selectedSubcodeId);
                get_subtypeCode(id, sl);
                let url = "{{ route('subtype.by-code', ['id' => ':id']) }}".replace(':id', id);
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        // console.log("AJAX success for sl", sl, data);
                        if (data && data.length) {
                            $('#subtype_' + sl).html(data).removeAttr("disabled");
                            if (selectedSubcodeId) {
                                $('#subtype_' + sl).val(selectedSubcodeId).trigger('change');
                            }
                        } else {
                            $('#subtype_' + sl).attr("disabled", "disabled").find('option').remove();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX error:", textStatus, errorThrown);
                    }
                });
            }

            // Initialize the dropdowns with the pre-selected values
            setTimeout(function() {
                // console.log("Running subtype loader...");
                @foreach ($opening_balance as $index => $item)
                    console.log("Calling load_subtypeAndSetSelected with", {{ $item->acc_coa_id }},
                        {{ $index + 1 }}, {{ $item->acc_subcode_id ?? 'null' }});
                    load_subtypeAndSetSelected({{ $item->acc_coa_id }}, {{ $index + 1 }},
                        {{ $item->acc_subcode_id ?? 'null' }});
                @endforeach
            }, 300);

        });
    </script>
@endsection

@push('js')
    <script>
        window.appData = {
            subTypeUrl: "{{ route('subtype.by-id', ['id' => ':id']) }}",
            subTypeCodeUrl: "{{ route('subtype.by-code', ['id' => ':id']) }}"
        };
    </script>

    <script src="{{ module_asset('Accounts/js/account-ledger.js') }}" type="text/javascript"></script>
    <script src="{{ module_asset('Accounts/js/opb-row-more.js?v=2.3') }}" type="text/javascript"></script>
@endpush
