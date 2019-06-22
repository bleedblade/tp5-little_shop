<?php


namespace app\index\controller;

use think\Cookie;
use app\common\model\Order;
use app\common\controller\Base;
use think\Db;

class Sell extends Base
{
    public function index()
    {
        print_r($this->userinfo);
        return __METHOD__;
    }

    public function createorder()
    {
//       $gid =  input('gid');
//       $num =  input('num');
       $uid = $this->userinfo['Id'];
       $mycart=Db::name('Cart')->where("uid",$uid)->select();
       //$mycart和$buys是一样的作用，都是将要下单的商品（gid,num,price等）目前还没写怎么获取，打算从购物车表直接查询获取将结果转到订单表
        //    ["uid"]
        //    ["gid"]
        //    ["num"]
        //    ["gprice"]
//       $buys = [
//            [
//                'gid' => 103,
//                'price' => 10,
//                'num' => 1,
//            ] ,
//           [
//               'gid' => 104,
//               'price' => 20,
//               'num' => 5,
//           ],
//           [
//               'gid' => 105,
//               'price' => 30,
//               'num' => 1,
//           ]
//
//       ];
       $total_price = 0;
       $total_num = 0;
       foreach ($mycart as $k=>$v){
           $total_price += ($v['gprice']*$v['num']);
           $total_num += $v['num'];
       }//到这里获得了uid,mycart,total_price,total_num

        $post_data=file_get_contents("php://input");
        $json_data=json_decode($post_data,true);
        $buyer=html_entity_decode($json_data["buyer"]);
        $address=html_entity_decode($json_data["address"]);
        $phone=html_entity_decode($json_data["phone"]);
        $token=$json_data["token"];
        session_start();
        if(isset($_SESSION['daxia'][$token])){
            unset($_SESSION['daxia'][$token]);
        }
        else{
            echo '{"errno":1,"msg":"token值错误！"}';
            exit;
        }
        if(empty($buyer) or empty($phone) or empty($address))
        {
            echo '{"errno":1,"msg":"收货人、联系电话、收货地址不能为空！"}';
            exit;
        }
        if (is_numeric($phone)or 8<=strlen($phone) or 13>=strlen($phone)){
            //$phone=$phone->toString();
        }
        else
        {
            echo '{"errno":1,"msg":"手机号格式错误！"}';
            exit;
        }
//        $buyer=$buyer->toString();
//        $address=$address->toString();
//        $phone=$phone->toString();

       $data  = [
           'uid' => $uid,
           'gcount' => $total_num,
           'cost' => $total_price,
           'status' => 1,
           'create_time' =>time(),
           'buyer' => html_entity_decode($buyer),
           'phone' => html_entity_decode($phone),
           'address' => html_entity_decode($address)
       ];
       $orderid = Db::name('order')->insertGetId($data);

        foreach ($mycart as $k=>$v){
            $listdata[]  = [
                'gid' => $v['gid'],
                'num' => $v['num'],
                'gprice' => $v['gprice'],
                'oid' =>$orderid,
                'gname'=> $v['gname']
            ];
        }
        Db::name('order_goods')->insertAll($listdata);
        echo '{"errno":0,"msg":"创建订单成功！"}';
    }

}
