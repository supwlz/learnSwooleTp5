<?php
namespace app\index\controller;
use app\common\base\controller\Base;
use think\Db;
use app\common\lib\redis\Predis;
class Index extends Base
{
    public function index()
    {
        return '';
    }

    public function hello($name = 'ThinkPHP5')
    {
        $this->wlzErrorLog($name,'test');
        return 'hello,' . $name.'redis:'.$this->predis()->get('wlz_test');
    }

    public  function loginSendSms(){dump($_REQUEST);
        $mobile =$_REQUEST['mobile'];
        $type='login';
        return $this->sendSms($mobile,$type);
    }
    public function login(){
        $mobile =$_REQUEST['mobile'];
        $code = $_REQUEST['code'];
        $code_info = Db::name('mobile_code')
            ->where('status',1)
            ->where('mobile',$mobile)
            ->where('type','login')
            ->find();
        if(empty($code_info))
            return $this->error( '请先发送验证码');
        Db::name('mobile_code')->where('id', $code_info['id'])->update(['status' => 0]);
        if($code!=$code_info['code'])
            return $this->error( '验证码错误');
        $data  =array(
            'is_login'=>1,
            'login_time'=>date('Y-m-d H:i:s')
        );
        $this->predis()->set(Predis::USER_IS_LOGIN_PRE.$mobile,$data,600);
        return $this->success('登录成功');
    }


}
