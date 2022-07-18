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
            ['link' => "pallets", 'name' => "Pallets"], ['name' => "Index"]
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
            ['link' => "pallets", 'name' => "Pallets"], ['name' => "Create"]
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
        $pallet = Pallets::create([
            'pallets_id' => $request->pallet_name,
        ]);

        return redirect('pallets/' . $pallet->id . '/edit');

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
            ['link' => "pallets", 'name' => "Pallets"], ['name' => "View"]
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
    public function edit(Pallets $pallet)
    {
        $breadcrumbs = [
            ['link' => "pallets", 'name' => "Pallets"], ['name' => "Create"]
        ];

        return view('pallets/edit', ['breadcrumbs' => $breadcrumbs, 'pallets' => $pallet]);
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
        $scanned_products = ScannedProducts::where('bol', $request->bol_id)->get();

        if (count($scanned_products)) {
            $bol_id_array = unserialize($pallet->bol_ids);
            if ($bol_id_array) {
                foreach ($bol_id_array as $ids) {
                    if ($ids == $request->bol_id) {
                        return response()->json(array('message' => 'Bol already added to this pallet', 'code' => '403'));
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

            ScannedProducts::where('bol', $request->bol_id)->update(['pallet_id' => $pallet->id]);

            $pallet->update([
                'bol_ids' => serialize($bol_id_array),
                'total_price' => $new_total_price,
                'total_unit' => $new_total_units,
            ]);

            return response()->json(array(
                'data' => unserialize($pallet->bol_ids),
                'code' => '201'
            ));
        } else {
            return response()->json(array('message' => 'Bol id not found', 'code' => '403'));
        }
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
