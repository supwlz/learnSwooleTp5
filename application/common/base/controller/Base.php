<?php
/**
 * Created by PhpStorm.
 * User: wlz
 * Date: 18-5-24
 * Time: 下午10:14
 */

namespace app\common\base\controller;
use \think\Db;
class Base
{


    public function wlzErrorLog($message,$type=''){
        if(is_array($message))
            $message = json_encode($message);
        $data = [
            'type'=>$type,
            'message'=>$message,
            'ctime'=>time()
        ];
       return  Db::name('error_log')->insert($data);
    }


    public function jreturn($status,$msg,$data){
        $res = array(
            'status'=>$status,
            'message'=>$msg,
            'data'=>$data
        );
        return json($res);
    }
    public function success($msg,$data=[]){
        return $this->jreturn(1,$msg,$data);
    }
    public function error($msg,$data=[]){
        return $this->jreturn(0,$msg,$data);
    }

    public function sendSms($mobile,$type,$is_task = 1){
        $code = mt_rand(1000,9999);
        if($is_task&& isset($_SERVER['swoole_http_server'])){
            $task_data =array(
                'method'=>'sendSms',
                'data'=>array(
                    'mobile'=>$mobile,
                    'code'=>$code,
                    'type'=>$type
                ),
            );
            return $_SERVER['swoole_http_server']->task($task_data);
        }
        return \app\common\lib\SendSms::sendSmsCode($mobile,$code,$type);

    }

    public function predis(){
        return  \app\common\lib\redis\Predis::getInstance();
    }
}