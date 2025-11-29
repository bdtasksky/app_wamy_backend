<div class="row dashboard_heading mb-3">
    <div class="fixed-tab walet-tab col-12 col-md-12 ps-0">
        <div class="wallet-menu">
            @can('read_account_reports')
                <a class="btn btn-navy rounded-10 {{ request()->routeIs('account.report.financial') || request()->routeIs('account.report.general.ledger.search') ? 'active' : '' }}" href="{{ route('account.report.financial') }}">
                    {{ __('language.general_ledger') }}
                </a>
                
                <a class="btn btn-navy rounded-10 {{ request()->routeIs('account.report.sub.ledger') || request()->routeIs('account.report.sub.ledger.search') ? 'active':'' }}" href="{{ route('account.report.sub.ledger') }}">
                    {{ __('language.sub_ledger') }}
                </a>
                
                <a class="btn btn-navy rounded-10 {{ request()->routeIs('account.report.sub.ledger.merged') || request()->routeIs('account.report.sub.ledger.merged.search') ? 'active':'' }}" href="{{ route('account.report.sub.ledger.merged') }}">
                    {{ __('language.sub_ledger_merged') }}
                </a>
                
                <a class="btn btn-navy rounded-10 {{ request()->routeIs('account.report.trial.balance') || request()->routeIs('account.report.trial.balance.search') ? 'active':'' }}" href="{{ route('account.report.trial.balance') }}">
                    {{ __('language.trial_balance') }}
                </a>
                
                <a class="btn btn-navy rounded-10 {{ request()->routeIs('account.report.received.payment.report') || request()->routeIs('account.report.received.payment.report.search') ? 'active':'' }}" href="{{ route('account.report.received.payment.report') }}">
                    {{ __('language.received_payment') }}
                </a>
                
                <a class="btn btn-navy rounded-10 {{ request()->routeIs('account.report.profit.loss') || request()->routeIs('account.report.profit.loss.report.search') ? 'active':'' }}" href="{{ route('account.report.profit.loss') }}">
                    {{ __('language.profit_loss') }}
                </a>

                <a class="btn btn-navy rounded-10 {{ request()->routeIs('account.report.income.statement') || request()->routeIs('account.report.income.statement.search') ? 'active':'' }}" href="{{ route('account.report.income.statement') }}">
                    {{ __('language.income_statement') }}
                </a>
                
                <a class="btn btn-navy rounded-10 {{ request()->routeIs('account.report.balance.sheet.report') || request()->routeIs('account.report.balance.sheet.report.search') ? 'active':'' }}" href="{{ route('account.report.balance.sheet.report') }}">
                    {{ __('language.balance_sheet') }}
                </a>
            @endcan
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
  .wallet-menu .btn.active {
    background-color: #feae45 !important;
    color: #fff !important;
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