<?php
//SQL pieslēgšanās informācija
$db_server = "mysql:3306";
$db_database = "baumuin_battle";
$db_user = "baumuin_bauma";
$db_password = "{GIwlpQ<?3>g";

//pieslēdzamies SQL serverim
$connection = @mysql_connect($db_server, $db_user, $db_password);
mysql_set_charset("utf8", $connection);
mysql_select_db($db_database);
?>