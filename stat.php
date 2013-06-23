<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lv" lang="lv">
<head>
<title>Kedas cīņa - statistika</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="description" content="Salīdzini Kedas bildes!"/>
<meta name="keywords" content="Keda, foto, attēli, salīdzinājums"/>
<meta name="author" content="Matīss Rikters"/>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
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
include('includes/init_sql.php');
//kopā bilžu
$kopskaits = mysql_query("SELECT count(*) skaits FROM ratings");
while($r1=mysql_fetch_array($kopskaits)){
	$skaits=$r1["skaits"];
}
//kopā albumu
$kopskaits = mysql_query("SELECT count(distinct album) skaits FROM ratings");
while($r1=mysql_fetch_array($kopskaits)){
	$albums=$r1["skaits"];
}
//Neskatītas bildes
$kopskaits = mysql_query("SELECT count(*) skaits FROM ratings where views is null");
while($r1=mysql_fetch_array($kopskaits)){
	$neskatitas=$r1["skaits"];
}
//Bildes bez balsīm
$kopskaits = mysql_query("SELECT count(*) skaits FROM ratings where votes<1");
while($r1=mysql_fetch_array($kopskaits)){
	$bezbalsim=$r1["skaits"];
}
//vairāk par 1 balsi
$kopskaits = mysql_query("SELECT count(*) skaits FROM ratings where votes>1");
while($r1=mysql_fetch_array($kopskaits)){
	$vairak=$r1["skaits"];
}
//bildes skatītas
$kopskaits = mysql_query("SELECT count(*) skaits FROM ratings where views>0");
while($r1=mysql_fetch_array($kopskaits)){
	$skatits=$r1["skaits"];
}
//bildes ar balsojumiem
$kopskaits = mysql_query("SELECT count(*) skaits FROM ratings where votes>0");
while($r1=mysql_fetch_array($kopskaits)){
	$balsots=$r1["skaits"];
}
//visvairāk balsu
$kopskaits = mysql_query("SELECT max(votes) maksimalais FROM ratings");
while($r1=mysql_fetch_array($kopskaits)){
	$maksimalais=$r1["maksimalais"];
}
$aaa=0;
//Lielākie albumi
$kopskaits = mysql_query("select distinct album, count(album) skaits from ratings group by album order by skaits desc limit 0,10");
while($r1=mysql_fetch_array($kopskaits)){
	$skaitss[$aaa]=$r1["skaits"];
	$albumss[$aaa]=$r1["album"];
	$aaa++;
}
$aaa=0;
//ISO
$kopskaits = mysql_query("select distinct iso, count(iso) skaits from ratings where iso IS NOT NULL and iso != '0' group by iso order by skaits desc");
while($r1=mysql_fetch_array($kopskaits)){
	$isosk[$aaa]=$r1["skaits"];
	$isoo[$aaa]=$r1["iso"];
	$aaa++;
}
$aaa=0;
//F-stop
$kopskaits = mysql_query("select distinct fstop, count(fstop) skaits from ratings where fstop IS NOT NULL and fstop != '0' group by fstop order by skaits desc");
while($r1=mysql_fetch_array($kopskaits)){
	$fstopsk[$aaa]=$r1["skaits"];
	$fstopp[$aaa]=round($r1["fstop"],1);
	$aaa++;
}
$aaa=0;
//Shutter
$kopskaits = mysql_query("select distinct exposure, count(exposure) skaits from ratings where exposure IS NOT NULL and exposure != '0' group by exposure order by skaits desc");
while($r1=mysql_fetch_array($kopskaits)){
	$shuttsk[$aaa]=$r1["skaits"];
	$shutt[$aaa]=$r1["exposure"];
	$aaa++;
}
$aaa=0;
//Focal length
$kopskaits = mysql_query("select distinct focallength, count(focallength) skaits from ratings where focallength IS NOT NULL and focallength != '0' group by focallength order by skaits desc");
while($r1=mysql_fetch_array($kopskaits)){
	$focalsk[$aaa]=$r1["skaits"];
	$focal[$aaa]=$r1["focallength"];
	$aaa++;
}
$aaa=0;
//year
$kopskaits = mysql_query("select year, count(distinct album) albumi, count(year) bildes from ratings group by year order by year asc");
while($r1=mysql_fetch_array($kopskaits)){
	$yearsk[$aaa]=$r1["bildes"];
	$yearalb[$aaa]=$r1["albumi"];
	$yearz[$aaa]=$r1["year"];
	$aaa++;
}
$aaa=0;
//year-rating
$kopskaits = mysql_query("SELECT DISTINCT year, (sum( votes ) / sum( views )) reitings FROM ratings GROUP BY year ORDER BY year ASC");
while($r1=mysql_fetch_array($kopskaits)){
	$gr_reit[$aaa]=$r1["reitings"];
	$gr_gad[$aaa]=$r1["year"];
	$aaa++;
}
$aaa=0;
//place
$kopskaits = mysql_query("select distinct lng, lat, count(lng) skaits from geo group by lng order by skaits desc");
while($r1=mysql_fetch_array($kopskaits)){
	$placesk[$aaa]=$r1["skaits"];
	$lng[$aaa]=$r1["lng"];
	$lat[$aaa]=$r1["lat"];
	$aaa++;
}
$aaa=0;
//month
$kopskaits = mysql_query("select distinct month, count(month) skaits, count(distinct album) albumi from ratings where month IS NOT NULL and month!= '0' group by month order by month asc");
while($r1=mysql_fetch_array($kopskaits)){
	$monthsk[$aaa]=$r1["skaits"];
	$monthskalb[$aaa]=$r1["albumi"];
	$aaa++;
}
$aaa=0;
//weekday
$kopskaits = mysql_query("select distinct day, count(day) skaits, count(distinct album) albumi from ratings where day IS NOT NULL and day!= '0' group by day order by day asc");
while($r1=mysql_fetch_array($kopskaits)){
	$daysk[$aaa]=$r1["skaits"];
	$dayskal[$aaa]=$r1["albumi"];
	$aaa++;
}
$aaa=0;
//Camera model
$kopskaits = mysql_query("select distinct model, count(model) skaits from ratings where model IS NOT NULL and model!= '' group by model order by skaits desc");
while($r1=mysql_fetch_array($kopskaits)){
	$modelsk[$aaa]=$r1["skaits"];
	$modelz[$aaa]=$r1["model"];
	$aaa++;
}
$xxx=0;
for ($c=$maksimalais;$c>0;$c--){
	//visvairāk balsu
	$kopskaits = mysql_query("SELECT count(*) skaits FROM ratings where votes='$c'");
	while($r1=mysql_fetch_array($kopskaits)){
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
</ul>
<div id="tabs-1">
<div style="margin-top:10px;">
Kopā bilžu: <b><?php echo $skaits;?></b>. Kopā albumu: <b><?php echo $albums;?></b><br/>
Balsots par bildēm <b><?php echo $balsots;?></b> reižu.<br/>
Vēl ne reizi cīņā neparādījušās bildes: <b><?php echo $neskatitas;?></b>.<br/>
Bildes bez balsīm: <b><?php echo $bezbalsim;?></b>.<br/>
Bildes ar vairāk par vienu balsi: <b><?php echo $vairak;?></b>.<br/>
</div>
	<div style="margin-left:-90px;">
	<div style="float:left;width:40%;height:300px;padding-right:90px;margin-left:-20px" id="chart_div"></div>
	<div style="float:left;width:35%;height:300px;padding-right:70px;" id="chart_div10"></div>
	<div style="float:left;width:40%;height:300px;padding-right:70px;" id="chart_div6"></div>
	<div style="float:left;width:40%;height:300px;padding-right:70px;" id="chart_div7"></div>
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
</div>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
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
	['<?php echo $albumss[0];?>', <?php echo $skaitss[0];?>],
	['<?php echo $albumss[1];?>', <?php echo $skaitss[1];?>],
	['<?php echo $albumss[2];?>', <?php echo $skaitss[2];?>],
	['<?php echo $albumss[3];?>', <?php echo $skaitss[3];?>],
	['<?php echo $albumss[4];?>', <?php echo $skaitss[4];?>],
	['<?php echo $albumss[5];?>', <?php echo $skaitss[5];?>],
	['<?php echo $albumss[6];?>', <?php echo $skaitss[6];?>],
	['<?php echo $albumss[7];?>', <?php echo $skaitss[7];?>],
	['<?php echo $albumss[8];?>', <?php echo $skaitss[8];?>],
	['<?php echo $albumss[9];?>', <?php echo $skaitss[9];?>]
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
	new google.visualization.BarChart(document.getElementById('chart_div1')).
		draw(data,
			 {	title:"Lielākie albumi",
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
for ($m=0;$m<20;$m++){
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
for ($m=0;$m<20;$m++){
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
for ($m=0;$m<20;$m++){
	if($shutt[$m]<1) {$ashspd[$m]="1/".(round(1/$shutt[$m]));} else {$ashspd[$m]=$shutt[$m];}
}
  ?>  
  function drawVisualization() {
	// Create and populate the data table.
	var data = new google.visualization.DataTable();
	var raw_data = [
<?php
for ($m=0;$m<20;$m++){
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
for ($m=0;$m<20;$m++){
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
  google.load('visualization', '1.0', {'packages':['corechart']});
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
				 'width':485,
				 'height':300,
				 'backgroundColor':'transparent',
				 'is3D':'true'};
  chart = new google.visualization.PieChart(document.getElementById('chart_div6'));
  chart.draw(data, options);
  }
</script>
<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
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
					curveType: "function",
					width: 485, height: 300,
					vAxis: {maxValue: 7000,textStyle:{color: 'blue'}},
					series:{1:{targetAxisIndex:1}},
					backgroundColor:'transparent',
					vAxes:{1:{maxValue:200,textStyle:{color: 'red'}}}
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div7'));
        chart.draw(data, options);
      }
</script>
<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
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
					title:'Vidējais reitingts pa gadiem',
					curveType: "function",
					width: 485, height: 300,
					vAxis: {maxValue: 1,textStyle:{color: 'blue'}},
					backgroundColor:'transparent',
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div10'));
        chart.draw(data, options);
      }
</script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Diena');
	data.addColumn('number', 'Bildes');
	data.addColumn('number', 'Albumi');
			 data.addRows(12);			
			 data.setValue(0, 0, "Janvāris");data.setValue(0, 1, <?php echo $monthsk[0];?>);data.setValue(0, 2, <?php echo $monthskalb[0];?>);
			 data.setValue(1, 0, "Februāris");data.setValue(1, 1, <?php echo $monthsk[1];?>);data.setValue(1, 2, <?php echo $monthskalb[1];?>);
			 data.setValue(2, 0, "Marts");data.setValue(2, 1, <?php echo $monthsk[2];?>);data.setValue(2, 2, <?php echo $monthskalb[2];?>);
			 data.setValue(3, 0, "Aprīlis");data.setValue(3, 1, <?php echo $monthsk[3];?>);data.setValue(3, 2, <?php echo $monthskalb[3];?>);
			 data.setValue(4, 0, "Maijs");data.setValue(4, 1, <?php echo $monthsk[4];?>);data.setValue(4, 2, <?php echo $monthskalb[4];?>);
			 data.setValue(5, 0, "Jūnijs");data.setValue(5, 1, <?php echo $monthsk[5];?>);data.setValue(5, 2, <?php echo $monthskalb[5];?>);
			 data.setValue(6, 0, "Jūlijs");data.setValue(6, 1, <?php echo $monthsk[6];?>);data.setValue(6, 2, <?php echo $monthskalb[6];?>);
			 data.setValue(7, 0, "Augusts");data.setValue(7, 1, <?php echo $monthsk[7];?>);data.setValue(7, 2, <?php echo $monthskalb[7];?>);
			 data.setValue(8, 0, "Septembris");data.setValue(8, 1, <?php echo $monthsk[8];?>);data.setValue(8, 2, <?php echo $monthskalb[8];?>);
			 data.setValue(9, 0, "Oktobris");data.setValue(9, 1, <?php echo $monthsk[9];?>);data.setValue(9, 2, <?php echo $monthskalb[9];?>);
			 data.setValue(10, 0, "Novembris");data.setValue(10, 1, <?php echo $monthsk[10];?>);data.setValue(10, 2, <?php echo $monthskalb[10];?>);
			 data.setValue(11, 0, "Decembris");data.setValue(11, 1, <?php echo $monthsk[11];?>);data.setValue(11, 2, <?php echo $monthskalb[11];?>);
			 var chart = new google.visualization.ColumnChart(document.getElementById('chart_div8'));
	chart.draw(data, {title:'Mēnesis',width: 485, height: 300,'backgroundColor':'transparent',vAxis: {maxValue: 2000,textStyle:{color: 'blue'}},series:{1:{targetAxisIndex:1}},vAxes:{1:{maxValue:100,textStyle:{color: 'red'}}}});
	}
</script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Diena');
	data.addColumn('number', 'Bildes');
	data.addColumn('number', 'Albumi');
			 data.addRows(7);			
			 data.setValue(6, 0, "Svētdiena");data.setValue(6, 1, <?php echo $daysk[6];?>);data.setValue(6, 2, <?php echo $dayskal[6];?>);
			 data.setValue(0, 0, "Pirmdiena");data.setValue(0, 1, <?php echo $daysk[0];?>);data.setValue(0, 2, <?php echo $dayskal[0];?>);
			 data.setValue(1, 0, "Otradiena");data.setValue(1, 1, <?php echo $daysk[1];?>);data.setValue(1, 2, <?php echo $dayskal[1];?>);
			 data.setValue(2, 0, "Trešdiena");data.setValue(2, 1, <?php echo $daysk[2];?>);data.setValue(2, 2, <?php echo $dayskal[2];?>);
			 data.setValue(3, 0, "Ceturtdiena");data.setValue(3, 1, <?php echo $daysk[3];?>);data.setValue(3, 2, <?php echo $dayskal[3];?>);
			 data.setValue(4, 0, "Piektdiena");data.setValue(4, 1, <?php echo $daysk[4];?>);data.setValue(4, 2, <?php echo $dayskal[4];?>);
			 data.setValue(5, 0, "Sestdiena");data.setValue(5, 1, <?php echo $daysk[5];?>);data.setValue(5, 2, <?php echo $dayskal[5];?>);
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