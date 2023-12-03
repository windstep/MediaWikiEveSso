# MediaWiki OAuth2 Client
Alpha version of an OAuth2 client that will allow users to create accounts and log in to MediaWiki using 
[EVE Online's SSO](https://eveonline-third-party-documentation.readthedocs.io/en/latest/sso/intro.html) service. The *Login*
and *Create Account* links will be replaced with a *LOG IN with EVE Online* button

This is a fork of the generic [Schine/MW-OAuth2Client](https://github.com/Schine/MW-OAuth2Client) implementation ()currently 
maintained by [Schine GmbH](https://www.star-made.org/)) which uses  the PHP League's [OAuth2 Client](https://github.com/thephpleague/oauth2-client)
under the hood.

Currently Requires MediaWiki 1.25+.

## Installation

Build the composer for the version of PHP you are using in the webserver
(this does not work, if you use the version you want to migrate to!)
```
$ /usr/bin/php7.4-cli -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$ /usr/bin/php7.4-cli ./composer-setup.php
```
Then you can run composer in the PHP directories like:
```
$ /usr/bin/php7.4-cli ~/composer/composer.phar update
```

Clone this repo into a `MW-EVE-SSO` directory in the `extension` directory:
```
$ cd extensions
$ git clone https://github.com/Signal-Cartel/MediaWikiEveSso.git MW-EVE-SSO
```

Next run 'git submodule update --init' in the cloned directory. This will initialize the local configuration file and fetch all data from the OAuth2 client library.

```
$ cd MW-EVE-SSO
$ git submodule update --init
```


Finally, run [composer](https://getcomposer.org/) in `vendors/oauth2-client` to install the library dependencies.
```
$ cd vendors/oauth2-client
$ composer install --no-dev
```

## Usage

Add the following line to your LocalSettings.php file.

```
wfLoadExtension( 'MW-EVE-SSO' );
```

Required settings to be added to `LocalSettings.php`
You can  get a client ID and Secret by registering an SSO Application for your wiki on the [EVE Developers](https://developers.eveonline.com/) site 
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
In order to replace the login link with an official *LOG IN with EVE Online* button you need to adde the following CSS
to the [MediaWiki:Common.css](https://www.mediawiki.org/wiki/Manual:Interface/Stylesheets) page on your wiki

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
