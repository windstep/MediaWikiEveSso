# MediaWiki OAuth2 Client

MediaWiki implementation of the PHP League's [OAuth2 Client](https://github.com/thephpleague/oauth2-client), to allow MediaWiki to act as a client to any OAuth2 server. Currently maintained by [Schine GmbH](https://www.star-made.org/).

## Installation

Clone this repo into the extension directory. In the cloned directory, run 'git submodule init' to initialize the local configuration file, followed by 'git submodule update' to fetch all data from the OAuth2 client library.

Finally, run [composer](https://getcomposer.org/) in /vendors/oauth2-client to install the library dependency.

```
composer install
```

## Usage

Add the following line to your LocalSettings.php file.

```
require_once "$IP/extensions/MW-OAuth2Client/OAuth2Client.php";
```

Required settings to be added to LocalSettings.php

```
$wgOAuth2Client['client']['id']     = ''; // The client ID assigned to you by the provider
$wgOAuth2Client['client']['secret'] = ''; // The client secret assigned to you by the provider

$wgOAuth2Client['configuration']['authorize_endpoint']     = ''; // Authorization URL
$wgOAuth2Client['configuration']['access_token_endpoint']  = ''; // Token URL
$wgOAuth2Client['configuration']['api_endpoint']           = ''; // URL to fetch user JSON
$wgOAuth2Client['configuration']['redirect_uri']           = ''; // URL for OAuth2 server to redirect to

$wgOAuth2Client['configuration']['username'] = 'username'; // JSON path to username
$wgOAuth2Client['configuration']['email'] = 'email'; // JSON path to email
```

The **Redirect URI** for your wiki should be:

```
http://your.wiki.domain/path/to/wiki/Special:OAuth2Client/callback
```

Optional further configuration

```
$wgOAuth2Client['configuration']['http_bearer_token'] = 'Bearer'; // Token to use in HTTP Authentication
$wgOAuth2Client['configuration']['query_parameter_token'] = 'auth_token'; // query parameter to use
$wgOAuth2Client['configuration']['scopes'] = 'read_citizen_info'; //Permissions

$wgOAuth2Client['configuration']['service_name'] = 'Citizen Registry'; // the name of your service
$wgOAuth2Client['configuration']['service_login_link_text'] = 'Login with StarMade'; // the text of the login link

```

## License
LGPL (GNU Lesser General Public License) http://www.gnu.org/licenses/lgpl.html
