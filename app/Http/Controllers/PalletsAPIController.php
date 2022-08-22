<?php

namespace App\Http\Controllers;

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

        $scanned_data = ScannedProducts::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->toArray();

        $daily_bucket = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->where('package_id', 'DROPSHIP_BIN')->get()->toArray();
        $weekly_bucket = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->where('package_id', 'DROPSHIP_BIN')->get()->toArray();
        $bucket_data = array_merge($daily_bucket, $weekly_bucket);

        $daily_bucket_scanned = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->whereNotNull('bol_ids')->get()->toArray();
        $weekly_bucket_scanned = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->whereNotNull('bol_ids')->get()->toArray();
        $bucket_scanned_data = array_merge($daily_bucket_scanned, $weekly_bucket_scanned);

        if (count($weekly_data)) {
            $total_cost = Manifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->sum('total_cost');
            $unit_cost = Manifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->sum('unit_cost');

            return response()->json(array('code' => 201, 'message' => 'Found in weekly Manifest', 'total_cost' => number_format($total_cost, 2), 'unit_cost' => number_format($unit_cost, 2), 'data' => $weekly_data));
        } else if (count($daily_data)) {
            $total_cost = DailyManifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->sum('total_cost');
            $unit_cost = DailyManifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->sum('unit_cost');

            return response()->json(array('code' => 201, 'message' => 'Found in weekly Manifest', 'total_cost' => number_format($total_cost, 2), 'unit_cost' => number_format($unit_cost, 2), 'data' => $daily_data));
        } else if (count($scanned_data)) {
            return response()->json(array('code' => 203, 'message' => 'Already received', 'data' => $scanned_data));
        } else if (count($bucket_data)) {
            return response()->json(array('code' => 204, 'message' => 'This bol id is a part of bucket', 'data' => $bucket_data));
        } else if (count($bucket_scanned_data)) {
            return response()->json(array('code' => 205, 'message' => 'This bol id is a part of bucket', 'data' => $bucket_scanned_data));
        } else {
            return response()->json(array('code' => 206, 'product_id' => $request->manifest_id, 'message' => 'Product Not found do you want this to unknown',));
        }
    }
}
