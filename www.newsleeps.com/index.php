<?php
// 2017-06-27 Added CANADA
// 2018-03-30 Added Rest-of-World
// 2018-12-17 PHP 7.0
// HEADER
require_once("header.php");
?>


<!-- HOME -->
  <div class="container fade-in fade-in-delay-12">
     <div class="row"> 
       <div class="col-sm-8 col-md-8">
		  <h3>Stay in the Newest Hotels and Resorts</h3>
			<p>Stay at nice, fresh places when you travel, 
			all <b>brand new</b> or <b>newly renovated</b> in the past year or so.
			&nbsp; Let <em>NEWSLEEPS</em> help you find something new!
			</p>

		  <!-- BASIC SEARCH -->
		  <div class="panel-footer">
		  <h4>Search for new Hotels <font size="-1"><i>-Now with Canada! (and Rest of World)</i></font></h4>
		     <form role="form" method="get" action="results.php" name="newsleep_search">
                <div class="form-group">
				  <?php
				  // SEARCH BY STATE/PROV
				  $sql = 'SELECT DISTINCT h.country,s.* FROM new_hotels h, states_provinces s'
        				  . ' WHERE s.state_prov = h.state_prov'
        				  . ' order by h.country,s.full_name';
				  $result = mysqli_query($conn,$sql);
				  ?>
                  <select class="form-control selectpicker show-menu-arrow" data-title="Select Location" style="display: none;" data-size="8" name="state" onChange="this.form.submit();">
					<option>Select Location</option>
					<option value='WORLD'>REST OF WORLD</option>
					<?php 
						while($row = mysqli_fetch_assoc($result)) 
						{ 
						       echo "<option value=".$row['state_prov'].">";
						       if ($row['country'] == "USA") { 
							   		echo ucfirst(strtolower($row['full_name']));
								}
							   else {
						       		echo strtoupper(substr($row['country'],0,3))." ".ucfirst(strtolower($row['full_name']));
								}
						       echo "</option>";
						}
						mysqli_free_result($result); 
					?>
                  </select>
                </div><!-- /.form-group -->	
				<br>

                <div class="form-group">
                    Or <button type="submit" name="browse" value="Browse Map" class="btn btn-danger">Browse Map</button>
				    Or <a href="search.php" class="btn btn-default">Advanced Search</a>
                </div><!-- /.form-group -->	

             </form>
		  </div><!-- /.panel-footer -->
			 <!-- END BASIC SEARCH -->

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
    <?php
    // MAIN SCREEN LIST OF NEW HOTELS opening in last 6 mo from now
    $sql = 'SELECT h.*, a.* FROM `new_hotels` h, affiliate_data a'
        . ' WHERE a.hotel_chain = h.hotel_chain'
        . ' AND (h.hotel_type = \'resort\' OR h.hotel_type = \'boutique\') '
		. ' and h.open_date between now() - interval 6 month  and now() + interval 6 month '
        . ' order by RAND()'
        . ' limit 6';

    $result = mysqli_query($conn,$sql);
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

