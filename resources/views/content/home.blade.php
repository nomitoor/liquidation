@extends('layouts/contentLayoutMaster')

@section('title', 'Home')

@section('content')

<!-- Medal Card -->
<div class="row">
    <div class="col-xl-4 col-md-6 col-12">
        <div class="card card-congratulation-medal">
            <div class="card-body">
                <p class="card-text font-small-3 text-align-center">Receive products</p>
                <a href="/bar-code-scanner" class="btn btn-primary">Receive products</a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 col-12">
        <div class="card card-congratulation-medal">
            <div class="card-body">
                <p class="card-text font-small-3 text-align-center">View all of your product list</p>
                <a href="/view-scanned-products" class="btn btn-primary">View product list</a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 col-12">
        <div class="card card-congratulation-medal">
            <div class="card-body">
                <p class="card-text font-small-3 text-align-center">View all of your unknown product list</p>
                <a href="/unknown" class="btn btn-primary">View unknown product list</a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 col-12">
        <div class="card card-congratulation-medal">
            <div class="card-body">
                <p class="card-text font-small-3 text-align-center">Create Pallets</p>
                <a href="/pallets/create" class="btn btn-primary">Create Pallets</a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 col-12">
        <div class="card card-congratulation-medal">
            <div class="card-body">
                <p class="card-text font-small-3 text-align-center">Create unknown Pallets</p>
                <a href="/pallets/create" class="btn btn-primary">Create unknown Pallets</a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 col-12">
        <div class="card card-congratulation-medal">
            <div class="card-body">
                <p class="card-text font-small-3 text-align-center">View Pallet List</p>
                <a href="pallets" class="btn btn-primary">View Pallet List</a>
            </div>
        </div>
    </div>
</div>
<!--/ Medal Card -->
@endsection