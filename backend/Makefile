phpstan:
	php -d memory_limit=4G vendor/bin/phpstan analyse

cs-check:
	./vendor/bin/php-cs-fixer fix --allow-risky=yes --dry-run

cs-fix:
	./vendor/bin/php-cs-fixer fix --allow-risky=yes

phpcs:
	./vendor/bin/phpcs --standard=PSR12 app --report=diff --colors

dep:
	./vendor/bin/deptrac analyse

build:
	docker build -t dep:main .
unit:
	./vendor/bin/phpunit --bootstrap tests/unit/bootstrap.php tests/unit

pre-commit: phpstan cs-check phpcs dep unit

help: ## List all available commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-16s\033[0m %s\n", $$1, $$2}'

.DEFAULT_GOAL := help
