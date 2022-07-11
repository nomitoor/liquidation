<?php

namespace App\Http\Controllers;

use App\Models\Manifest;
use App\Models\ManifestRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ManifestImport;
use App\Models\ScannedProducts;

class ManifestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $manifest = Manifest::all();

        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Upload Manfiest"]
        ];

        return view('manifest/manifest', ['breadcrumbs' => $breadcrumbs, 'manifest' => $manifest]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Index"]
        ];

        return view('manifest/create', ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->has('manifestfile')) {
            $file = $request->file('manifestfile');
            $filename = $file->getClientOriginalName();
            $location = 'uploads';

            $file->move(public_path($location), $file->getClientOriginalName());

            $filepath = public_path($location . "/" . $filename);

            Excel::import(new ManifestImport, $filepath);

            ManifestRecord::create([
                'file_name' => $filename,
                'number_of_entities' => '1234567', // TODO: Work on count of the records
                'uploaded_by' => auth()->user()->id
            ]);

            $breadcrumbs = [
                ['link' => "manifest", 'name' => "Manifest"], ['name' => "Upload Manfiest"]
            ];
            $manifest = Manifest::all();
            return view('manifest/manifest', ['breadcrumbs' => $breadcrumbs, 'manifest' => $manifest]);
        }
        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Upload Manfiest"]
        ];
        $manifest = Manifest::all();
        return view('manifest/manifest', ['breadcrumbs' => $breadcrumbs, 'manifest' => $manifest]);
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

    public function getAll()
    {
        return response()->json(array('data' => Manifest::all()));
    }

    public function codeScanner()
    {
        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Index"]
        ];

        return view('manifest/scanner', ['breadcrumbs' => $breadcrumbs]);
    }

    public function getManifest(Request $request)
    {
        $with_package_id = Manifest::where('package_id', $request->id)->get();
        $with_bol_id = Manifest::where('bol', $request->id)->get();

        if (count($with_package_id)) {
            return response()->json(array('message' => 'Found with Package ID', 'data' => $with_package_id, 'code' => '201'));
        } else if (count($with_bol_id)) {
            return response()->json(array('message' => 'Found with Bol ID', 'data' => $with_bol_id, 'code' => '201'));
        } else {
            return response()->json(array('message' => 'not found', 'code' => '404'));
        }
    }

    public function importToScannedProducts(Request $request)
    {
        $with_package_id = Manifest::where('package_id', $request->id)->get();
        $with_bol_id = Manifest::where('bol', $request->id)->get();

        if (count($with_package_id)) {
            foreach ($with_package_id as $item) {
                ScannedProducts::create([
                    'bol' => $item->bol,
                    'package_id' => $item->package_id,
                    'item_description' => $item->item_description,
                    'units' => $item->units,
                    'unit_cost' => $item->unit_cost,
                    'total_cost' => $item->total_cost,
                ]);
                $item->delete();
            }
        } else {
            foreach ($with_bol_id as $item) {
                $current = ScannedProducts::create([
                    'bol' => $item->bol,
                    'package_id' => $item->package_id,
                    'item_description' => $item->item_description,
                    'units' => $item->units,
                    'unit_cost' => $item->unit_cost,
                    'total_cost' => $item->total_cost,
                ]);
                $item->delete();
            }
        }

        return response()->json(array('message' => 'Manifest producsts updated', 'code' => '201'));
    }
}
