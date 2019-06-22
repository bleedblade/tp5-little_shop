<?php


namespace app\common\controller;

use app\common\model\User;
use think\Controller;
use think\Cookie;
use think\Request;

class Base extends Controller
{
    //在每次调用的页面的操作都会进行用户验证
    protected $userinfo;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);//魔术方法
        $username =  Cookie::get('username');
        $auth =  Cookie::get('auth');
        if(!$username || !$auth){//验证两个cookie是否都存在
            $this->error('请先登录','User/login/login');
        }
        //验证username为真
        $USER = User::where('username',$username)->find();
        if(!$USER){
            $this->error('用户不存在,请先注册','User/login/login');
        }
        //验证令牌为真
        if($auth != md5($USER['password'].$USER['login_time'])){
            Cookie::set('auth',null);
            $this->error('auth错误，用户登录信息失效，请重新登录','User/login/login');
        }
        $this->userinfo = $USER->toArray();
    }

    public function logout(){
        //销毁session
        cookie("username", NULL);
        cookie("auth", NULL);
        //跳转页面
        $this->redirect('Index/index/index');
    }
}