# Using Docker

Postmill includes a configuration file for Docker Compose. This allows you to
set up a development environment quickly, without risk to your system (e.g. from
third-party libraries that turn out to be malicious), and without having to mess
around with third-party repositories to get latest versions of Postmill's
dependencies.

Currently there is no configuration for running Postmill in production using
Docker. Pre-built images for this purpose are planned to be released in the
future.

## Running commands

Any calls to `bin/console` or `composer` should be made in the `php` container.
Here are a few examples:

```
$ docker-compose exec php composer validate --strict
$ docker-compose exec php bin/console cache:clear
```

For commands pertaining to assets (e.g. anything that has to do with `npm`), the
`assets` container should be used.

```
$ docker-compose exec assets npm run build-prod
```

## Maintaining correct file permissions

In a Linux/Unix environment, you may have trouble with new files being owned by
the root user, instead of your own user account. To solve this, create a file
named `docker-compose.override.yml`, and populate it like so:

```yaml
version: '3'

services:
    php:
        user: 420:69
    assets:
        user: 420:69
```

In this example, `420` is the UID, and `69` is the GID. You can find the
appropriate values by running `id -u` and `id -g`, respectively. Typically both
of these values will be `1000` on a single-user system.
