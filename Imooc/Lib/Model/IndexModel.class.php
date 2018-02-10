<?php
//继承了model就会报错，不确定原因
class IndexModel {
	public function reponseMsg(){
		//1.获取到微信推送过来post数据（xml格式）
		$postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
		$tempstr=$postArr;
		//2.处理消息类型，并设置回复类型和内容
		/*接受到的xml信息(事件)https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140453&token=&lang=zh_CN
		<xml>
		<ToUserName><![CDATA[toUser]]></ToUserName>
		<FromUserName><![CDATA[FromUser]]></FromUserName>
		<CreateTime>123456789</CreateTime>
		<MsgType><![CDATA[event]]></MsgType>
		<Event><![CDATA[subscribe]]></Event>
		</xml>
	    */

	    /*接受普通消息
	    <xml>
		 <ToUserName><![CDATA[toUser]]></ToUserName>
		 <FromUserName><![CDATA[fromUser]]></FromUserName>
		 <CreateTime>1348831860</CreateTime>
		 <MsgType><![CDATA[text]]></MsgType>
		 <Content><![CDATA[this is a test]]></Content>
		 <MsgId>1234567890123456</MsgId>
		 </xml>
	     */
	    //利用下面变成数组形式
		 $postObj = simplexml_load_string( $postArr );
		//$postObj->ToUserName = '';
		//$postObj->FromUserName = '';
		//$postObj->CreateTime = '';
		//$postObj->MsgType = '';
		//$postObj->Event = '';
		// gh_6ff72e57c296

		//回复用户消息(纯文本格式)	
		/*回复过去的文本数据
			<xml>
			<ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[fromUser]]></FromUserName>
			<CreateTime>12345678</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[你好]]></Content>
			</xml>
		*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$time     = time();
			$msgType  =  'text';
		//判断该数据包是否是订阅的事件推送
			if( strtolower( $postObj->MsgType) == 'event'){
			//如果是关注 subscribe 事件
				if( strtolower($postObj->Event == 'subscribe') ){
				//关注的回复信息有限制长度
				$content  = '欢迎关注我们的微信公众账号';//.$tempstr;

				$template = "
				<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
				</xml>
				";
			    //sprintf相当于csharp中的string.fromat不过这里需要指定是什么类型，%s代表string
				$info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
			}
		}else if(strtolower($postObj->MsgType=='text')){
			switch(trim($postObj->Content)){
				case 'hello':
				$content  = 'hello,too';
				break;
				case 'minxi':
				$content  = 'hello baby';
				break;
				case 'baidu'://链接
				$content  = '<a href="http://www.baidu.com">百度</a>';
				break;
				case 'tianqi':
				    $ch = curl_init();
				    $url = 'http://apis.baidu.com/apistore/weatherservice/citylist?cityname=%E6%9C%9D%E9%98%B3';
				    $header = array(
				        'apikey: 95e07f8df9d6a33b9e6915a18e2064f0',
				    );
				    // 添加apikey到header
				    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
				    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				    // 执行HTTP请求
				    curl_setopt($ch , CURLOPT_URL , $url);
				    $res = curl_exec($ch);

				    var_dump(json_decode($res));
				    exit;
				break;
				case 'tuwen1'://进行一个图文的发送，最多进行是个图文的发送
				$arr = array(
							array(
								'title'=>'imooc',
								'description'=>"imooc is very cool",
								'picUrl'=>'http://img.ph.126.net/jQWkoygrzLUGBI8-98E7Zg==/6597855917470782460.jpg',
								'url'=>'http://www.baidu.com',
							),
							array(
								'title'=>'qq',
								'description'=>"qq is very cool",
								'picUrl'=>'http://img.ph.126.net/jQWkoygrzLUGBI8-98E7Zg==/6597855917470782460.jpg',
								'url'=>'http://www.qq.com',
							),
						);
				$template = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<ArticleCount>".count($arr)."</ArticleCount>
						<Articles>";
			    foreach($arr as $k=>$v){
					$template .="<item>
								<Title><![CDATA[".$v['title']."]]></Title> 
								<Description><![CDATA[".$v['description']."]]></Description>
								<PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
								<Url><![CDATA[".$v['url']."]]></Url>
								</item>";
				}
			
				$template .="</Articles>
						     </xml> ";
			    echo sprintf($template, $toUser, $fromUser, time(), 'news');
				exit;
				default:
				$content  = trim($postObj->Content);
				break;
			}

			$template = "
			<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Content><![CDATA[%s]]></Content>
			</xml>
			";
			    //sprintf相当于csharp中的string.fromat不过这里需要指定是什么类型，%s代表string
			$info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
		}
		
		echo $info;
	}


	public function getWxServerIp(){
		$token='m1gQ2huThJZVss6jR8TFLJXAQAYUDNIvudomauxZiuNOEHqryshH0ARAv3PAKHLhIfMVRGzKyGs5uLS4Zh5KKpTBdCEuMIEREpDmDYfAbQW9Vo9Q2khu-8jJjIqijqcSDCUcAJAJIA';
		$url='https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$token;

		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

		//因为“https”是加密的，所以要在curl设置参数里面加上上面两句话，才能得到access_token吧,不然会得到null！
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在 

		//调用接口
		$res=curl_exec($ch);
		curl_close();

		if(curl_errno($ch)){
			var_dump(curl_error($ch));
			exit;
		}

		$arr=json_decode($res,true);
		var_dump($arr);
	}

	public  function strmake($str){
			return md5($str);
	}
}