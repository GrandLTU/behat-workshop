Admin platform
==============

Installation
------------

`./install.sh`

Or

`composer install`
`npm i`
`npm run gulp`
`bin/console doc:d:c`
`bin/console doc:s:c`
`bin/console admin-platform:install:setup -n`

Credentials
-----------

Username: *admin-platform@example.com*
Password: *admin-platform*

Behat
-----

Tests without javascript can be run right away

`vendor/bin/behat -p no_js`

To run tests with javascript start web server and chromedriver
 - `bin/console server:start 127.0.0.1:8000`
 - `chromedriver --url-base=wd/hub --port=4444` [Installation](https://gist.github.com/ziadoz/3e8ab7e944d02fe872c3454d17af31a5)

Now it is possible to run behat tests with javascript

`vendor/bin/behat -p javascript`

Run all tests:

`vendor/bin/behat`
