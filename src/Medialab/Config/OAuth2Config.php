<?php

namespace Medialab\Config;

/**
 * Class OAuth2Config
 * @package medialab-sdk-php
 *
 * Config class for authentication using the OAuth2 workflow.
 */
class OAuth2Config extends AbstractConfig {

	/**
	 * @var \Medialab\Client\OAuth2Client
	 */
	private $client;

	/**
	 * @var array
	 */
	private $options = [
		'clientId' => '',
		'clientSecret' => '',
		'redirectUri' => '',
		'state' => '',
		'scopes' => [],
	];

	/**
	 * @var \Medialab\Client\OAuth2MedialabProvider
	 */
	private $provider;

	/**
	 * OAuth2Config constructor.
	 * @param string $medialab_url
	 */
	public function __construct(string $medialab_url) {
		parent::__construct();

		$this->setURL($medialab_url);

		$this->options['state'] = md5(uniqid());

	}

	/**
	 * Set (app) client for OAuth2
	 * @param string $client_id
	 * @param string $client_secret
	 * @return OAuth2Config
	 */
	public function setClient(string $client_id, string $client_secret): OAuth2Config {
		$this->options['clientId'] = $client_id;
		$this->options['clientSecret'] = $client_secret;
		return $this;
	}

	/**
	 * Set redirect uri for after OAuth2 authorization.
	 * Must match the one registered with MediaLab.
	 * @param string $redirect_uri
	 * @return OAuth2Config
	 */
	public function setRedirectUri(string $redirect_uri): OAuth2Config {
		$this->options['redirectUri'] = $redirect_uri;
		return $this;
	}

	/**
	 * Set a state to prevent CSRF when authorizing
	 * @param string $state
	 * @return OAuth2Config
	 */
	public function setState(string $state): OAuth2Config {
		$this->options['state'] = $state;
		return $this;
	}

	/**
	 * Add a scope to request for OAuth2
	 * @param string $scope
	 * @return OAuth2Config
	 */
	public function addScope(string $scope): OAuth2Config {
		$this->options['scopes'][] = $scope;
		return $this;
	}

	/**
	 * Get client
	 * @return \Medialab\Client\OAuth2Client
	 */
	public function getClient(): \Medialab\Client\OAuth2Client {
		if($this->client === null) {
			$this->client = new \Medialab\Client\OAuth2Client($this);
		}
		return $this->client;
	}

	/**
	 * Load a new instance for the Medialab OAuth2 provider.
	 * @return \Medialab\Client\OAuth2MedialabProvider
	 */
	public function getOAuth2MedialabProvider(): \Medialab\Client\OAuth2MedialabProvider {
		if($this->provider === null) {
			$this->provider = new \Medialab\Client\OAuth2MedialabProvider($this->options, [
				'httpClient' => $this->getHttpClient(),
			]);
			$this->provider->setMedialabApiURL($this->getURL());
		}

		return $this->provider;
	}
}