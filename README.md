# Ecommerce example application backend

This is an incomplete backend for an example ecommerce application

## Quickstart with example

### Requirements
- PHP 8.1+ with extensions
- Composer 
- GNU make is not needed, but recommended

### With make

```shell
# basic installation with sqlite
make example_install 
# start development server
make serve
# visit the docs page at localhost:8000/docs to see documentation
```



### Without make

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
