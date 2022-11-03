@extends('layouts/contentLayoutMaster')

@section('title', 'Unknown Products')

@section('vendor-style')
{{-- vendor css files --}}
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" type="text/css" href="{{asset('css/base/plugins/forms/pickers/form-flat-pickr.css')}}">
@endsection


@section('content')
<!-- Ajax Sourced Server-side -->
<section id="ajax-datatable">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">All Unknown Products</h4>
                    <a class="btn btn-primary" onclick="checkManifest()">Check Manifest</a>
                </div>
                <div class="card-datatable">
                    <table class="manifest-data table">
                        <thead>
                            <tr>
                                <th>bol</th>
                                <th>item description</th>
                                <th>package id</th>
                                <th>total cost</th>
                                <th>unit cost</th>
                                <th>units</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!--/ Ajax Sourced Server-side -->
@endsection


@section('vendor-script')
{{-- vendor files --}}
<script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap4.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
@endsection

@section('page-script')
{{-- Page js files --}}
<!-- <script src="{{ asset(mix('js/scripts/tables/table-datatables-advanced.js')) }}"></script> -->

<script>
    var dt_ajax_table = $('.manifest-data');


    var dt_ajax = dt_ajax_table.dataTable({
        processing: true,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: "{{ route('all-uknown-products') }}",
        columns: [{
                data: 'bol'
            },
            {
                data: 'item_description'
            },
            {
                data: 'package_id'
            },
            {
                data: 'total_cost'
            },
            {
                data: 'unit_cost'
            },
            {
                data: 'units'
            },
        ],
        language: {
            paginate: {
                // remove previous & next text from pagination
                previous: '&nbsp;',
                next: '&nbsp;'
            }
        }
    });

    function checkManifest() {
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: '<?php echo route('checkManifest') ?>',
            data: {
                '_token': '<?php echo csrf_token() ?>',
            },
            success: function(data) {
                alert('done');
            }
        });
    }
</script>
@endsection