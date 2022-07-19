@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Pallets')

@section('vendor-style')
<!-- vendor css files -->
<link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection
@section('page-style')
<!-- Page css files -->
<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-file-uploader.css')) }}">
<style>
    .elipsis {
        white-space: nowrap;
        overflow: hidden;
    }
</style>
@endsection

@section('content')
<!-- Dropzone section start -->
<section id="dropzone-examples">
    <!-- single file upload starts -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Please Enter Pallet Details</h4>
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
                                <div class="input-group">
                                    <input type="text" class="form-control product_code" placeholder="Enter product ID or Bol ID" />
                                    <span class="input-group-btn">
                                        <button class="btn btn-info">
                                            <i class="fa fa-undo" aria-hidden="true"></i>Undo
                                        </button>
                                    </span>
                                </div>
                                <div class="">
                                    <label>Added BOL IDs</label>
                                    <table class="table" id="myTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="elipsis">Bol IDs</th>
                                                <th class="elipsis">Units</th>
                                                <th class="elipsis">Unit Cost</th>
                                                <th class="elipsis">Total Cost</th>
                                                <th class="elipsis">Package ID</th>
                                                <th class="elipsis">Item Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($pallets->bol_ids !== null)
                                            @foreach($scanned_products as $product)
                                            <tr>
                                                <th>
                                                    <div class="elipsis">
                                                        <img style="width:25px;cursor:pointer" onclick="deletePallet('<?php echo $pallets->id ?>','<?php echo $product->bol ?>')" src="https://eccdatacenter.ae/umeattendance/Images/Close_Rej.jpg">
                                                    </div>
                                                </th>
                                                <th>
                                                    <div class="elipsis">{{$product->bol}}</div>
                                                </th>
                                                <th>
                                                    <div class="elipsis">{{$product->units}}</div>
                                                </th>
                                                <th>
                                                    <div class="elipsis">{{$product->unit_cost}}</div>
                                                </th>
                                                <th>
                                                    <div class="elipsis">{{$product->total_cost}}</div>
                                                </th>
                                                <th>
                                                    <div class="elipsis">{{$product->package_id}}</div>
                                                </th>
                                                <th>
                                                    <div class="elipsis">{{$product->item_description}}</div>
                                                </th>
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
        var executed = false;

        if (!executed) {
            executed = true;
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
                        location.reload()
                        $('#pallet-added').removeClass('d-none')
                        $('#pallet-added').addClass('d-block')

                        $('.product_code').val('');
                        var table = document.getElementById("myTable").getElementsByTagName('tbody')[0];
                        table.innerHTML = "";

                        data.data.forEach((scanned_product) => {
                            var row = table.insertRow(0);
                            var cell0 = row.insertCell(0);
                            var cell1 = row.insertCell(1);
                            var cell2 = row.insertCell(2);
                            var cell3 = row.insertCell(3);
                            var cell4 = row.insertCell(4);
                            var cell5 = row.insertCell(5);

                            cell0.innerHTML = scanned_product.bol;
                            cell1.innerHTML = scanned_product.package_id;
                            cell2.innerHTML = scanned_product.item_description
                            cell3.innerHTML = scanned_product.units;
                            cell4.innerHTML = scanned_product.unit_cost;
                            cell5.innerHTML = scanned_product.total_cost;

                        })

                    } else if (data.code == '403') {
                        $('.product_code').val('');
                        alert(data.message);
                    } else {
                        $('.product_code').val('');
                    }
                }
            });
        }
    })

    function deletePallet(id, bol_id) {
        var result = confirm("Are your sure you want to remove this product from this pallete?");
        if (result) {
            $.ajax({
                type: 'post',
                url: '/pallets/delete',
                data: {
                    '_token': '<?php echo csrf_token() ?>',
                    'bol_id': bol_id,
                    'id': id,
                },
                success: function(data) {
                    if (data.code == '201') {
                        location.reload()
                    }
                }
            });
        }
    }

    setInterval(function() {
        $('#pallet-added').removeClass('d-block')
        $('#pallet-added').addClass('d-none')
    }, 2000);
</script>
@endsection