<?php
require_once ( "settings.php" );
require_once ( "twitch-api.php" );


if (!isset($_GET['login'])) {
    //http_response_code(400);
    exit();
}

if (!isset($_GET['clientid'])) {
    //http_response_code(400);
    exit();
}

if (!isset($_GET['secret'])) {
    //http_response_code(400);
    exit();

}

if (!isset($_GET['uid'])) {
    //http_response_code(400);
    exit();

}

//####################################################################
//Check input UID
$sUID = file_get_contents ( $sFileKeyUID );
if ($sUID != $_GET['uid'])
{
	exit();
}

//####################################################################
//Input data
$oInputData = [
	"login" 	=> $_GET['login'],
	"clientid" 	=> $_GET['clientid'],
	"secret" 	=> $_GET['secret'],
	"state" 	=> bin2hex(openssl_random_pseudo_bytes(16)),
];

file_put_contents (
	$sFileNameInputData,
	json_encode ( 
		$oInputData 
	),
	LOCK_EX );
	

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


//####################################################################
//Query authorize
$url = $authorizeUri . "?" . http_build_query ( 
	[
		"response_type" 	=> "code",
        "client_id" 		=> $oInputData["clientid"],
        "redirect_uri" 		=> $redirectUri,
        "scope" 			=> implode(" ", $scopes),
        "state" 			=> $oInputData["state"],
    ] );

//####################################################################
//Redirect browser to authorize
header("Location: ".$url); exit();


