var map, polylines = [], markers = [], markerInfo = [];
var old = {};

var isMapFull = false;
var loc;

function saveLocation(element) {
	var loc = {};

	var item = jQuery(element).prev();
	loc.element = element;
	if (item.length) {
		loc.prev = item[0];
	} else {
		loc.parent = jQuery(element).parent()[0];
	}
	return (loc);
}

function restoreLocation(loc) {
	if (loc.parent) {
		jQuery(loc.parent).prepend(loc.element);
	} else {
		jQuery(loc.prev).after(loc.element);
	}
}

function doFullscreen() {
	//console.log("doFullscreen");
	var oBody = jQuery('body');
	var oHTML = jQuery('html');
	var oMap = jQuery('#map_canvas');

	console.log(oMap);
	var xc = Math.round(jQuery(window).width() / 2);
	var yc = Math.round(jQuery(window).height() / 2);

	if (!isMapFull) { // enter Fullscreen
		//console.log("doFullscreen enter");
		old.val = oMap.attr("style");
		old.body = oBody.attr("style");
		old.w = oMap.width();
		old.h = oMap.height();
		old.p = oMap.position();
		oldCenter = map.getCenter();
		old.scrolpos = oBody.scrollTop();
		old.scrolpos2 = oHTML.scrollTop();
		oBody.css("overflow", "hidden");
		oHTML.css("overflow", "hidden"); //ie7 fix
		oBody.scrollTop(0);
		oHTML.scrollTop(0);

		oMap.css({
			zIndex : 1000,
			position : 'fixed',
			top : '0px',
			left : '0px',
			width : '100%',
			height : '100%',
			margin : '0px',
			padding : '0px',
			border : '0px'
		});

		loc = saveLocation(oMap);
		oMap.appendTo("body");

		google.maps.event.trigger(map, 'resize');
		map.panTo(oldCenter);
		isMapFull = true;

	} else {
		oMap.css({
			position : 'relative',
			top : Math.round(old.p.top) + 'px',
			left : Math.round(old.p.left) + 'px',
			width : old.w + 'px',
			height : old.h + 'px'
		});

		restoreLocation(loc);
		//oMap.prependTo("#gpxMap");

		oBody.attr("style", old.body);
		oBody.css("overflow", "auto");
		oHTML.css("overflow", "auto"); //ie7 fix
		oMap.attr("style", old.val);
		oBody.scrollTop(old.scrolpos);
		oHTML.scrollTop(old.scrolpos2);
		google.maps.event.trigger(map, 'resize');
		map.panTo(oldCenter);
		isMapFull = false;

	}

}

function HomeControl(controlDiv, map) {

	// Set CSS styles for the DIV containing the control
	// Setting padding to 5 px will offset the control
	// from the edge of the map.
	controlDiv.style.padding = '5px';

	// Set CSS for the control border.
	var controlUI = document.createElement('div');
	controlUI.style.backgroundColor = 'white';
	controlUI.style.borderStyle = 'solid';
	controlUI.style.borderWidth = '1px';
	controlUI.style.borderColor = 'rgba(0, 0, 0, 0.3)';
	controlUI.style.borderRadius = '2px';
	controlUI.style.boxShadow = '0 1px 4px -1px rgba(0, 0, 0, 0.3)';
	
	controlUI.style.padding = '1px 6px';
	controlUI.style.cursor = 'pointer';
	controlUI.style.textAlign = 'center';
	controlUI.title = 'Click to change fullscreen mode';
	controlDiv.appendChild(controlUI);

	// Set CSS for the control interior.
	var controlText = document.createElement('div');
	controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
	controlText.style.fontSize = '11px';

	controlText.innerHTML = '<b>resize map</b>';
	controlUI.appendChild(controlText);

	// Setup the click event listeners: simply set the map to Chicago.
	google.maps.event.addDomListener(controlUI, 'click', function () {
		doFullscreen();
	});
}

