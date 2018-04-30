#!/usr/bin/env bash

composer install \
&& npm i \
&& npm run gulp \
&& bin/console doctrine:database:drop --force \
&& bin/console doctrine:database:create \
&& bin/console doctrine:schema:create \
&& bin/console admin-platform:install:setup -n
