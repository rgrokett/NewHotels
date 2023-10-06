<?php
// HEADER
require_once("header.php");
?>

    <!-- Two columns below the header -->
    <div class="container fade-in fade-in-delay-12">
    	<div class="row">
        <div class="col-md-8 col-sm-8">
        	<div class="page-header" align="left">
            <h1>Contact us</h1>
			<h4>- Send Comments or add a new hotel!</h4>
          </div>
			<p><b>Know of a newly opened or opening soon place to stay?</b></p>
			<p><b>Add it to NewSleeps!</b>  Just point us to their website or<br>
			location and we will check it out!
			</P>
			<p>
			Submit Hotels, Motels, Resorts, Bed &amp; Breakfast; any place<br>
			that has opened in the past year or is opening sometime in<br>
			the near future.
			</p>
			<p>
			<strong>or feel free to send us comments!</strong><br>
			Include your email address if you wish to have a reply.
			</p>
			<br />
		  
          <!-- Form - JUSTMAIL -->
          <form role="form" method="get" action="/cgi-bin/justmail" name="add_hotel">
            <div class="row">
              <div class="form-group col-md-6 col-sm-6">
                <label for="inputEmail3" class="control-label">Website URL:</label>
                <input type="text" class="form-control" placeholder="www.new_hotel.com" name="WEBURL">
              </div>
              <div class="form-group col-md-6 col-sm-6">
                <label for="inputEmail3" class="control-label">Hotel Name:</label>
                <input type="text" class="form-control" id="inputEmail3" placeholder="Name" name="HOTELNAME">
              </div>
              <div class="form-group col-md-6 col-sm-6">
                <label for="inputEmail3" class="control-label">City,State or ZIP Code:</label>
                <input type="text" class="form-control" id="inputEmail3" placeholder="" name="HOTELLOC">
              </div>
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Comments</label>
              <textarea class="form-control" rows="6" name="COMMENTS"></textarea>
            </div>
			<INPUT TYPE="hidden" NAME="redirect" VALUE="http://www.newsleeps.com/thanks.php">
			<INPUT type='hidden' name='subject' value='NewSleeps Comment'>
            <button type="submit" name="SUBMIT" class="btn btn-primary pull-right">Send</button>
          </form>
          <!-- End Form -->
          <div class="spacer"></div>
        </div><!-- col-md-8 form contact--> 


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

