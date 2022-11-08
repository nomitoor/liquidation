<?php

namespace App\Http\Controllers;
use DB;

use App\Models\Category;
use App\Models\ClaimList;
use App\Models\Pallets;
use App\Models\PalletProductRelation;
use Redirect;
use App\Models\ScannedProducts;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PalletsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pallets = Pallets::with('category')->orderBy('id', 'DESC')->paginate(10);

        /* foreach ($pallets as $key => $pallet) {
        //     // $rec = 0;
        //     // try {
        //     //     $all_bol_ids = Pallets::where('id', $pallet->id)->get(['bol_ids']);
        //     //     if( $all_bol_ids[0]->bol_ids != null){
        //     //         $bol_ids = unserialize($all_bol_ids[0]->bol_ids);
        //     //         $recovery = ScannedProducts::whereIn('bol', $bol_ids)->orWhereIn('package_id',$bol_ids )->orWhereIn('lqin',$bol_ids )->get();
        //     //         foreach ($recovery as $recov) {
        //     //             $rec += $recov->total_recovery;
        //     //         }
        //     //     }
            
        //     // } catch (Throwable $e) {
        //     //     $rec = 0;

        //     //     //sdfsd
        //     // }
        //     // $pallets[$key]['recovery'] = $rec;
        //     // //$recovery = ScannedProducts::where('pallet_id', $pallet->id)->get();
            
         } */
        $breadcrumbs = [
            ['link' => "pallets", 'name' => "Pallets"], ['name' => "Available Pallets"]
        ];

        return view('pallets/pallets', [
            'breadcrumbs' => $breadcrumbs,
            'pallets' => $pallets,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $products = ScannedProducts::select('bol')->where('pallet_id', null)->groupBy('bol')->get();
        $categories = Category::orderBy('title', 'ASC')->get(['id', 'title']);

        $breadcrumbs = [
            ['link' => "pallets", 'name' => "Pallets"], ['name' => "Create"]
        ];

        return view('pallets/create', ['breadcrumbs' => $breadcrumbs, 'products' => $products, 'categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pallet = Pallets::create([
            'description' => $request->description,
            'category_id' => $request->category_id,
            'total_price' => 0,
            'total_unit' => 0,
            'total_recovery' => 0,
            'pallet_image' => 'default-pallet.png'
        ]);

        return redirect('pallets/' . $pallet->id . '/edit');

        // $total_price = 0;
        // $total_units = 0;
        // $total_recovery = 0;

        // foreach ($request->bol as $bol) {
        //     $scanned_products = ScannedProducts::where('bol', $bol)->get();

        //     foreach ($scanned_products as $products) {
        //         $total_price += (float) $products->total_cost;
        //         $total_units += (int) $products->units;
        //     }

        //     ScannedProducts::where('bol', $bol)->orWhere('package_id', $request->bol_id)->update(['pallet_id' => $request->id]);
        // }

        // Pallets::create([
        //     'bol_ids' => json_encode($request->bol),
        //     'total_price' => $total_price,
        //     'total_unit' => $total_units
        // ]);

        // return redirect('/pallets');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Pallets $pallet)
    {
        $bol_ids_data = PalletProductRelation::select('scanned_products_id')->where('pallet_id', $pallet->id)->get();

        if (count($bol_ids_data)) {

            $bol_ids = $bol_ids_data->toArray();
            $pallet_products = [];

            foreach ($bol_ids as $product_unique_id) {
                $prd = ScannedProducts::where('id', $product_unique_id)->first();
                array_push($pallet_products, $prd);
            }

            $breadcrumbs = [
                ['link' => "pallets", 'name' => "Pallets"], ['name' => "View"]
            ];

            return view('pallets/products', [
                'breadcrumbs' => $breadcrumbs,
                'products' => $pallet_products,
                'invoice_number' => $pallet->id,
                'date_issued' => Carbon::parse($pallet->created_at)->format('d-M-Y'),
                'total_price' => $pallet->total_price,
                'total_units' => $pallet->total_unit
            ]);
        } else {
            return Redirect::back()->withErrors(['error' => 'No products added to this pallet']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Pallets $pallet)
    {
        $breadcrumbs = [
            ['link' => "pallets", 'name' => "Pallets"], ['name' => "Create"]
        ];

        $bol_ids_data = PalletProductRelation::select('scanned_products_id')->where('pallet_id', $pallet->id)->get();
        $scanned_products = [];

        if (count($bol_ids_data)) {

            $bol_ids = $bol_ids_data->toArray();

            foreach ($bol_ids as $product_unique_id) {
                $prd = ScannedProducts::where('id', $product_unique_id)->get(['id', 'bol', 'package_id', 'item_description', 'units', 'unit_cost', 'total_cost']);
                array_push($scanned_products, $prd[0]);
            }
        }



        // $scanned_products = ScannedProducts::whereIn('bol', unserialize($pallet->bol_ids) ?: [])->orWhereIn('package_id', unserialize($pallet->bol_ids) ?: [])
        // ->get(['id', 'bol', 'package_id', 'item_description', 'units', 'unit_cost', 'total_cost']);

        // $total_price = 0;
        // $data = unserialize($pallet->bol_ids);
        // if ($data != null && count($data) > 0) {
        //     if (($key = array_search(end($data), $data)) !== false) {
        //         $products = ScannedProducts::where('bol', $data[$key])->get();
        //         foreach ($products as $product) {
        //             $total_price += $product->total_cost;
        //         }
        //     }
        // }


        return view('pallets/edit', ['breadcrumbs' => $breadcrumbs, 'pallets' => $pallet, 'last_total_cost' => 'NAN', 'scanned_products' => $scanned_products]);
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pallets $pallet)
    {

        if ($request->bol_id != null && trim($request->bol_id, ' ') != '' && $request->bol_id!='DROPSHIP_BIN') {

            // if already added into pallet then simply give error to users;
            $relationCheck = PalletProductRelation::where('bol_id', $request->bol_id)->get();

            if (count($relationCheck) > 0) {
                $pallet_id_of_package = $relationCheck[0]->pallet_id;
                return \Redirect::back()->withErrors(['error' => '^^^^^^^^   - Bol already added to pallet -> DE' . sprintf("%05d", $pallet_id_of_package)]);
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
                    return \Redirect::back()->withErrors(['error' => 'Already added to pallet -> DE' . sprintf("%05d", $pallet_id_of_package) . '   --  with -- >' . $type_of_package]);
                } else {
                    // Simple Adding To Pallet
                    if(PalletsController::addtoPalletMainF($request,$pallet)){
                      return \Redirect::back()->withErrors(['error' => 'Pallet updated Succesfully']);
                    }else{
                        return  \Redirect::back()->withErrors(['error' => 'Nothing Found Against Searched ID']);
                    }
            
                }  } else {
                    return \Redirect::back()->withErrors(['error' => 'Product is not available in Scan']);
                }
                
            }
        } else {
            return \Redirect::back()->withErrors(['error' => 'Input Something to Search']);
        }
    }


    public function addtoPalletMainF(Request $request,Pallets $pallet){

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
                        $query->increment('total_recovery', $total_recovery);
        
        
            
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
                        $query ->increment('total_recovery', $total_recovery);
        
        
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
                    } else {
                        return false;
                    }
    }


   


    public function unknown()
    {
        $unknown = ScannedProducts::where('unknown_list', 'yes')->get();
        $breadcrumbs = [
            ['link' => "pallets", 'name' => "Pallets"], ['name' => "Index"]
        ];

        return view('different/unknown', [
            'breadcrumbs' => $breadcrumbs,
            // 'unknown' => $unknown
        ]);
    }

    public function claims()
    {
        $claims = ClaimList::get();
        $breadcrumbs = [
            ['link' => "pallets", 'name' => "Pallets"], ['name' => "Index"]
        ];

        return view('different/claims', [
            'breadcrumbs' => $breadcrumbs,
            'unknown' => $claims
        ]);
    }

    

    public function updatePalletDescription(Request $request)
    {
        $foundPallet = Pallets::where('id', $request->pallet_id)->update(['description' => $request->description]);

        return redirect('pallets/' . $request->pallet_id . '/edit');
    }


  
}
