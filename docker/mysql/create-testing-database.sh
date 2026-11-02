#!/usr/bin/env bash

mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS inodebox_testing;
EOSQL

if [ -n "$MYSQL_USER" ]; then
mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    GRANT ALL PRIVILEGES ON \`inodebox_testing%\`.* TO '$MYSQL_USER'@'%';
EOSQL
fi
