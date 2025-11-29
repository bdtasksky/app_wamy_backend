<div class="modal fade" id="allvaucherModal" aria-labelledby="moduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="allVoucherModalLabel">Voucher Detail</h5>
                <button type="button" class="btn-close rmpdf" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="all_vaucher_view" style="padding:0;"></div>
            <div class="modal-footer">
                <button class="btn btn-warning" name="btnPrint" id="btnPrint" onclick="printVaucher('all_vaucher_view');">
                    <i class='fa fa-print'></i> Print
                </button>
                {{-- <a id="pdfDownload" href="" target="_blank" title="Download PDF">
                    <button class="btn btn-success btn-md" name="btnPdf" id="btnPdf">
                        <i class="fa-file-pdf-o"></i> PDF
                    </button>
                </a> --}}
                <button type="button" class="btn btn-danger rmpdf" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    window.appData = {
        voucherDetailsUrl: "{{ route('accounts.voucher.details') }}",
        pdfDeleteUrl: "{{ route('accounts.voucher.pdf.delete') }}",
    };
</script>
<script src="{{ module_asset('Accounts/js/voucher-details.js?v=7') }}"></script> 
@endpush