<?php 
// WORLD MAP
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

    var default_latlng = new google.maps.LatLng(0, 0);
    Center = new Object();

    //mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}

    function initialize() {
	Center.lat = default_latlng.lat();
	Center.lng = default_latlng.lng();
	Center.radius = 10000;

        map = new google.maps.Map(document.getElementById("map-canvas"), {
          center: new google.maps.LatLng(Center.lat, Center.lng),
	  gestureHandling: 'greedy',
          zoom: 0
        });
        infoWindow = new google.maps.InfoWindow();

	
  	searchLocationsNear(Center);

     }  // end initialize()
  

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

     var searchUrl = 'worldgenXML.php?lat=' + center.lat + '&lng=' + center.lng + '&radius=' + center.radius;
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
         var country = markerNodes[i].getAttribute("country");
         var open_date = markerNodes[i].getAttribute("open_date");
         var hotelid = markerNodes[i].getAttribute("hotelid");
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

         createMarker(latlng, name, hurl, address, city, country, open_date, hotelid, expedia);
         bounds.extend(latlng);
       }
       map.fitBounds(bounds);
      });
    }

    function createMarker(latlng, name, hurl, address, city, country, open_date, hotelid, expedia) {
      var html = "<font size='-1'><a href='"+hurl+"' target='_blank' style='text-decoration: underline;'><b>"+name+"</b></a>"+expedia+"<br/>"+address+"<br/>"+city+", "+country+"<br /><i><strong>open: "+open_date+"</strong><i></font>";
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
       console.log("setRadiusFromMap radius=" + proxmity);
       return (proxmity);
    }

    // PARSE XML MESSAGE FROM REPLY
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

    function doNothing() {}


    google.maps.event.addDomListener(window, 'load', initialize);	

</script>

