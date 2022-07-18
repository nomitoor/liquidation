@extends('layouts/contentLayoutMaster')

@section('title', 'Create Pallets')

@section('vendor-style')
<!-- vendor css files -->
<link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection
@section('page-style')
<!-- Page css files -->
<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-file-uploader.css')) }}">
@endsection

@section('content')
<!-- Dropzone section start -->
<section id="dropzone-examples">
    <!-- single file upload starts -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Please add pallet name</h4>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="upload-file" action="{{ route('pallets.store') }}">
                        {{ csrf_field() }}
                        <div class="row">

                            <div class="col-md-12 mb-1">
                                <label>Name</label>
                                <input type="text" class="form-control" name="pallet_name" />
                                <!-- <label>Select products</label>
                                <select class="select2 form-control" name="bol[]" multiple>
                                    @foreach($products as $product)
                                    <option value='{{ $product->bol }}'>{{$product->bol}}</option>
                                    @endforeach
                                </select> -->
                            </div>

                            <div class="col-md-12 mb-1">
                                <label>Description</label>
                                <input type="text" class="form-control" name="description" />
                            </div>

                            <div class="col-md-12 mb-1">
                                <label>Category</label>
                                <select name="category_id" id="categoryId" class="form-control">
                                    <option value="">Please select a category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary" id="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- single file upload ends -->
</section>
<!-- Dropzone section end -->
@endsection

@section('vendor-script')
<!-- vendor files -->
<script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')
<!-- Page js files -->
<script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
@endsection