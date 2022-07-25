@extends('layouts/contentLayoutMaster')

@section('title', 'Import Products')


@section('content')
<div class="card">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-xl-12 col-sm-12 col-md-12 col-12 mt-2">
                <button class="btn btn-primary" id="start_seasion">Start Session</button>
                <div class="d-none enter-details">
                    <div class="form-group">
                        <label for="basicInput">Paste Bar code product ID or Bol ID</label>
                        <input type="text" class="form-control product_code" id="product_code" name="product_code" placeholder="Paste Bar code product ID or Bol ID" />
                    </div>

                    <label for="basicInput">Type product ID or Bol ID</label>
                    <div class="input-group">
                        <input type="text" class="form-control product_code_type" id="product_code_type" placeholder="Type product ID or Bol ID" />
                        <span class="input-group-btn">
                            <button class="btn btn-primary" onclick="getData()">Enter</button>
                        </span>
                    </div>

                    <div class="d-none">
                        <label for="basicInput">Remove from the product list</label>
                        <div class="input-group">
                            <input type="text" class="form-control remove_from_products" id="remove_from_products" placeholder="Type product ID or Bol ID" />
                        </div>
                    </div>

                    <div class="mt-1">
                        <button class="btn btn-danger" id="end_seasion">End Session</button>
                    </div>
                </div>
                <input type="hidden" id="number" />
                <!-- <button id="scan_bar_code" class="btn btn-primary w-100 col-lg-4 col-lg-4 col-sm-4">Open Camera</button>
            <button id="stop_camera" class="btn btn-primary w-100 col-lg-4 col-lg-4 col-sm-4">Stop Camera</button> -->
            </div>
            <div class="col-lg-6 col-xl-6 col-sm-12 col-md-4 mt-2">
                <h1 class="counter" style="margin-top: 90px;text-align: center;"></h1>
            </div>
        </div>
    </div>

    <div class="d-none" id="camera-div">
        <div class="row">
            <div class="col-lg-6 col-xl-6 col-sm-12 col-md-4 mt-1 mb-2 ml-2">
                <button id="opener" class="btn btn-primary w-100">Scan Again</button>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div id="modal" title="Barcode scanner">
                    <span class="found"></span>
                    <div id="interactive" class="viewport"></div>
                    <div id="deviceSelection" class="d-none"></div>
                </div>
            </div>
        </div>
    </div>

    <section id="modal-themes">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="demo-inline-spacing">

                            <div class="d-inline-block">


                                <div class="modal-size-lg d-inline-block">
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-outline-primary open-modal d-none" data-toggle="modal" data-target="#large">
                                        Large Modal
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade text-left" id="large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel17">Found Manifests</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="container-fluid">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-lg-2 col-sm-12 col-md-12">
                                                                <label>Total Units</label>
                                                                <h3 class="total_units"></h3>
                                                            </div>
                                                            <div class="col-xs-12 col-lg-2 col-sm-12 col-md-12">
                                                                <label>Grand Total</label>
                                                                <h3 class="total_costs"></h3>
                                                            </div>
                                                            <div class="col-xs-12 col-lg-6 col-sm-12 col-md-12 ml-auto mb-2 text-right">
                                                                <input type="hidden" id="select_id" />
                                                                <button class="btn btn-success mt-1 accept_products">Accept</button>
                                                                <button class="btn btn-danger mt-1 accept_products_to_claim_list">Add to Claim List</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Description</th>
                                                                <th>units</th>
                                                                <th>unit cost</th>
                                                                <th>total cost</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="myTable">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <button type="button" class="btn btn-primary d-none" id="unknown-list" data-toggle="modal" data-target="#exampleModal">
        Launch demo modal
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add to unknown list</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h3 class="uknown-list"></h3>
                    <input type="hidden" value="" id="unknown-field">
                    <div class="form-check">
                        <input type="radio" class="form-check-radio" name="unknownId" value="bol" id="bolid">
                        <label class="form-check-label" for="bolid">Save as BOL ID</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-radio" name="unknownId" value="package" id="packageid">
                        <label class="form-check-label" for="packageid">Save as Package ID</label>
                    </div>
                    <button class="btn btn-primary mt-1 add-unknown-id">Submit</button>
                </div>
            </div>
        </div>
    </div>


    <section id="modal-themes">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="demo-inline-spacing">

                            <div class="d-inline-block">


                                <div class="modal-size-lg d-inline-block">
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-outline-primary open-product-model d-none" data-toggle="modal" data-target="#large-new">
                                        Large Modal
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade text-left" id="large-new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel17">Found Products</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="container-fluid">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-lg-2 col-sm-12 col-md-12">
                                                                <label>Total Units</label>
                                                                <h3 class="total_units"></h3>
                                                            </div>
                                                            <div class="col-xs-12 col-lg-2 col-sm-12 col-md-12">
                                                                <label>Grand Total</label>
                                                                <h3 class="total_costs"></h3>
                                                            </div>
                                                            <div class="col-xs-12 col-lg-6 col-sm-12 col-md-12 ml-auto mb-2 text-right">
                                                                <input type="hidden" id="return_id" />
                                                                <button class="btn btn-success mt-1 return_to_manifest">Return</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Description</th>
                                                                <th>units</th>
                                                                <th>unit cost</th>
                                                                <th>total cost</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="myNewTable">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>
