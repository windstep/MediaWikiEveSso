<?php
/**
 * SpecialOAuth2Client.php
 * Based on TwitterLogin by David Raison, which is based on the guideline published by Dave Challis at http://blogs.ecs.soton.ac.uk/webteam/2010/04/13/254/
 * @license: LGPL (GNU Lesser General Public License) http://www.gnu.org/licenses/lgpl.html
 *
 * @file SpecialOAuth2Client.php
 * @ingroup OAuth2Client
 *
 * @author Joost de Keijzer
 *
 * Uses the OAuth2 library https://github.com/vznet/oauth_2.0_client_php
 *
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This is a MediaWiki extension, and must be run from within MediaWiki.' );
}

class SpecialOAuth2Client extends SpecialPage {

	private $_provider;

	/**
	 * Required settings in global $wgOAuth2Client
	 *
	 * $wgOAuth2Client['client']['id']
	 * $wgOAuth2Client['client']['secret']
	 * //$wgOAuth2Client['client']['callback_url'] // extension should know
	 *
	 * $wgOAuth2Client['configuration']['authorize_endpoint']
	 * $wgOAuth2Client['configuration']['access_token_endpoint']
	 * $wgOAuth2Client['configuration']['http_bearer_token']
	 * $wgOAuth2Client['configuration']['query_parameter_token']
	 * $wgOAuth2Client['configuration']['api_endpoint']
	 */
	public function __construct() {

		parent::__construct('OAuth2Client'); // ???: wat doet dit?
		global $wgOAuth2Client, $wgScriptPath;
		global $wgServer, $wgArticlePath;

		$this->_provider = new \League\OAuth2\Client\Provider\GenericProvider([
			'clientId'                => $wgOAuth2Client['client']['id'],    // The client ID assigned to you by the provider
			'clientSecret'            => $wgOAuth2Client['client']['secret'],   // The client password assigned to you by the provider
			'redirectUri'             => $wgOAuth2Client['configuration']['redirect_uri'],
			'urlAuthorize'            => $wgOAuth2Client['configuration']['authorize_endpoint'],
			'urlAccessToken'          => $wgOAuth2Client['configuration']['access_token_endpoint'],
			'urlResourceOwnerDetails' => $wgOAuth2Client['configuration']['api_endpoint'],
			'scopes'                  => $wgOAuth2Client['configuration']['scopes']
		]);
	}

	// default method being called by a specialpage
	public function execute( $parameter ){
		$this->setHeaders();
		switch($parameter){
			case 'redirect':
				$this->_redirect();
			break;
			case 'callback':
				$this->_handleCallback();
			break;
			case 'logout':
				$this->_logout();
			break;
			default:
				$this->_default();
			break;
		}

	}

	private function _redirect() {

		global $wgRequest;
		$_SESSION['returnto'] = $wgRequest->getVal( 'returnto' );

		// Fetch the authorization URL from the provider; this returns the
		// urlAuthorize option and generates and applies any necessary parameters
		// (e.g. state).
		$authorizationUrl = $this->_provider->getAuthorizationUrl();

		// Get the state generated for you and store it to the session.
		$_SESSION['oauth2state'] = $this->_provider->getState();

		// Redirect the user to the authorization URL.
		header('Location: ' . $authorizationUrl);
		exit;
	}

	private function _handleCallback(){
		try {

			// Try to get an access token using the authorization code grant.
			$accessToken = $this->_provider->getAccessToken('authorization_code', [
				'code' => $_GET['code']
			]);
		} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

			// Failed to get the access token or user details.
			exit($e->getMessage());

		}

		$resourceOwner = $this->_provider->getResourceOwner($accessToken);
		$user = $this->_userHandling( $resourceOwner->toArray() );
		$user->setCookies();

		global $wgOut;
		if( $user->getRegistration() > wfTimestamp( TS_MW ) - 1 ) {
			// new user!
			$wgOut->redirect(SpecialPage::getTitleFor('Preferences')->getLinkUrl());
		} else {
			$title = null;
			if( isset( $_SESSION['returnto'] ) ) {
				$title = Title::newFromText( $_SESSION['returnto'] );
				unset( $_SESSION['returnto'] );
			}

			if( !$title instanceof Title || 0 > $title->mArticleID ) {
				$title = Title::newMainPage();
			}
			$wgOut->redirect( $title->getFullURL() );
		}
		return true;
	}

	private function _logout() {
		global $wgOAuth2Client, $wgOut, $wgUser;
		if( $wgUser->isLoggedIn() ) $wgUser->logout();

		$service_name = ( isset( $wgOAuth2Client['configuration']['service_name'] ) && 0 < strlen( $wgOAuth2Client['configuration']['service_name'] ) ? $wgOAuth2Client['configuration']['service_name'] : 'OAuth2' );

		$wgOut->setPagetitle( wfMessage( 'oauth2client-logout-header', $service_name)->text() );
		$wgOut->addWikiMsg( 'oauth2client-logged-out' );
		$wgOut->addWikiMsg( 'oauth2client-login-with-oauth2-again', $this->getTitle( 'redirect' )->getPrefixedURL(), $service_name );
	}

	private function _default(){
		global $wgOAuth2Client, $wgOut, $wgUser, $wgScriptPath, $wgExtensionAssetsPath;
		$service_name = ( isset( $wgOAuth2Client['configuration']['service_name'] ) && 0 < strlen( $wgOAuth2Client['configuration']['service_name'] ) ? $wgOAuth2Client['configuration']['service_name'] : 'OAuth2' );

		$wgOut->setPagetitle( wfMessage( 'oauth2client-login-header', $service_name)->text() );
		if ( !$wgUser->isLoggedIn() ) {
			$wgOut->addWikiMsg( 'oauth2client-you-can-login-to-this-wiki-with-oauth2', $service_name );
			$wgOut->addWikiMsg( 'oauth2client-login-with-oauth2', $this->getTitle( 'redirect' )->getPrefixedURL(), $service_name );

		} else {
			$wgOut->addWikiMsg( 'oauth2client-youre-already-loggedin' );
		}
		return true;
	}

	protected function _userHandling( $response ) {
		global $wgOAuth2Client, $wgAuth;

		$username = $response['user'][$wgOAuth2Client['configuration']['username']];
		$email = $response['user'][$wgOAuth2Client['configuration']['email']];

		$user = User::newFromName($username, 'creatable');
		if (!$user) {
			throw new MWException('Could not create user with username:' . $username);
			die();
		}
		$user->setRealName($username);
		$user->setEmail($email);
		$user->load();
		if ( !( $user instanceof User && $user->getId() ) ) {
			$user->addToDatabase();
		}
		$user->setToken();
		// Setup the session
		global $wgSessionStarted;
		if (!$wgSessionStarted) {
			wfSetupSession();
		}
		$user->setCookies();
		$this->getContext()->setUser( $user );
		$user->saveSettings();
		global $wgUser;
		$wgUser = $user;
		$sessionUser = User::newFromSession($this->getRequest());
		$sessionUser->load();
		return $user;
	}

}
