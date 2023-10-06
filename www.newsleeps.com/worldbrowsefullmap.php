<?php
// WORLD MAP
// HEADER
require_once("header.php");
?>


<!-- HOME -->
  <div class="fade-in fade-in-delay-12">
  <!-- Placing the map -->
  <div class="container">
    <div class="row">
       <div class="col-md-12 col-sm-12">
        <!-- Map-->
			 <div class="search-box">
			   <div class="col-md-4 col-sm-4"></div>
			   <div class="col-md-4 col-sm-4">
            	<button type="button" class="btn btn-danger btn-block" onclick="searchLocations()" value="Search">Click to Find New Hotels</button>
			  </div>
			</div>
  		  <div><select id="locationSelect" style="width:100%;visibility:hidden"></select></div>
  		  <div id="map-canvas" class="map_slider"></div> 

		  <div class="col-md-1 col-md-offset-11 col-sm-1 col-sm-offset-10 map-search-box">
            	<button type="button" class="btn btn-danger btn-xs" onclick="searchLocations()" value="Search">Click for Hotels</button>
          </div>
		  
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
			</div><!-- /.whiteboard -->
        </div> 

      </div><!-- /.col-md-12 --> 

    </div><!-- /.row home-->
  </div><!-- /.container --> 
  </div><!-- /.fade-in --> 


<?php
// MAP
include("worldmap.php");
?>

<?php
// FOOTER
require_once("footer.php");
?>

