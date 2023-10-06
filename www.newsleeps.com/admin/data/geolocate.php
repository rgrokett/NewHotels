<?php
// THIS UPDATES ALL THE LAT/LONG SETTINGS FOR THE HOTELS 
// FOR PLOTTING ON GOOGLE MAPS
// // 2019-06-03 PHP 7.0 MySQLi


// DB Connection
require_once("dbconnect.php");


define("MAPS_HOST", "maps.googleapis.com");
define("KEY", "YourGoogleAPIkey");

echo "<HTML><BODY>\n";
echo "<PRE>\n";
echo "Loading Geo Location data\n";

// Select all blank the rows in the markers table
$query = "SELECT * FROM new_hotels WHERE lat = 0.0";

$result = mysqli_query($conn,$query);
if (!$result) {
  die("Invalid query: " . mysqli_error($conn));
}

// Initialize delay in geocode speed
$delay = 200000;
$base_url = "https://".MAPS_HOST."/maps/api/geocode/json?sensor=false&key=".KEY;

// Iterate through the rows, geocoding each address
while ($row = @mysqli_fetch_assoc($result)) {
  $geocode_pending = true;

  while ($geocode_pending) {
    $address = $row['address1']." ".$row['city'].",".$row['state_prov']." ".$row['postal_code'];
    $id = $row['id'];

    $request_url = $base_url . "&address=" . urlencode($address);

	echo "($id) $request_url\n";

    $json = file_get_contents($request_url) or die("url not loading");

    $resp = json_decode($json, true);
    $status = $resp['status'];
    if (strcmp($status, "OK") == 0) {
      // successful geocode
	  $lat = $resp['results'][0]['geometry']['location']['lat'];
	  $lng = $resp['results'][0]['geometry']['location']['lng'];
      $geocode_pending = false;

	  echo "   $lat,$lng\n";

      $query = sprintf("UPDATE new_hotels " .
             " SET lat = '%s', lng = '%s' " .
             " WHERE id = %s LIMIT 1;",
             mysqli_real_escape_string($conn,$lat),
             mysqli_real_escape_string($conn,$lng),
             mysqli_real_escape_string($conn,$id));
      $update_result = mysqli_query($conn,$query);
      if (!$update_result) {
        die("Invalid query: " . mysqli_error($conn));
      }
    } else if (strcmp($status, "OVER_QUERY_LIMIT") == 0) {
      // sent geocodes too fast
      $delay += 100000;
    } else {
      // failure to geocode
      $geocode_pending = false;
      echo "Address " . $address . " failed to geocoded. ";
      echo "Received status " . $status . "
\n";
    }
    usleep($delay);
  }
}

echo "<BR>FINISHED</PRE></BODY></HTML>\n";

?>
