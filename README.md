# tp5-little_shop
基于ThinkPHP5的小商城（加了点Web安全的初学料）

具体的代码都在app里面。
admin是管理员的操作。
common的Base是验证用户身份信息（cookie）的，验证失败只能访问商城首页，否则返回到登录页面。
goods是商品的信息，商品展示、商品（详情）查询等。
index是商城的首页，包括了商品查询渲染页面、
user是用户，包括了用户的注册登录，个人中心订单的查询、状态的更改，访问商品详情页面并可以添加商品到购物车、对购物车中的商品进行编辑操作、将购物车中的商品生成订单并提交。

还不会用git……是直接上传的，有配置https，但是证书我就不上传了嘿嘿嘿

功能都是些普通的功能，分两个端（用户端和管理员端）。
用户端：用户注册登录，商品浏览、商品详情浏览，购物车功能，下单，个人中心的订单查看，订单收货，修改密码等。
管理员端：商品的增删改查、订单的查看、发货，修改密码。

但是主要是想搭建模拟一个环境然后整渗透测试来着……然后发现我太菜了渗透失败……（还是说没有漏洞（因为太简单了？！）【警觉】）

在文件上传这一块，使用ThinkPHP5自带的文件上传功能，但是发现上传的一句话也跟不能用，就觉得很迷，同样的内容，在文件夹下新建和通过上传到文件夹，直接新建的一句话能用，上传的一句话就用不了……这是为啥……感觉就很神秘。
