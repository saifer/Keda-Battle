<?php
$atslegvards = $_GET['v'];
$last_msg_id=$_GET['last_msg_id'];

 $sql=mysql_query("SELECT * FROM tags WHERE tag LIKE '%$atslegvards' and id < '$last_msg_id' ORDER BY id DESC LIMIT 15");
 $last_msg_id="";

		while($row=mysql_fetch_array($sql))
		{
		$msgID= $row['id'];
		$bildesurl= $row['img'];
	echo '<a id="'.$msgID.'" href="'.$bildesurl.'" class="highslide" onclick="return hs.expand(this)"><img style="width:100px;padding:4px;border-radius:10px;" src="'.$bildesurl.'"/></a>';
	}
?>
