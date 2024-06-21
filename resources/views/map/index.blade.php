@extends('layouts.layout')

@section('title', '道路管理情報システム')

@section('css')
    <style>
        .scroll_bar {
            overflow-x: auto;
            overflow-y: auto;
        }

        .scroll_bar::-webkit-scrollbar {
            width: 6px;
            height: 10px;
        }

        .scroll_bar::-webkit-scrollbar-thumb {
            background-color: #888;
        }

        .scroll_bar::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }

        .scroll_bar::-webkit-scrollbar-track {
            background-color: #f1f1f1;
        }

        .cesium-widget {
            width: calc((var(--vw, 1vw) * 100) - var(--sidebar, 0px)) !important;
            height: calc(var(--vh, 1vh) * 100) !important;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            zoom: 100%;
        }

        #cesiumContainer {
            width: 100%;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        #mapContainer {
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        .button-group .btn_icon {
            display: none;
        }

        .button-group .btn_icon.show {
            display: inline-block;
        }

        .btn_hidden {
            opacity: 0;
            transition: opacity 0.5s ease-out;
        }

        #toolbar {
            position: absolute;
            top: 10px;
            left: calc(50px + var(--sidebar, 0px));
            background: whitesmoke;
        }
    </style>
@endsection
@section('content')
    @include('map.sidebar')
    <div id="mapContainer" class="height-100 bg-light relative">
        <div id="cesiumContainer"></div>
        <div id="toolbar">
            <div class="button-group">
                {{-- <button id="mainButton">Main Button</button> --}}
                {{-- <button class="btn_icon animate__animated">Button</button>
                <button class="btn_icon animate__animated">Button 2</button>
                <button class="btn_icon animate__animated">Button 3</button>
                <button class="btn_icon animate__animated">Button 3</button>
                <button class="btn_icon animate__animated">Button 3</button>
                <button class="btn_icon animate__animated">Button 3</button>
                <button class="btn_icon animate__animated">Button 3</button> --}}
{{-- 
                <button id="distance" type="button" class="cesium-button btn_icon animate__animated">Distance</button>
                <button id="component-Distance" type="button" class="cesium-button btn_icon animate__animated">Component
                    Distance</button>
                <button id="polyline-Distance" type="button" class="cesium-button btn_icon animate__animated">Polyline
                    Distance</button>
                <button id="horizontal-Distance" type="button" class="cesium-button btn_icon animate__animated">Horizontal
                    Distance</button>
                <button id="vertical-Distance" type="button" class="cesium-button btn_icon animate__animated">Vertical
                    Distance</button>
                <button id="height-From-Terrain" type="button" class="cesium-button btn_icon animate__animated">Height From
                    Terrain</button>
                <button id="area" type="button" class="cesium-button btn_icon animate__animated">Area</button>
                <button id="point" type="button" class="cesium-button btn_icon animate__animated">Point</button> --}}
            </div>
            <div>
                <button id="loadData1">Tileset</button>
                <button id="loadData2">Line</button>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('/js/app.js') }}"></script>
    <script>
        // Start Siderbar
        const sidebarWidth = 70; // Width when sidebar is collapsed
        const expandedSidebarWidth = 320; // Width when sidebar is expanded

        const setFullSize = () => {
            const vh = window.innerHeight * 0.01;
            const vw = window.innerWidth * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
            document.documentElement.style.setProperty('--vw', `${vw}px`);

            if ($('#navBodyItems').hasClass('d-none')) {
                document.documentElement.style.setProperty('--sidebar', `${sidebarWidth}px`);
            } else {
                document.documentElement.style.setProperty('--sidebar', `${expandedSidebarWidth}px`);
            }
        };

        window.addEventListener('resize', setFullSize);
        setFullSize();

        $(document).on('click', '#navMain a', function(e) {
            e.preventDefault();
            $('#navBodyItems').toggleClass('d-none');
            setFullSize(); // Update sizes
        });

        $(document).on('click', '#mainButton', function() {
            if ($('.button-group .btn_icon').not('#mainButton').hasClass('show')) {
                $('.button-group .btn_icon').not('#mainButton').each(function(index, element) {
                    $(element).delay(index * 100).queue(function(next) {
                        $(element).removeClass('show animate__fadeInLeft').addClass(
                            'animate__fadeOutRight');
                        next();
                    }).delay(500).queue(function(next) {
                        $(element).removeClass('animate__fadeOutRight').addClass('btn_hidden');
                        next();
                    });
                });
            } else {
                $('.button-group .btn_icon').not('#mainButton').each(function(index, element) {
                    $(element).removeClass('btn_hidden').delay(index * 100).queue(function(next) {
                        $(element).addClass('show animate__fadeInLeft');
                        next();
                    });
                });
            }
        });

        $(document).on('click', '#loadData1', async function() {
            const assetId = 847734;
            try {
                let tileset = viewer.scene.primitives._primitives.find(p => p.assetId === assetId);
                if (!tileset) {
                    tileset = await Cesium.Cesium3DTileset.fromIonAssetId(assetId);
                    viewer.scene.primitives.add(tileset);
                }
                await viewer.zoomTo(tileset);
            } catch (error) {
                console.log('error', error);
            }
        })

        $(document).on('click', '#loadData2', async function() {
            try {
                const resource = await Cesium.IonResource.fromAssetId(2629997);
                const dataSource = await Cesium.KmlDataSource.load(resource, {
                camera: viewer.scene.camera,
                canvas: viewer.scene.canvas,
            });
                await viewer.dataSources.add(dataSource);
                await viewer.zoomTo(dataSource);
            } catch (error) {
                console.log(error);
            }
        })


        // End Siderbar
        // // Start Distance
        // var scene = viewer.scene;
        // var units = new Cesium.MeasureUnits();
        // var primitives = scene.primitives.add(new Cesium.PrimitiveCollection());
        // var points = primitives.add(new Cesium.PointPrimitiveCollection());
        // var labels = primitives.add(new Cesium.LabelCollection());
        // var mouseHandler = new Cesium.MeasurementMouseHandler(scene);
        // var measurementOptions = {
        //     scene: scene,
        //     units: units,
        //     points: points,
        //     labels: labels,
        //     primitives: primitives
        // };
        // var componentOptions = Cesium.clone(measurementOptions);
        // componentOptions.showComponentLines = true;
        // var distanceMeasurement = new Cesium.DistanceMeasurement(measurementOptions);
        // var componentMeasurement = new Cesium.DistanceMeasurement(componentOptions);
        // var polylineMeasurement = new Cesium.PolylineMeasurement(measurementOptions);
        // var horizontalMeasurement = new Cesium.HorizontalMeasurement(measurementOptions);
        // var verticalMeasurement = new Cesium.VerticalMeasurement(measurementOptions);
        // var heightMeasurement = new Cesium.HeightMeasurement(measurementOptions);
        // var areaMeasurement = new Cesium.AreaMeasurement(measurementOptions);
        // var pointMeasurement = new Cesium.PointMeasurement(measurementOptions);

        // document.getElementById("distance").addEventListener("click", function() {
        //     if (mouseHandler.selectedMeasurement)
        //         mouseHandler.selectedMeasurement.reset()
        //     mouseHandler.selectedMeasurement = distanceMeasurement;
        // });
        // document.getElementById("component-Distance").addEventListener("click", function() {
        //     if (mouseHandler.selectedMeasurement)
        //         mouseHandler.selectedMeasurement.reset();
        //     mouseHandler.selectedMeasurement = componentMeasurement;
        // });
        // document.getElementById("polyline-Distance").addEventListener("click", function() {
        //     if (mouseHandler.selectedMeasurement)
        //         mouseHandler.selectedMeasurement.reset();
        //     mouseHandler.selectedMeasurement = polylineMeasurement;
        // });

        // document.getElementById("horizontal-Distance").addEventListener("click", function() {
        //     if (mouseHandler.selectedMeasurement)
        //         mouseHandler.selectedMeasurement.reset();
        //     mouseHandler.selectedMeasurement = horizontalMeasurement;
        // });
        // document.getElementById("vertical-Distance").addEventListener("click", function() {
        //     if (mouseHandler.selectedMeasurement)
        //         mouseHandler.selectedMeasurement.reset();
        //     mouseHandler.selectedMeasurement = verticalMeasurement;
        // });
        // document.getElementById("height-From-Terrain").addEventListener("click", function() {
        //     if (mouseHandler.selectedMeasurement)
        //         mouseHandler.selectedMeasurement.reset();
        //     mouseHandler.selectedMeasurement = heightMeasurement;
        // });

        // document.getElementById("area").addEventListener("click", function() {
        //     if (mouseHandler.selectedMeasurement)
        //         mouseHandler.selectedMeasurement.reset();
        //     mouseHandler.selectedMeasurement = areaMeasurement;
        // });
        // document.getElementById("point").addEventListener("click", function() {
        //     if (mouseHandler.selectedMeasurement)
        //         mouseHandler.selectedMeasurement.reset();
        //     mouseHandler.selectedMeasurement = pointMeasurement;
        // });
        // mouseHandler.selectedMeasurement = distanceMeasurement;
        // mouseHandler.activate();
        // End Distance


        // Start Measure

        const gl = viewer.scene.context._gl;
        const maxTextureSize = gl.getParameter(gl.MAX_TEXTURE_SIZE);
        console.log("Maximum Texture Size Supported:", maxTextureSize);

        // $(document).on('click', '#distance', async function() {
        //     try {
        //         const areaMeasure = new AreaMeasure(viewer, {
        //             onEnd: (entity) => {
        //                 console.log(entity); // 测量完成回调函数，返回测量结果
        //             },
        //         });
        //         areaMeasure.start();
        //     } catch (error) {
        //         console.error("Error while starting area measure:", error);
        //     }

        // });

        // End Measure
    </script>
@endsection
