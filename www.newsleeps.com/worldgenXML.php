<?php  
require("dbconnect.php");

// WORLD MAP 2018-03-31
// 2018-12-17 PHP 7.0
//EXAMPLE URL:  http://www.newsleeps.com/genXML.php?lat=30&lng=-81&radius=300
//
// Get parameters from URL
$center_lat = $_GET["lat"];
$center_lng = $_GET["lng"];
$radius = $_GET["radius"];
$max_hits = 150;

// Start XML file, create parent node
$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);

// Search the rows in the markers table
$query = sprintf("SELECT address1, name, url, city, country, open_date, hotelid, lat, lng, ( 3959 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM world_hotels HAVING distance < '%s' ORDER BY distance LIMIT 0 , $max_hits",
  mysqli_real_escape_string($conn,$center_lat),
  mysqli_real_escape_string($conn,$center_lng),
  mysqli_real_escape_string($conn,$center_lat),
  mysqli_real_escape_string($conn,$radius));

$result = mysqli_query($conn,$query);

if (!$result) {
  die("Invalid query: " . mysqli_error($conn));
}


header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each
while ($row = @mysqli_fetch_assoc($result)){
  if ($row['name'] == "") { continue;	}	// Skip blank records

// patch to remove non-ascii chars
$name = preg_replace('/[^(\x20-\x7F)]*/','', $row['name']);
$url = preg_replace('/[^(\x20-\x7F)]*/','', $row['url']);
$address = preg_replace('/[^(\x20-\x7F)]*/','', $row['address1']);

  $node = $dom->createElement("marker");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("name", $name);
  $newnode->setAttribute("hurl", $url);
  $newnode->setAttribute("address", $address);
  $newnode->setAttribute("city", $row['city']);
  $newnode->setAttribute("country", $row['country']);
  $open_date = date('F, Y', strtotime($row['open_date']) );
  $newnode->setAttribute("open_date", $open_date);
  $newnode->setAttribute("hotelid", $row['hotelid']);
  $newnode->setAttribute("lat", $row['lat']);
  $newnode->setAttribute("lng", $row['lng']);
  $newnode->setAttribute("distance", $row['distance']);
}

echo $dom->saveXML();


?>

