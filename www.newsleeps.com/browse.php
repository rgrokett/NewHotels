<?php
	$in_browsestate = htmlspecialchars($_REQUEST["state"]);

	$in_browsestate = strtoupper($in_browsestate);

// HEADER
require_once("header.php");
?>


<!-- HOME -->
  <div class="container fade-in fade-in-delay-12">
   	<!-- Placing the map -->
    <div class="row">
        <!-- Map-->
        <div class="col-md-8 col-sm-8">
		<?php
		// BASIC SEARCH BLOCK
		include("basicsearch.php");
		?>
			<div class="search-box">
			  <div class="col-md-6">
            	<button type="button" class="btn btn-danger btn-block" onclick="searchLocations()" value="Search">Click to Show New Hotels</button>
			  </div>
			</div>
		  <p><div align="right" class="hidden-xs"><i class="fa fa-globe"></i><a href="browsefullmap.php?state=<?php echo $in_browsestate;?>">See Larger Map</a></div></p>
  		  <div><select id="locationSelect" style="width:100%;visibility:hidden"></select></div>
          <div id="map-canvas" class="map"></div>

		  <div class="col-md-2 col-md-offset-10 col-sm-2 col-sm-offset-9 map-search-box">
            	<button type="button" class="btn btn-danger btn-xs" onclick="searchLocations()" value="Search">Click for Hotels</button>
          </div>

	  <br>
	  <div class="col-md-6">
          <button type="button" class="btn btn-warning btn-block" onclick="showGoogleMap()" value="Search">Show ALL Hotels near here</button>
	  </div>
	  <br>
	  <br>
	
		  <!-- GOOGLE -->
			<br>
			<div class="whiteboard">
			<p>
			<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
			<!-- newsleeps2banner -->
			<ins class="adsbygoogle"
     			style="display:block"
     			data-ad-client="ca-pub-9977035341220560"
     			data-ad-slot="6679825216"
     			data-ad-format="auto"></ins>
			<script>
			(adsbygoogle = window.adsbygoogle || []).push({});
			</script>
		    </p>	
			</div>
		  
        </div> 

		<?php
		// FEATURED BLOCK
		include("featured.php");
		?>

    </div><!-- /.row home-->
  </div><!-- /.container --> 

<?php
// MAP
include("map.php");
?>

<?php
// FOOTER
require_once("footer.php");
?>

