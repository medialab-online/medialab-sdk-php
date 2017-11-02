<?php

require_once __DIR__ . '/authorize.php';

/**
 * This example uses the OAuth2 authorization workflow with "upload" permissions, then uploads a file.
 * For more details on how to upload files using the API, visit https://docs.medialab.co/upload.
 */

$config = new Medialab\Config\OAuth2Config(ML_MEDIALAB_URI);
$config ->setClient(ML_API_CLIENT, ML_API_SECRET)
		->setRedirectUri(ML_REDIRECT_URI)
		->addScope(Medialab\Scopes::SCOPE_BASIC)
		->addScope(Medialab\Scopes::SCOPE_UPLOAD);

try {
	ml_api_authenticate($config, 'upload.php');
} catch (\Exception $ex) {
	print_r($ex->getMessage());
	die();
}

$media = new Medialab\Service\Media($config);
$target_folder_id = 1;

try {
	// to upload a single file, we can simply call "uploadFile":
	$file = $media->uploadFile($target_folder_id, __DIR__.'/../README.md');

	// if we want to upload multiple files in a single batch, it's recommended to request an upload id first:
	$media->startUpload();
	// now we can upload as many files as we like
	$file_1 = $media->uploadFile($target_folder_id, __DIR__.'/../README.md');
	$file_2 = $media->uploadFile($target_folder_id, __DIR__.'/../CHANGELOG.md');
	// and then finally mark the upload batch as finished.
	$media->finishUpload();

	echo '<pre>';
	print_r($file_1);
	echo '</pre>';
} catch(Exception $ex) {
	print_r('An error has occured while executing the API command: ' . $ex->getMessage());
}
