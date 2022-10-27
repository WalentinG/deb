define compose
    @docker compose -p deb $(1)
endef

up:
	$(call compose, up -d)

ps:
	$(call compose, ps)

stop:
	$(call compose, stop)

down:
	$(call compose, down)

logs:
	$(call compose, logs -f php-api)

sh:
	$(call compose, exec php-api sh)

api-migrate:
	$(call compose, run --rm php-migrate)
