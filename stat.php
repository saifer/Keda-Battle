<?php
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include('includes/init_sql.php');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lv" lang="lv">
<head>
<title>Kedas cīņa - statistika</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="description" content="Salīdzini Kedas bildes!"/>
<meta name="keywords" content="Keda, foto, attēli, salīdzinājums"/>
<meta name="author" content="Matīss Rikters"/>
<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="includes/style.css">
<link rel="stylesheet" type="text/css" href="includes/jq/css/smoothness/jquery-ui-1.10.1.custom.css" />
<script type="text/javascript" src="includes/jq/js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="includes/jq/js/jquery-ui-1.10.1.custom.js"></script>
<script>
$(function() {
$("#tabs").tabs({
                fx: { height: 'toggle', opacity: 'toggle'},
                show: function(event, ui) {
                          if (ui.panel.id == "tabs-4") {
                                  $(ui.panel).css("height","100%")
                                initialize()
                                }}
                });
});
</script>
</head>
<body style='margin: 0px; height: 100%;'>
<div style='position: fixed; top: 0; left: 0; width: 50%; height: 813%; background-color: black; z-index: 1;'></div>
<div style='position: absolute; top: 0; left: 0; z-index: 2; width: 100%;'>
<br/>
<?php

function aasort (&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}

function get_chart_data($data_name, $year = ""){
	global $connection;
	if($data_name == "day" || $data_name == "month"){
        $origName = $data_name;
		$query_string = "select distinct ".$data_name.", count(".$data_name.") skaits, count(distinct album) albumi from ratings where ".$data_name." IS NOT NULL ".($year==""?"":" AND year = '".$year."'")." AND ".$data_name." != '0' group by ".$data_name." order by ".$data_name." asc";
		$data_name = "albumi";
	}else{
		$query_string = "select distinct ".$data_name.", count(".$data_name.") skaits from ratings WHERE ".$data_name." IS NOT NULL ".($year==""?"":" AND year = '".$year."'")." AND ".$data_name." != '0' AND ".$data_name." != '' group by ".$data_name." order by skaits desc".($data_name!="model"?" LIMIT 20":"");
	}
	$query = mysqli_query($connection, $query_string);
	$i=0;
	while($r1=mysqli_fetch_array($query)){
		$count[$i]=$r1["skaits"];
		$value[$i]=$r1[$data_name];
        if($data_name == "albumi"){
            $dayVal = $r1[$origName];
            if($origName == "day" && $dayVal == "7")
                $dayVal = "0";
            $day[$i]=$dayVal;
        }
		$i++;
	}
	return array($count, $value, $day);
}
function get_count_data($data_name, $year = ""){
	global $connection;
	switch($data_name){
		case "skaits":
			$query_string = "SELECT count(*) skaits FROM ratings".($year==""?"":" WHERE year = '".$year."'");
			break;
		case "albums":
			$query_string = "SELECT count(distinct album) skaits FROM ratings".($year==""?"":" WHERE year = '".$year."'");
			break;
		case "neskatitas":
			$query_string = "SELECT count(*) skaits FROM ratings WHERE views is null".($year==""?"":" AND year = '".$year."'");
			break;
		case "bezbalsim":
			$query_string = "SELECT count(*) skaits FROM ratings WHERE votes<1".($year==""?"":" AND year = '".$year."'");
			break;
		case "vairak":
			$query_string = "SELECT count(*) skaits FROM ratings WHERE votes>1".($year==""?"":" AND year = '".$year."'");
			break;
		case "skatits":
			$query_string = "SELECT sum(views) skaits FROM ratings WHERE views>0".($year==""?"":" AND year = '".$year."'");
			break;
		case "balsots":
			$query_string = "SELECT sum(votes) skaits FROM ratings where votes>0 ".($year==""?"":" AND year = '".$year."'");
			break;
		case "maksimalais":
			$query_string = "SELECT max(votes) skaits FROM ratings".($year==""?"":" WHERE year = '".$year."'");
			break;
	}
	$query = mysqli_query($connection, $query_string);
	while($r1=mysqli_fetch_array($query)){
		$skaits=$r1["skaits"];
	}
	return $skaits;
}

$skaits 		= get_count_data("skaits");//kopā bilžu
$albums 		= get_count_data("albums");//kopā albumu
$neskatitas 	= get_count_data("neskatitas");//Neskatītas bildes
$bezbalsim 		= get_count_data("bezbalsim");//Bildes bez balsīm
$vairak 		= get_count_data("vairak");//vairāk par 1 balsi
$skatits 		= get_count_data("skatits");//bildes skatītas
$balsots 		= get_count_data("balsots");//bildes ar balsojumiem
$maksimalais 	= get_count_data("maksimalais");//visvairāk balsu

