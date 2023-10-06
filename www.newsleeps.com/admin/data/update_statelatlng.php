#!/usr/bin/php
// UPDATE THE LAT/LNG FIELD IN STATE_PROVINCE TABLE
// // 2019-06-03 PHP 7.0 MySQLi

<?php
// DB Connection
require_once("dbconnect.php");

echo "<PRE>";

// OPEN CSV FILE and read
$filename = "state_latlng.csv";
$recarray = get_csv($filename);

// FOR EACH RECORD in ARRAY
$i = 0; 
while (list($row,$data) = @each($recarray))
{
	// Skip comment lines
	if ( substr(trim($data[0]),0,1) == "#") { continue; }

	$STATE=trim(addslashes($data[0]));
	$LAT=trim(addslashes($data[1]));
	$LNG =trim(addslashes($data[2]));


echo "STATE=[$STATE] ";
echo "LAT=[$LAT]\t";
echo "LNG=[$LNG]\n";


// UPDATE a row of information into the table 
$sql ="UPDATE states_provinces SET LAT='$LAT', LNG='$LNG' WHERE STATE_PROV='$STATE'";

$res = mysqli_query($conn,$sql) or die(mysqli_error($conn));  

$i++;
}
echo "$i Data Records Updated!\n";
echo "</PRE>";
exit;



// LOAD A CSV FILE
function get_csv($filename, $delim =","){ 

    $row = 0; 
    $dump = array(); 
    
    $f = fopen ($filename,"r"); 
    $size = filesize($filename)+1; 
    while ($data = fgetcsv($f, $size, $delim)) { 
        $dump[$row] = $data; 
        //echo $data[1]."\n"; 
        $row++; 
    } 
    fclose ($f); 
    
    return $dump; 
} 

?>

