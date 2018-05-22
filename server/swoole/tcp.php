<?php
/**
 * Created by PhpStorm.
 * User: wlz
 * Date: 18-5-7
 * Time: 下午9:33
 */
//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("127.0.0.1", 9501);

$serv->set(array(
    'worker_num' => 4,    //worker process num
    'max_request' => 100,
    'dispatch_mode' => 2,
));

/**
 * 监听连接进入事件.$fd是客户端连接唯一标识
 * $reactor_id 官网文档上不是很详细，其实还有参数3$reactor_id：线程id
 */
$serv->on('connect', function ($serv, $fd) {
    echo "Client: Connect,fd=".$fd."\n";
});

/**
 * 监听数据接收事件
 * $fd 客户端标识
 * $reactor_id 线程Id,官网文档上是用$from_id
 */
$serv->on('receive', function ($serv, $fd, $reactor_id, $data) {
    $serv->send($fd, "Client: Connect,fd=".$fd."-reactor_id=".$reactor_id."-Server: ".$data);
});

//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start();
