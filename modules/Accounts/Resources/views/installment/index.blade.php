@extends('backend.layouts.app')
@section('title', __('language.installment_list'))
@push('css')
@endpush
@section('content')

    @include('backend.layouts.common.validation')
    @include('backend.layouts.common.message')

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-17 fw-semi-bold mb-0">{{ __('language.installment_list') }}</h6>
                </div>
                <div class="text-end">
                    <div class="actions">
                         <a href="{{ route('installment-report') }}" class="btn btn-primary btn-md pull-right">
                                <i class="fa fa-file" aria-hidden="true"></i> {{ __('language.monthly_installment_report') }}
                            </a>
                        <button type="button" class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne"> <i class="fas fa-filter"></i> {{__('language.filter')}}</button>
                        @can('create_installment')
                        <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#create-installment"><i
                                class="fa fa-plus-circle"></i>&nbsp;{{ __('language.add_installment') }}</a>
                        @endcan
                        @include('accounts::installment.modal.create')
                    </div>
                </div>
            </div>
        </div>


        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item">
                            <div id="flush-collapseOne" class="accordion-collapse collapse bg-white mb-4" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">

                                <div class="row">
                                    {{-- <div class="col-md-2 mb-4">
                                        <select id="employee_name" name="employee_name" class="select-basic-single">
                                            <option value="">{{__('language.select_employee')}}</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{$employee->id}}">{{ucwords($employee->name) . ' - ' . $employee->name_ar}}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                    <div class="col-md-2 mb-4 align-self-end">
                                        <button type="button" id="installment-filter" class="btn btn-success">{{ __('language.find') }}</button>
                                        <button type="button" id="installment-search-reset" class="btn btn-danger">{{ __('language.reset') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table_customize">
                {{ $dataTable->table() }}
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="approveDetailsViewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body" id="viewData">
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    <script src="{{ module_asset('Accounts/js/installment.js') }}"></script>

    <script type="module">
        //Custom Data table Search
        $(document).ready(function () {
            $('#employee-installment-table').on('click', '#installmentApprove', function(e) {
                e.preventDefault();
                $('#ajaxForm').removeClass('was-validated');
                let submit_url = $(this).attr('data-approve-url');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Approve it!',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: submit_url,
                            data: {"_token": "{{ csrf_token() }}"},
                            dataType: 'json',
                            success: function(response) {

                                if (response.status == true) {
                                    Swal.fire('Approve!', response.message, 'success');
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                                $('#employee-installment-table').DataTable().ajax.reload(null, false);
                            },
                            error: function() {
                                Swal.fire('Error!', 'Something went wrong. Try again.', 'error');
                            }
                        });
                    }
                });
            });
        // Store the assets and liabilities data passed from the controller
        // It's important to properly encode the PHP collections to JSON
        var assets = {!! json_encode($assets) !!};
        var liabilities = {!! json_encode($liabilities) !!};

        // Listen for changes on the 'type' dropdown
        $('#type').on('change', function() {
            // Get the selected value
            var selectedType = $(this).val();
            
            // Get a reference to the 'installment_head' dropdown
            var installmentHead = $('#installment_head');
            
            // Clear the current options in the installment_head dropdown
            installmentHead.empty();
            
            // Add the default "Select One" option
            installmentHead.append('<option value="" selected disabled>{{ __("language.select_one") }}</option>');

            if (selectedType === 'RECEIVABLE') {
                // If RECEIVABLE is selected, populate with assets
                $.each(assets, function(index, asset) {
                    installmentHead.append($('<option>', {
                        value: asset.id, // Or any other unique identifier from your table
                        text: asset.account_name // Or the name column from your table
                    }));
                });
            } else if (selectedType === 'PAYABLE') {
                // If PAYABLE is selected, populate with liabilities
                $.each(liabilities, function(index, liability) {
                    installmentHead.append($('<option>', {
                        value: liability.id, // Or any other unique identifier
                        text: liability.account_name // Or the name column
                    }));
                });
            }
            
            // If you are using a library like Select2, you may need to refresh it
            // installmentHead.trigger('change'); 
        });
    });
    </script>
@endpush
