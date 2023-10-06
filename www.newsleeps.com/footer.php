

    <!-- Footer -->
    <div id="footer">
      <div class="container">
      	<div class="row">
          <div class="col-md-12">
                <img src="images/logo-newsleeps-white.png" alt="New sleeps" class="img-responsive footer-logo"/>
				<ul class="list-unstyled district">
				  <li><i class="fa fa-envelope-o"></i><a class="text-danger" href="contact.php">Add a new Hotel or Comment</a></li>
				</ul>
            	<p>See the <a href="about.php">About</a> page for &nbsp;important notes before booking your next vacation.<br />
				For travel reservations and for more information, see our travel partners, above. <br>All trademarks &copy; their respective owners.
				</p>
          </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
      </div><!-- /.container -->
      <!-- FOOTER -->
      <footer>
      	<div class="container">
        	<a href="#" class="scrollup"><i class="fa fa-2x fa-angle-double-up"></i></a>
        	<p class="pull-right"><a href="#">Back to top</a></p>
        	<p>&copy; 2021 GNU License &middot; <a href="privacy.php">Privacy</a> &middot; <a href="warranty.php">Terms</a></p>
        </div>
      </footer>
    </div><!-- /.#footer -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-2.1.0.min.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery-ui-1.10.4.custom.min.js" type="text/javascript"></script>
    <script src="js/bootstrap-select.js"></script>
    <script>
		/* Popover */
			$('#example').popover({html:true,});
			$('#example2').popover({html:true,});
			$('#example3').popover({html:true,});
			$('#example4').popover({html:true,});
			$('#example5').popover({html:true,});
			$('#example6').popover({html:true,});
			$('#example7').popover({html:true,});
			$('#example8').popover({html:true,});
			$('#example9').popover({html:true,});
			$('#example10').popover({html:true,});
			$('#example11').popover({html:true,});
			$('#example12').popover({html:true,});
    
    </script>
    <script>
		/* Search Box */
		$('.selectpicker').selectpicker();

		/* Slider range */
        $(function() {
            $( "#slider-range" ).slider({
              range: true,
              min: 10000,
              max: 1000000,
              step: 500,
              values: [ 200000, 400000 ],
              slide: function( event, ui ) {
                $( "#amount" ).val( "€ " + ui.values[ 0 ] + " - € " + ui.values[ 1 ] );
                $( "#amount_min" ).val( "€ " + ui.values[ 0 ] );
                $( "#amount_max" ).val( "€ " + ui.values[ 1 ] );
              }
            });
            $( "#amount" ).val( "€ " + $( "#slider-range" ).slider( "values", 0 ) +
              " - € " + $( "#slider-range" ).slider( "values", 1 ) );
            $( "#amount_min" ).val( "€" + $( "#slider-range" ).slider( "values", 0 ));
            $( "#amount_max" ).val( "€" + $( "#slider-range" ).slider( "values", 1 ));
        });
    </script>
    <script type="text/javascript">
	/* Scroll to top */
        $(document).ready(function(){ 
            $(window).scroll(function(){
                if ($(this).scrollTop() > 200) {
                    $('.scrollup').fadeIn();
                } else {
                    $('.scrollup').fadeOut();
                }
            }); 
            $('.scrollup').click(function(){
                $("html, body").animate({ scrollTop: 0 }, 600);
                return false;
            });
      
        });
		/* Topbar hide on scroll*/
        $(document).scroll(function () {
            var y = $(this).scrollTop();
            if (y > 200) {
                $('.topbar').slideUp(150);
            } else {
                $('.topbar').slideDown(150);
            }
        });
    </script>
	<!-- ADDETECT -->
<script>  
  window.onload = function() {   
    setTimeout(function() { 
      var ad = document.querySelector("ins.adsbygoogle");
      if (ad && ad.innerHTML.replace(/\s/g, "").length == 0) {
        if (typeof ga !== 'undefined') {
            ga('send', 'event', 'Adblock', 'Yes', {'nonInteraction': 1}); 
        } else if (typeof _gaq !== 'undefined') {
            // Log a non-interactive event in old Google Analytics
            _gaq.push(['_trackEvent', 'Adblock', 'Yes', undefined, undefined, true]);
        }
      }
    }, 2000);
  }; 
</script>
<!-- Userway.org ACCESSIBILITY -->
<script>(function(d){var s = d.createElement("script");s.setAttribute("data-account", "YourAccount");s.setAttribute("src", "https://cdn.userway.org/widget.js");(d.body || d.head).appendChild(s);})(document)</script><noscript>Please ensure Javascript is enabled for purposes of <a href="https://userway.org">website accessibility</a></noscript>

  </body>
</html>
