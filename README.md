# Ecommerce example application backend

This is an incomplete REST API for an example ecommerce application,
built with Laravel plus some Laravel packages.

## Quickstart

### Requirements
- PHP 8.1+ with extensions (you might need to install others, depending on your environment)
    - ext-dom
    - ext-xml
    - ext-curl
    - ext-zip
    - ext-sqlite3
    - ext-mbstring
- [Composer](https://getcomposer.org/download/) 
- GNU make is not needed, but recommended

For instance, in Ubuntu you can install php 8.1 and extensions like this
```shell
sudo apt install php8.1-cli php8.1-dom php8.1-xml php8.1-curl \
php8.1-zip php8.1-sqlite3 php8.1-mbstring 
```

### Install with make

```shell
# basic installation with sqlite
make example_install 
# start development server
make serve
# visit the docs page at localhost:8000/docs to see documentation
```


### Install without make

```shell
# This does the same as "make example_install"
composer install
if [ ! -f .env ]; then cp -n .env.example .env; fi
if [ ! -f .env.testing ]; then cp -n .env.testing.example .env.testing; fi
touch database/database.sqlite
touch database/test_database.sqlite
php artisan migrate:fresh --seed
php artisan scribe:generate
php artisan test
# start development server
php artisan serve
# visit the docs page at localhost:8000/docs to see documentation
```

### Promote a user to admin

Admins have all capabilities. Editors have capabilities to edit/delete/update products.

To promote a user to admin:
1. Create a user on the `POST /api/register` endpoint (read the docs for parameters)
2. Run this command to promote the user
```shell
php artisan app:make-user-admin {userId}
```

## Documentation

The docs, built with [Scribe](https://scribe.knuckles.wtf/laravel/)
describe each of the endpoints with their outputs and input parameters.
They might not be 100% complete, but should be good enough to guide you
without having to read all the code. 

## Packages used in addition of Laravel core

- [Scribe](https://scribe.knuckles.wtf/laravel/)
- [Laravel-permission](https://spatie.be/docs/laravel-permission/v5/introduction)
- [Laravel-tags](https://spatie.be/docs/laravel-tags/v4/introduction)
