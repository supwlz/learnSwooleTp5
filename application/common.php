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