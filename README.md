# TwitchOAuth

You need an Apache HTTPD server with "AllowOverride All" on your Directory.

You need a public domain with HTTPS where to put all these project files.


Part 1 - Prepare code
======

Step 1
-----------
Create your own secret UID value.
This secret UID will protect your domain as a personnal token.

Step 2
-----------
Replace "012345678abcdef012345678abcdef" from "/logs/key_uid.txt" with you secret UID use a personnal manager to create a new one as "KEY_UID".

Step 3
-----------
Edit "settings.php" and replace the content of variable "$redirectUri" with your URI to your "oauth.php" like this "https://twitch.mypersonnal.com/oauth.php".


Part 2 - Prepare App
======

Step 1
-----------
Open in your browser the URL "https://dev.twitch.tv/" using your streamer account.

Step 2
-----------
Access to your Console

Step 3
-----------
Access to Applications

Step 4
-----------
Create a new Application with parameters like these :
> Name : "MySuperApplicationName"
>
> URL : "https://twitch.mypersonnal.com/oauth.php" <= Your URI from Part1-Step3
> 
> Category : "Analytics tool" or other
> 
> Type of client : "Confidential"
> 
Click on "Create"

Step 5
-----------
Open your newly created application.

Copy your "Client identification" into a password manager as "CLIENT_ID".

Click on "New secret".

Copy your "Secret" into a password manager as "SECRET".


Part 3 - Do OAuth
======

Step 1
-----------
Authorize your OAuth with your browser.
> `https://twitch.mypersonnal.com/oauth-query.php?uid=[KEY_UID]&login=[YOUR_TWITCH_LOGIN]&clientid=[CLIENT_ID]&secret=[SECRET]`
>
> `KEY_UID` the UID you've put into `/logs/key_uid.txt`.
>
> `YOUR_TWITCH_LOGIN` your Twitch login used to connect to your twitch account as a streamer/broadcaster.
>
> `CLIENT_ID` the client identification from your application created on dev.twitch.tv
>
> `SECRET` the secret from your application created on dev.twitch.tv
>

Step 2
-----------
You could see the "/logs/auth_data.json", it will contain all needed informations
~~~
{
  "login":"#####streamer_login#####",
  "clientid":"[CLIENT_ID]",
  "secret":"[SECRET]",
  "state":"#####a state value########",
  "code":"#####a code value#####",
  "token":"#####your token#####",
  "token_refresh":"#####your token for refresh#####",
  "token_expire":#####token_expire_time#####,
  "token_scope":["bits:read","channel:read:subscriptions","moderator:read:followers"],
  "broadcasterid":"#####broadcaster_id#####"
}
~~~

Part 4 - Play !
======
You could use this code to get all authentication data (and auto-update if needed) and call helix...

Sample
~~~
<?php
require_once ( "settings.php" );
require_once ( "twitch-api.php" );

//####################################################################
//Check input UID
if (!isset($_GET['uid'])) {
    exit();
}
$sUID = file_get_contents ( $sFileKeyUID );
if ($sUID != $_GET['uid'])
{
	exit();
}

$oAuthData = TwitchAutoOAuth ( );
if ($oAuthData===null)
{
	exit();
}

$sUri = "https://api.twitch.tv/helix/channels/followers?broadcaster_id=".urlencode($oAuthData["broadcasterid"])."&first=100";
	
$data = TwitchApiCurlHeader (
		[
			"Client-Id: " . $oAuthData["clientid"],
			"Authorization: Bearer " . $oAuthData["token"],
		],
		$sUri );
~~~
