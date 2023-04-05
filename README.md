# Laravel DB Storage Files
<p>File storage inside MySQL database. This file storage is convenient if you need to have access to files from multiple servers and do not want to create separate FTP servers.</p>

* At the moment, it is only compatible with MySQL and Laravel because it uses models from Laravel.

## Installion 

Install package
```{bash}
composer require bazarov392/laravel-db-storage-files
```
Create a table with files in the database.
```{bash}
php artisan migrate --path=/vendor/bazarov392/laravel-db-storage-files/migrations
```

## Usage

All necessary information is available in the docs directory.All necessary information is available in the "docs" directory.