$aaa=0;
//ISO
$fs 		= get_chart_data("iso");
$isosk 		= $fs[0];
$isoo 		= $fs[1];
//F-stop
$fs 		= get_chart_data("fstop");
$fstopsk 	= $fs[0];
$fstopp 	= $fs[1];
//Shutter
$fs 		= get_chart_data("exposure");
$shuttsk 	= $fs[0];
$shutt 		= $fs[1];
//Focal length
$fs 		= get_chart_data("focallength");
$focalsk 	= $fs[0];
$focal 		= $fs[1];
//Camera model
$fs 		= get_chart_data("model");
$modelsk 	= $fs[0];
$modelz 	= $fs[1];
//Month
$fs 		= get_chart_data("month");
$monthsk 	= $fs[0];
$monthskalb = $fs[1];
//Weekday
$fs 		= get_chart_data("day");
$daysk 		= $fs[0];
$dayskal 	= $fs[1];
//year
$kopskaits = mysqli_query($connection, "select year, count(distinct album) albumi, count(year) bildes from ratings group by year order by year asc");
while($r1=mysqli_fetch_array($kopskaits)){
	$yearsk[$aaa]=$r1["bildes"];
	$yearalb[$aaa]=$r1["albumi"];
	$yearz[$aaa]=$r1["year"];
	$aaa++;
}
$aaa=0;
//year-rating
$kopskaits = mysqli_query($connection, "SELECT DISTINCT year, (sum( votes ) / sum( views )) reitings FROM ratings GROUP BY year ORDER BY year ASC");
while($r1=mysqli_fetch_array($kopskaits)){
	$gr_reit[$aaa]=$r1["reitings"];
	$gr_gad[$aaa]=$r1["year"];
	//katra gada dati
	$skaitsG[$r1["year"]] 		= get_count_data("skaits", $r1["year"]);//kopā bilžu
	$albumsG[$r1["year"]] 		= get_count_data("albums", $r1["year"]);//kopā albumu
	$neskatitasG[$r1["year"]] 	= get_count_data("neskatitas", $r1["year"]);//Neskatītas bildes
	$bezbalsimG[$r1["year"]] 	= get_count_data("bezbalsim", $r1["year"]);//Bildes bez balsīm
	$vairakG[$r1["year"]] 		= get_count_data("vairak", $r1["year"]);//vairāk par 1 balsi
	$skatitsG[$r1["year"]] 		= get_count_data("skatits", $r1["year"]);//bildes skatītas
	$balsotsG[$r1["year"]] 		= get_count_data("balsots", $r1["year"]);//bildes ar balsojumiem
	$maksimalaisG[$r1["year"]] 	= get_count_data("maksimalais", $r1["year"]);//visvairāk balsu
	
	$fs 						= get_chart_data("model", $r1["year"]);//Camera model
	$modelskG[$r1["year"]] 		= $fs[0];
	$modelzG[$r1["year"]] 		= $fs[1];
	$fs 						= get_chart_data("month", $r1["year"]);//Month
	$monthskG[$r1["year"]] 		= $fs[0];
	$monthskalbG[$r1["year"]] 	= $fs[1];
	$month_num[$r1["year"]] 	= $fs[2];
	$fs 						= get_chart_data("day", $r1["year"]);//Weekday
	$dayskG[$r1["year"]] 		= $fs[0];
	$dayskalG[$r1["year"]] 		= $fs[1];
	$day_num[$r1["year"]] 		= $fs[2];
	$fs 						= get_chart_data("iso", $r1["year"]);//ISO
	$isoskG[$r1["year"]] 		= $fs[0];
	$isooG[$r1["year"]] 		= $fs[1];
	$fs 						= get_chart_data("fstop", $r1["year"]);//F-stop
	$fstopskG[$r1["year"]] 		= $fs[0];
	$fstoppG[$r1["year"]] 		= $fs[1];
	$fs 						= get_chart_data("exposure", $r1["year"]);//Shutter
	$shuttskG[$r1["year"]] 		= $fs[0];
	$shuttG[$r1["year"]] 		= $fs[1];
	$fs 						= get_chart_data("focallength", $r1["year"]);//Focal length
	$focalskG[$r1["year"]] 		= $fs[0];
	$focalG[$r1["year"]] 		= $fs[1];
	
	$aaa++;
}

$xxx=0;
for ($c=$maksimalais;$c>0;$c--){
	//visvairāk balsu
	$kopskaits = mysqli_query($connection, "SELECT count(*) skaits FROM ratings where votes='$c'");
	while($r1=mysqli_fetch_array($kopskaits)){
		$sk[$xxx]=$r1["skaits"];
		$xxx++;
	}
}
?>
<h2 style="margin:auto auto;text-align:center;padding:5px;background-color:lightgrey;border-radius:15px;width:250px;opacity:0.7">Statistika</h2>
<div style="margin:auto auto;padding:5px;padding-left:20px;background-color:lightgrey;border-radius:15px;width:85%;opacity:0.99;margin-top:20px;">

<div id="tabs">
<ul>
	<li><a href="#tabs-1">Statistika</a></li>
	<li><a href="#tabs-2">EXIF dati</a></li>
	<?php
		$tabCount = 3;
		for ($m=0;$m<count($gr_gad);$m++){
			echo '<li><a href="#tabs-'.$tabCount.'">\''.substr($gr_gad[$m], -2).'</a></li>'; 
			$tabCount++;
		}
	?>
</ul>
<div id="tabs-1">
<div style="margin-top:10px;">
	Kopā bilžu: <b><?php echo $skaits;?></b>. Kopā albumu: <b><?php echo $albums;?></b><br/>
	Balsots par bildēm <b><?php echo $balsots;?></b> reižu<br/>
	Vēl ne reizi cīņā neparādījušās bildes: <b><?php echo $neskatitas;?></b><br/>
	Bildes bez balsīm: <b><?php echo $bezbalsim;?></b><br/>
	Bildes ar vairāk par vienu balsi: <b><?php echo $vairak;?></b><br/>
	<a href="http://lielakeda.lv/battle/get/updateAllNew.php">Pārbaudīt, vai ir izmaiņas</a><br/>
	</div>
		<div style="margin-left:-90px;">
		<div style="float:left;width:40%;height:300px;padding-right:90px;margin-left:-20px" id="chart_div"></div>
		<div style="float:left;width:35%;height:300px;padding-right:70px;" id="chart_div10"></div>
		<div style="float:left;width:40%;height:400px;padding-right:70px;" id="chart_div6"></div>
		<div style="float:left;width:40%;height:400px;padding-right:70px;" id="chart_div7"></div>
		<div style="float:left;width:40%;height:300px;padding-right:50px;margin-left:30px" id="chart_div8"></div>
		<div style="float:left;width:40%;height:300px;padding-right:50px;" id="chart_div9"></div>
		</div>
	<br style="clear:both;"/>
