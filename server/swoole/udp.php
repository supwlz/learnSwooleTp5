<?php
/**
 * Created by PhpStorm.
 * User: wlz
 * Date: 18-5-7
 * Time: 下午10:43
 */

//创建Server对象，监听 127.0.0.1:9502端口
$serv = new swoole_server("127.0.0.1", 9502,SWOOLE_PROCESS,SWOOLE_SOCK_UDP);


$serv->set(array(
    'worker_num' => 4,    //worker process num
    'max_request' => 100,
));

/*
 * UDP服务器与TCP服务器不同，UDP没有连接的概念。启动Server后，
 * 客户端无需Connect，直接可以向Server监听的9502端口发送数据包。对应的事件为onPacket。
 *
 */
//
/**监听数据接收事件
 * $clientInfo是客户端的相关信息，是一个数组，有客户端的IP和端口等内容调用 $server->sendto 方法向客户端发送数据
 */
$serv->on('Packet', function ($serv, $data, $clientInfo) {
    $serv->sendto($clientInfo['address'], $clientInfo['port'], "Server ".$data);
    var_dump($clientInfo);
});




//启动服务器
$serv->start();
