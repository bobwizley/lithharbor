<?php
ini_set('display_errors','off');
// phpBB 3.0.x auto-generated configuration file
// Do not change anything in this file!
$dbms = 'mysqli';
$dbhost = getenv('DB_HOST') ?: 'mariadb';
$dbport = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'lithharbor';
$dbuser = getenv('DB_USER') ?: 'lithharbor';
$dbpasswd = getenv('DB_PASSWORD') ?: '';
$table_prefix = 'phpbb_';
$acm_type = 'file';
$load_extensions = '';

@define('PHPBB_INSTALLED', true);
// @define('DEBUG', true);
// @define('DEBUG_EXTRA', true);
?>
