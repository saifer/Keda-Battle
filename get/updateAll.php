<?php
header('Content-Type: text/html; charset=UTF-8');
include('../includes/init_sql.php');
set_time_limit(0);
error_reporting(E_ERROR); 
ini_set('xdebug.var_display_max_depth', '10');
ini_set('xdebug.var_display_max_children', '256');
ini_set('xdebug.var_display_max_data', '1024');
ini_set("display_errors", 1); 
session_start();
require_once 'vendor/autoload.php';

// Sākam!
echo "<hr/><b>BILDES</b><br/><hr/>";

//Detaļas
$blacklist = array('Auto Backup', '2017-09-03', 'Screen Captures', 'Vivus MTB Tukums', 'Collages', 'sniegs', 'Samč18', 'Kolāžas', 'Ekrāna tvērumi', 'nooo', 'nooo2', 'noo1', 'noo0', 'Profile Photos', '2013-03-31', '1/27/13', 'toAdd', 'L&L', 'rhejis', 'kill', 'poolparty', 'subd', 'baba', 'dp', 'httpwww.youtube.comwatchv=DnGdoEa1tPg', '13', '7d', 'j^2', 'qwerty', 'pa', 'Drop Box', 'muzeji', 'kursa darbi', 'exp', 'Pistuve', 'What are you dingo', 'Untitled Album', '20', '11032011', 'hc12', '22', 'idz', '07', '27', 'speķ', 'ss', 'bobball', '18', '17', 'El', '09', 'Wil', 'rage', '02112010', 'q', 'ig', 'li', '25', 'Sig', 'wall_1', 'L', '26082010', 'kin', 'LiLiBalle', 'hood', 'Lien', 'šw', 'rtop', 'sm', '26', 'hāj', 'sv', '15', 't', '7_1', 'sergio martinez hardcore party', 'storm', 'aa', 'happy', '02', 'mo', 'a', '27062010', '27_1', '24_1', 'dl', 'izlaidūmi', 'gewdd', '040120101', '04', 'PBD', 'Din', 'Prikaļi', 'backup', 'fin', 'Bumba', 'Mmm');

$client = new Google_Client();
$client->setAuthConfig('client_credentials.json');
$client->setAccessType ("offline");
$client->setApprovalPrompt ("force");
$client->setIncludeGrantedScopes(true);
$client->addScope("https://www.googleapis.com/auth/photoslibrary.readonly");

$accessFile = 'accessToken.json';
$refreshFile = 'refreshToken.json';

if (file_exists($accessFile)) {
	$accessToken = json_decode(file_get_contents($accessFile), true);
	if(isset($accessToken["access_token"]))
		$accessToken = $accessToken["access_token"];
	// echo $accessToken;
	$client->setAccessToken($accessToken);
}
if ($client->isAccessTokenExpired() && file_exists($refreshFile)) {
	$refreshToken = json_decode(file_get_contents($refreshFile), true);
	$client->fetchAccessTokenWithRefreshToken($refreshToken);
	
	file_put_contents($accessFile, json_encode($client->getAccessToken()));
	file_put_contents($refreshFile, json_encode($client->getRefreshToken()));
}else{
	$authUrl = $client->createAuthUrl();
	echo $authUrl, "\n";
	// $code = rtrim(fgets(STDIN));
	$client->authenticate($code);
	
	file_put_contents($accessFile, json_encode($client->getAccessToken()));
	file_put_contents($refreshFile, json_encode($client->getRefreshToken()));
}
$service = new Google_Service_PhotosLibrary($client);

//Dabū albumu sarakstu
$optParams = array('pageSize' => '50');
$results = $service->albums->listAlbums($optParams);
$albums = array();
foreach ($results as $album) {
  $albums[] = array($album['id'], $album['title']);
}

