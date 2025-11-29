<div class="modal-header">
    <div class="d-flex flex-column flex-md-row gap-3 w-100 justify-content-between px-4 pt-4">
        <div>
            <h2 class="fs-25 fw-bold text-navy">{{ app_setting()->title }}</h2>
            <p>{{ app_setting()->address }}</p>
            <div>
                <span class="text-navy fw-bold">VAT Number</span>
                <span>{{ app_setting()->tax_no }}</span>
            </div>
            <div>
                <span class="text-navy fw-bold">Commercial Registration Number</span>
                <span>{{ app_setting()->commercial_registration_no }}</span>
            </div>
        </div>
        <img src="{{ app_setting()->logo }}" alt="Company Logo" />
    </div>
</div>

<div class="modal-body">
    <div class="px-4">
        <table class="dataTable rounded-10 table table-bordered">
            <tbody>
                <tr>
                    <th>
                        <span class="text-navy">Date</span>
                        <br />
                        <span class="fw-medium">{{ \Carbon\Carbon::parse($data->posting_date)->format('d/m/Y') ?? '-' }}</span>
                    </th>
                    <th>
                        <span class="text-navy">Transaction No</span>
                        <br />
                        <span class="fw-medium">{{ $data->transaction_id ?? '-' }}</span>
                    </th>
                    <th>
                        <span class="text-navy">Received By</span>
                        <br />
                        <span class="fw-medium">
                            @switch($data->transfer_type)
                                @case('employee_collection')
                                    Employee: {{ $data->employee->name ?? '—' }}
                                    @break

                                @case('party_collection')
                                    Project: {{ $data->project->name ?? '—' }}
                                    @break

                                @case('add_money_from_system')
                                    System Account Head: {{ $data->systemCOA->account_name ?? '—' }}
                                    @break

                                @default
                                    Wallet User: {{ $data->from_wallet_user->wallet_user_name ?? '—' }}
                            @endswitch
                        </span>
                    </th>
                    <th>
                        <span class="text-navy">Received From</span>
                        <br />
                        <span class="fw-medium">
                            Wallet User: {{ $data->to_wallet_user->wallet_user_name ?? '—' }}
                       </span>
                    </th>     
                </tr>
                <tr>
                    <th>
                        <span class="text-navy">Amount</span>
                        <br />
                        <span class="fw-medium">{{ bt_number_format($data->amount) }}
                            {{ $data->currency ?? 'SAR' }}</span>
                    </th>
                    <th>
                        <span class="text-navy">Cash</span>
                        <br />
                        <span class="fw-medium">{{ bt_number_format($data->cash_amount) ?? '0.000' }}</span>
                    </th>
                    <th>
                        <span class="text-navy">Bank</span>
                        <br />
                        <span class="fw-medium">{{ bt_number_format($data->bank_amount) ?? '0.000' }}</span>
                    </th>
                    <th>
                        <span class="text-navy">Status</span>
                        <br />
                        <span class="fw-medium {{ $data->transaction_status == 'Received' ? 'text_green' : 'text-danger' }}">
                            {{ ucfirst($data->transaction_status) }}
                        </span>
                    </th>
                </tr>
                <tr>
                    <th colspan="4">
                        <span class="text-navy">Remark</span>
                        <br />
                        <span class="fw-medium">{{ $data->narration ?? '-' }}</span>
                    </th>
                </tr>
            </tbody>
        </table>

        <div class="d-flex justify-content-end gap-3 my-4">
            <button type="button" class="btn btn-danger bg-red px-4 py-2 rounded-3"
                data-bs-dismiss="modal">Close</button>
            <a href="#" class="btn btn-success submit_button px-4 py-2 rounded-3" onclick="getPDF('viewData');">Download
                PDF</a>
        </div>
        <p class="text-center fs-15 text-muted">{{ app_setting()->title }}</p>
    </div>
</div>
