
DIESELUP [![Build Status](https://travis-ci.org/xcopy/dieselup.svg?branch=master)](https://travis-ci.org/xcopy/dieselup)
========

System requirements
-------------------

* *nix
* Git
* PHP 5.4.x (also `curl` and `php5-curl`)
* Composer

Usage:
------

```shell
cd
git clone git@github.com:xcopy/dieselup.git
cd dieselup
composer install
```

Then set your username and password in `.env` file

```shell
bin/dieselup YOUR-TOPIC-ID`
```

Cron task example:

```shell
*/1 * * * * /path/to/dieselup/bin/dieselup YOUR-TOPIC-ID > /dev/null 2>&1
```

That's it!
