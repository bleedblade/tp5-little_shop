<?php

    namespace app\goods\controller;

//    use app\common\model\Goods;

    use app\common\controller\Base;
    use app\common\model\User;
    use app\common\model\Order;
    use think\Controller;
    use think\Cookie;
    use think\Db;

    class Goods extends Base
{
    //用于View循环测试的乱添加20个商品
    public function addGoods(){
        $data=[];
        for ($i=0;$i<20;$i++)
        {
            $data[]=[
                "gname"=>"零食{$i}",
                "gprice"=>"{$i}",
                "gcount"=>100,
            ];
        }
        Db::name('goods')->insertAll($data);
        return "ok";
    }
    //商品详情，用户点开后加载的，需要传递gid参数
    public function gooddetail()
    {

            $username = Cookie::get('username');
            $auth = Cookie::get('auth');
            $this->assign("welcome", 1);
            $this->assign("username", $username);
            if (!$username || !$auth) {//验证两个cookie是否都存在，都不在为真
                $this->assign("welcome", 0);
            }
            //验证username为真
            $USER = User::where('username', $username)->find();
            if (!$USER) {
                $this->assign("welcome", 0);

            }
            //验证令牌为真
            if ($auth != md5($USER['password'] . $USER['login_time'])) {
                Cookie::set('auth', null);
                $this->assign("welcome", 0);
            }
            //验证用户身份完毕，开始获取商品详情数据
            //查找指定商品数据
            $gid = input("gid");
            if(Db::name('Goods')->where(['gid'=>$gid])->find())//Goods表中存在该gid
            {
            $res = \app\index\model\Goods::where("gid", $gid)
                ->find();
            //dump($res['gid']);
            $this->assign("gooddetails", $res);
            $this->assign("gname", $res['gname']);
            $this->assign("gcount", $res['gcount']);
            $this->assign("gid", $res['gid']);
            $this->assign("gprice", $res['gprice']);
            return $this->fetch();
            }else
            {
                return $this->error('该商品不存在');
            }
        }

    //将添加的商品传入购物车，保存在该用户的表中
    public function tocart()
    {
//        $username=Cookie::get('username');
//        $uid=Db::table(User)->value($username);
//        if ($this->request->method() == 'POST')//在商品详情页面选择添加到购物车
//        {
            //查找购物车表中是否已有该数据
//        $gid=input("gid");
//        $num=input("num");
//        $data=input("data");
        $post_data=file_get_contents("php://input");
        $json_data=json_decode($post_data,true);
        if(empty($json_data)) {
            echo "data为空";
            dump($json_data);
        }
        $gid=$json_data["gid"];
        $num=$json_data["num"];
        $uid=$this->userinfo['Id'];
        $res=Db::name("Goods")->where(['gid'=>$gid])->find();
        if(empty($gid) or empty($num) or !$res or $num<=0){// or $num>$res["gcount"]
            echo '{"msg":"添加失败，请检查商品和数量"}';
            exit;
        }
            $res = Db::name('Cart')->where([
                'uid' => $uid,
                'gid' => $gid
            ])->find();
            if (!$res)//不存在，添加数据后跳转到购物车页面
            {
//                $uid = $this->userinfo['Id'];
                $gprice = Db::name("Goods")->where(["gid"=>$gid])->value('gprice');
                $gname = Db::name("Goods")->where(["gid"=>$gid])->value('gname');
                $data = [
                    'uid' => $uid,
                    'gid' => $gid,
                    'num' => $num,
                    'gprice' => $gprice,
                    'gname' => $gname,
                ];

                Db::name("Cart")->insert($data);
                echo '{"msg":"购物车添加新商品成功"}';
                //echo "<script>alert('成功添加到购物车')</script>";
                //return $this->showcart();
            } else//存在，直接跳转到购物车页面//查找该商品数量，加上
            {
                $old_num = Db::name("Cart")->where(["gid"=>$gid,"uid"=>$uid])->value('num');
                Db::name("Cart")->where(["gid"=>$gid,"uid"=>$uid])->setField('num',$num+$old_num);
                echo '{"msg":"在购物车中已有该商品，所选数量已更新，请在购物车中进行确认","test":'.$old_num.'}';
            }
    }


