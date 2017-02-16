check: test cs-fix

test:
	php bin/phpunit

cs-fix:
	php vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix -vv

cs-fix-dry-run:
	php vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix -vv --dry-run

check-ci: test cs-fix-dry-run
