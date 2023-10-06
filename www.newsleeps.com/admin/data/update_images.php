<?php
// THIS UPDATES THE EAN Photo URL FOR THE EAN HOTELS 
// Run at a CGI
// // 2019-06-03 PHP 7.0 MySQLi

ini_set("memory_limit","512M");

// DB Connection
require_once("dbconnect.php");


echo "<HTML><BODY>\n";
echo "<PRE>\n";
echo "Loading EAN Image data\n";

// Select all hotel IDs
$query = "SELECT DISTINCT id,hotelid FROM new_hotels WHERE hotelid is not null ORDER BY hotelid";

$result = mysqli_query($conn,$query);
if (!$result) {
  die("Invalid query: " . mysqli_error($conn));
}

// OPEN THE EAN HOTEL DATA FILE
# EANHotelID|URL
$filename = "HotelImageList.csv";
$file_arr = get_csv($filename);


// Iterate through the rows, Updating each address
// Match using ZIP and Street Number

while ($row = @mysqli_fetch_assoc($result)) 
{
    set_time_limit(10);
    $hotelid = $row['hotelid'];
    $id = $row['id'];

    if (!array_key_exists($hotelid,$file_arr))
    {
	echo "NO $hotelid FOUND\n";
	continue;
    }	
    $url = $file_arr[$hotelid];

    if (!empty($url))
    {
	$F_HOTELID = $hotelid;
	$F_URL	   = $url;

	$F_URL = rtrim($F_URL);

	$found = "NONE";
	if ($F_HOTELID == $hotelid)
	{
		$found = $F_HOTELID;
      		$query = sprintf("UPDATE new_hotels " .
       		" SET photo_url = '%s' " .
       		" WHERE id = %s LIMIT 1;",
       		mysqli_real_escape_string($conn,$F_URL),
       		mysqli_real_escape_string($conn,$id));

		#echo "$query\n";

      		$update_result = mysqli_query($conn,$query);
      		if (!$update_result) {
       			die("Invalid query: " . mysqli_error($conn));
      		}
      	} 
        echo "($id) [".$found."]\n";
    }

}

echo "<BR>FINISHED</PRE></BODY></HTML>\n";
exit;

// LOAD A CSV FILE into Associative array
function get_csv($filename, $delim ="|"){ 

    $dump = array();
    
    $f = fopen ($filename,"r"); 
    $size = 1024;
    while ($row = fgetcsv($f, $size, $delim)) { 
            $dump[$row[0]] = $row[1]; 
            unset($row);
    } 
    fclose ($f); 

    return $dump; 
} 




?>

