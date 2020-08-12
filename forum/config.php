<?php


$db_type = 'mysqli';
$db_host='db150c.pair.com';
$db_username='jstan2_2_w';
$db_password='Js120767';
$db_name = 'jstan2_general';
$db_prefix = 'ichange_fluxbb_';
$p_connect = false;
if($_SERVER['SERVER_NAME'] == "localhost"){
	$db_host = 'localhost';
	$db_username = 'admin';
	$db_password = 'admin';
}

$cookie_name = 'pun_cookie_3b46c1';
$cookie_domain = '';
$cookie_path = '/';
$cookie_secure = 0;
$cookie_seed = 'e35e3acccc54b3af';

define('PUN', 1);
