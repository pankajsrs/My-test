<?php

include_once('library/config/config.php');

$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
@list($url, $param) = explode("?", $url);
@list($url, $param) = explode("&", $url);
$split_arr = split("/", str_replace(BASE_URL, "", $url));
$controller = isset($split_arr[0]) && !empty($split_arr[0])?$split_arr[0]:'index';
$action = isset($split_arr[1]) && !empty($split_arr[1])?$split_arr[1]:'index';
  
$_REQUEST['params'] = $split_arr;
$_REQUEST['controller'] = $controller;
$_REQUEST['action'] = $action;

include_once('controller.php');
$obj_controller = new Controller($controller, $action);
