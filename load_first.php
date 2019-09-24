<?php
$atslegvards = $_GET['v'];
$sql=mysqli_query($connection, "SELECT * FROM tags WHERE tag LIKE '%$atslegvards' ORDER BY id DESC LIMIT 200");
while($row=mysqli_fetch_array($sql)){
		$msgID= $row['id'];
		$bildesurl= $row['img'];
        // echo '<a id="'.$msgID.'" href="'.preg_replace("~\/(?!.*\/)~", "/s2048/", $bildesurl).'" class="highslide" onclick="return hs.expand(this)"><img style="width:100px;padding:4px;border-radius:10px;" src="'.$bildesurl.'"/></a>';
        if(substr($bildesurl, 0, 4) == "http"){
            echo '<a id="'.$msgID.'" href="'.preg_replace("~\/(?!.*\/)~", "/s2048/", $bildesurl).'" class="highslide" onclick="return hs.expand(this)"><img style="width:100px;padding:4px;border-radius:10px;" src="'.$bildesurl.'"/></a>';
        }else{
            echo "<a id='".$msgID."' href='#' class='highslide' onclick='return hs.expand(this)'><img style='width:100px;padding:4px;border-radius:10px;' onload='(function(){var imgElement = this; var jsonURL=\"https://photoslibrary.googleapis.com/v1/mediaItems/".$bildesurl."?access_token=".$accessToken."\"; $.getJSON(jsonURL, function(data) { var imgURL = data.baseUrl+\"=w2000\"; imgElement.src=imgURL; imgElement.parentElement.href=imgURL; }); }).call(this)' src='includes/bigLoader.gif'/>";
        }
	}
?>
