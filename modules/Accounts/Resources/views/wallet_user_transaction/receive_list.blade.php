@extends('backend.layouts.app')

@section('title', __('language.wallet_receive'))
@push('css')
<link rel="stylesheet" href="{{ module_asset('Wallet/Resources/assets/css/transfer.css?v_' . date('h_i')) }}">
@endpush
@section('content')
{{-- @include('wallet::wallet_header') --}}

    <div class="card mb-4 ">
        @include('backend.layouts.common.validation')
        @include('backend.layouts.common.message')

        <div class="card-header py-2 py-xxl-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-17 fw-semi-bold mb-0">{{ __('language.wallet_receive') }}</h6>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{ $dataTable->table() }}
        </div> 
    </div>
    <!-- Modal -->
    <div class="modal fade" id="detailsViewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body" id="viewData">
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
    function receiveMoney(id) {
        Swal.fire({
            title: "Are you sure you want to receive money?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, receive money',
            cancelButtonText: 'Cancel',
            didOpen: () => {
                const confirmButton = Swal.getConfirmButton();
                if (confirmButton) {
                    confirmButton.focus();
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $('[onclick="receiveMoney(' + id + ')"]').data('url');
                var csrf = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        id: id,
                        _token: csrf,
                    },
                    success: function (data) {
                        if (data.success) {
                            toastr.success(data.message || 'Approved successfully');
                            location.reload();
                        } else {
                            toastr.error(data.message || 'Approval failed');
                        }
                    },
                    error: function () {
                        toastr.error('Error', 'Error');
                    }
                });
            }
        });
    }
    function detailsView(id) {
        var url = $('#detailsView-' + id).data('url');
        var csrf = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                id: id,
                _token: csrf,
            },
            success: function (data) {
                if (data) {
                    $('#viewData').html('');
                    $('#viewData').html(data);
                    $('#detailsViewModal').modal('show');
                }
            },
            error: function () {
                toastr.error('Error', 'Error');
            }
        });
    }
    </script>
@endpush
