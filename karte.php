<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lv" lang="lv">
<head>
<title>Kedas cīņa - karte</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="description" content="Salīdzini Kedas bildes!"/>
<meta name="keywords" content="Keda, foto, attēli, salīdzinājums"/>
<meta name="author" content="Matīss Rikters"/>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="includes/style.css">
</head>
<body style='margin: 0px; height: 100%;'>
<div style='position: fixed; top: 0; left: 0; width: 50%; height: 813%; background-color: black; z-index: 1;'></div>
<div style='position: absolute; top: 0; left: 0; z-index: 2; width: 100%;'>
<br/>
<?php
include('includes/init_sql.php');
$aaa=0;
//place
$kopskaits = mysql_query("select distinct lng, lat, count(lng) skaits, album from geo group by lng order by skaits desc");
while($r1=mysql_fetch_array($kopskaits)){
	$placesk[$aaa]=$r1["skaits"];
	$lng[$aaa]=$r1["lng"];
	$lat[$aaa]=$r1["lat"];
	$albums[$aaa]=$r1["album"];
	$aaa++;
}
?>
<h2 style="margin:auto auto;text-align:center;padding:5px;background-color:lightgrey;border-radius:15px;width:250px;opacity:0.7">Karte</h2>
<div style="margin:auto auto;padding:5px;padding-left:20px;background-color:lightgrey;border-radius:15px;width:80%;opacity:0.99;margin-top:20px;">
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
      var map;
      function initialize() {
        var mapOptions = {
          zoom: 4,
          center: new google.maps.LatLng(56.9465363, 24.1048503),
          mapTypeId: google.maps.MapTypeId.HYBRID
        };
        map = new google.maps.Map(document.getElementById('map_canvas'),
            mapOptions);
<?php	
				$i=0;
for ($m=0;$m<count($lng);$m++){
					?>
					//Apraksts
					var contentString<?php echo $i;?> = '<a target="_blank" href="http://lielakeda.lv/?album=<?php echo $albums[$m];?>"><?php echo $albums[$m]." (".$placesk[$m]." bildes)";?>';
					var infowindow<?php echo $i;?> = new google.maps.InfoWindow({
						content: contentString<?php echo $i;?>
					});
						
					//Atzīmē vietu kartē
					var parkingPos = new google.maps.LatLng(<?php echo $lat[$m];?>, <?php echo $lng[$m];?>);
					var marker<?php echo $i;?> = new google.maps.Marker({
						position: parkingPos,
						map: map,
						title:"<?php echo $albums[$m];?>"
					});
					google.maps.event.addListener(marker<?php echo $i;?>, 'click', function() {
					  infowindow<?php echo $i;?>.open(map,marker<?php echo $i;?>);
					});
					<?php
					$i=$i+1;
}
?>

      }
      google.maps.event.addDomListener(window, 'load', initialize);
			
		</script>
		<div id="map_canvas" style="margin:auto auto; width:700px; height:520px"></div>
<br style="clear:both;"/>
</div>
<br style="clear:both;"/>
<div style="position: fixed; bottom: 0px;  margin: auto auto; width:100%;background-color:lightgrey;text-align:center; opacity:0.85;">
	<a style="color:black; font-weight:bold; text-decoration:none;" href="index.php">Sākums</a> | 
	<a style="color:black; font-weight:bold; text-decoration:none;" href="topp.php">TOP bildes</a> | 
	<a style="color:black; font-weight:bold; text-decoration:none;" href="topa.php">TOP albumi</a> | 
	<a style="color:black; font-weight:bold; text-decoration:none;" href="stat.php">Statistika</a> | 
	<a style="color:black; font-weight:bold; text-decoration:none;" href="karte.php">Karte</a> | 
	<a style="color:black; font-weight:bold; text-decoration:none;" href="tags.php">Atslēgvārdi</a>
</div>
</div>
</body>
</html>