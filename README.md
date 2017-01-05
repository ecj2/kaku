## Kaku
Kaku is a lightweight content management system written in PHP. It was designed to facilitate managing blogs and to run well on older hardware, such as the Raspberry Pi.

## Installation
Clone Kaku onto your Web server, configure your database settings in `core/includes/configuration.php`, and then view the index of your blog in a Web browser. That is all there is to it.

## URL Rewrite
In order to use Kaku with pretty URLs, you will need to configure your Web server accordingly.

On Nginx, use the following for your Kaku directory:

```
if (!-e $request_filename) {

  rewrite ^(.+)$ index.php?path=$1;
}
```

On Apache, use the following in your .htaccess file:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.+)$ index.php?path=$1 [QSA,B]
```

## Requirements
PHP >= 5.5 and MySQL >= 5.0

