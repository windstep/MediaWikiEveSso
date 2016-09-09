Schine developed Version of https://github.com/joostdekeijzer/mw-oauth2-client-extension
============================

mw-oauth2-client-extension
--------------------------

MediaWiki OAuth2 Client Extension

MediaWiki implementation of the [OAuth2 Client library](https://github.com/thephpleague/oauth2-client).
Originally based on https://github.com/joostdekeijzer/mw-oauth2-client-extension

Required settings in global $wgOAuth2Client

    $wgOAuth2Client['client']['id']     = ''; // Your App Id or Client Id received by OAuth2 Server Administrator
    $wgOAuth2Client['client']['secret'] = ''; // Secret received by OAuth2 Server Administrator
    
    $wgOAuth2Client['configuration']['authorize_endpoint']     = '';            // full url's
    $wgOAuth2Client['configuration']['access_token_endpoint']  = '';
    $wgOAuth2Client['configuration']['api_endpoint']           = '';
	$wgOAuth2Client['configuration']['redirect_uri']           = '';
	$wgOAuth2Client['configuration']['http_bearer_token'] = 'Bearer'; // Token to use in HTTP Authentication
	$wgOAuth2Client['configuration']['query_parameter_token'] = 'auth_token'; // query parameter to use

	$wgOAuth2Client['configuration']['username'] = 'username'; // query parameter to use
	$wgOAuth2Client['configuration']['email'] = 'email'; // query parameter to use

The callback url back to your wiki would be:

    http://your.wiki.domain/path/to/wiki/Special:OAuth2Client/callback

License
-------
LGPL (GNU Lesser General Public License) http://www.gnu.org/licenses/lgpl.html

Installation Process
---------------------
Clone this directory to your extensions folder.

cd into the extension's directory and run three commands: 'git submodule init' followed by 'git submodule update' and finally 'php composer.phar install' to get the OAuth2 vendor libraries to work with this extension. If you don't have composer see https://getcomposer.org/doc/00-intro.md

Add the following line to your LocalSettings.php file.
require_once "$IP/extensions/mw-oauth2-client-extension/OAuth2Client.php";

Add the configuration as mentioned above.
