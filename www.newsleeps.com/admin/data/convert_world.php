#!/usr/bin/php

<?php
$dump = array(); 
$size = "";
$comma =",";
$delim ="|";
    
echo "Building World Hotels...\n";

// OPEN PIPE and CSV FILE 
$expediafile = "ActivePropertyList.txt";
$infile = "world_hotels.csv";
$outfile = "batch_world.csv";
$errfile = "error_world.log";
$in = fopen($infile,"r") or die ("cannot open ".$infile); 
$out = fopen($outfile,"w") or die ("cannot open ".$outfile); 
$err = fopen($errfile,"w") or die ("cannot open ".$errfile); 

# LOAD EAN PHOTO DATA FILE
# EANHotelID|URL
$filename = "HotelImageList.csv";
$file_arr = get_csv($filename);

// FOR EACH RECORD in ARRAY WORLD HOTELS FILE
while(!feof($in)) 
{ 
        $indata = fgetcsv($in, $size, $comma);

	// Skip comment lines
	if ( substr(trim($indata[0]),0,1) == "#") { continue; }
	if ( "NAME" == strtoupper($indata[0])) { continue; }

	$INNAME=trim(addslashes($indata[0]));
	$INOPEN_DATE=trim(addslashes($indata[1]));
	$INURL=trim(addslashes($indata[2]));

	$INNAME = trim($INNAME);

	// GREP EXPEDIA FILE AND LOOK FOR MATCH
        $rec = `grep -i -m 1 "$INNAME" $expediafile`;

	// Set up to try splitting on comma
	$arr_shortName = explode( ",", $INNAME, 2 );

	if (empty($rec)) {
            $rec = `grep -i -m 1 "$arr_shortName[0]" $expediafile`;
	    if (empty($rec)) {
		echo "NO MATCH FOR: $INNAME\n";
	        fwrite($err,"NO MATCH FOR: $INNAME|$INURL\n");
		continue;
	    }
	}

        $data = explode($delim, $rec);
	    
	    $HOTELID=trim(addslashes($data[0]));
	    $NAME=trim(addslashes($data[2]));
	    $ADDRESS1 =trim(addslashes($data[3]));
	    $CITY =trim(addslashes($data[5]));
	    $COUNTRY =trim(addslashes($data[8]));
	    $LAT =trim(addslashes($data[9]));
	    $LNG =trim(addslashes($data[10]));

	    #echo "---->MATCH: $NAME = $INNAME\n";

	    // STREET NUMBER
	    list($STREET) = explode(" ",$ADDRESS1);	

	    // ADD PHOTO URL
    	    $PHOTOURL = "NULL";
	    if (array_key_exists($HOTELID,$file_arr)) {
    	        $url = $file_arr[$HOTELID];
		$PHOTOURL = rtrim($url);
    	    }	
	    else {
		echo "NO PHOTO FOUND FOR $HOTELID: $NAME\n";
	    }

	    // ADD REMAINING ORIGINAL FIELDS
	    $OPEN_DATE = $INOPEN_DATE;
	    $URL = $INURL;

	    // FIX DATES
	    $OPEN_DATE = date("Y-m-d",strtotime($OPEN_DATE));

	    // WRITE DATA IN pipe csv FORMAT
	    $data ="$NAME|$URL|$ADDRESS1|$CITY|$COUNTRY|$OPEN_DATE|$PHOTOURL|$LAT|$LNG|$HOTELID";

	    fwrite($out,"$data\n");
}
fclose ($err);
fclose ($out);
fclose ($in);
#echo "File $outfile Created!\n";
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

