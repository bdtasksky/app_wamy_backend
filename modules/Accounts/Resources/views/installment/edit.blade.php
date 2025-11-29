@extends('backend.layouts.app')
@section('title', __('language.installment'))
@push('css')
@endpush
@section('content')
    @include('backend.layouts.common.validation')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-17 fw-semi-bold mb-0">{{ __('language.edit_installment') }}</h6>
                </div>
                <div class="text-end">
                    <div class="actions">
                        @can('create_installment')
                            <a href="{{ route('installments.index') }}" class="btn btn-success"><i
                                    class="fa fa-list"></i>&nbsp;{{ __('language.installment_list') }}</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        <form class="validateEditForm" action="{{ route('installments.update', $installment->id) }}" method="POST">
            @method('PATCH')
            @csrf
            <input type="hidden" id="installment_head_value" value="{{ $installment->acc_coa_id_type }}">
            <div class="card-body">
                <div class="form-group mb-2 mx-0 row">
                    <label for="type"
                        class="col-sm-3 col-form-label ps-0">{{ __('language.installment_type') }}<span
                            class="text-danger">*</span></label>
                    <div class="col-lg-9 text-start">
                        <select name="installType" id="type" required
                            class="form-control select-basic-single">
                            <option value="" selected disabled>{{ __('language.select_type') }}</option>
                            <option value="RECEIVABLE" {{ ($installment->installType=='RECEIVABLE'?'Selected':'') }}>RECEIVABLE</option>
                            <option value="PAYABLE" {{ ($installment->installType=='PAYABLE'?'Selected':'') }}>PAYABLE</option>
                        </select>
                    </div>
                </div>
                <div class="form-group mb-2 mx-0 row">
                    <label for="installment_head"
                        class="col-sm-3 col-form-label ps-0">{{ __('language.installment_head') }}<span
                            class="text-danger">*</span></label>
                    <div class="col-lg-9 text-start">
                        <select name="acc_coa_id_type" id="installment_head" required class="form-control select-basic-single">
                            <option value="" selected disabled>{{ __('language.select_one') }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group mb-2 mx-0 row">
                    <label for="installment_type" class="col-lg-3 col-form-label ps-0">{{ __('language.installment_name') }}<span
                            class="text-danger">*</span></label>
                    <div class="col-lg-9 text-start">
                        <input type="text" class="form-control" name="installment_type" required id="installment_type"
                            value="{{ $installment->installment_type }}" placeholder="{{ __('language.installment_name') }}">
                    </div>
                </div>
                <div class="form-group mb-2 mx-0 row">
                    <label for="remarks" class="col-lg-3 col-form-label ps-0">{{ __('language.remarks') }}</label>
                    <div class="col-lg-9 text-start">
                        <textarea class="form-control" name="remarks" id="remarks" cols="30" rows="4">{{ $installment->remarks }}</textarea>
                    </div>
                </div>

                <div class="form-group mb-2 mx-0 row">
                    <label for="amount" class="col-lg-3 col-form-label ps-0">{{ __('language.amount') }}<span
                            class="text-danger">*</span></label>
                    <div class="col-lg-9 text-start">
                        <input type="number" class="form-control" required id="installment-amount" name="amount"
                            placeholder="{{ __('language.amount') }}" value="{{ $installment->amount }}">
                    </div>
                </div>

                <div class="form-group mb-2 mx-0 row">
                    <label for="effective_date"
                        class="col-lg-3 col-form-label ps-0">{{ __('language.effective_date') }}<span
                            class="text-danger">*</span></label>
                    <div class="col-lg-9 text-start">
                        <input type="text" class="form-control date_picker" name="effective_date" required
                            id="effective_date" value="{{ $installment->effective_date }}"
                            placeholder="{{ __('language.effective_date') }}">
                    </div>
                </div>

                <div class="form-group mb-2 mx-0 row">
                    <label for="installment-period"
                        class="col-lg-3 col-form-label ps-0">{{ __('language.installment') }}<span
                            class="text-danger">*</span></label>
                    <div class="col-lg-9 text-start">
                        <input type="number" class="form-control" required id="installment-period" name="installment"
                            placeholder="{{ __('language.installment') }}" value="{{ $installment->installment }}">
                    </div>
                </div>

                <div class="form-group mb-2 mx-0 row">
                    <label for="repayment-amount"
                        class="col-lg-3 col-form-label ps-0">{{ __('language.installment_amount') }}<span
                            class="text-danger">*</span></label>
                    <div class="col-lg-9 text-start">
                        <input type="number" class="form-control" required id="installment-amount"
                            name="installment_amount" placeholder="{{ __('language.installment_amount') }}"
                            value="{{ $installment->installment_amount }}">
                    </div>
                </div>

                <div class="form-group mb-2 mx-0 row">
                    <label for="installment_cleared"
                        class="col-lg-3 col-form-label ps-0">{{ __('language.paid_amount') }}</label>
                    <div class="col-lg-9 text-start">
                        <input type="number" class="form-control" required id="paid_amount" name="paid_amount"
                            placeholder="{{ __('language.paid_amount') }}" value="{{ $installment->paid_amount }}">
                    </div>
                </div>

                <div class="form-group mb-2 mx-0 row">
                    <label for="installment_cleared"
                        class="col-lg-3 col-form-label ps-0">{{ __('language.installment_cleared') }}</label>
                    <div class="col-lg-9 text-start">
                        <input type="number" class="form-control" required id="installment_cleared"
                            name="installment_cleared" placeholder="{{ __('language.installment_cleared') }}"
                            value="{{ $installment->installment_cleared }}">
                    </div>
                </div>

                @radio(['input_name' => 'is_active', 'data_set' => [1 => 'Active', 0 => 'Inactive'], 'value' => $installment->is_active])

                <div id="installment-details" class="table_customize">
                    @if (count($installment->accInstallmentRecords) > 0)
                        <hr>
                        <h3 class="text-center">{{ __('language.installment_details') }}</h3>

                        <table class="table table-sm table-bordered table-striped text-center">
                            <thead>
                                <tr>
                                    <th>{{ __('language.sl') }}</th>
                                    <th>{{ __('language.number_of_installment') }}</th>
                                    <th>{{ __('language.installment_amount') }}</th>
                                    <th>{{ __('language.installment_date') }}</th>
                                    <th>{{ __('language.adjustment_amount') }}</th>
                                    <th>{{ __('language.adjustment_date') }}</th>
                                    <th>{{ __('language.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($installment->accInstallmentRecords as $i => $installment)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $installment->number_of_installment }}</td>
                                        <td>{{ $installment->installment_amount }}</td>
                                        <td>{{ $installment->installment_date }}</td>
                                        <td>{{ $installment->adjustment_amount }}</td>
                                        <td>{{ $installment->adjustment_date }}</td>
                                        <td>
                                            <span class="badge bg-@if($installment->status == 'Paid'){{'success'}}@elseif($installment->status == 'Processing'){{'primary'}}@elseif($installment->status == 'Unpaid') {{'danger'}} @endif">{{ $installment->status }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="card-footer text-end">
                <a href="{{ route('installments.index') }}" class="btn btn-danger">{{ __('language.close') }}</a>
                <button type="submit" class="btn btn-success" id="update_submit">{{ __('language.update') }}</button>
            </div>
    </div>
    </form>

@endsection
@push('js')
    <script src="{{ module_asset('Accounts/js/installment.js') }}"></script>
    <script>
    $(document).ready(function() {
        // --- Initial Setup & Debugging ---
        console.log("--- Edit Form Script Initialized ---");

        // Initialize Select2 first. This is important.
        $('.select-basic-single').select2();
        console.log("Select2 Initialized.");

        // --- Data from Controller ---
        var assets = {!! json_encode($assets) !!};
        var liabilities = {!! json_encode($liabilities) !!};
        console.log("Assets data from controller:", assets);
        console.log("Liabilities data from controller:", liabilities);
        var savedInstallmentHeadId=$('#installment_head_value').val();
        // --- Get the currently saved ID for the installment head ---
        // Ensure this blade variable contains the correct ID from your database.
        // var savedInstallmentHeadId = '{{ $installment->acc_coa_id_type }}';
        console.log("The ID we need to select is:", savedInstallmentHeadId, "(Type:", typeof savedInstallmentHeadId, ")");
        
        // --- Get references to the dropdowns ---
        var typeDropdown = $('#type');
        var installmentHeadDropdown = $('#installment_head');

        // --- Reusable function to populate the second dropdown ---
        function populateInstallmentHead(selectedType) {
            console.log("Function 'populateInstallmentHead' called with type:", selectedType);
            installmentHeadDropdown.empty().append('<option value="" selected disabled>{{ __("language.select_one") }}</option>');

            var optionsData = [];
            if (selectedType === 'RECEIVABLE') {
                optionsData = assets;
            } else if (selectedType === 'PAYABLE') {
                optionsData = liabilities;
            }

            console.log("Populating 'Installment Head' with this data:", optionsData);

            if (optionsData.length > 0) {
                $.each(optionsData, function(index, item) {
                    // IMPORTANT: Ensure 'item.id' and 'item.head_name' match your actual database column names.
                    installmentHeadDropdown.append($('<option>', {
                        value: item.id,
                        text: item.account_name
                    }));
                });
                console.log("'Installment Head' dropdown has been populated in the HTML.");
            } else {
                console.warn("Warning: No options data found for the selected type.");
            }
        }

        // --- 1. Handle the "change" event ---
        typeDropdown.on('change', function() {
            var selectedType = $(this).val();
            populateInstallmentHead(selectedType);
            // We do not set the value here, as the user is making a new choice.
        });

        // --- 2. Handle the initial state on page load for editing ---
        var initialType = typeDropdown.val();
        console.log("Initial 'Installment Type' on page load is:", initialType);

        if (initialType) {
            // A) Populate the second dropdown
            populateInstallmentHead(initialType);

            // B) Attempt to set the saved value
            console.log("Now attempting to set the selected value to:", savedInstallmentHeadId);
            installmentHeadDropdown.val(savedInstallmentHeadId);
            
            // C) VERIFICATION STEP: Check if the value was actually set
            var currentValue = installmentHeadDropdown.val();
            console.log("Value AFTER setting it is now:", currentValue);

            if (currentValue === savedInstallmentHeadId) {
                console.log("%cSUCCESS: The value was set correctly on the select element.", "color: green; font-weight: bold;");
            } else {
                console.error("FAILURE: The value could NOT be set. This usually means no <option> with value='" + savedInstallmentHeadId + "' exists in the dropdown.");
            }
            
            // D) Trigger the change for Select2 to update its display
            console.log("Triggering 'change' for Select2 to update the UI.");
            installmentHeadDropdown.trigger('change');
        } else {
            console.warn("Warning: No initial installment type was selected on page load.");
        }
    });
    </script>
@endpush
