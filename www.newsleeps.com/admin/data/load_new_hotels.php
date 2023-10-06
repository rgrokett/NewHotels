#!/usr/bin/php

<?php
// 2017-06-21 Update for canada
// 2019-04-25 changed from 23 mo to 18mo
// // 2019-06-03 PHP 7.0 MySQLi
// DB Connection
require_once("dbconnect.php");

date_default_timezone_set("UTC");


echo "<PRE>";
echo "Be sure to add HOTEL_CHAINs to affiliate_data table\n";

// OPEN CSV FILE and read
$filename = "new_hotels.csv";
$recarray = get_csv($filename);

// DELETE OLD DATA
	$sql="truncate table new_hotels";
	$res=mysqli_query($conn,$sql) or die("3211321231");		
	

// FOR EACH RECORD in ARRAY
$i = 0; 
while (list($row,$data) = @each($recarray))
{
	// Skip comment lines
	if ( substr(trim($data[0]),0,1) == "#") { continue; }

	$NAME=trim(addslashes($data[0]));
	$WEBSITE=trim(addslashes($data[1]));
	$URL =trim(addslashes($data[2]));
	$ADDRESS1 =trim(addslashes($data[3]));
	$CITY =trim(addslashes($data[4]));
	$STATE_PROV =trim(addslashes($data[5]));
	$COUNTRY =trim(addslashes($data[6]));
	$POSTAL_CODE =trim(addslashes($data[7]));
	$MAP_URL =trim(addslashes($data[8]));
	$PHONE =trim(addslashes($data[9]));
	$HOTEL_CHAIN =trim(addslashes($data[10]));
	$HOTEL_TYPE =trim(addslashes($data[11]));
	$OPEN_DATE =trim(addslashes($data[12]));
	$PHOTO_URL =trim(addslashes($data[13]));
	$RATING =trim(addslashes($data[14]));
	$LAST_VERIFIED_DATE =trim(addslashes($data[15]));
	$LAT =trim(addslashes($data[16]));
	$LNG =trim(addslashes($data[17]));
	$HOTELID =trim(addslashes($data[18]));


// Skip any header
if (strcasecmp($NAME,"name") == 0) { continue; }

// FIX WEBSITE
if ($WEBSITE == "") { $WEBSITE =  parse_url($URL, PHP_URL_HOST); }
	

// FIX DATES
if ($LAST_VERIFIED_DATE == "null") { $LAST_VERIFIED_DATE = date('Y-m-d'); }

// DEFAULT OPEN DATE TO 6 months
$default_date = strtotime ( "-6 months") ;
$default_date = date( 'Y-m-d' , $default_date );

if ($OPEN_DATE == "null") { $OPEN_DATE = $default_date; }
if ($OPEN_DATE == "") { $OPEN_DATE = $default_date; }
$OPEN_DATE = date("Y-m-d",strtotime($OPEN_DATE));

// FIX COUNTRY
if ($COUNTRY == "US") { $COUNTRY = "USA"; }
if ($COUNTRY == "CA") { $COUNTRY = "CANADA"; }

// FIX POSTAL_CODE (rmv spaces for Canada)
$POSTAL_CODE = str_replace(' ', '', $POSTAL_CODE);

// FIX PHOTO_URL
if (strpos(strtoupper($PHOTO_URL),"HTTP") === false) { $PHOTO_URL = "HTTP://$WEBSITE/$PHOTO_URL"; }

// FIX LAT LNG
if ($LAT == "") { $LAT = "0.0"; }
if ($LNG == "") { $LNG = "0.0"; }

// FIX  HOTELID
if ($HOTELID == "") { $HOTELID = "NULL"; }

// FIX  RATING
if ($RATING == "") { $RATING = "0"; }


echo $i.")";
echo "NAME=[$NAME]\n";
echo "WEBSITE=[$WEBSITE]\n";
echo "OPEN_DATE=[$OPEN_DATE]\n";
echo "PHOTO_URL=[$PHOTO_URL]\n";
echo "LAST_VERIFIED_DATE=[$LAST_VERIFIED_DATE]\n";


// Insert a row of information into the table 
$sql ="INSERT INTO new_hotels SET
NAME='$NAME', 
WEBSITE='$WEBSITE',
URL='$URL',
ADDRESS1='$ADDRESS1',
CITY='$CITY',
STATE_PROV='$STATE_PROV',
COUNTRY='$COUNTRY',
POSTAL_CODE='$POSTAL_CODE',
MAP_URL='$MAP_URL',
PHONE='$PHONE',
HOTEL_CHAIN='$HOTEL_CHAIN',
HOTEL_TYPE='$HOTEL_TYPE',
OPEN_DATE='$OPEN_DATE',
PHOTO_URL='$PHOTO_URL',
RATING='$RATING',
LAST_VERIFIED_DATE='$LAST_VERIFIED_DATE',
LAT='$LAT',
LNG='$LNG',
HOTELID=$HOTELID";

// Handle Intl chars CANADA
$sql = utf8_encode($sql);

$res = mysqli_query($conn,$sql) or die(mysqli_error($conn));  

$i++;
}
echo "$i Data Records Inserted!\n";

// DELETE RECORDS OVER 23 MONTHS OLD
$result = mysqli_query($conn,"Select count(*) FROM new_hotels WHERE open_date < now() - interval 23 month");
$row = mysqli_fetch_row($result);
$cnt = $row[0];
echo "Deleting ".$cnt." old records.\n";
mysqli_query($conn,"DELETE FROM new_hotels WHERE open_date < now() - interval 23 month");

$result = mysqli_query($conn,"SELECT count(*) FROM new_hotels");
$row = mysqli_fetch_row($result);
$total = $row[0];
echo "\nTotal records: ".$total."\n";

echo "</PRE>";
exit;

// SHOW A ROW
if ($result = mysqli_query($conn,"SELECT * FROM new_hotels"))
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

