<?php
include('includes/init_sql.php');
require_once 'get/vendor/autoload.php';

$client = new Google_Client();
$client->setAuthConfig('get/client_credentials.json');
$client->setAccessType ("offline");
$client->setApprovalPrompt ("force");
$client->setIncludeGrantedScopes(true);
$client->addScope("https://www.googleapis.com/auth/photoslibrary.readonly");

$accessFile = 'get/accessToken.json';
$refreshFile = 'get/refreshToken.json';

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
$atslegvards = $_GET['v'];

$last_msg_id=$_GET['last_msg_id'];
$action=$_GET['action'];

if($action <> "get")
{
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lv" lang="lv">
<head>
    <title>Kedas cīņa - <?php echo $_GET['v']; ?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta name="description" content="Salīdzini Kedas bildes!"/>
    <meta name="keywords" content="Keda, foto, attēli, salīdzinājums"/>
    <meta name="author" content="Matīss Rikters"/>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="includes/style.css">
        <link rel="stylesheet" type="text/css" href="includes/highslide/highslide.css">
        <script type="text/javascript" src="includes/highslide/highslide.js"></script>
        <script type="text/javascript">
        hs.graphicsDir = 'includes/highslide/graphics/';
        </script>
    <script type="text/javascript" src="includes/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
            
        function last_msg_funtion() 
        { 
           
           var ID=$(".highslide:last").attr("id");
            $('div#last_msg_loader').html('<img src="bigLoader.gif">');
            $.post("vards.php?v=<?php echo $_GET['v']; ?>&action=get&last_msg_id="+ID,
            
            function(data){
                if (data != "") {
                $(".highslide:last").after(data);			
                }
                $('div#last_msg_loader').empty();
            });
        };  
        
        $(window).scroll(function(){
            if  ($(window).scrollTop() == $(document).height() - $(window).height()){
               last_msg_funtion();
            }
        }); 

    });

    </script>
</head>
<body style='margin: 0px; height: 100%;'>
    <div style='position: fixed; top: 0; left: 0; width: 50%; height: 813%; background-color: black; z-index: 1;'></div>
    <div style='position: absolute; top: 0; left: 0; z-index: 2; width: 100%;'>
    <h2 style="margin:auto auto;text-align:center;padding:5px;margin-top:20px;background-color:lightgrey;border-radius:15px;width:250px;opacity:0.7">Atslēgvārdi</h2>
    <div id="contentdiv" style='margin:auto auto;text-align:center;margin-top:15px;padding:5px;background-color:lightgrey;border-radius:15px;opacity:0.99'>
    <br/>
    <?php

    include('load_first.php');

    ?>
    <div id="last_msg_loader"></div>
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
}
else
{
 
    include('load_second.php');		
	
}
?>	