<?php

namespace App\Http\Controllers;

use App\Models\Manifest;
use App\Models\ManifestRecord;
use Illuminate\Http\Request;
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
        $file = $request->file('manifestfile');
        // if ($file) {
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize(); //Get size of uploaded file in bytes
        //Check for file extension and size
        $this->checkUploadedFileProperties($extension, $fileSize);
        //Where uploaded file will be stored on the server 
        $location = 'uploads'; //Created an "uploads" folder for that
        // Upload file
        $file->move($location, $filename);
        // In case the uploaded file path is to be stored in the database 
        $filepath = public_path($location . "/" . $filename);

        $customerArr = $this->csvToArray($filepath);

        $count = 0;

        foreach ($customerArr as $key => $value) {
            $count += 1;
            Manifest::create([
                'bol' => $value['BOL'],
                'package_id' => $value['PkgID'],
                'item_description' => $value['ItemDesc'],
                'units' => $value['Units'],
                'unit_cost' => $value['UnitCost'],
                'total_cost' => $value['TotalCost']
            ]);
        }
        ManifestRecord::create([
            'file_name' => $filename,
            'number_of_entities' => $count + 1,
            'uploaded_by' => auth()->user()->id
        ]);

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

    function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    public function checkUploadedFileProperties($extension, $fileSize)
    {
        $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
        $maxFileSize = 3097152; // Uploaded file size limit is 2mb
        if (in_array(strtolower($extension), $valid_extension)) {
            if ($fileSize <= $maxFileSize) {
            } else {
                throw new \Exception('No file was uploaded', Response::HTTP_REQUEST_ENTITY_TOO_LARGE); //413 error
            }
        } else {
            throw new \Exception('Invalid file extension', Response::HTTP_UNSUPPORTED_MEDIA_TYPE); //415 error
        }
    }
}
