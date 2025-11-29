@extends('backend.layouts.app')
@section('title', __('language.view_installment_details'))
@push('css')
@endpush
@section('content')

    {{-- @include('humanresource::installment_header') --}}
    @include('backend.layouts.common.validation')
    @include('backend.layouts.common.message')

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-17 fw-semi-bold mb-0">{{ __('language.view_installment_details') }}</h6>
                </div>
                <div class="text-end">
                    <div class="actions">

                        @can('create_installment')
                            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#create-installment"><i
                                    class="fa fa-plus-circle"></i>&nbsp;{{ __('language.add_installment') }}</a>
                        @endcan
                        @php
                            $hasPaidDetail = $installment->accInstallmentRecords()->whereIn('status', ['Processing', 'Paid', 'Adjusted'])->exists();
                        @endphp

                        @include('accounts::installment.modal.create')
                    </div>
                </div>
            </div>
        </div>


        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <table class="table-responsive table-bordered table-striped table">
                        <tr>
                            <th width="20%">{{ __('language.coa_head') }}</th>
                            <td>{{ $installment->acc_coa?->account_name }}</td>
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
                                <span class="badge bg-@if($installment->is_paid == 'Paid'){{'success'}}@elseif($installment->is_paid == 'Processing'){{'primary'}}"@elseif($installment->is_paid == 'Unpaid'){{'danger'}}@endif">{{ $installment->is_paid }}</span>                                
                            </td>
                        </tr>

                    </table>
                </div>
            </div>
            @if(count($installment->accInstallmentRecords) > 0)
                <hr>
                <div class="table_customize">
                    <h3 class="text-center">{{ __('language.installment_details') }}</h3>

                    <table class="table table-sm table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th>{{ __('language.sl') }}</th>
                                <th>{{ __('Adjustment') }}</th>
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
                                    <td>{{ $i+1 }}</td>
                                    <td>
                                        <input type="checkbox" class="installment-check" 
                                            value="{{ $installment->id }}" 
                                            data-number="{{ $installment->number_of_installment }}"
                                            data-amount="{{ $installment->installment_amount }}"
                                            data-date="{{ $installment->installment_date }}"
                                            @if($installment->status != 'Unpaid') disabled @endif>
                                        
                                    </td>
                                    <td>{{ $installment->number_of_installment }}</td>
                                    <td>{{ $installment->installment_amount }}</td>
                                    <td>{{ $installment->installment_date }}</td>
                                    <td>{{ $installment->adjustment_amount }}</td>
                                    <td>{{ $installment->adjustment_date }}</td>
                                    
                                    <td>
                                        @if($installment->status == 'Paid')
                                            <span class="badge bg-success">{{ $installment->status }}</span>
                                        @elseif($installment->status == 'Hold')
                                            <span class="badge bg-warning">{{ $installment->status }}</span>
                                        @elseif($installment->status == 'Unpaid')
                                            <span class="badge bg-danger">{{ $installment->status }}</span>
                                            <button type="button" class="btn btn-warning btn-sm hold-installment-btn" 
                                                    data-id="{{ $installment->id }}">
                                                Hold Installment
                                            </button>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-success mt-3" id="submit-adjustment-btn">
                        {{ __('Submit Adjustment') }}
                    </button>
                </div>
            @endif
        </div>
    </div>


