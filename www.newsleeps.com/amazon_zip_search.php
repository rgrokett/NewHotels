<?php 
// 2018-12-17 PHP 7.0
echo "<html>\n";
echo "<body>\n";

// INCOMING FORM VARIABLES
	$in_zip = htmlspecialchars($_REQUEST["zip"]);
	$in_zip = strtoupper($in_zip);
	$in_zip = preg_replace('/\s+/', '', $in_zip);

// DB Connection
require_once("dbconnect.php");
?>
<B>Enter the zip code for the hotel:</B><br>
	
<form method="GET" action="amazon_zip_search.php" name="newsleep_search">
ZIP CODE<input size="7" name="zip" value="">
<input name="submit" value="Search" type="submit" />
</form>


<?php
// SEARCH BY ZIP


// MAIN SCREEN LIST OF NEW HOTELS
$sql = sprintf("SELECT h.* FROM new_hotels h WHERE h.postal_code = '%s' order by h.name",
			mysqli_real_escape_string($conn,$in_zip)
			);

$result = mysqli_query($conn,$sql);

$n = 0;
echo "<table>";
while($row = mysqli_fetch_assoc($result)) { 
		echo "<TR><TD>FOUND: hotelid=".$row['hotelid'].":</TD><TD>".$row['name']."</TD><TD>".$row['open_date']."</TD></TR>";
		$n++;
	}
if (($n == 0) && ($in_zip != ""))
	{
		echo "<TR><TD>No Data Found.</TD></TR>";
	}
echo "</table>";

mysqli_free_result($result); 

mysqli_close($conn); 

?>

</body>
</html>

