<?php
use App\Enums\PanelPrefixEnum;
use Illuminate\Support\Facades\Route;
use Modules\Project\Http\Controllers\ProjectController;
use Modules\Project\Http\Controllers\ProjectPostController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix(PanelPrefixEnum::ADMIN->value)->group(function () {
    Route::group(['prefix' => 'project', 'middleware' => ['auth']], function () {

        Route::controller(ProjectController::class)->name('project.')->group(function () {

            Route::get('/list_of_projects', 'index')->name('index');
            Route::get('/show', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{project:id}', 'edit')->name('edit');
            Route::put('/update/{id}', 'update')->name('update');
            Route::delete('delete/{project:id}', 'destroy')->name('destroy');
        });
        Route::controller(ProjectPostController::class)->name('project.post.')->group(function () {
            Route::get('/project-post','create')->name('create');
            Route::post('/project-post-store', 'store')->name('store');
        });

    });
});