<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaterkitController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ManifestController;
use App\Http\Controllers\PalletsController;

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

// Auth::routes();
Auth::routes();

Route::get('migrate', function () {
    \Artisan::call('migrate');
    \Artisan::call('db:seed');
});


Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [StaterkitController::class, 'home'])->name('home');
    Route::get('home', [StaterkitController::class, 'home'])->name('home');

    Route::get('get-all', [ManifestController::class, 'getAll'])->name('allManifest');
    Route::get('show-found-products', [ManifestController::class, 'getFoundProducts']);
    Route::get('bar-code-scanner', [ManifestController::class, 'codeScanner']);
    Route::post('scanned-manifests', [ManifestController::class, 'getManifest'])->name('scanned-manifests');
    Route::post('import-scanned-products', [ManifestController::class, 'importToScannedProducts'])->name('import-scanned-products');
    Route::get('view-scanned-products', [ManifestController::class, 'viewScannedProducts'])->name('view-scanned-products');
    Route::get('all-scanned-products', [ManifestController::class, 'allScannedProducts'])->name('all-scanned-products');
    Route::resource('manifest', ManifestController::class);
    
    Route::post('pallets/delete', [PalletsController::class, 'deletePalletsWithBol']);
    Route::post('pallets/undo', [PalletsController::class, 'undoPallets']);
    Route::resource('pallets', PalletsController::class);

    Route::get('unknown', [PalletsController::class, 'unknown']);
    Route::get('claims', [PalletsController::class, 'claims']);

    // Route Components
    Route::get('layouts/collapsed-menu', [StaterkitController::class, 'collapsed_menu'])->name('collapsed-menu');
    Route::get('layouts/full', [StaterkitController::class, 'layout_full'])->name('layout-full');
    Route::get('layouts/without-menu', [StaterkitController::class, 'without_menu'])->name('without-menu');
    Route::get('layouts/empty', [StaterkitController::class, 'layout_empty'])->name('layout-empty');
    Route::get('layouts/blank', [StaterkitController::class, 'layout_blank'])->name('layout-blank');


    // locale Route
    Route::get('lang/{locale}', [LanguageController::class, 'swap']);
});
