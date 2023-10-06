<?php
// HEADER
// 2018-01-17 Added FUTURE SEARCH
// 2018-03-31 Added Rest of World
// 2018-12-17 PHP 7.0
require_once("header.php");
?>


<!-- HOME -->
  <div class="container fade-in fade-in-delay-12">
     <div class="row"> 
       <div class="col-sm-8 col-md-8">

		  <!-- ADVANCED SEARCH -->
		  <h4>Search for new Hotels: <small><strong>Select any of the following</strong></small></h4>
		     <form role="form" method="get" action="results2.php" name="search">
                <div class="form-group">
				  <?php
				  // SEARCH BY STATE/PROV
				  $sql = 'SELECT DISTINCT h.country,s.* FROM new_hotels h, states_provinces s'
        				  . ' WHERE s.state_prov = h.state_prov'
        				  . ' order by h.country,s.full_name';
				  $result = mysqli_query($conn,$sql);
				  ?>
                  <select class="form-control selectpicker show-menu-arrow" style="display: none;" data-size="8" name="state">
					<option value="ALL">Select Location</option>
					<option value="ALL">ALL US/Canada</option>
					<option value="WORLD">REST OF WORLD</option>
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

                <div class="form-group">
				  <?php
				  // SEARCH BY HOTEL CHAIN
				  $sql = 'SELECT DISTINCT h.hotel_chain FROM new_hotels h'
        				  . ' order by h.hotel_chain';
				  $result = mysqli_query($conn,$sql);
				  ?>
                  <select class="form-control selectpicker show-menu-arrow" style="display: none;" name="hotel_chain">
                    <option value="ALL">Hotel Chain</option>
					<option value="ALL">ALL</option>
					<?php while($row = mysqli_fetch_assoc($result)) { ?>
						<option value="<?php echo $row['hotel_chain'];?>">
						<?php echo ucfirst(strtolower($row['hotel_chain']));?>
						</option>
					<?php
						}
						mysqli_free_result($result); 
					?>
                  </select>
                </div><!-- /.form-group -->	

                <div class="form-group">
				  <?php
				  // SEARCH BY HOTEL TYPE
				  $sql = 'SELECT DISTINCT h.hotel_type FROM new_hotels h'
        				  . ' order by h.hotel_type';
				  $result = mysqli_query($conn,$sql);
				  ?>
                  <select class="form-control selectpicker show-menu-arrow" style="display: none;" name="hotel_type">
                    <option value="ALL">Hotel Type</option>
					<option value="ALL">ALL</option>
					<?php while($row = mysqli_fetch_assoc($result)) { ?>
						<option value="<?php echo $row['hotel_type'];?>">
						<?php echo ucfirst(strtolower($row['hotel_type']));?>
						</option>
					<?php
						}
						mysqli_free_result($result); 
					?>
                  </select>
                </div><!-- /.form-group -->

			    <div class="form-group">
                  <label class="col-lg-2 control-label">Opened</label>
                  <div class="col-lg-10">
                    <div class="radio">
                      <label>
                        <input type="radio" name="opened" id="optionsRadios1" value="ALL" checked="">
                        Past Year or so
                      </label>
                    </div>
                    <div class="radio">
                      <label>
                        <input type="radio" name="opened" id="optionsRadios2" value="6">
                        Past 6 Months
                      </label>
                    </div>
                    <div class="radio">
                      <label>
                        <input type="radio" name="opened" id="optionsRadios3" value="3">
                        Past 3 Months
                      </label>
                    </div>
                    <div class="radio">
                      <label>
                        <input type="radio" name="opened" id="optionsRadios4" value="FUTURE">
                        Coming Soon
                      </label>
                    </div>
                  </div><!-- /.col-lg-10 -->
                </div><!-- /.form-group -->

                <button type="submit" class="btn btn-danger btn-block">Filter now</button>
             </form>

		  <br>

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

		</div><!-- /.col-md-8 -->

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

