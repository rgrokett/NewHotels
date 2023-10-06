#!/usr/bin/php

<?php
// DB Connection
// // 2019-06-03 PHP 7.0 MySQLi
require_once("dbconnect.php");

echo "<PRE>";

// OPEN CSV FILE and read
$filename = "affiliate_data.csv";
$recarray = get_csv($filename);

// DELETE OLD DATA
	$sql="truncate table affiliate_data";
	$res=mysqli_query($conn,$sql) or die("3211321231");		
	

// FOR EACH RECORD in ARRAY
$i = 0; 
while (list($row,$data) = @each($recarray))
{
	// Skip comment lines
	if ( substr(trim($data[0]),0,1) == "#") { continue; }

	$ID=trim(addslashes($data[0]));
	$HOTEL_CHAIN=trim(addslashes($data[1]));
	$NEW_OPENINGS_URL =trim(addslashes($data[2]));
	$SCRIPT_ID =trim(addslashes($data[3]));
	$LAST_VERIFIED_DATE =trim(addslashes($data[4]));
	$AFFILIATE_URL =trim(addslashes($data[5]));
	$LOGIN =trim(addslashes($data[6]));
	$PWD =trim(addslashes($data[7]));


// Skip any header
if (strcasecmp($NAME,"name") == 0) { continue; }

// FIX DATES
if ($LAST_VERIFIED_DATE == "null") { $LAST_VERIFIED_DATE = date('Y-m-d'); }


echo "ID=[$ID]\n";
echo "HOTEL_CHAIN=[$HOTEL_CHAIN]\n";
echo "LAST_VERIFIED_DATE=[$LAST_VERIFIED_DATE]\n";


// Insert a row of information into the table 
$sql ="INSERT INTO affiliate_data SET
ID='$ID', 
HOTEL_CHAIN='$HOTEL_CHAIN',
NEW_OPENINGS_URL='$NEW_OPENINGS_URL',
SCRIPT_ID='$SCRIPT_ID',
LAST_VERIFIED_DATE='$LAST_VERIFIED_DATE',
AFFILIATE_URL='$AFFILIATE_URL',
LOGIN='$LOGIN',
PWD='$PWD'";

$res = mysqli_query($conn,$sql) or die(mysqli_error($conn));  

$i++;
}
echo "$i Data Records Inserted!\n";
echo "</PRE>";
exit;


// SHOW A ROW
if ($result = mysqli_query($conn,"SELECT * FROM affiliate_data"))
{
  if ($row = mysqli_fetch_row($result))
  {
    fwrite(STDOUT,"0=$row[0]\n");
    fwrite(STDOUT,"1=$row[1]\n");
    fwrite(STDOUT,"2=$row[2]\n");
  }
}



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

