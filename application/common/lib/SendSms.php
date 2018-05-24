<?php
/**
 * Created by PhpStorm.
 * User: wlz
 * Date: 18-5-23
 * Time: 下午10:42
 */
namespace app\common\lib;
use think\Db;
use think\Exception;

class SendSms
{
    public static function sendSmsCode($mobile,$code,$type){
        $data = [
            'mobile'=>$mobile,
            'code'=>$code,
            'type'=>$type,
            'status'=>1,
            'ctime'=>time()
        ];
        $res =  Db::table('swoole_mobile_code')->insert($data);
        sleep(5);//模拟短信延迟
        return $res;
    }


}