<?php
// THIS UPDATES THE EAN HOTELID FOR THE HOTELS 
// FOR USE WITH Expedia Booking
// 2019-02-24 Speed up due to Cloudflare 100 sec timeout
// // 2019-06-03 PHP 7.0 MySQLi

ini_set("memory_limit","512M");

// DB Connection
require_once("dbconnect.php");

$sec = time();

echo "<HTML><BODY>\n";
echo "<PRE>\n";
echo "Loading EAN HotelID data\n";

// Select all blank rows in the hotels table
$query = "SELECT * FROM new_hotels WHERE hotelid is null";

$result = mysqli_query($conn,$query);
if (!$result) {
  die("Invalid query: " . mysqli_error($conn));
}

// LOAD THE EAN HOTEL DATA FILE INTO MEMORY
$filename = "Hotel_All_Active.csv";
$recarray = file($filename, FILE_IGNORE_NEW_LINES);

// Iterate through the DB rows 
// Match using ZIP and Street Number
// Update rec if hotel id found
while ($row = @mysqli_fetch_assoc($result)) 
{
	set_time_limit(10);
    $address1 = strtoupper($row['address1']);
    list($streetnum) = explode(' ',$row['address1']);
	$zip = $row['postal_code'];
    $id = $row['id'];
	echo "($id) [".$zip."--".$row['address1']."] = ";

	# SEARCH FILE FOR MATCHING ZIP CODE
    $arr_zips = preg_grep("/\|$zip\|/",$recarray);

	while (list($row,$s_data) = @each($arr_zips))
	{
		$found = "NONE";
        $data = explode('|',$s_data);

		$F_HOTELID =  $data[0];
		$F_STREETNUM =  $data[1];
		$F_ZIP	= $data[3];

	    // LOOK FOR ZIP MATCHES	
        if (strcmp($zip, $F_ZIP) == 0) {
         // NOW CHECK ADDRESS
         if (strcmp($streetnum, $F_STREETNUM) == 0) {
	  	    // FOUND ADDRESS MATCH
	 	    $found = "$F_HOTELID"; 
	  
      	    $query = sprintf("UPDATE new_hotels " .
             " SET hotelid = '%s' " .
             " WHERE id = %s LIMIT 1;",
             mysqli_real_escape_string($conn,$F_HOTELID),
             mysqli_real_escape_string($conn,$id));
      	    $update_result = mysqli_query($conn,$query);
      	    if (!$update_result) {
        	    die("Invalid query: " . mysqli_error($conn));
      	    }
		    break;
         } 
	    }
    }
	echo "[".$found."]\n";

}
echo "\n\nFINISHED.\n";
echo "</PRE></BODY></HTML>\n";
exit;

?>
