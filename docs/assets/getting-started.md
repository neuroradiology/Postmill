Getting started
===

This document will take you through the steps necessary to set up your own,
local Postmill instance. If you want to run a live site, there are other
necessary steps to take that are not mentioned here.

## Requirements

* PHP >= 7.1 with the APCu, curl, gd, mbstring, PDO_PGSQL and xml extensions.
* PostgreSQL >= 9.4
* [Composer](https://getcomposer.org/)
* [Node.js](https://nodejs.org/en/) (optional; keep reading)

## Cloning the git repository

Clone the git repository somewhere and navigate there.

~~~
$ git clone https://gitlab.com/edgyemma/Postmill.git
$ cd Postmill
~~~

## Frontend assets

To quickly get started, you can [download the latest frontend assets][assets]
and unpack them inside Postmill's root folder. The `public/build` directory
should now contain some files.

[assets]: https://gitlab.com/edgyemma/Postmill/-/jobs/artifacts/improved-ci/download?job=build-assets%3Aprod

### Building your own assets

For developing, you'll want the ability to build your own assets. 

## Setting up the backend
