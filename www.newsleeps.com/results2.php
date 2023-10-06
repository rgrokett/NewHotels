<?php 
// 2017-06-27 ADDED CANADA
// 2018-01-17 ADDED Future search
// 2018-03-31 ADDED Rest of World
// 2018-09-03 ADDED View on Map
// 2018-12-17 PHP 7.0
// INCOMING FORM VARIABLES
	$in_country = htmlspecialchars($_REQUEST["country"]);
	$in_state = htmlspecialchars($_REQUEST["state"]);
	$in_hotel_chain = htmlspecialchars($_REQUEST["hotel_chain"]);
	$in_hotel_type = htmlspecialchars($_REQUEST["hotel_type"]);
	$in_opened = htmlspecialchars($_REQUEST["opened"]);
	$in_browse = htmlspecialchars($_REQUEST["browse"]);
	$in_browsestate = htmlspecialchars($_REQUEST["browsestate"]);

	$in_country = strtoupper($in_country);
	$in_state = strtoupper($in_state);
	$in_hotel_chain = strtoupper($in_hotel_chain);
	$in_hotel_type = strtoupper($in_hotel_type);
	$in_opened = strtoupper($in_opened);
	$in_browse = strtoupper($in_browse);
	$in_browsestate = strtoupper($in_browsestate);

// REST OF WORLD (redirect)
	if ("$in_state" == "WORLD")
	{
		header("Location: worldbrowsefullmap.php");
		exit;
	}
	
// REDIRECT IF NEEDED
	if (($in_browse == "BROWSE") ||
	    ($in_browse == "BROWSE MAP")) {
		if ($in_browsestate != "") {
		   header("Location: browse.php?state=$in_browsestate");
		} else {
		   header("Location: browse.php");
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
    <?php
$search_str = "Country: $in_country<br>"
			. "State..: $in_state<br>"
			. "Chain..: $in_hotel_chain<br>"
			. "Type...: $in_hotel_type<br>";

if ( "$in_country" == "ALL") { $in_country = ""; }
if ( "$in_state" == "ALL") { $in_state = ""; }
if ( "$in_hotel_chain" == "ALL") { $in_hotel_chain = ""; }
if ( "$in_hotel_type" == "ALL") { $in_hotel_type = ""; }
if ( "$in_opened" == "ALL") { $in_opened = "23"; }

if ( "$in_opened" == "FUTURE") { $datesearch = "AND h.open_date between now() and (now() + interval 6 month)"; }
else { $datesearch = "AND h.open_date between now() - interval %5\$s month  and now()"; }

if ( "$in_state" > "")  { $orderby = "h.city"; }
else { $orderby = "h.name"; }

$search_str .= "Opened.: past $in_opened months<br>";

// MAIN SCREEN LIST OF NEW HOTELS
$string = <<<THEQUERY
SELECT h.*, a.* FROM new_hotels h, affiliate_data a 
WHERE a.hotel_chain = h.hotel_chain 
AND h.country LIKE '%1\$s' 
AND h.state_prov LIKE '%2\$s' 
AND h.hotel_chain LIKE '%3\$s' 
AND h.hotel_type LIKE '%4\$s' 
$datesearch
order by $orderby
LIMIT 50
THEQUERY;

$sql = sprintf($string,
			mysqli_real_escape_string($conn,"%"."$in_country"."%"),
			mysqli_real_escape_string($conn,"%"."$in_state"."%"),
			mysqli_real_escape_string($conn,"%"."$in_hotel_chain"."%"),
			mysqli_real_escape_string($conn,"%"."$in_hotel_type"."%"),
			mysqli_real_escape_string($conn,"$in_opened"),
			mysqli_real_escape_string($conn,"$orderby")
			);

//DEBUG print "<p>SQL=$sql</p>";

$encsql = bin2hex($sql);

    $result = mysqli_query($conn,$sql);
	$row_cnt = mysqli_num_rows($result);

?>

			<!-- MAP ICON -->
            <div class="property-row">
              <div class="thumbnail darken col-md-4 col-sm-5">
                  <a href="browsefullmap.php?sql=<?php echo $encsql;?>">
                    <img class="img-thumbnail" src="images/map_icon.png" alt="map icon" style="width:150px;height:100px"/>
				  </a>
              </div>
              <div class="col-md-8 col-sm-7">
                <div class="property-info">
                  <a href="browsefullmap.php?sql=<?php echo $encsql;?>">
                    <h3>View Results on Map</h3>
                  </a>
                </div><!-- ./property-info --> 
              </div><!-- ./col-md-8 col-sm-7 -->
            </div><!-- ./property-row -->
			<!-- END MAP ICON -->

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

