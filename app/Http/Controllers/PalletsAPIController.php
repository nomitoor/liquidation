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
        $scanned_products = ScannedProducts::where('pallet_id', $pallet->id)->get();

        return response()->json(array('code' => '201', 'pallet_data' => $pallet, 'products' => $scanned_products, 'message' => 'Pallet Found'));
    }

    public function getManifestDetails(Request $request)
    {
        $weekly_data = Manifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->toArray();
        $daily_data = DailyManifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->toArray();

        $daily_bucket = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->where('package_id', 'DROPSHIP_BIN')->get()->toArray();
        $weekly_bucket = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->where('package_id', 'DROPSHIP_BIN')->get()->toArray();

        $data = array_merge($weekly_data, $daily_data);

        $scanned_data = ScannedProducts::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->get()->toArray();

        return response()->json(array('message' => 'Found in daily or weekly Manifest', 'data' => $data));
    }
}
