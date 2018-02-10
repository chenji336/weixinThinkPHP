<?php
return array(
	//'配置项'=>'配置值'
	'name'=>'ChenJi',
	'LOAD_EXT_CONFIG'=>'user',//这个就是为了引用自己定义的user.php，否则用不了
	'URL_MODEL'=>1,//默认的标示
	'URL_HTML_SUFFIX'=>'html|shtml',//url伪静态后缀，默认的是html，我们可以用|来进行添加(对于伪静态的理解就是不是一个静态页面)，好处就是让蜘蛛可以爬到，引擎更快的找到


	//开始配置数据库的一些参数
	'DB_TYPE'=>'mysql',//数据库类型
	'DB_HOST'=>'localhost',//数据库服务器地址
	'DB_NAME'=>'test',//数据库名
	'DB_USER'=>'root',//数据库用户名
	'DB_PWD'=>'1234',//数据库密码
	'DB_PORT'=>'3306',//端口号
	'DB_PREFIX'=>'',//数据库表前缀
	//开始主从读写分离
	'DB_RW_SEPARATE'=>true,
	//多个主数据库服务器
	'DB_MASTER_NUM'=>'1'
);
?>