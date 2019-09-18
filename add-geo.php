<?php
header('Content-Type: text/html; charset=UTF-8');
//SQL pieslēgšanās informācija
$db_server = "localhost";
$db_user = "baumuin_bauma";
$db_password = "{GIwlpQ<?3>g";
$db_database = "baumuin_battle";

//pieslēdzamies SQL serverim
$connection = mysqli_connect($db_server, $db_user, $db_password, $db_database);
mysqli_set_charset($connection, "utf8");
session_start();

//saglabāsim datubāzē
if (isset($_POST['submit'])){
    // var_dump($_POST);
    foreach($_POST as $key => $value){
        $keyParts = explode("-", $key);
        $id = $keyParts[1];
        if($keyParts[0] == "lng"){
            $lng = $value;
            //ja viss ir, saglabājam
            if(
                isset($id) && 
                isset($lat) && 
                isset($lng) && 
                isset($album) && 
                isset($img) && 
                $id !== "" && 
                $lat !== "" && 
                $lng !== "" && 
                $album !== "" && 
                $img !== ""
            ){
                // echo $id."; ".$lat."; ".$lng."; ".$img."; ".$album."</br>";
                $result = mysqli_query($connection, "insert into geo (img, img_id, album, lat, lng) values('$img', '$id', '$album', '$lat', '$lng')");
            }
            
            //nodzēšam vērtības
            unset($id);
            unset($lat);
            unset($lng);
            unset($album);
            unset($img);
        }else if($keyParts[0] == "lat"){
            $lat = $value;
        }else if($keyParts[0] == "album"){
            $album = $value;
        }else if($keyParts[0] == "img"){
            $img = $value;
        }
    }
}