//iet cauri albumiem
for ($i = 0; $i < sizeof($albums); $i++){
	$albumID 	= $albums[$i][0];
	$FullTitle 	= $albums[$i][1];
	if( in_array( $FullTitle, $blacklist ) ) {
		continue;
	}
	$origTitle = $FullTitle;
	
	$FullTitle = str_replace('ā','a',$FullTitle);
	$FullTitle = str_replace('Ā','A',$FullTitle);
	$FullTitle = str_replace('č','c',$FullTitle);
	$FullTitle = str_replace('Č','C',$FullTitle);
	$FullTitle = str_replace('ē','e',$FullTitle);
	$FullTitle = str_replace('Ē','E',$FullTitle);
	$FullTitle = str_replace('ģ','g',$FullTitle);
	$FullTitle = str_replace('Ģ','G',$FullTitle);
	$FullTitle = str_replace('ī','i',$FullTitle);
	$FullTitle = str_replace('Ī','I',$FullTitle);
	$FullTitle = str_replace('ķ','k',$FullTitle);
	$FullTitle = str_replace('Ķ','K',$FullTitle);
	$FullTitle = str_replace('ļ','l',$FullTitle);
	$FullTitle = str_replace('Ļ','L',$FullTitle);
	$FullTitle = str_replace('ņ','n',$FullTitle);
	$FullTitle = str_replace('Ņ','N',$FullTitle);
	$FullTitle = str_replace('š','s',$FullTitle);
	$FullTitle = str_replace('Š','S',$FullTitle);
	$FullTitle = str_replace('ū','u',$FullTitle);
	$FullTitle = str_replace('Ū','U',$FullTitle);
	$FullTitle = str_replace('ž','z',$FullTitle);
	$FullTitle = str_replace('Ž','Z',$FullTitle);
	$FullTitle = str_replace(' ','_',$FullTitle);
	$FullTitle = mysqli_escape_string($connection, $FullTitle);
	
	$irnav = mysqli_query($connection, "select album from ratings where album LIKE '$FullTitle' OR album LIKE '$albumID' OR albumID LIKE '$albumID'");
	
	if(mysqli_num_rows($irnav) == 0 ) {
		$optParams = array('pageSize' => '100');
		$pictures = showAlbumContent($service, $albumID, $optParams);
		
		
		if(is_array($pictures)){
			echo "Sākam albumu <b>".$albums[$i][1]."</b> (sauksim to par <i>".$FullTitle."</i>)<br/>";
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
				$taken = $pictures[$j]['taken'];
			
				//pievieno jaunu
				$result = mysqli_query($connection, "insert into ratings (img, album, albumID, iso, model, fstop, exposure, focallength, year, month, day, taken) values('$random_pic', '$FullTitle', '$albumID', '$iso', '$model', '$fstop', '$exposure', '$focallength', '$year', '$month', '$weekday', '$taken')");
				echo "Albums <b>".$origTitle."</b> (".$FullTitle.") papildināts ar bildi <img style='height:20px;' src='$random_pic'/><br/>";
			}
		}
	}
}

echo "<hr/>Gatavs!";

function showAlbumContent($service, $albumID){
	$reqq = new Google_Service_PhotosLibrary_SearchMediaItemsRequest();
	$reqq->setAlbumId($albumID);
	$albumContent = $service->mediaItems->search($reqq);

	$tmp = array();
	$skaititajs = 0;
	foreach($albumContent->mediaItems as $item){
    	$tmp[$skaititajs]['src'] = $item->baseUrl."=w2048-h1024";
    	$tmp[$skaititajs]['iso'] = $item->mediaMetadata->photo->isoEquivalent;
    	$tmp[$skaititajs]['model'] = $item->mediaMetadata->photo->cameraModel;
    	$tmp[$skaititajs]['fstop'] = $item->mediaMetadata->photo->apertureFNumber;
    	$tmp[$skaititajs]['exposure'] = $item->mediaMetadata->photo->exposureTime;
    	$tmp[$skaititajs]['focallength'] = $item->mediaMetadata->photo->focalLength;
    	$tmp[$skaititajs]['year'] = date('Y', strtotime($item->mediaMetadata->creationTime));
    	$tmp[$skaititajs]['month'] = date('n', strtotime($item->mediaMetadata->creationTime));
    	$tmp[$skaititajs]['weekday'] = date('N', strtotime($item->mediaMetadata->creationTime));
    	$tmp[$skaititajs]['taken'] = date('Y-m-d G:i:s', strtotime($item->mediaMetadata->creationTime));
		$skaititajs++;
    }
    return $tmp;
}
