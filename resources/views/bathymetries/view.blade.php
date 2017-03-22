@extends('layouts.app')

<style type="text/css">

</style>

@section('content')
<div ng-controller="BathymetryController">
    <div id="map"></div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
	//Variables
    map = new L.Map('map', {
        editable: true,
        minZoom:6,
        maxZoom:10,
        maxBounds:[[29.305561325527698, -98.87695312500001],[2.4601811810210052, -46.58203125000001]]
    }).setView(new L.LatLng(14, -70),6);;
    var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    var osmUrl_grid = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    var googleUrl = 'http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}';
    osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors';
    osm = L.tileLayer(osmUrl, {maxZoom: 18, attribution: osmAttrib});
    google = L.tileLayer(googleUrl, {maxZoom: 18, attribution: 'google'});
    drawnItems = L.featureGroup().addTo(map);
    L.control.layers({
            'Google': google.addTo(map),
            'OpenStreetMap':osm.addTo(map) 
        }, 
        {
            'Seleccion':drawnItems
        }, 
        { 
            position: 'topleft', collapsed: false
        }).addTo(map);

    //Dialog
    var dialogContent = ["<h2>Descargar el area seleccionada</h2>", "<p>Seleccione las opciones de descarga a continuacion:","<div class='form-group'><label>Formato de archivo:</label><select class='form-control' id='format'><option value='xyz'>XYZ</option><option value='csv'>CSV</option><option value='img'>Grafico</option></select></div>", '<div class="form-group"><button class="btn btn-primary btn-block" id="link-btn">Obtener enlace de descarga</button></div>', '<div id="link-container"><p>Este es su enlace de descarga:</p><p><a href="" id="link" target="_blank"></a></p></div>'].join('');
    var dialogOptions = {
    	size: [300, 460],
    	initOpen: false
    };
    var dialog = L.control.dialog(dialogOptions).setContent(dialogContent).addTo(map);
    dialog.freeze();
    //Graticule
    L.latlngGraticule({
        showLabel: true,
        weight:1,
        opacity: 2,
        color:"#999",
        fontColor: "#000",
        zoomInterval: [
            {start: 2, end: 3, interval: 30},
            {start: 4, end: 4, interval: 10},
            {start: 5, end: 7, interval: 5},
            {start: 8, end: 10, interval: 1}
        ]
    }).addTo(map);
    //Coordinates
    L.control.coordinates({
        position:"bottomleft", //optional default "bootomright"
        decimals:4, //optional default 4
        decimalSeperator:".", //optional default "."
        labelTemplateLat:"Latitud: {y}", //optional default "Lat: {y}"
        labelTemplateLng:"Longitud: {x}", //optional default "Lng: {x}"
        enableUserInput:false, //optional default true
        useDMS:false, //optional default false
        useLatLngOrder: true, //ordering of labels, default false-> lng-lat
        markerType: L.marker, //optional default L.marker
    }).addTo(map);
    //Controls

    L.control.custom({
        position: 'topright',
        content: '<div class="form-group"><label>Lat-i</label><input type="text" placeholder="Lat-i" class="form-control input-sm" ng-model="lat1" id="lat1"></div>'
    }).addTo(map);

    L.control.custom({
        position: 'topright',
        content: '<div class="form-group"><label>Lat-f</label><input type="text" placeholder="Lat-f" class="form-control input-sm" ng-model="lat2" id="lat2"></div>'
    }).addTo(map);

    L.control.custom({
        position: 'topright',
        content: '<div class="form-group"><label>Lon-i</label><input type="text" placeholder="Lon-i" class="form-control input-sm" ng-model="lon1" id="lon1"></div>'
    }).addTo(map);

    L.control.custom({
        position: 'topright',
        content: '<div class="form-group"><label>Lon-f</label><input type="text" placeholder="Lon-f" class="form-control input-sm" ng-model="lon2" id="lon2"></div>'
    }).addTo(map);

    L.control.custom({
        position: 'topright',
        content: '<div class="form-group"><label>Angulo</label><input type="text" placeholder="Angulo" class="form-control input-sm" ng-model="angle" id="angle"></div>'
    }).addTo(map);

    L.control.custom({
        position: 'topright',
        content: '<div class="form-group""><button class="btn btn-primary btn-xs btn-block" id="action-btn">Descargar datos</button></div>'
    }).addTo(map);

    L.EditControl = L.Control.extend({

        options: {
            position: 'topleft',
            callback: null,
            kind: '',
            html: ''
        },

        onAdd: function (map) {
            var container = L.DomUtil.create('div', 'leaflet-control leaflet-bar'),
                link = L.DomUtil.create('a', '', container);

            link.href = '#';
            link.title = 'Crear un nuevo ' + this.options.kind;
            link.innerHTML = this.options.html;
            L.DomEvent.on(link, 'click', L.DomEvent.stop)
                      .on(link, 'click', function () {
                        window.LAYER = this.options.callback.call(map.editTools);
                      }, this);

            return container;
        }

    });

    L.NewRectangleControl = L.EditControl.extend({

        options: {
            position: 'topleft',
            callback: newRectangle,
            kind: 'rectangle',
            html: '⬛'
        }

    });

    map.addControl(new L.NewRectangleControl({

        edit: {
            featureGroup: drawnItems,
            poly : {
              allowIntersection : false
            }
          },
        draw: {
            polygon : {
              allowIntersection: false,
              showArea:true, 

            }
          }
        }
    ));


    //Events

    map.on('editable:drawing:start', function(e) {
        drawnItems.clearLayers();
    });
    map.on('editable:drawing:move', function(e) {  
        //var coordinates = e.layer.getLatLngs()[0];
        // console.log(coordinates);
        rotate_call(e);
    });

    /*map.on('editable:drawing:end', function(e) {      
      rotate_call(e);
      var layer = e.layer;
      drawnItems.addLayer(layer);
      global_polygon.transform.enable();
      global_polygon.dragging.enable();
      global_polygon.on("transformed", rotate_call);
     
    });*/

    map.on('editable:drawing:end', function(e) {      
        var coordinates = e.layer.getLatLngs()[0];
        var max_lat = Math.abs(coordinates[0].lat -  coordinates[2].lat);
        var max_lng = Math.abs(coordinates[1].lng - coordinates[3].lng);
    
        if(max_lat < 7 || max_lng < 7){ //si es mas pequeño de lo permitido ok
            rotate_call(e);
            var layer = e.layer;
            drawnItems.addLayer(layer);
            console.log(global_polygon);
            //global_polygon.disableEdit();
            global_polygon.transform.enable();
            global_polygon.dragging.enable();
            //global_polygon.on("rotateend", rotate_call);
            global_polygon.on("transformed", rotate_call);
            //dibujar marcador
            global_marker = new L.marker([global_polygon._bounds._southWest.lat, global_polygon._bounds._southWest.lng], {icon: greenIcon}).on('click', onClickMarker);
            global_marker.addTo(map);
        }else{
        //borrar rectangulo por que es mas grande de lo permitido
   
        map.removeLayer(global_polygon);
        alert("El tamaño del area es muy grande, intente con una mas pequeña")
    }
     
     
  });

    //Functions
    var global_polygon;
    function newRectangle(){
        for (var k in drawnItems._layers){
            drawnItems._layers[k].transform.disable();
            drawnItems._layers[k].dragging.disable(); 
        }
        drawnItems.clearLayers();
        if(typeof global_polygon != "undefined"){
            global_polygon.transform.disable();
        }
        global_polygon = map.editTools.startRectangle(null,{
        // interactive: true, 
            draggable: true, 
            transform: true, 
            color: "red", 
            weight: 1 
        });
    }

    function rotate_call(e){  
    	if(drawnItems.getLayers().length>0){
    		var coordinates = e.layer.getLatLngs()[0];
        	$("#angle").val(e.rotation);
        	$("#lat1").val(coordinates[0].lat);
        	$("#lon1").val(coordinates[1].lng);
        	$("#lat2").val(coordinates[2].lat);
        	$("#lon2").val(coordinates[3].lng);
    	}
    }

    $("#lat1, #lat2, #lon1, #lon2").on('input', function(e) {
     	//e.preventDefault();

     	if($("#lat1").val()!="" && $("#lat2").val()!="" && $("#lon1").val()!="" && $("#lon2").val()!="" ){
      		for (var k in drawnItems._layers){
        		drawnItems._layers[k].transform.disable();
        		drawnItems._layers[k].dragging.disable();
      		}
       		drawnItems.clearLayers();

        	// define rectangle geographical bounds
       		var bounds = [[$("#lat1").val(), $("#lon1").val()], [$("#lat2").val(), $("#lon2").val()]];
        	// add rectangle passing bounds and some basic styles
       		var polygon = L.rectangle(bounds,  { 
       			interactive: true,
				draggable: true, 
          		transform: true, 
          		color: "red",
          		weight: 1
          	}).addTo(drawnItems);
      
      		polygon.transform.enable();
       		polygon.dragging.enable();
       		polygon.on("transformstart",function(e){
        		rotate_call(e);
       		});
       		polygon.on("transformed",function(e){
        		rotate_call(e);
       		})
     	}
   });

    $('#action-btn').click(function(){
    	changeDialogLocation();
        dialog.open();
    });

    window.onresize	= function(){
    	changeDialogLocation();
    }

    function changeDialogLocation(){
    	var x = ($(window).width()/2)-150;
    	var y = ($(window).height()/2)-240;
    	dialog.setLocation([y,x]);
    }

    var baseApiUrl = "http://localhost:3000/";

    var bathymetryEndpoint = ""

    $('#link-btn').click(function(){
    	var url = baseApiUrl + bathymetryEndpoint + "?format="+ $('#format').val();
    	var coordinates = drawnItems.toGeoJSON().features[0].geometry.coordinates[0];
    	coordinates.forEach(function(c, i, array){
    		url = url + "&coords[]="+c;
    	});
    	dialog.setSize([300,460]);
    	$("#link").text(url);
    	$("#link").attr("href", url);
    	$("#link").css("word-wrap", "break-word");
    	console.log(url);
    });



</script>
@endsection