?>
<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
  </head>
  <body>
    <div id="map" style="height: 600px; width: 590px; float: right; margin:20px;"></div>
    <script>
      var map;
	  var marker;
	  var lastFocused = null;
      function copy(id, previousId) {
        latElementSrc = document.getElementsByName('lat-'+previousId)[0];
        lngElementSrc = document.getElementsByName('lng-'+previousId)[0];
        
        latElementTrg = document.getElementsByName('lat-'+id)[0];
        lngElementTrg = document.getElementsByName('lng-'+id)[0];
        
        latElementTrg.value = latElementSrc.value;
        lngElementTrg.value = lngElementSrc.value;
      }
      function set(id) {
        latElement = document.getElementsByName('lat-'+id)[0];
        lngElement = document.getElementsByName('lng-'+id)[0];
		
		marker.setPosition( new google.maps.LatLng( latElement.value, lngElement.value ) );
		map.panTo( new google.maps.LatLng( latElement.value, lngElement.value ) );
      }
      function initMap() {
		var myLatlng = new google.maps.LatLng(56.8796,24.6032);
		var mapProp = {
			center:myLatlng,
			zoom:10,
			mapTypeId:google.maps.MapTypeId.ROADMAP
		};
        map = new google.maps.Map(document.getElementById('map'), mapProp);
		marker = new google.maps.Marker({
		  position: myLatlng,
		  map: map,
		  title: 'Here?',
		  draggable:true  
		});
		document.getElementById('lat').value= 56.8796
		document.getElementById('lng').value= 24.6032  
		// marker drag event
		google.maps.event.addListener(marker,'drag',function(event) {
            var latElement = document.getElementById('lat');
            var lngElement = document.getElementById('lng');
            
            if(lastFocused !== null){
                var elementNumber = lastFocused.name.split("-")[1];
                latElement = document.getElementsByName('lat-'+elementNumber)[0];
                lngElement = document.getElementsByName('lng-'+elementNumber)[0];
            }
            
			latElement.value = event.latLng.lat();
			lngElement.value = event.latLng.lng();
		});

		//marker drag event end
		google.maps.event.addListener(marker,'dragend',function(event) {
            var latElement = document.getElementById('lat');
            var lngElement = document.getElementById('lng');
            
            if(lastFocused !== null){
                var elementNumber = lastFocused.name.split("-")[1];
                latElement = document.getElementsByName('lat-'+elementNumber)[0];
                lngElement = document.getElementsByName('lng-'+elementNumber)[0];
            }
            
			latElement.value = event.latLng.lat();
			lngElement.value = event.latLng.lng();
		});
      }
    </script>
	<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDvGo83FtJD6vJQpWBhH60Z4ZrbRtFTJyI&callback=initMap">  </script>
	<?php
        $queryCikIr = "SELECT Count(`Id`) as `count` FROM `ratings` WHERE `ratings`.`img` NOT IN (SELECT `img` FROM `geo`)";
        $dataCikIr = mysqli_query($connection, $queryCikIr);
		$reqCikIr = mysqli_fetch_array($dataCikIr);
	?>
	<form style="margin-top:-10px;" action="add-geo.php" method="post">
		<input type='hidden' name='lat' id='lat'>  
		<input type='hidden' name='lng' id='lng'> 
	
        <table border="1" style="margin:20px">
        <tr>
            <th>Album</th>
            <th>Image</th>
            <th>Latitude</th>
            <th>Longitude</th>
        </tr>
        <?php
        $query = "SELECT `Id`, `album`, `img` FROM `ratings` WHERE `ratings`.`img` NOT IN (SELECT `img` FROM `geo`) ORDER BY `album` Limit 0, 7";
        $color = random_color();
        $previousAlbum = "";
        
        $data = mysqli_query($connection, $query);
		$previousId = 0;
        while($req = mysqli_fetch_array($data)){
            $Id		= $req["Id"];
            $album	= $req["album"];
            $img	= $req["img"];
            
            if($previousAlbum != $album){
                $color = random_color();
                $previousAlbum = $album;
            }
            
            //Vai šī albuma citām bildēm jau ir atrašanās vieta? Vai visām aptuveni viena? Varbūt var automātiski piedāvāt aizpildīt ar to pašu?
            $albumQuery = "SELECT DISTINCT(CONCAT(`lat`, `lng`)) as `latlng`, `lat`, `lng` FROM `geo` WHERE `album` LIKE '$album' GROUP BY CONCAT(`lat`, `lng`) ";
            $albumQueryRez = mysqli_query($connection, $albumQuery);
            $resultCount = mysqli_num_rows($albumQueryRez);
            
            if($resultCount == 1 ) {
                $albumReq = mysqli_fetch_array($albumQueryRez);
                $lat = $albumReq['lat'];
                $lng = $albumReq['lng'];
            }else{
                $lat = "";
                $lng = "";
            }
            
            echo "<tr>";
            echo "<td style='background-color: #".$color."' >".$album."</td><td><img style='height:100px;' src='".$img."' />";
            echo "<input type='hidden' name='img-".$Id."' id='img-".$Id."' value='".$img."'>";
            echo "<input type='hidden' name='album-".$Id."' id='album-".$Id."' value='".$album."'>";
            echo "</td>";
            echo "<td><input style='display:inline;width:95px;' name='lat-".$Id."' id='lat-".$Id."' onfocus='lastFocused=this;' value='".$lat."'><br/>";
            echo "<input type='button' name='".$Id."' onclick='copy(".($Id.",".$previousId).");' value='Copy Previous'></td>";
            echo "<td><input style='display:inline;width:95px;' name='lng-".$Id."' id='lng-".$Id."' onfocus='lastFocused=this;' value='".$lng."'>";
            echo "<input style='display:inline;' type='button' name='".$Id."' onclick='set(".($Id).");' value='*'><br/>";
            echo "<input type='button' name='".$Id."' onclick='copy(".($Id.",".$previousId).");' value='Copy Previous'></td>";
            echo "</tr>";
			
			$previousId = $Id;
        }
        
        function random_color_part() {
            return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
        }

        function random_color() {
            return random_color_part() . random_color_part() . random_color_part();
        }
        ?>
        </table>
		<input type="submit" name="submit" value="Submit">
        <?php
		echo "Atlicis: ".$reqCikIr['count'];
        ?>
	</form>
  </body>
</html>