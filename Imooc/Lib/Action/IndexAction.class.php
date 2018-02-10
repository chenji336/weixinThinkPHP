<?php
header("Content-type:text/html;charset=utf-8");//防止乱码
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {

	public function index(){
		//获得参数 signature nonce token timestamp echostr
		$nonce     = $_GET['nonce'];
		$token     = 'weixinChenji';
		$timestamp = $_GET['timestamp'];
		$echostr   = $_GET['echostr'];
		$signature = $_GET['signature'];
		//形成数组，然后按字典序排序
		$array = array($nonce, $timestamp, $token);
		sort($array);
		//拼接成字符串,sha1加密 ，然后与signature进行校验
		$str = sha1( implode( $array ) );
		if( $str  == $signature && $echostr ){
			header('content-type:text');
			ob_clean();
			//第一次接入weixin api接口的时候
			echo  $echostr;
			exit;
		}else{
			 $this->definedItems();
			 $this->reponseMsg();
		}
	}


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
				if( strtolower($postObj->Event) == 'subscribe') {
				//关注的回复信息有限制长度
					$content  = '欢迎关注我们的微信公众账号';//.$tempstr;
				}else if( strtolower($postObj->Event) == 'click') {
					$content  = $postObj->EventKey;//菜单进行的事件回复
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


	// public function http_curl(){
	// 	//curl是php中比较重要的函数，可以抓取网站的内容
	// 	//获取百度的首页
	// 	//1.初始化curl
	// 	$ch=curl_init();
	// 	$url="http://www.baidu.com";
	// 	//2.设置curl的参数
	// 	curl_setopt($ch,CURLOPT_URL,$url);
	// 	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	// 	//3.采集
	// 	$output=curl_exec($ch);
	// 	//4.关闭
	// 	curl_close();
	// 	var_dump($output);

	// 	// $info = curl_getinfo($ch);
	// 	// echo ' 获取 '.$info['url'].'耗时'.$info['total_time'].'秒';
	// }


public function http_curl($url,$type='get',$res='json',$arr='',$typeHttp='http'){
			//curl是php中比较重要的函数，可以抓取网站的内容
			//获取百度的首页
			//1.初始化curl
	$ch=curl_init();
			//2.设置curl的参数
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	if($type=='post'){
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
	}

	if($typeHttp=='https'){
				//因为“https”是加密的，所以要在curl设置参数里面加上上面两句话，才能得到access_token吧,不然会得到null！
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查  
		        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在 
		    }
			//3.采集
		    $output=curl_exec($ch);
			//4.关闭
		    curl_close();

		    if(curl_errno($ch)){
		    	return curl_error($ch);
		    }

		    if($res=='json'){
		    	return json_decode($output,true);
		    }else
		    {
		    	return $output;
		    }

		}

// 没有进行包装之前获取token
/*public function getWxAccessToken(){
		$appid='wx7aaa111b6099d474';
		$appsecret='d787b80ab42b02acf700611a8d43fdcd';
		$url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;

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
	}*/
	


		public function getWxAccessToken(){

			if($_SESSION['access_token']&&$_SESSION['expire_time']>time()){
				$access_token=$_SESSION['access_token'];
			}else{
				// 自己的订阅号，接口没那么多
				// $appid='wx7aaa111b6099d474';
				// $appsecret='d787b80ab42b02acf700611a8d43fdcd';

				// 测试账号
				$appid='wx6247ff16084ba57f';
				$appsecret='80d83df5d5caf908d7e7dfea1e645116';
				$url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;

				$res=$this->http_curl($url,'get','json','','https');

				$access_token=$res['access_token'];
			//保存在session中
				$_SESSION['access_token']=$access_token;
				$_SESSION['expire_time']=time()+7200;
			}
			// var_dump($access_token);
			return $access_token;
		}

		public function getWxServerIp(){
			$token=$this->getWxAccessToken();
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


    public function test(){
			// $indexModel=new IndexModel();
			// //$commonModel=new CommonModel();
		      $common=new CommonModel();
	  //   	$common=D('Common');
	     	echo $common->strmake('555');
		//$this->getWxServerIp();
    	//var_dump($this->getWxAccessToken());


    }

    //调动天气的api接口查看天气数据
    public function tianqi(){
            //http://apistore.baidu.com/apiworks/servicedetail/112.html
    	$ch = curl_init();
    	// 这个免费的api已经被停用了，使用就缴费
    	// $url = 'http://apis.baidu.com/apistore/weatherservice/cityname?cityname='.urlencode('深圳');
    	$url = 'http://apistore.baidu.com/microservice/weather?citypinyin=beijing';
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
    }


    public function definedItems(){
    	$access_token=$this->getWxAccessToken();
    	$url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
    	echo $url.'<br>'.'<hr>';
    	$postArr=array(
    		'button'=>array(
    			array(
    				'name'=> urlencode('菜单一'),
    				'type'=>'click',
    				'key'=>'V1001_TODAY_MUSIC'
    				),
    			array(
    				'name'=>urlencode('菜单1'),
    				'sub_button'=>array(
    					array(
    						'type'=>'view',
    						'name'=>urlencode('搜索1'),
    						'url'=>'http://www.baidu.com'
    						),
    					array(
    						'type'=>'view',
    						'name'=>urlencode('视频'),
    						'url'=>'http://www.baidu.com'
    						),
    					array(
    						'type'=>'click',
    						'name'=>urlencode('赞一下我们'),
    						'key'=>'V1001_GOOD'
    						),
    					)
    				)
    			)
    		);

    	$postData=urldecode(json_encode($postArr));

    	echo $postData.'<hr>';
    	$res=$this->http_curl($url,'post','json',$postData,'https');

    	var_dump($res);
    	
    }



    public function sendMessageAll(){
    	//1.获取access_token
    	$access_token=$this->getWxAccessToken();
    	$url='https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token='.$access_token;
    		echo $url.'<hr>';
    	//2.获取数组
    	    //简单文本
	    	// {     
	    	// 	"touser":"OPENID",
	    	// 	"text":{           
	    	// 		"content":"CONTENT"            
	    	// 	},     
	    	// 	"msgtype":"text"
	    	// }
	    	// 图文
	    	// {
	    	// 	"touser":"OPENID", 
	    	// 	"mpnews":{              
	    	// 		"media_id":"123dsdajkasd231jhksad"               
	    	// 	},
	    	// 	"msgtype":"mpnews" 
	    	// }
	       $postArr=array(
	    		'touser'=>'oCf0HwvSJpYYfwZzojM6LnpGZDp8',
	    		'text'=>array('content'=>urlencode('这是我给你发的群发接口')),
	    		'msgtype'=>'text'
	    	);
	       //获取图文
    		// $postArr=array(
	    	// 	'touser'=>'oCf0HwvSJpYYfwZzojM6LnpGZDp8',
	    	// 	'mpnews'=>array('media_id'=>$this->get_thumb_id()),
	    	// 	'msgtype'=>'mpnews'
	    	// );
    	//3.数组转换成json
    	$postJson=urldecode(json_encode($postArr));
    	echo $postJson.'<hr>';
    	//4.curl调用
    	$res=$this->http_curl($url,'post','json',$postJson,'https');
        echo $res.'<hr>';
    	var_dump($res);
    }


    //获取thumbid
    //https://mp.weixin.qq.com/wiki/10/78b15308b053286e2a66b33f0f0f5fb6.html这个链接在文档中没有出现，需要百度搜索“基础支持-上传多媒体文件接口中获得”
    public function get_thumb_id(){
    	$access_token=$this->getWxAccessToken();
    	$url='http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token='.$access_token.'&type=image';


    	$filepath=dirname(__FILE__).'/23.jpg';
    	 echo $access_token.'<br>';
    	 echo $filepath.'<br>';
    	 $data=array('media'=>'@'.$filepath);
    	
    	 $res=$this->http_curl($url,'post','json',$data,'https');

    	 echo $res['media_id'].'<br>';
    	 return $res['media_id'];
    }

    //模板消息的发送
    public function sendTemplateMessage(){

    	  // {
       //     "touser":"OPENID",
       //     "template_id":"ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
       //     "url":"http://weixin.qq.com/download",            
       //     "data":{
       //             "first": {
       //                 "value":"恭喜你购买成功！",
       //                 "color":"#173177"
       //             }
       //     }
       //}

    	$url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->getWxAccessToken();
    	$postArr=array(
    			'touser'=>'oCf0HwvSJpYYfwZzojM6LnpGZDp8',
    			'template_id'=>'2DniV3S5W3mb_UjGhkI-KBgaOC1Cy2GlRfK-uhFlrbo',	
    			'url'=>'http://www.baidu.com',
    			'data'=>array(
    					'name'=>array('value'=>'陈骥','color'=>'#173177'),
    					'money'=>array('value'=>'100','color'=>'#173177'),
    					'date'=>array('value'=>date('Y-m-d H:i:s'),'color'=>'#173177'),
    				)
    		);
    	$postJson=json_encode($postArr);
    	$res=$this->http_curl($url,'post','json',$postJson,'https');

    	var_dump($res);

    }

    //获取openid
    public function getBaseInfo(){
    	$appid='wx6247ff16084ba57f';
    	$redirect_uri=urlencode('http://1.chenjitest2.applinzi.com/weixinThinkPHP/imooc.php/Index/getUserOpenId');
    	$url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
    	header('location:'.$url);
    }

    public function getUserOpenId(){
    	$appid='wx6247ff16084ba57f';
    	$appsecret='80d83df5d5caf908d7e7dfea1e645116';
    	$code=$_GET['code'];
    	$url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';

    	$res=$this->http_curl($url,'get','json','','https');

    	var_dump($res);
    }

    //拉取用户信息
    public function getInfo(){
    	$appid='wx6247ff16084ba57f';
    	$redirect_uri=urlencode('http://1.chenjitest2.applinzi.com/weixinThinkPHP/imooc.php/Index/getUserInfo');
    	$url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect';
    	header('location:'.$url);
    }

    public function getUserInfo(){
    	$appid='wx6247ff16084ba57f';
    	$appsecret='80d83df5d5caf908d7e7dfea1e645116';
    	$code=$_GET['code'];
    	$url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';
    	$res=$this->http_curl($url,'get','json','','https');

    	$access_token=$res['access_token'];
    	$openid=$res['openid'];

    	$urlInfo='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
    	$userinfo=$this->http_curl($urlInfo,'get','json','','https');
    	var_dump($userinfo);
    }


    //下面都是调用js-sdk分享接口的代码
    //-------------------------------------------------------------------------------------------------------------
    public function getNonceStr(){
    	$nonceArr=array(
    		'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W',
    		'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w',
    		'0','1','2','3','4','5','6','7','8','9'
    		);
    	$nonceStr='';
    	for($i=1;$i<=16;$i++){
			$key=rand(0,count($nonceArr)-1);
			$nonceStr.=$nonceArr[$key];
    	}

    	return $nonceStr;
    }

    public function get_jsapi_ticket(){
    	if($_SESSION['jsapi_ticket']&&$_SESSION['jsapi_expires_in']>time()){
    		$jsapi_ticket=$_SESSION['jsapi_ticket'];
    	}else{
			$access_token=$this->getWxAccessToken();
	    	$url='https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
	    	$res=$this->http_curl($url,'get','json','','https');
	    	$jsapi_ticket=$res['ticket'];
	    	$_SESSION['jsapi_ticket']=	$jsapi_ticket;
	    	$_SESSION['jsapi_expires_in']=time()+7000;
    	}
    	echo 'get_jsapi_ticket:   '.$_SESSION['jsapi_ticket'].'  -----    '.$_SESSION['jsapi_expires_in'].'<br>';
    	return $jsapi_ticket;
    }

    public function share(){
      	$jsapi_ticket=$this->get_jsapi_ticket();
      	$noncestr=$this->getNonceStr();
      	$timestamp=time();
	      //////
      	// //$url如果后面有？也需要完全一样，否则解析出来就是无效的signature
	      //////
      	// $url='http://1.chenjitest2.applinzi.com/weixinThinkPHP/imooc.php/Index/share';
      	 // 注意 URL 一定要动态获取，不能 hardcode.因为分享的连接地址变了，多了?from=singlemessage，所以需要动态获取
	    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      	$signature='jsapi_ticket='.$jsapi_ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
      	$signature=sha1($signature);
    	$this->assign('titleName','陈骥朋友圈分享');
    	$this->assign('appId','wx6247ff16084ba57f');
    	$this->assign('timestamp',$timestamp);
    	$this->assign('nonceStr',$noncestr);
    	$this->assign('signature',$signature);
    	
    	echo 'jsapi_ticket:   '.$jsapi_ticket.'<br>';
    	echo 'noncestr:    '.$noncestr.'<br>';
    	echo 'timestamp:   '.$timestamp.'<br>';
    	echo 'url:    '.$url.'<br>';
    	echo 'wx7aaa111b6099d474<br>';
    	echo 'signature:   '.$signature.'<br>';
    	$this->display('Index/share');
    }

     public function share2(){
      	
    	$this->display('Index/share2');
    }
  //-------------------------------------------------------------------------------------------------------------
}
