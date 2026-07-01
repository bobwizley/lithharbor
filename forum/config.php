<?php
ini_set('display_errors', 'off');
// phpBB 3.3.x configuration file — values resolved from the environment.
$dbms = 'phpbb\\db\\driver\\postgres';
$dbhost = getenv('DB_HOST') ?: 'postgres';
$dbport = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'lithharbor';
$dbuser = getenv('DB_USER') ?: 'lithharbor';
$dbpasswd = getenv('DB_PASSWORD') ?: '';
$table_prefix = 'phpbb_';
$phpbb_adm_relative_path = 'adm/';
$acm_type = 'phpbb\\cache\\driver\\file';

@define('PHPBB_INSTALLED', true);
@define('PHPBB_ENVIRONMENT', 'production');
