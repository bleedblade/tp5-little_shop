<?php
    namespace app\user\controller;
    use app\common\controller\Base;
    use think\Controller;
    use think\Request;
    use think\Cookie;
    use app\user\model\User;
    use app\common\model\Order;
    use app\common\model\OrderGoods;
    use app\index\controller\Index;
    use think\Db;
    use app\common\model\Cart;
    use app\goods\controller\Goods;
    use \think\Validate;


/**
*
*/
class Usercenter extends Goods
{
    public function index()
    {
        $username = Cookie::get('username');
        $auth = Cookie::get('auth');
        $this->assign("welcome", 1);
        $this->assign("username", $username);
        $uid = $this->userinfo['Id'];
        $map = [
            'uid'=>$uid,
        ];
        $status = input('status');
        if(!empty($status) and $status<=5){
            $map['status'] = $status;
        }
        $o=new Order;
        //$data = Db::name('Order')->where('uid',$uid)->select();
        $data = $o->where($map)->select()->toArray();
        $status=NULL;
        $map=[];
        $this->assign("data", $data);
        $this->assign('empty','<span class="empty">没有数据</span>');
        return view('/index/index');
    }

    public function getItem($id)
    {
//        $o=new Order;
//        $data = $o->where(['oid'=>$id])->find();
        $data=Db::name('OrderGoods')->where(['oid'=>$id])->select();
        $this->assign("data", $data);
        return view('/index/item');
    }

    public function showallorder()
    {
        $uid = $this->userinfo['Id'];
        $o=new Oder;
        //$data = Db::name('Order')->where('uid',$uid)->select();
        $data = $o->where(['uid'=>$uid])->page(1,10)->select()->toArray();
        $this->assign("data", $data);
        $this->assign("datalength", count($data));

    }

    public function chpwd()
    {
        $username = Cookie::get('username');
        $uid = $this->userinfo['Id'];
        //$auth = Cookie::get('auth');
        $this->assign("welcome", 1);
        $this->assign("username", $username);
        if($this->request->method() =='POST') {
            $post_data = file_get_contents("php://input");
            $json_data = json_decode($post_data, true);
            $password = $json_data["password"];
            $pc = preg_match("/[^a-zA-Z0-9]{1}/", $password);
            if ($pc) {
                echo '{"errno":1,"msg":"请不要输入除数字、字母外的字符！"}';
                exit;
            }
            $pc = preg_match("/[a-zA-Z0-9]{4,6}/", $password);
            if (!$pc) {
                echo '{"errno":1,"msg":"格式错误！密码格式为4-6位数字、字母组合！"}';
                exit;
            }
            $password=md5($password.'login');
            $res=Db::name("User")->where(['id'=>$uid,'username'=>$username])->setField(['password'=>$password]);
            $USER = User::where('username', $username)->find();
            $auth = md5($password . $USER['login_time']);
            Cookie::set('auth', $auth, ['expire' => 365 * 86400,'httponly' => true]);
            echo '{"errno":0,"msg":"密码修改成功"}';
            exit;
        }
        return view('/index/chpwd');
    }
    public function changepwd()
    {
        $username = Cookie::get('username');
        $uid = $this->userinfo['Id'];
        $post_data = file_get_contents("php://input");
        $json_data = json_decode($post_data, true);
        $password = $json_data["password"];
        $pc = preg_match("/[^a-zA-Z0-9]{1}/", $password);
        if ($pc) {
            echo '{"errno":1,"msg":"请不要输入除数字、字母外的字符！"}';
            exit;
        }
        $pc = preg_match("/[a-zA-Z0-9]{4,6}/", $password);
        if (!$pc) {
            echo '{"errno":1,"msg":"格式错误！密码格式为4-6位数字、字母组合！"}';
            exit;
        }
        $password=md5($password.'login');
        $res=Db::name("User")->where(['id'=>$uid,'username'=>$username])->setField(['password'=>$password]);
        $auth = md5($password . 'login');
        Cookie::set('auth', $auth, ['expire' => 365 * 86400]);
        header('Content-Type:application/json');
        echo '{"errno":0,"msg":"密码修改成功"}';
    }

    public function showorderdetail()
    {
        $post_data=file_get_contents("php://input");
        $json_data=json_decode($post_data,true);
        $oid=$json_data['oid'];
        $uid = $this->userinfo['Id'];
        $ou=Db::table('Order')->where(['uid'=>$uid,'oid'=>$oid])->find();
        if(!$ou){
            echo '{"errno":1,"msg":"订单错误"}';
            exit;
        }
        //$o = new Order;
        //$data = $o->where(['oid'=>$oid])->select()->toArray();
        $data=Db::name('OrderGoods')->where(['oid'=>$oid])->select();
        $this->assign("orderdetaildata", $data);
        $this->assign("empty", '<span class="empty">没有数据</span>');
        exit;
        //dump($data);
    }

