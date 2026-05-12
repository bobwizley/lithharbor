<?php
if (!defined('IN_PHPBB')) exit;
$expired = (time() > 1804392091) ? true : false;
if ($expired) { return; }

$data =  unserialize('a:4:{s:4:"name";s:9:"AeroBlack";s:9:"copyright";s:25:"&copy; phpBB Headquarters";s:7:"version";s:5:"3.0.5";s:8:"filetime";i:1244658212;}');

?>