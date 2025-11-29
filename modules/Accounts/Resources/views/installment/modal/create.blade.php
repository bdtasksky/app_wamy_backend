<!-- Modal -->
<div class="modal fade" id="create-installment" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">
                    {{ __('language.add_new_installment') }}
                </h5>
            </div>
            <form class="validateForm" action="{{ route('installments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group mb-2 mx-0 row">
                            <label for="type"
                                class="col-sm-3 col-form-label ps-0">{{ __('language.installment_type') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9 text-start">
                                <select name="install_type" id="type" required
                                    class="form-control select-basic-single">
                                    <option value="" selected disabled>{{ __('language.select_type') }}</option>
                                    <option value="RECEIVABLE">RECEIVABLE</option>
                                    <option value="PAYABLE">PAYABLE</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-2 mx-0 row">
                            <label for="installment_head"
                                class="col-sm-3 col-form-label ps-0">{{ __('language.installment_head') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9 text-start">
                                <select name="installment_head" id="installment_head" required
                                    class="form-control select-basic-single">
                                    <option value="" selected disabled>{{ __('language.select_one') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-2 mx-0 row">
                            <label for="installment_type"
                                class="col-lg-3 col-form-label ps-0">{{ __('language.installment_name') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9 text-start">
                                <input type="text" name="installment_type" required id="installment_type" value=""
                                    placeholder="{{ __('language.installment_name') }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group mb-2 mx-0 row">
                            <label for="remarks"
                                class="col-lg-3 col-form-label ps-0">{{ __('language.remarks') }}</label>
                            <div class="col-lg-9 text-start">
                                <textarea class="form-control" name="remarks" id="remarks" cols="30" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="form-group mb-2 mx-0 row">
                            <label for="amount" class="col-lg-3 col-form-label ps-0">{{ __('language.amount') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9 text-start">
                                <input type="number" required id="installment-month-amount" name="amount"
                                    placeholder="{{ __('language.amount') }}" class="form-control">
                            </div>
                        </div>

                        <div class="form-group mb-2 mx-0 row">
                            <label for="installment-period"
                                class="col-lg-3 col-form-label ps-0">{{ __('language.installment') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9 text-start">
                                <input type="number" required id="installment-period"  name="installment"
                                    placeholder="{{ __('language.installment') }}" class="form-control">
                            </div>
                        </div>

                        <div class="form-group mb-2 mx-0 row">
                            <label for="installment-amount"
                                class="col-lg-3 col-form-label ps-0">{{ __('language.installment_amount') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9 text-start">
                                <input type="number" readonly required id="installment-amount" name="installment_amount"
                                    placeholder="{{ __('language.installment_amount') }}" class="form-control">
                            </div>
                        </div>

                        <div class="form-group mb-2 mx-0 row">
                            <label for="effective_date"
                                class="col-lg-3 col-form-label ps-0">{{ __('language.effective_date') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9 text-start">
                                <input type="text" name="effective_date" required id="effective_date" value=""
                                    placeholder="{{ __('language.effective_date') }}" class="form-control installment_date_picker">
                            </div>
                        </div>

                        @radio(['input_name' => 'is_active', 'data_set' => [1 => 'Active', 0 => 'Inactive'], 'value' => 1, 'required' => true])


                        <div id="installment-details" class="">

                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger"
                        data-bs-dismiss="modal">{{ __('language.close') }}</button>
                    <button class="btn btn-success submit_button" id="create_submit">{{ __('language.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