</div>
<div id="tabs-2">
	<div style="margin-left:-110px;">
	<div style="float:left;width:40%;height:800px;padding-right:60px;" id="chart_div2"></div>
	<div style="float:left;width:40%;height:800px;padding-right:60px;" id="chart_div3"></div>
	<div style="float:left;width:40%;height:800px;padding-right:60px;" id="chart_div4"></div>
	<div style="float:left;width:40%;height:800px;padding-right:60px;" id="chart_div5"></div>
	</div>
<br style="clear:both;"/>
</div>
	<?php
		$tabCount = 3;
		for ($m=0;$m<count($gr_gad);$m++){
			echo '<div id="tabs-'.$tabCount.'">
				<div style="margin-left:-90px;">
					<div style="float:left;width:36%;height:300px;margin-left:100px">
						Kopā bilžu: <b>'.$skaitsG[$gr_gad[$m]].'</b>. Kopā albumu: <b>'.$albumsG[$gr_gad[$m]].'</b><br/>
						Balsots par bildēm <b>'.$balsotsG[$gr_gad[$m]].'</b> reižu<br/>
						Vēl ne reizi cīņā neparādījušās bildes: <b>'.$neskatitasG[$gr_gad[$m]].'</b><br/>
						Bildes bez balsīm: <b>'.$bezbalsimG[$gr_gad[$m]].'</b><br/>
						Bildes ar vairāk par vienu balsi: <b>'.$vairakG[$gr_gad[$m]].'</b><br/>
					</div>
					<div style="float:left;width:40%;height:400px;padding-right:50px;margin-top:-40px" id="chart_div2'.$gr_gad[$m].'"></div>
					<div style="float:left;width:40%;height:300px;padding-right:50px;margin-left:30px" id="chart_div3'.$gr_gad[$m].'"></div>
					<div style="float:left;width:40%;height:300px;padding-right:50px;" id="chart_div4'.$gr_gad[$m].'"></div>
					<div style="float:left;width:40%;height:800px;padding-right:60px;" id="chart_div5'.$gr_gad[$m].'"></div>
					<div style="float:left;width:40%;height:800px;padding-right:60px;" id="chart_div6'.$gr_gad[$m].'"></div>
					<div style="float:left;width:40%;height:800px;padding-right:60px;" id="chart_div7'.$gr_gad[$m].'"></div>
					<div style="float:left;width:40%;height:800px;padding-right:60px;" id="chart_div8'.$gr_gad[$m].'"></div>
				</div>
				<br style="clear:both;"/>
			</div>';
			$tabCount++;
		}
        $dayArray = array(
            array(6, "Sv"),
            array(0, "Pi"),
            array(1, "Ot"),
            array(2, "Tr"),
            array(3, "Ce"),
            array(4, "Pk"),
            array(5, "Se")
        );
        $monthArray = array(
            array(-1, "None"),
            array(0, "Jan"),
            array(1, "Feb"),
            array(2, "Mar"),
            array(3, "Apr"),
            array(4, "Mai"),
            array(5, "Jūn"),
            array(6, "Jūl"),
            array(7, "Aug"),
            array(8, "Sep"),
            array(9, "Okt"),
            array(10, "Nov"),
            array(11, "Dec")
        );
        
        
        function padDayArray($arrayToPad){
            for($i = 0; $i < 7; $i++){
                if(!isset($arrayToPad[$i]))
                    $arrayToPad[$i] = "0";
            }
            return $arrayToPad;
        }
        
        function padDays($arrayToPad){
            for($i = 0; $i < 7; $i++){
                $k = strval($i);
                if(!in_array($k,$arrayToPad))
                    $arrayToPad[] = $k;
            }
            return $arrayToPad;
        }
        function padMonthArray($arrayToPad){
            for($i = 1; $i < 12; $i++){
                if(!isset($arrayToPad[$i]))
                    $arrayToPad[$i] = "0";
            }
            return $arrayToPad;
        }
        
        function padMonths($arrayToPad){
            for($i = 1; $i < 13; $i++){
                $k = strval($i);
                if(!in_array($k,$arrayToPad))
                    $arrayToPad[] = $k;
            }
            return $arrayToPad;
        }
        ?>
