<?php
$atslegvards = $_GET['v'];
$sql=mysql_query("SELECT * FROM tags WHERE tag LIKE '%$atslegvards' ORDER BY id DESC LIMIT 40");
while($row=mysql_fetch_array($sql))
		{
		$msgID= $row['id'];
		$bildesurl= $row['img'];
	echo '<a id="'.$msgID.'" href="'.$bildesurl.'" class="highslide" onclick="return hs.expand(this)"><img style="width:100px;padding:4px;border-radius:10px;" src="'.$bildesurl.'"/></a>';
	}
?>
