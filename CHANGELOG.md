# Change Log
All notable changes to this project will be documented in this file.

## [3.1.0] - 2018-04-16
- The Guzzle HTTP client is now set/taken from the Config objects to allow for a custom HTTP client (it can no longer be set in the PrivateTokenClient).
- The OAuth2 provider creation is now also part of the Config objects.

## [3.0.0] - 2017-10-31
- Refactoring Config util to allow multiple types of config (OAuth2 and PrivateToken).
  Please note these changes are not backwards compatible and will require a change to how the config class is set up.
- Adding support to execute API methods while authenticating using a private token.
- Upgrading League\OAuth2 client library to v2.2.
- Refactoring all methods to include type hinting and return values, now requiring PHP 7.1+. 

## [2.0.1] - 2016-01-05
- Changing scope delimiter to space as per OAuth2 specifications.

## [2.0.0] - 2016-01-01
- Updating league/oauth2-client package to 1.1, now requiring PHP 5.5.
- Adding Media service.

## [1.0.0] - 2015-12-24
- Initial release (PHP 5.3 compatible).
