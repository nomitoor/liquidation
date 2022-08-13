@extends('layouts/contentLayoutMaster')

@section('title', 'Create Containers')

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
                    <h4 class="card-title">Please Enter Container Name</h4>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="upload-file" action="{{ route('containers.store') }}">
                        {{ csrf_field() }}
                        <div class="row">

                            <div class="col-md-12">
                            </div>

                            <div class="col-md-12">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" />
                            </div>

                            <div class="col-md-12 mt-1">
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