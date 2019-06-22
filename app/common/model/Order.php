<?php


namespace app\common\model;


use think\Model;

class Order extends Model
{
//    protected $autoWriteTimestamp = true;
//    //protected $createTime = 'createtime';
//    protected $insert = ['create_time'];
//    protected $updateTime = false;


    public function getStatusAttr($val)
    {
        switch ($val) {
            case '1':
                return "已提交";
                break;
            case '2':
                return "已发货";
                break;
            case '3':
                return "已收货";
                break;
            case '4':
                return "取消订单中";
                break;
            case "5":
                return "订单已取消";
                break;
            default:
                return "订单异常";
                break;
        }
    }
       public function getCreateTimeAttr($val)
       {
           return date("Y-m-d H:i:s",$val);
       }
}