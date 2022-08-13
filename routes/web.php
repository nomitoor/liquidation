<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaterkitController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ManifestController;
use App\Http\Controllers\PalletsController;
use App\Http\Controllers\ContainerController;

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
    Route::post('products-for-manifest', [ManifestController::class, 'getProducts'])->name('products-for-manifest');
    Route::post('import-scanned-products', [ManifestController::class, 'importToScannedProducts'])->name('import-scanned-products');

    Route::get('view-bucket', [ManifestController::class, 'viewBucket']);

    Route::post('remove-scanned-products', [ManifestController::class, 'removeScannedProducts'])->name('remove-scanned-products');

    Route::get('view-scanned-products', [ManifestController::class, 'viewScannedProducts'])->name('view-scanned-products');
    Route::get('all-scanned-products', [ManifestController::class, 'allScannedProducts'])->name('all-scanned-products');
    Route::get('all-uknown-products', [ManifestController::class, 'allUnknownProducts'])->name('all-uknown-products');
    Route::get('all-claim-products', [ManifestController::class, 'allClaims'])->name('all-claim-products');
    Route::get('all-bucket-manifest', [ManifestController::class, 'allBuckets'])->name('allBuckets');
    Route::get('all-daily-manifests', [ManifestController::class, 'allDailyManifests'])->name('all-daily-manifests');
    Route::get('manifest/daily/create', [ManifestController::class, 'dailyCreate'])->name('daily-manifest-create');
    Route::post('manifest/daily/store', [ManifestController::class, 'storeDaily'])->name('daily-manifest-store');

    Route::get('daily-manifest', [ManifestController::class, 'dailyManifest']);

    Route::resource('manifest', ManifestController::class);

    Route::get('download-containers/{container}', [ContainerController::class, 'downloadContainer'])->name('download-container');
    Route::get('export-containers/{container}', [ContainerController::class, 'exportContainers'])->name('export-container');
    Route::get('export-containers/client/{container}', [ContainerController::class, 'exportContainersClient'])->name('export-container-client');

    Route::resource('containers', ContainerController::class);

    Route::post('pallets/delete', [PalletsController::class, 'deletePalletsWithBol']);
    Route::post('pallets/undo', [PalletsController::class, 'undoPallets']);
    Route::resource('pallets', PalletsController::class);

    Route::get('unknown', [PalletsController::class, 'unknown']);
    Route::get('claims', [PalletsController::class, 'claims']);

    Route::post('manifest/checkManifest', [ManifestController::class, 'updateManifest'])->name('checkManifest');

    Route::get('export/scanned/products/{id}', [ManifestController::class, 'exportScannedProducts'])->name('exporScanned');
    Route::get('export/scanned/products/client/{id}', [ManifestController::class, 'clientExportScannedProducts'])->name('client');

    // Route Components
    Route::get('layouts/collapsed-menu', [StaterkitController::class, 'collapsed_menu'])->name('collapsed-menu');
    Route::get('layouts/full', [StaterkitController::class, 'layout_full'])->name('layout-full');
    Route::get('layouts/without-menu', [StaterkitController::class, 'without_menu'])->name('without-menu');
    Route::get('layouts/empty', [StaterkitController::class, 'layout_empty'])->name('layout-empty');
    Route::get('layouts/blank', [StaterkitController::class, 'layout_blank'])->name('layout-blank');


    // locale Route
    Route::get('lang/{locale}', [LanguageController::class, 'swap']);
});
