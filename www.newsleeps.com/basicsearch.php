		  <!-- BASIC SEARCH -->
		  <div class="panel-footer">
		  <h4>Search for new Hotels <font size="-1"><i>-Now with Canada!</i></font></h4>
		     <form role="form" method="get" action="results.php" name="newsleep_search">
                <div class="form-group">
				  <?php
				  // 2018-12-17 PHP 7.0
				  // SEARCH BY STATE/PROV
				  $sql = 'SELECT DISTINCT h.country,s.* FROM new_hotels h, states_provinces s'
        				  . ' WHERE s.state_prov = h.state_prov'
        				  . ' order by h.country,s.full_name';
				  $result = mysqli_query($conn,$sql);
				  ?>
                  <select class="form-control selectpicker show-menu-arrow" data-title="Select Location" style="display: none;" data-size="8" name="state" onChange="this.form.submit();">
					<option>Select Location</option>
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
             </form>
		  </div><!-- /.panel-footer -->
			 <!-- END BASIC SEARCH -->


				<br>
