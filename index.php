<?php
// 只允许CLI模式运行
use app\server;

if (php_sapi_name() != 'cli') die('You must use the CLI.');

// 初始化
if (extension_loaded('swoole')) {
    require_once "./vendor/autoload.php";
    defined("BASE_PATH") or define("BASE_PATH", dirname(__FILE__));
    $config = require_once("./config.php");
    $app = new server($config);
    $app->run();
} else {
    die('Not swoole extension.');
}


