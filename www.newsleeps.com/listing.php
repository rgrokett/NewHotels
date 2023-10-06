	<?php
	// 2018-08-28 added hotel_type renovated
	// 2018-12-17 PHP 7.0
	$row_cnt = mysqli_num_rows($result);

    $red = "<font color=\"red\">";
    $grn = "<font color=\"green\">";
    $blue = "<font color=\"blue\">";

	// SEARCH RESULTS
	if (strlen($in_state) > 0) 
		{$found = "<h4>Newest Hotels and Resorts in: $in_state </h4>";}
	else 
		{$found = "<h4>Newest Hotels and Resorts:</h4>";}

	// SEARCH HITS
	if ( $row_cnt > 49 )
	{
		$found = "${red}More than 50 hotels found.</font> You may wish to narrow your search.";
	}

	if ( $row_cnt == 0 )
	{
		$found = "No Matches! Try again?";
	}
	?>
	<!-- LISTING HEADER -->
          <?php echo $found; ?>
	    <!-- LISTINGS -->
    	  <?php
    	  $rownum = 0;
    	  while($row = mysqli_fetch_assoc($result)) { 
	    	  if ( $row['open_date'] > date('Y-m-d'))  
		    	  { $open_str = "$red Opening"; }
	    	  else
		    	  { $open_str = "$grn Opened"; }

	    	  if ( $row['hotel_type'] == "Newly Renovated")
		    	  { $open_str = "$grn Renovated"; }

	    	  $open_date = date('F, Y', strtotime($row['open_date']) );
			  $rownum = $rownum +1;
    	  ?>
		<div class="row">
          <div class="col-md-12">
            <div class="property-row">
              <div class="contract sale"><?php echo $row['hotel_type']; ?></div>
              <div class="thumbnail darken col-md-4 col-sm-5">
                <img class="img-thumbnail" src="<?php echo $row['photo_url']; ?>" alt="hotel photo" style="width:250px;height:200px" onerror="ImgError(this);"/>
                <a href="<?php echo $row['url']; ?>" target="_blank"><div class="imgcaption"></div></a>
              </div>
              <div class="col-md-8 col-sm-7">
                <div class="property-info">
                  <a href="<?php echo $row['url']; ?>" target="_blank">
                    <h3><?php echo $row['name']; ?></h3>
                  </a>
                  <?php 
					echo "<p class='text-muted'>".$row['website']."<br>";
					echo "".$row['address1']."<br>";
					echo "".$row['city'].", ".$row['state_prov']." ".$row['postal_code']." ".$row['country']."";
					echo "<br>".$row['phone']."</p>";
					echo "<p>";
					// EXPEDIA COMENCIA
					if ($row['hotelid'] > 0 ) 
					{
						echo "<a href=\"https://newsleeps.comencia.com/hotel/".$row['hotelid']."\" target=\"_blank\">".$blue."Check Prices</font></a>";
					}
					// END EXPEDIA COMENCIA
					echo "&nbsp;&nbsp;&nbsp;<a href=\"".$row['map_url']."\" target=\"_blank\">".$blue."Show MAP</font></a></p>";
					echo "<p class='text-primary'><strong>".$open_str.": ".$open_date."</font></strong></p>";
					?>
                </div><!-- ./property-info --> 
              </div><!-- ./col-md-8 col-sm-7 -->
            </div><!-- ./property-row -->
          </div><!-- ./col-md-12 -->
        </div><!-- /.row listing-->
		  <?php
		  } // end while
			mysqli_free_result($result); 
		  ?>

        </div><!-- ./col-md-8 col-sm-8 -->
		<!-- /LISTINGS -->

