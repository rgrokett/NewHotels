<!-- FEATURED -->
<?php
// FEATURED NEW HOTEL
// 2018-01-17 Added Future Search
// 2018-06-07 Added Rest of World & Rmv USA
// 2018-09-03 Added Top Destinations
// 2018-12-17 PHP 7.0

$sql = 'SELECT date_format(h.open_date,\'%M %Y\') as OPENED,h.*, a.* FROM `new_hotels` h, affiliate_data a'
        . ' WHERE a.hotel_chain = h.hotel_chain'
        . ' AND h.hotel_type = \'resort\' '
		. ' AND h.open_date between now() - interval 6 month  and now() '
        . ' order by RAND()'
        . ' limit 1';

$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($result); 
?>

		<div class="col-md-4 col-sm-4 col-xs-12">
		  <h3>Top Destinations</h3>
		  <div class="side card">
            	      <div class="thumbnail col-md-4 col-sm-12 col-xs-4">
			  <a href="<?php echo $row['url']; ?>" target="_blank">
			  <img class="img-responsive" alt="featured hotel" src="<?php echo $row['photo_url']; ?>" onerror="ImgError(this);"/></a>
            	      </div>
                      <div class="col-md-8 col-sm-12 col-xs-8 mini">
		   	  <a href="results.php?state=NV&city=Las Vegas" class="btn btn-info btn-block" ><span style="font-size:larger;">Las Vegas</span></a>
		   	  <a href="results.php?state=CA&city=Los Angeles" class="btn btn-warning btn-block" ><span style="font-size:larger;">Los Angeles</span></a>
		   	  <a href="results.php?state=FL&city=Orlando" class="btn btn-danger btn-block" ><span style="font-size:larger;">Orlando</span></a>
		   	  <a href="results.php?state=NY&city=New York City" class="btn btn-success btn-block" ><span style="font-size:larger;">New York City</span></a>
                      </div><!-- /.col-md-8 mini -->
                </div><!--/.side card -->

		  <!-- ADVANCED SEARCH -->
		  <h4>Search for new Hotels</h4>
		  <div class="sidebar-search-box">
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
          </div><!--/.sidebar-search-box -->

		  <!-- GOOGLE -->
		  <div class="side card">
            <div class="col-md-8 col-sm-12 col-xs-8 mini">
				<p>
				<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
				<!-- newsleeps2featured -->
				<ins class="adsbygoogle"
     				style="display:block"
     				data-ad-client="ca-pub-9977035341220560"
     				data-ad-slot="5981821216"
     				data-ad-format="auto"></ins>
				<script>
				(adsbygoogle = window.adsbygoogle || []).push({});
				</script>	
				</p>	
			</div>
          </div><!--/.side card -->


          <!-- Social follow us --->
          <h3>Follow us</h3>
          <span class="follow-social"><a href="https://www.facebook.com/newsleeps"><i class="fa fa-facebook"></i></a></span>
          <span class="follow-social"><a href="https://twitter.com/intent/tweet?button_hashtag=newsleeps"><i class="fa fa-twitter"></i></a></span>
          <span class="follow-social"><a href="#"><i class="fa fa-google"></i></a></span>
          <span class="follow-social"><a href="https://www.pinterest.com/newsleeps/"><i class="fa fa-pinterest"></i></a></span>
          <!-- /.Social follow us --->

        </div><!--/.col-xs-12 -->
<!-- /FEATURED -->

