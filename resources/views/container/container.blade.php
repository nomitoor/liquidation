@extends('layouts/contentLayoutMaster')

@section('title', 'All Containers')

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
                    <h4 class="card-title">All Containers</h4>
                    <a class="btn btn-primary" href="{{ route('containers.create') }}">Create Containers</a>
                </div>
                <div class="card-datatable">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Container Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($containers as $container)
                            <tr>
                                <td>{{ $container->name }}</td>
                                <td>
                                    <a class="btn btn-warning btn-sm" href="{{ route('export-container', ['container' => $container->id]) }}"> Export </a>
                                    <a class="btn btn-warning btn-sm" href="{{ route('export-container-client', ['container' => $container->id])}}"> Export for Client </a>

                                    <a href="{{ route('containers.edit', $container->id) }}" class="btn btn-info btn-sm">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
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
    function viewProducts(id) {
        alert(id)
    }

    function getManifest(id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo route('scanned-manifests') ?>',
            data: {
                '_token': '<?php echo csrf_token() ?>',
                'id': id
            },
            success: function(data) {

                if (data.code == '201') {
                    $('.open-modal').click();
                    var table = document.getElementById("myTable");
                    var unit_count = 0;
                    var total_cost = 0;
                    table.innerHTML = "";

                    data.data.forEach((manifest) => {
                        var row = table.insertRow(0);
                        var cell0 = row.insertCell(0);
                        var cell1 = row.insertCell(1);
                        var cell2 = row.insertCell(2);
                        var cell3 = row.insertCell(3);

                        unit_count += parseInt(manifest.units)
                        total_cost = parseFloat(total_cost) + parseFloat(manifest.total_cost)

                        cell0.innerHTML = manifest.item_description;
                        cell1.innerHTML = manifest.units;
                        cell2.innerHTML = manifest.unit_cost;
                        cell3.innerHTML = manifest.total_cost;

                    })
                    $('.total_units').html(unit_count)
                    $('.total_costs').html(total_cost.toFixed(2))

                } else {
                    alert("Nothing found against your ID please try with a valid ID");
                }
            }
        });
    }
</script>
@endsection