</div>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<?php
	for ($gds=0;$gds<count($gr_gad);$gds++){
        $dayskG[$gr_gad[$gds]]      = padDayArray($dayskG[$gr_gad[$gds]]);
        $dayskalG[$gr_gad[$gds]]    = padDayArray($dayskalG[$gr_gad[$gds]]);
        $day_num[$gr_gad[$gds]]     = padDays($day_num[$gr_gad[$gds]]);
        
        $monthskG[$gr_gad[$gds]]    = padMonthArray($monthskG[$gr_gad[$gds]]);
        $monthskalbG[$gr_gad[$gds]] = padMonthArray($monthskalbG[$gr_gad[$gds]]);
        $month_num[$gr_gad[$gds]]   = padMonths($month_num[$gr_gad[$gds]]);
?>
		<script type="text/javascript">
		  var chart;
		  google.load('visualization', '1.1', {'packages':['corechart', 'calendar']});
		  google.setOnLoadCallback(drawChart);
		  function drawChart() {
		  var data = new google.visualization.DataTable();
		  data.addColumn('string', 'Topping');
		  data.addColumn('number', 'Slices');
		  data.addRows([
<?php
		$outputArray = array();
		for ($m=0;$m<count($modelzG[$gr_gad[$gds]]);$m++){
			$outputArray[] = array('Nosaukums' => $modelzG[$gr_gad[$gds]][$m], 'Skaits' => $modelskG[$gr_gad[$gds]][$m]);
		}
		for ($m=0;$m<count($modelz);$m++){
			if(!in_array($modelz[$m], $modelzG[$gr_gad[$gds]])){
				$outputArray[] = array('Nosaukums' => $modelz[$m], 'Skaits' => 0);
			}
		}
		
		aasort($outputArray,"Nosaukums");

		for ($m=0;$m<count($outputArray);$m++){
			echo "['".$outputArray[$m]['Nosaukums']."',".$outputArray[$m]['Skaits']."]";
			if ($m!=count($outputArray)-1) echo ",";
		}
?>
			]);
		  var options = {'title':'Uzņemts ar',
				  'legend': 'none',
				  'pieSliceText': 'label',
				  'width':650,
				  'height':400,
				  'backgroundColor':'transparent',
				  legend:{position: 'right', 
				  textStyle: {color: 'black', fontSize: 11}},
				  pieSliceTextStyle:{color: 'black', fontSize: 8, bold: true},
				  colors: ['#e2f200', '#00a2f2', '#f27999', '#b6eef2', '#9173e6', '#e2e6ac', '#3a00d9', '#66aacc', '#00eeff', '#ff0000', '#cc00ff', '#ff00aa', '#40ffa6', '#73ff40', '#f780ff', '#f2bfff', '#ffc8bf', '#f28100', '#bf0000', '#30bfa3', '#bf8660', '#b22d3e', '#00a62c', '#a66f00', '#a65395', '#265499', '#999126', '#806460', '#686080', '#2e0073', '#73005c']
			};
		  chart = new google.visualization.PieChart(document.getElementById('chart_div2<?php echo $gr_gad[$gds];?>'));
		  chart.draw(data, options);
		  }
		</script>
		<script type="text/javascript">
		  google.setOnLoadCallback(drawChart);
		  function drawChart() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Diena');
			data.addColumn('number', 'Bildes');
			data.addColumn('number', 'Albumi');
					 data.addRows(12);			
					 data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][0])][0]; ?>, 0, "<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][0])][1]; ?>");
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][0])][0]; ?>, 1, <?php echo $monthskG[$gr_gad[$gds]][0];?>);
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][0])][0]; ?>, 2, <?php echo $monthskalbG[$gr_gad[$gds]][0];?>);
					 data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][1])][0]; ?>, 0, "<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][1])][1]; ?>");
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][1])][0]; ?>, 1, <?php echo $monthskG[$gr_gad[$gds]][1];?>);
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][1])][0]; ?>, 2, <?php echo $monthskalbG[$gr_gad[$gds]][1];?>);
					 data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][2])][0]; ?>, 0, "<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][2])][1]; ?>");
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][2])][0]; ?>, 1, <?php echo $monthskG[$gr_gad[$gds]][2];?>);
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][2])][0]; ?>, 2, <?php echo $monthskalbG[$gr_gad[$gds]][2];?>);
					 data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][3])][0]; ?>, 0, "<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][3])][1]; ?>");
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][3])][0]; ?>, 1, <?php echo $monthskG[$gr_gad[$gds]][3];?>);
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][3])][0]; ?>, 2, <?php echo $monthskalbG[$gr_gad[$gds]][3];?>);
					 data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][4])][0]; ?>, 0, "<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][4])][1]; ?>");
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][4])][0]; ?>, 1, <?php echo $monthskG[$gr_gad[$gds]][4];?>);
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][4])][0]; ?>, 2, <?php echo $monthskalbG[$gr_gad[$gds]][4];?>);
					 data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][5])][0]; ?>, 0, "<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][5])][1]; ?>");
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][5])][0]; ?>, 1, <?php echo $monthskG[$gr_gad[$gds]][5];?>);
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][5])][0]; ?>, 2, <?php echo $monthskalbG[$gr_gad[$gds]][5];?>);
					 data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][6])][0]; ?>, 0, "<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][6])][1]; ?>");
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][6])][0]; ?>, 1, <?php echo $monthskG[$gr_gad[$gds]][6];?>);
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][6])][0]; ?>, 2, <?php echo $monthskalbG[$gr_gad[$gds]][6];?>);
					 data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][7])][0]; ?>, 0, "<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][7])][1]; ?>");
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][7])][0]; ?>, 1, <?php echo $monthskG[$gr_gad[$gds]][7];?>);
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][7])][0]; ?>, 2, <?php echo $monthskalbG[$gr_gad[$gds]][7];?>);
					 data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][8])][0]; ?>, 0, "<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][8])][1]; ?>");
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][8])][0]; ?>, 1, <?php echo $monthskG[$gr_gad[$gds]][8];?>);
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][8])][0]; ?>, 2, <?php echo $monthskalbG[$gr_gad[$gds]][8];?>);
					 data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][9])][0]; ?>, 0, "<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][9])][1]; ?>");
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][9])][0]; ?>, 1, <?php echo $monthskG[$gr_gad[$gds]][9];?>);
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][9])][0]; ?>, 2, <?php echo $monthskalbG[$gr_gad[$gds]][9];?>);
					 data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][10])][0]; ?>, 0, "<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][10])][1]; ?>");
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][10])][0]; ?>, 1, <?php echo $monthskG[$gr_gad[$gds]][10];?>);
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][10])][0]; ?>, 2, <?php echo $monthskalbG[$gr_gad[$gds]][10];?>);
					 data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][11])][0]; ?>, 0, "<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][11])][1]; ?>");
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][11])][0]; ?>, 1, <?php echo $monthskG[$gr_gad[$gds]][11];?>);
                     data.setValue(<?php echo $monthArray[intval($month_num[$gr_gad[$gds]][11])][0]; ?>, 2, <?php echo $monthskalbG[$gr_gad[$gds]][11];?>);
					 var chart = new google.visualization.ColumnChart(document.getElementById('chart_div3<?php echo $gr_gad[$gds];?>'));
			chart.draw(data, {title:'Mēnesis',width: 485, height: 300,'backgroundColor':'transparent',vAxis: {maxValue: 800,textStyle:{color: 'blue'}},series:{1:{targetAxisIndex:1}},vAxes:{1:{maxValue:30,textStyle:{color: 'red'}}}});
			}
		</script>
		<script type="text/javascript">
		  google.setOnLoadCallback(drawChart);
		  function drawChart() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Diena');
			data.addColumn('number', 'Bildes');
			data.addColumn('number', 'Albumi');
					 data.addRows(7);			
					 data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][0])][0]; ?>, 0, "<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][0])][1]; ?>");
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][0])][0]; ?>, 1, <?php echo $dayskG[$gr_gad[$gds]][0];?>);
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][0])][0]; ?>, 2, <?php echo $dayskalG[$gr_gad[$gds]][0];?>);
					 data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][1])][0]; ?>, 0, "<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][1])][1]; ?>");
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][1])][0]; ?>, 1, <?php echo $dayskG[$gr_gad[$gds]][1];?>);
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][1])][0]; ?>, 2, <?php echo $dayskalG[$gr_gad[$gds]][1];?>);
					 data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][2])][0]; ?>, 0, "<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][2])][1]; ?>");
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][2])][0]; ?>, 1, <?php echo $dayskG[$gr_gad[$gds]][2];?>);
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][2])][0]; ?>, 2, <?php echo $dayskalG[$gr_gad[$gds]][2];?>);
					 data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][3])][0]; ?>, 0, "<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][3])][1]; ?>");
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][3])][0]; ?>, 1, <?php echo $dayskG[$gr_gad[$gds]][3];?>);
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][3])][0]; ?>, 2, <?php echo $dayskalG[$gr_gad[$gds]][3];?>);
					 data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][4])][0]; ?>, 0, "<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][4])][1]; ?>");
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][4])][0]; ?>, 1, <?php echo $dayskG[$gr_gad[$gds]][4];?>);
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][4])][0]; ?>, 2, <?php echo $dayskalG[$gr_gad[$gds]][4];?>);
					 data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][5])][0]; ?>, 0, "<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][5])][1]; ?>");
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][5])][0]; ?>, 1, <?php echo $dayskG[$gr_gad[$gds]][5];?>);
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][5])][0]; ?>, 2, <?php echo $dayskalG[$gr_gad[$gds]][5];?>);
					 data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][6])][0]; ?>, 0, "<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][6])][1]; ?>");
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][6])][0]; ?>, 1, <?php echo $dayskG[$gr_gad[$gds]][6];?>);
                     data.setValue(<?php echo $dayArray[intval($day_num[$gr_gad[$gds]][6])][0]; ?>, 2, <?php echo $dayskalG[$gr_gad[$gds]][6];?>);
					 var chart = new google.visualization.ColumnChart(document.getElementById('chart_div4<?php echo $gr_gad[$gds];?>'));
			chart.draw(data, {title:'Diena',width: 485, height: 300,'backgroundColor':'transparent',vAxis: {maxValue: 2000,textStyle:{color: 'blue'}},series:{1:{targetAxisIndex:1}},vAxes:{1:{maxValue:40,textStyle:{color: 'red'}}}});
			}
		</script>
		<script type="text/javascript">  
		  function drawVisualization() {
			// Create and populate the data table.
			var data = new google.visualization.DataTable();
			var raw_data = [
		<?php
		for ($m=0;$m<count($isooG[$gr_gad[$gds]]);$m++){
			echo "['ISO".$isooG[$gr_gad[$gds]][$m]."',".$isoskG[$gr_gad[$gds]][$m]."]";
			if ($m!=count($isooG[$gr_gad[$gds]])-1) echo ",";
		}
		?>
							];
			var years = [''];
			data.addColumn('string', 'Year');
			for (var i = 0; i  < raw_data.length; ++i) {
			  data.addColumn('number', raw_data[i][0]);    
			}
			data.addRows(years.length);
			for (var j = 0; j < years.length; ++j) {    
			  data.setValue(j, 0, years[j].toString());    
			}
			for (var i = 0; i  < raw_data.length; ++i) {
			  for (var j = 1; j  < raw_data[i].length; ++j) {
				data.setValue(j-1, i+1, raw_data[i][j]);    
			  }
			}
			new google.visualization.BarChart(document.getElementById('chart_div5<?php echo $gr_gad[$gds];?>')).
				draw(data,
					 {	title:"ISO",
						width:500, height:800,
						hAxis: {title: "Bilžu skaits"},
						backgroundColor:'transparent'
					  }
				);
		  }
		  google.setOnLoadCallback(drawVisualization);
		</script>
		<script type="text/javascript">
		  function drawVisualization() {
			// Create and populate the data table.
			var data = new google.visualization.DataTable();
			var raw_data = [
		<?php
		for ($m=0;$m<count($fstoppG[$gr_gad[$gds]]);$m++){
			echo "['F".$fstoppG[$gr_gad[$gds]][$m]."',".$fstopskG[$gr_gad[$gds]][$m]."]";
			if ($m!=count($fstoppG[$gr_gad[$gds]])-1) echo ",";
		}
		?>
							];
			var years = [''];
			data.addColumn('string', 'Year');
			for (var i = 0; i  < raw_data.length; ++i) {
			  data.addColumn('number', raw_data[i][0]);    
			}
			data.addRows(years.length);
			for (var j = 0; j < years.length; ++j) {    
			  data.setValue(j, 0, years[j].toString());    
			}
			for (var i = 0; i  < raw_data.length; ++i) {
			  for (var j = 1; j  < raw_data[i].length; ++j) {
				data.setValue(j-1, i+1, raw_data[i][j]);    
			  }
			}
			new google.visualization.BarChart(document.getElementById('chart_div6<?php echo $gr_gad[$gds];?>')).
				draw(data,
					 {	title:"Diafragmas atvērums",
						width:500, height:800,
						hAxis: {title: "Bilžu skaits"},
						backgroundColor:'transparent'
					  }
				);
		  }
		  google.setOnLoadCallback(drawVisualization);
		</script>
		<script type="text/javascript">
		  <?php
		for ($m=0;$m<count($shuttG[$gr_gad[$gds]]);$m++){
			if($shuttG[$gr_gad[$gds]][$m]<1) {$ashspdG[$gr_gad[$gds]][$m]="1/".(round(1/$shuttG[$gr_gad[$gds]][$m]));} else {$ashspdG[$gr_gad[$gds]][$m]=$shuttG[$gr_gad[$gds]][$m];}
		}
		  ?>  
		  function drawVisualization() {
			// Create and populate the data table.
			var data = new google.visualization.DataTable();
			var raw_data = [
		<?php
		for ($m=0;$m<count($ashspdG[$gr_gad[$gds]]);$m++){
			echo "['".$ashspdG[$gr_gad[$gds]][$m]."s',".$shuttskG[$gr_gad[$gds]][$m]."]";
			if ($m!=count($ashspdG[$gr_gad[$gds]])-1) echo ",";
		}
		?>
							];
			var years = [''];
			data.addColumn('string', 'Year');
			for (var i = 0; i  < raw_data.length; ++i) {
			  data.addColumn('number', raw_data[i][0]);    
			}
			data.addRows(years.length);
			for (var j = 0; j < years.length; ++j) {    
			  data.setValue(j, 0, years[j].toString());    
			}
			for (var i = 0; i  < raw_data.length; ++i) {
			  for (var j = 1; j  < raw_data[i].length; ++j) {
				data.setValue(j-1, i+1, raw_data[i][j]);    
			  }
			}
			new google.visualization.BarChart(document.getElementById('chart_div7<?php echo $gr_gad[$gds];?>')).
				draw(data,
					 {	title:"Slēdža ātrums",
						width:500, height:800,
						hAxis: {title: "Bilžu skaits"},
						backgroundColor:'transparent'
					  }
				);
		  }
		  google.setOnLoadCallback(drawVisualization);
		</script>
		<script type="text/javascript">
		  function drawVisualization() {
			// Create and populate the data table.
			var data = new google.visualization.DataTable();
			var raw_data = [
		<?php
		for ($m=0;$m<count($focalG[$gr_gad[$gds]]);$m++){
			echo "['".$focalG[$gr_gad[$gds]][$m]."mm',".$focalskG[$gr_gad[$gds]][$m]."]";
			if ($m!=count($focalG[$gr_gad[$gds]])-1) echo ",";
		}
		?>
							];
			var years = [''];
			data.addColumn('string', 'Year');
			for (var i = 0; i  < raw_data.length; ++i) {
			  data.addColumn('number', raw_data[i][0]);    
			}
			data.addRows(years.length);
			for (var j = 0; j < years.length; ++j) {    
			  data.setValue(j, 0, years[j].toString());    
			}
			for (var i = 0; i  < raw_data.length; ++i) {
			  for (var j = 1; j  < raw_data[i].length; ++j) {
				data.setValue(j-1, i+1, raw_data[i][j]);    
			  }
			}
			new google.visualization.BarChart(document.getElementById('chart_div8<?php echo $gr_gad[$gds];?>')).
				draw(data,
					 {	title:"Fokusa attālums",
						width:500, height:800,
						hAxis: {title: "Bilžu skaits"},
						backgroundColor:'transparent'
					  }
				);
		  }
		  google.setOnLoadCallback(drawVisualization);
		</script>
