# ![](docs/assets/postmill-128.png) Postmill

**Postmill** is a free, web-based, social link aggregator with voting and
threaded comments. It is built on the [Symfony](https://symfony.com/) framework.
Any similarities between this software and that of a large community symbolised
by an alien logo are purely coincidental.

## Requirements

* PHP >= 7.1 with the curl, gd, iconv, intl, json, mbstring, PDO_PGSQL and xml
  extensions.
* PostgreSQL >= 9.4
* [Composer](https://getcomposer.org/)

Postmill should be able to run under any Unix environment. It has been tested to
work under Linux, macOS, and Windows 10's Linux subsystem. Running directly on
Windows may work, but is unsupported.

## Getting started

Clone the repository somewhere and navigate there with the command line.

### Retrieving frontend assets

Download [the pre-built assets][assets] and unzip them into the `public/build`
folder such that this contains a large amount of files. Alternatively, you may
install Node and compile your own assets with `npm run build-dev`/`npm run
build-prod`.

### Setting up the backend

1.  Run `composer install`.

2.  Create a `.env.local` file in the project root. At minimum, you must define
    your database configuration here:
    
    ~~~bash
    DATABASE_URL='pgsql://db_user:db_password@localhost:5432/db_name?serverVersion=9.6'
    ~~~
    
    Instructions for setting up a database can be found at
    [docs/database-setup.md](docs/database-setup.md).
    
    You can copy other values from `.env` to override them in your installation.

3.  Run `vendor/bin/requirements-checker` to ensure your environment meets
    necessary requirements needed to run Postmill. Fix any errors that arise.

4.  Run `bin/console doctrine:migrations:migrate` to load the database schema.

5.  Run `bin/console app:user:add <username> --admin` to create a user account.

6.  Run `bin/console server:run` to start the application.

7.  Navigate to <http://localhost:8000/>. Log in with the credentials you chose
    in step 2.

## Support

We have a [support board][support] and a [Matrix channel][matrix] where you can
ask for help and support.

## Reporting issues

* Bugs and suggestions on how to improve existing functionality should be
  reported on the [issue tracker][issues].
* Feature requests are no longer acceptable on the issue tracker--I will not do
  free work for you.
* Support questions do not belong on the issue tracker. Use the aforementioned
  public support channels.

## Contributions

You are always welcome to submit merge requests for bug fixes, improvements,
documentation, translations, and so on.

If you want to contribute a new feature, you should start an issue on the issue
tracker to gauge the possibility of it being merged. A feature that's outside
the scope of the software is likely to be rejected. We don't want hurt feelings,
so please just ask beforehand.

If you'd like to support me with money, you can send Bitcoins to
`1AXAH2ZaHfVsq2xnbXRN9497FpUAri8x72`.

## Contact

You can email emma1312@protonmail.ch to discuss something in private with the
creator of the software. **Do not email me for asking for support**--use the
public support channels instead.

## License

The software is released under the zlib license. See the `LICENSE` file for
details.


[issues]: https://gitlab.com/edgyemma/Postmill/issues
[matrix]: https://matrix.to/#/#postmill:matrix.org
[support]: https://community.postmill.xyz/f/Support
[assets]: https://gitlab.com/edgyemma/Postmill/-/jobs/artifacts/master/download?job=build-assets%3Aprod
