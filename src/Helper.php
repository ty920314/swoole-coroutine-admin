<?php

use Co\System;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

function logger()
{
    $logger = new Logger('my_logger');
    try {
        $stream = new StreamHandler(BASE_PATH . '/log/ww.log', Logger::DEBUG);
        $logger->pushHandler($stream);
        return $logger;
    } catch (Exception $e) {
        return false;
    }

}

function showInfo($msg)
{
    echo $msg . PHP_EOL;
}

/**
 * 获取文件内容
 * @param $filePath
 * @return string
 */
function getFileContent($filePath)
{
    $content = '';
    if (!is_file($filePath)) {
        return '';
    }
    $fp = fopen($filePath, 'r');
    defer(function () use ($fp) {
        fclose($fp);
    });
    while ($tmp = System::fgets($fp)) {
        $content .= $tmp;
    }
    return $content;
}