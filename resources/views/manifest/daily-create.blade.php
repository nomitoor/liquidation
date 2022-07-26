@extends('layouts/contentLayoutMaster')

@section('title', 'Uploading manifest')

@section('vendor-style')
<!-- vendor css files -->
<link rel="stylesheet" href="{{ asset(mix('vendors/css/file-uploaders/dropzone.min.css')) }}">
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
                    <h4 class="card-title">Please upload manifest file here</h4>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="upload-file" action="{{ route('daily-manifest-store') }}">
                        {{ csrf_field() }}
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="file" name="manifestfile" placeholder="Choose file" id="file">
                                    @error('file')
                                    <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                    @enderror
                                </div>
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
    <!-- remove all thumbnails file upload ends -->
</section>
<!-- Dropzone section end -->
@endsection

@section('vendor-script')
<!-- vendor files -->
<script src="{{ asset(mix('vendors/js/file-uploaders/dropzone.min.js')) }}"></script>
@endsection
@section('page-script')
<!-- Page js files -->
<script src="{{ asset(mix('js/scripts/forms/form-file-uploader.js')) }}"></script>
@endsection