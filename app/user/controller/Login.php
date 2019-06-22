<?php

	namespace app\user\controller;
	use think\Controller;
	use think\Request;
	use think\Cookie;
	use app\user\model\User;
	use app\index\controller\Index;
	use think\Db;
	use think\captcha;


	/**
	 * 
	 */
	class Login extends Controller
	{

		public function index()
		{
			if(!isset($_COOKIE['userinfo'])||!isset($_COOKIE['token']))//没有token，出于未登录状态
			{
				exit("<script>location.href='login.php';</script>");
			}
			$token=$_COOKIE['token'];

		}
		

		public function refresh_member()
		{
			//先查找数据库相关的数据，再生成token
			//$res=User::where("username","=",$username);
			//cookie(token)=

		}
		//username和password的格式检查，这一块代码已经报废，具体的验证方法为tp5自带的验证器方法，例子为下面的login方法
		public function unpwcheck($username,$password)
		{
			if (!$username || !$password) 
			{
				exit("<script>alert('用户名和密码不能为空！');location='javascript:history.back()';</script>");
				if (!preg_match("/[A-Za-z0-9]{6,12}/i", trim($username))) 
				{
					exit("<script>alert('用户名请使用不包含特殊字符、6-12长度的字符串！');location='javascript:history.back()';</script>");
				}
				if (!preg_match("/[A-Za-z0-9]{6,12}/i", trim($password))) 
				{
					exit("<script>alert('密码请使用不包含特殊字符、6-12长度的字符串！');location='javascript:history.back()';</script>");
				}
			}
			return true;
		}

        public function show_captcha(){
            $captcha = new \think\captcha\Captcha();
            $captcha->imageW=121;
            $captcha->imageH = 32;  //图片高
            $captcha->fontSize =14;  //字体大小
            $captcha->length   = 4;  //字符数
            $captcha->fontttf = '5.ttf';  //字体
            $captcha->expire = 30;  //有效期
            $captcha->useNoise = false;  //不添加杂点
            return $captcha->entry();
        }
        public function captcha_post(){
            $code=input('post.captcha');
            $captcha = new \think\captcha\Captcha();
            $result=$captcha->check($code);
            if($result===false){
                echo '验证码错误';exit;
            }
        }
        public function logintest()
        {
                //获取到传入的username和password
                $username = input('username');
                $password = input('password');
                $USER = User::where('username', $username)->find();
                if (!$USER) {
                    $this->error('用户不存在');
                }
                if (md5($password . 'login') != $USER['password']) {
                    $this->error('密码不正确');
                }
                User::where('username', $username)->setField("login_time",time());
                $auth = md5($USER['password'] . $USER['login_time']);
                dump($USER);
                dump($USER['login_time']);
                Cookie::set('username', $username, ['expire' => 365 * 86400]);
                Cookie::set('auth', $auth, ['expire' => 365 * 86400]);
                //$this->success('登录成功','Index/index/info');
                $this->success('登录成功', 'Index/index/index');
                exit;
        }
		public function login()
        {
            if ($this->request->method() == 'POST') {
                //获取到传入的username和password
                $username = input('username');
                $password = input('password');
                $code=input('post.captcha');
                $captcha = new \think\captcha\Captcha();
                $result=$captcha->check($code);
                if($result===false){
                    //echo '{"errno":1,"msg":"验证码错误"}';
                    //echo '<script>alert("验证码错误")</script>';
                    $this->error("验证码错误");
                    exit;
                }
                $result = $this->validate(input(), [
                    'username' => 'require|length:4,6',//这里对传入赋值的username和password进行格式验证，详情参考tp5开发文档的验证-验证器
                    'password' => 'require|length:4,6',
                ]);
                if (true !== $result) {
                    $this->error($result);
                }
                User::where('username', $username)->setField("login_time",time());
                $USER = User::where('username', $username)->find();
                if (!$USER) {
                    $this->error('用户不存在');
                }
                if (md5($password . 'login') != $USER['password']) {
                    $this->error('密码不正确');
                }

                $auth = md5($USER['password'] . $USER['login_time']);
                Cookie::set('username', $username, ['expire' => 365 * 86400,'httponly' => true]);
                Cookie::set('auth', $auth, ['expire' => 365 * 86400,'httponly' => true]);
                //$this->success('登录成功','Index/index/info');
                $this->success('登录成功', 'Index/index/index');
                exit;
            }
            return view('index/login');
        }
			//下方代码报废，上方的exit已跳过下方unpwcheck代码
//			if($this->unpwcheck($username,$password)){
//				//验证用户名是否已注册
//				$res=User::where("username",$username);
//				if (!$res) {//用户不存在
//					exit("<script>alert('用户不存在，请先注册！');location.href='register.html';</script>");
//				}
//				//验证用户名密码是否符合
//
//				$res=User::get(function($query)use($username,$password){
//					$query->where("username",$username)
//						->where("password",$password);
//				});
//				if($res)
//				{
//					refresh_member();
//				}
//				else
//				{
//					exit("<script>alert('密码错误！');location.href='login.html';</script>");
//				}
//			}
//			//$indexController = new Index;
//			//return $indexController->index();
//
//
//		}

        //此函数作废
		public function reg()
		{
			$username=input('post.username');
			$password=input('post.password');

			if($this->unpwcheck($username,$password))
			{
				//验证完用户名、密码输入合法，接下来验证用户名是否已被注册
				$res=User::where("username",$username)
					->find();
				//从数据库中查找用户名
				if($res)//如果返回为1，则表明用户已存在
				{
					exit("<script>alert('该用户名已被注册！');location.href='register.html';</script>");
				}
				else
				{
					User::create([
						'username'=>$username,
						'password'=>$password,
					]);
					exit("<script>alert('注册成功！请登录');location.href='login';</script>");
				}
			}
		}

		public function register()
		{
			if($this->request->method() =='POST'){

				$username=input('post.username');//''
				$password=input('post.password');
				$result = $this->validate(input(),[
					'username' => 'require|length:4,6',
					'password' => 'require|length:4,6',
				]);
				if(true !== $result){
					$this->error($result);
					exit;
				}

				    //验证输入合法
                    $uc=preg_match("/[^a-zA-Z0-9]{1}/",$username);
                    $pc=preg_match("/[^a-zA-Z0-9]{1}/",$password);
                    if($uc and $pc){
                        $this->error("请不要输入除数字、字母外的字符！");
                        exit;
                    }

                    //验证完用户名、密码输入合法，接下来验证用户名是否已被注册
					$res=User::where("username",$username)
						->find();
					//从数据库中查找用户名
					if($res)//如果返回为1，则表明用户已存在
					{
						$this->error('用户已存在');
					}
					else
					{
                        $code=input('post.captcha');
                        $captcha = new \think\captcha\Captcha();
                        $result=$captcha->check($code);
                        if($result===false){
                            //echo '{"errno":1,"msg":"验证码错误"}';
                            //echo '<script>alert("验证码错误")</script>';
                            $this->error("验证码错误");
                            exit;
                        }
						User::create([
							'username'=>$username,
							'password'=>$password,
						]);
						$this->success('注册成功！请登录','user/login/login');
					}

				exit;
			}
			return view('index/register');
		}


		public function usernameexist()
        {
            $post_data=file_get_contents("php://input");
            $json_data=json_decode($post_data,true);
            $username=$json_data['username'];
            $res=User::where("username",$username)
                ->find();
            //从数据库中查找用户名
            if($res)//如果返回为1，则表明用户已存在
            {
                echo '{"errno":1,"msg":"用户名已存在"}';
            }
            else
            {
                echo '{"errno":0,"msg":"用户名可注册"}';
            }
        }

	}
?>