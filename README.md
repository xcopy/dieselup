
DIESELUP [![Build Status](https://travis-ci.org/xcopy/dieselup.svg?branch=master)](https://travis-ci.org/xcopy/dieselup) [![Latest Stable Version](https://poser.pugx.org/xcopy/dieselup/v/stable)](https://packagist.org/packages/xcopy/dieselup)
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

Like Ruby?
---------

Sweet. Check out [Ruby dieselup](https://github.com/xcopy/dieselup-ruby) 

Contributing
------------

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create a new Pull Request