        public function goodstest(){
//            $gid=input("gid");
//            $num=input("num");
//            $data=input("data");
//            $data="{\"gid\":3,\"num\":1}";
//            $post_data=file_get_contents("php://input");
//            $json_data=json_decode($post_data,true);
//            if(empty($data)) {
//                echo "data为空";
//            }
//            $old_num = Db::name("Cart")->where(["gid"=>4,"uid"=>13])->value('num');
//            echo "old_num";
//            dump($old_num);
//            dump(Db::name("Goods")->where(["gid"=>1])->column('gprice'));
//            dump($json_data);
//            dump($json_data["gid"]);
//            dump(input("gid"));
//            echo '<script>alert("后端收到")</script>';
//            $sho = new Order;
//            $res=Order::select();
//            dump($res->toArray());
            $strname="gr<>//\/!@&(&(~@#^&*`gr*dghth2543";
           $result=preg_match("/[^a-zA-Z0-9]{1}/",$strname);
           dump($result);

        }
        public function showcart()
        {
            $username = Cookie::get('username');
            $auth = Cookie::get('auth');
            $this->assign("welcome", 1);
            $this->assign("username", $username);
            if (!$username || !$auth) {//验证两个cookie是否都存在，都不在为真
                $this->assign("welcome", 0);
            }
            //验证username为真
            $USER = User::where('username', $username)->find();
            if (!$USER) {
                $this->assign("welcome", 0);

            }
            //验证令牌为真
            if ($auth != md5($USER['password'] . $USER['login_time'])) {
                Cookie::set('auth', null);
                $this->assign("welcome", 0);
            }
            //用户验证完毕（从上面的gooddetail上扒下来的）
            $uid = $this->userinfo['Id'];
            $cart = Db::table('Cart')->where('uid', $uid)->select();
            //dump($cart);
            $total_price = 0;
            foreach ($cart as $k=>$v) {
                $total_price += ($v['gprice'] * $v['num']);
            }
            $this->assign('total_price',$total_price);
            $this->assign('cart',$cart);
            $this->assign('empty','<span class="empty">没有数据</span>');
            return view('/goods/cart');
        }

        public function changecartnum()
        {
            $post_data=file_get_contents("php://input");
            $json_data=json_decode($post_data,true);
            $gid=$json_data["gid"];
            $num=$json_data["num"];
            $uid=$this->userinfo['Id'];
            if(empty($gid) or empty($num) or !is_int($gid) or !is_int($num) or $num<=0 or $gid<=0){
                echo '{"msg":"添加失败，请检查商品和数量"}';
                exit;
            }
            Db::name('Cart')->where(['uid'=>$uid,'gid'=>$gid])->setField('num',$num);
            $cart = Db::table('Cart')->where('uid', $uid)->select();
            //dump($cart);
            $total_price = 0;
            foreach ($cart as $k=>$v) {
                $total_price += ($v['gprice'] * $v['num']);
            }
            echo '{"total_price":'.$total_price.'}';
        }

        public function deletecartgood(){
            $post_data=file_get_contents("php://input");
            $json_data=json_decode($post_data,true);
            $gid=$json_data["gid"];
            $uid=$this->userinfo['Id'];
            if(empty($gid) or !is_int($gid) or $gid<=0){
                echo '{"msg":"删除失败，请检查商品和数量"}';
                exit;
            }
            Db::name('Cart')->where(['uid'=>$uid,'gid'=>$gid])->delete();
            $cart = Db::table('Cart')->where('uid', $uid)->select();
            //dump($cart);
            $total_price = 0;
            foreach ($cart as $k=>$v) {
                $total_price += ($v['gprice'] * $v['num']);
            }
            echo '{"total_price":'.$total_price.',"errno":1,"msg":"从购物车删除商品成功！"}';
        }

        public function carttoorder()
        {
            $username = Cookie::get('username');
            $auth = Cookie::get('auth');
            $this->assign("welcome", 1);
            $this->assign("username", $username);
            if (!$username || !$auth) {//验证两个cookie是否都存在，都不在为真
                $this->assign("welcome", 0);
            }
            //验证username为真
            $USER = User::where('username', $username)->find();
            if (!$USER) {
                $this->assign("welcome", 0);

            }
            //验证令牌为真
            if ($auth != md5($USER['password'] . $USER['login_time'])) {
                Cookie::set('auth', null);
                $this->assign("welcome", 0);
            }
            //用户验证完毕（从上面的gooddetail上扒下来的）
            $uid = $this->userinfo['Id'];
            $cart = Db::table('Cart')->where('uid', $uid)->select();
            if(empty($cart))
            {
                return $this->error('购物车为空，请先添加商品到购物车！');
            }
            //dump($cart);
            $total_price = 0;
            foreach ($cart as $k=>$v) {
                $total_price += ($v['gprice'] * $v['num']);
            }

            $pToken = md5(uniqid(rand(),true).'daxia');
            $_SESSION['daxia'][$pToken]=true;
            $this->assign('token',$pToken);
            $this->assign('total_price',$total_price);
            $this->assign('cart',$cart);
            $this->assign('empty','<span class="empty">没有数据</span>');
            return view('/goods/carttoorder');
        }

}