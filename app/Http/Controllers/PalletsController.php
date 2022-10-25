<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ClaimList;
use App\Models\Pallets;
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
        
        foreach ($pallets as $key => $pallet) {
            $rec = 0;
            //$recovery = ScannedProducts::where('pallet_id', $pallet->id)->get();
            $all_bol_ids = Pallets::where('id', $pallet->id)->get(['bol_ids']);
            $bol_ids = unserialize($all_bol_ids[0]->bol_ids);
            $recovery = ScannedProducts::whereIn('bol', $bol_ids)->orWhereIn('package_id',$bol_ids )->orWhereIn('lqin',$bol_ids )->get();
            foreach ($recovery as $recov) {
                $rec += $recov->total_recovery;
            }
            $pallets[$key]['recovery'] = $rec;
        }
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
            'category_id' => $request->category_id
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

            ScannedProducts::where('bol', $bol)->orWhere('package_id', $request->bol_id)->update(['pallet_id' => $request->id]);
        }

        Pallets::create([
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
        $bol_ids = unserialize($pallet->bol_ids);

        if ($bol_ids) {
            $pallet_products = [];

            foreach ($bol_ids as $bol_id) {
                $prd = ScannedProducts::where('bol', $bol_id)->orWhere('package_id', $bol_id)->orWhere('lqin', $bol_id)->get();
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
                'invoice_number' => $pallet->id,
                'date_issued' => Carbon::parse($pallet->created_at)->format('d-M-Y'),
                'total_price' => $pallet->total_price,
                'total_units' => $pallet->total_unit
            ]);
        }

        return Redirect::back()->withErrors(['error' => 'No products added to this pallet']);
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

        $scanned_products = ScannedProducts::whereIn('bol', unserialize($pallet->bol_ids) ?: [])->orWhereIn('package_id', unserialize($pallet->bol_ids) ?: [])->get(['id', 'bol', 'package_id', 'item_description', 'units', 'unit_cost', 'total_cost']);

        $total_price = 0;
        $data = unserialize($pallet->bol_ids);
        if ($data != null && count($data) > 0) {
            if (($key = array_search(end($data), $data)) !== false) {
                $products = ScannedProducts::where('bol', $data[$key])->get();
                foreach ($products as $product) {
                    $total_price += $product->total_cost;
                }
            }
        }


        return view('pallets/edit', ['breadcrumbs' => $breadcrumbs, 'pallets' => $pallet, 'last_total_cost' => $total_price, 'scanned_products' => $scanned_products]);
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
        $products_query = ScannedProducts::where('bol', $request->bol_id)->whereNull('pallet_id');
        $with_package_id = ScannedProducts::where('package_id', $request->bol_id)->whereNull('pallet_id');

        $scanned_products = $products_query->get();
        $scanned_products_with_package_id = $with_package_id->get();

        if (count($scanned_products)) {
            $bol_id_array = unserialize($pallet->bol_ids);
            if ($bol_id_array) {
                foreach ($bol_id_array as $ids) {
                    if ($ids == $request->bol_id) {
                        return \Redirect::back()->withErrors(['error' => 'Bol already added to this pallet']);
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

            $updated_scanned_products = ScannedProducts::whereIn('bol', $bol_id_array)->get(['id', 'bol', 'package_id', 'item_description', 'units', 'unit_cost', 'total_cost']);

            $pallet->update([
                'bol_ids' => serialize($bol_id_array),
                'total_price' => $new_total_price,
                'total_unit' => $new_total_units,
            ]);

            return \Redirect::back()->withErrors(['error' => 'Pallet updated Succesfully']);
        } else if (count($scanned_products_with_package_id)) {
            $bol_id_array = unserialize($pallet->bol_ids);
            if ($bol_id_array) {
                foreach ($bol_id_array as $ids) {
                    if ($ids == $request->bol_id) {
                        return \Redirect::back()->withErrors(['error' => 'Bol already added to this pallet']);
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

            $updated_scanned_products = ScannedProducts::whereIn('package_id', $bol_id_array)->get(['id', 'bol', 'package_id', 'item_description', 'units', 'unit_cost', 'total_cost']);

            $pallet->update([
                'bol_ids' => serialize($bol_id_array),
                'total_price' => $new_total_price,
                'total_unit' => $new_total_units,
            ]);

            return \Redirect::back()->withErrors(['error' => 'Pallet updated Succesfully']);
        } else {

            $products_query = ScannedProducts::where('bol', $request->bol_id)->where('pallet_id', '<>', NULL)->first();
            $with_package_id = ScannedProducts::where('package_id', $request->bol_id)->where('pallet_id', '<>', NULL)->first();

            if (!is_null($products_query)) {
                $pallet_details = Pallets::where('id', $products_query->pallet_id)->first();

                return \Redirect::back()->withErrors(['error' => 'This BOL ID is already part of PALLET: ' . $pallet_details->description . ' with PALLET ID: DE' . sprintf("%05d", $pallet_details->id)]);
            } else {
                $pallet_details = Pallets::where('id', $with_package_id->pallet_id)->first();
                return \Redirect::back()->withErrors(['error' => 'This PACKAGE ID is already part of PALLET: ' . $pallet_details->description . ' with PALLET ID: DE' . sprintf("%05d", $pallet_details->id)]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deletePalletsWithBol(Request $request)
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

        return response()->json(array('code' => '201', 'message' => 'done'));
    }

    public function undoPallets(Request $request)
    {
        $pallet = Pallets::where('id', $request->id)->first();
        $data = unserialize($pallet->bol_ids);

        if (($key = array_search(end($data), $data)) !== false) {
            ScannedProducts::where('bol', $data[$key])->orWhere('package_id', $data[$key])->update(['pallet_id' => NULL]);
            unset($data[$key]);
        }


        $total_price = 0;
        $total_units = 0;
        foreach ($data as $value) {
            $products = ScannedProducts::where('bol', $value)->get(['units', 'unit_cost', 'total_cost']);

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

        return response()->json(array('code' => '201', 'message' => 'done'));
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

    public function destroy($id)
    {
        $pallet = Pallets::where('id', $id)->first();
        ScannedProducts::where('pallet_id', $id)->update(['pallet_id' => NULL]);

        if ($pallet->bol_ids) {
            $data = unserialize($pallet->bol_ids);

            $total_price = 0;
            $total_units = 0;
            foreach ($data as $value) {
                $products = ScannedProducts::where('bol', $value)->get(['units', 'unit_cost', 'total_cost']);

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
        }

        Pallets::findorFail($id)->update(['category_id' => null]);
        $pallet->container()->detach();
        $pallet->delete();
        return redirect('/pallets');
    }

    public function updatePalletDescription(Request $request)
    {
        $foundPallet = Pallets::where('id', $request->pallet_id)->update(['description' => $request->description]);

        return redirect('pallets/' . $request->pallet_id . '/edit');
    }
}
