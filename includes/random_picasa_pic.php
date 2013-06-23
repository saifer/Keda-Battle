<?php
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
//get a random album 
$album_title = $albums[array_rand($albums, 1)];
//get the list of pictures from the album
$pictures = showAlbumContent($userid, $album_title);
//get a random picture from the album
if(is_array($pictures)){
	$numurs = array_rand($pictures,1);
	$random_pic = $pictures[$numurs]['src'];
	$model = $pictures[$numurs]['model'];
	$iso = $pictures[$numurs]['iso'];
	$fstop = $pictures[$numurs]['fstop'];
	$exposure = $pictures[$numurs]['exposure'];
	$focallength = $pictures[$numurs]['focallength'];
	$year = $pictures[$numurs]['year'];
	$month = $pictures[$numurs]['month'];
	$weekday = $pictures[$numurs]['weekday'];
	$keywords = $pictures[$numurs]['keywords'];
}
else{
	$random_pic = 'https://lh5.googleusercontent.com/_aZ7-eybl7nM/TRHvlEFTHCI/AAAAAAAC0tg/tWtI1JEg9Ig/s1600/n%235%235%4084.245.216.215%235%4078.84.2.172%235%4085.254.33.82%235%4081.198.173.102%235%4087.110.93.47.jpg';
}

//get a random album 
$aalbum_title = $albums[array_rand($albums, 1)];
//get the list of pictures from the album
$apictures = showAlbumContent($userid, $aalbum_title);
//get a random picture from the album
if(is_array($apictures)){
	$anumurs = array_rand($apictures,1);
	$arandom_pic = $apictures[$anumurs]['src'];
	$amodel = $apictures[$anumurs]['model'];
	$aiso = $apictures[$anumurs]['iso'];
	$afstop = $apictures[$anumurs]['fstop'];
	$aexposure = $apictures[$anumurs]['exposure'];
	$afocallength = $apictures[$anumurs]['focallength'];
	$ayear = $apictures[$anumurs]['year'];
	$amonth = $apictures[$anumurs]['month'];
	$aweekday = $apictures[$anumurs]['weekday'];
	$akeywords = $apictures[$anumurs]['keywords'];
}
else{
	$arandom_pic = 'https://lh5.googleusercontent.com/_aZ7-eybl7nM/TRHvlEFTHCI/AAAAAAAC0tg/tWtI1JEg9Ig/s1600/n%235%235%4084.245.216.215%235%4078.84.2.172%235%4085.254.33.82%235%4081.198.173.102%235%4087.110.93.47.jpg';
}
?>

