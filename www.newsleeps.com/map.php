<?php 
// Fix for Geolocation
// 2018-08-28 added hotel_type
// 2018-09-03 added View on Map
// 2018-09-03 added City (lat/lng)
// 2018-11-03 fix for geolocation
// 2018-12-17 PHP 7.0
//
// INCOMING FORM VARIABLES
	$in_state = htmlspecialchars($_REQUEST["state"]);
	$in_sql   = htmlspecialchars($_REQUEST["sql"]);
	$in_city  = htmlspecialchars($_REQUEST["city"]);

	$in_state = strtoupper($in_state);

// CITY LAT/LNG
	$arr_city = array(
    		'LAS VEGAS' => '36.02|-115.18',
    		'LOS ANGELES' => '34.05|-118.25',
    		'ORLANDO' => '28.54|-81.38',
    		'NEW YORK CITY' => '40.71|-74.00');

if ( "$in_state" == "ALL") { $in_state = ""; }

if ( "$in_sql" > "") { $in_state = "KS"; }

// QUERY FOR LAT/LONG OF STATE
	$sql = "SELECT lat, lng FROM states_provinces where state_prov='$in_state' LIMIT 1";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);

	$state_selected = $in_state;
	$state_lat = $row['lat'];
	$state_lng = $row['lng'];
	mysqli_free_result($result); 	
//
// Use CITY Lat/Lng 
$city_selected = "";
if ( "$in_city" > "" ) {
	$lat_lng = explode("|",$arr_city["$in_city"]);
	$state_lat = $lat_lng[0];
	$state_lng = $lat_lng[1];
	$city_selected = $in_city;
}

?>

<script type="text/javascript"
    src="https://maps.google.com/maps/api/js?key=AIzaSyCD2OBUpOgEwQ8GXwpaKvQN_kyFfQWebzM&libraries=geometry,places">
</script>


