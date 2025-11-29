<div class="col-md-12 p-4" id="vaucherPrintArea">
    <div class="row">
        <div class="col-md-3">
            {{-- <img src="{{ asset($settingsInfo->logo) }}" alt="Logo" height="40px"><br><br> --}}
        </div>
        <div class="col-md-6 text-center">
            {{-- <h2>{{ $settingsInfo->title }}</h2> --}}



            @if($voucherHead->IsApprove == 0)
                <h4 class="text-danger">Pending Voucher</h4>
            @else
                <h4 class="text-success">Approved Voucher</h4>
            @endif
        </div>
        <div class="col-md-3"></div>
        <div class="col-md-4">
            <div class="border border-3" style="padding-left:10px;">
                <label class="font-weight-600 mb-0">{{ __('language.voucher_type') }}</label> : {{ $voucherHead->VoucherType }}<br>
                <label class="font-weight-600 mb-0">{{ __('language.financial_year') }}</label> : {{ $voucherHead->Fiyear_name }}<br>
                <label class="font-weight-600 mb-0">{{ __('language.voucher_no') }}</label> : {{ $voucherHead->VoucherNumber }}<br>
                <label class="font-weight-600 mb-0">{{ __('language.date') }}</label> : {{ \Carbon\Carbon::parse($voucherHead->VoucherDate)->format('d/m/Y') }}
            </div>
        </div>
        <div class="col-md-4">
            @if(!is_null($accVoucherAttachment) && $accVoucherAttachment->isNotEmpty())
                <table class="table table-bordered">
                    <thead>
                        <tr >
                            <th colspan="2" class="text-center"> {{__('language.attachment')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accVoucherAttachment as $attachment)
                            @php
                                $fileUrl = asset('public/storage/'.$attachment->file_name);
                                $fileExt = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                            @endphp
                            <tr>
                                <td class="text-center">{{ $attachment->attachment_name }}</td>
                                <td class="text-end">
                                    <button class="btn btn-info-soft btn-sm view-attachment" 
                                        data-url="{{ $fileUrl }}" 
                                        data-type="{{ $fileExt }}">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach 
                    </tbody>
                </table>
            @endif
        </div>
        <div class="col-md-4">
            <img class="sidebar-logo sidebar_brand_icon mx-auto img-fluid" src="{{ app_setting()->sidebar_logo }}"
                alt="{{ __('language.logo') }}">
        </div>
    </div>

    <table class="datatable table table-bordered table-hover">
        <thead>
            <tr>
                <th class="text-center">{{ __('language.particulars') }}</th>
                <th class="text-center">Comments</th>
                <th class="text-center">{{ __('language.debit') }}</th>
                <th class="text-center">{{ __('language.credit') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $Debit = 0;
                $Credit = 0;
            @endphp
            @forelse($result as $row)
                @php
                    if ($row->Dr_Amount != 0) {
                        $Debit += $row->Dr_Amount;
                    }
                    if ($row->Cr_Amount != 0) {
                        $Credit += $row->Cr_Amount;
                    }
                @endphp
                <tr>
                    <td><strong style="font-size: 15px;">{{ $row->account_name }}</strong><br></td>
                    <td>{{ $row->LaserComments }}</td>
                    <td class="text-end">{{ number_format($row->Dr_Amount, 2) }}</td>
                    <td class="text-end">{{ number_format($row->Cr_Amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-danger">{{ __('language.data_is_not_available') }}</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="text-end" colspan="2">{{ __('language.total') }}</th>
                <th class="text-end">{{ number_format($Debit, 2) }}</th>
                <th class="text-end">{{ number_format($Credit, 2) }}</th>
            </tr>
            <tr>
                <th colspan="4">{{ __('language.in_words') }} : {{ ucwords(numberToWords($voucherHead->TranAmount)) }}</th>
            </tr>
            <tr>
                <th colspan="4">{{ __('language.remark') }} : {{ $voucherHead->Remarks }}</th>
            </tr>
        </tfoot>
    </table>
    @if($accVoucherChildren)
    <h5 class="text-center" style="margin-top: 100px;">Deferred Schedule</h5>
    <table class="datatable table table-bordered table-hover" >
        <thead>
            <tr>

                <th>{{ __('language.voucher_no') }}</th>
                <th>{{ __('language.date') }}</th>
                <th>{{ __('language.remark') }}</th>
                <th class="text-end">{{ __('language.amount') }}</th>
                <th class="text-center">{{ __('language.status') }}</th>
            </tr>
        </thead>
        <tbody id="ledger-body">
            @foreach($accVoucherChildren as $child)
            <tr class="text-center">
                <td>{{ $child->VoucherNumber }}</td>
                <td>{{ $child->VoucherDate }}</td>
                <td>{{ $child->Remarks }}</td>
                <td>{{ $child->TranAmount }}</td>
                <td>{!! ($child->IsApprove == 1 ? '<span class="label label-success">Approved</span>' : '<span class="label label-danger">Pending</span>') !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="row" style="margin-top: 100px;">
        <div class="col-4 mb-5">
            <div class="border-top border-dark">
                <p class="text-center mt-2">{{ __('language.prepared_by') }}</p>
            </div>
        </div>
        <div class="col-4 mb-5">
            <div class="border-top border-dark">
                <p class="text-center mt-2">{{ __('language.checked_by') }}</p>
            </div>
        </div>
        <div class="col-4 mb-5">
            <div class="border-top border-dark">
                <p class="text-center mt-2">{{ __('language.authorized_signature') }}</p>
            </div>
        </div>
        @if($voucherHead->VoucherType=='Payment Voucher')
        <div class="col-4 mb-5">
            <div class="border-top border-dark">
                <p class="text-center mt-2">{{ __('language.receiver_acknowledgement') }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
