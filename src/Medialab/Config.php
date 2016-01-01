<?php

namespace Medialab;

class Config {

	/**
	 * @var \Medialab\Client $medialab_client
	 */
	protected $medialab_client;

	/**
	 * @var array $options
	 */
	protected $options = [
		'medialabUri' => '',
		'clientId' => '',
		'clientSecret' => '',
		'redirectUri' => '',
		'state' => '',
		'scopes' => [],
	];

	function __construct() {
		$this->options['state'] = md5(uniqid());
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
		$this->options['medialabUri'] = $medialab_uri;
		return $this;
	}

	/**
	 * Set (app) client
	 * @param string $client_id
	 * @param string $client_secret
	 * @return \Medialab\Config
	 */
	public function setClient($client_id, $client_secret) {
		$this->options['clientId'] = $client_id;
		$this->options['clientSecret'] = $client_secret;
		return $this;
	}

	/**
	 * Set redirect uri for after authorization.
	 * Must match the one registered with MediaLab.
	 * @param string $redirect_uri
	 * @return \Medialab\Config
	 */
	public function setRedirectUri($redirect_uri) {
		$this->options['redirectUri'] = $redirect_uri;
		return $this;
	}

	/**
	 * Set a state to prevent CSRF when authorizing
	 * @param string $state
	 * @return \Medialab\Config
	 */
	public function setState($state) {
		$this->options['state'] = $state;
		return $this;
	}

	/**
	 * Add a scope to request
	 * @param string $scope
	 * @return \Medialab\Config
	 */
	public function addScope($scope) {
		$this->options['scopes'][] = $scope;
		return $this;
	}

	/**
	 * Get a MediaLab client with the provided configuration
	 * @return \Medialab\Client
	 */
	public function getClient() {
		if($this->medialab_client === null) {
			$this->medialab_client = new Client($this->options);
		}
		return $this->medialab_client;
	}
}