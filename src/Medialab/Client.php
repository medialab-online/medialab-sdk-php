<?php

namespace Medialab;

class Client {

	/**
	 * @var \Medialab\Config $config
	 */
	protected $config;

	/**
	 * @var \League\OAuth2\Client\Token\AccessToken $token
	 */
	protected $token;

	/**
	 * Open new API instance
	 * @param \Medialab\Config $config
	 */
	function __construct(Config $config) {
		$this->config = $config;
	}

	/**
	 * Get config
	 * @return \Medialab\Config
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * Create a new URL for authentication
	 * @param string $state unique string to prevent CSRF
	 * @return string
	 */
	public function getAuthorizationUrl($state = null) {
		$auth_url = $this->config->getProvider()->getAuthorizationUrl(array(
			'state' => $state,
		));
		return $auth_url;
	}

	/**
	 * Attempt to exchange the authorization code for an access_token
	 * @param string $authorization_code
	 * @return \League\OAuth2\Client\Token\AccessToken
	 * @throws \InvalidArgumentException
	 */
	public function authenticate($authorization_code) {
		$token = $this->config->getProvider()->getAccessToken('authorization_code', array(
			'code' => $authorization_code
		));
		return $token;
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
			'access_token' => isset($token_info->accessToken) ? $token_info->accessToken : null,
			'refresh_token' => isset($token_info->refreshToken) ? $token_info->refreshToken : null,
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
		$grant = new \League\OAuth2\Client\Grant\RefreshToken();

		$this->token = $this->config->getProvider()->getAccessToken($grant, array(
			'refresh_token' => $refresh_token,
		));
		return $this->token;
	}

	/**
	 * Is the token expired, or expiring in the next 30 seconds?
	 * @return boolean
	 */
	public function isAccessTokenExpired() {
		if (!$this->token || !isset($this->token->expires)) {
			return true;
		}

		// If the token is set to expire in the next 30 seconds.
		return ($this->token->expires - 30) < time();
	}

	/**
	 * Get the refresh token, if any.
	 * @return string|null NULL if no refresh_token found, string otherwise
	 */
	public function getRefreshToken() {
		if(isset($this->token->refresh_token)) {
			return $this->token->refresh_token;
		} else {
			return null;
		}
	}

	/**
	 * Retrieve access token
	 * @return \League\OAuth2\Client\Token\AccessToken
	 * @throws Exception
	 */
	public function getAccessToken() {
		return $this->token;
	}
}