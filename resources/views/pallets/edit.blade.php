@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Pallets')

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
                    <div class="col-12 text-center alert alert-success mt-2 mb-0 d-none" id="pallet-added" role="alert">
                        <div class="alert-body"><strong>BOL ID added to pallet!</strong></div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="upload-file" action="{{ route('pallets.store') }}">
                        {{ csrf_field() }}
                        <div class="row">

                            <div class="col-md-12 mb-1">
                                <label>Name</label>
                                <input type="text" class="form-control product_code" id="product_code" placeholder="Enter product ID or Bol ID" />

                                <div class="">
                                    <label>Added BOL IDs</label>
                                    <table class="table" id="myTable">
                                        <thead>
                                            <tr>
                                                <th>Bol IDs</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($pallets->bol_ids !== null)
                                            @foreach(unserialize($pallets->bol_ids) as $bol_id)
                                            <tr>
                                                <th>{{$bol_id}}</th>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <th>No Bol ID found!</th>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
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
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
    $('.product_code').bind("input change", function(e) {
        var product_code = $('.product_code').val();

        $.ajax({
            type: 'PATCH',
            url: '<?php echo route('pallets.update', $pallets->id) ?>',
            data: {
                '_token': '<?php echo csrf_token() ?>',
                'bol_id': product_code
            },
            success: function(data) {

                if (data.code == '201') {
                    $('#pallet-added').removeClass('d-none')
                    $('#pallet-added').addClass('d-block')

                    $('.product_code').val('');
                    console.log(data.data);

                    // data.data.forEach((pallet) => {
                    //     console.log(pallet);
                    // })

                    // $('.open-modal').click();
                    var table = document.getElementById("myTable");
                    // var unit_count = 0;
                    // var total_cost = 0;
                    table.innerHTML = "";

                    data.data.forEach((pallet) => {
                        var row = table.insertRow(0);
                        var cell0 = row.insertCell(0);

                        // var cell1 = row.insertCell(1);
                        // var cell2 = row.insertCell(2);
                        // var cell3 = row.insertCell(3);

                        // unit_count += parseInt(manifest.units)
                        // total_cost = parseFloat(total_cost) + parseFloat(manifest.total_cost)

                        cell0.innerHTML = pallet;
                        // cell1.innerHTML = manifest.units;
                        // cell2.innerHTML = manifest.unit_cost;
                        // cell3.innerHTML = manifest.total_cost;

                    })

                    // $('.total_units').html(unit_count)
                    // $('.total_costs').html(total_cost.toFixed(2))

                } else if (data.code == '403') {
                    $('.product_code').val('');
                    alert(data.message);
                } else {
                    $('.product_code').val('');
                }
            }
        });
    })
    setInterval(function() {
        $('#pallet-added').removeClass('d-block')
        $('#pallet-added').addClass('d-none')
    }, 2000);
</script>
@endsection