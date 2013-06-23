<?php
header('Content-Type: text/html; charset=UTF-8');
include('../includes/init_sql.php');
set_time_limit(0);
error_reporting(E_ERROR); 
echo "<hr/><b>BILDES</b><br/><hr/>";
function curlit($url)
{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$return=curl_exec ($ch);
		curl_close ($ch);
		return $return;
}

function showAlbumContent($userId, $albumName)
{
    $url = 'http://picasaweb.google.com/data/feed/api/user/' .
            urlencode($userId) . '/album/' . urlencode($albumName);
    $xml = curlit($url);
    $xml = str_replace("xmlns='http://www.w3.org/2005/Atom'", '', $xml);

    $dom = new domdocument;
    $dom->loadXml($xml);
    
    $xpath = new domxpath($dom);
    $nodes = $xpath->query('//entry');
	$skaititajs=0;
    foreach ($nodes as $node) {
    	$tmp[$skaititajs]['src'] = $xpath->query('.//media:content/@url', $node)->item(0)->textContent;
    	$tmp[$skaititajs]['iso'] = $xpath->query('.//exif:iso', $node)->item(0)->textContent;
    	$tmp[$skaititajs]['model'] = $xpath->query('.//exif:model', $node)->item(0)->textContent;
    	$tmp[$skaititajs]['fstop'] = $xpath->query('.//exif:fstop', $node)->item(0)->textContent;
    	$tmp[$skaititajs]['exposure'] = $xpath->query('.//exif:exposure', $node)->item(0)->textContent;
    	$tmp[$skaititajs]['focallength'] = $xpath->query('.//exif:focallength', $node)->item(0)->textContent;
    	$tmp[$skaititajs]['year'] = date('Y',substr($xpath->query('.//exif:time', $node)->item(0)->textContent, 0, -3));
    	$tmp[$skaititajs]['month'] = date('n',substr($xpath->query('.//exif:time', $node)->item(0)->textContent, 0, -3));
    	$tmp[$skaititajs]['weekday'] = date('N',substr($xpath->query('.//exif:time', $node)->item(0)->textContent, 0, -3));
    	if($xpath->query('.//gml:pos', $node))$tmp[$skaititajs]['position'] = $xpath->query('.//gml:pos', $node)->item(0)->textContent;
    	$tmp[$skaititajs]['keywords'] = $xpath->query('.//media:keywords', $node)->item(0)->textContent;
		$skaititajs++;
    }
    return $tmp;
}

function getAlbums($userId)
{
    $url = 'http://picasaweb.google.com/data/feed/api/user/' .
		urlencode($userId) . '?kind=album';
	$xml = curlit($url);
    $xml = str_replace("xmlns='http://www.w3.org/2005/Atom'", '', $xml);

    $dom = new domdocument;
    $dom->loadXml($xml);

    $xpath = new domxpath($dom);
    $nodes = $xpath->query('//entry');
    foreach ($nodes as $node) {
		$tmp[] = $xpath->query('gphoto:name', $node)->item(0)->textContent;
    }
    return $tmp;
}

//Picasa lietotājvārds
$userid = 'matiss.tk';
//Dabū albumu sarakstu
$albums = getAlbums($userid);
//iet cauri albumiem
for ($i=0;$i<sizeof($albums);$i++){
	$irnav=mysql_query("select album from ratings where album LIKE '$albums[$i]'");
	if (mysql_num_rows($irnav) == 0 ) {
		$pictures = showAlbumContent($userid, $albums[$i]);
		if(is_array($pictures)){
			echo "Sākam albumu <b>".$albums[$i]."</b><br/>";
			//iet cauri bildēm
			for ($j=0;$j<count($pictures);$j++){
				//paņem bildes informāciju
				$random_pic = $pictures[$j]['src'];
				$model = $pictures[$j]['model'];
				$iso = $pictures[$j]['iso'];
				$fstop = $pictures[$j]['fstop'];
				$exposure = $pictures[$j]['exposure'];
				$focallength = $pictures[$j]['focallength'];
				$year = $pictures[$j]['year'];
				$month = $pictures[$j]['month'];
				$weekday = $pictures[$j]['weekday'];
			
				//pievieno jaunu
				$result = MYSQL_QUERY("insert into ratings (img, album, iso, model, fstop, exposure, focallength, year, month, day) values('$random_pic', '$albums[$i]', '$iso', '$model', '$fstop', '$exposure', '$focallength', '$year', '$month', '$weekday')");
				echo $albums[$i]." papildināts ar bildi <img style='height:20px;' src='$random_pic'/><br/>";
				//pievieno pozīciju, ja ir
				$position = $pictures[$j]['position'];
				$coord = explode(" ", $position);
				if($position!=''){
						echo $adresz."<br/>";
						echo $random_pic."<br/>";
						//ja nav, pievieno
						$result = MYSQL_QUERY("insert into geo (img, album, lat, lng) values('$random_pic', '$albums[$i]', '$coord[0]', '$coord[1]')");
						echo $albums[$i]." papildināts ar vietu ".$coord[0].", ".$coord[0]."<br/>";
				}
				//pievieno atslēgvārdus
				$keywords = $pictures[$j]['keywords'];
				$atslegvardi = explode(",", $keywords);
				foreach($atslegvardi as $atslegvards){
				if($atslegvards!=''){
						$result = MYSQL_QUERY("insert into tags (img, tag) values('$random_pic', '$atslegvards')");
						echo $albums[$i]." papildināts ar tagu ".$atslegvards."<br/>";
					}
				}
			}
		}
	}
}

echo "<hr/>Gatavs!";
?>