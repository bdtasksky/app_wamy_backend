<style>
    .shadow-02{
        box-shadow: rgba(0, 0, 0, 0.24) 0px 0px 3px;
    }
    .btn-plane{
        font-size: 15px;
        font-weight: 600;
        padding: 10px 15px;
        border: 0px;
        background: transparent;
    }
    .btn-plane:hover{
        color: #019868;
    }
    .btn-plane.active{
        background-color: #019868;
        color: #fff;
        border-radius: 6px;
    }
</style>

<div class="card">
    <div class="card-header">
        <!-- Opening Balance List Button -->
        <a href="{{ route('accounts.opening-balance.list') }}">
            <button class="btn-plane {{ request()->segment(3) == 'opening_balancelist' ? 'active' : '' }}">
                {{ __('language.opening_balance') }}
            </button>
        </a>

        <?php
            // Fetch ended year (you can convert this to a query in Blade later)
            $ended_year = DB::table('acc_financialyear')->where('is_active', 2)->first();
        ?>

        @if ($ended_year)
            <!-- Display nothing if ended year exists (no action required) -->
        @else
            <!-- Add Opening Balance Button -->
            <a href="{{ route('accounts.opening-balance.form') }}">
                <button class="btn-plane {{ request()->segment(3) == 'opening_balanceform' ? 'active' : '' }}">
                    {{ __('language.add_opening_balance') }}
                </button>
            </a>
        @endif
    </div>
</div>
