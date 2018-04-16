<?php

namespace Medialab\Config;

/**
 * Interface ConfigInterface
 * @package medialab-sdk-php
 *
 * An interface for configs to be loaded into the MediaLabService.
 */
interface ConfigInterface {

	/**
	 * Get URL to API.
	 * @return string
	 */
	public function getURL(): string;

	/**
	 * Get instance of client.
	 * @return \Medialab\Client\ClientInterface
	 */
	public function getClient();

	/**
	 * Get instance of the Http client.
	 * @return \GuzzleHttp\ClientInterface
	 */
	public function getHttpClient(): \GuzzleHttp\ClientInterface;
}