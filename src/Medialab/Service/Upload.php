<?php

namespace Medialab\Service;

class Upload extends MedialabService {

	/**
	 * @var array $upload_id
	 */
	protected $upload_id;

	function __construct($client) {
		parent::__construct($client);
	}

	/**
	 * Start the upload process by requesting an upload id
	 */
	public function startUpload() {
		$this->upload_id = $this->execute('upload/id', 'POST');
		return $this->upload_id;
	}

	/**
	 * Upload a file
	 * @param int $folder_id target folder
	 * @param string $path absolute path to file
	 * @param string $filename to change filename
	 * @return array
	 */
	public function uploadFile($folder_id, $path, $filename = null) {
		if(empty($this->upload_id)) {
			$this->startUpload();
		}
		$data = array(
			'file' => '@'.$path,
		);
		if($filename !== null) {
			$data['file'] .= ';filename='.$filename;
		}
		$url = $this->getProvider()->createUrl(
			"upload/file/{$this->upload_id['ulid']}/{$folder_id}"
		);
		$headers = $this->getProvider()->getHeaders($this->client->getAccessToken());
		$headers_format = array();
		foreach($headers as $key => $header) {
			$headers_format[] = $key . ': ' . $header;
		}

		// unfortunately the shipped version of guzzle does not support custom curl options
		// so we have to make our own request for uploading files
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_format);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		curl_close ($ch);

		return $this->parseResponse($result);
	}

	/**
	 * Finish the upload process
	 */
	public function finishUpload() {
		$result = $this->execute('upload/id/' . $this->upload_id['ulid'], 'DELETE');
		$this->upload_id = null;
		return $result;
	}
}