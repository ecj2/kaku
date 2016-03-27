## Kaku
Kaku is a simple, lightweight Weblog content management system written in PHP. It was designed to run well on older hardware, such as the Raspberry Pi.

## License
See the [LICENSE](LICENSE.md) file for license rights and limitations (MIT).

## Installation
Edit the database configurations in `includes/configuration.php`, then view `index.php` in a Web browser. Tables will be created automatically. If errors appear, check your database configurations and run `index.php` again. If no errors appear, you have successfully installed Kaku, and are free to delete `install.php`.

## URL Rewrite
In order to use Kaku with pretty URLs, you will need to configure your Web server accordingly.

On Nginx, use the following for your Kaku directory:

```
if (!-e $request_filename) {

  rewrite ^(.+)$ index.php?path=$1;
}
```

On Apache, use the following in your .htaccess file wherever you installed Kaku:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.+)$ index.php?path=$1 [QSA,L]
```

You should also redirect 404 errors to `error.php?code=404` on both Nginx and Apache.

## Requirements
PHP >= 5.5

MySQL >= 5.0

## Development
Please look at the [wiki](https://github.com/ecj2/kaku/wiki) for development information.
