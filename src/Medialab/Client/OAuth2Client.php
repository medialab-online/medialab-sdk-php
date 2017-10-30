<?php

namespace Medialab\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class OAuth2Client
 * @package medialab-sdk-php
 *
 * A client to handle requests that are authorized using the OAuth2 workflow.
 * It uses the OAuth2Client library to provide all OAuth2 related features, and merely provides a
 * library-independent interface to be used in the SDK.
 * This hopefully prevents us from having to rewrite the SDK part when upgrading/switching client libraries.
 *
 * It utilizes the OAuth2MedialabProvider which in turn extends the actual library.
 */
class OAuth2Client implements ClientInterface {

	/**
	 * @var \Medialab\Config\OAuth2Config
	 */
	private $config;

	/**
	 * @var OAuth2MedialabProvider
	 */
	private $provider;

	/**
	 * @var \League\OAuth2\Client\Token\AccessToken
	 */
	private $token;

	/**
	 * Open new API instance
	 * @param \Medialab\Config\OAuth2Config $config
	 */
	public function __construct(\Medialab\Config\OAuth2Config $config) {
		$this->config = $config;
		$this->provider = new OAuth2MedialabProvider($config->getOAuth2Options());
		$this->provider->setMedialabApiURL($config->getURL());
	}

	/**
	 * Prepare a new HTTP request.
	 *
	 * @param string $url
	 * @param string $http_method
	 * @param array $options
	 * @return \Psr\Http\Message\RequestInterface
	 */
	public function prepareRequest(string $url, string $http_method, array $options = []): \Psr\Http\Message\RequestInterface {
		return $this->provider->getAuthenticatedRequest(
			$http_method,
			$url,
			$this->getAccessToken(),
			$options
		);
	}

	/**
	 * Execute a new HTTP request and return its response.
	 *
	 * @param string $url
	 * @param string $http_method
	 * @param array $options
	 * @return \Psr\Http\Message\ResponseInterface
	 * @throws \Exception
	 */
	public function executeRequest(string $url, string $http_method, array $options = []): \Psr\Http\Message\ResponseInterface {
		// get authorization headers from provider
		$options['headers'] = $this->getProvider()->getHeaders($this->getAccessToken());

		try {
			return $this->getProvider()->getHttpClient()->request(
				$http_method,
				$url,
				$options
			);
		} catch(GuzzleException $ex) {
			throw new \Exception($ex->getMessage());
		}
	}

	/**
	 * Create a new URL for authentication
	 * @return string
	 */
	public function getAuthorizationUrl(): string {
		return $this->provider->getAuthorizationUrl();
	}

	/**
	 * Attempt to exchange the authorization code for an access_token
	 * @param string $authorization_code
	 * @return \League\OAuth2\Client\Token\AccessToken
	 * @throws \InvalidArgumentException
	 */
	public function loadAccessTokenFromAuthCode(string $authorization_code): \League\OAuth2\Client\Token\AccessToken {
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
	 * @param string $access_token
	 * @return \League\OAuth2\Client\Token\AccessToken
	 * @throws \InvalidArgumentException
	 */
	public function loadAccessTokenFromJSON(string $access_token): \League\OAuth2\Client\Token\AccessToken {
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
	public function loadAccessTokenFromRefreshToken(string $refresh_token): \League\OAuth2\Client\Token\AccessToken {
		$this->token = $this->provider->getAccessToken('refresh_token', [
			'refresh_token' => $refresh_token,
		]);
		return $this->token;
	}

	/**
	 * Retrieve access token
	 * @return \League\OAuth2\Client\Token\AccessToken
	 */
	public function getAccessToken(): ?\League\OAuth2\Client\Token\AccessToken {
		return $this->token;
	}

	/**
	 * Get state
	 * @return string
	 */
	public function getState(): string {
		return $this->provider->getState();
	}

	/**
	 * Get provider
	 * @return OAuth2MedialabProvider
	 */
	private function getProvider(): OAuth2MedialabProvider {
		return $this->provider;
	}
}