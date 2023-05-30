
test:
	php artisan test

docs:
	php artisan scribe:generate

serve:
	php artisan serve

example_install:
	composer install
	if [ ! -f .env ]; then cp -n .env.example .env; fi
	if [ ! -f .env.testing ]; then cp -n .env.testing.example .env.testing; fi
	touch database/database.sqlite
	touch database/test_database.sqlite
	php artisan migrate:fresh --seed
	php artisan scribe:generate
	php artisan test
