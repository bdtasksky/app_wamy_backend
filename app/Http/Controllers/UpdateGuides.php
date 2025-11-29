<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class UpdateGuides extends Controller
{
    public function __construct()
    {
        $this->middleware(['demo'])->only(['runUpdatedCommand']);
    }

    public function index()
    {
        return view('backend.update_guides');
    }

    public function runUpdatedCommand()
    {
        try {
            // Step 1: Clear cachesy
            Artisan::call('cache:clear');
            Artisan::call('optimize:clear');

            // Step 2: DB inserts
            DB::beginTransaction();

            // Add permalink column if not exists
            $columns = DB::select("SHOW COLUMNS FROM applications LIKE 'permalink'");
            if (empty($columns)) {
                DB::statement("
                    ALTER TABLE `applications` 
                    ADD COLUMN `permalink` ENUM('default', 'category', 'date') NOT NULL DEFAULT 'default' 
                    AFTER `show_archive_post`;
                ");
            }

            // Add slug column if not exists
            $columns = DB::select("SHOW COLUMNS FROM post_tags LIKE 'slug'");
            if (empty($columns)) {
                DB::statement("
                    ALTER TABLE `post_tags`
                    ADD COLUMN `slug` VARCHAR(255) AFTER `tag`,
                    ADD INDEX (`slug`);
                ");
            }

            // Populate slug values
            DB::statement("UPDATE post_tags SET slug = LOWER(REPLACE(TRIM(tag), ' ', '-'));");
            DB::statement("UPDATE post_tags SET tag = REPLACE(tag, '-', ' ');");

            DB::commit();

            return redirect('admin/update/guides')->with('success', localize('update_completed_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect('admin/update/guides')
                ->with('fail', localize('update_failed'));
        }
    }
}
