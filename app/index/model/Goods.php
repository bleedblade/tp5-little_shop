<?php

	namespace app\index\model;

	use think\Model;
	//use traits\model\SoftDelete;
	class Goods extends Model{
		public function getCategoryAttr($val){
			switch ($val) {
				case '1':
					return "零食";
					break;
				case '2':
					return "饮料";
					break;
				default:
					return "未知";
					break;
			}
		}
	}

?>