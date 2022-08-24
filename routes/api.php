<?php

use App\Http\Controllers\PalletsAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('pallet/store', [PalletsAPIController::class, 'storeAPI']);
Route::get('pallet/view', [PalletsAPIController::class, 'allPallets']);
Route::get('pallet/view/{id}', [PalletsAPIController::class, 'getPallet']);
Route::post('manifests/details', [PalletsAPIController::class, 'getManifestDetails']);

Route::post('pallets/update/{pallet}', [PalletsAPIController::class, 'addToPallet']);
Route::post('manifest/add-to-unknown', [PalletsAPIController::class, 'addToUknown']);
Route::post('manifest/addToScannedAndPallet', [PalletsAPIController::class, 'addToScannedAndPallet']);
Route::post('manifest/removePallets', [PalletsAPIController::class, 'removePallets']);
