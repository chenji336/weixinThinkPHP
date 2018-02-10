<?php
	//基于tp(THINKPHP)框架
    // 如果放在新浪云上，建议吧下面的define（'APP_DEBUG',true）取掉，或则改成false，否则会报错（不会去读写）
    // 如果修改就修改新浪云的缓存文件，就可以显示出来修改后的数据
	define('APP_DEBUG',true);//开启调试很关键，要不然不提示错误原因，而且可以不需要考虑缓存
	//1.定义项目的名称
	define('APP_NAME','Imooc');
	//2.定义项目的路径
	define('APP_PATH','Imooc/');
	//3.引入tp的核心文件
	require('./ThinkPHP/ThinkPHP.php'); //include