<?php


namespace app\controller;


use app\lib\Controller;
use app\lib\WorkError;

class Common extends Controller
{

    /**
     * @var \swoole_http_request
     */
    public $request;
    /**
     * @var \swoole_http_response
     */
    public $response;

    public function __construct(\swoole_http_request $req, \swoole_http_response $res)
    {
        $this->request = $req;
        $this->response = $res;
        $this->Init();
    }

    /**
     * init something
     */
    public function Init()
    {
    }

    /**
     * 默认action
     */
    public function actionIndex()
    {
        $this->response->end("defaultIndex");
    }

    /**
     * 获取get参数
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->_getParam($name, 'get');
    }

    /**
     * 获取post参数
     * @param $name
     * @return mixed
     */
    public function post($name)
    {
        return $this->_getParam($name, 'post');
    }

    /**
     * @param $name
     * @param $method
     * @return mixed
     */
    private function _getParam($name, $method)
    {
        if (empty($this->request->$method[$name])) {
            $this->request->$method[$name] = null;
        }
        return $this->request->$method[$name];
    }

    /**
     * @param string $msg
     * @param int $code
     * @throws WorkError
     */
    public function error($msg = '',$code = 404)
    {
        throw new WorkError(json_encode(
            [
                'error'=>2,
                'msg'=>$msg,
                'data'=>[]
            ]
        ), $code);
    }

    public function sendMsg($msg, $data)
    {
        $this->response->end(json_encode(
            [
                'error'=>0,
                'msg'=>$msg,
                'data'=>$data
            ]
        ));
    }
}