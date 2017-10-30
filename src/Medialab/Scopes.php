<?php

namespace Medialab;

/**
 * Class Scopes
 * @package medialab-sdk-php
 *
 * An overview of all available scopes supported by the OAuth2 API.
 */
class Scopes {

	/**
	 * Read access files and folders the user has access to.
	 */
	const SCOPE_BASIC = 'basic';

	/**
	 * View user profile information.
	 */
	const SCOPE_USER_INFO = 'user.info';

	/**
	 * Edit and remove files/folders.
	 */
	const SCOPE_MANAGE = 'manage';

	/**
	 * Upload files.
	 */
	const SCOPE_UPLOAD = 'upload';

	/**
	 * Share files by email and embed code.
	 */
	const SCOPE_SHARE = 'share';
	
}