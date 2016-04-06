# System requirements

* *nix
* Git
* PHP 5.4.x (also `curl` and `php5-curl`)
* Composer

# Usage:

* `cd`
* `git clone git@github.com:xcopy/dieselup.git`
* `cd dieselup`
* `composer install`
* `cp .env.dist .env`
* Set your username and password in `.env` file
* `bin/dieselup YOUR-TOPIC-ID`

## Cron task example:

`*/1 * * * * /path/to/dieselup/bin/dieselup YOUR-TOPIC-ID > /dev/null 2>&1`

That's it!