function initialize() {

	map = new google.maps.Map(document.getElementById('map_canvas'), {
			zoom : 8,
			mapTypeControl : true,
			mapTypeControlOptions : {
				style : google.maps.MapTypeControlStyle.DROPDOWN_MENU,
				mapTypeIds : [google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.SATELLITE, google.maps.MapTypeId.HYBRID, google.maps.MapTypeId.TERRAIN, 'osmmapnik', 'cyclekarte', 'wanderwegekarte', 'wanderwegesat']
			},
			panControl : true,
			zoomControl : true,
			scaleControl : true,
			streetViewControl : true,
			mapTypeId : google.maps.MapTypeId.TERRAIN
			//mapTypeId: google.maps.MapTypeId.ROADMAP
		});

	// Generate extra map types
	osmmapnik = new google.maps.ImageMapType({
			getTileUrl : function (ll, z) {
				var X = ll.x % (1 << z);
				return "http://tile.openstreetmap.org/" + z + "/" + X + "/" + ll.y + ".png";
			},
			tileSize : new google.maps.Size(256, 256),
			isPng : true,
			maxZoom : 18,
			name : "OSM Mapnik",
			alt : "Open Streetmap Mapnik"
		});
	map.mapTypes.set('osmmapnik', osmmapnik);



	cyclekarte = new google.maps.ImageMapType({
			getTileUrl : function (ll, z) {
				var X = ll.x % (1 << z);
				return "http://c.tile.opencyclemap.org/cycle/" + z + "/" + X + "/" + ll.y + ".png";
			},
			tileSize : new google.maps.Size(256, 256),
			isPng : true,
			maxZoom : 18,
			name : "OSM Cycle",
			alt : "Open Streetmap Cycle"
		});
	map.mapTypes.set('cyclekarte', cyclekarte);

	wanderwegekarte = new google.maps.ImageMapType({
			getTileUrl : function (ll, z) {
				var X = ll.x % (1 << z);
				//var mapLayer="WMTS_Rainfall_BGR"; // Wasserläufe
				var mapLayer = "WMTS_TOPOMAP_APB-PAB"; //Hybrid Schummerung mit Wanderwege
				//var mapLayer="WMTS_HYBRIDMAP_APB-PAB"; // Hybrid Ortophoto mit Wanderwege
				//var mapLayer="WMTS_TRANSPORT-NETWORK_APB-PAB"; // nur Straßen
				//var mapLayer="WMTS_CONTOURLINES_APB-PAB"; // nur Höhenlinien
				//var mapLayer="WMTS_BASEMAP_APB-PAB"; // nur Schummerung
				//var mapLayer="WMTS_TRAILS_APB-PAB"; // nur Wanderwege
				//var mapLayer="WMTS_GEONAMES_APB-PAB"; // nur Ortsbezeichnungen und POIs

				return "http://sdi.provinz.bz.it/geoserver/gwc/service/wmts?SERVICE=WMTS&REQUEST=GetTile&VERSION=1.0.0&Layer=" + mapLayer + "&Style=default&Format=image/png8&TileMatrixSet=GoogleMapsCompatible&TileMatrix=GoogleMapsCompatible:" + z + "&TileRow=" + ll.y + "&TileCol=" + X;
			},
			tileSize : new google.maps.Size(256, 256),
			isPng : true,
			maxZoom : 20,
			name : "Wandern K",
			alt : "Wanderwege Südtirol Karte"
		});
	map.mapTypes.set('wanderwegekarte', wanderwegekarte);

	wanderwegesat = new google.maps.ImageMapType({
			getTileUrl : function (ll, z) {
				var X = ll.x % (1 << z);
				//var mapLayer="WMTS_Rainfall_BGR"; // Wasserläufe
				//var mapLayer="WMTS_TOPOMAP_APB-PAB"; //Hybrid Schummerung mit Wanderwege
				var mapLayer = "WMTS_HYBRIDMAP_APB-PAB"; // Hybrid Ortophoto mit Wanderwege
				//var mapLayer="WMTS_TRANSPORT-NETWORK_APB-PAB"; // nur Straßen
				//var mapLayer="WMTS_CONTOURLINES_APB-PAB"; // nur Höhenlinien
				//var mapLayer="WMTS_BASEMAP_APB-PAB"; // nur Schummerung
				//var mapLayer="WMTS_TRAILS_APB-PAB"; // nur Wanderwege
				//var mapLayer="WMTS_GEONAMES_APB-PAB"; // nur Ortsbezeichnungen und POIs
				return "http://sdi.provinz.bz.it/geoserver/gwc/service/wmts?SERVICE=WMTS&REQUEST=GetTile&VERSION=1.0.0&Layer=" + mapLayer + "&Style=default&Format=image/png8&TileMatrixSet=GoogleMapsCompatible&TileMatrix=GoogleMapsCompatible:" + z + "&TileRow=" + ll.y + "&TileCol=" + X;
			},
			tileSize : new google.maps.Size(256, 256),
			isPng : true,
			maxZoom : 20,
			name : "Wandern S",
			alt : "Wanderwege Südtirol Satellit"
		});
	map.mapTypes.set('wanderwegesat', wanderwegesat);

	// End generate extra map types


	// Create the DIV to hold the control and call the HomeControl() constructor
	// passing in this DIV.
	var homeControlDiv = document.createElement('div');
	var homeControl = new HomeControl(homeControlDiv, map);

	homeControlDiv.index = 1;
	map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);

	var sfOptions = {
		keepSpiderfied : true,
		nearbyDistance : 40,
		circleSpiralSwitchover : 15,
		legWeight : 2
	};
	var oms = new OverlappingMarkerSpiderfier(map, sfOptions);
	//var iw = new gm.InfoWindow();
	oms.addListener('click', function (marker, event) {
		//iw.setContent(marker.desc);
		//iw.open(map, marker);
	});

	var polypath = [],
	str_path = [];

	for (i in poly) {

		polypath[i] = [];
		for (l in poly[i].content) {
			polypath[i].push(new google.maps.LatLng(poly[i].content[l][0], poly[i].content[l][1]));
		}

		str_path[i] = [];
		for (l in poly[i].content) {
			str_path[i].push(poly[i].content[l][0] + ',' + poly[i].content[l][1]);
		}
		str_path[i] = str_path[i].join('|');
	}

	var staticMap = '<img width=200 height=200 src="http://maps.googleapis.com/maps/api/staticmap?path=color:0xff0000ff|weight:5|%%&size=200x200&sensor=false"/>';
	var infoBubble = new InfoBubble({
			maxWidth : 300
		});
	infoBubble.addTab('Details', '<div></div>');

	for (i in polypath) {
		polylines[i] = new google.maps.Polyline({
				path : polypath[i],
				strokeColor : '#ff0000',
				strokeWeight : 2,
				map : map
			});
		markers[i] = new google.maps.Marker({
				position : polypath[i][0],
				title : poly[i].title,
				map : map,
				draggable : true,
				raiseOnDrag : false,
				animation : google.maps.Animation.DROP,
				icon : iconBase + markerIcons[poly[i].icon]
			});
		markers[i].info = poly[i].title;
		markers[i].idx = i;

		oms.addMarker(markers[i]);
		google.maps.event.addListener(markers[i], 'click', markerClick);

	}

	function markerClick() {
		var staticMapN = staticMap.replace('%%', str_path[this.idx]);
		var viewLink = '<p><a href="' + poly[this.idx].url + '">' + poly[this.idx].title + '</a> - <a href="' + poly[this.idx].atturl + '">Download</a><br/><a href="#" onclick="return zoomhere(' + this.idx + ');">Zoom</a></p>';
		var content = staticMapN.toString() + viewLink;
		infoBubble.updateTab(0, 'Details', content);
		infoBubble.open(map, this);
	}

	function zoomhere(i) {
		infoBubble.close();
		map.setCenter(markers[i].position);
		map.setZoom(12);
		return false;
	}

	map.setCenter(markers[0].position);

	//dimizu markerclusterer
	var mcOptions = {
		gridSize : 50,
		maxZoom : 12
	};
	var markerCluster = new MarkerClusterer(map, markers, mcOptions);

}

google.maps.event.addDomListener(window, 'load', initialize);