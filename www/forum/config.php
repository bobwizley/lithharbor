<?php
ini_set('display_errors', 'off');
// phpBB 3.3.x configuration file — values resolved from the environment.
$dbms = 'phpbb\\db\\driver\\mysqli';
$dbhost = getenv('DB_HOST') ?: 'mariadb';
$dbport = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'lithharbor';
$dbuser = getenv('DB_USER') ?: 'lithharbor';
$dbpasswd = getenv('DB_PASSWORD') ?: '';
$table_prefix = 'phpbb_';
$phpbb_adm_relative_path = 'adm/';
$acm_type = 'phpbb\\cache\\driver\\file';

@define('PHPBB_INSTALLED', true);
@define('PHPBB_ENVIRONMENT', 'production');
