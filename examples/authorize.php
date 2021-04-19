<?php

define('ML_MEDIALAB_URI', 'https://example.medialab.co');
define('ML_API_CLIENT', '<INSERT_CLIENT_ID_HERE>');
define('ML_API_SECRET', '<INSERT_CLIENT_SECRET_HERE>');
define('ML_REDIRECT_URI', 'https://path/to/project/authorize.php');

/**
 * The OAuth2 workflow requires forwarding the user to the MediaLab authorize page to grant permission to your app.
 * The access token is then stored in a local session, together with a refresh token, that we can use to request
 * a new access token without having to forward the user again.
 * If neither access token nor refresh token are present, we will need to forward the user again.
 *
 * The code below is an example on how you could set up the authorize workflow.
 * In order for this to work, you must have a registered API client, with the redirect URI matching the "ML_DIRECT_URI" constant.
 */

require_once __DIR__ . '/../vendor/autoload.php';
session_start();


if(isset($_SESSION['medialab_api_redirect']) && (isset($_GET['error']) || isset($_GET['code']))) {
	// user is coming back from an authorize request, redirect to original file
	header('Location: ' . $_SESSION['medialab_api_redirect'] . '?' . $_SERVER['QUERY_STRING']);
	unset($_SESSION['medialab_api_redirect']);
	die();
}

/**
 * Attempt to authenticate with the API, or redirect the user where necessary.
 * @param Medialab\Config\OAuth2Config $config
 * @param string $redirect_to
 * @param boolean $verbose print access_token/expiry time on succesful authentication
 * @return \League\OAuth2\Client\Token\AccessToken
 * @throws \InvalidArgumentException
 */
function ml_api_authenticate(Medialab\Config\OAuth2Config $config, $redirect_to = null, $verbose = true) {
	$client = $config->getClient();

	if(isset($_GET['error'])) {
		// If, after an authorization attempt, the user comes back with an error he/she probably declined the authorization request.
		throw new \InvalidArgumentException("An error has occurred while processing your authorization: {$_GET['error']} ({$_GET['message']})");
	}

	if(isset($_GET['code'])) {
		// If we have a user coming back with an authorization code,
		// we need to complete the OAuth 2 process by exchanging it for an access token.
		// First, we need to validate the state parameter against CSRF.
		if(isset($_SESSION['medialab_state']) && (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['medialab_state']))) {
			unset($_SESSION['medialab_state']);
			throw new \InvalidArgumentException('Invalid state found with authorization code.');
		}

		$token = $client->loadAccessTokenFromAuthCode($_GET['code']);
		$_SESSION['medialab_token'] = json_encode($token);

		// Refresh page to remove the code getvar
		$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
		die();
	}

	if(isset($_SESSION['medialab_token'])) {
		$token = $client->loadAccessTokenFromJSON($_SESSION['medialab_token']);

		if($token->hasExpired()) {
			// Access token has expired. If we have a valid refresh_token, we can use that to request a new access_token.
			// If we don't, we will need to start with the authorize again.
			// If you're a real app, you need to store the refresh_token in a persistent db.
			unset($_SESSION['medialab_token']);
			$token = $client->loadAccessTokenFromRefreshToken($token->getRefreshToken());
			$_SESSION['medialab_token'] = json_encode($token);
		}
	}

	if(!isset($token) || !$token) {
		// No valid token, start authorization process from scratch.
		if($redirect_to != null) {
			$_SESSION['medialab_api_redirect'] = $redirect_to;
		}
		// We include a "state" to prevent CSRF attacks.
		$_SESSION['medialab_state'] = $client->getState();

		printf('<a href="%s">Please follow this link to authenticate your app</a>', $client->getAuthorizationUrl());
		die();
	}

	if($verbose) {
		echo 'Access token: ' . $token->getToken() . '<br/>';
		echo 'Refresh token: ' . $token->getRefreshToken() . '<br/>';
		echo 'Expires: ' . $token->getExpires() . '<br/><br/>';
	}
	return $token;
}
