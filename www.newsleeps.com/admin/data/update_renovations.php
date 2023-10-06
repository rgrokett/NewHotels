<?php
// THIS UPDATES HOTEL_TYPES FROM THE EAN RENOVATIONS DB 
// Run at a CGI 
// // 2019-06-03 PHP 7.0 MySQLi

ini_set("memory_limit","512M");

// DB Connection
require_once("dbconnect.php");

echo "<HTML><BODY>\n";
echo "<PRE>\n";
echo "Updating EAN Renovations Hotel Type\n"; 

// Select all hotel IDs
$query = "SELECT DISTINCT hotelid FROM new_hotels WHERE hotelid is not null ORDER BY hotelid";

$result = mysqli_query($conn,$query);
if (!$result) {
  die("Invalid query: " . mysqli_error($conn));
} 

// OPEN THE EAN RENOVATIONS DATA FILE
# EANHotelID|COUNTRY|DESC
$filename = "PropertyRenovationsList.csv";
$file_arr = get_csv($filename); 

// Iterate through the rows, Updating each record
// Match using EAN HOTEL ID
$i = 0;
while ($row = @mysqli_fetch_assoc($result)) 
{
    set_time_limit(10);
    $hotelid = $row['hotelid'];

    if (!array_key_exists($hotelid,$file_arr))
    {
		continue;
    }	
    $renov = $file_arr[$hotelid];

    if (!empty($renov))
    {
	  $F_HOTELID = $hotelid;

	  $found = "NO CHANGE";
	  if ($F_HOTELID == $hotelid)
	  {
			$found = $F_HOTELID;
			// Update NEW_HOTELS.HOTEL_TYPE 
      		$sql = sprintf("UPDATE new_hotels SET hotel_type = 'Newly Renovated' WHERE" .
       		" hotelid = '%s'" ,
       		mysqli_real_escape_string($conn,$F_HOTELID));

		    echo "$sql\n";

      		$res = mysqli_query($conn,$sql) or die(mysqli_error($conn));
      } 
      echo "[".$found."]\n";
    }

$i++;
}
echo "$i Data Records Updated!\n";
echo "<BR>FINISHED</PRE></BODY></HTML>\n";
exit;



// LOAD A CSV FILE
function get_csv($filename, $delim ="|"){ 

    $dump = array(); 
    
    $f = fopen ($filename,"r"); 
    $size = filesize($filename)+1; 
    while ($row = fgetcsv($f, $size, $delim)) { 
        $dump[$row[0]] = $row[2]; 
        unset($row);
    } 
    fclose ($f); 
    
    return $dump; 
} 

?>

