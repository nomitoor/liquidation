<?php

namespace App\Http\Controllers;

use App\Models\Manifest;
use App\Models\ManifestRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ManifestImport;

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
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Index"]
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

            $breadcrumbs = [
                ['link' => "manifest", 'name' => "Manifest"], ['name' => "Index"]
            ];
            $manifest = Manifest::all();
            return view('manifest/manifest', ['breadcrumbs' => $breadcrumbs, 'manifest' => $manifest]);
        }
        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Index"]
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
        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Index"]
        ];

        return view('manifest/import-products', ['breadcrumbs' => $breadcrumbs]);
    }

    public function getFoundProducts(Request $request)
    {
        return response()->json(array('data' => Manifest::where('package_id', $request->package_id)->get()));
    }
}
