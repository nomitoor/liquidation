<?php

namespace App\Http\Controllers;

use App\Models\ClaimList;
use App\Models\DailyManifest;
use App\Models\Manifest;
use App\Models\Pallets;
use App\Models\ScannedProducts;
use Illuminate\Http\Request;

class PalletsAPIController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAPI(Request $request)
    {
        $pallet = Pallets::create([
            'description' => $request->description,
            'category_id' => $request->category_id
        ]);

        return response()->json(array('message' => 'Pallet Created successfully', 'id' => $pallet->id));
    }

    public function allPallets(Request $request)
    {
        $pallets = Pallets::with('category')->orderBy('id', 'desc')->paginate($request->per_page ?? 5);
        return response()->json($pallets);
    }

    public function getPallet(Request $request)
    {
        $pallet = Pallets::with('category')->find($request->id);
        // if (count($pallet)) {
        //        } else {
        //     return response()->json(array('code' => '404', 'message' => 'Pallet Not Found'));
        // }
        $scanned_products = ScannedProducts::where('pallet_id', $pallet->id)->get();
        return response()->json(array('code' => '201', 'pallet_data' => $pallet, 'products' => $scanned_products, 'message' => 'Pallet Found'));
    }

    public function getManifestDetails(Request $request)
    {
        $weekly_data = Manifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->toArray();
        $daily_data = DailyManifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->toArray();
        $with_lpn = Manifest::where('lpn', $request->manifest_id)->get()->toArray();

        $scanned_data = ScannedProducts::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->toArray();

        $daily_bucket = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->where('package_id', 'DROPSHIP_BIN')->get()->toArray();
        $weekly_bucket = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->where('package_id', 'DROPSHIP_BIN')->get()->toArray();
        $bucket_data = array_merge($daily_bucket, $weekly_bucket);

        $daily_bucket_scanned = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->whereNotNull('bol_ids')->get()->toArray();
        $weekly_bucket_scanned = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->whereNotNull('bol_ids')->get()->toArray();
        $bucket_scanned_data = array_merge($daily_bucket_scanned, $weekly_bucket_scanned);

        if (count($scanned_data)) {
            $total_cost = ScannedProducts::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->sum('total_cost');
            $unit_cost = ScannedProducts::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->sum('units');

            return response()->json(array('code' => 203, 'message' => 'Already received', 'unit_cost' => number_format($total_cost, 2), 'total_cost' => number_format($unit_cost, 2), 'data' => $scanned_data));
        } else if (count($with_lpn)) {
            return response()->json(array('message' => 'Found with LPN', 'data' => $with_lpn, 'code' => '201'));
        } else if (count($weekly_data)) {
            $total_cost = Manifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->sum('total_cost');
            $unit_cost = Manifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->sum('units');

            return response()->json(array('code' => 201, 'message' => 'Found in weekly Manifest', 'unit_cost' => number_format($total_cost, 2), 'total_cost' => number_format($unit_cost, 2), 'data' => $weekly_data));
        } else if (count($daily_data)) {
            $total_cost = DailyManifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->sum('total_cost');
            $unit_cost = DailyManifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->sum('units');

            return response()->json(array('code' => 201, 'message' => 'Found in weekly Manifest', 'unit_cost' => number_format($total_cost, 2), 'total_cost' => number_format($unit_cost, 2), 'data' => $daily_data));
        } else if (count($bucket_data)) {
            return response()->json(array('code' => 204, 'message' => 'This bol id is a part of bucket', 'data' => $bucket_data));
        } else if (count($bucket_scanned_data)) {
            return response()->json(array('code' => 205, 'message' => 'This bol id is a part of bucket', 'data' => $bucket_scanned_data));
        } else {
            return response()->json(array('code' => 206, 'product_id' => $request->manifest_id, 'message' => 'Product Not found do you want this to unknown',));
        }
    }

    // Add to pallet with bol id and pallet
    public function addToPallet(Request $request, Pallets $pallet)
    {
        $products_query = ScannedProducts::where('bol', $request->bol_id)->whereNull('pallet_id');
        $with_package_id = ScannedProducts::where('package_id', $request->bol_id)->whereNull('pallet_id');

        $scanned_products = $products_query->get();
        $scanned_products_with_package_id = $with_package_id->get();

        if (count($scanned_products)) {
            $bol_id_array = unserialize($pallet->bol_ids);
            if ($bol_id_array) {
                foreach ($bol_id_array as $ids) {
                    if ($ids == $request->bol_id) {
                        return response()->json(['code' => 201, 'message' => 'Bol already added to this pallet']);
                    }
                }
                array_push($bol_id_array, $request->bol_id);
            } else {
                $bol_id_array = [];
                array_push($bol_id_array, $request->bol_id);
            }

            $total_price = 0;
            $total_units = 0;

            foreach ($scanned_products as $products) {
                $total_price += (float) $products->total_cost;
                $total_units += (int) $products->units;
            }

            $new_total_price = $pallet->total_price + $total_price;
            $new_total_units = $pallet->total_unit + $total_units;

            ScannedProducts::where('bol', $request->bol_id)->orWhere('package_id', $request->bol_id)->update(['pallet_id' => $pallet->id]);
            ScannedProducts::whereIn('bol', $bol_id_array)->get(['id', 'bol', 'package_id', 'item_description', 'units', 'unit_cost', 'total_cost']);

            $pallet->update([
                'bol_ids' => serialize($bol_id_array),
                'total_price' => $new_total_price,
                'total_unit' => $new_total_units,
            ]);
            return response()->json(array('code' => 201, 'message' => 'Pallet updated Succesfully'));
        } else if (count($scanned_products_with_package_id)) {
            $bol_id_array = unserialize($pallet->bol_ids);
            if ($bol_id_array) {
                foreach ($bol_id_array as $ids) {
                    if ($ids == $request->bol_id) {
                        return response()->json(array('code' => 403, 'message' => 'Bol already added to this pallet'));
                    }
                }
                array_push($bol_id_array, $request->bol_id);
            } else {
                $bol_id_array = [];
                array_push($bol_id_array, $request->bol_id);
            }

            $total_price = 0;
            $total_units = 0;

            foreach ($scanned_products_with_package_id as $products) {
                $total_price += (float) $products->total_cost;
                $total_units += (int) $products->units;
            }

            $new_total_price = $pallet->total_price + $total_price;
            $new_total_units = $pallet->total_unit + $total_units;

            ScannedProducts::where('bol', $request->bol_id)->orWhere('package_id', $request->bol_id)->update(['pallet_id' => $pallet->id]);
            ScannedProducts::whereIn('package_id', $bol_id_array)->get(['id', 'bol', 'package_id', 'item_description', 'units', 'unit_cost', 'total_cost']);

            $pallet->update([
                'bol_ids' => serialize($bol_id_array),
                'total_price' => $new_total_price,
                'total_unit' => $new_total_units,
            ]);

            return response()->json(array('code' => 201, 'message' => 'Pallet updated Succesfully'));
        } else {

            $products_query = ScannedProducts::where('bol', $request->bol_id)->where('pallet_id', '<>', NULL)->first();
            $with_package_id = ScannedProducts::where('package_id', $request->bol_id)->where('pallet_id', '<>', NULL)->first();

            if (!is_null($products_query)) {
                $pallet_details = Pallets::where('id', $products_query->pallet_id)->first();
                return response()->json(array('code' => 201, 'message' => 'This BOL ID is already part of PALLET: ' . $pallet_details->description . ' with PALLET ID: DE' . sprintf("%05d", $pallet_details->id)));
            } else {
                $pallet_details = Pallets::where('id', $with_package_id->pallet_id)->first();
                return response()->json(array('code' => 201, 'message' => 'This BOL ID is already part of PALLET: ' . $pallet_details->description . ' with PALLET ID: DE' . sprintf("%05d", $pallet_details->id)));
            }
        }
    }

    // Add to unknown
    public function addToUknown(Request $request)
    {
        if ($request->save_as == 'bol') {
            $bol_id = $request->id;
            $package_id = '';
        } else {
            $package_id = $request->id;
            $bol_id = '';
        }

        ScannedProducts::create([
            'bol' => $bol_id,
            'package_id' => $package_id,
            'unknown_list' => 'yes'
        ]);

        return response()->json(array('message' => 'Added to unknown list', 'code' => '201'));
    }

    public function addToScannedAndPallet(Request $request)
    {
        $with_package_id = Manifest::where('package_id', $request->bol_id)->get();
        $with_bol_id = Manifest::where('bol', $request->bol_id)->get();

        $daily_with_package_id = DailyManifest::where('package_id', $request->bol_id)->get();
        $daily_with_bol_id = DailyManifest::where('bol', $request->bol_id)->get();

        if (count($with_package_id)) {
            foreach ($with_package_id as $item) {
                if ($request->claim_list) {
                    ClaimList::create([
                        'bol' => $item->bol,
                        'package_id' => $item->package_id,
                        'item_description' => $item->item_description,
                        'units' => $item->units,
                        'unit_cost' => $item->unit_cost,
                        'total_cost' => $item->total_cost,
                        'claim_desription' => $request->description
                    ]);
                } else {
                    ScannedProducts::create([
                        'bol' => $item->bol,
                        'package_id' => $item->package_id,
                        'item_description' => $item->item_description,
                        'units' => $item->units,
                        'unit_cost' => $item->unit_cost,
                        'total_cost' => $item->total_cost,
                        'asin' => $item->asin,
                        'GLDesc' => $item->GLDesc,
                        'unit_recovery' => $item->unit_recovery,
                        'total_recovery' => $item->total_recovery,
                        'recovery_rate' => $item->recovery_rate,
                        'removal_reason' => $item->removal_reason
                    ]);
                }
                $item->delete();
            }

            if ($request->has('pallet_id')) {
                $this->addToPallet($request, Pallets::find($request->pallet_id));
            }
        } else if (count($with_bol_id)) {
            foreach ($with_bol_id as $item) {
                if ($request->claim_list) {
                    ClaimList::create([
                        'bol' => $item->bol,
                        'package_id' => $item->package_id,
                        'item_description' => $item->item_description,
                        'units' => $item->units,
                        'unit_cost' => $item->unit_cost,
                        'total_cost' => $item->total_cost,
                        'claim_desription' => $request->description
                    ]);
                } else {
                    ScannedProducts::create([
                        'bol' => $item->bol,
                        'package_id' => $item->package_id,
                        'item_description' => $item->item_description,
                        'units' => $item->units,
                        'unit_cost' => $item->unit_cost,
                        'total_cost' => $item->total_cost,
                        'asin' => $item->asin,
                        'GLDesc' => $item->GLDesc,
                        'unit_recovery' => $item->unit_recovery,
                        'total_recovery' => $item->total_recovery,
                        'recovery_rate' => $item->recovery_rate,
                        'removal_reason' => $item->removal_reason
                    ]);
                }
                $item->delete();
            }
            if ($request->has('pallet_id')) {
                $this->addToPallet($request, Pallets::find($request->pallet_id));
            }
        } else if (count($daily_with_package_id)) {
            foreach ($daily_with_package_id as $item) {
                if ($request->claim_list) {
                    ClaimList::create([
                        'bol' => $item->bol,
                        'package_id' => $item->package_id,
                        'item_description' => $item->item_description,
                        'units' => $item->units,
                        'unit_cost' => $item->unit_cost,
                        'total_cost' => $item->total_cost,
                        'claim_desription' => $request->description
                    ]);
                } else {
                    ScannedProducts::create([
                        'bol' => $item->bol,
                        'package_id' => $item->package_id,
                        'item_description' => $item->item_description,
                        'units' => $item->units,
                        'unit_cost' => $item->unit_cost,
                        'total_cost' => $item->total_cost,
                        'asin' => $item->asin,
                        'GLDesc' => $item->GLDesc,
                        'unit_recovery' => $item->unit_recovery,
                        'total_recovery' => $item->total_recovery,
                        'recovery_rate' => $item->recovery_rate,
                        'removal_reason' => $item->removal_reason
                    ]);
                }
                $item->delete();
            }
            if ($request->has('pallet_id')) {
                $this->addToPallet($request, Pallets::find($request->pallet_id));
            }
        } else if (count($daily_with_bol_id)) {
            foreach ($daily_with_bol_id as $item) {
                if ($request->claim_list) {
                    ClaimList::create([
                        'bol' => $item->bol,
                        'package_id' => $item->package_id,
                        'item_description' => $item->item_description,
                        'units' => $item->units,
                        'unit_cost' => $item->unit_cost,
                        'total_cost' => $item->total_cost,
                        'claim_desription' => $request->description
                    ]);
                } else {
                    ScannedProducts::create([
                        'bol' => $item->bol,
                        'package_id' => $item->package_id,
                        'item_description' => $item->item_description,
                        'units' => $item->units,
                        'unit_cost' => $item->unit_cost,
                        'total_cost' => $item->total_cost,
                        'asin' => $item->asin,
                        'GLDesc' => $item->GLDesc,
                        'unit_recovery' => $item->unit_recovery,
                        'total_recovery' => $item->total_recovery,
                        'recovery_rate' => $item->recovery_rate,
                        'removal_reason' => $item->removal_reason
                    ]);
                }
                $item->delete();
            }
            if ($request->has('pallet_id')) {
                $this->addToPallet($request, Pallets::find($request->pallet_id));
            }
        }

        return response()->json(array('message' => 'Manifest products updated', 'code' => '201'));
    }

    public function removePallets(Request $request)
    {
        $pallet = Pallets::where('id', $request->id)->first();
        $data = unserialize($pallet->bol_ids);

        if (($key = array_search($request->bol_id, $data)) !== false || ($key = array_search($request->package_id, $data)) !== false) {
            unset($data[$key]);
        }

        ScannedProducts::where('bol', $request->bol_id)->orWhere('package_id', $request->package_id)->update(['pallet_id' => NULL]);

        $total_price = 0;
        $total_units = 0;
        foreach ($data as $value) {
            $products = ScannedProducts::where('bol', $value)->orWhere('package_id', $value)->get(['units', 'unit_cost', 'total_cost']);

            foreach ($products as $product) {
                $total_price += (float) $product->total_cost;
                $total_units += (int) $product->units;
            }
        }

        $pallet->update([
            'bol_ids' => serialize($data),
            'total_price' => $total_price,
            'total_unit' => $total_units,
        ]);

        return response()->json(array('code' => '201', 'message' => 'Pallet removed from the list'));
    }
}
