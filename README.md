# SVC SDK for PHP

Services SDK for PHP - Use LifeMD Services in your PHP project.

## Installation

Installation via [Composer](https://getcomposer.org/). First, add the repository to your `composer.json` file:

```bash
"repositories":[
        {
            "type": "vcs",
            "url": "git@github.com:thecvlb/svc-sdk-php.git"
        }
    ]
```

And add the package to your `requirements`:
```bash
"thecvlb/svc-sdk-php": "1.*"
```

## Authentication

Your application will need an OAuth access token to make requests on the LifeMD Services API. Once your application is registered you can request the JWT with your `client_id` and `client_secret`. Your access  token will be stored in your Redis instance for the duration of its lifecycle. If the token is not found in Redis the SDK will request a new token and update Redis.

## Usage

Instantiate a new SVC SDK object and then make requests with the service endpoints.

```php
use \CVLB\Svc\Api\Sdk;
use \CVLB\Svc\Api\AuthService;
use \CVLB\Svc\Api\ClientBuilder;

$svc = new Sdk(new AuthService(new Redis(), ['client_id' => '<your client_id>', 'client_secret' => '<your client_secret>']), new ClientBuilder());
```

## Available Services

The following services and endpoints are available.

### Logging

SVC Logging service allows you to send your application logs entries to a central repository. Documentation can be found at https://documenter.getpostman.com/view/16680838/UVeGpQY7

#### Sample Requests
```php
$svc->logging()->put('Log this message');

\Exception $exception
$svc->logging()->put($exception->getMessage(), $exception->getTrace(), $exception->getCode());
```
#### Response
```json
{
  "success": true,
  "code": 200,
  "message": "OK"
}
```