    public function showorder1()//查询已提交（待发货）订单
    {
        $username = Cookie::get('username');
        $auth = Cookie::get('auth');
        $this->assign("welcome", 1);
        $this->assign("username", $username);
        $uid = $this->userinfo['Id'];

        $o = new Order;
        //$data = Db::name('Order')->where(['uid' => $uid, 'status' => 1])->select();
        $data = $o->where(['uid'=>$uid,'status'=>1])->select()->toArray();
        $this->assign("data", $data);
        $this->assign('empty', '<span class="empty">没有数据</span>');
        return view('/index/showorder1');
    }

    public function showorder2()//查询已提交（待发货）订单
    {
        $username = Cookie::get('username');
        $auth = Cookie::get('auth');
        $this->assign("welcome", 1);
        $this->assign("username", $username);
        $uid = $this->userinfo['Id'];

        $o=new Order;
        //$data = Db::name('Order')->where(['uid'=>$uid,'status'=>2])->select();
        $data = $o->where(['uid'=>$uid,'status'=>2])->select()->toArray();
        $this->assign("data", $data);
        $this->assign('empty','<span class="empty">没有数据</span>');
        return view('/index/showorder2');

    }

    public function showorder4()//查询已提交（待发货）订单
    {
        $username = Cookie::get('username');
        $auth = Cookie::get('auth');
        $this->assign("welcome", 1);
        $this->assign("username", $username);
        $uid = $this->userinfo['Id'];

        $o = new Order;
        //$data = Db::name('Order')->where(['uid' => $uid, 'status' => 4])->select();
        $data = $o->where(['uid'=>$uid,'status'=>4])->page(1,10)->select()->toArray();
        $this->assign("data", $data);
        $this->assign('empty', '<span class="empty">没有数据</span>');
        return view('/index/showorder4');
    }

    public function showordertest()//查询订单
    {
        $uid = $this->userinfo['Id'];
        $o = new Order;
        $data = $o->where(['uid'=>$uid])->page(1,10)->select()->toArray();
        dump(count($data));
        dump($data);
        $this->assign("data", $data);
        $this->assign("datalength", count($data));

    }

    public function receiveorder()
    {
        $post_data=file_get_contents("php://input");
        $json_data=json_decode($post_data,true);
        $oid=$json_data['oid'];
        $username = Cookie::get('username');
        $uid = $this->userinfo['Id'];
        $oldstatus=Db::name("Order")->where(['uid'=>$uid,'oid'=>$oid])->value('status');
        if($oldstatus>3){
            echo '{"msg":"订单状态更改失败","errno":1}';
            exit;
        }
        Db::name("Order")->where(['uid'=>$uid,'oid'=>$oid])->setField(['status'=>3]);
        echo '{"msg":"订单状态更改成功","errno":0}';
    }
    public function cancelorder()
    {
        $post_data=file_get_contents("php://input");
        $json_data=json_decode($post_data,true);
        $oid=$json_data['oid'];
        $username = Cookie::get('username');
        $uid = $this->userinfo['Id'];
        $oldstatus=Db::name("Order")->where(['uid'=>$uid,'oid'=>$oid])->value('status');
        if($oldstatus>4){
            echo '{"msg":"订单状态更改失败","errno":1}';
            exit;
        }
        Db::name("Order")->where(['uid'=>$uid,'oid'=>$oid])->setField(['status'=>4]);
        echo '{"msg":"订单状态更改成功","errno":0}';
    }


    public function cart()
    {
//        if($this->request->method() == 'POST')//在商品详情页面选择添加到购物车
//        {}
            //查找购物车表中是否已有该数据
//            $res = Db::name('Cart')->where([
//                'uid'=>$this->userinfo['Id'],
//                'gid'=>$this->gooddetail()->gid
//            ])->find();
//            if (!$res)//不存在，添加数据后跳转到购物车页面
//            {
//                return tocart();
//                return $this->fetch();
//            }
//            else//存在，直接跳转到购物车页面
//            {}
                return $this->fetch();
    }

    public function test()
    {
        $data=Db::name('OrderGoods')->where(['oid'=>10])->select();
        dump($data);
    }
}