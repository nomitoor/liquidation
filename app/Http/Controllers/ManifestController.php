<?php

namespace App\Http\Controllers;

use App\Exports\ScannedProductsClientExport;
use App\Exports\ScannedProductsExport;
use App\Imports\ManifestCompareImport;
use App\Imports\DailyManifestImport;
use App\Models\Manifest;
use App\Models\ManifestRecord;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ManifestImport;
use App\Models\ClaimList;
use App\Models\DailyManifest;
use App\Models\ScannedProducts;
use App\Models\DailyManifestRecord;
use App\Models\ManifestCompare;
use App\Models\Pallets;
use Illuminate\Http\Response;

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

    public function dailyManifest()
    {
        $manifest = Manifest::all();

        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Index"]
        ];

        return view('manifest/daily-manifest', ['breadcrumbs' => $breadcrumbs, 'manifest' => $manifest]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Create"]
        ];

        return view('manifest/create', ['breadcrumbs' => $breadcrumbs]);
    }

    public function dailyCreate()
    {
        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Create"]
        ];

        return view('manifest/daily-create', ['breadcrumbs' => $breadcrumbs]);
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

            Excel::import(new ManifestImport($filename), $filepath);

            ManifestRecord::create([
                'file_name' => $filename,
                'number_of_entities' => '1234567', // TODO: Work on count of the records
                'uploaded_by' => auth()->user()->id
            ]);

            if (\File::exists($filepath)) {
                unlink($filepath);
            }
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

    public function storeDaily(Request $request)
    {
        if ($request->has('manifestfile')) {
            $file = $request->file('manifestfile');
            $filename = $file->getClientOriginalName();
            $location = 'uploads';

            $file->move(public_path($location), $file->getClientOriginalName());

            $filepath = public_path($location . "/" . $filename);
            DailyManifestRecord::create([
                'file_name' => $filename,
                'number_of_entities' => '1234567', // TODO: Work on count of the records
                'uploaded_by' => auth()->user()->id
            ]);
            Excel::import(new DailyManifestImport($filename), $filepath);

            $breadcrumbs = [
                ['link' => "manifest", 'name' => "Manifest"], ['name' => "Upload Manfiest"]
            ];
            $manifest = Manifest::all();
            return view('manifest/daily-manifest', ['breadcrumbs' => $breadcrumbs, 'manifest' => $manifest]);
        }
        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Upload Manfiest"]
        ];
        $manifest = DailyManifest::all();
        return view('manifest/daily-manifest', ['breadcrumbs' => $breadcrumbs, 'manifest' => $manifest]);
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
            ['link' => "view-scanned-products", 'name' => "Products"], ['name' => "Scan Products"]
        ];

        return view('manifest/scanner', ['breadcrumbs' => $breadcrumbs]);
    }

    public function getManifest(Request $request)
    {
        $with_package_id = Manifest::where('package_id', $request->id)->get();
        $with_bol_id = Manifest::where('bol', $request->id)->get();
        $dropshipbin = Manifest::whereRaw("find_in_set('$request->id',bol)")->where('package_id', 'DROPSHIP_BIN')->get();
        $with_package_id_unknown = Manifest::whereRaw("find_in_set('$request->id',bol)")->where('package_id', '<>', 'DROPSHIP_BIN')->where('bol_ids', null)->get();
        $with_bol_id_unknown = Manifest::whereRaw("find_in_set('$request->id',bol)")->where('package_id', '<>', 'DROPSHIP_BIN')->where('bol_ids', null)->get();
        $dropshipbin_bucket = Manifest::whereRaw("find_in_set('$request->id',bol)")->where('bol_ids', '<>', null)->get();

        $daily_with_package_id = DailyManifest::where('package_id', $request->id)->get();
        $daily_with_bol_id = DailyManifest::where('bol', $request->id)->get();
        $daily_dropshipbin = DailyManifest::whereRaw("find_in_set('$request->id',bol)")->where('package_id', 'DROPSHIP_BIN')->get();
        $daily_with_package_id_unknown = DailyManifest::whereRaw("find_in_set('$request->id',bol)")->where('package_id', '<>', 'DROPSHIP_BIN')->where('bol_ids', null)->get();
        $daily_with_bol_id_unknown = DailyManifest::whereRaw("find_in_set('$request->id',bol)")->where('package_id', '<>', 'DROPSHIP_BIN')->where('bol_ids', null)->get();
        $daily_dropshipbin_bucket = DailyManifest::whereRaw("find_in_set('$request->id',bol)")->where('bol_ids', '<>', null)->get();

        if (count($with_package_id)) {
            return response()->json(array('message' => 'Found with Package ID', 'data' => $with_package_id, 'code' => '201'));
        } else if (count($with_bol_id)) {
            return response()->json(array('message' => 'Found with Bol ID', 'data' => $with_bol_id, 'code' => '201'));
        } else if (count($with_package_id_unknown)) {
            return response()->json(array('message' => 'Found with Package ID', 'code' => '215', 'package_id' => $with_package_id_unknown[0]->package_id));
        } else if (count($with_bol_id_unknown)) {
            return response()->json(array('message' => 'Found with Package ID', 'code' => '215', 'package_id' => $with_bol_id_unknown[0]->package_id));
        } else if (count($dropshipbin)) {
            return response()->json(array('message' => 'Found with Bol ID', 'data' => $dropshipbin, 'code' => '201'));
        } else if (count($dropshipbin_bucket)) {
            return response()->json(array('message' => 'Found with Bol ID', 'data' => $dropshipbin_bucket, 'code' => '201'));
        } else if (count($daily_with_package_id)) {
            return response()->json(array('message' => 'Found with Package ID', 'data' => $daily_with_package_id, 'code' => '201'));
        } else if (count($daily_with_bol_id)) {
            return response()->json(array('message' => 'Found with Bol ID', 'data' => $daily_with_bol_id, 'code' => '201'));
        } else if (count($daily_dropshipbin)) {
            return response()->json(array('message' => 'Found with Bol ID', 'data' => $daily_dropshipbin, 'code' => '201'));
        } else if (count($daily_with_package_id_unknown)) {
            return response()->json(array('message' => 'Found with Package ID', 'code' => '215', 'package_id' => $daily_with_package_id_unknown[0]->package_id));
        } else if (count($daily_with_bol_id_unknown)) {
            return response()->json(array('message' => 'Found with Package ID', 'code' => '215', 'package_id' => $daily_with_bol_id_unknown[0]->package_id));
        } else if (count($daily_dropshipbin_bucket)) {
            return response()->json(array('message' => 'Found with Bol ID', 'data' => $daily_dropshipbin_bucket, 'code' => '201'));
        } else {
            $with_package_id = ScannedProducts::where('package_id', $request->id)->get();
            $with_bol_id = ScannedProducts::where('bol', $request->id)->get();

            if (count($with_package_id)) {
                return response()->json(array('message' => 'Found in scanned productss', 'code' => '304'));
            } else if (count($with_bol_id)) {
                return response()->json(array('message' => 'Found in scanned productss', 'code' => '304'));
            } else {
                return response()->json(array('message' => 'not found', 'code' => '404'));
            }

            return response()->json(array('message' => 'not found', 'code' => '404'));
        }
    }

    public function importToScannedProducts(Request $request)
    {
        if ($request->unknown) {
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

        $with_package_id = Manifest::where('package_id', $request->id)->get();
        $with_bol_id = Manifest::where('bol', $request->id)->get();
        $dropshipbin = Manifest::whereRaw("find_in_set('$request->id',bol)")->where('package_id', 'DROPSHIP_BIN')->get();
        $dropshipbin_bucket = Manifest::whereRaw("find_in_set('$request->id',bol)")->where('bol_ids', '<>', null)->get();

        $daily_with_package_id = DailyManifest::where('package_id', $request->id)->get();
        $daily_with_bol_id = DailyManifest::where('bol', $request->id)->get();
        $daily_dropshipbin = DailyManifest::whereRaw("find_in_set('$request->id',bol)")->where('package_id', 'DROPSHIP_BIN')->get();
        $daily_dropshipbin_bucket = DailyManifest::whereRaw("find_in_set('$request->id',bol)")->where('bol_ids', '<>', null)->get();


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
                        'removal_reason' => $item->removal_reason
                    ]);
                }
                $item->delete();
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
                        'removal_reason' => $item->removal_reason
                    ]);
                }
                $item->delete();
            }
        } else if (count($dropshipbin)) {
            $id_array = [];
            $string = '';
            foreach ($dropshipbin as $bin) {
                array_push($id_array, $bin);

                $exploded_bin = explode(',', $bin->bol);
                while (($i = array_search($request->id, $exploded_bin)) !== false) {
                    unset($exploded_bin[$i]);
                }

                $bol_ids = $bin->bol_ids;
                if ($bol_ids == null) {
                    $string = serialize($request->id);
                } else {
                    $ids = unserialize($bol_ids);
                    $array = explode(',', $ids);
                    if (in_array($request->id, $array)) {
                        $string = $bin->bol_ids;
                        return response()->json(array('message' => 'Id already scanned', 'code' => '707'));
                    } else {
                        $string = serialize($ids . ',' . $request->id);
                    }
                }
            }

            $package_id = 'Bucket#' . uniqid();
            Manifest::whereRaw("find_in_set('$request->id',bol)")->where('package_id', 'DROPSHIP_BIN')->update([
                'bol_ids' => $string,
                'package_id' => $package_id,
            ]);

            return response()->json(array('message' => 'Manifest products updated', 'code' => '909', 'package_id' => $package_id));
        } else if (count($dropshipbin_bucket)) {
            $id_array = [];
            $string = '';
            $package_id = '';
            foreach ($dropshipbin_bucket as $bin_bucket) {
                array_push($id_array, $bin_bucket);

                $exploded_bin = explode(',', $bin_bucket->bol);
                while (($i = array_search($request->id, $exploded_bin)) !== false) {
                    unset($exploded_bin[$i]);
                }
                $bol_ids = $bin_bucket->bol_ids;

                if ($bol_ids == null) {
                    $string = serialize($request->id);
                } else {
                    $ids = unserialize($bol_ids);
                    $array = explode(',', $ids);
                    if (in_array($request->id, $array)) {
                        $string = $bin_bucket->bol_ids;
                        return response()->json(array('message' => 'Id already scanned', 'code' => '707'));
                    } else {
                        $string = serialize($ids . ',' . $request->id);
                    }
                }

                $package_id = $bin_bucket->package_id;
            }

            Manifest::whereRaw("find_in_set('$request->id',bol)")->where('bol_ids', '<>', null)->update([
                'bol_ids' => $string,
            ]);

            $dropshipbin_bucket_new = Manifest::whereRaw("find_in_set('$request->id',bol)")->where('bol_ids', '<>', null)->get();

            foreach ($dropshipbin_bucket_new as $new) {

                $bol_ids = $new->bol_ids;

                $ids = unserialize($bol_ids);
                $array = explode(',', $ids);
                $bol_id = explode(',', $new->bol);

                sort($array);
                sort($bol_id);
                if ($array === $bol_id) {
                    foreach ($bol_id as $value) {
                        $related_ids = Manifest::whereRaw("find_in_set('$value',bol)")->get();

                        foreach ($related_ids as $ids) {
                            ScannedProducts::create([
                                'bol' => $ids->bol,
                                'package_id' => $ids->package_id,
                                'item_description' => $ids->item_description,
                                'units' => $ids->units,
                                'unit_cost' => $ids->unit_cost,
                                'total_cost' => $ids->total_cost,
                                'asin' => $ids->asin,
                                'GLDesc' => $ids->GLDesc,
                                'unit_recovery' => $ids->unit_recovery,
                                'total_recovery' => $ids->total_recovery,
                                'recovery_rate' => $ids->recovery_rate,
                                'removal_reason' => $ids->removal_reason
                            ]);

                            $ids->delete();
                        }
                    }
                    return response()->json(array('message' => 'Manifest products updated', 'code' => '910', 'package_id' => $package_id));
                }
            }

            return response()->json(array('message' => 'Manifest products updated', 'code' => '909', 'package_id' => $package_id));
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
                        'removal_reason' => $item->removal_reason
                    ]);
                }
                $item->delete();
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
                        'removal_reason' => $item->removal_reason
                    ]);
                }
                $item->delete();
            }
        } else if (count($daily_dropshipbin)) {
            $id_array = [];
            $string = '';
            foreach ($daily_dropshipbin as $bin) {
                array_push($id_array, $bin);

                $exploded_bin = explode(',', $bin->bol);
                while (($i = array_search($request->id, $exploded_bin)) !== false) {
                    unset($exploded_bin[$i]);
                }

                $bol_ids = $bin->bol_ids;
                if ($bol_ids == null) {
                    $string = serialize($request->id);
                } else {
                    $ids = unserialize($bol_ids);
                    $array = explode(',', $ids);
                    if (in_array($request->id, $array)) {
                        $string = $bin->bol_ids;
                        return response()->json(array('message' => 'Id already scanned', 'code' => '707'));
                    } else {
                        $string = serialize($ids . ',' . $request->id);
                    }
                }
            }

            $package_id = 'Bucket#' . uniqid();
            DailyManifest::whereRaw("find_in_set('$request->id',bol)")->where('package_id', 'DROPSHIP_BIN')->update([
                'bol_ids' => $string,
                'package_id' => $package_id,
            ]);

            return response()->json(array('message' => 'Manifest products updated', 'code' => '909', 'package_id' => $package_id));
        } else if (count($daily_dropshipbin_bucket)) {
            $id_array = [];
            $string = '';
            $package_id = '';
            foreach ($daily_dropshipbin_bucket as $bin_bucket) {
                array_push($id_array, $bin_bucket);

                $exploded_bin = explode(',', $bin_bucket->bol);
                while (($i = array_search($request->id, $exploded_bin)) !== false) {
                    unset($exploded_bin[$i]);
                }
                $bol_ids = $bin_bucket->bol_ids;

                if ($bol_ids == null) {
                    $string = serialize($request->id);
                } else {
                    $ids = unserialize($bol_ids);
                    $array = explode(',', $ids);
                    if (in_array($request->id, $array)) {
                        $string = $bin_bucket->bol_ids;
                        return response()->json(array('message' => 'Id already scanned', 'code' => '707'));
                    } else {
                        $string = serialize($ids . ',' . $request->id);
                    }
                }

                $package_id = $bin_bucket->package_id;
            }

            DailyManifest::whereRaw("find_in_set('$request->id',bol)")->where('bol_ids', '<>', null)->update([
                'bol_ids' => $string,
            ]);

            $dropshipbin_bucket_new = DailyManifest::whereRaw("find_in_set('$request->id',bol)")->where('bol_ids', '<>', null)->get();

            foreach ($dropshipbin_bucket_new as $new) {

                $bol_ids = $new->bol_ids;

                $ids = unserialize($bol_ids);
                $array = explode(',', $ids);
                $bol_id = explode(',', $new->bol);

                sort($array);
                sort($bol_id);
                if ($array === $bol_id) {
                    foreach ($bol_id as $value) {
                        $related_ids = DailyManifest::whereRaw("find_in_set('$value',bol)")->get();

                        foreach ($related_ids as $ids) {
                            ScannedProducts::create([
                                'bol' => $ids->bol,
                                'package_id' => $ids->package_id,
                                'item_description' => $ids->item_description,
                                'units' => $ids->units,
                                'unit_cost' => $ids->unit_cost,
                                'total_cost' => $ids->total_cost,
                                'asin' => $ids->asin,
                                'GLDesc' => $ids->GLDesc,
                                'unit_recovery' => $ids->unit_recovery,
                                'total_recovery' => $ids->total_recovery,
                                'recovery_rate' => $ids->recovery_rate,
                                'removal_reason' => $ids->removal_reason
                            ]);

                            $ids->delete();
                        }
                    }
                    return response()->json(array('message' => 'Manifest products updated', 'code' => '910', 'package_id' => $package_id));
                }
            }

            return response()->json(array('message' => 'Manifest products updated', 'code' => '909', 'package_id' => $package_id));
        }

        return response()->json(array('message' => 'Manifest products updated', 'code' => '201'));
    }

    public function removeScannedProducts(Request $request)
    {
        $with_package_id = ScannedProducts::where('package_id', $request->id)->get();
        $with_bol_id = ScannedProducts::where('bol', $request->id)->get();

        if (count($with_package_id)) {
            foreach ($with_package_id as $item) {
                Manifest::create([
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
                Manifest::create([
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

        return response()->json(array('message' => 'Manifest products updated', 'code' => '201'));
    }

    public function viewScannedProducts()
    {
        $breadcrumbs = [
            ['link' => "view-scanned-products", 'name' => "Products"], ['name' => "Scanned List"]
        ];

        return view('manifest/products', ['breadcrumbs' => $breadcrumbs]);
    }

    public function allScannedProducts()
    {
        return response()->json(array('data' => ScannedProducts::get()));
    }

    public function allClaims()
    {
        return response()->json(array('data' => ClaimList::get()));
    }

    public function allBuckets()
    {
        $mani = Manifest::where('bol_ids', '<>', null)->get()->toArray();
        $daily = DailyManifest::where('bol_ids', '<>', null)->get()->toArray();

        return response()->json(array('data' => array_merge($daily, $mani)));
    }

    public function allUnknownProducts()
    {
        return response()->json(array('data' => ScannedProducts::where('unknown_list', 'yes')->get()));
    }

    public function getProducts(Request $request)
    {
        $with_package_id = ScannedProducts::where('package_id', $request->id)->get();
        $with_bol_id = ScannedProducts::where('bol', $request->id)->get();

        if (count($with_package_id)) {
            return response()->json(array('message' => 'Found with Package ID', 'data' => $with_package_id, 'code' => '201'));
        } else if (count($with_bol_id)) {
            return response()->json(array('message' => 'Found with Bol ID', 'data' => $with_bol_id, 'code' => '201'));
        } else {
            return response()->json(array('message' => 'not found', 'code' => '404'));
        }
    }

    public function exportScannedProducts(Request $request)
    {
        $pallet_name = Pallets::where('id', $request->id)->first();
        return Excel::download(new ScannedProductsExport($request->id),  'DE' . sprintf("%05d", $request->id) . '-' . 'For-Admin.xlsx');
    }

    public function clientExportScannedProducts(Request $request)
    {
        $pallet_name = Pallets::where('id', $request->id)->first();
        return Excel::download(new ScannedProductsClientExport($request->id), 'DE' . sprintf("%05d", $request->id) . '.xlsx');
    }

    public function viewBucket()
    {
        $breadcrumbs = [
            ['link' => "view-scanned-products", 'name' => "Products"], ['name' => "Index"]
        ];

        return view('manifest/bucket', ['breadcrumbs' => $breadcrumbs]);
    }

    public function allDailyManifests()
    {
        return response()->json(array('data' => DailyManifest::get()));
    }

    public function updateManifest()
    {
        $all_unknowns = ScannedProducts::where('unknown_list', 'yes')->get();
        foreach ($all_unknowns as $unknown) {
            $founds = Manifest::where('package_id', $unknown->package_id)->orWhere('bol', $unknown->bol)->get();

            if (count($founds)) {
                foreach ($founds as $found) {
                    $unknown->update([
                        'bol' => $found->bol,
                        'package_id' => $found->package_id,
                        'item_description' => $found->item_description,
                        'units' => $found->units,
                        'unit_cost' => $found->unit_cost,
                        'total_cost' => $found->total_cost,
                        'asin' => $found->asin,
                        'GLDesc' => $found->GLDesc,
                        'unit_recovery' => $found->unit_recovery,
                        'total_recovery' => $found->total_recovery,
                        'recovery_rate' => $found->recovery_rate,
                        'removal_reason' => $found->removal_reason,
                        'unknown_list' => null
                    ]);
                    $found->delete();
                }
            }
        }

        return redirect()->back();
    }


    public function compareManifest(Request $request)
    {
        $breadcrumbs = [
            ['link' => "manifest", 'name' => "Manifest"], ['name' => "Create"]
        ];

        return view('manifest/compare', ['breadcrumbs' => $breadcrumbs]);
    }

    public function downloadUpdatedManifest(Request $request)
    {
        if ($request->has('uploaded_file')) {
            \DB::table('manifest_compares')->truncate();

            $file = $request->file('uploaded_file');
            $filename = $file->getClientOriginalName();
            $location = 'uploads';
            $file->move(public_path($location), $file->getClientOriginalName());
            $filepath = public_path($location . "/" . $filename);


            Excel::import(new ManifestCompareImport($filename), $filepath);

            unlink($filepath);

            $wrongBolIds = ManifestCompare::where('bol', 'LIKE', '%+%')->pluck('package_id')->toArray();


            foreach ($wrongBolIds as $key => $compare) {
                
                        $found_from_scanned = ScannedProducts::where('package_id', $compare)->first();
                        if (!is_null($found_from_scanned)) {
                            ManifestCompare::where('package_id', $compare)->delete();
                        }
               
               
            }


            $all_manifest_to_compare = ManifestCompare::pluck('bol')->toArray();

            $containsScanned = ScannedProducts::where('bol', 'LIKE', '%+%')->pluck('bol')->toArray();
            $allbolids = ScannedProducts::pluck('bol')->toArray();

            $allneeded = array_diff($allbolids, $containsScanned);


            $all_to_compare = array_diff($all_manifest_to_compare, $allneeded);

    
           // dd($all_manifest_to_compare,$all_to_compare,$allneeded);

            return Excel::download(new ScannedProductsExport($all_to_compare),  'Daily-Weekly-Comparison.xlsx');
        }
    }
}
