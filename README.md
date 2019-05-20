# Router
The PSR RESTful Router Library

## Installation
```bash
composer require dionchaika/router:dev-master
```

```php
<?php

require_once 'vendor/autoload.php';
```

For pretty URLs create an .htaccess file
(for Apache) in your server public directory:
```apacheconf
<IfModule mod_rewrite.c>

    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule ^ - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]

</IfModule>
```

## Configuration
```php
<?php

use Dionchaika\Router\Route;
use Dionchaika\Router\Router;
use Dionchaika\Http\ServerRequest;
use Dionchaika\Http\Utils\Emitter;
use Dionchaika\Router\RouteCollection;

$routes = new RouteCollection([

    new Route('GET', '/', '\\App\\Controllers\\Home'),
    new Route('GET', '/about', '\\App\\Controllers\\About'),
    new Route('GET', '/contact', '\\App\\Controllers\\Contact')

]);

$router = new Router($routes);

Emitter::emit($router->match(ServerRequest::createFromGlobals()));
```

## Basic usage
1. Registering routes:
```php
<?php

use Dionchaika\Http\Factory\ResponseFactory;

$router->get('/', function ($request) {
    return (new ResponseFactory)
        ->createPlainTextResponse('Welcome!');
});

//
// Allowed request methods:
//
// $router->get( pattern, handler );
// $router->put( pattern, handler );
// $router->head( pattern, handler );
// $router->post( pattern, handler );
// $router->patch( pattern, handler );
// $router->delete( pattern, handler );
// $router->options( pattern, handler );
//
```
