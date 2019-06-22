<?php
	
	use think\Env;
	return [
		//'app_status'		=> Env::get('status','dev'),
		'auto_bind_module'	=> true,
		'app_trace'	=>	true,
		'trace'	=>	[
			'type'	=>	'html',
		],
		'view_replace_str'	=>	[
			'__CSS__'	=>	'/static/css',
			'__JAVASCRIPT__'	=>	'/static/javascript',
            '__IMG__'   =>  '/static/img',
            '__UploadIMG__' =>  '/uploads/img',
		],
		'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 3600,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => 'localhost',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => false,
        // 是否使用 setcookie
        'setcookie' => true,
    ],
    	'SHOW_PAGE_TRACE'=>true,
 		'debug'           =>true,
 		'show_error_msg'         => true,
        'app_debug' =>  true,
	];
?>