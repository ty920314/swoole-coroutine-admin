<?php


namespace app\controller;


class Test extends Common
{
    public function actionIest()
    {
        $this->response->end($this->get('target_a'));
    }

    public function actionD()
    {
        $this->response->end($this->get('target_a'));
    }
}