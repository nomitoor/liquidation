@extends('layouts/contentLayoutMaster')

@section('title', 'All Pallets')

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
                    <h4 class="card-title">All Pallets</h4>
                    <a class="btn btn-primary" href="{{ route('pallets.create') }}">Create Pallets</a>
                </div>
                @if($errors->any())

                <div class="col-12 text-center alert alert-danger mt-2 mb-0" id="pallet-added" role="alert">
                    <div class="alert-body">
                        @foreach($errors->all() as $error)
                        <strong>{{ $error }}</strong>
                        @endforeach
                    </div>
                </div>
                <br>
                @endif
                <div class="card-datatable">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Pallet Image</th>
                                <th>id</th>
                                <th>Category</th>
                                <th>Cost</th>
                                <th>Units</th>
                                <th>Recovery</th>
                                <th>Description</th>
                                
                                <th style="width: 260px;">Actions</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pallets as $pallet)
                            <tr>
                                <td>
                                <div class="pop" >

                                <a href="#" id="pop">
                                    <img style=" height: 40px;width: 40px;border-radius: 10%;" src="https://cdn-icons-png.flaticon.com/512/5676/5676330.png" >
                                    <input value="{{ $pallet->created_at }}" type="hidden"> 
                                    <input value="{{ $pallet->created_at }}" type="hidden" class="updated"> 

                                </a>
</div>
                                </td>
                                <td>{{ 'DE'.sprintf("%05d", $pallet->id) }}</td>
                                <td>{{ $pallet->category->title ?? '-' }}</td>
                                <td>{{ $pallet->total_price }}</td>
                                <td>{{ $pallet->total_unit }}</td>
                                <td>{{ $pallet->total_recovery }}</td>
                                <td>{{ $pallet->description }}</td>
                               
                                <td>
                                    <a href="{{ route('pallets.show', $pallet->id) }}" class="btn btn-warning btn-sm">
                                        View
                                    </a>
                                    <a href="{{ route('pallets.edit', $pallet->id) }}" class="btn btn-info btn-sm">
                                        Edit
                                    </a>

                                    <a onclick="deleteAllPallets({{ $pallet->id }})"  class="btn btn-danger btn-sm">
                                        Delete
                                    </a>
                                    <!-- <form onSubmit="return confirm('Do you want to delete this pallet?')" action="{{ url('/pallets', ['pallet' => $pallet->id]) }}" method="post">
                                        <input class="btn btn-danger btn-sm" type="submit" value="Delete" />
                                        <input type="hidden" name="_method" value="delete" />
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    </form>
                                   -->
                                </td>
                                <td>
                                
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{ $pallets->links('pagination::bootstrap-4') }}
        </div>
    </div>
</section>

<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" data-dismiss="modal">
      <div class="modal-content"  >              
        <div class="modal-body">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
             <img src="" class="imagepreview" style="width: 100%;" >
        </div> 
     <div class="modal-footer">
       <div class="col-xs-12">
           <p class="text-right">Created At  --  Updated At</p>
           <p class="text-left"></p>
       </div>
     </div>         
   </div>
 </div>
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

$(function() {
    $('.pop').on('click', function() {
        $('.imagepreview').attr('src', $(this).find('img').attr('src'));
        $('.text-left').text($(this).find('input').attr('value')+'  --  '+$('.updated').attr('value'));

        $('#imagemodal').modal('show');   
    });     
});
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

    function deleteAllPallets(id) {
        var result = confirm("Are your sure you want delete this Pallet?");
        if (result) {
            event.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'api/pallet/deleteFullPallet',
                data: {
                    '_token': '<?php echo csrf_token(); ?>',
                    'id': id,
                },
                success: function(data) {
                    if (data.code == 200) {
                        location.reload()
                    }else{
                        alert(data.message);
                    }
                }
            });
        }
    }
</script>
<style>
  .pallet-image{
 
}

</style>
@endsection