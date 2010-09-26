<?
/**
 * 用户注册
 */
?>
<script>

function checkForm()
{
	if(!checkvalue('u_name'))
	{
		return false;
	}
	if(!checkvalue("u_nickname"))
	{
		return false;
	}
	if(!checkvalue("u_pwd"))
	{
		return false;
	}
	if(!checkvalue("u_pwd_1"))
	{
		return false;
	}
	if(!checkvalue("u_phone"))
	{
		return false;
	}
	if(!checkvalue("u_mobile"))
	{
		return false;
	}
	if(!checkvalue("u_email"))
	{
		return false;
	}
}
function checkvalue(obS)
{
	
	if(obS=='u_name')
	{
		document.getElementById("nameTr").style.backgroundColor ='';
		var u_name=document.getElementById("u_name");

		if(u_name.value.length<5 || u_name.value.length>20)
		{
			document.getElementById("nameImg").style.display='';
			document.getElementById("nameImg").src="./images/ico_delete.gif";
			document.getElementById("nameImg").title="错误";
			return false;
		}
		else if(!/^[A-Za-z0-9\u0391-\uFFE5-_]+$/.test(u_name.value))
		{
			document.getElementById("nameImg").style.display='';
			document.getElementById("nameImg").src="./images/ico_delete.gif";
			document.getElementById("nameImg").title="错误";
			return false;
		}
		else
		{
			document.getElementById('nameDiv').style.display='none';
			document.getElementById("nameImg").style.display='';
			document.getElementById("nameImg").src="./images/ico_check.gif";document.getElementById("nameImg").title="正确";
		}
		
	}
	if(obS=='u_nickname')
	{
		document.getElementById("nicknameTr").style.backgroundColor ='';
		var u_nickname=document.getElementById("u_nickname");
		if(u_nickname.value=='')
		{
			document.getElementById("nicknameImg").style.display='';
			document.getElementById("nicknameImg").src="./images/ico_delete.gif";
			document.getElementById("nicknameImg").title="错误";
			return false;
		}
		else if(!/^[A-Za-z0-9\u0391-\uFFE5-_]+$/.test(u_nickname.value))
		{
			document.getElementById("nicknameImg").style.display='';
			document.getElementById("nicknameImg").src="./images/ico_delete.gif";
			document.getElementById("nicknameImg").title="错误";
			return false;
		}
		else
		{
			document.getElementById('nicknameDiv').style.display='none';
			document.getElementById("nicknameImg").style.display='';
			document.getElementById("nicknameImg").src="./images/ico_check.gif";
			document.getElementById("nicknameImg").title="正确";
		}
	}
	if(obS=='u_pwd')
	{
		document.getElementById("pwdTr").style.backgroundColor ='';
		var yh_mima=document.getElementById("u_pwd").value;
		if(yh_mima=='')
		{
			document.getElementById("pwdImg").style.display='';
			document.getElementById("pwdImg").src="./images/ico_delete.gif";
			document.getElementById("pwdImg").title="错误";
			return false;
		}
		else if(yh_mima == '' || yh_mima.length<5 ||yh_mima.length<20 || !( /[0-9]+/.test(yh_mima) && /[a-zA-Z]+/.test(yh_mima) ) )
		{
			document.getElementById("pwdImg").style.display='';
			document.getElementById("pwdImg").src="./images/ico_delete.gif";
			document.getElementById("pwdImg").title="错误";
			return false;
		}
		else
		{
			document.getElementById('pwdDiv').style.display='none';
			document.getElementById("pwdImg").style.display='';
			document.getElementById("pwdImg").src="./images/ico_check.gif";
			document.getElementById("pwdImg").title="正确";
		}
	}
	if(obS=='u_pwd_1')
	{
		document.getElementById("pwd_1Tr").style.backgroundColor ='';
		var yh_mima=document.getElementById("u_pwd").value;
		var yh_mima2=document.getElementById("u_pwd_1").value;
		if(yh_mima!=yh_mima2)
		{
			document.getElementById("pwd_1Img").style.display='';
			document.getElementById("pwd_1Img").src="./images/ico_delete.gif";
			document.getElementById("pwd_1Img").title="错误";
			return false;
		}
		else
		{
			document.getElementById('pwd_1Div').style.display='none';
			document.getElementById("pwd_1Img").style.display='';
			document.getElementById("pwd_1Img").src="./images/ico_check.gif";
			document.getElementById("pwd_1Img").title="正确";
		}
		
	}
	if(obS=="u_phone")
	{
		document.getElementById("phoneTr").style.backgroundColor ='';
		var u_phone=document.getElementById("u_phone").value;
		if(!/^(([0\+]\d{2,3}-)?(0\d{2,3})-)?(\d{7,8})(-(\d{3,}))?$|^1[35]\d{9}$/.test(u_phone))
		{
			document.getElementById("phoneImg").style.display='';
			document.getElementById("phoneImg").src="./images/ico_delete.gif";
			document.getElementById("phoneImg").title="错误";
			return false;
		}
		else
		{
			document.getElementById('phoneDiv').style.display='none';
			document.getElementById("phoneImg").style.display='';
			document.getElementById("phoneImg").src="./images/ico_check.gif";
			document.getElementById("phoneImg").title="正确";
		}
	}
	if(obS=="u_mobile")
	{
		document.getElementById("mobileTr").style.backgroundColor ='';
		var u_mobile=document.getElementById("u_mobile").value;
		if(u_mobile=='')
		{
			document.getElementById("mobileImg").style.display='';
			document.getElementById("mobileImg").src="./images/ico_delete.gif";
			document.getElementById("mobileImg").title="错误";
			return false;
		}
		else if(!/^((\(\d{3}\))|(\d{3}\-))?(13|15|18)\d{9}$/.test(u_mobile))
		{
			document.getElementById("mobileImg").style.display='';
			document.getElementById("mobileImg").src="./images/ico_delete.gif";
			document.getElementById("mobileImg").title="错误";
			return false;
		}
		else
		{
			document.getElementById('mobileDiv').style.display='none';
			document.getElementById("mobileImg").style.display='';
			document.getElementById("mobileImg").src="./images/ico_check.gif";
			document.getElementById("mobileImg").title="正确";
		}
	}
	if(obS=="u_address")
	{
		document.getElementById("addressTr").style.backgroundColor ='';
	}
	if(obS=="u_email")
	{
		document.getElementById("emailTr").style.backgroundColor ='';
		var u_email=document.getElementById("u_email").value;
		if(!/^\w+([-+.]\w+)*@\w+([-.]\\w+)*\.\w+([-.]\w+)*$/.test(u_email))
		{
			document.getElementById("emailImg").style.display='';
			document.getElementById("emailImg").src="./images/ico_delete.gif";
			document.getElementById("emailImg").title="错误";
			return false;
		}
		else
		{
			document.getElementById('emailDiv').style.display='none';
			document.getElementById("emailImg").style.display='';
			document.getElementById("emailImg").src="./images/ico_check.gif";
			document.getElementById("emailImg").title="正确";
		}
	}
	
}
function viewtixing(strOb)
{
	document.getElementById(strOb+"Div").style.display='';
	document.getElementById(strOb+"Tr").style.backgroundColor ='#F4FCFE';
}
function reloadcode()
{
	document.getElementById("safecode").src="?do=ps_user_yanzhengma";
}
</script><!--FF6600-->
<style>
.mDiv{width:700px;height:400px;border-style: solid;border-width: 1px thin;border-left-color:#FF6600;border-top-color:#FF6600;border-right-color:#FF6600;border-bottom-color :#FF6600;}
.infoDiv{width:380px;height:25px;border-width: 1px thin;font-size:12px;text-align:left;line-height:25px;border-style: solid;background-color :#F4FCFE;border-left-color:#D4E8FC;border-top-color:#D4E8FC;border-right-color:#D4E8FC;border-bottom-color :#D4E8FC;}
.leftDiv{padding-left:5px}
td{height:31px}
.mTr{background-color :#F4FCFE;}
</style>
<form action="" method="POST" onsubmit="return checkForm();">
<center>
<div  class="mDiv" align="center">
<br>
<div class="leftDiv">
<table width="100%" border=0 cellpadding="0" cellspacing="0">
	<tr id="nameTr">
	
	<td width='15%'><?=Watt_I18n::trans("会员名")?>:</td>
	<td width='30%'><input type="text" name="u_name" id="u_name" onfocus="viewtixing('name')" onblur="checkvalue('u_name')" >&nbsp;<img src='' id="nameImg" style="display:none" /></td>
	<td width="55%"><div class='infoDiv' id="nameDiv" style="display:none">&nbsp;5-20个字符,只允许 数字、字母、汉字、-_,推荐使用汉字.</div></td>
	
	</tr>
	<tr id="nicknameTr">
	<td><?=Watt_I18n::trans("昵称")?>:</td>
	<td><input type="text" name="u_nickname" id="u_nickname" onfocus="viewtixing('nickname')" onblur="checkvalue('u_nickname')">&nbsp;<img src='' id="nicknameImg" style="display:none" /></td>
	<td><div class='infoDiv' id="nicknameDiv" style="display:none">&nbsp;请输入您的昵称.</div></td>
	</tr>
	<tr id="sexTr">
	<td><?=Watt_I18n::trans("称谓")?>:</td>
	<td><input type="radio" name="u_sex" id="u_sex" value="1"><?=Watt_I18n::trans("先生")?>
        <input type="radio" name="u_sex" id="u_sex" value="2" checked ><?=Watt_I18n::trans("女士")?>
		<img src='' id="sexImg" style="display:none"/>
		</td>
	<td><div class='infoDiv' id="nicknameDiv" style="display:none">&nbsp;请选择您的称谓.</div></td>
	</tr>
	<tr id="pwdTr">
	<td><?=Watt_I18n::trans("密码")?>:</td>
	<td><input type="password" name="u_pwd" id="u_pwd" onfocus="viewtixing('pwd')" onblur="checkvalue('u_pwd')">&nbsp;<img src='' id="pwdImg" style="display:none"/></td>
	<td><div class='infoDiv' id="pwdDiv" style="display:none">&nbsp;请输入您的密码,5-20个字符,只允许 数字、字母.</div></td>
	</tr>
	<tr id="pwd_1Tr">
	<td><?=Watt_I18n::trans("确认密码")?>:</td>
	<td><input type="password" name="u_pwd_1" id="u_pwd_1" onfocus="viewtixing('pwd_1')" onblur="checkvalue('u_pwd_1')">&nbsp;<img src='' id="pwd_1Img" style="display:none"/></td>
	<td><div class='infoDiv' id="pwd_1Div" style="display:none">&nbsp;请再次输入您的密码.</div></td>
	</tr>
	<tr id="phoneTr">
	<td><?=Watt_I18n::trans("电话")?>:</td>
	<td><input type="text" name="u_phone" id="u_phone" onfocus="viewtixing('phone')" onblur="checkvalue('u_phone')">&nbsp;<img src='' id="phoneImg" style="display:none" /></td>
	<td><div class='infoDiv' id="phoneDiv" style="display:none">&nbsp;请留下您的电话号码且必须符合电话号码的格式,以便于及时沟通.</div></td>
	</tr>
	<tr id="mobileTr">
	<td><?=Watt_I18n::trans("手机")?>:</td>
	<td><input type="text" name="u_mobile" id="u_mobile" onfocus="viewtixing('mobile')" onblur="checkvalue('u_mobile')">&nbsp;<img src='' id="mobileImg" style="display:none"/></td>
	<td><div class='infoDiv' id="mobileDiv" style="display:none">&nbsp;请留下您的手机号码且必须符合手机号码的格式,以便于及时沟通.</div></td>
	</tr>
	<tr id="addressTr">
	<td><?=Watt_I18n::trans("地址")?>:</td>
	<td><input type="text" name="u_address" id="u_address" onfocus="viewtixing('address')" onblur="checkvalue('u_address')">&nbsp;<img src='' id="addressImg" style="display:none"/></td>
	<td><div class='infoDiv' id="addressDiv" style="display:none">&nbsp;请正确输入您的详细地址,以便于能够及时找到</div></td>
	</tr>
	<tr id="emailTr">
	<td><?=Watt_I18n::trans("邮箱")?>:</td>
	<td><input type="text" name="u_email" id="u_email" onfocus="viewtixing('email')" onblur="checkvalue('u_email')">&nbsp;<img src='' id="emailImg" style="display:none"/></td>
	<td><div class='infoDiv' id="emailDiv" style="display:none">&nbsp;请输入您的邮箱且必须符合邮箱的格式</div></td>
	</tr>
	<tr id="emailTr">
	<td><?=Watt_I18n::trans("验证码")?>:</td>
	<td><input type="text" name="u_yanzhengma" id="u_yanzhengma" onfocus="viewtixing('email')" onblur="checkvalue('u_yanzhengma')">&nbsp;<img src='' id="yanzhengmaImg" style="display:none"/>
	<img src="?do=ps_user_yanzhengma"  id="safecode" onclick="reloadcode();"/>
	</td>
	<td><div class='infoDiv' id="yanzhengmaDiv" style="display:none">&nbsp;请输入您的邮箱且必须符合邮箱的格式</div></td>
	</tr>
	<tr>
		<td colspan=3 align='left'>
		<br>
		<div style="padding-left:100px">
			<input type="submit" value="<?=Watt_I18n::trans("注册")?>">
          <input type="reset" value="<?=Watt_I18n::trans("取消")?>">
          <input type="hidden" name="op" value="1">
		 </div>
		</td>
	</tr>
</table>
<div>
</div>
</center>
</form>