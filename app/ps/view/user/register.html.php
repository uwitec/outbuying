<?
/**
 * 用户注册
 */
?>
<style>
.uDiv{margin:0,0,5px,0;}
.uSpan{float:left;margin:0,0,0,5px}
label{color:red}
</style>
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
<form action="" method="POST" onsubmit="return checkForm();">
<center>
<div  style="width:300px">
<fieldset>
     <legend><?=Watt_I18n::trans("用户注册")?></legend>
         <br>
          <div class="uDiv">
              <span class="uSpan">
                  　　<label>*</label><?=Watt_I18n::trans("姓名")?>:
              </span>
              <span>
                  <input type="text" name="u_name" id="u_name">
              </span>
          </div>
          <div class="uDiv">
              <span class="uSpan">
                  　　<label>*</label><?=Watt_I18n::trans("昵称")?>:
              </span>
              <span>
                  <input type="text" name="u_nickname" id="u_nickname">
              </span>
          </div>
           <div class="uDiv">
              <span class="uSpan">
                  　　&nbsp;<?=Watt_I18n::trans("称谓")?>:
              </span>
              <span>
                  <input type="radio" name="u_sex" id="u_sex" value="1" checked><?=Watt_I18n::trans("先生")?>
                  <input type="radio" name="u_sex" id="u_sex" value="2"><?=Watt_I18n::trans("女士")?>
              </span>
          </div>
          <div class="uDiv">
              <span class="uSpan">
                  　　<label>*</label><?=Watt_I18n::trans("密码")?>:
              </span>
              <span>
                  <input type="password" name="u_pwd" id="u_pwd">
              </span>
          </div>
           <div class="uDiv">
              <span class="uSpan" >
                  <label>*</label><?=Watt_I18n::trans("密码重复")?>:
              </span>
              <span>
                  <input type="password" name="u_pwd_1" id="u_pwd_1">
              </span>
          </div>
          <div class="uDiv">
              <span class="uSpan" >
                  　　&nbsp;&nbsp;<?=Watt_I18n::trans("电话")?>
              </span>
              <span>
                  <input type="text" name="u_phone" id="u_phone">
              </span>
          </div>
          <div class="uDiv">
              <span class="uSpan" >
                  　　<label>*</label><?=Watt_I18n::trans("手机")?>
              </span>
              <span>
                  <input type="text" name="u_mobile" id="u_mobile">
              </span>
          </div>
          <div class="uDiv">
              <span class="uSpan" >
                  　　<label>*</label><?=Watt_I18n::trans("地址")?>
              </span>
              <span>
                  <input type="text" name="u_address" id="u_address">
              </span>
          </div>
          <div class="uDiv">
              <span class="uSpan" >
                  　　&nbsp;<?=Watt_I18n::trans("邮箱")?>
              </span>
              <span>
                  <input type="text" name="u_email" id="u_email">
              </span>
          </div>
          <div align="center" class="uDiv">
          <span>
          <input type="submit" value="<?=Watt_I18n::trans("注册")?>">
          　
          <input type="reset" value="<?=Watt_I18n::trans("取消")?>">
          <input type="hidden" name="op" value="1">
          </span>
          </div>
         
</fieldset>
</div>
</center>
</form>