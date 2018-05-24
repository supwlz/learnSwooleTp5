<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

    function addErrorLog($message,$type=''){
        if(is_array($message))
            $message = json_encode($message);
        $data = [
            'type'=>$type,
            'message'=>$message,
            'ctime'=>time()
        ];
       return  think\Db::table('swoole_error_log')->insert($data);
    }


    function jreturn($status,$msg,$data){
        $res = array(
            'status'=>$status,
            'message'=>$msg,
            'data'=>$data
        );
        echo json_encode($res);
        return true;
    }
    function success($msg,$data=[]){
        echo jreturn(1,$msg,$data);
        return true;
    }
    function error($msg,$data=[]){
        echo jreturn(0,$msg,$data);
        return true;
    }

    function sendSms($mobile,$type,$is_task = 1){
        $code = round(1000,9999);
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
        return app\common\lib\SendSms::sendSmsCode($mobile,$code,$type);

    }