<!-- Adjustment Modal -->
<div class="modal fade" id="adjustmentModal" tabindex="-1" aria-labelledby="adjustmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="adjustmentModalLabel">{{ __('Submit Adjustment') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="adjustmentForm">
          @csrf

          <div class="table-responsive">
            <table class="table table-bordered text-center">
              <thead>
                <tr>
                  <th>{{ __('language.number_of_installment') }}</th>
                  <th>{{ __('language.installment_amount') }}</th>
                  <th>{{ __('language.installment_date') }}</th>
                </tr>
              </thead>
              <tbody id="selectedInstallments">
                <!-- filled dynamically -->
              </tbody>
            </table>
          </div>

          <div class="mb-3">
            <label for="payment_method" class="form-label">{{ __('Payment Method') }}</label>

            <select name="acc_coas_id" id="acc_coa_id" class="form-control" required>
                <option value="" selected disabled>{{ __('language.select_one') }}</option>
                @foreach ($allheads as $allhead)
                    <option value="{{ $allhead->id }}">{{ $allhead->account_name }}</option>
                @endforeach
            </select>
            
            
          </div>

        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" id="confirmAdjustment" class="btn btn-primary py-2 actionBtn text-end">
                                <span class="generatesubmit">
                                    {{ __('Confirm & Submit') }}
                                </span>
                                <span class="d-none generateloading">
                                    <i class="fa fa-spinner fa-spin"></i>
                                    Please Wait...
                                </span>
                            </button>
        {{-- <button type="button" class="btn btn-primary" id="confirmAdjustment">{{ __('Confirm & Submit') }}</button> --}}
      </div>
    </div>
  </div>
</div>


@endsection
@push('js')
<script src="{{ module_asset('HumanResource/Resources/assets/js/installment.js') }}"></script>
<script>
    $(document).ready(function() {
    // Open modal with selected installments
    $("#submit-adjustment-btn").on("click", function() {
        let selected = $(".installment-check:checked");
        if (selected.length === 0) {
            alert("Please select at least one unpaid installment.");
            return;
        }

        let rows = "";
        selected.each(function() {
            rows += `
                <tr>
                    <td>${$(this).data("number")}</td>
                    <td>${$(this).data("amount")}</td>
                    <td>${$(this).data("date")}</td>
                </tr>
            `;
        });

        $("#selectedInstallments").html(rows);
        $("#adjustmentModal").modal("show");

        $(".generatesubmit").removeClass("d-none");
        $(".generateloading").addClass("d-none");
        $('.actionBtn').prop('disabled', false);
    });

    // Confirm submit
    $("#confirmAdjustment").on("click", function() {
        let ids = [];
        $(".installment-check:checked").each(function() {
            ids.push($(this).val());
        });

        let acc_coas_id = $("#acc_coa_id").val();

        if (ids.length === 0) {
            Swal.fire("Warning!", "Please select at least one installment.", "warning");
            return;
        }

        $(".generatesubmit").addClass("d-none");
        $(".generateloading").removeClass("d-none");
        $('.actionBtn').prop('disabled', true);

        Swal.fire({
            title: "Are you sure?",
            text: "You are about to submit the adjustment for selected installments.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Submit!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('installments.adjustment-submit') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        installment_ids: ids,
                        acc_coas_id: acc_coas_id
                    },
                    success: function(response) {

                        $(".adjustmentModal").modal("hide");
                        $(".generatesubmit").removeClass("d-none");
                        $(".generateloading").addClass("d-none");
                        $('.actionBtn').prop('disabled', false);

                        Swal.fire(
                            "Success!",
                            "Adjustment submitted successfully.",
                            "success"
                        ).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {

                        $(".generatesubmit").removeClass("d-none");
                        $(".generateloading").addClass("d-none");
                        $('.actionBtn').prop('disabled', false);

                        Swal.fire("Error!", "Something went wrong. Please try again.", "error");

                    }
                });
            }
        });
    });

});


$(document).on("click", ".hold-installment-btn", function() {
    let installmentId = $(this).data("id");

    Swal.fire({
        title: "Are you sure?",
        text: "This installment will be marked as On Hold.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Hold it",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('installments.hold') }}", // <-- define this route
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    installment_id: installmentId
                },
                success: function(response) {
                    Swal.fire("Held!", "The installment installment has been put on hold.", "success")
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire("Error!", "Something went wrong. Please try again.", "error");
                }
            });
        }
    });
});


</script>
@endpush
