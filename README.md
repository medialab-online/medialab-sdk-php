# MediaLab API SDK

A library to easily implement the [MediaLab API](https://www.medialab.co/).
Full documentation of API methods is available [here](https://docs.medialab.co/).

License: [MIT](LICENSE)

Please see the examples directory on how to use these classes.

## Requirements
* PHP 7.1+
* PHP cURL extension

If you require PHP 5 support, look at version 2.

## Authentication
The SDK supports 2 methods to authenticate with the API:
* OAuth2.
  See [authorize.php](examples/authorize.php) on how you could set up the authorization workflow.
* Private Token: CLI tools or private tools that only need to access a single account.
  See [private_token.php](examples/private_token.php) for an example.

## Setup

If you're using [Composer](http://getcomposer.org/) for your project's dependencies, add the following to your "composer.json":

```
"require": {
    "medialab/medialab-sdk-php": "^3.0"
}
```
