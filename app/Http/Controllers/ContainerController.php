<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Container;
use App\Models\Pallets;
use App\Exports\ContainerClientExport;
use App\Exports\ContainerExport;
use Maatwebsite\Excel\Facades\Excel;

class ContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $container = Container::with('pallets')->get();

        $breadcrumbs = [
            ['link' => "Container", 'name' => "Container"], ['name' => "Index"]
        ];

        return view('container/container', ['breadcrumbs' => $breadcrumbs, 'containers' => $container]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumbs = [
            ['link' => "pallets", 'name' => "Pallets"], ['name' => "Create"]
        ];

        return view('container/create', ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $container = Container::create([
            'name' => $request->name,
        ]);

        return redirect('containers/' . $container->id . '/edit');
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
    public function edit(Container $container)
    {
        $breadcrumbs = [
            ['link' => "pallets", 'name' => "Pallets"], ['name' => "Create"]
        ];

        $container = Container::with('pallets')->find($container->id);

        return view('container/edit', ['breadcrumbs' => $breadcrumbs, 'container' => $container]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Container $container)
    {
        $pallet_id = explode('DE', $request->pallet_id);
        $trimed_id = ltrim($pallet_id[1], "0");

        // Check if pallet is already a part of container 
        $container_exits = Container::whereHas('pallets', function ($query) use ($trimed_id) {
            $query->where('pallet_id', $trimed_id);
        })->get();

        $container->pallets()->sync($trimed_id, false);
        // if (is_null($container_exits)) {
        return response()->json(array('code' => '201', 'message' => 'Added successfully'));
        // } else {
        //     return response()->json(array('code' => '204', 'message' => 'This pallet is already part of a continer'));
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Container $container)
    {
        $container->pallets()->detach($request->package_id);

        return response()->json(array('code' => '201', 'message' => 'Deleted successfully!'));
    }

    public function exportContainers(Container $container)
    {
        return Excel::download(new ContainerExport($container), $container->id . '-' . 'containers.xlsx');
    }

    public function exportContainersClient(Container $container)
    {
        return Excel::download(new ContainerClientExport($container), $container->id . '-' . 'containers-client.xlsx');
    }
}
