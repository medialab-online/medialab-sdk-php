<?php

namespace Medialab\Config;

/**
 * Class AbstractConfig
 * @package medialab-sdk-php
 *
 * Shared config code.
 */
abstract class AbstractConfig implements ConfigInterface {

	/**
	 * @var $url
	 */
	private $url;

	/**
	 * Set the full URL to the MediaLab channel (e.g. https://example.medialab.co)
	 * @param string $url
	 */
	protected function setURL(string $url): void {
		// make sure we have an ending slash
		$url = rtrim($url, '/') . '/';

		if (strpos($url, 'api/') === false) {
			$url .= 'api/';
		}
		$this->url = $url;
	}

	/**
	 * Get API URL.
	 * @return string
	 */
	public function getURL(): string {
		return $this->url;
	}

}