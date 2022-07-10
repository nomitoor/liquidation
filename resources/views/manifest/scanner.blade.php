@extends('layouts/contentLayoutMaster')

@section('title', 'DataTables')


@section('content')
<div class="card">
    <div class="row">
        <div class="col-lg-6 col-xl-6 col-sm-12 col-md-4 mt-2 mb-2 ml-2">
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
@endsection

@section('page-script')
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
    $(function() {
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

        App.init();

        Quagga.onDetected(function(result) {
            var code = result.codeResult.code;
            Quagga.stop();

            window.location.href = "scan-results?package_id=" + code;

            console.log(code);
        });
    });
</script>
@endsection