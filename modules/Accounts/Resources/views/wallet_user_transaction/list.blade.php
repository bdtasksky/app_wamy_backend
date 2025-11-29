@extends('backend.layouts.app')

@section('title', __('language.wallet_transfer'))
@push('css')
<link rel="stylesheet" href="{{ module_asset('Wallet/Resources/assets/css/transfer.css?v_' . date('h_i')) }}">
@endpush
@section('content')
{{-- @include('wallet::wallet_header') --}}

    <div class="card mb-4">
        @include('backend.layouts.common.validation')
        @include('backend.layouts.common.message')

        <div class="card-header  py-2 py-xxl-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-17 fw-semi-bold mb-0">{{ __('language.wallet_transfer') }}</h6>
                </div>
                <div class="text-end">
                    <div class="actions">
                        @can('create_wallet_users_transaction')
                        <a href="{{ route('accounts.wallet.user_transaction.create') }}" class="btn btn-navy px-3 px-xl-4 py-2 fs-15 rounded-8"><i class="fa fa-plus-circle"></i>&nbsp;{{ __('language.transfer') }}</a>
                        @endcan
                    </div>
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
