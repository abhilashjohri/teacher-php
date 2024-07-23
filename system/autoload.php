<?php 
session_start();
error_reporting(E_ERROR);
define("_DIR_",dirname(__FILE__));
include_once ( _DIR_.'/config.php');
include_once __DIR__ . '/class/Database.php';
include_once __DIR__ . '/function.php';
?>    