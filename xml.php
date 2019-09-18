<?php
error_reporting(0); 
header("Content-Type: text/xml; charset=utf-8");
include "includes/init_sql.php";

//Dabū lapu un periodu
$lapa = $_GET['lapa'];

//Izveido XML sākumu
$xml_output = "<?xml version=\"1.0\"?>\n";
$xml_output .= "<entries>\n";

//////////////////////
//		Lapas		//
//////////////////////

//Sākums
//Saitei jāizskatās http://lielakeda.lv/battle/xml.php?lapa=sakums
//			vai		http://lielakeda.lv/battle/xml.php?lapa=sakums&id=bildesID&skatita=skatitasBildesID
if ($lapa == 'sakums'){
	//Ja ir balsojums, pievieno balsi
	if(isset($_GET['id'])&&strlen($_GET['id'])>30&&isset($_GET['skatita'])&&strlen($_GET['skatita'])>30){
		$bildesID = $_GET['id'];
		$skatita = $_GET['skatita'];
		//dabū bildi, par kuru balsots
		$balsiojumi1 = mysqli_query($connection, "SELECT * FROM ratings where img LIKE '%$bildesID%'");
		$r1=mysqli_fetch_array($balsiojumi1);
		//palielina bildes balsis un skatījumus
		$b_balsis=$r1["votes"];
		$b_skatijumi=$r1["views"];
			$b_balsis++;
			$b_skatijumi++;
			$result = mysqli_query($connection, "update ratings set votes = '$b_balsis',  views = '$b_skatijumi' where img LIKE '%$bildesID%'");
		//dabū otru bildi
		$balsiojumi1 = mysqli_query($connection, "SELECT * FROM ratings where img LIKE '%$skatita%'");
		$r1=mysqli_fetch_array($balsiojumi1);
		//palielina bildes skatījumus
		$s_skatijumi=$r1["views"];
			$s_skatijumi++;
			$result = mysqli_query($connection, "update ratings set views = '$s_skatijumi' where img LIKE '%$skatita%'");
	}
	//Dabū divas bildes
	$query = mysqli_query($connection, "select * from ratings ORDER BY RAND() LIMIT 2");
	$rrr=mysqli_fetch_array($query);
		$random_pic=$rrr["img"];
		$balsis=$rrr["votes"];
		$skatijumi=$rrr["views"];
		if($skatijumi<1)$skatijumi=NULL;
		$albumID=$rrr["album"];
	$rrr=mysqli_fetch_array($query);
		$arandom_pic=$rrr["img"];
		$abalsis=$rrr["votes"];
		$askatijumi=$rrr["views"];
		if($askatijumi<1)$askatijumi=NULL;
		$aalbumID=$rrr["album"];
	//izskaitļo bildes ID
	$bb = explode("/", $random_pic);
	$bb2 = explode("/", $arandom_pic);
	if(strcmp($bb[0],"http:")==0||strcmp($bb[0],"https:")==0){$bildesID = "";}else{$bildesID = "http://";}
	if(strcmp($bb2[0],"http:")==0||strcmp($bb2[0],"https:")==0){$bildesID2 = "";}else{$bildesID2 = "http://";}
	for ($i=0; $i<sizeof($bb)-1; $i++){$bildesID.=$bb[$i]."/";}
	for ($i=0; $i<sizeof($bb2)-1; $i++){$bildesID2.=$bb2[$i]."/";}
	//dabū lielās bildes
	$aa = explode("/", $bildesID);
	$aa2 = explode("/", $bildesID2);
	for ($i=0; $i<sizeof($aa)-1; $i++){$aaa1.=$aa[$i]."/";if($i==sizeof($aa)-2){$aaa1.="s640/";};}
	for ($i=0; $i<sizeof($aa2)-1; $i++){$aaa2.=$aa2[$i]."/";if($i==sizeof($aa2)-2){$aaa2.="s640/";};}
	//Izveido XML
	$xml_output .= "\t<entry>\n";
	$xml_output .= "\t\t<id>" . $bildesID . "</id>\n";
	$xml_output .= "\t\t<otra>" . $bildesID2 . "</otra>\n";
	$xml_output .= "\t\t<src>" . $aaa1 . "</src>\n";
	$xml_output .= "\t\t<balsis>" . $balsis . "</balsis>\n";
	$xml_output .= "\t\t<reitings>" . ($balsis > 0 ? round($balsis/$skatijumi,2) : 0) . "</reitings>\n";
	$xml_output .= "\t\t<albums>" . $albumID . "</albums>\n";
	$xml_output .= "\t</entry>\n";
	$xml_output .= "\t<entry>\n";
	$xml_output .= "\t\t<id>" . $bildesID2 . "</id>\n";
	$xml_output .= "\t\t<otra>" . $bildesID . "</otra>\n";
	$xml_output .= "\t\t<src>" . $aaa2 . "</src>\n";
	$xml_output .= "\t\t<balsis>" . $abalsis . "</balsis>\n";
	$xml_output .= "\t\t<reitings>" . ($abalsis > 0 ? round($abalsis/$askatijumi,2) : 0) . "</reitings>\n";
	$xml_output .= "\t\t<albums>" . $aalbumID . "</albums>\n";
	$xml_output .= "\t</entry>\n";

//Bilžu tops
}else if($lapa == 'bildes'){
	$balsiojumi1 = mysqli_query($connection, "SELECT img, album, votes, (votes/views) rating FROM ratings where (votes/views) > 0 and votes>1 ORDER BY (rating*votes) DESC LIMIT 0 , 12");
	while($r1=mysqli_fetch_array($balsiojumi1)){
		$url=$r1["img"];
		$balsis=$r1["votes"];
		$albumID=$r1["album"];
		$reitings=$r1["rating"];
		//Izveido XML
		$xml_output .= "\t<entry>\n";
		$xml_output .= "\t\t<URL>" . $url . "</URL>\n";
		$xml_output .= "\t\t<src>" . $url . "</src>\n";
		$xml_output .= "\t\t<balsis>" . $balsis . "</balsis>\n";
		$xml_output .= "\t\t<albums>" . $albumID . "</albums>\n";
		$xml_output .= "\t\t<reitings>" . round($reitings, 2) . "</reitings>\n";
		$xml_output .= "\t</entry>\n";
	}

//Albumu tops
}else if($lapa == 'albumi'){
	$balsiojumi1 = mysqli_query($connection, "SELECT distinct album, (sum(votes)/sum(views)) skaits, sum(votes) bals, img FROM `ratings` where votes>0 and views>0 group by album ORDER BY skaits desc, bals desc limit 0, 10");
	while($r1=mysqli_fetch_array($balsiojumi1)){
		$url=$r1["img"];
		$balsis=$r1["skaits"];
		$albumID=$r1["album"];
		$votes=$r1["bals"];
		//Izveido XML
		$xml_output .= "\t<entry>\n";
		$xml_output .= "\t\t<URL>" . $url . "</URL>\n";
		$xml_output .= "\t\t<src>" . $url . "</src>\n";
		$xml_output .= "\t\t<reitings>" . round($balsis, 2) . "</reitings>\n";
		$xml_output .= "\t\t<balsis>" . $votes . "</balsis>\n";
		$xml_output .= "\t\t<albums>" . $albumID . "</albums>\n";
		$xml_output .= "\t</entry>\n";
	}

//Statistika
}else if($lapa == 'stat'){

	//Bilžu kopskaits
	$kopskaits = mysqli_query($connection, "SELECT count(*) skaits FROM ratings");
	while($r1=mysqli_fetch_array($kopskaits)){
		$kop=$r1["skaits"];
	}
	//Bildes ar balsojumiem
	// $kopskaits = mysqli_query($connection, "SELECT count(*) skaits FROM ratings where votes>0");
	$kopskaits = mysqli_query($connection, "SELECT sum(votes) skaits FROM ratings where votes>0 ");
	while($r1=mysqli_fetch_array($kopskaits)){
		$balsots=$r1["skaits"];
	}
	//Bildes parādītas
	$kopskaits = mysqli_query($connection, "SELECT sum(views) skaits FROM ratings where views>0");
	while($r1=mysqli_fetch_array($kopskaits)){
		$skatits=$r1["skaits"];
	}
	//Albumu kopskaits
	$kopskaits = mysqli_query($connection, "SELECT count(distinct album) skaits FROM ratings");
	while($r1=mysqli_fetch_array($kopskaits)){
		$albumi=$r1["skaits"];
	}
	//Izveido XML
	$xml_output .= "\t<entry>\n";
	$xml_output .= "\t\t<bildes>" . $kop . "</bildes>\n";
	$xml_output .= "\t\t<albumi>" . $albumi . "</albumi>\n";
	$xml_output .= "\t\t<balsis>" . $balsots . "</balsis>\n";
	$xml_output .= "\t\t<skatits>" . $skatits . "</skatits>\n";
	$xml_output .= "\t</entry>\n";

}
//Izveido XML beigas
$xml_output .= "</entries>";

//Izdrukā XML
echo $xml_output;

?>