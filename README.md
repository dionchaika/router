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
```.htaccess
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