<script type="text/javascript">
	var map;
	var markers = [];
	var infoWindow;
        var initialLocation;
        var blue = "<font color=\"blue\">";
        var red = "<font color=\"red\">";
        var grn = "<font color=\"green\">";

        var default_latlng = new google.maps.LatLng(39, -80);
	var default_radius = 150;
	var default_zoom = 6;

	var state_selected = "<?php Print($state_selected); ?>";
	var city_selected = "<?php Print($city_selected); ?>";
	var sqlstr = "<?php Print($in_sql); ?>";

	Center = new Object();

    //mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}

    function initialize() {
		Center.lat = default_latlng.lat();
		Center.lng = default_latlng.lng();
		Center.radius = default_radius;

      map = new google.maps.Map(document.getElementById("map-canvas"), {
        center: new google.maps.LatLng(Center.lat, Center.lng),
        zoom: 5,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
	gestureHandling: 'greedy'
      });
      infoWindow = new google.maps.InfoWindow();

      // View on Map
      if (sqlstr) {
	      default_radius = 2000;
	      default_zoom = 9;
      }
      // City 
      if (city_selected) {
	      default_radius = 25;
	      default_zoom = 3;
      }

  	  // CENTER ON STATE 
  	  if (state_selected) {
		var tmp_lat = "<?php Print($state_lat); ?>";
		var tmp_lng = "<?php Print($state_lng); ?>";
    		var state_latlng = new google.maps.LatLng(tmp_lat, tmp_lng);
    		initialLocation = state_latlng;
    		map.setCenter(initialLocation);
		map.setZoom(default_zoom);
		Center.lat = tmp_lat;
		Center.lng = tmp_lng;
		Center.radius = default_radius;
		console.log("Using State Lat/Long");
		console.log("lat/lng:"+Center.lat+"/"+Center.lng);
  	  	searchLocationsNear(Center);
  	
  	  // CENTER ON USER LOCATION
  	  // Try HTML5 Geolocation (Preferred)
  	  } else if(navigator.geolocation) {
    		browserSupportFlag = true;
		console.log("Using HTML5 Geolocation");
    		navigator.geolocation.getCurrentPosition(function(position) {
	  	    Center.lat = position.coords.latitude;
	  	    Center.lng = position.coords.longitude;
	  	    Center.radius = 150;
		    console.log("Position lat:"+position.coords.latitude);
      		    initialLocation = new google.maps.LatLng(Center.lat,Center.lng);
      		    map.setCenter(initialLocation);
		    map.setZoom(6);
		    console.log("lat/lng:"+Center.lat+"/"+Center.lng);
  	  	    searchLocationsNear(Center);
    		}, function() {
      		    handleNoGeolocation(browserSupportFlag);
    	   	});

  	  } else {
    		browserSupportFlag = false;
    		handleNoGeolocation(browserSupportFlag);
  	  }


     //google.maps.event.addListener(map, "bounds_changed", function() { searchLocations(); });


  }  // end initialize()
  

  function handleNoGeolocation(errorFlag) {
        initialLocation = default_latlng;
        map.setCenter(initialLocation);
	Center.lat = default_latlng.lat();
	Center.lng = default_latlng.lng();
	Center.radius = 150;
	console.log("I can't find your location at the moment");
  	searchLocationsNear(Center);
  }

   // UPDATE NEW SEARCH LOCATION
   function searchLocations() {
	var radius = setRadiusFromMap();

   	Center.lat = map.getCenter().lat(); 
   	Center.lng = map.getCenter().lng();  
   	Center.radius = radius;
	console.log("lat/lng=" + Center.lat + "/" + Center.lng);
	console.log("radius =" + Center.radius);
        searchLocationsNear(Center);
     };

   function clearLocations() {
     infoWindow.close();
     for (var i = 0; i < markers.length; i++) {
       markers[i].setMap(null);
     }
     markers.length = 0;

   }

   function searchLocationsNear(center) {
         clearLocations(); 

	 center.lat = roundNumber(center.lat,2);
	 center.lng = roundNumber(center.lng,2);

         var searchUrl = 'genXML.php?lat=' + center.lat + '&lng=' + center.lng + '&radius=' + center.radius + '&sql=' + sqlstr;
	 console.log(searchUrl);
         downloadUrl(searchUrl, function(data) {
         var xml = parseXml(data);
         var markerNodes = xml.documentElement.getElementsByTagName("marker");
	 if (markerNodes.length < 1) {
		console.log("Nothing close by, try wider area");
		return;
	 }
         var bounds = new google.maps.LatLngBounds();
	   
         for (var i = 0; i < markerNodes.length; i++) {
             var name = markerNodes[i].getAttribute("name");
             var hurl = markerNodes[i].getAttribute("hurl");
             var address = markerNodes[i].getAttribute("address");
             var city = markerNodes[i].getAttribute("city");
	     var state = markerNodes[i].getAttribute("state");
             var hotel_type = markerNodes[i].getAttribute("hotel_type");
             var open_date = markerNodes[i].getAttribute("open_date");
             var hotelid = markerNodes[i].getAttribute("hotelid");
             var distance = parseFloat(markerNodes[i].getAttribute("distance"));
             var latlng = new google.maps.LatLng(
                  parseFloat(markerNodes[i].getAttribute("lat")),
                  parseFloat(markerNodes[i].getAttribute("lng")));

			

	     // EXPEDIA COMENCIA CHECK PRICE
	     var expedia = "";
	     if (hotelid > 0 ) 
	     {
		  expedia = "<br /><a href=\"https://newsleeps.comencia.com/hotel/"+hotelid+"\" target=\"_blank\">"+blue+"Check Prices</font></a>";
	     }
	     // END EXPEDIA COMENCIA

         createMarker(latlng, name, hurl, address, city, state, hotel_type, open_date, hotelid, expedia);
         bounds.extend(latlng);
       }
       map.fitBounds(bounds);
      });
    }

    function createMarker(latlng, name, hurl, address, city, state, hotel_type, open_date, hotelid, expedia) {
      var open = "open";
      if (hotel_type == "Newly Renovated") {
	      open = "renovated";
      }

      var html = "<font size='-1'><a href='"+hurl+"' target='_blank' style='text-decoration: underline;'><b>"+name+"</b></a>"+expedia+"<br/>"+address+"<br/>"+city+", "+state+"<br /><i><strong>"+open+": "+open_date+"</strong><i></font>";
      var marker = new google.maps.Marker({
        map: map,
        position: latlng
      });
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
      google.maps.event.addListener(marker,'mouseover',function(){
        infoWindow.setContent(html);
        infoWindow.open(map,marker);
      });

      markers.push(marker);
    }


    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request.responseText, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

	// Watches for map bounds change - if so - set radius and refresh data Start
	function setRadiusFromMap() {
    	var bounds = map.getBounds();

    	// Then the points
    	var cnPoint = bounds.getCenter();
    	var nePoint = bounds.getNorthEast();

    	// Now, each individual coordinate
    	var ceLat = cnPoint.lat();
    	var ceLng = cnPoint.lng();
    	var neLat = nePoint.lat();
    	var neLng = nePoint.lng();

    	var proximitymeter = google.maps.geometry.spherical.computeDistanceBetween(cnPoint, nePoint);
    	var proximitymiles = proximitymeter * 0.000621371192;
    	var proxmity = proximitymiles;
		if ( proxmity > 150 ) { proxmity = 150; }  // Set max search area
		console.log("setRadiusFromMap radius=" + proxmity);
		return (proxmity);
	}


    function parseXml(str) {
      if (window.ActiveXObject) {
        var doc = new ActiveXObject('Microsoft.XMLDOM');
        doc.loadXML(str);
        return doc;
      } else if (window.DOMParser) {
        return (new DOMParser).parseFromString(str, 'text/xml');
      }
    }

	function roundNumber(num, dec) {
		var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
		return result;
	}

    // Open full Google Map with all hotels
    function showGoogleMap() {
	    var mylat = map.getCenter().lat(); var mylng = map.getCenter().lng();
	    var myzoom = map.getZoom();
	    mapURL = "https://www.google.com/maps/search/hotels/@"+mylat+","+mylng+","+myzoom+"z";
	    window.open(mapURL,'_blank');
    }	    
	
	    function doNothing() {}



    google.maps.event.addDomListener(window, 'load', initialize);	

</script>

