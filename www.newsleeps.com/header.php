<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Find and stay in brand new hotels and resorts.">
	<meta name="keywords" content="new hotels,new hotel openings,new sleeps, new resorts,new openings, hotel grand openings, hotels, resorts, motels,reservations, travel" />
    <meta name="author" content="#">
    <link rel="shortcut icon" href="images/ico/favicon.png">
    <title>NewSleeps! Find New Places to Stay</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link type="text/css" href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" />
    <link href="css/bootstrap-select.css" rel="stylesheet">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <link href="css/custom.css" rel="stylesheet">

	<!-- Mobile Icons -->
	<link href="https://www.newsleeps.com/apple-touch-icon.png" rel="apple-touch-icon" />
	<link href="https://www.newsleeps.com/apple-touch-icon-152x152.png" rel="apple-touch-icon" sizes="152x152" />
	<link href="https://www.newsleeps.com/apple-touch-icon-167x167.png" rel="apple-touch-icon" sizes="167x167" />
	<link href="https://www.newsleeps.com/apple-touch-icon-180x180.png" rel="apple-touch-icon" sizes="180x180" />
	<link href="https://www.newsleeps.com/icon-hires.png" rel="icon" sizes="192x192" />
	<link href="https://www.newsleeps.com/icon-normal.png" rel="icon" sizes="128x128" />

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "http://schema.org",
      "@type": "Organization",
	  "name" : "NewSleeps",
      "url": "https://www.newsleeps.com",
      "logo": "https://www.newsleeps.com/images/newsleeps_logo.png",
	  "sameAs" : [
		"https://www.facebook.com/newsleeps",
		"https://www.pinterest.com/newsleeps/new-hotels-and-resorts/",
		"https://twitter.com/newsleeps"
		]
    }
    </script>
	
	<!-- GENERIC HOTEL IMAGE -->
	<script type="text/javascript">
	function ImgError(source){
    	source.src = "images/GENERIC.jpg";
    	source.onerror = "";
    	return true;
	}
	</script> 

  </head>

  <body> 	
	<?php
	// Report all errors except E_NOTICE
	error_reporting(E_ALL & ~(E_NOTICE|E_DEPRECATED));

	// DB Connection
	require_once("dbconnect.php");
	?>
  <!-- NAVBAR ================================================== -->
    <div class="navbar-wrapper fade-in fade-in-delay-02">
      <!-- Navbar -->
      <div class="container fade-in fade-in-delay-06">
        <div class="row">
          <div class="navbar navbar-default" role="navigation">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><img src="images/logo-newsleeps-white.png" alt="New sleeps" /></a>
            </div><!-- /.navbar-header -->
            <div class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                <li><a href="index.php">Home</a></li>
                <li><a href="browse.php">Browse Map</a></li>
                <li><a href="faq.php">FAQ</a></li>
                <li><a href="search.php">Search</a></li>
                <li><a href="about.php">About</a></li>
              </ul>
            </div><!-- /.navbar-collapse -->
          </div><!-- /.navbar-default -->
        </div><!-- /.row -->  
      </div><!-- /.container -->
    </div><!-- /.navbar-wrapper -->	
    <!-- End Navbar -->
    <!-- Property details image header -->
    <div class="header fade-in fade-in-delay-08">
    	<div class="details-property"></div>
    </div>
    <!-- /.image header -->

  <!-- END Header ============================================== -->


