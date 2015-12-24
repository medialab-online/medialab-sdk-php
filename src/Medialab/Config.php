<?php

namespace Medialab;

class Config {

	/**
	 * @var \Medialab\OAuth\MedialabProvider $provider
	 */
	protected $provider;

	function __construct()  {
		$this->provider = new OAuth\MedialabProvider();
	}

	/**
	 * Return MedialabProvider
	 * @return \Medialab\OAuth\MedialabProvider
	 */
	public function getProvider() {
		return $this->provider;
	}

	/**
	 * Set the full URI to the MediaLab channel (e.g. https://demo.medialab.co)
	 * @param string $medialab_uri
	 * @return \Medialab\Config
	 */
	public function setMedialab($medialab_uri) {
		if(substr($medialab_uri, -1) != '/') {
			$medialab_uri .= '/';
		}
		if(strpos($medialab_uri, 'api/') === false) {
			$medialab_uri .= 'api/';
		}
		$this->provider->medialabUri = $medialab_uri;
		return $this;
	}

	/**
	 * Set (app) client
	 * @param string $client_id
	 * @param string $client_secret
	 * @return \Medialab\Config
	 */
	public function setClient($client_id, $client_secret) {
		$this->provider->clientId = $client_id;
		$this->provider->clientSecret = $client_secret;
		return $this;
	}

	/**
	 * Set redirect uri for after authorization.
	 * Must match the one registered with MediaLab.
	 * @param string $redirect_uri
	 * @return \Medialab\Config
	 */
	public function setRedirectUri($redirect_uri) {
		$this->provider->redirectUri = $redirect_uri;
		return $this;
	}

	/**
	 * Add a scope to request
	 * @param string $scope
	 * @return \Medialab\Config
	 */
	public function addScope($scope) {
		$this->provider->scopes[] = $scope;
		return $this;
	}
}