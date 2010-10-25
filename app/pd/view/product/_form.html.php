<?
//产品分类
?>
<?
include( Pft_Config::getCfg('PATH_ROOT').'inc/view/header.inc.php' );
?>
<style>
._grid{border:1px solid #D4E8FC;}
._td{border-left-color:##D4E8FC;border-top-color:#fff;border-right-color:#fff;border-bottom-color :#fff;}
.pDiv{padding-left:10px;}
</style>
<script src="js/jquery.min.js" ></script>
<script>
$(document).ready(function(){
	/*$.ajax({
	   type: "POST",
	   url: "?do=tools_uploader_upload",
	   data: "",
	   success: function(msg){
		 $('#imgDiv').append(msg);
		 
	   }
	}
	);
	*/
	
	/*$("#s_Submit").bind('click',function(){
		alert();
		alert($("#p_name").val());
		
	});
	*/
	$('#imgDiv').load('?do=tools_uploader_upload');//加载上传文件页面
}
);
function saveProduct()
{
	var p_name=document.getElementById("p_name");
	var pPname=document.getElementById("pPname");
	if(p_name.value=="")
	{
		pPname.innerHTML="请输入名称";
		return false;
	}
	else
	{
		pPname.innerHTML="";
	}
	var p_price=document.getElementById("p_price");
	var pPrice=document.getElementById("pPrice");
	if(p_price.value=="")
	{
		pPrice.innerHTML="请输入价格";
		return false;
	}
	else
	{
		pPrice.innerHTML="";
	}
	var p_market_price=document.getElementById("p_market_price");
	var pMarketPrice=document.getElementById("pMarketPrice");
	if(p_market_price.value=="")
	{
		pMarketPrice.innerHTML="请输入市场价格";
		return false;
	}
	else
	{
		pMarketPrice.innerHTML="";
	}
	var p_mem_price=document.getElementById("p_mem_price");
	var pMemPrice=document.getElementById("pMemPrice");
	if(p_mem_price.value=="")
	{
		pMemPrice.innerHTML="请输入会员价格";
		return false;
	}
	else
	{
		pMemPrice.innerHTML="";
	}
	var p_unit=document.getElementById("p_unit");
	var pUnit=document.getElementById("pUnit");
	if(p_unit.value=="")
	{
		pUnit.innerHTML="请输入会员价格";
		return false;
	}
	else
	{
		pUnit.innerHTML="";
	}
	
}
</script>
<table width="90%" align="center" cellpadding="0" cellspacing="0" class="_grid">
<tr>
<td width="70%" height="400px" valign="top" >
<!---------------添加产品----------------------->
<div class="pDiv">
  <div><b>添加产品</b></div>
  <div>
    <div>
    <form action="" method="get">
      <table width="100%" cellpadding="2" cellspacing="2" border="0">
        <tr>
          <td width="20%">名称</td>
          <td width="40%"><input type="text" id="p_name" name="p_name"></td>
          <td width="40%"><span id="pPname"></span></td>
        </tr>
         <tr>
          <td>价格</td>
          <td><input type="text" id="p_price" name="p_price"></td>
          <td><span id="pPrice"></span></td>
        </tr>
        <tr>
          <td>市场价格</td>
          <td><input type="text" id="p_market_price" name="p_market_price"></td>
          <td><span id="pMarketPrice"></span></td>
        </tr>
         <tr>
          <td>会员价格</td>
          <td><input type="text" id="p_mem_price" name="p_mem_price"></td>
          <td><span id="pMemPrice"></span></td>
        </tr>
         <tr>
          <td>单位</td>
          <td><input type="text" id="p_unit" name="p_unit"></td>
          <td><span id="pUnit"></span></td>
        </tr>
         <tr>
          <td>产品图片</td>
          <td><div id="imgDiv"></div></td>
          <td><span id="pImg"></span></td>
        </tr>
         <tr>
          <td>产品说明</td>
          <td><textarea id="p_info" name="p_info" rows="2" cols="30"></textarea></td>
          <td><span id="pInfo"></span></td>
        </tr>
        <tr>
        <td colspan="3" align="center">
        <input type="submit" style="display:none" id="Submit">
        <input type="hidden" name="op" value="1">
        <input type="button" id="s_Submit" onclick="saveProduct();"  value="<?=Watt_I18n::trans("保存")?>"> 
        </td>
        </tr>
      </table>
      </form>
    </div>
  </div>
</div>
</td>
<td width="30%" height="400px" valign="top" class="_grid _td">
<div  class="pDiv">
aaaaaaaaa
</div>
</td>
</tr>
</table>
<?
include( Pft_Config::getCfg('PATH_ROOT').'inc/view/footer.inc.php' );
?>