<?php

namespace Modules\Accounts\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class AccountsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(AccCoasTableSeeder::class);
        $this->call(AccFinancialyearTableSeeder::class);
        $this->call(AccOpeningbalanceTableSeeder::class);
        $this->call(AccPredefinedTableSeeder::class);
        $this->call(AccPredefinedSeetingTableSeeder::class);
        $this->call(AccReportFormateTableSeeder::class);
        $this->call(AccReportNameTableSeeder::class);
        $this->call(AccSubcodeTableSeeder::class);
        $this->call(AccSubtypeTableSeeder::class);
        $this->call(AccTransactionsTableSeeder::class);
        $this->call(AccTypesTableSeeder::class);
        $this->call(AccVouchartypeTableSeeder::class);
        $this->call(AccVoucherDetailsTableSeeder::class);
        $this->call(AccVoucherEventTableSeeder::class);
        $this->call(AccVoucherMasterTableSeeder::class);
    }
}
