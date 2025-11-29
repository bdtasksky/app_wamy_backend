<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fs-17 fw-semi-bold mb-0">Installment Disbursment</h6>
            </div>
        </div>
    </div>


    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <form class="validateForm" action="{{ route('installments.approve_installment', $installment->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                <table class="table-responsive table-bordered table-striped table">
                    <tr>
                        <th width="20%">{{ __('language.payment_head') }} <span class="text-danger">*</span></th>
                        <td><select name="acc_coas_id" id="acc_coa_id" class="form-control" required>
                            <option value="" selected disabled>{{ __('language.select_one') }}</option>
                            @foreach ($allheads as $allhead)
                                <option value="{{ $allhead->id }}">{{ $allhead->account_name }}</option>
                            @endforeach
                        </select></td>
                    </tr>
                    <tr>
                        <th width="20%">{{ __('language.installment_type') }}</th>
                        <td>{{ $installment->installment_type }}</td>
                    </tr>
                    <tr>
                        <th width="20%">{{ __('language.installment_amount') }}</th>
                        <td>{{ $installment->amount }}</td>
                    </tr>
                    <tr>
                        <th width="20%">{{ __('language.installment') }}</th>
                        <td>{{ $installment->installment }}</td>
                    </tr>
                    <tr>
                        <th width="20%">{{ __('language.installment_amount') }}</th>
                        <td>{{ $installment->installment_amount }}</td>
                    </tr>
                    <tr>
                        <th width="20%">{{ __('language.installment_cleared') }}</th>
                        <td>{{ $installment->installment_cleared }}</td>
                    </tr>
                    <tr>
                        <th width="20%">{{ __('language.paid_amount') }}</th>
                        <td>{{ $installment->paid_amount }}</td>
                    </tr>
                    <tr>
                        <th width="20%">{{ __('language.remaining_amount') }}</th>
                        <td>{{ $installment->remaining_balance }}</td>
                    </tr>
                    <tr>
                        <th width="20%">{{ __('language.effective_date') }}</th>
                        <td>{{ $installment->effective_date }}</td>
                    </tr>
                    <tr>
                        <th width="20%">{{ __('language.status') }}</th>
                        <td>
                            @if ($installment->is_active == 1)
                                <span class="badge bg-success">{{ __('language.active') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('language.inactive') }}</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th width="20%">{{ __('language.is_paid') }}</th>
                        <td>
                            <span class="badge bg-@if ($installment->is_paid == 'Paid') {{ 'success' }}@elseif($installment->is_paid == 'Processing'){{ 'primary' }}"@elseif($installment->is_paid == 'Unpaid'){{ 'danger' }}@endif">{{ $installment->is_paid }}</span>
                            </td>
                        </tr>
                    </table>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">{{ __('language.close') }}</button>
                        <button class="btn btn-success submit_button" id="create_submit">{{ __('language.approve') }}</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>