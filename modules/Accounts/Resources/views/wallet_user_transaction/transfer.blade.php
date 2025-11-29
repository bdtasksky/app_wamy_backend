@extends('backend.layouts.app')

@section('title', __('language.wallet_transfer'))
@push('css')
<link rel="stylesheet" href="{{ module_asset('Wallet/Resources/assets/css/transfer.css?v_' . date('h_i')) }}">
    <link rel="stylesheet" href="{{ module_asset('Wallet/Resources/assets/css/advance.css?v_' . date('h_i')) }}">

@endpush
@section('content')
{{-- @include('wallet::wallet_header') --}}

    <div class="card mb-4">
        @include('backend.layouts.common.validation')
        @include('backend.layouts.common.message')

        <div class="card-header py-2 py-xxl-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-17 fw-semi-bold mb-0">{{ __('language.wallet_transfer') }}</h6>
                </div>
                <div class="text-end">
                    <div class="actions">
                        <a href="{{ route('accounts.wallet.user_transaction.index') }}" class="btn btn-navy px-3 px-xl-4 py-2 fs-15 rounded-8">{{ __('language.transfer_list') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="leadForm" class="validateForm" action="{{ route('accounts.wallet.user_transaction.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="transaction_type" value="Transfer">
                    <input type="hidden" name="head_receive" id="head_receive" value="0">

                    <div class="d-flex flex-column flex-xl-row border rounded-10 mb-3">
                        <!-- Transfer From -->
                        <div class="w-50 p-5">

                            <input type="hidden" name="transfer_type" value="head_office_accounts" class="form-control" readonly>

                            {{-- <p class="fs-18 mb-4 fw-semi-bold">{{ __('language.transfer_from') }}</p> --}}
                     {{-- <div class="form-group mb-3 row align-items-center">
                            <label for="transfer_type" class="col-lg-3 col-form-label label_wallet_user_name">
                              {{ __('language.transfer_type') }} :
                            </label>
                            <div class="col-lg-9">
                              <select name="transfer_type" id="transfer_type" class="form-control select-basic-single rounded-3" required>
                                <option value="" selected disabled>{{ __('language.select_transfer_type') }}</option>
                                <option value="balance_transfer" selected>Balance Transfer</option>
                                @if($headuser->is_headuser=='1')
                                <option value="head_office_accounts">Head Office Accounts</option>
                                @endif
                              </select>
                            </div>
                          </div> --}}

                            {{-- <div class="form-group mb-3 row align-items-center" id="from_wallet_user_name">
                                <label for="from_wallet_user_name"
                                    class="col-lg-4 col-form-label label_wallet_user_name"> {{ __('language.name') }} :
                                </label>
                                <div class="col-lg-8">
                                    <select name="from_wallet_users_id" id="from_wallet_users_id"
                                        class="form-control select-basic-single rounded-3">
                                        <option value="{{ $login_user->id }}" selected>
                                            {{ $login_user->wallet_user_name }}
                                        </option>
                                    </select>
                                </div>
                            </div> 

                            <div class="form-group mb-3 row align-items-center" id="employee_input">
                                <label for="employee_input"
                                    class="col-lg-4 col-form-label label_wallet_user_name"> {{ __('language.name') }} :
                                </label>
                                <div class="col-lg-8">
                                    <select name="employee_id" id="employee_id" class="form-control select-basic-single rounded-3">  
                                    </select>
                                </div>
                            </div>--}}

                            <div class="form-group mb-3 row" id="from_transaction_method">
                                <label class="col-lg-4 col-form-label label_is_active">{{ __('language.transaction_method') }}: </label>
                                <div class="col-lg-8 d-flex align-items-center gap-4 text-start">
                                    <div class="form-check">
                                        <input type="radio" id="from_method_cash"
                                            class="form-check-input check-warning" name="from_transaction_method"
                                            value="cash" checked>
                                        <label for="from_method_cash"
                                            class="form-check-label">{{ __('language.cash') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" id="from_method_bank"
                                            class="form-check-input check-warning" name="from_transaction_method"
                                            value="bank" >
                                        <label for="from_method_bank"
                                            class="form-check-label">{{ __('language.bank') }}</label>
                                    </div>
                                </div>
                            </div>

                            

                            
                            <div class="form-group mb-3 row align-items-center" id="acc_coa_id">
                                <label for="from_account_head"
                                    class="col-lg-4 col-form-label label_wallet_user_name">{{ __('language.account_head') }}
                                    : </label>
                                <div class="col-lg-8">
                                   <select name="acc_coas_id" id="acc_coas_id" class="form-control select-basic-single rounded-3">
                                      <option value="" selected data-type="0">{{ __('language.select_one') }}</option>
                                      @foreach ($allheads as $allhead)
                                          <option value="{{ $allhead->id }}" data-type="{{ $allhead->is_cash_nature }}">{{ $allhead->account_name }}</option>
                                      @endforeach
                                  </select>
                                </div>
                            </div>
                            <div class="form-group mb-3 row align-items-center">
                                <label for="from_account_head"
                                    class="col-lg-4 col-form-label label_wallet_user_name">Avaiable Balance
                                    : </label>
                                  <div class="col-lg-8"><span class="text-success" id="cl_balance">0.00</span> SAR</div>   
                            </div>   
                        </div>
                        
                        <!-- Transfer To -->
                        <div class="w-50 p-5 border-xl-left">
                            <p class="fs-18 mb-4 fw-semi-bold">{{ __('language.transfer_to') }}</p>
                            <div class="form-group mb-3 row align-items-center">
                                <label for="recipient_name" class="col-lg-3 col-form-label label_wallet_user_name">
                                    {{ __('language.recipient_name') }} : </label>
                                <div class="col-lg-7">
                                    <select name="to_wallet_users_id" id="to_wallet_users_id"
                                        class="form-control select-basic-single rounded-3" required>
                                        <option value="" selected disabled>{{ __('language.select_user') }}
                                        </option>
                                        @foreach ($users as $key => $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('to_wallet_users_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->wallet_user_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('to_wallet_users_id'))
                                        <div class="error text-danger text-start">
                                            {{ $errors->first('to_wallet_users_id') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label
                                    class="col-lg-3 col-form-label label_is_active">{{ __('language.transaction_method') }}
                                    : </label>
                                <div class="col-lg-9 d-flex align-items-center gap-4 text-start">
                                    <div class="form-check">
                                        <input type="radio" id="to_method_cash" class="form-check-input check-warning"
                                            name="to_transaction_method" value="cash" checked>
                                        <label for="to_method_cash"
                                            class="form-check-label">{{ __('language.cash') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" id="to_method_bank" class="form-check-input check-warning"
                                            name="to_transaction_method" value="bank">
                                        <label for="to_method_bank"
                                            class="form-check-label">{{ __('language.bank') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 row align-items-center">
                                <label for="posting_date"
                                    class="col-lg-3 col-form-label label_wallet_user_name">{{ __('language.date') }} :
                                </label>
                                <div class="col-lg-7">
                                    <input type="date" name="posting_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                            </div>
                            <div class="form-group mb-3 row align-items-center">
                                <label for="amount"
                                    class="col-lg-3 col-form-label label_wallet_user_name">{{ __('language.amount') }}
                                    : </label>
                                <div class="col-lg-7">
                                    <input type="number" min="1.00" step="any" name="amount"
                                        class="form-control rounded-3" placeholder="{{ __('language.amount') }}"
                                        value="1.00" required>
                                </div>
                            </div>
                            <div class="form-group mb-3 row align-items-center">
                                <label for="narration"
                                    class="col-lg-3 col-form-label label_wallet_user_name">{{ __('language.description') }}
                                    : </label>
                                <div class="col-lg-7">
                                    <textarea name="narration" class="form-control rounded-3" cols="10" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="d-flex justify-content-end">
                  <button class="btn btn-success px-3 px-xl-4 py-2 fs-15 rounded-8 submit_button acBtn" id="create_submit">  
                    <span class="generatesubmit">
                        Transfer
                    </span>
                    <span class="d-none generateloading">
                        <i class="fa fa-spinner fa-spin"></i>
                        Please Wait...
                    </span></button>
                </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('js')
<script>
  $(document).ready(function () {
    function handleTransferType() {
      var transferType = $('#transfer_type').val();

      if (transferType === 'head_office_accounts') {
        // Show account head in Transfer From
        $('input[name="acc_coas_id"]').val('').trigger('change').prop('disabled', false);
        $('#acc_coa_id').show();
        $('#head_receive').val(1);

        // Hide recipient select and make it readonly with "System"
        $('#from_wallet_user_name').hide();
        $('#from_transaction_method').hide();
        $('#employee_input').hide();

        // Set the dropdown to the current user's wallet_user_id and disable it
        $('#from_wallet_users_id').val('{{ $login_user->id }}').trigger('change');
        $('#to_wallet_users_id').prop('disabled', true);

      }
    }

    // Initial state
    handleTransferType();

    // On change
    $('#transfer_type').on('change', handleTransferType);
  

    const $select = $('#acc_coas_id');

    const wasSelect2 = !!($.fn.select2 && ($select.hasClass('select2-hidden-accessible') || $select.data('select2')));
    $select.data('wasSelect2', wasSelect2);

    if (!$select.data('origOptions')) {
        const orig = [];
        $select.find('option').each(function () {
            const $o = $(this);
            orig.push({
                value: $o.attr('value'),
                text: $o.text(),
                dataType: $o.attr('data-type'),
                selected: $o.prop('selected') || false
            });
        });
        $select.data('origOptions', orig);
    }

    function rebuildOptions(wantedType) {
        const orig = $select.data('origOptions') || [];
        const hadSelect2 = $select.data('wasSelect2');
        const previousVal = $select.val();

        if (hadSelect2 && $.fn.select2) {
            try { $select.select2('destroy'); } catch (e) {}
        }

        $select.empty();
        orig.forEach(item => {
            if (item.value === '' || String(item.dataType) === String(wantedType)) {
                const opt = $('<option>')
                    .attr('value', item.value)
                    .attr('data-type', item.dataType)
                    .text(item.text);
                if (String(item.value) === String(previousVal)) opt.prop('selected', true);
                $select.append(opt);
            }
        });

        if ($select.find('option[value="' + previousVal + '"]').length === 0) {
            $select.val('');
        }

        if (hadSelect2 && $.fn.select2) {
            try { $select.select2({ width: '100%' }); } catch (e) {}
        }

        $select.trigger('change');
    }

    function filterAccountHeads() {
        const selectedMethod = $('input[name="from_transaction_method"]:checked').val();
        const wantedType = selectedMethod === 'cash' ? '1' : '0';
        rebuildOptions(wantedType);
    }

    filterAccountHeads();

    $('input[name="from_transaction_method"]').on('change', function () {
        filterAccountHeads();
    });

    // 🔹 AJAX call on option select
    $select.on('change', function () {
        const selectedVal = $(this).val();
        if (!selectedVal) return;

        $.ajax({
            url: "{{ route('get_acc_coa_balance') }}", // 🔸 replace with your route URL
            method: 'POST',
            data: {
                id: selectedVal,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                $('#cl_balance').text(response.result);
            },
            error: function (xhr) {
                console.error('AJAX error:', xhr.responseText);
            }
        });
    });

});
</script>
@endpush

