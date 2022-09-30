<?php
require_once 'Auth.php';
$host =  $_SERVER['HTTP_HOST'];
$auth = new Auth();
$auth->auth($host);