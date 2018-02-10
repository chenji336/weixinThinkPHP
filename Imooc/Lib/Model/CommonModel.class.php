<?php 
//如果extends Model报错是因为Model需要配置数据库吧 在config中,这是我自己的看法
	class CommonModel extends Model{
		public  function strmake($str){
			return md5($str);
		}
	}
 ?>