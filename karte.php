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
$vskaits = mysqli_query($connection, "select distinct countryCode from coord_code");

$kodi = array();
while($vvs = mysqli_fetch_array($vskaits)){
	$kodi[] = strtolower($vvs["countryCode"]);
};

//Visu valstu kodi
$europe			= array("va", "ch", "ad", "ee", "is", "am", "al", "cz", "ge", "at", "ie", "gi", "gr", "nl", "pt", "no", "lv", "lt", "lu", "es", "it", "ro", "pl", "be", "fr", "bg", "dk", "hr", "de", "hu", "ba", "fi", "by", "fo", "mc", "cy", "mk", "sk", "mt", "si", "sm", "se", "gb");
$oceania 		= array("ck", "pw", "tv", "na", "ki", "mh", "nu", "to", "nz", "au", "vu", "sb", "ws", "fj", "fm");
$africa 		= array("gw", "zm", "ci", "eh", "gq", "eg", "cg", "cf", "ao", "ga", "et", "gn", "gm", "zw", "cv", "gh", "rw", "tz", "cm", "na", "ne", "ng", "tn", "lr", "ls", "tg", "td", "er", "ly", "bf", "dj", "sl", "bi", "bj", "za", "bw", "dz", "sz", "mg", "ma", "ke", "ml", "km", "st", "mu", "mw", "so", "sn", "mr", "sc", "ug", "sd", "mz");
$asia 			= array("mn", "cn", "af", "am", "vn", "ge", "in", "az", "id", "ru", "la", "tw", "tr", "lk", "tm", "tj", "pg", "th", "np", "pk", "ph", "bd", "ua", "bn", "jp", "bt", "hk", "kg", "uz", "mm", "sg", "mo", "kh", "kr", "mv", "kz", "my");
$northAmerica 	= array("gt", "ag", "vg", "ai", "vi", "ca", "gd", "aw", "cr", "cu", "pr", "ni", "tt", "gp", "pa", "do", "dm", "bb", "ht", "jm", "hn", "bs", "bz", "sx", "sv", "us", "mq", "ms", "ky", "mx");
$southAmerica 	= array("gd", "py", "co", "ve", "cl", "sr", "bo", "ec", "gf", "ar", "gy", "br", "pe", "uy", "fk");
$middleEast 	= array("om", "lb", "iq", "ye", "ir", "bh", "sy", "qa", "jo", "kw", "il", "ae", "sa");



