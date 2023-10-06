#!/usr/bin/php

<?php
// 2018-03-14 Modified for World Hotels
// 2019-04-25 Changed from 23mo to 18mo
// 2019-06-03 PHP 7.0 MySQLi
// DB Connection
require_once("dbconnect.php");

date_default_timezone_set("UTC");


echo "<PRE>";

// OPEN CSV FILE and read
$filename = "batch_world.csv";
$recarray = get_csv($filename);

// DELETE OLD DATA
	$sql="truncate table world_hotels";
	$res=mysqli_query($conn,$sql) or die("3211321231");		
	

// FOR EACH RECORD in ARRAY
$i = 0; 
while (list($row,$data) = @each($recarray))
{
	// Skip comment lines
	if ( substr(trim($data[0]),0,1) == "#") { continue; }

	$NAME=trim(addslashes($data[0]));
	$URL =trim(addslashes($data[1]));
	$ADDRESS1 =trim(addslashes($data[2]));
	$CITY =trim(addslashes($data[3]));
	$COUNTRY =trim(addslashes($data[4]));
	$OPEN_DATE =trim(addslashes($data[5]));
	$PHOTO_URL =trim(addslashes($data[6]));
	$LAT =trim(addslashes($data[7]));
	$LNG =trim(addslashes($data[8]));
	$HOTELID =trim(addslashes($data[9]));


// Skip any header
if (strcasecmp($NAME,"name") == 0) { continue; }

// DEFAULT OPEN DATE TO 6 months
$default_date = strtotime ( "-6 months") ;
$default_date = date( 'Y-m-d' , $default_date );

if ($OPEN_DATE == "null") { $OPEN_DATE = $default_date; }
if ($OPEN_DATE == "") { $OPEN_DATE = $default_date; }
$OPEN_DATE = date("Y-m-d",strtotime($OPEN_DATE));

// FIX LAT LNG
if ($LAT == "") { $LAT = "0.0"; }
if ($LNG == "") { $LNG = "0.0"; }

// Skip any null HOTELID
if ($HOTELID == "NULL") { continue; }


echo $i.")";
echo "NAME=[$NAME]\n";
echo "COUNTRY=[$COUNTRY]\n";
echo "OPEN_DATE=[$OPEN_DATE]\n";
echo "PHOTO_URL=[$PHOTO_URL]\n";

// Insert a row of information into the table 
$sql ="INSERT INTO world_hotels SET
NAME='$NAME', 
URL='$URL',
ADDRESS1='$ADDRESS1',
CITY='$CITY',
COUNTRY='$COUNTRY',
OPEN_DATE='$OPEN_DATE',
PHOTO_URL='$PHOTO_URL',
LAT='$LAT',
LNG='$LNG',
HOTELID=$HOTELID";

// Handle Intl chars 
$sql = utf8_encode($sql);

$res = mysqli_query($conn,$sql) or die(mysqli_error($conn));  

$i++;
}
echo "$i Data Records Inserted!\n";

// DELETE RECORDS OVER 23 MONTHS OLD
$result = mysqli_query($conn,"Select count(*) FROM world_hotels WHERE open_date < now() - interval 23 month");
$row = mysqli_fetch_row($result);
$cnt = $row[0];
echo "Deleting ".$cnt." old records.\n";
mysqli_query($conn,"DELETE FROM world_hotels WHERE open_date < now() - interval 23 month");

$result = mysqli_query($conn,"SELECT count(*) FROM world_hotels");
$row = mysqli_fetch_row($result);
$total = $row[0];
echo "\nTotal records: ".$total."\n";

echo "</PRE>";
exit;

// SHOW A ROW
if ($result = mysqli_query($conn,"SELECT * FROM world_hotels"))
{
  if ($row = mysqli_fetch_row($result))
  {
    fwrite(STDOUT,"0=$row[0]\n");
    fwrite(STDOUT,"1=$row[1]\n");
    fwrite(STDOUT,"2=$row[2]\n");
  }
}



// LOAD A CSV FILE
function get_csv($filename, $delim ="|"){ 

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

