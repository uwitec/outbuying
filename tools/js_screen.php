<html>
<head>
<title>JS检测浏览器宽、高</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function getInfo()
{
    var s = "";
    s += " 网页可见区域宽："+ document.body.clientWidth+" \n";
    s += " 网页可见区域高："+ document.body.clientHeight+" \n";
    s += " 网页可见区域宽："+ document.body.offsetWidth + " (包括边线和滚动条的宽)"+" \n";
    s += " 网页可见区域高："+ document.body.offsetHeight + " (包括边线的宽)"+" \n";
    s += " 网页正文全文宽："+ document.body.scrollWidth+" \n";
    s += " 网页正文全文高："+ document.body.scrollHeight+" \n";
    s += " 网页被卷去的高(ff)："+ document.body.scrollTop+" \n";
    s += " 网页被卷去的高(ie)："+ document.documentElement.scrollTop+" \n";    
    s += " 网页被卷去的左："+ document.body.scrollLeft+" \n";
    s += " 网页正文部分上："+ window.screenTop+" \n";
    s += " 网页正文部分左："+ window.screenLeft+" \n";      
    s += " 屏幕分辨率的高："+ window.screen.height+" \n";
    s += " 屏幕分辨率的宽："+ window.screen.width+" \n";
    s += " 屏幕可用工作区高度："+ window.screen.availHeight+" \n";
    s += " 屏幕可用工作区宽度："+ window.screen.availWidth+" \n";
    s += " 你的屏幕设置是 "+ window.screen.colorDepth +" 位彩色"+" \n";
    s += " 你的屏幕设置 "+ window.screen.deviceXDPI +" 像素/英寸"+" \n";
    s += " window的页面可视部分实际高度(ff) "+window.innerHeight+" ";    
    //alert (s);
	document.getElementById('screen_info').value = s;
}
</script>
</head>
<body>
<div>
<pre>
网页可见区域宽：	document.body.clientWidth
网页可见区域高：	document.body.clientHeight
网页可见区域宽(包括边线和滚动条的宽)：	document.body.offsetWidth
网页可见区域高(包括边线的宽)：	document.body.offsetHeight
网页正文全文宽：	document.body.scrollWidth
网页正文全文高：	document.body.scrollHeight
网页被卷去的高(ff)：	document.body.scrollTop
网页被卷去的高(ie)：	document.documentElement.scrollTop    
网页被卷去的左：	document.body.scrollLeft
网页正文部分上：	window.screenTop
网页正文部分左：	window.screenLeft      
屏幕分辨率的高：	window.screen.height
屏幕分辨率的宽：	window.screen.width
屏幕可用工作区高度：	window.screen.availHeight
屏幕可用工作区宽度：	window.screen.availWidth
屏幕彩色设置(位)：	 window.screen.colorDepth
屏幕设置DPI(像素/英寸)：	window.screen.deviceXDPI
window的页面可视部分实际高度(ff)：	window.innerHeight
</pre>
<button onclick="getInfo();">开始检测</button>
<textarea id="screen_info" cols="80" rows="15"></textarea>
</div>
</body>
</html>