<?php
$site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=== 'on' ? "https" : "http") ."://" . $_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI'];
$site_url = 'http://localhost/apswebtech/teacher-php/';
define("SITE_URL",$site_url);
//---database details---
defined('DB_HOST')  OR define("DB_HOST", 'localhost');
defined('DB_USER')  OR define('DB_USER', 'root');
defined('DB_PASSWORD')  OR define('DB_PASSWORD', '');
defined('DB_NAME')  OR define('DB_NAME', 'teachers_db');
defined('LOCAL_TIMEZONE')  OR define('LOCAL_TIMEZONE','Asia/Calcutta');

?>