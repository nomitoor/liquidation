<?php

namespace App\Http\Controllers;

use App\Models\Pallets;
use App\Models\PalletsProducts;
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
        $pallets = Pallets::get();

        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Upload Manfiest"]
        ];

        return view('pallets/pallets', [
            'breadcrumbs' => $breadcrumbs,
            'pallets' => $pallets
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

        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Index"]
        ];

        return view('pallets/create', ['breadcrumbs' => $breadcrumbs, 'products' => $products]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $total_price = 0;
        $total_units = 0;

        foreach ($request->bol as $bol) {
            $scanned_products = ScannedProducts::where('bol', $bol)->get();
            
            foreach ($scanned_products as $products) {
                $total_price += (float) $products->total_cost;
                $total_units += (int) $products->units;                
            }

            ScannedProducts::where('bol', $bol)->update(['pallet_id' => $request->pallet_name]);
        }

        Pallets::create([
            'pallets_id' => $request->pallet_name,
            'bol_ids' => json_encode($request->bol),
            'total_price' => $total_price,
            'total_unit' => $total_units
        ]);

        return redirect('/pallets');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Pallets $pallet)
    {
        $bol_id = explode(',', $pallet->bol_ids);
        $bol_ids = str_replace('"', "", str_replace('[', "", str_replace(']', "", $bol_id)));

        $pallet_products = [];

        foreach ($bol_ids as $bol_id) {
            $prd = ScannedProducts::where('bol', $bol_id)->get();
            foreach ($prd as $pd) {
                array_push($pallet_products, $pd);
            }
        }

        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Index"]
        ];

        return view('pallets/products', [
            'breadcrumbs' => $breadcrumbs,
            'products' => $pallet_products,
            'invoice_number' => $pallet->pallets_id,
            'date_issued' => Carbon::parse($pallet->created_at)->format('d-M-Y'),
            'total_price' => $pallet->total_price,
            'total_units' => $pallet->total_unit
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
