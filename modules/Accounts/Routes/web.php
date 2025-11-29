<?php
use App\Enums\PanelPrefixEnum;
use Illuminate\Support\Facades\Route;
use Modules\Accounts\Http\Controllers\SubController;
use Modules\Accounts\Http\Controllers\AccCoaController;
use Modules\Accounts\Http\Controllers\AccBankController;
use Modules\Accounts\Http\Controllers\SubTypeController;
use Modules\Accounts\Http\Controllers\AccReportController;
use Modules\Accounts\Http\Controllers\AccSubcodeController;
use Modules\Accounts\Http\Controllers\AccVoucherController;
use Modules\Accounts\Http\Controllers\AccPredefinedController;
use Modules\Accounts\Http\Controllers\AccInstallmentController;
use Modules\Accounts\Http\Controllers\AccFinancialYearController;
use Modules\Accounts\Http\Controllers\AccOpeningBalanceController;
use Modules\Accounts\Http\Controllers\AccPendingVoucherController;
use Modules\Accounts\Http\Controllers\AccWalletUsersTransactionController;

Route::prefix(PanelPrefixEnum::ADMIN->value)->group(function () {
    Route::group(['prefix' => 'accounts', 'middleware' => 'auth'], function () {

        Route::resource('wallet/user/transaction', AccWalletUsersTransactionController::class)->names('accounts.wallet.user_transaction');
        Route::get('wallet/user/transaction/{id}/details', [AccWalletUsersTransactionController::class, 'details'])->name('accounts.wallet.transaction.details');
        Route::get('wallet/user/receive_list', [AccWalletUsersTransactionController::class, 'receive_list'])->name('accounts.wallet.receive.list');
        Route::post('wallet/user/receive_list/{id}/approve', [AccWalletUsersTransactionController::class, 'approve'])->name('accounts.wallet.receive.approve');
        Route::post('/get-acc-coa-balance', [AccWalletUsersTransactionController::class, 'getAccCoaBalance'])->name('get_acc_coa_balance');

        // financial year
        Route::prefix('financial-year')->name('accounts.financial.year')->controller(AccFinancialYearController::class)->group(function () {
            Route::any('list', 'fin_yearlist')->name('list');
            Route::post('end', 'yearEnding')->name('end');
            Route::post('update', 'singlefinyear_update')->name('update');
        });

        Route::post('open-book', [AccFinancialYearController::class, 'openbook'])->name('accounts.openbook');

        // opening balance
        Route::prefix('opening-balance')->name('accounts.opening-balance.')->controller(AccOpeningBalanceController::class)->group(function () {
            Route::get('list', 'opening_balancelist')->name('list');
            Route::post('get', 'getOpeningBalance')->name('get');
            Route::get('form', 'opening_balanceform')->name('form');
            Route::post('save', 'opening_balance')->name('save');
        });

        Route::get('/get-subtype-by-code/{id}', [AccOpeningBalanceController::class, 'getSubtypeByCode'])->name('subtype.by-code');
        Route::get('/get-subtype-by-id/{id}', [AccOpeningBalanceController::class, 'getSubtypeById'])->name('subtype.by-id');

        Route::prefix('voucher')->name('accounts.voucher.')->controller(AccVoucherController::class)->group(function () {
            Route::any('list', 'voucher_list')->name('list');
            Route::any('deferred-list', 'deferred_voucher_list')->name('deferredList');
            Route::get('form', 'voucher_form')->name('form');
            Route::get('edit/{id}', 'voucher_edit')->name('edit');
            Route::get('update/{id}', 'voucher_edit')->name('edit');
            Route::post('save', 'voucher_save')->name('save');
            Route::post('delete', 'deleteVoucher')->name('delete');
            Route::post('reverse', 'reverseVoucher')->name('reverse');
            Route::post('details', 'voucherDetails')->name('details');
            Route::post('pdf/delete', 'pdfDelete')->name('pdf.delete');
            Route::post('get-list', 'getVoucherList')->name('getList');
            Route::post('get-deferred-list', 'getDeferredVoucherList')->name('getDeferredList');
            Route::get('/delete-vaoucher-attachment/{id}', 'deleteAttachment')->name('delete.attachment');
            Route::post('save-deferred-schedule', 'saveDeferredSchedule')->name('save_deferred_schedule');
            Route::post('remove-deferred', 'removeDeferredSchedule')->name('remove_deferred');
            Route::get('deferred-report', 'deferred_voucher_report')->name('deferredReport');
            Route::post('get-deferred-report', 'getDeferredVoucherReport')->name('getDeferredReport');
            Route::post('detail-children', 'voucherDetailChildren')->name('detailChildren');
            Route::get('/get-deffered-balance/{id}', 'getDefferedBalance')->name('get_deffered_balance');

            Route::get('deferred-schedule', 'deferredSchedule')->name('deferred_schedule');
            Route::post('get-deferred-schedule', 'getDeferredSchedule')->name('getDeferredSchedule');
        });

        // Predefined Accounts Routes
        Route::prefix('predefined-accounts')->name('accounts.predefined.')->controller(AccPredefinedController::class)->group(function () {
            Route::get('/', 'predefined_accounts')->name('accounts');
            Route::get('form', 'predefined_form')->name('form');
            Route::get('edit/{id}', 'predefined_edit')->name('edit');
            Route::post('save', 'predefined_save')->name('save');
            Route::post('update/{id}', 'predefined_update')->name('update');
        });

        Route::post('getPredefinedSettingList', [AccPredefinedController::class, 'getPredefinedSettingList'])->name('getPredefinedSettingList');

        Route::resource('subtypes', SubTypeController::class);
        Route::resource('subcodes', AccSubcodeController::class);
        Route::resource('installments', AccInstallmentController::class);
        Route::get('installment_report', [AccInstallmentController::class, 'installmentReport'])->name('installment-report');
        Route::post('get_installment_report', [AccInstallmentController::class, 'getInstallmentReport'])->name('get-installment-report');


        Route::name('account.')->controller(AccCoaController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/get/detail/{coa}', 'show')->name('show');
            Route::get('/{coa}/edit', 'edit')->name('edit');
            Route::post('/update', 'update')->name('update');
            Route::post('/del', 'destroy')->name('destroy');
            Route::get('/all/subtype', 'allsubtype')->name('allsubtype');
            Route::get('/export-acc-coa', 'exportAccCoaToExcel')->name('export-acc-coa');
            Route::post('/import-acc-coa', 'importAccCoaFromExcel')->name('import-acc-coa');
        });

        Route::prefix('pending')->name('accounts.pending.')->group(function () {
            Route::get('list', [AccPendingVoucherController::class, 'voucher_list'])->name('voucher.list');
            Route::post('get-list', [AccPendingVoucherController::class, 'getPendingVoucherList'])->name('get.list');
            Route::post('approve', [AccPendingVoucherController::class, 'voucherApproved'])->name('voucher.approve');
        });

        Route::name('account.report.')->controller(AccReportController::class)->group(function () {
            // general ledger by link
            Route::get('/general-ledger-report-by-link', 'generalLedgerReportByLink')->name('general.ledger.by-link');
            // general ledger
            Route::get('/financial-reports', 'financial_report')->name('financial');
            Route::post('/general-ledger-report-search', 'generalLedgerReportSearch')->name('general.ledger.search');

            // sub ledger
            Route::get('/sub-ledger-report', 'sub_ledger_report')->name('sub.ledger');
            Route::post('/sub-ledger-report-search', 'subLedgerReportSearch')->name('sub.ledger.search');

            // sub ledger merged
            Route::get('/sub-ledger-merged-report', 'sub_ledger_merged_report')->name('sub.ledger.merged');
            Route::post('/sub-ledger-merged-report-search', 'subLedgerMergedReportSearch')->name('sub.ledger.merged.search');

            // trial balance 
            Route::get('/trial-balance', 'trial_balance_financial_report')->name('trial.balance');
            Route::post('/trial-balance-search', 'trialBalanceReportSearch')->name('trial.balance.search');

            // balance sheet 
            Route::get('/balance-sheet-report', 'balance_sheet_report')->name('balance.sheet.report');
            Route::post('/balance-sheet-report-search', 'balanceSheetReportSearch')->name('balance.sheet.report.search');

            // profit loss
            Route::get('/profit-loss-report', 'profit_loss_report')->name('profit.loss');
            Route::post('/profit-loss-report-search', 'profitLossReportSearch')->name('profit.loss.report.search');

            // profit loss
            Route::get('/received-payment-report', 'received_payment_report')->name('received.payment.report');
            Route::post('/received-payment-report-search', 'received_payment_report_search')->name('received.payment.report.search');

            // income statement
            Route::get('/income-statement', 'income_statement')->name('income.statement');
            Route::post('/income-statement-search', 'income_statement')->name('income.statement.search');


            // helper
            Route::get('/getCoaFromSubtype/{id}', 'getCoaFromSubtype')->name('get.coa.from.subtype');
            Route::get('/getsubcode/{id}', 'getsubcode')->name('get.subcode');

            Route::get('/cash-flow-report', 'cash_flow_report')->name('cash.flow.report');
            Route::post('/cash-flow-report-search', 'cash_flow_report_search')->name('cash.flow.report.search');
        });

        Route::name('account.bank.')->controller(AccBankController::class)->group(function () {
            //Bank ledger
            Route::get('/bank-ledger-report', 'bank_ledger_report')->name('ledger');
            Route::post('/bank-ledger-report-search', 'bank_ledger_report_search')->name('ledger.search');
        });

        Route::prefix('installments')->name('installments.')->controller(AccInstallmentController::class)->group(function () {

            Route::post('approve-for-disbusment/{id}', 'approveForDisbusment')->name('approve-for-disbusment');
            Route::post('adjustment-submit', 'submitAdjustment')->name('adjustment-submit');
            Route::post('hold', 'holdInstallment')->name('hold');
            Route::get('approve/{id}', 'approve')->name('approve');
            Route::put('approve-installment/{id}', 'approveInstallment')->name('approve_installment');
        });
    });
});
