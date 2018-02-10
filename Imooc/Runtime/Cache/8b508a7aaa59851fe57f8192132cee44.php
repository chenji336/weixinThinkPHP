<?php if (!defined('THINK_PATH')) exit(); if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" id="viewport" content="width=device-width, initial-scale=1">
	<title>Document</title>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js" type="text/javascript"></script>
</head>
<body>
	<?php echo ($titleName); ?>
	<button onclick="chooseImage()" id="ccc">找出图片</button>
	 <script>
		wx.config({
		    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
		    appId: '<?php echo ($appId); ?>', // 必填，公众号的唯一标识
		    timestamp: '<?php echo ($timestamp); ?>', // 必填，生成签名的时间戳
		    nonceStr: '<?php echo ($nonceStr); ?>', // 必填，生成签名的随机串
		    signature: '<?php echo ($signature); ?>',// 必填，签名，见附录1
		    jsApiList: ["onMenuShareTimeline","onMenuShareAppMessage","chooseImage"] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
		});

		wx.ready(function(){
   			wx.onMenuShareTimeline({
			    title: '分享朋友圈标题', // 分享标题
			    link: 'http://1.chenjitest2.applinzi.com/weixinThinkPHP/imooc.php/Index/share', // 分享链接
			    imgUrl: 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png', // 分享图标
			    success: function () { 
			        // 用户确认分享后执行的回调函数
			        alert("分享朋友圈成功");
			    },
			    cancel: function () { 
			        // 用户取消分享后执行的回调函数
			         alert("分享朋友圈失败");
			    }
			})


   			wx.onMenuShareAppMessage({
			    title: '分享给朋友', // 分享标题
			    desc: '分享给朋友的描述', // 分享描述
			    link: 'http://1.chenjitest2.applinzi.com/weixinThinkPHP/imooc.php/Index/share', // 分享链接
			    imgUrl: 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png', // 分享图标
			    type: 'link', // 分享类型,music、video或link，不填默认为link
			    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
			    success: function () { 
			        // 用户确认分享后执行的回调函数
			          alert("分享给朋友成功");
			    },
			    cancel: function () { 
			        // 用户取消分享后执行的回调函数
			         alert("分享给朋友失败");
			    }
			});

		});

		function chooseImage(){
			alert(1);
			wx.chooseImage({
		    count: 1, // 默认9
		    sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
		    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
		    success: function (res) {
		        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
		    }
			});
		}
		

		wx.error(function(res){
   			alert('wx.error:'+res);
		});


	</script>
</body>
</html>