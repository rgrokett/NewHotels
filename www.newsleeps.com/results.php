<?php 
// 2017-06-27 Updated for Canada
// 2018-03-30 Added Rest of World
// 2018-12-17 PHP 7.0
// INCOMING FORM VARIABLES
	$in_country = htmlspecialchars($_REQUEST["state"]);
	$in_city = htmlspecialchars($_REQUEST["city"]);
	$in_state = htmlspecialchars($_REQUEST["state"]);
	$in_browse = htmlspecialchars($_REQUEST["browse"]);
	$in_browsestate = htmlspecialchars($_REQUEST["browsestate"]);
	$in_browsecountry = htmlspecialchars($_REQUEST["browsestate"]);

	$in_country = strtoupper($in_country);
	$in_city = strtoupper($in_city);
	$in_state = strtoupper($in_state);
	$in_browse = strtoupper($in_browse);
	$in_browsestate = strtoupper($in_browsestate);
	$in_browsecountry = strtoupper($in_browsecountry);

// REST OF WORLD (redirect)
	if ("$in_state" == "WORLD")
	{
		header("Location: worldbrowsefullmap.php");
		exit;
	}

// CONVERT "CANADA" to default center of Canada ("Manatoba")
    if ("$in_country" == "CANADA") 
	{ 
		$in_state = "ON"; 
		$in_browsestate = $in_state; 
	}
	
// REDIRECT IF NEEDED
	if (($in_browse == "BROWSE") ||
	    ($in_browse == "BROWSE MAP")) {
		if ($in_browsestate != "") {
		   header("Location: browsefullmap.php?state=$in_browsestate");
		} else {
		   header("Location: browsefullmap.php");
		}
		exit;
	}
?>

<?php
// HEADER
require_once("header.php");
?>

<script type="text/javascript">
function SubmitState() {
		
	var newstate = document.getElementById('state').value;
	document.getElementById('browsestate').value=newstate;
	document.forms["newsleep_search"].submit();
	}
</script>


<!-- HOME -->
  <div class="container fade-in fade-in-delay-12">
     <div class="row"> 
       <div class="col-sm-8 col-md-8">

		  <!-- BASIC SEARCH -->
		  <h4>Search for new Hotels</h4>

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
			<!-- END GOOGLE -->

			<!-- MAP ICON -->
            <div class="property-row">
              <div class="thumbnail darken col-md-4 col-sm-5">
                  <a href="browsefullmap.php?state=<?php echo $in_state;?>&city=<?php echo $in_city;?>">
                    <img class="img-thumbnail" src="images/map_icon.png" alt="map icon" style="width:150px;height:100px"/>
				  </a>
              </div>
              <div class="col-md-8 col-sm-7">
                <div class="property-info">
                  <a href="browsefullmap.php?state=<?php echo $in_state;?>&city=<?php echo $in_city;?>">
                    <h3>View Results on Map</h3>
                  </a>
                </div><!-- ./property-info --> 
              </div><!-- ./col-md-8 col-sm-7 -->
            </div><!-- ./property-row -->
			<!-- END MAP ICON -->

			
    <?php
	// HANDLE CANADA (Returns all hotels in canada)
	if ("$in_country" == 'CANADA') 
	{
	$sql = sprintf("SELECT h.*, a.* FROM new_hotels h, affiliate_data a WHERE a.hotel_chain = h.hotel_chain AND h.country LIKE '%s' order by h.state_prov",
			mysqli_real_escape_string($conn,"%"."$in_country"."%")
			);

	}
	else 
	{
    // MAIN SCREEN LIST OF NEW HOTELS 
	$sql = sprintf("SELECT h.*, a.* FROM new_hotels h, affiliate_data a WHERE a.hotel_chain = h.hotel_chain AND h.state_prov LIKE '%s' AND h.city LIKE '%s' order by h.city",
			mysqli_real_escape_string($conn,"%"."$in_state"."%"),
			mysqli_real_escape_string($conn,"%"."$in_city"."%")
			);
	}

     // DEBUG print "<p>SQL=$sql</p>";

    $result = mysqli_query($conn,$sql);
	$row_cnt = mysqli_num_rows($result);
    ?>
		<?php
		// LISTINGS BLOCK
		include("listing.php");
		?>

		<?php
		// FEATURED BLOCK
		include("featured.php");
		?>

    </div><!-- /.row home-->
  </div><!-- /.container --> 

<?php
// FOOTER
require_once("footer.php");
?>

