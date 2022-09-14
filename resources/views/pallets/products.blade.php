@extends('layouts/contentLayoutMaster')

@section('title', 'View Pallets')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
@endsection
@section('page-style')
<link rel="stylesheet" href="{{asset('css/base/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" href="{{asset('css/base/pages/app-invoice.css')}}">
@endsection

@section('content')
<section class="invoice-preview-wrapper">
    <div class="row invoice-preview">
        <!-- Invoice -->
        <div class="col-xl-9 col-md-8 col-12">
            <div class="card invoice-preview-card">
                <div class="card-body invoice-padding pb-0">
                    <!-- Header starts -->
                    <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
                        <div>
                            <div class="logo-wrapper">
                            <img src="{{asset('images/logo/logo-web.png')}}">

                                <h3 class="text-primary invoice-logo">LIKE-TRADING - PALLET</h3>
                            </div>
                        </div>
                        <div class="mt-md-0 mt-2">
                            <h4 class="invoice-title">
                                Pallet ID
                                <span class="invoice-number">#{{ $invoice_number }}</span>
                            </h4>
                            <div class="invoice-date-wrapper">
                                <p class="invoice-date-title">Date Created:</p>
                                <p class="invoice-date">{{ $date_issued }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Header ends -->
                </div>

                <hr class="invoice-spacing" />


                <div id="print-able">
                    <!-- Address and Contact starts -->
                    <div class="card-body invoice-padding pt-0">
                        <div class="row invoice-spacing">
                            <div class="col-xl-12 p-0 mt-xl-0 mt-1">
                                <h6 class="mb-1">Product Details:</h6>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="pr-1">Total Units:</td>
                                            <td><span class="font-weight-bold">{{ $total_units }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="pr-1">Total Price:</td>
                                            <td><span class="font-weight-bold">€ {{ $total_price }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Address and Contact ends -->

                    <!-- Invoice Description starts -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="py-1">Item Description</th>
                                    <th class="py-1">Total Units</th>
                                    <th class="py-1">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($products))
                                    @foreach($products as $product)

                                    <tr>
                                        <td>
                                            {{ $product->item_description }}
                                        </td>
                                        <td>
                                            {{ $product->units }}
                                        </td>
                                        <td>
                                            € {{ $product->total_cost }}
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Invoice -->

        <!-- Invoice Actions -->
        <div class="col-xl-3 col-md-4 col-12 invoice-actions mt-md-0 mt-2">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-outline-secondary btn-block mb-75" href="{{ route('pallets.edit', $invoice_number) }}"> Edit </a>
                    <a class="btn btn-outline-secondary btn-block mb-75" href="{{ route('exporScanned', ['id' => $invoice_number]) }}"> Export </a>
                    <a class="btn btn-outline-secondary btn-block mb-75" href="{{ route('client', ['id' => $invoice_number])}}"> Export for Client </a>

                </div>
            </div>
        </div>
        <!-- /Invoice Actions -->
    </div>
</section>

@endsection

@section('vendor-script')
<script src="{{asset('vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('js/scripts/pages/app-invoice.js')}}"></script>
<script>
    function print_page() {

        var mywindow = window.open('', 'PRINT', 'height=900,width=900');

        mywindow.document.write('<html><head><title>' + document.title + '</title>');
        mywindow.document.write('</head><body >');
        mywindow.document.write('<h1>' + document.title + '</h1>');
        mywindow.document.write(document.getElementById('print-able').innerHTML);
        mywindow.document.write('</body></html>');

        // mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }
</script>
@endsection