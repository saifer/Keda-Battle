<?php
include('init_sql.php');
set_time_limit(0);
error_reporting(0); 
//Dabū gadījuma attēlu no mana picasa konta
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
    	$tmp[$skaititajs]['keywords'] = $xpath->query('.//media:keywords', $node)->item(0)->textContent;
    	$tmp[$skaititajs]['position'] = $xpath->query('.//gml:pos', $node)->item(0)->textContent;
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

//set the google user id.  my pics are nice, but this should be yours.
$userid = 'matiss.tk';
//get the list of albums
$albums = getAlbums($userid);
//*
for ($i=0;$i<600;$i++){
//iet cauri albumiem
	$pictures = showAlbumContent($userid, $albums[$i]);
	if(is_array($pictures)){
		//iet cauri bildēm
		for ($j=0;$j<count($pictures);$j++){
			//paņem bildes informāciju
			$random_pic = $pictures[$j]['src'];
			
			/* šitais šobrīd nav vajadzīgs
			
			$model = $pictures[$j]['model'];
			$iso = $pictures[$j]['iso'];
			$fstop = $pictures[$j]['fstop'];
			$exposure = $pictures[$j]['exposure'];
			$focallength = $pictures[$j]['focallength'];
			$year = $pictures[$j]['year'];
			$month = $pictures[$j]['month'];
			$weekday = $pictures[$j]['weekday'];
			
			*/
			
			$keywords = $pictures[$j]['keywords'];
			$position = $pictures[$j]['position'];
			$atslegvardi = explode(",", $keywords);
			$coord = explode(" ", $position);
			//nočeko db
			$balsiojumi = mysql_query("SELECT * FROM ratings where img = '$random_pic'");
			if(mysql_num_rows($balsiojumi)>0){
				//atjaunina
				//$result = MYSQL_QUERY("update ratings set iso = '$iso', model = '$model', fstop = '$fstop', exposure = '$exposure', focallength = '$focallength', ytags (img, tag) values('$random_pic', '$atslegvards')");
					}
				}
				if($position!=''){
					$result = MYSQL_QUERY("insert into geo (img, lat, lng) values('$random_pic', '$coord[0]', '$coord[1]')");
				}
			}
		}
	}
}
//*/
?>
<pre>
<?php
print_r($albums);
?>
</pre>

ear = '$year', month = '$month', day = '$weekday', album = '$albums[$i]' where img = '$random_pic'");		
				//nočeko via ir tāds vārds konkrētai bildei
				foreach($atslegvardi as $atslegvards){
					if($atslegvards!=''){
						$rezz = mysql_query("SELECT * FROM tags where img = '$random_pic' and tag = '$atslegvards'");
						if(mysql_num_rows($rezz)==0){
							//ja nav, pievieno
							$result = MYSQL_QUERY("insert into tags (img, tag) values('$random_pic', '$atslegvards')");
						}
					}
				}
				//atrašanās vieta
				if($position!=''){
					$rezz = mysql_query("SELECT * FROM geo where img = '$random_pic' and lat = '$coord[0]'");
					if(mysql_num_rows($rezz)==0){
						//ja nav, pievieno
						$result = MYSQL_QUERY("insert into geo (img, lat, lng) values('$random_pic', '$coord[0]', '$coord[1]')");
					}
				}
			}else{
				//pievieno jaunu
				$result = MYSQL_QUERY("insert into ratings (img, rating, album, iso, model, fstop, exposure, focallength, year, month, day) values('$random_pic', 0, '$albums[$i]', '$iso', '$model', '$fstop', '$exposure', '$focallength', '$year', '$month', '$weekday')");
				foreach($atslegvardi as $atslegvards){
					if($atslegvards!=''){
						$result = MYSQL_QUERY("insert into 