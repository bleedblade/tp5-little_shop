<?php
	namespace app\index\controller;
	use app\common\controller\Base;
    use app\common\model\User;
    use think\Controller;
	use think\Request;
	use app\index\model\Goods;
	use think\Cookie;
	//use think\Loader;
	//use app\common\controller\Index as commonIndex;
	use think\Db;

    //这里进行了更改，不继承base，而是继承Controller（为了不调用初始化魔术方法验证用户信息，可以在无登录状态访问商城首页）
	class Index extends Controller
	{

//	    public function __construct(Request $request = null)
//        {
//            parent::__construct($request);
//
//        }
//        public function __construct(Request $request = null)
//        {
//            parent::__construct($request);//魔术方法
//            $username =  Cookie::get('username');
//            $auth =  Cookie::get('auth');
//            if(!$username || !$auth){//验证两个cookie是否都存在，都不在为真
//                $this->assign("welcome","false");
//                exit;
//            }
//            //验证username为真
//            $USER = User::where('username',$username)->find();
//            if(!$USER){
//                $this->assign("welcome","false");
//                exit;
//            }
//            //验证令牌为真
//            if($auth != md5($USER['password'].'login')){
//                Cookie::set('auth',null);
//                $this->assign("welcome","false");
//                exit;
//            }
//            $this->assign("welcome","true");
//            exit;
//        }

        public function info()
        {

            return  json($this->userinfo);
            //这里的源代码已经移动到base.php中的初始化魔术方法用于验证用户登录信息，可以跳过这里了
            //http://localhost/index.php/Index/index/info.html
            //Controller->Base->Index->info
           return 'info';
		}

		public function index()
		{
            $username =  Cookie::get('username');
            $auth =  Cookie::get('auth');
            $this->assign("welcome",1);
            $this->assign("username",$username);
            if(!$username || !$auth){//验证两个cookie是否都存在，都不在为真
                $this->assign("welcome",0);
            }
            //验证username为真
            $USER = User::where('username',$username)->find();
            if(!$USER){
                $this->assign("welcome",0);

            }
            //验证令牌为真
            if($auth != md5($USER['password'].$USER['login_time'])){
                Cookie::set('auth',null);
                $this->assign("welcome",0);
            }
			$res = Goods::where('1=1')
				->select();
				$goodslist=[];
				foreach ($res as $val) {
					array_push($goodslist, $val->toArray());
				}
				//$goodslist=$val->toArray();
			//dump($goodslist);
				//注意这里的assign()方法需要继承Controller才可以
			$this->assign('goodslist',$goodslist);
			return $this->fetch();
		}

		public function logout(){
            //销毁session
            cookie("username", NULL);
            cookie("auth", NULL);
            //跳转页面
            $this->redirect('Index/index/index');
            //return app/common/base/logout();在commoin/base下写了logout方法，但是不会用，就先复制一份过来
        }

		public function modeltest()
		{

			$users = Users::get(4);
			$res = $users->delete(true);
			dump($res);
		}


		// public function setusercookie(name){
		// 	Cookie(['prefix'=>'think_', 'expire'=>3600, 'path'=>'/']);
		// 	// 单独指定当前前缀
		// 	Cookie::prefix('think_');
		// }

	// 	public function common()
	// 	{->where(["id"=>2])
	// 		$common = new commonIndex;
	// 		return $common->index();
	// 	}
	}

?>