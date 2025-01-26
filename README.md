# web-php

Basic library code for web applications in PHP.

## Installation

You can require it directly with Composer:

```bash
composer require jdwx/web-php
```

Or download the source from GitHub: https://github.com/jdwx/web-php.git

## Requirements

This module requires PHP 8.2 or later.

## Usage

The most-used functionality of this module is providing a type-safe interface to the web-related superglobals. Here is a basic usage example:

```php
$req = Request::getGlobal();

# Returns a Parameter or null if the parameter is not set.
$param = $req->get( 'param' );

# Returns a Parameter or throws an exception if the parameter is not set.
$param = $req->getEx( 'param' );

# Get a parameter as a string, exploding if either assumption (that it 
# exists and is a string) is false. (See jdwx_param for other types and
# conversions available.) This is the most common idiom for getting 
# parameters from the request safely.
$param = $req->postEx( 'param' )->asString();

# Get the contents of a file upload without moving it to a permanent location.
$param = $req->FILES()->fetchAsString( 'file_param' );
```

There are many unit tests for this module which provide additional examples of usage.

## Stability

This bulk of this module (include functionality related to requests, sessions, server values, and files) is considered stable and is extensively used in production code, handling millions of requests per day.

The IRouter interface and derivatives should be considered unstable and subject to change.

The test coverage is very good but not complete. Some web-related functionality is difficult to test in a unit test.

## History

This module was adapted from a private repository in December 2024.

