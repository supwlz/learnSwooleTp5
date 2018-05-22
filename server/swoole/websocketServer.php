<?php
/**
 * Created by PhpStorm.
 * User: wlz
 * Date: 18-5-8
 * Time: 下午10:45
 */

class websocketServer
{
    const host = '127.0.0.1';
    const port = 9056;
    public $websocket = null;

    public function __construct()
    {
        $this->websocket = new swoole_websocket_server(self::host,self::port);
        //通过websocketserver加载静态文件
//        $this->websocket->set(
//            [
//                'enable_static_handler'=>true, //
//                'document_root'=>'/home/wlz/www/learnSwoole/static',//静态资源默认存放目录
//            ]
//        );
        $this->websocket->set(
            [
                'task_worker_num'=>1,
            ]
        );
        $this->websocket->on('open',[$this,'onOpen']);
        $this->websocket->on('message',[$this,'onMessage']);
        $this->websocket->on('close',[$this,'onClose']);
        $this->websocket->on('task',[$this,'onTask']);//绑定task任务
        $this->websocket->on('finish',[$this,'onFinish']);//绑定finish回调
        $this->websocket->start();
    }

    public  function onOpen($ws, $request) {
        var_dump($request->fd, $request->get, $request->server);
        $ws->push($request->fd, "hello, welcome websocket server\n");
    }

    public  function onMessage($ws, $frame) {
        echo "Message: {$frame->data}\n";
        //task任务
        if($frame->data=='send_task'){
            $ws->task('send task ok');
        }
        /** 定时器 start*/
        if($frame->data =='time_after'){
            //执行一次的定时任务，两种方式均可
            $ws->after(5000,function ()use ($ws, $frame){
                echo "after 5s "."\n";
                $ws->push($frame->fd, "server server->after: {$frame->data}");
            });
            //3000ms后执行此函数
            swoole_timer_after(3000, function ()use ($ws, $frame) {
                echo "swoole_timer_after 3s "."\n";
                $ws->push($frame->fd, "swoole_timer_after: {$frame->data}");
            });
            //重复执行定时任务，两种方式均可,tick 方式会有timer_id回传参数
            $ws->tick(6000,function ($timer_id)use ($ws, $frame){
                echo "tick 6s timer_id:".$timer_id."\n";
                $ws->push($frame->fd, "server server->tick: {$frame->data}");
            });
            //3000ms后执行此函数
            swoole_timer_tick(10000, function ($timer_id)use ($ws, $frame) {
                echo "swoole_timer_tick 10s timer_id:".$timer_id."\n";
                $ws->push($frame->fd, "swoole_timer_tick: {$frame->data}");
            });
        }
        /** 定时器 end */


        $ws->push($frame->fd, "server: {$frame->data}");
    }

    public  function onClose($ws, $fd) {
        echo "client-{$fd} is closed\n";
    }

    public function onTask($serv, $task_id,$src_worker_id,$data){
        echo 'this is onTask:task_id = '.$task_id.',src_worker_id='.$src_worker_id.', data='.$data."\n";
        sleep(10);
        return $data;
    }

    public function onFinish($serv, $task_id, $data){
        echo 'this is onFinish:data='.$data."\n";
    }


}

$obj = new websocketServer();