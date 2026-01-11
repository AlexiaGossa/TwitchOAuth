<?php
require_once ( "settings.php" );
require_once ( "twitch-api.php" );

/*

We've got a oauth query. Check if state exists and do processing...

*/



if (!isset($_GET['code']))
{
    http_response_code(400);
	echo "Twitch code error";
    exit();
}

if (!isset($_GET['state']))
{
    http_response_code(400);
	echo "Twitch state error";
    exit();
}

//####################################################################
//Read the input data
$sRaw = file_get_contents (
	$sFileNameInputData );
if ($sRaw===false)
{
	exit();
}
$oInputData = json_decode (
	$sRaw,
	true );
if ($oInputData===null)
{
	exit();
}

if ($oInputData["state"] != $_GET['state'])
{
	echo "Twitch invalid state";
	exit();
}

//####################################################################
//Write the twitch auth data
$oInputData["code"] = $_GET['code'];

file_put_contents (
	$sFileNameAuthData,
	json_encode ( 
		$oInputData 
	),
	LOCK_EX );
	
//####################################################################
//Now we try to get token
$data = TwitchApiCurl ( 
	[
		'client_id' 		=> $oInputData["clientid"],
		'client_secret' 	=> $oInputData["secret"],
		'code' 				=> $oInputData["code"],
		'grant_type' 		=> 'authorization_code',
		'redirect_uri' 		=> $redirectUri,
	],
	$tokenUri );
	
if ($data==null)
{
	echo "Twitch token error";
	exit();
}

$accessToken  = $data['access_token'] ?? null;
$refreshToken = $data['refresh_token'] ?? null;
$expiresIn    = $data['expires_in'] ?? null;
$scope        = $data['scope'] ?? [];

if (!$accessToken) {
    http_response_code(500);
    echo "Réponse inattendue (pas de access_token)\n";
    echo $response;
    exit;
}

$oInputData["token"] 			= $accessToken;
$oInputData["token_refresh"] 	= $refreshToken;
$oInputData["token_expire"] 	= time() + (int)$expiresIn;
$oInputData["token_scope"] 		= $scope;

file_put_contents (
	$sFileNameAuthData,
	json_encode ( 
		$oInputData 
	),
	LOCK_EX );

	
echo "Token for ".$oInputData["login"]." stored.\n<br/>";


//####################################################################
//Now we try to get broadcaster-id

$data = TwitchApiCurlHeader ( 
	[
			"Client-Id: " . $oInputData["clientid"],
			"Authorization: Bearer " . $oInputData["token"],
	],
	"https://api.twitch.tv/helix/users?login=".urlencode($oInputData["login"] ) );

if ($data==null)
{
	echo "Twitch users get error";
	exit();
}


$broadcasterId = $data['data'][0]['id'] ?? null;

$oInputData["broadcasterid"] = $broadcasterId;

file_put_contents (
	$sFileNameAuthData,
	json_encode ( 
		$oInputData 
	),
	LOCK_EX );


echo "broadcasterId for ".$oInputData["login"]." stored.\n<br/>";


//####################################################################
//Clean input file
file_put_contents (
	$sFileNameInputData,
	"",
	LOCK_EX );

exit();
