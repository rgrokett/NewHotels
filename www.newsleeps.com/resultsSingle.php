<?php 
// 2018-12-17 PHP 7.0
// INCOMING FORM VARIABLES
	$in_hotelid = htmlspecialchars($_REQUEST["hotelid"]);
	$in_hotelid = strtoupper($in_hotelid);

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
<?php /* comment out form
		     <form role="form" method="get" action="results.php" name="newsleep_search">
                <div class="form-group">
				  <?php
				  // SEARCH BY STATE/PROV
				  $sql = 'SELECT DISTINCT s.* FROM new_hotels h, states_provinces s'
        				  . ' WHERE s.state_prov = h.state_prov'
        				  . ' order by s.full_name';
				  $result = mysqli_query($conn,$sql);
				  ?>
                  <select class="form-control selectpicker show-menu-arrow" data-title="Select Location" style="display: none;" data-size="8" name="state" onChange="this.form.submit();">
					<option>Select Location</option>
					<?php while($row = mysqli_fetch_assoc($result)) { ?>
						<option value="<?php echo $row['state_prov'];?>">
						<?php echo ucfirst(strtolower($row['full_name']));?>
						</option>
					<?php
						}
						mysqli_free_result($result); 
					?>
                  </select>
                </div><!-- /.form-group -->	

				<input type="hidden" id="browsestate" name="browsestate" value="<?php echo $in_state;?>">

                <div class="form-group">
                    Or <button type="submit" name="browse" value="Browse Map" class="btn btn-danger">Browse Map</button>
                    Or <button type="button" name="search" onclick="window.history.back();" class="btn btn-default">Advanced Search</button>
                </div><!-- /.form-group -->	

             </form>
end comment */ ?>
			
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
$search_str = "HotelID: $in_hotelid<br>";

if ( "$in_hotelid" == "") { $in_hotelid = "00000"; }

// MAIN SCREEN LIST OF NEW HOTELS
$string = <<<THEQUERY
SELECT h.*, a.* FROM new_hotels h, affiliate_data a 
WHERE h.hotelid LIKE '%1\$s' 
order by h.name
LIMIT 1
THEQUERY;

$sql = sprintf($string,
			mysqli_real_escape_string($conn,"$in_hotelid")
			);

//DEBUG print "<p>SQL=$sql</p>";


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

