@extends('layouts/contentLayoutMaster')

@section('title', 'Upload Weekly Manifest')

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
                    <h4 class="card-title">Upload Weekly Manifest Below</h4>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="upload-file" action="{{ route('manifest.store') }}">
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
                                <button type="submit" class="btn btn-primary" id="submit">Upload Now </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- single file upload ends -->

    <!-- multi file upload starts -->
    <!-- <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Multiple Files Upload</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        By default, dropzone is a multiple file uploader. User can either click on the dropzone area and select
                        multiple files or just drop all selected files in the dropzone area. This example is the most basic setup
                        for dropzone.
                    </p>
                    <form action="#" class="dropzone dropzone-area" id="dpz-multiple-files">
                        <div class="dz-message">Drop files here or click to upload.</div>
                    </form>
                </div>
            </div>
        </div>
    </div> -->
    <!-- multi file upload ends -->

    <!-- button file upload starts -->
    <!-- <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Use Button To Select Files</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Using this method, user gets an option to select the files using a button instead dropping all the files
                        after selected from the folders. Set <code>clickable</code> to match the button's id for button to work as
                        file selector.
                    </p>
                    <button id="select-files" class="btn btn-outline-primary mb-1">
                        <i data-feather="file"></i> Click me to select files
                    </button>
                    <form action="#" class="dropzone dropzone-area" id="dpz-btn-select-files">
                        <div class="dz-message">Drop files here or click button to upload.</div>
                    </form>
                </div>
            </div>
        </div>
    </div> -->
    <!-- button file upload ends -->

    <!-- limit file upload starts -->
    <!-- <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Limit File Size & No. Of Files</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        In many case user must be limited to upload certain no. of files. You can always set the
                        <code>maxFiles</code> option to limit no. of upload files. <code>maxfilesexceeded</code> event will be
                        called if uploads exceeds the limit. Also, if you want to limit the file size of uploads then set the
                        <code>maxFilesize</code> option. Define the maximum file size to be uploded in MBs like <code>0.5</code> MB
                        as is in this example. User can also define <code>maxThumbnailFilesize</code> in MB. When the uploaded file
                        exceeds this limit, the thumbnail will not be generated.
                    </p>
                    <form action="#" class="dropzone dropzone-area" id="dpz-file-limits">
                        <div class="dz-message">Drop files here or click to upload.</div>
                    </form>
                </div>
            </div>
        </div>
    </div> -->
    <!-- limit file upload ends -->

    <!-- accepted file upload starts -->
    <!-- <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Accepted files</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        The default implementation of <code>accept</code> checks the file's mime type or extension against this
                        list. This is a comma separated list of mime types or file extensions. Eg.:
                        <code>image/*,application/pdf,.psd</code>. If the Dropzone is <code>clickable</code> this option will be
                        used as <code>accept</code> parameter on the hidden file input as well.
                    </p>
                    <form action="#" class="dropzone dropzone-area" id="dpz-accept-files">
                        <div class="dz-message">Drop files here or click to upload.</div>
                    </form>
                </div>
            </div>
        </div>
    </div> -->
    <!-- accepted file upload ends -->

    <!-- remove thumbnail file upload starts -->
    <!-- <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Remove Thumbnail</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        This example allows user to remove any file out of all uploaded files. This will add a link to every file
                        preview to remove or cancel (if already uploading) the file. The <code>dictCancelUpload</code>,
                        <code>dictCancelUploadConfirmation</code> and <code>dictRemoveFile</code> options are used for the wording.
                    </p>
                    <form action="#" class="dropzone dropzone-area" id="dpz-remove-thumb">
                        <div class="dz-message">Drop files here or click to upload.</div>
                    </form>
                </div>
            </div>
        </div>
    </div> -->
    <!-- remove thumbnail file upload ends -->

    <!-- remove all thumbnails file upload starts -->
    <!-- <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Remove All Thumbnails</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        This example allows user to create a button that will remove all files from a dropzone. Hear for the
                        button's click event and then call <code>removeAllFiles</code> method to remove all the files from the
                        dropzone.
                    </p>
                    <button id="clear-dropzone" class="btn btn-outline-primary mb-1">
                        <i data-feather="trash" class="mr-25"></i>
                        <span class="align-middle">Clear Dropzone</span>
                    </button>
                    <form action="#" class="dropzone dropzone-area" id="dpz-remove-all-thumb">
                        <div class="dz-message">Drop files here or click to upload.</div>
                    </form>
                </div>
            </div>
        </div>
    </div> -->
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