<?php

namespace App\Http\Controllers;
use DB;

use App\Models\ClaimList;
use App\Models\DailyManifest;
use App\Models\Manifest;
use App\Models\Pallets;
use App\Models\LPN;
use App\Models\PalletProductRelation;
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
        // $pallets = Pallets::with('category')->orderBy('id', 'desc')->paginate($request->per_page ?? 5);
        $pallets = Pallets::with('category')->orderBy('id', 'desc')->paginate(25);
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



    // Searching inside weekly & Daily.
    // Search on base of Packageid, LQIN, Bol
    public function getManifestDetails(Request $request)
    {
        if ($request->manifest_id != null && trim($request->manifest_id, ' ') != '' && $request->bol_id!='DROPSHIP_BIN') {


            $weekly_data = Manifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->orWhere('lpn', $request->manifest_id)->orWhere('lqin', $request->manifest_id)->get()->toArray();
            $daily_data = DailyManifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->orWhere('lpn', $request->manifest_id)->orWhere('lqin', $request->manifest_id)->get()->toArray();
            $scanned_data = ScannedProducts::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->orWhere('lqin', $request->manifest_id)->get()->toArray();

            $daily_bucket = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->where('package_id', 'DROPSHIP_BIN')->get()->toArray();
            $weekly_bucket = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->where('package_id', 'DROPSHIP_BIN')->get()->toArray();
            $bucket_data = array_merge($daily_bucket, $weekly_bucket);

            $daily_bucket_scanned = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->whereNotNull('bol_ids')->get()->toArray();
            $weekly_bucket_scanned = DailyManifest::whereRaw("find_in_set('$request->manifest_id',bol)")->whereNotNull('bol_ids')->get()->toArray();
            $bucket_scanned_data = array_merge($daily_bucket_scanned, $weekly_bucket_scanned);

            if (count($scanned_data)) {
                $total_cost = ScannedProducts::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->orWhere('lqin', $request->manifest_id)->get()->sum('total_cost');
                $unit_cost = ScannedProducts::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->orWhere('lqin', $request->manifest_id)->get()->sum('units');
                $total_recovery = ScannedProducts::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->orWhere('lqin', $request->manifest_id)->get()->sum('total_recovery');

                return response()->json(array('code' => 203, 'message' => 'Already received', 'unit_cost' => number_format($total_cost, 2), 'total_cost' => number_format($unit_cost, 2), 'total_recovery' => number_format($total_recovery, 2),  'data' => $scanned_data));
            } else if (count($weekly_data)) {
                $total_cost = Manifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->orWhere('lpn', $request->manifest_id)->orWhere('lqin', $request->manifest_id)->get()->sum('total_cost');
                $unit_cost = Manifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->orWhere('lpn', $request->manifest_id)->orWhere('lqin', $request->manifest_id)->get()->sum('units');
                $total_recovery = Manifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->orWhere('lpn', $request->manifest_id)->orWhere('lqin', $request->manifest_id)->get()->sum('total_recovery');

                return response()->json(array('code' => 201, 'message' => 'Found in weekly Manifest', 'unit_cost' => number_format($total_cost, 2), 'total_cost' => number_format($unit_cost, 2), 'total_recovery' => number_format($total_recovery, 2),  'data' => $weekly_data));
            } else if (count($daily_data)) {
                $total_cost = DailyManifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->orWhere('lpn', $request->manifest_id)->orWhere('lqin', $request->manifest_id)->get()->sum('total_cost');
                $unit_cost = DailyManifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->orWhere('lpn', $request->manifest_id)->orWhere('lqin', $request->manifest_id)->get()->sum('units');
                $total_recovery = DailyManifest::where('bol', $request->manifest_id)->orWhere('package_id', $request->manifest_id)->orWhere('lpn', $request->manifest_id)->orWhere('lqin', $request->manifest_id)->get()->sum('total_recovery');

                return response()->json(array('code' => 201, 'message' => 'Found in daily Manifest - Checked', 'unit_cost' => number_format($total_cost, 2), 'total_cost' => number_format($unit_cost, 2), 'total_recovery' => number_format($total_recovery, 2), 'data' => $daily_data));
            } else if (count($bucket_data)) {
                return response()->json(array('code' => 204, 'message' => 'This bol id is a part of bucket', 'data' => $bucket_data));
            } else if (count($bucket_scanned_data)) {
                return response()->json(array('code' => 205, 'message' => 'This bol id is a part of bucket', 'data' => $bucket_scanned_data));
            } else {
                return response()->json(array('code' => 206, 'product_id' => $request->manifest_id, 'message' => 'Product Not found do you want this to unknown',));
            }
        } else {
            return response()->json(array('code' => 500, 'message' => 'Please Input Something',));
        }
    }




    // Add to pallet with bol id and pallet.
    // Used in Scanned & Add to Pallet As Well.
    public function addToPallet(Request $request, Pallets $pallet)
    {

    if($request->bol_id != null && trim($request->bol_id, ' ') != ''){
 
        $relationCheck = PalletProductRelation::where('bol_id', $request->bol_id)->get();

        if (count($relationCheck) > 0) {

            $pallet_id_of_package = $relationCheck[0]->pallet_id;
            return response()->json(['code' => 403, 'message' => 'Bol already added to pallet - > ' . $pallet_id_of_package]);
       
        } else {
            $product = ScannedProducts::where('bol', $request->bol_id)
            ->orWhere('package_id', $request->bol_id)
            ->orWhere('lqin', $request->bol_id)
            ->get();
       
        // check if product exist or not in the scanned    
        if (count($product)>0) {
           
            $relationCheck = PalletProductRelation::where('bol_id', $product[0]->bol)
            ->orWhere('bol_id',  $product[0]->package_id)
            ->orWhere('bol_id',  $product[0]->lqin)
            ->get();

        // Checking before adding if lqin, packageid and bol exist in relation    
        if (count($relationCheck)) {
            $pallet_id_of_package = $relationCheck[0]->pallet_id;
            $type_of_package = $relationCheck[0]->type;    
            return response()->json(['code' => 403, 'message' => 'Bol already added to pallet - > ' . $pallet_id_of_package . '  - With -- '. $type_of_package]);
        } else {
            // Simple Adding To Pallet
            if(PalletsAPIController::addToPalletMain($request,$pallet)){
              return response()->json(array('code' => 201, 'message' => 'Pallet updated Succesfully'));
            }else{
                return response()->json(['code' => 404, 'message' => 'Nothing Found Against ID']);
            }
    
        }

        }else{
            return response()->json(['code' => 404, 'message' => 'Product is not Scanned Yet']);
        }

        }
    }else{
        return response()->json(['code' => 404, 'message' => 'Input Something To Add To Pallet' ]);
    }
        
    }


    public function addToPalletMain(Request $request, Pallets $pallet){

            $products_query = ScannedProducts::where('bol', $request->bol_id);
            $with_package_id = ScannedProducts::where('package_id', $request->bol_id);
            $with_lqin = ScannedProducts::where('lqin', $request->bol_id);

            $scanned_products = $products_query->get();
            $scanned_products_with_package_id = $with_package_id->get();
            $scanned_products_with_lqin = $with_lqin->get();

            if (count($scanned_products)) {
               
                $total_price = 0;
                $total_units = 0;
                $total_recovery = 0;

                foreach ($scanned_products as $products) {
                    $total_price += (float) $products->total_cost;
                    $total_units += (int) $products->units;
                    $total_recovery += (float) $products->total_recovery;
                }


                foreach ($scanned_products as $scannedids) {
                    PalletProductRelation::Create([
                        'pallet_id' => $pallet->id,
                        'scanned_products_id' => $scannedids->id,
                        'bol_id' => $request->bol_id,
                        'type' => 'BOL-ID'
                    ]);
                }

                $query = DB::table('pallet')->where('id',$pallet->id);   
                $query ->increment('total_price', $total_price);
                $query ->increment('total_unit', $total_units);
                $query ->increment('total_recovery', $total_recovery);

                ScannedProducts::where('bol', $request->bol_id)->orWhere('package_id', $request->bol_id)->update(['pallet_id' => $pallet->id]);
                return true;
            } else if (count($scanned_products_with_package_id)) {
                

                $total_price = 0;
                $total_units = 0;
                $total_recovery = 0;

                foreach ($scanned_products_with_package_id as $products) {
                    $total_price += (float) $products->total_cost;
                    $total_units += (int) $products->units;
                    $total_recovery += (float) $products->total_recovery;
                }

               
                foreach ($scanned_products_with_package_id as $scannedids) {
                    PalletProductRelation::Create([
                        'pallet_id' => $pallet->id,
                        'scanned_products_id' => $scannedids->id,
                        'bol_id' => $request->bol_id,
                        'type' => 'PACKAGE-ID'
                    ]);
                }

                $query = DB::table('pallet')->where('id',$pallet->id);   
                $query ->increment('total_price', $total_price);
                $query ->increment('total_unit', $total_units);
                $query->increment('total_recovery', $total_recovery);


                ScannedProducts::where('bol', $request->bol_id)->orWhere('package_id', $request->bol_id)->update(['pallet_id' => $pallet->id]);
                return true;
            } else if (count($scanned_products_with_lqin)) {
                
                $total_price = 0;
                $total_units = 0;
                $total_recovery = 0;

                foreach ($scanned_products_with_lqin as $products) {
                    $total_price += (float) $products->total_cost;
                    $total_units += (int) $products->units;
                    $total_recovery += (float) $products->total_recovery;
                }

                foreach ($scanned_products_with_lqin as $scannedids) {
                    PalletProductRelation::Create([
                        'pallet_id' => $pallet->id,
                        'scanned_products_id' => $scannedids->id,
                        'bol_id' => $request->bol_id,
                        'type' => 'LQIN-ID'
                    ]);
                }
                $query = DB::table('pallet')->where('id',$pallet->id);   
                $query ->increment('total_price', $total_price);
                $query ->increment('total_unit', $total_units);
                $query->increment('total_recovery', $total_recovery);


                ScannedProducts::where('bol', $request->bol_id)->orWhere('package_id', $request->bol_id)->update(['pallet_id' => $pallet->id]);
                return true;
            } else{
                return false;
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

        if($request->bol_id != null && trim($request->bol_id, ' ') != '' && $request->bol_id!='DROPSHIP_BIN'){
    
            $scanned_data = ScannedProducts::where('bol', $request->bol_id)->orWhere('package_id', $request->bol_id)->orWhere('lqin', $request->bol_id)->get()->toArray();
            if (count($scanned_data)>0) {
                return response()->json(array('message' => 'Product Already Had been Received', 'code' => 402));
            }
            else{
                $with_package_id = Manifest::where('package_id', $request->bol_id)->get();
                $with_bol_id = Manifest::where('bol', $request->bol_id)->get();
                $with_lqin = Manifest::where('lqin', $request->bol_id)->get();
        
                $daily_with_package_id = DailyManifest::where('package_id', $request->bol_id)->get();
                $daily_with_bol_id = DailyManifest::where('bol', $request->bol_id)->get();
                $daily_with_lqin = DailyManifest::where('lqin', $request->bol_id)->get();
        
        
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
                                'removal_reason' => $item->removal_reason,
                                'lqin' => $item->lqin,
                                'file_name' => $item->filename
                            ]);
                        }
                        $item->delete();
                    }
        
                    if ($request->has('pallet_id')) {
                        return $this->addToPallet($request, Pallets::find($request->pallet_id));
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
                                'removal_reason' => $item->removal_reason,
                                'lqin' => $item->lqin,
                                'file_name' => $item->filename
                            ]);
                        }
                        $item->delete();
                    }
                    if ($request->has('pallet_id')) {
                        return $this->addToPallet($request, Pallets::find($request->pallet_id));
                    }
                } else if (count($with_lqin)) {
                    foreach ($with_lqin as $item) {
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
                                'removal_reason' => $item->removal_reason,
                                'lqin' => $item->lqin,
                                'file_name' => $item->filename
                            ]);
                        }
                        $item->delete();
                    }
                    if ($request->has('pallet_id')) {
                        return $this->addToPallet($request, Pallets::find($request->pallet_id));
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
                                'removal_reason' => $item->removal_reason,
                                'lqin' => $item->lqin,
                                'file_name' => $item->filename
                            ]);
                        }
                        $item->delete();
                    }
                    if ($request->has('pallet_id')) {
                        return  $this->addToPallet($request, Pallets::find($request->pallet_id));
                    }
                } else if (count($daily_with_lqin)) {
                    foreach ($daily_with_lqin as $item) {
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
                                'removal_reason' => $item->removal_reason,
                                'lqin' => $item->lqin,
                                'file_name' => $item->filename
                            ]);
                        }
                        $item->delete();
                    }
                    if ($request->has('pallet_id')) {
                        return $this->addToPallet($request, Pallets::find($request->pallet_id));
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
                                'removal_reason' => $item->removal_reason,
                                'lqin' => $item->lqin,
                                'file_name' => $item->filename
                            ]);
                        }
                        $item->delete();
                    }
                    if ($request->has('pallet_id')) {
                       return $this->addToPallet($request, Pallets::find($request->pallet_id));
                    }
                }
        
                return response()->json(array('message' => 'Product Recived,- Not Added in AnyPallet', 'code' => 201));
           
            }
       
        }else{
            return response()->json(array('message' => 'Input Something To Search', 'code' => 404));

        }
       
       
      

    }





        

   
   
   
   
        public function removePallets(Request $request)
    {


        $bol_ids_data = PalletProductRelation::select('scanned_products_id')->where('pallet_id', $request->id)
        ->where(function($q) use($request){ 
            $q->Where('bol_id', $request->package_id )
              ->orWhere('bol_id', $request->bol_id);
        });

        $data_to_remove = $bol_ids_data->get();

        if(count($data_to_remove)){
            $dataCalculate= ScannedProducts::whereIn('id', $data_to_remove)->get();
            $total_price = 0;
            $total_units = 0;
            $total_recovery = 0;
    
            foreach ($dataCalculate as $product) {
                    $total_price += (float) $product->total_cost;
                    $total_units += (int) $product->units;
                    $total_recovery += (float) $product->total_recovery;
    
                }
        
        
            DB::table('pallet')->decrement('total_price', $total_price);
            DB::table('pallet')->decrement('total_unit', $total_units);
            DB::table('pallet')->decrement('total_recovery', $total_recovery);
            $bol_ids_data->delete();
            return response()->json(array('code' => '201', 'message' => 'Pallet removed from the list'));
      
        }else{
            return response()->json(array('code' => '203', 'message' => 'Unable to remove'));
        }

         }


    public function findBolFromLpn(Request $request)
    {
        try {
            $data = LPN::where('lpn', $request->lpn)->get(['bol', 'package_id']);
            if (count($data) > 0) {
                return response()->json(array('message' => 'Data found against LPN', 'data' => $data,  'code' => '201'));
            } else {
                return response()->json(array('message' => 'No LPN Found', 'data' => [],  'code' => '404'));
            }
        } catch (\Throwable $th) {
            return response()->json(array('message' => 'Not found', 'code' => '404'));
        }
    }
}