$kopskaits = mysqli_query($connection, "select distinct lng, lat, count(lng) skaits, album from geo group by lng order by skaits desc");
while($r1=mysqli_fetch_array($kopskaits)){
	$placesk[$aaa]=$r1["skaits"];
	$lng[$aaa]=$r1["lng"];
	$lat[$aaa]=$r1["lat"];
	$albums[$aaa]=$r1["album"];
	$aaa++;
	//reģionu kartēm
	$lngX = $r1["lng"];
	$latX = $r1["lat"];
	$skaits = $r1["skaits"];
	$coord_code = mysqli_query($connection, "SELECT * FROM coord_code where lat like $latX AND lng like $lngX");
	if(mysqli_num_rows($coord_code)!=0){
		//get region code and add it to array
		$c_code = mysqli_fetch_array($coord_code);
		//ja ir
		$code = $c_code['code'];
		$countryCode = $c_code['countryCode'];
		$adminName1 = $c_code['adminName1'];
		$reg_code[$code]['sk'] = $reg_code[$code]['sk']+$skaits;
		$reg_code[$code]['cc'] = $countryCode;
		$reg_code[$code]['ad'] = $adminName1;
		$country_code[$countryCode] = $country_code[$countryCode]+$skaits;
	}else{
		//get code from coordinates and insert into db
		$string = file_get_contents("http://api.geonames.org/countrySubdivisionJSON?formatted=true&lat=".$latX."&lng=".$lngX."&username=saifer&style=full");
		$json=json_decode($string, true);
		$code = $json["codes"][0]["code"];
		$countryCode = $json["countryCode"];
		$adminName1 = $json["adminName1"];
		mysqli_query($connection, "INSERT INTO coord_code (lng, lat, code, countryCode, adminName1) VALUES ('$lngX', '$latX', '$code', '$countryCode', '$adminName1')");
		$reg_code[$code]['sk'] = $reg_code[$code]['sk']+$skaits;
		$reg_code[$code]['cc'] = $countryCode;
		$reg_code[$code]['ad'] = $adminName1;
		$country_code[$countryCode] = $country_code[$countryCode]+$skaits;
	}
}
?>
<h2 style="margin:auto auto;text-align:center;padding:5px;background-color:lightgrey;border-radius:15px;width:250px;opacity:0.7">Karte</h2>
<div style="margin:auto auto;padding:5px;padding-left:20px;background-color:lightgrey;border-radius:15px;width:80%;opacity:0.99;margin-top:20px;">
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
      var map;
      function initialize() {
        var mapOptions = {
          zoom: 2,
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
					//var contentString<?php echo $i;?> = '<a target="_blank" href="http://lielakeda.lv/?album=<?php echo $albums[$m];?>"><?php echo $albums[$m]." (".$placesk[$m]." bildes)";?>';
					var contentString<?php echo $i;?> = '<a target="_blank"><?php echo str_replace("'","",$albums[$m])." (".$placesk[$m]." bildes)";?>';
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
		

<!-- Reģionu kartēm -->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
<?php
drawRegionMap("LV", $reg_code);
drawRegionMap("EE", $reg_code);
drawRegionMap("JP", $reg_code);
drawRegionMap("LT", $reg_code);
?>
		
<!-- new google map chart -->
    <script type="text/javascript">
	google.setOnLoadCallback(drawVisualization);

	function drawVisualization() {var data = new google.visualization.DataTable();

	 data.addColumn('string', 'Province');
	 data.addColumn('number', 'Value');  
	 data.addColumn({type:'string', role:'tooltip'});
<?php
$maxVal = 0;
foreach ($country_code as $key => $value){
	if($key != "LV"){
	   if ($value['sk']==1) {$tviti=" bilde";} else {$tviti=" bildes";}
		?>
		data.addRows([[ '<?php echo $key;?>',<?php echo $value;?>,'<?php echo $value.$tviti;?>']]);
		<?
		if ($value > $maxVal) $maxVal = $value;
	}
}
		?>
		data.addRows([[ 'LV',<?php echo round($maxVal*1.3);?>,'<?php echo "Daudz bilžu";?>']]);

        var options = {
		};

        var chart = new google.visualization.GeoChart(document.getElementById('fullregions_div'));
        var chart2 = new google.visualization.GeoChart(document.getElementById('euregions_div'));

        chart.draw(data, options);
        chart2.draw(data, { region: '150' });
      }
    </script>
		
		<h3 style="margin:auto auto; width: 1200px; ">Pasaules reģionu karte<br/></h3>
		<div id="fullregions_div" style="margin:auto auto; width: 1200px; height: 800px;"></div>
		<br/>
		<h3 style="margin:auto auto; width: 1200px; ">Eiropas reģionu karte<br/></h3>
		<div id="euregions_div" style="margin:auto auto; width: 1200px; height: 800px;"></div>
		<br/>
		<div style="margin:auto auto; width: 1200px;">
            <h3 style="margin:auto auto; width: 600px; float:left;">Latvijas reģionu karte<br/></h3>
            <h3 style="margin:auto auto; width: 600px; float:right;">Igaunijas reģionu karte<br/></h3>
        </div>
		<div style="margin:auto auto; width: 1200px; height: 400px;">
            <div id="regions_LV_div" style="width: 600px; height: 400px; float:left;"></div>
            <div id="regions_EE_div" style="width: 600px; height: 400px; float:left;"></div>
        </div>
		<br/>
		<div style="margin:auto auto; width: 1200px;">
            <h3 style="margin:auto auto; width: 600px; float:left;">Lietuvas reģionu karte<br/></h3>
            <h3 style="margin:auto auto; width: 600px; float:right;">Japānas reģionu karte<br/></h3>
        </div>
		<div style="margin:auto auto; width: 1200px; height: 400px;">
            <div id="regions_LT_div" style="width: 600px; height: 400px; float:left;"></div>
            <div id="regions_JP_div" style="width: 600px; height: 400px; float:left;"></div>
        </div>
		<br/>
		<h3 style="margin:auto auto; width: 1200px; ">Pasaules karte<br/></h3>
		<div id="map_canvas" style="margin:auto auto; width:1200px; height:800px"></div>
		<br/>
		<h3 style="margin:auto auto; width: 1200px; ">Valstu statistika<br/></h3>
		<div style="margin:auto auto; width: 1200px;">
			<br/>Kopā: <?php echo count($kodi); ?> / <?php echo (count($europe) + count($oceania) + count($asia) + count($northAmerica) + count($southAmerica) + count($middleEast) + count($africa)); ?>
			<br/>
			<ul>
				<li>Eiropa: <?php echo count(array_intersect($kodi, $europe)); ?> / <?php echo count($europe); ?></li>
				<li>Āzija: <?php echo count(array_intersect($kodi, $asia)); ?> / <?php echo count($asia); ?></li>
				<li>Āfrika: <?php echo count(array_intersect($kodi, $africa)); ?> / <?php echo count($africa); ?></li>
				<li>Tuvie austrumi: <?php echo count(array_intersect($kodi, $middleEast)); ?> / <?php echo count($middleEast); ?></li>
				<li>Okeānija: <?php echo count(array_intersect($kodi, $oceania)); ?> / <?php echo count($oceania); ?></li>
				<li>Ziemeļamerika: <?php echo count(array_intersect($kodi, $northAmerica)); ?> / <?php echo count($northAmerica); ?></li>
				<li>Dienvidamerika: <?php echo count(array_intersect($kodi, $southAmerica)); ?> / <?php echo count($southAmerica); ?></li>
			</ul>
		</div>
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

<?php

function drawRegionMap($region, $reg_code){
    ?>
    <script type="text/javascript">
    google.load('visualization', '1', {'packages': ['geochart']});
    google.setOnLoadCallback(drawVisualization);
    function drawVisualization() {var data = new google.visualization.DataTable();

     data.addColumn('string', 'Province');
     data.addColumn('number', 'Value');  
     data.addColumn({type:'string', role:'tooltip'});
        <?php
        foreach ($reg_code as $key => $value){
           if ($value['sk']==1) {$tviti=" bilde";} else {$tviti=" bildes";}
           if ($value['cc'] == $region){
            ?>
            data.addRows([[ '<?php echo $value['cc']."-".$key;?>',<?php echo $value['sk'];?>,'<?php echo str_replace("'","",$value['ad'])." - ".$value['sk'].$tviti;?>']]);
            <?
           }
        }
        ?>
        var options = {
            resolution: 'provinces',
            region:'<?php echo $region; ?>'
        };

        var chart = new google.visualization.GeoChart(document.getElementById('regions_<?php echo $region; ?>_div'));

        chart.draw(data, options);
      }
    </script>
    <?php
}