<?php } ?>
<script type="text/javascript">  
  function drawVisualization() {
	// Create and populate the data table.
	var data = new google.visualization.DataTable();
	var raw_data = [
<?php
for ($m=0;$m<count($sk);$m++){
	echo "['".($maksimalais-$m)." balsis',".$sk[$m]."]";
	if ($m!=count($sk)-1) echo ",";
}
?>
					];
	var years = [''];
	data.addColumn('string', 'Year');
	for (var i = 0; i  < raw_data.length; ++i) {
	  data.addColumn('number', raw_data[i][0]);    
	}
	data.addRows(years.length);
	for (var j = 0; j < years.length; ++j) {    
	  data.setValue(j, 0, years[j].toString());    
	}
	for (var i = 0; i  < raw_data.length; ++i) {
	  for (var j = 1; j  < raw_data[i].length; ++j) {
		data.setValue(j-1, i+1, raw_data[i][j]);    
	  }
	}
	new google.visualization.BarChart(document.getElementById('chart_div')).
		draw(data,
			 {	title:"Balsu īpatsvars",
				width:500, height:300,
				hAxis: {title: "Bilžu skaits"},
				backgroundColor:'transparent'
			  }
		);
  }
  google.setOnLoadCallback(drawVisualization);
</script>
<script type="text/javascript">  
  function drawVisualization() {
	// Create and populate the data table.
	var data = new google.visualization.DataTable();
	var raw_data = [
<?php
for ($m=0;$m<count($isoo);$m++){
	echo "['ISO".$isoo[$m]."',".$isosk[$m]."]";
	if ($m!=count($isoo)-1) echo ",";
}
?>
					];
	var years = [''];
	data.addColumn('string', 'Year');
	for (var i = 0; i  < raw_data.length; ++i) {
	  data.addColumn('number', raw_data[i][0]);    
	}
	data.addRows(years.length);
	for (var j = 0; j < years.length; ++j) {    
	  data.setValue(j, 0, years[j].toString());    
	}
	for (var i = 0; i  < raw_data.length; ++i) {
	  for (var j = 1; j  < raw_data[i].length; ++j) {
		data.setValue(j-1, i+1, raw_data[i][j]);    
	  }
	}
	new google.visualization.BarChart(document.getElementById('chart_div2')).
		draw(data,
			 {	title:"ISO",
				width:500, height:800,
				hAxis: {title: "Bilžu skaits"},
				backgroundColor:'transparent'
			  }
		);
  }
  google.setOnLoadCallback(drawVisualization);
