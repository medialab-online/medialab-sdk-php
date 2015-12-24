<?php

session_start();

/**
 * Attempt to authenticate with the API, or redirect the user where necessary.
 * @param Medialab\Client $client
 * @param boolean $verbose print access_token/expiry time on succesful authentication
 * @return \League\OAuth2\Client\Token\AccessToken
 * @throws \InvalidArgumentException
 */
function ml_api_authenticate(Medialab\Client $client, $verbose = true) {
	if(isset($_GET['logout'])) {
		unset($_SESSION['medialab_token']);
	}

	if(isset($_GET['error'])) {
		// If, after an authorization attempt, the user comes back with an error,
		// he/she probably declined the authorization request.
		throw new \InvalidArgumentException("An error has occurred while processing your authorization: {$_GET['error']} ({$_GET['error_description']})");
	}

	if(isset($_GET['code'])) {
		// If we have a user coming back with an authorization code,
		// we need to complete the OAuth 2 process by exchanging it for an access token.
		// First, we need to validate the state parameter against CSRF.
		if(isset($_SESSION['medialab_state'])) {
			if(empty($_GET['state']) || ($_GET['state'] !== $_SESSION['medialab_state'])) {
				throw new \InvalidArgumentException('No valid state found with authorization code.');
			}
		}

		// Attempt to exchange our authorization code for an access_token
		try {
			$token = $client->authenticate($_GET['code']);
			$_SESSION['medialab_token'] = json_encode($token);
		} catch(\InvalidArgumentException $ex) {
			throw new \InvalidArgumentException('Error authenticating: ' . $ex->getMessage());
		}

		// Redirect the user to remove the code getvar
		$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
		die();
	}

	if(isset($_SESSION['medialab_token'])) {
		$client->loadAccessTokenFromJSON($_SESSION['medialab_token']);

		if($client->isAccessTokenExpired()) {
			// Access token has expired. If we have a valid refresh_token, we can use that
			// to request a new access_token.
			// If we don't, we will need to start with the authorize again.
			unset($_SESSION['medialab_token']);

			$refresh_token = $client->getRefreshToken();

			if($refresh_token !== null) {
				try {
					$token = $client->loadAccessTokenFromRefreshToken($refresh_token);
				} catch (\InvalidArgumentException $ex) {
					throw new \InvalidArgumentException('An error has occurred while using the refresh_token: ' . $ex->getMessage());
				}
			}
		} else {
			$token = $client->getAccessToken();
		}
	}

	if(!isset($token) || !$token) {
		/**
		 * No valid token, start authorization process from scratch.
		 * We include a "state" to prevent CSRF attacks.
		 */
		$_SESSION['medialab_state'] = md5(uniqid());
		$auth_url = $client->getAuthorizationUrl($_SESSION['medialab_state']);
		printf('<a href="%s">Please follow this link to authenticate your app</a>', $auth_url);
		die();
	}

	if($verbose) {
		// Use this to interact with an API on the users behalf
		printf('Use this to interact with an API on the users behalf: %s<br/>', $token->accessToken);

		// Number of seconds until the access token will expire, and need refreshing
		printf('Number of seconds until the access token will expire, and need refreshing: %d<br/>', $token->expires - time());
	}
	return $token;
}
