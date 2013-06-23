<?php
error_reporting(0);
include('../includes/init_sql.php');
set_time_limit(0);
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
    	if($xpath->query('.//gml:pos', $node))$tmp[$skaititajs]['position'] = $xpath->query('.//gml:pos', $node)->item(0)->textContent;
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
			$position = $pictures[$j]['position'];
			$coord = explode(" ", $position);
			//nočeko db
			if($position!=''){
				$rezz = mysql_query("SELECT * FROM geo where img = '$random_pic' and lat = '$coord[0]'");
				if(mysql_num_rows($rezz)==0){
					//ja nav, pievieno
					$result = MYSQL_QUERY("insert into geo (img, album, lat, lng) values('$random_pic', '$albums[$i]', '$coord[0]', '$coord[1]')");
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

