<?php

namespace Medialab;

class Client {

	/**
	 * @var \Medialab\OAuth\MedialabProvider $provider
	 */
	protected $provider;

	/**
	 * @var \League\OAuth2\Client\Token\AccessToken $token
	 */
	protected $token;

	/**
	 * Open new API instance
	 * @param array $options
	 */
	function __construct(array $options) {
		$this->provider = new OAuth\MedialabProvider($options);
	}

	/**
	 * Create a new URL for authentication
	 * @param string $state unique string to prevent CSRF
	 * @return string
	 */
	public function getAuthorizationUrl() {
		return $this->provider->getAuthorizationUrl();
	}

	/**
	 * Attempt to exchange the authorization code for an access_token
	 * @param string $authorization_code
	 * @return \League\OAuth2\Client\Token\AccessToken
	 * @throws \InvalidArgumentException
	 */
	public function loadAccessTokenFromAuthCode($authorization_code) {
		try {
			$this->token = $this->provider->getAccessToken('authorization_code', array(
				'code' => $authorization_code
			));
		} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $ex) {
			throw new \InvalidArgumentException($ex->getMessage());
		}
		return $this->token;
	}

	/**
	 * Attempt to load the access_token from the json_encoded AccessToken object
	 * @param type $access_token
	 * @return \League\OAuth2\Client\Token\AccessToken
	 * @throws \InvalidArgumentException
	 */
	public function loadAccessTokenFromJSON($access_token) {
		$token_info = json_decode($access_token);
		if($token_info === null) {
			throw new \InvalidArgumentException('Invalid JSON for loading access_token');
		}

		// because the OAuth2 client package from PHP League has some consistency issues, we cannot pass the object directly
		// instead make sure we have an array
		$options = array(
			'access_token' => isset($token_info->access_token) ? $token_info->access_token : null,
			'refresh_token' => isset($token_info->refresh_token) ? $token_info->refresh_token : null,
			'expires' => isset($token_info->expires) ? $token_info->expires : null,
		);
		$this->token = new \League\OAuth2\Client\Token\AccessToken($options);
		return $this->token;
	}

	/**
	 * Attempt to load a new access_token using the refresh_token.
	 * @param string $refresh_token
	 * @return \League\OAuth2\Client\Token\AccessToken
	 * @throws \InvalidArgumentException
	 */
	public function loadAccessTokenFromRefreshToken($refresh_token) {
		$this->token = $this->provider->getAccessToken('refresh_token', [
			'refresh_token' => $refresh_token,
		]);
		return $this->token;
	}

	/**
	 * Retrieve access token
	 * @return \League\OAuth2\Client\Token\AccessToken
	 * @throws Exception
	 */
	public function getAccessToken() {
		return $this->token;
	}

	/**
	 * Get provider
	 * @return \Medialab\OAuth\MedialabProvider
	 */
	public function getProvider() {
		return $this->provider;
	}

	/**
	 * Get state
	 * @return string
	 */
	public function getState() {
		return $this->provider->getState();
	}
}