<?php

namespace Modules\Theme\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ThemeSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('themes')->insert([
            ['name' => 'Classic', 'image_path' => 'backend/img/themes/1.png', 'is_active' => false],
            ['name' => 'News', 'image_path' => 'backend/img/themes/2.png', 'is_active' => false],
            ['name' => 'Magazine', 'image_path' => 'backend/img/themes/3.png', 'is_active' => false],
            ['name' => 'Times', 'image_path' => 'backend/img/themes/4.png', 'is_active' => true],
            ['name' => 'Gazette', 'image_path' => 'backend/img/themes/5.png', 'is_active' => false],
            ['name' => 'Fashion', 'image_path' => 'backend/img/themes/6.png', 'is_active' => false],
            ['name' => 'Penmark', 'image_path' => 'backend/img/themes/7.png', 'is_active' => false],
            ['name' => 'Storylane', 'image_path' => 'backend/img/themes/8.png', 'is_active' => false],
            ['name' => 'Wordcraft', 'image_path' => 'backend/img/themes/9.png', 'is_active' => false],
        ]);
    }
}
