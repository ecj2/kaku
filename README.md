## Kaku
Kaku is a simple, lightweight Weblog content management system written in PHP. It was designed to run well on older hardware, such as the Raspberry Pi.

## License
See the [LICENSE](LICENSE.md) file for license rights and limitations (MIT).

## Installation
Edit the database configurations in `includes/configuration.php`, then view `index.php` in a Web browser. Tables will be created automatically. If errors appear, check your database configurations and run `index.php` again. If no errors appear, you have successfully installed Kaku, and are free to delete `install.php`.

## URL Rewrite
In order to use Kaku with pretty URLs, you will need to configure your Web server accordingly.

On Nginx, use the following:

```
rewrite ^(.*)/feed$ $1/feed.php;
rewrite ^(.*)/post/(.*)$ $1/?post_url=$2;
rewrite ^(.*)/page/(.*)$ $1/?page_url=$2;
rewrite ^(.*)/page/([0-9]+)$ $1/?page_number=$2;
```

On Apache, use the following (not tested):

```
RewriteEngine On
RewriteBase /
rewrite ^(.*)/feed$ $1/feed.php;
rewrite ^(.*)/post/(.*)$ $1/?post_url=$2;
rewrite ^(.*)/page/(.*)$ $1/?page_url=$2;
rewrite ^(.*)/page/([0-9]+)$ $1/?page_number=$2;
```

You should also redirect 404 errors to `error.php?code=404`.

## Requirements
PHP 5.5 or greater and MySQL 5.0 or greater is required.
