<div class="row  dashboard_heading">
    <div class="fixed-tab walet-tab col-12 col-md-12 ps-0">
        <div class="wallet-menu">
            <a href="{{ route('account.bank.ledger') }}" class="btn btn-navy rounded-10 {{ request()->routeIs('account.bank.*') ? 'active' : '' }}">{{ __('language.bank_ledger') }}</a>
            <a href="#" class="btn btn-navy rounded-10">{{ __('language.bank_reconciliation') }}</a>

            <div class="dropdown">
                <a class="btn btn-navy dropdown-toggle rounded-10"
                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                   {{ __('language.report') }}
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item " href="#">Top Sheet</a></li>
                </ul>
            </div>
            
        </div>
    </div>
</div>

@push('css')
<style>
    .walet-tab{
            background: #f0f1f2;
            margin-right: 0px !important;
    }
      .table.new-custom-table-two tr td:last-child,
    .table.new-custom-table-two tr th:last-child{
            padding: 13px 20px 13px 5px !important;
    }
    .table.new-custom-table-two tr td:first-child,
    .table.new-custom-table-two tr th:first-child{
            padding: 13px 5px 13px 20px !important;
    }
    .wallet-menu{
        display: flex;
        align-items: center;
        gap: 10px;
    }
    @media only screen and  (max-width: 1200px) {
       .wallet-menu .btn-navy {
    font-size: 12px !important;
    padding: 7px 8px !important;
       }
       .btn-navy,
       .btn-success{
        font-size: 12px !important;
       }
       .wallet-menu{
        flex-wrap: wrap;
        gap: 5px !important;
    }
    
}
@media screen and (min-width: 1200px) and (max-width: 1399px) {
    .wallet-menu .btn-navy {
    font-size: 13px !important;
    padding: 7px 8px !important;
}
.btn-navy,
.btn-success{
        font-size: 13px !important;
       }
       .wallet-menu{
        gap: 5px !important;
    }
}
@media screen and (min-width: 1400px) and (max-width: 1500px) {
    .wallet-menu .btn-navy {
    font-size: 14px !important;
    padding: 7px 8px !important;
}
.btn-navy,
.btn-success{
        font-size: 14px !important;
       }
    }
  
    </style>
@endpush
@push('js')
    <script>
    window.addEventListener('load', adjustLowerDiv);
    window.addEventListener('resize', adjustLowerDiv);

     function adjustLowerDiv() {
        const upper = document.querySelector('.walet-tab');
        const lower = document.querySelector('.fixed-tab-body');

        const topBarHeight = 8;
        const upperHeight = upper.offsetHeight;

        lower.style.marginTop = (topBarHeight + upperHeight) + 'px';
        lower.style.height = `calc(100% - ${topBarHeight + upperHeight}px)`;
    }

    window.addEventListener('load', adjustLowerDiv);
    window.addEventListener('resize', adjustLowerDiv);
</script>
@endpush