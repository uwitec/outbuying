/***
*jquery dialog
*
filter: Alpha(opacity=65);
-moz-opacity:.65;
opacity:0.65; 
position:absolute;
background-color:#ccc;
z-index:1001;
***/
(function($)
{
	var DIALOGDATA="<div id='jQmDiv'><table width='100%' border='0'   cellpadding='1' cellspacing='1'><tr><td colspan='2'><div id='jQtDiv'>{title}</div></td><td><div id='jQCloseIcon'></div></td></tr><tr><td width='30%'><div id='jQimgDiv'></div></td><td width='60%'><div id='jQBodyDiv'></div></td><td width='10%'></td></tr><tr><td colspan='3'><div id='jQButton'></div></td></tr></table><div>";
	var DIALOGFILTER="<div id='jQFilterDiv'></div>";
	$.fn.show=function(option)
	{
		var def={
			viewtype:1,//1=有模式 2=无模式
			width:300, //对话框宽度
			height:200,//对话框高度
			top:null,	//对话框所在
			left:null,	//左边位置
			viewclass:1, //1=只显示[确定] 2=显示[确定][取消] 3=显示[是][否][取消]
			title:'',   //标题
			text:''	//显示内容
		};
		option=$.extend(def,option);
		manPage=$(this);
		var _man={
		height:document.documentElement.clientHeight,
		width:document.documentElement.clientWidth
		};

		//页面加载
		manPage.append(DIALOGFILTER);
		manPage.append(DIALOGDATA);

		$("#jQFilterDiv").hide();

		//对话框覆盖层
		if(option.viewtype==1)//有模式对话框
		{
			
			$("#jQFilterDiv").css({width:_man.width,height:_man.height,top:'0px',left:'0px'});
			$("#jQFilterDiv").show();
		}

		//计算对话框的位置
		option.left=Math.floor(_man.width/2)-Math.floor(def.width);
		option.top=(Math.floor(_man.height/2)-Math.floor(def.height))-20;
	}
}
)(jQuery)