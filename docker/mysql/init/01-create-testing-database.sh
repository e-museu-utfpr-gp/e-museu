#!/bin/bash
set -euo pipefail
# Runs on first MySQL container init only (empty data volume). Grants app user access to the PHPUnit DB name.
umask 077
cnf="$(mktemp)"
{
	echo '[client]'
	echo 'user=root'
	echo "password=${MYSQL_ROOT_PASSWORD}"
} >"$cnf"
mysql --defaults-extra-file="$cnf" <<-EOSQL
	CREATE DATABASE IF NOT EXISTS emuseu_testing
		CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	GRANT ALL PRIVILEGES ON emuseu_testing.* TO '${MYSQL_USER}'@'%';
	FLUSH PRIVILEGES;
EOSQL
rm -f "$cnf"
