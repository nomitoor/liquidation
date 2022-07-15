<?php

namespace App\Http\Controllers;

use App\Models\Pallets;
use App\Models\PalletsProducts;
use App\Models\ScannedProducts;
use Illuminate\Http\Request;

class PalletsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $manifest = Manifest::all();

        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Upload Manfiest"]
        ];

        return view('pallets/pallets', [
            'breadcrumbs' => $breadcrumbs,
            // 'manifest' => $manifest
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $products = ScannedProducts::select('bol')->groupBy('bol')->get();

        $added_pallelts = PalletsProducts::select('bol_id')->get();

        dd($added_pallelts);

        foreach ($added_pallelts as $added) {
            $check = $products->contains($added->bol);
            if($check){
                $products->forget($added->bol);
            }
        }

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
        $pallet_id = uniqid();
        
        Pallets::create([
            'pallets_id' => $pallet_id,
        ]);

        foreach ($request->bol as $bol) {
            PalletsProducts::create([
                'pallets' => $pallet_id,
                'bol_id' => $bol
            ]);
        }

        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Upload Manfiest"]
        ];

        return view('pallets/pallets', [
            'breadcrumbs' => $breadcrumbs,
            // 'manifest' => $manifest
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
