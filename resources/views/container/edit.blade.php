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
                    <div class="mt-1 mb-1">
                        @foreach($pallets as $pallet)
                            <button class="btn btn-success btn-sm" onclick="push_pallet_id_to_input('{{ 'DE' . sprintf('%05d', $pallet->id) }}')">{{ 'DE' . sprintf("%05d", $pallet->id) }}</button>
                        @endforeach
                    </div>
                    <form method="POST" enctype="multipart/form-data" id="upload-file" action="{{ route('pallets.store') }}">
                        {{ csrf_field() }}
                        <div class="row">

                            <div class="col-md-12 mb-1">
                                <label>Paste Pallet ID</label>
                                <div class="input-group">
                                    <input type="text" class="form-control pallet_id" placeholder="Enter product ID or Bol ID" />
                                    <span class="input-group-btn d-none">
                                        <button class="btn btn-info" onclick="undoPallet('<?php echo $container->id ?>')">
                                            <i class="fa fa-undo" aria-hidden="true"></i>Undo
                                        </button>
                                    </span>
                                </div>
                                <label>Paste Pallet ID</label>
                                <div class="input-group">
                                    <input type="text" class="form-control pallet_id_manually" placeholder="Enter product ID or Bol ID" />
                                    <span class="input-group-btn">
                                        <button class="btn btn-info" onclick="addPalletManually()">
                                            <i class="fa fa-undo" aria-hidden="true"></i>Add Pallet
                                        </button>
                                    </span>
                                </div>
                                
                                <div class="">
                                    <label>Added BOL IDs</label>
                                    <table class="table" id="myTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Pallet ID</th>
                                                <th class="elipsis">Description</th>
                                                <th class="elipsis">Total Cost</th>
                                                <th class="elipsis">Unit Cost</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($container->pallets !== null)
                                            @foreach($container->pallets as $product)
                                            <tr>
                                                <th>
                                                    <div class="elipsis">
                                                        <img style="width:25px;cursor:pointer" onclick="deletePallet('<?php echo $container->id ?>','<?php echo $product->id ?>')" src="https://eccdatacenter.ae/umeattendance/Images/Close_Rej.jpg">
                                                    </div>
                                                </th>
                                                <th>{{ 'DE' . sprintf("%05d", $product->id) }}</th>
                                                <th>
                                                    <div class="elipsis">{{$product->description}}</div>
                                                </th>
                                                <th>
                                                    <div class="elipsis">{{$product->total_price}}</div>
                                                </th>
                                                <th>
                                                    <div class="elipsis">{{$product->total_unit}}</div>
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
    $('.pallet_id').bind("input change", function(e) {
        e.preventDefault();
        var executed = false;

        if (!executed) {
            executed = true;
            var pallet_id = $('.pallet_id').val();
            $.ajax({
                type: 'PATCH',
                url: '<?php echo route('containers.update', $container->id) ?>',
                data: {
                    '_token': '<?php echo csrf_token() ?>',
                    'pallet_id': pallet_id
                },
                success: function(data) {

                    if (data.code == '201') {
                        location.reload()
                        $('#pallet-added').removeClass('d-none')
                        $('#pallet-added').addClass('d-block')

                        $('.pallet_id').val('');
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

                        alert(data.message);

                    } else if (data.code == '204') {
                        $('.pallet_id').val('');
                        alert(data.message);
                    } else {
                        $('.pallet_id').val('');
                    }
                }
            });
        }
    })

    function push_pallet_id_to_input(received_pallet_id){
        $('.pallet_id_manually').val(received_pallet_id)
        this.addPalletManually()
    }

    function addPalletManually() {
        event.preventDefault();
        var executed = false;

        if (!executed) {
            executed = true;
            var pallet_id = $('.pallet_id_manually').val();
            $.ajax({
                type: 'PATCH',
                url: '<?php echo route('containers.update', $container->id) ?>',
                data: {
                    '_token': '<?php echo csrf_token() ?>',
                    'pallet_id': pallet_id
                },
                success: function(data) {

                    if (data.code == '201') {
                        location.reload()
                        $('#pallet-added').removeClass('d-none')
                        $('#pallet-added').addClass('d-block')

                        $('.pallet_id').val('');
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

                        alert(data.message);

                    } else if (data.code == '204') {
                        $('.pallet_id').val('');
                        alert(data.message);
                    } else {
                        $('.pallet_id').val('');
                    }
                }
            });
        }
    }

    function undoPallet(id) {
        var result = confirm("Are your sure you want to remove this product from this pallete?");
        if (result) {
            event.preventDefault();
            $.ajax({
                type: 'post',
                url: '/pallets/undo',
                data: {
                    '_token': '<?php echo csrf_token() ?>',
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

    function deletePallet(id, package_id) {
        var result = confirm("Are your sure you want to remove this product from this pallete?");
        if (result) {
            console.log(id, package_id);
            event.preventDefault();
            $.ajax({
                type: 'delete',
                url: '<?php echo route('containers.destroy', $container->id) ?>',
                data: {
                    '_token': '<?php echo csrf_token() ?>',
                    'package_id': package_id,
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