</script>
<script type="text/javascript">
  function drawVisualization() {
	// Create and populate the data table.
	var data = new google.visualization.DataTable();
	var raw_data = [
<?php
for ($m=0;$m<count($fstopp);$m++){
	echo "['F".$fstopp[$m]."',".$fstopsk[$m]."]";
	if ($m!=count($fstopp)-1) echo ",";
}
?>
					];
	var years = [''];
	data.addColumn('string', 'Year');
	for (var i = 0; i  < raw_data.length; ++i) {
	  data.addColumn('number', raw_data[i][0]);    
	}
	data.addRows(years.length);
	for (var j = 0; j < years.length; ++j) {    
	  data.setValue(j, 0, years[j].toString());    
	}
	for (var i = 0; i  < raw_data.length; ++i) {
	  for (var j = 1; j  < raw_data[i].length; ++j) {
		data.setValue(j-1, i+1, raw_data[i][j]);    
	  }
	}
	new google.visualization.BarChart(document.getElementById('chart_div3')).
		draw(data,
			 {	title:"Diafragmas atvērums",
				width:500, height:800,
				hAxis: {title: "Bilžu skaits"},
				backgroundColor:'transparent'
			  }
		);
  }
  google.setOnLoadCallback(drawVisualization);
</script>
<script type="text/javascript">
  <?php
