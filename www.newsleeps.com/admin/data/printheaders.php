<html>
<body>
<pre>
<?php

foreach (getallheaders() as $name => $value) {
    echo "$name: $value\n";
}

?>
</pre>
</body>
</html>

