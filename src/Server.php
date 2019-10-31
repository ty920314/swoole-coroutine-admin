<?php


namespace app;

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server\Task;

class Server
{
    public $mater_pid = 0;
    public $manager_pid = 0;

    public function __construct()
    {

    }

    public function run()
    {
        $http = new \swoole_http_server("127.0.0.1", 9501, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
        $http->set([
            'work_num' => 2,
            'task_worker_num' => 10,
            'task_enable_coroutine' => true,
            'log_file' => BASE_PATH . '/log/swoole.log',
//            'daemonize'=>1
        ]);
        $http->on('ManagerStart', [$this, 'onManagerStart']);
        $http->on('WorkerStart', [$this, 'onWorkerStart']);
        $http->on('Start', [$this, 'onStart']);
        $http->on('Task', [$this, 'onTask']);
        $http->on('Finish', [$this, 'onFinish']);
        $http->on('Request', [$this, 'onRequest']);
        $http->start();
    }

    /**
     * @param \swoole_server $server
     */
    public function onStart(\swoole_server $server)
    {
        $this->mater_pid = $server->master_pid;
        $this->manager_pid = $server->manager_pid;
        \showInfo("onStart--" . PHP_EOL . $this->mater_pid . PHP_EOL . $this->manager_pid);
//        $server->task("");
    }

    public function onManagerStart(\swoole_server $server)
    {
        \showInfo("onManagerStart--");

    }

    public function onWorkerStart(\swoole_server $server)
    {
//        print_r(get_included_files());//此数组中的文件表示进程启动前就加载了，所以无法reload
    }

    public function onTask(\swoole_server $serv, Task $task)
    {

    }

    public function onFinish(\swoole_server $serv, int $task_id, string $data)
    {

    }


    public function onRequest(Request $request, Response $response)
    {
        go(function () use ($request, $response) {
            $uri = \getParam($request->server['request_uri']);
            try {
                $reflection = new \ReflectionClass("\\app\\controller\\" . ucfirst($uri[1]));
                $method = $uri[2];
                $hasMethod = $reflection->getMethod('action' . ucfirst($method));
                if ($hasMethod) {
                    $response->end(call_user_func([self::getController($reflection->getName(), $request, $response), 'action' . ucfirst($method)]));
                } else {
                    $response->end(sprintf("action method %s does not exist", $uri[1]));
                }
            } catch (\ReflectionException $e) {
                $response->end(sprintf("module %s does not exist", $uri[1]));
            }
        });

    }

    /**
     * create a new Controller class
     * @param $controller
     * @param $req
     * @param $res
     * @return mixed
     */
    private static function getController($controller, $req, $res)
    {
        return new $controller($req, $res);
    }

}