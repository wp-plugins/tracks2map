var map, polylines = [], markers = [], markerInfo = [];

function initialize() {
	
	map = new google.maps.Map(document.getElementById('map_canvas'), {
		zoom : mapZoom,
		mapTypeId : google.maps.MapTypeId.TERRAIN
	});
	
	var polypath = [], str_path = [];
	
	for (i in poly) {
		
		polypath[i] = [];
		for (l in poly[i].content ) {
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
			animation : google.maps.Animation.DROP,
			icon: iconBase + markerIcons[poly[i].icon]
		});
		markers[i].info = poly[i].title;
		markers[i].idx = i;
		
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
}

google.maps.event.addDomListener(window, 'load', initialize);
