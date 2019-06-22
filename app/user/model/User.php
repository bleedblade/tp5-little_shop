<?php

	namespace app\user\model;

	use think\Model;
	use think\Db;

	Class User extends Model
	{
		
		//将password存入数据库前先进行一次加盐的md5加密
		public function setPasswordAttr($val)
		{
			$salt='login';
			return md5($val.$salt);
		}
	}