@endsection

@section('page-script')
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
    $(function() {
        $('#product_code').focus();

        var App = {
            init: function() {
                Quagga.init(this.state, function(err) {
                    if (err) {
                        console.log(err);
                        return;
                    }
                    App.attachListeners();
                    App.checkCapabilities();
                    Quagga.start();
                });
            },
            checkCapabilities: function() {
                var track = Quagga.CameraAccess.getActiveTrack();
                var capabilities = {};
                if (typeof track.getCapabilities === 'function') {
                    capabilities = track.getCapabilities();
                }
                this.applySettingsVisibility('zoom', capabilities.zoom);
                this.applySettingsVisibility('torch', capabilities.torch);
            },
            updateOptionsForMediaRange: function(node, range) {
                console.log('updateOptionsForMediaRange', node, range);
                var NUM_STEPS = 6;
                var stepSize = (range.max - range.min) / NUM_STEPS;
                var option;
                var value;
                while (node.firstChild) {
                    node.removeChild(node.firstChild);
                }
                for (var i = 0; i <= NUM_STEPS; i++) {
                    value = range.min + (stepSize * i);
                    option = document.createElement('option');
                    option.value = value;
                    option.innerHTML = value;
                    node.appendChild(option);
                }
            },
            applySettingsVisibility: function(setting, capability) {
                if (typeof capability === 'boolean') {
                    var node = document.querySelector('input[name="settings_' + setting + '"]');
                    if (node) {
                        node.parentNode.style.display = capability ? 'block' : 'none';
                    }
                    return;
                }
                if (window.MediaSettingsRange && capability instanceof window.MediaSettingsRange) {
                    var node = document.querySelector('select[name="settings_' + setting + '"]');
                    if (node) {
                        this.updateOptionsForMediaRange(node, capability);
                        node.parentNode.style.display = 'block';
                    }
                    return;
                }
            },
            initCameraSelection: function() {
                var streamLabel = Quagga.CameraAccess.getActiveStreamLabel();

                return Quagga.CameraAccess.enumerateVideoDevices()
                    .then(function(devices) {
                        function pruneText(text) {
                            return text.length > 30 ? text.substr(0, 30) : text;
                        }
                        var $deviceSelection = document.getElementById("deviceSelection");

                        while ($deviceSelection.firstChild) {
                            $deviceSelection.removeChild($deviceSelection.firstChild);
                        }
                        devices.forEach(function(device) {
                            var $option = document.createElement("option");
                            $option.value = device.deviceId || device.id;
                            $option.appendChild(document.createTextNode(pruneText(device.label || device.deviceId || device.id)));
                            $option.selected = streamLabel === device.label;
                            $deviceSelection.appendChild($option);
                        });
                    });
            },
            attachListeners: function() {
                var self = this;

                self.initCameraSelection();
                $(".controls").on("click", "button.stop", function(e) {
                    e.preventDefault();
                    Quagga.stop();
                });

                $(".controls .reader-config-group").on("change", "input, select", function(e) {
                    e.preventDefault();
                    var $target = $(e.target),
                        value = $target.attr("type") === "checkbox" ? $target.prop("checked") : $target.val(),
                        name = $target.attr("name"),
                        state = self._convertNameToState(name);

                    console.log("Value of " + state + " changed to " + value);
                    self.setState(state, value);
                });
            },
            _accessByPath: function(obj, path, val) {
                var parts = path.split('.'),
                    depth = parts.length,
                    setter = (typeof val !== "undefined") ? true : false;

                return parts.reduce(function(o, key, i) {
                    if (setter && (i + 1) === depth) {
                        if (typeof o[key] === "object" && typeof val === "object") {
                            Object.assign(o[key], val);
                        } else {
                            o[key] = val;
                        }
                    }
                    return key in o ? o[key] : {};
                }, obj);
            },
            _convertNameToState: function(name) {
                return name.replace("_", ".").split("-").reduce(function(result, value) {
                    return result + value.charAt(0).toUpperCase() + value.substring(1);
                });
            },
            detachListeners: function() {
                $(".controls").off("click", "button.stop");
                $(".controls .reader-config-group").off("change", "input, select");
            },
            applySetting: function(setting, value) {
                var track = Quagga.CameraAccess.getActiveTrack();
                if (track && typeof track.getCapabilities === 'function') {
                    switch (setting) {
                        case 'zoom':
                            return track.applyConstraints({
                                advanced: [{
                                    zoom: parseFloat(value)
                                }]
                            });
                        case 'torch':
                            return track.applyConstraints({
                                advanced: [{
                                    torch: !!value
                                }]
                            });
                    }
                }
            },
            setState: function(path, value) {
                var self = this;

                if (typeof self._accessByPath(self.inputMapper, path) === "function") {
                    value = self._accessByPath(self.inputMapper, path)(value);
                }

                if (path.startsWith('settings.')) {
                    var setting = path.substring(9);
                    return self.applySetting(setting, value);
                }
                self._accessByPath(self.state, path, value);

                console.log(JSON.stringify(self.state));
                App.detachListeners();
                Quagga.stop();
                App.init();
            },
            inputMapper: {
                inputStream: {
                    constraints: function(value) {
                        if (/^(\d+)x(\d+)$/.test(value)) {
                            var values = value.split('x');
                            return {
                                width: {
                                    min: parseInt(values[0])
                                },
                                height: {
                                    min: parseInt(values[1])
                                }
                            };
                        }
                        return {
                            deviceId: value
                        };
                    }
                },
                numOfWorkers: function(value) {
                    return parseInt(value);
                },
                decoder: {
                    readers: function(value) {
                        if (value === 'ean_extended') {
                            return [{
                                format: "ean_reader",
                                config: {
                                    supplements: [
                                        'ean_5_reader', 'ean_2_reader'
                                    ]
                                }
                            }];
                        }
                        return [{
                            format: value + "_reader",
                            config: {}
                        }];
                    }
                }
            },
            state: {
                inputStream: {
                    type: "LiveStream",
                    constraints: {
                        width: {
                            min: 640
                        },
                        height: {
                            min: 480
                        },
                        aspectRatio: {
                            min: 1,
                            max: 100
                        },
                        facingMode: "environment" // or user
                    }
                },
                locator: {
                    patchSize: "medium",
                    halfSample: true
                },
                numOfWorkers: 2,
                frequency: 10,
                decoder: {
                    readers: [{
                        format: "code_128_reader",
                        config: {}
                    }]
                },
                locate: true
            },
            lastResult: null
        };
        $('#scan_bar_code').click(function() {
            $('#camera-div').removeClass('d-none');
            $('#camera-div').addClass('d-block');
            App.init();
        });

        $('#stop_camera').click(function() {
            $('#camera-div').addClass('d-none');
            $('#camera-div').removeClass('d-block');
            Quagga.stop();
        });
        $('#opener').click(function() {
            $('#camera-div').removeClass('d-none');
            $('#camera-div').addClass('d-block');
            App.init();
        });

        Quagga.onDetected(function(result) {
            var code = result.codeResult.code;
            Quagga.stop();

            getManifest(code)
        });
    });

    $('#start_seasion').click(function(e) {
        e.preventDefault();
        $('.enter-details').removeClass('d-none');
        $('.enter-details').addClass('d-block');
        alert('Seation Started')
    });

    $('#end_seasion').click(function(e) {
        e.preventDefault();
        $('.enter-details').removeClass('d-block');
        $('.enter-details').addClass('d-none');
        document.getElementById('number').value = 0;
        $('.counter').html('')
        alert('Seation ended')
    })

    $('.product_code').bind("input change", function(e) {
        var product_code = $('.product_code').val();
        getManifest(product_code)
        $('#select_id').val(product_code)
        $('.product_code').val('');
    })

    function getData() {
        var product_code_type = $('.product_code_type').val();
        getManifest(product_code_type)
        $('#select_id').val(product_code_type)
        $('.product_code_type').val('');
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

                } else if (data.code == '215') {
                    alert('Please scan these with package ID: ' + data.package_id);
                } else if (data.code == '304') {
                    $('#return_id').val(id)
                    getProductList(id)

                } else {
                    var result = confirm("Do you want add this to unknown list?");
                    if (result) {
                        $('.uknown-list').html(id)
                        $('#unknown-field').val(id)
                        $('#unknown-list').click()
                    }
                }
            }
        });
    }

    $('.remove_from_products').bind("input change", function(e) {
        var remove_from_products = $('.remove_from_products').val();
        $('#return_id').val(remove_from_products)
        $('.remove_from_products').val('');
    })

    function getProductList(id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo route('products-for-manifest') ?>',
            data: {
                '_token': '<?php echo csrf_token() ?>',
                'id': id
            },
            success: function(data) {

                if (data.code == '201') {
                    console.log(data);
                    $('.open-product-model').click();
                    var table = document.getElementById("myNewTable");
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

                    var result = confirm("Do you want add this to unknown list?");
                    if (result) {
                        $('.uknown-list').html(id)
                        $('#unknown-field').val(id)
                        $('#unknown-list').click()
                    }
                }
            }
        });
    }

    $('.return_to_manifest').click(function() {
        var return_id = $('#return_id').val();

        $.ajax({
            type: 'POST',
            url: '<?php echo route('remove-scanned-products') ?>',
            data: {
                '_token': '<?php echo csrf_token() ?>',
                'id': return_id
            },
            success: function(data) {
                if (data.code == '201') {
                    $('.close').click()
                    $('#product_code').focus();
                } else {
                    alert('Error')
                }
            }
        });
    });

    var i = 0;
    $('.add-unknown-id').click(function() {
        var select_id = $('#unknown-field').val();
        var save_as = $('input[name="unknownId"]:checked').val();
        i++;
        document.getElementById('number').value = i;
        $('.counter').html(document.getElementById('number').value)

        $.ajax({
            type: 'POST',
            url: '<?php echo route('import-scanned-products') ?>',
            data: {
                '_token': '<?php echo csrf_token() ?>',
                'id': select_id,
                'save_as': save_as,
                'unknown': true
            },
            success: function(data) {
                if (data.code == '201') {
                    alert(data.message);
                    $('.close').click()
                    $('#product_code').focus();
                } else {
                    alert('Error')
                }
            }
        });
    });

    $('.accept_products').click(function() {

        var select_id = $('#select_id').val();
        i++;
        document.getElementById('number').value = i;
        $('.counter').html(document.getElementById('number').value)

        $.ajax({
            type: 'POST',
            url: '<?php echo route('import-scanned-products') ?>',
            data: {
                '_token': '<?php echo csrf_token() ?>',
                'id': select_id
            },
            success: function(data) {

                if (data.code == '201') {
                    $('.close').click()
                    $('#product_code').focus();

                } else if (data.code == '909') {
                    alert(select_id + ' Scanned successfully, with product ID: ' + data.package_id + " Please copy this to a safe place..");
                    $('.close').click()
                    $('#product_code').focus();

                } else if (data.code == '910') {
                    alert(select_id + ' Scanned last time successfully, with product ID: ' + data.package_id + " Please copy this to a safe place..");
                    $('.close').click()
                    $('#product_code').focus();

                } else if (data.code == '707') {
                    alert(data.message);
                    $('.close').click()
                    $('#product_code').focus();
                } else {
                    alert('Error')
                }
            }
        });
    });

    $('.accept_products_to_claim_list').click(function() {
        var select_id = $('#select_id').val();
        i++;
        document.getElementById('number').value = i;
        $('.counter').html(document.getElementById('number').value)

        let description = prompt("Please enter claim desription");
        while (!description) {
            description = prompt("Please enter claim desription");
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo route('import-scanned-products') ?>',
            data: {
                '_token': '<?php echo csrf_token() ?>',
                'id': select_id,
                'claim_list': true,
                'description': description,
            },
            success: function(data) {
                if (data.code == '201') {
                    $('.close').click()
                    $('#product_code').focus();
                } else {
                    alert('Error')
                }
            }
        });
    });
</script>
@endsection