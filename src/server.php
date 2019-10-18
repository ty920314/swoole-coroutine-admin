<?php

namespace app;


use app\lib\WorkError;
use Swoole\Coroutine;

final class server
{
    /**
     * @var \Co\Http\Server
     */
    private $server = null;
    private $config;

    /**
     * 动态创建一个协程服务器
     * server constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = &$config;

        go(function () {
            $this->server = new \Co\Http\Server($this->config['host'], $this->config['port']);
            $this->server->set([
                'log_file'=>BASE_PATH.'/log/swoole.log',
                'log_level' => SWOOLE_LOG_TRACE,
                'trace_flags' => SWOOLE_TRACE_ALL,
            ]);
        });
    }

    private static function getController($controller, &$req, &$res)
    {
        return new $controller($req, $res);
    }

    public function run()
    {
        go(function () {
            //循环配置路由
            foreach ($this->config['route'] as $route => $resp) {
                //忽略大小写
                $route = strtolower($route);
                $resp = strtolower($resp);
                //动态配置路由callback
                go(function ()use ($route){
                    $this->server->handle($route, function (\swoole_http_request $req, \swoole_http_response $res) {
                        //记录日志
                        \logger()->info(sprintf("method:%s uri:%s cid:%s ", $req->server['request_method'], $req->server['request_uri'], \Co::getCid()));
                        //parse uri
                        $uri = $this->getParam($req->server['path_info']);
                        $method = $this->config['defaultAction'];
                        try {
                            $reflection = new \ReflectionClass("\\app\\controller\\" . ucfirst($uri[1]));
                            $method = $this->getAction($req);
                            try {
                                $reflection->getMethod('action' . ucfirst($method));
                            } catch (\ReflectionException $am) {
                                empty($errMsg) && $errMsg = sprintf("action %s does not exist", $method);
                                $method = $this->config['defaultAction'];
                            }

                        } catch (\ReflectionException $e) {
                            empty($errMsg) && $errMsg = sprintf("module %s does not exist", $uri[1]);
                        }
                        if (!empty($errMsg)) {
                            $res->end($errMsg);
                        } else {
                            $controller = self::getController("\\app\\controller\\" . ucfirst($uri[1]), $req, $res);
                            try{
                                call_user_func([$controller, 'action' . ucfirst($method)]);
                            }catch (WorkError $e){
                                $res->end($e->getMessage());
                            }

                        }
                    });
                });

            }
            \showInfo('|-------------------------------------------|');
            \showInfo('|--------------Server Start-----------------|');
            \showInfo('|-------------------------------------------|');

            $this->server->start();
        });
    }

    /**
     * 解析路由
     * @param $pathInfo
     * @return array
     */
    private function getParam($pathInfo)
    {
        // 拆分模块为数组
        $pathInfo = explode('/', $pathInfo);
        empty($pathInfo[1]) && $pathInfo[1] = $this->config['defaultController'];
        return $pathInfo;
    }

    /**
     * 获取action方法
     * @param \swoole_http_request $req
     * @return string
     */
    private function getAction(\swoole_http_request &$req)
    {
        empty($req->get['target_a']) && $req->get['target_a'] = $this->config['defaultAction'];
        return $req->get['target_a'];
    }


}