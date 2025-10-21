# API Development

API Development are based on Dingo API. Refer to [wiki](https://github.com/dingo/api/wiki) for more details.

An example has been created in `routes/api/_.php`.

## Sample Usage

```php
<?php

$client = new http\Client;
$request = new http\Client\Request;

$request->setRequestUrl('https://project.test/api/user');
$request->setRequestMethod('GET');
$request->setHeaders([
  'Accept' => 'application/vnd.project.v1+json',
  'Authorization' => 'Bearer [YOUR-API-KEY]'
]);

$client->enqueue($request)->send();
$response = $client->getResponse();

echo $response->getBody();
```

## Accept Header

Accept header, by default you can get the value by:

```php
api_accept_header();
```

This will return default Accept header. By default is: `application/vnd.project.v1+json`.

If you are developing newer version, make sure to define in routes the API version and simply change the version number when consuming the API, such as:

```php
$request->setHeaders([
  'Accept' => 'application/vnd.project.v2+json',
  'Authorization' => 'Bearer [YOUR-API-KEY]'
]);
```

Unless you don't need the old API version, you are safely update default `API_VERSION` in `.env` file.

## Configuration

## Important Configuration Notes

Before you decided to let consumer to use the API, do choose, either to use domain or path base for the API.

If you are choosing domain based, define `API_DOMAIN` in your `.env`, else define `API_PREFIX`.

You may want to import Insomnia [file](Insomnia.json) to test out your APIs.
