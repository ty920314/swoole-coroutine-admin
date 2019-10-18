<?php


namespace app\lib;


abstract class Controller
{
    abstract public function actionIndex();
    abstract public function error();
}