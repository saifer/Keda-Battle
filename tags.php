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
<link rel="stylesheet" type="text/css" href="includes/tag/css/wordcloud.css">
</head>
<body style='margin: 0px; height: 100%;'>
<div style='position: fixed; top: 0; left: 0; width: 50%; height: 813%; background-color: black; z-index: 1;'></div>
<div style='position: absolute; top: 0; left: 0; z-index: 2; width: 100%;'>
<h2 style="margin:auto auto;text-align:center;padding:5px;margin-top:20px;background-color:lightgrey;border-radius:15px;width:250px;opacity:0.7">Atslēgvārdi</h2>
<div style='margin:auto auto;text-align:center;margin-top:15px;padding:5px;background-color:lightgrey;border-radius:15px;opacity:0.99'>
<br/>
<?php
require 'includes/tag/classes/wordcloud.class.php';
include('includes/init_sql.php');

$vardi = mysql_query("SELECT tag from tags");

$cloud = new wordCloud();
//jāuztaisa vēl, lai, uzklikojot uz kādu ēdienu, atvērtu visus tvītus, kas to pieminējuši...
while($r=mysql_fetch_array($vardi)){
	$nom = str_replace(" ", "",$r["tag"]);
	$cloud->addWord(array('word' => $nom, 'url' => 'vards.php?v='.urlencode($nom)));
}
$cloud->orderBy('word','ASC');
$myCloud = $cloud->showCloud('array');
foreach ($myCloud as $cloudArray) {
  echo ' &nbsp; <a style="z-index:1;text-decoration:none; color:black" href="'.$cloudArray['url'].'" class="word size'.$cloudArray['range'].'">'.$cloudArray['word'].'</a> &nbsp;';
}
?>
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