for ($m=0;$m<count($shutt);$m++){
	if($shutt[$m]<1) {$ashspd[$m]="1/".(round(1/$shutt[$m]));} else {$ashspd[$m]=$shutt[$m];}
}
  ?>  
  function drawVisualization() {
	// Create and populate the data table.
	var data = new google.visualization.DataTable();
	var raw_data = [
<?php
for ($m=0;$m<count($ashspd);$m++){
	echo "['".$ashspd[$m]."s',".$shuttsk[$m]."]";
	if ($m!=count($ashspd)-1) echo ",";
}
?>
					];
	var years = [''];
	data.addColumn('string', 'Year');
	for (var i = 0; i  < raw_data.length; ++i) {
	  data.addColumn('number', raw_data[i][0]);    
	}
	data.addRows(years.length);
	for (var j = 0; j < years.length; ++j) {    
	  data.setValue(j, 0, years[j].toString());    
	}
	for (var i = 0; i  < raw_data.length; ++i) {
	  for (var j = 1; j  < raw_data[i].length; ++j) {
		data.setValue(j-1, i+1, raw_data[i][j]);    
	  }
	}
	new google.visualization.BarChart(document.getElementById('chart_div4')).
		draw(data,
			 {	title:"Slēdža ātrums",
				width:500, height:800,
				hAxis: {title: "Bilžu skaits"},
				backgroundColor:'transparent'
			  }
		);
  }
  google.setOnLoadCallback(drawVisualization);
</script>
<script type="text/javascript">
  function drawVisualization() {
	// Create and populate the data table.
	var data = new google.visualization.DataTable();
	var raw_data = [
<?php
for ($m=0;$m<count($focal);$m++){
	echo "['".$focal[$m]."mm',".$focalsk[$m]."]";
	if ($m!=count($focal)-1) echo ",";
}
?>
					];
	var years = [''];
	data.addColumn('string', 'Year');
	for (var i = 0; i  < raw_data.length; ++i) {
	  data.addColumn('number', raw_data[i][0]);    
	}
	data.addRows(years.length);
	for (var j = 0; j < years.length; ++j) {    
	  data.setValue(j, 0, years[j].toString());    
	}
	for (var i = 0; i  < raw_data.length; ++i) {
	  for (var j = 1; j  < raw_data[i].length; ++j) {
		data.setValue(j-1, i+1, raw_data[i][j]);    
	  }
	}
	new google.visualization.BarChart(document.getElementById('chart_div5')).
		draw(data,
			 {	title:"Fokusa attālums",
				width:500, height:800,
				hAxis: {title: "Bilžu skaits"},
				backgroundColor:'transparent'
			  }
		);
  }
  google.setOnLoadCallback(drawVisualization);
</script>
<script type="text/javascript">
  var chart;
  google.load('visualization', '1.1', {'packages':['corechart', 'calendar']});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Topping');
  data.addColumn('number', 'Slices');
  data.addRows([
<?php
for ($m=0;$m<count($modelz);$m++){
	echo "['".$modelz[$m]."',".$modelsk[$m]."]";
	if ($m!=count($modelz)-1) echo ",";
}
?>
	]);
  var options = {'title':'Uzņemts ar',
          'legend': 'none',
          'pieSliceText': 'label',
		  'width':650,
		  'height':400,
		  'backgroundColor':'transparent',
          legend:{position: 'right', textStyle: {color: 'black', fontSize: 11}},
		  pieSliceTextStyle:{color: 'white', fontSize: 8}
	};
  chart = new google.visualization.PieChart(document.getElementById('chart_div6'));
  chart.draw(data, options);
  }
</script>
<script type="text/javascript">
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Gads', 'Bilžu skaits', 'Albumu skaits'],
<?php
for ($m=0;$m<count($yearz);$m++){
	echo "[".$yearz[$m].",".$yearsk[$m].",".($yearalb[$m])."]";
	if ($m!=count($yearz)-1) echo ",";
}
?>
        ]);

        var options = {
					title:'Gads',
					curveType: "none",
					width: 650, height: 350,
					vAxis: {maxValue: 7000, minValue: 0, viewWindow: { min: 0 }, textStyle:{color: 'blue'}, format:'###0'},
					hAxis: {format:'###000'},
					series:{1:{targetAxisIndex:1}},
					backgroundColor:'transparent',
					vAxes:{1:{maxValue:200, minValue: 0, viewWindow: { min: 0 }, textStyle:{color: 'red'}}}
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div7'));
		
		var formatter = new google.visualization.NumberFormat({ groupingSymbol: '', fractionDigits: 0 });
		formatter.format(data, 0);
		formatter.format(data, 1);
		formatter.format(data, 2);
		
        chart.draw(data, options);
      }
