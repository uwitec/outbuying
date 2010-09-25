<?
/**
 * 用户注册
 */
?>
<script>
function checkForm()
{
	var u_name=document.getElementById("u_name");
	if(u_name.value=="")
	{
		alert("<?=Watt_I18n::trans("请输入姓名")?>");
		u_name.focus();
		return false;
	}
	var u_nickname=document.getElementById("u_nickname");
	if(u_nickname.value=="")
	{
		alert("<?=Watt_I18n::trans("请输入昵称")?>");
		u_nickname.focus();
		return false;
	}
	var u_pwd=document.getElementById("u_pwd");
	if(u_pwd.value=="")
	{
		alert("<?=Watt_I18n::trans("请输入密码")?>");
		u_pwd.focus();
		return false;
	}
	var u_pwd_1=document.getElementById("u_pwd_1");
	if(u_pwd_1.value=="")
	{
		alert("<?=Watt_I18n::trans("请输入密码重复")?>");
		u_pwd_1.focus();
		return false;
	}
	if(u_pwd.value!=u_pwd_1.value)
	{
		alert("<?=Watt_I18n::trans("两次输入的密码不相同")?>");
		u_pwd_1.value='';
		u_pwd_1.focus();
		return false;
	}
	var u_mobile=document.getElementById("u_mobile");
	if(u_mobile.value=="")
	{
		alert("<?=Watt_I18n::trans("请输入手机")?>");
		u_mobile.focus();
		return false;
	}
	var u_address=document.getElementById("u_address");
	if(u_address.value=="")
	{
		alert("<?=Watt_I18n::trans("请输入地址")?>");
		u_address.focus();
		return false;
	}
}
</script>
<style>
.nameDiv{width:100px;float:left}
.vDiv{width:150px;float:left}
.infoDiv{width:300px;float:left}
.mDiv{clear:both}
.mainDiv{margin-left:200px}
.submitDiv{padding-left:0px}
</style>
<form action="" method="POST" onsubmit="return checkForm();">
<center>
<div class='mainDiv'>
	<div class="mDiv">
		<div class="nameDiv"><?=Watt_I18n::trans("用户名")?>:</div>
		<div class="vDiv"><input type="text" name="u_name" id="u_name"></div>
		<div class="infoDiv"><div>请填写用户的名称</div></div>
	</div>
	<div class="mDiv">
		<div class="nameDiv"><?=Watt_I18n::trans("昵称")?>:</div>
		<div class="vDiv"><input type="text" name="u_nickname" id="u_nickname"></div>
		<div class="infoDiv"><div></div></div>
	</div>
	<div class="mDiv">
		<div class="nameDiv"><?=Watt_I18n::trans("称谓")?>:</div>
		<div class="vDiv">
		<input type="radio" name="u_sex" id="u_sex" value="1" checked><?=Watt_I18n::trans("先生")?>
        <input type="radio" name="u_sex" id="u_sex" value="2"><?=Watt_I18n::trans("女士")?>
		</div>
		<div class="infoDiv"><div></div></div>
	</div>
	<div class="mDiv">
		<div class="nameDiv"><?=Watt_I18n::trans("密码")?>:</div>
		<div class="vDiv"><input type="text" name="u_pwd" id="u_pwd"></div>
		<div class="infoDiv"><div></div></div>
	</div>
	<div class="mDiv">
		<div class="nameDiv"><?=Watt_I18n::trans("再次输入密码")?>:</div>
		<div class="vDiv"><input type="text" name="u_pwd_1" id="u_pwd_1"></div>
		<div class="infoDiv"><div></div></div>
	</div>
	<div class="mDiv">
		<div class="nameDiv"><?=Watt_I18n::trans("电话")?>:</div>
		<div class="vDiv"><input type="text" name="u_phone" id="u_phone"></div>
		<div class="infoDiv"><div></div></div>
	</div>
	<div class="mDiv">
		<div class="nameDiv"><?=Watt_I18n::trans("手机")?>:</div>
		<div class="vDiv"><input type="text" name="u_mobile" id="u_mobile"></div>
		<div class="infoDiv"><div></div></div>
	</div>
	<div class="mDiv">
		<div class="nameDiv"><?=Watt_I18n::trans("地址")?>:</div>
		<div class="vDiv"><input type="text" name="u_address" id="u_address"></div>
		<div class="infoDiv"><div></div></div>
	</div>
	<div class="mDiv">
		<div class="nameDiv"><?=Watt_I18n::trans("邮箱")?>:</div>
		<div class="vDiv"><input type="text" name="u_email" id="u_email"></div>
		<div class="infoDiv"><div></div></div>
	</div>
	<div  class="mDiv" >
	<div class='submitDiv'>
		<input type="submit" value="<?=Watt_I18n::trans("注册")?>">　
        <input type="reset" value="<?=Watt_I18n::trans("取消")?>">
        <input type="hidden" name="op" value="1">
	</div>
		
	</div>


</div>
</center>
</form>