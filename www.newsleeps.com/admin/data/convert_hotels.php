#!/usr/bin/php

<?php
// 2018-03-18 added LAT/LNG 

$dump = array(); 
$size = "";
$delim ="|";
    
echo "Converting to CSV...\n";

// OPEN PIPE and CSV FILE 
$infile = "ActivePropertyList.txt";
$outfile = "Hotel_All_Active.csv";
$in = fopen($infile,"r") or die ("cannot open ".$infile); 
$out = fopen($outfile,"w") or die ("cannot open ".$outfile); 

// FOR EACH RECORD in ARRAY
while(!feof($in)) 
{ 
        $data = fgetcsv($in, $size, $delim);
	
	$HOTELID=trim(addslashes($data[0]));
	$NAME=trim(addslashes($data[2]));
	$ADDRESS1 =trim(addslashes($data[3]));
	$CITY =trim(addslashes($data[5]));
	$STATE_PROV =trim(addslashes($data[6]));
	$COUNTRY =trim(addslashes($data[8]));
	$POSTAL_CODE =trim(addslashes($data[7]));

	$LAT =trim(addslashes($data[9]));
	$LNG =trim(addslashes($data[10]));

	// STREET NUMBER
	list($STREET) = explode(" ",$ADDRESS1);	
	$LAST_VERIFIED_DATE = "null";
	$OPEN_DATE = "null";

// SKIP NON-USA
if (($COUNTRY != "US")&&($COUNTRY != "CA")) { continue; }

// FIX DATES
#if ($LAST_VERIFIED_DATE == "null") { $LAST_VERIFIED_DATE = date("Y-m-d"); }

#if ($OPEN_DATE == "null") { $OPEN_DATE = $LAST_VERIFIED_DATE; }
#$OPEN_DATE = date("Y-m-d",strtotime($OPEN_DATE));

// FIX COUNTRY
#if ($COUNTRY == "US") { $COUNTRY = "USA"; }

# REMOVE SPACES IN CA CODES
if ($COUNTRY == "CA") { $POSTAL_CODE = str_replace(' ', '', $POSTAL_CODE); }

// WRITE DATA IN pipe csv FORMAT
//$data ="$HOTELID|$NAME|$ADDRESS1|$CITY|$STATE_PROV|$COUNTRY|$POSTAL_CODE|$LAST_VERIFIED_DATE";
$data ="$HOTELID|$STREET|$ADDRESS1|$POSTAL_CODE|$LAT|$LNG";

fwrite($out,"$data\n");

}
fclose ($out);
fclose ($in);
echo "File $outfile Created!\n";
exit;


?>