</script>
<script type="text/javascript">
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Gads', 'Reitings'],
<?php
for ($m=0;$m<count($gr_gad);$m++){
	echo "[".$gr_gad[$m].",".$gr_reit[$m]."]";
	if ($m!=count($gr_gad)-1) echo ",";
}
?>
        ]);

        var options = {
					title:'Vidējais reitings pa gadiem',
					curveType: "none",
					legend:'top',
					width: 650, height: 350,
					vAxis: {maxValue: 1, minValue: 0, viewWindow: { min: 0 }, textStyle:{color: 'blue'}},
					hAxis: {format:'###000'},
					backgroundColor:'transparent',
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div10'));
		
		var formatter = new google.visualization.NumberFormat({ groupingSymbol: '', fractionDigits: 0 });
		formatter.format(data, 0);
		
        chart.draw(data, options);
      }
</script>
<script type="text/javascript">
  google.setOnLoadCallback(drawChart);
  function drawChart() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Diena');
	data.addColumn('number', 'Bildes');
	data.addColumn('number', 'Albumi');
			 data.addRows(12);			
			 data.setValue(0, 0, "Jan");data.setValue(0, 1, <?php echo $monthsk[0];?>);data.setValue(0, 2, <?php echo $monthskalb[0];?>);
			 data.setValue(1, 0, "Feb");data.setValue(1, 1, <?php echo $monthsk[1];?>);data.setValue(1, 2, <?php echo $monthskalb[1];?>);
			 data.setValue(2, 0, "Mar");data.setValue(2, 1, <?php echo $monthsk[2];?>);data.setValue(2, 2, <?php echo $monthskalb[2];?>);
			 data.setValue(3, 0, "Apr");data.setValue(3, 1, <?php echo $monthsk[3];?>);data.setValue(3, 2, <?php echo $monthskalb[3];?>);
			 data.setValue(4, 0, "Mai");data.setValue(4, 1, <?php echo $monthsk[4];?>);data.setValue(4, 2, <?php echo $monthskalb[4];?>);
			 data.setValue(5, 0, "Jūn");data.setValue(5, 1, <?php echo $monthsk[5];?>);data.setValue(5, 2, <?php echo $monthskalb[5];?>);
			 data.setValue(6, 0, "Jūl");data.setValue(6, 1, <?php echo $monthsk[6];?>);data.setValue(6, 2, <?php echo $monthskalb[6];?>);
			 data.setValue(7, 0, "Aug");data.setValue(7, 1, <?php echo $monthsk[7];?>);data.setValue(7, 2, <?php echo $monthskalb[7];?>);
			 data.setValue(8, 0, "Sep");data.setValue(8, 1, <?php echo $monthsk[8];?>);data.setValue(8, 2, <?php echo $monthskalb[8];?>);
			 data.setValue(9, 0, "Okt");data.setValue(9, 1, <?php echo $monthsk[9];?>);data.setValue(9, 2, <?php echo $monthskalb[9];?>);
			 data.setValue(10, 0, "Nov");data.setValue(10, 1, <?php echo $monthsk[10];?>);data.setValue(10, 2, <?php echo $monthskalb[10];?>);
			 data.setValue(11, 0, "Dec");data.setValue(11, 1, <?php echo $monthsk[11];?>);data.setValue(11, 2, <?php echo $monthskalb[11];?>);
			 var chart = new google.visualization.ColumnChart(document.getElementById('chart_div8'));
	chart.draw(data, {title:'Mēnesis',width: 485, height: 300,'backgroundColor':'transparent',vAxis: {maxValue: 2000,textStyle:{color: 'blue'}},series:{1:{targetAxisIndex:1}},vAxes:{1:{maxValue:100,textStyle:{color: 'red'}}}});
	}
</script>
<script type="text/javascript">
  google.setOnLoadCallback(drawChart);
  function drawChart() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Diena');
	data.addColumn('number', 'Bildes');
	data.addColumn('number', 'Albumi');
			 data.addRows(7);			
			 data.setValue(6, 0, "Sv");data.setValue(6, 1, <?php echo $daysk[6];?>);data.setValue(6, 2, <?php echo $dayskal[6];?>);
			 data.setValue(0, 0, "Pi");data.setValue(0, 1, <?php echo $daysk[0];?>);data.setValue(0, 2, <?php echo $dayskal[0];?>);
			 data.setValue(1, 0, "Ot");data.setValue(1, 1, <?php echo $daysk[1];?>);data.setValue(1, 2, <?php echo $dayskal[1];?>);
			 data.setValue(2, 0, "Tr");data.setValue(2, 1, <?php echo $daysk[2];?>);data.setValue(2, 2, <?php echo $dayskal[2];?>);
			 data.setValue(3, 0, "Ce");data.setValue(3, 1, <?php echo $daysk[3];?>);data.setValue(3, 2, <?php echo $dayskal[3];?>);
			 data.setValue(4, 0, "Pk");data.setValue(4, 1, <?php echo $daysk[4];?>);data.setValue(4, 2, <?php echo $dayskal[4];?>);
			 data.setValue(5, 0, "Se");data.setValue(5, 1, <?php echo $daysk[5];?>);data.setValue(5, 2, <?php echo $dayskal[5];?>);
			 var chart = new google.visualization.ColumnChart(document.getElementById('chart_div9'));
	chart.draw(data, {title:'Diena',width: 485, height: 300,'backgroundColor':'transparent',vAxis: {maxValue: 6000,textStyle:{color: 'blue'}},series:{1:{targetAxisIndex:1}},vAxes:{1:{maxValue:200,textStyle:{color: 'red'}}}});
	}
</script>
	
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