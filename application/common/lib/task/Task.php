<?php
/**
 * Created by PhpStorm.
 * User: Myron
 * Date: 2018/5/24
 * Time: 16:49
 */

namespace app\common\lib\task;
use app\common\lib\SendSms;

class Task
{

    public function sendSms($data){
        $obj = new SendSms;
        return $obj ->sendSmsCode($data['mobile'],$data['code'],$data['type']);
    }
}