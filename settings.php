<?php

$sFileKeyUID 				    = 	__DIR__ . '/logs/key_uid.txt';
$sFileNameInputData 			= 	__DIR__ . '/logs/input_data.json';
$sFileNameAuthData 				= 	__DIR__ . '/logs/auth_data.json';

$redirectUri 			      	= 	"https://twitch.mydomain.com/oauth.php"; //Your directory need to be accessible from a public access
$authorizeUri			      	=   "https://id.twitch.tv/oauth2/authorize";
$tokenUri				        =	"https://id.twitch.tv/oauth2/token";

$iRefreshMargin			    	=	  300; //5 minutes

$scopes 				        = 	[
								              "moderator:read:followers",
								              "channel:read:subscriptions",
								              "bits:read",
							              ];
