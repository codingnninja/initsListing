<?php
require_once '../classes/init.php';
//
$request = $_SERVER['REDIRECT_URL'];
//router is located in functions.php
route($request);