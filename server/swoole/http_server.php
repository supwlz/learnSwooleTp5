<?php
/**
 * Created by PhpStorm.
 * User: wlz
 * Date: 18-5-8
 * Time: 下午8:54
 */

//swoole_http_server 基于swoole_server
//可以做简单的web服务器
$http = new swoole_http_server("127.0.0.1",9053);

$http->set(
    [
        'enable_static_handler'=>true, //
        'document_root'=>'/home/wlz/www/learnSwooleTp5/public/static',//静态资源默认存放目录
        'work_num'=>5
    ]
);

$http->on('WorkerStart',function ($serv, $worker_id){
    // 定义应用目录
    define('APP_PATH', __DIR__ . '/../../application/');
    // ThinkPHP 引导文件
    // 加载基础文件
    require __DIR__ . '/../../thinkphp/base.php';
});
$http->on('request',function ($request,$response){
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

    }
echo request()->action().PHP_EOL;
    $content=ob_get_contents();
    ob_end_clean();
    $response->header('content-type','text/html;charset=utf-8');
    $response->end($content);

});
$http->start();


