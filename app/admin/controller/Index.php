<?php
	namespace app\admin\controller;
    use think\Controller;
    use think\Request;
    use think\Cookie;
    use app\common\model\Order;
    use app\common\model\Goods;
    use think\Db;
	use app\common\controller\Index as commonIndex;

	class Index extends Controller
	{
		public function index()
		{
			return view('/index');
		}

		public function test()
        {
//            $data = Db::name('Order')->paginate(2);
//// 把分页数据赋值给模板变量list
//            $this->assign('list', $data);
//// 渲染模板输出
//            //return $this->fetch();
//            return view('/test');
            $file = request()->file('image');
        }

        public function goodmanage()
        {
            //$g=new Goods;
            $kw = input('kw');
            if(!empty($kw))//有kw
            {
                $res=Db::table("Goods")->where('gname','like',$kw)->select();
            }else
            {
                $res=Db::table("Goods")->select();
            }
            $this->assign("data", $res);
            $this->assign("empty", '<span>未找到相关商品</span>');
            return view('/showgood');
        }

		public function addgood()
        {
            if ($this->request->method() == 'POST')
            {
                $gname = html_entity_decode($_POST['gname']);
                $gprice = html_entity_decode($_POST['gprice']);
                $gcount = (int)html_entity_decode($_POST['gcount']);
                //reg_match("/[^a-zA-Z0-9]{1}/",$username);
                if (!is_numeric($gprice) or !is_int($gcount) or $gprice <= 0 or $gcount <= 0) {
                    echo '{"errno":1,"msg":"价格和库存格式填写错误"}';
                    //echo !is_numeric($gprice);
                    echo !is_int($gcount);
                    dump($gcount);
                    //echo $gcount;
                    exit;
                }
                // 获取表单上传文件 例如上传了001.jpg
                $file = request()->file('image');
                // 移动到框架应用根目录/public/uploads/ 目录下
                if ($file) {
                    $info = $file->validate(['ext'=>'jpg,png,gif,jpeg'])->rule('uniqid')->move(ROOT_PATH . 'public/uploads/img/', true, false);
                    //$info = $file->rule('md5')->move(ROOT_PATH . 'public/uploads/img/','',false);
                    if ($info) {
                        // 成功上传后 获取上传信息
                        // 输出 jpg
//                    echo "输出jpg：".$info->getExtension()."\n";
//                    // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
//                    echo "输出日期+文件名:".$info->getSaveName()."\n";
//                    // 输出 42a79759f284b767dfcb2a0197904287.jpg
                        $gimgurl = '/uploads/img/' . $info->getFilename();
                        $data=[
                            'gname'=>$gname,
                            'gprice'=>$gprice,
                            'gcount'=>$gcount,
                            'gimg'=>$gimgurl
                        ];
                        $res=Db::name('Goods')->insert($data);
                        if(!$res){
                            echo "错误";
                        }
                    } else {
                        // 上传失败获取错误信息
                        echo $file->getError();
                    }
                }
            }
            return view('/addgood');
        }

        public function addgoodupload()
        {
            $file = request()->file('image');
            $gname = html_entity_decode($_POST['gname']);
            $gprice = html_entity_decode($_POST['gprice']);
            $gcount = (int)html_entity_decode($_POST['gcount']);
            //reg_match("/[^a-zA-Z0-9]{1}/",$username);
            if (!is_numeric($gprice) or !is_int($gcount) or $gprice <= 0 or $gcount <= 0) {
                echo '{"errno":1,"msg":"价格和库存格式填写错误"}';
                //echo !is_numeric($gprice);
                echo !is_int($gcount);
                dump($gcount);
                //echo $gcount;
                exit;
            }
            // 获取表单上传文件 例如上传了001.jpg

            // 移动到框架应用根目录/public/uploads/ 目录下
            echo "$file";
            dump($file);
            if ($file) {
                $info = $file->move(ROOT_PATH . 'public/uploads/img/', '', false);
                if ($info) {
                    $gimgurl = '/uploads/img/' . $info->getFilename();
                    echo "$gimgurl";
                    $data=[
                        'gname'=>$gname,
                        'gprice'=>$gprice,
                        'gcount'=>$gcount,
                        'gimg'=>$gimgurl
                    ];
                    dump($data);
                    $res=Db::name('Goods')->insert($data);
                    echo "$res";
                    dump($res);
                    if(!$res){
                        echo "错误";
                    }
                } else {
                    // 上传失败获取错误信息
                    echo $file->getError();
                }
            }
        }

        public function changegoodnum()
        {
            $post_data=file_get_contents("php://input");
            $json_data=json_decode($post_data,true);
            $gid=$json_data["gid"];
            $num=$json_data["num"];
            if(empty($gid) or empty($num) or !is_int($gid) or !is_int($num) or $num<=0 or $gid<=0){
                echo '{"msg":"更改失败，请检查商品数量"}';
                exit;
            }
            Db::name('Goods')->where(['gid'=>$gid])->setField('gcount',$num);
        }

        public function changegoodname()
        {
            $post_data=file_get_contents("php://input");
            $json_data=json_decode($post_data,true);
            $gid=$json_data["gid"];
            $name=$json_data["name"];
            if(empty($gid) or empty($name) or !is_int($gid) or $gid<=0){
                echo '{"msg":"更改失败，请检查商品名"}';
                exit;
            }
            Db::name('Goods')->where(['gid'=>$gid])->setField('gname',html_entity_decode($name));
        }

        public function changegoodprice()
        {
            $post_data=file_get_contents("php://input");
            $json_data=json_decode($post_data,true);
            $gid=$json_data["gid"];
            $gprice=$json_data["price"];
            if(empty($gid) or !is_int($gid) or $gid<=0 or $gprice<=0 or !is_numeric($gprice)){
                echo '{"msg":"更改失败，请检查商品价格","a":".$gprice."}';
                exit;
            }
            Db::name('Goods')->where(['gid'=>$gid])->setField('gprice',$gprice);
        }

        public function deletegood()
        {
            $post_data=file_get_contents("php://input");
            $json_data=json_decode($post_data,true);
            $gid=$json_data["gid"];

            if(empty($gid) or !is_int($gid) or $gid<=0){
                echo '{"errno":1,"msg":"删除失败，请检查商品和数量"}';
                exit;
            }
            Db::name('Goods')->where(['gid'=>$gid])->delete();
            echo '{"errno":0,"msg":"删除商品成功！"}';
        }
        public function findgood()
        {

        }

        public function ordermanage()
        {
            $o=new Order;
            $list = Order::paginate(3);
// 获取分页显示
            $page = $list->render();
// 模板变量赋值
            $this->assign('data', $list);
            $this->assign('page', $page);
// 渲染模板输出
            //return $this->fetch();
            //$data = Db::name('Order')->where('uid',$uid)->select();
            //$data = $o->paginate(2);
            //$data = $o->paginate(2);
            // 把分页数据赋值给模板变量list
            //$this->assign('list', $list);
            //$this->assign("data", $data);
            return view('/showorder');
        }
        public function showorder1()
        {
            $o = new Order;
            //$data = Db::name('Order')->where(['uid' => $uid, 'status' => 1])->select();
            //$data = $o->where(['status'=>1])->select()->toArray();
            $list = Order::where(['status'=>1])->paginate(3);
// 获取分页显示
            $page = $list->render();
            $this->assign("data", $list);
            $this->assign("page", $page);
            $this->assign('empty', '<span class="empty">没有数据</span>');
            return view('/showorder1');
        }
        public function showorder3()
        {
            $o = new Order;
            //$data = Db::name('Order')->where(['uid' => $uid, 'status' => 1])->select();
            //$data = $o->where(['status'=>3])->select()->toArray();
            //$this->assign("data", $data);
            $list = Order::where(['status'=>3])->paginate(3);
// 获取分页显示
            $page = $list->render();
            $this->assign("data", $list);
            $this->assign("page", $page);
            $this->assign('empty', '<span class="empty">没有数据</span>');
            return view('/showorder3');
        }
        public function showorder4()
        {
            $o = new Order;
            //$data = Db::name('Order')->where(['uid' => $uid, 'status' => 1])->select();
//            $data = $o->where(['status'=>4])->select()->toArray();
//            $this->assign("data", $data);
            $list = Order::where(['status'=>4])->paginate(3);
// 获取分页显示
            $page = $list->render();
            $this->assign("data", $list);
            $this->assign("page", $page);
            $this->assign('empty', '<span class="empty">没有数据</span>');
            return view('/showorder4');
        }
        public function getItem($id)
        {
//        $o=new Order;
//        $data = $o->where(['oid'=>$id])->find();
            $data=Db::name('OrderGoods')->where(['oid'=>$id])->select();
            $this->assign("data", $data);
            return view('/getitem');
        }

        public function sendorder()
        {
            $post_data=file_get_contents("php://input");
            $json_data=json_decode($post_data,true);
            $oid=$json_data['oid'];
            $oldstatus=Db::name("Order")->where(['oid'=>$oid])->value('status');
//            if($oldstatus>3){
//                echo '{"msg":"订单状态更改失败","errno":1}';
//                exit;
//            }
            Db::name("Order")->where(['oid'=>$oid])->setField(['status'=>2]);
            echo '{"msg":"订单状态更改成功","errno":0}';
        }

        public function changepwd()
        {
            return view('/changepwd');
        }

	}