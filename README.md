# MediaWiki OAuth2 Client
MediaWiki implementation of the PHP League's [OAuth2 Client](https://github.com/thephpleague/oauth2-client), to allow MediaWiki to act as a client to any OAuth2 server. Currently maintained by [Schine GmbH](https://www.star-made.org/).

Requires MediaWiki 1.25+.

## Installation

Clone this repo into the extension directory. In the cloned directory, run 'git submodule update --init' to initialize the local configuration file and fetch all data from the OAuth2 client library.

Finally, run [composer](https://getcomposer.org/) in /vendors/oauth2-client to install the library dependency.

```
composer install
```

## Usage

Add the following line to your LocalSettings.php file.

```
wfLoadExtension( 'MW-EVE-SSO' );
```

Required settings to be added to LocalSettings.php

```
$wgOAuth2Client['client']['id']     = ''; // The client ID assigned to you by the provider
$wgOAuth2Client['client']['secret'] = ''; // The client secret assigned to you by the provider
$wgOAuth2Client['configuration']['redirect_uri']           = ''; // URL for OAuth2 server to redirect to
```

The **Redirect URI** for your wiki should be:

```
http://your.wiki.domain/path/to/wiki/Special:OAuth2Client/callback
```

Configure which EVE characters are allowed to log in 

```
$wgOAuth2Client['configuration']['allowed_character_ids'] = []; // Specify specific characters here
$wgOAuth2Client['configuration']['allowed_corporation_ids'] = []; // All members of these corporations will be abe to log in
```

### Popup Window
To use a popup window to login to the external OAuth2 server, copy the JS from modal.js to the [MediaWiki:Common.js](https://www.mediawiki.org/wiki/Manual:Interface/JavaScript) page on your wiki.

### Login Button Styling
MediaWiki:Common.css

```CSS
/* Style oAuth Login link with 'LOG IN with EVE Online' image */
a.btn_mwevesso_login{
    background-image: url(https://web.ccpgamescdn.com/eveonlineassets/developers/eve-sso-login-black-small.png);
    display: block;
    margin: -10px auto;
    text-indent: -9999px;
    width: 195px;
    height: 30px;
}
```


## License
LGPL (GNU Lesser General Public License) http://www.gnu.org/licenses/lgpl.html
