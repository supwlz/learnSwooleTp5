<?php
/**
 * Created by PhpStorm.
 * User: wlz
 * Date: 18-5-23
 * Time: 下午10:08
 */

class HttpServer
{

    const HOST = '127.0.0.1';
    const PORT = 9053;
    public $http_server = null;

    public function __construct()
    {
        $this->http_server = new swoole_http_server(self::HOST,self::PORT);

        $this->http_server->set(
            [
                'enable_static_handler'=>true, //
                'document_root'=>'/home/wlz/www/learnSwooleTp5/public/static',//静态资源默认存放目录
                'work_num'=>5,
                'task_work_num'=>5
            ]);

        $this->http_server->on('workerstart',[$this,'onWorkerStart']);
        $this->http_server->on('request',[$this,'onRequest']);
        $this->http_server->on('task',[$this,'onTask']);//绑定task任务
        $this->http_server->on('finish',[$this,'onFinish']);//绑定finish回调
//        $this->http_server->on('close',[$this,'onClose']);

        $this->http_server->start();
    }

    public function onWorkerStart($serv, $worker_id){
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../../application/');
        // ThinkPHP 引导文件
        // 加载基础文件
        require __DIR__ . '/../../thinkphp/base.php';
    }

    public function onRequest($request,$response){
        $_SERVER = [];
        $_REQUEST = [];
        $_GET = [];
        $_POST = [];
        $_COOKIE = [];
        if(isset($request->server)){
            foreach ($request->server as $k=>$v){
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        if(isset($request->header)){
            foreach ($request->header as $k=>$v){
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        if(isset($request->get)){
            foreach ($request->get as $k=>$v){
                $_REQUEST[$k] = $_GET[$k]=$v;
            }
        }
        if(isset($request->post)){
            foreach ($request->post as $k=>$v){
                $_REQUEST[$k] = $_POST[$k]=$v;
            }
        }
        if(isset($request->cookie)){
            foreach ($request->cookie as $k=>$v){
                $_COOKIE[$k] =$v;
            }
        }
        // 执行应用并响应
        ob_start();
        try{

            think\Container::get('app', [defined('APP_PATH') ? APP_PATH : ''])
                ->run()
                ->send();
        }catch (Exception $e){
            echo date('Y-m-d H:i:s').PHP_EOL;
            echo $e->getMessage().PHP_EOL;
            addErrorLog($e->getMessage(),'onrequest');
        }
        $content=ob_get_contents();
        ob_end_clean();
        $response->header('content-type','text/html;charset=utf-8');
        $response->end($content);
    }


    public function onTask($serv, $task_id,$src_worker_id,$data){
        echo 'this is onTask:task_id = '.$task_id.',src_worker_id='.$src_worker_id.', data='.$data."\n";
        sleep(10);
        return $data;
    }

    public function onFinish($serv, $task_id, $data){
        echo 'this is onFinish:data='.$data."\n";
    }
//    public  function onClose($ws, $fd) {
//        echo "client-{$fd} is closed\n";
//    }

}
$obj = new HttpServer();