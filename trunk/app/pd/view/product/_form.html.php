<?
//产品分类
?>
<?
//include( Pft_Config::getCfg('PATH_ROOT').'inc/view/header.inc.php' );
?>
<style>
._grid{border:1px solid #D4E8FC;}
._td{border-left-color:##D4E8FC;border-top-color:#fff;border-right-color:#fff;border-bottom-color :#fff;}
.pDiv{padding-left:10px;}
#listFenlei{height:200px}
</style>
<script src="js/jquery.min.js" ></script>
<script src="js/jquery.form.js" ></script>
<script>
$(document).ready(function(){
	var jQ=jQuery.noConflict();//解决load页面中的冲突
	jQ('#imgDiv').load('?do=tools_uploader_upload');//加载上传文件页面

	//添加产品
	jQ("#s_Submit").bind('click',function(){
		if(jQ("#p_name").val()=="")
		{
			jQ("#pPname").empty();
			jQ("#pPname").append("请输入名称");
			return false;
		}
		else
		{
			jQ("#pPname").empty();
		}
		if(jQ("#p_price").val()=="")
		{
			jQ("#pPrice").empty();
			jQ("#pPrice").append("请输入价格");
			return false;
		}
		else
		{
			jQ("#pPrice").empty();
		}
		if(jQ("#p_market_price").val()=="")
		{
			jQ("#pMarketPrice").empty();
			jQ("#pMarketPrice").append("请输入市场价格");
			return false;
		}
		else
		{
			jQ("#pMarketPrice").empty();
		}
		if(jQ("#p_mem_price").val()=="")
		{
			jQ("#pMemPrice").empty();
			jQ("#pMemPrice").append("请输入会员价格");
			return false;
		}
		else
		{
			jQ("#pMemPrice").empty();
		}
		if(jQ("#p_unit").val()=="")
		{
			jQ("#pUnit").empty();
			jQ("#pUnit").append("请输入单位");
			return false;
		}
		else
		{
			jQ("#pUnit").empty();
		}
		//取上传的图片(一个产品只能对应一个图片)
		var imgs=jQ("input[name*='dNames']");
		if(imgs.length>1)
		{
			jQ("#pImg").empty();
			jQ("#pImg").append("一个产品对应一个图片");
			return false;
		}
		else
		{
			jQ("#pImg").empty();
		}
		var options={
			target:'#ajaxProductDiv',
			url:'?do=pd_product_addCategories',
			type:'POST',
			success: function(){
				alert(jQ("#ajaxProductDiv").text());
			}
		};
		jQ("#pForm").ajaxForm(options);
		return false;


	});
	//添加分类
	jQ("#addCg").bind("click",function(){
		var kindFenlei=jQ("#kindFenlei").val();
		if(kindFenlei!='')
		{
			//调用AJAX 添加分类
			jQ.ajax(
			{
				type:"GET",
				url:"?do=pd_product_addCategories",
				data:"kindFenlei="+encodeURIComponent(kindFenlei),
				success: function (response){
					//jQ("#listFenlei")
					eval("var ob="+response);
					
					var _div="<div><input type='checkbox' name='kinds[]' id='kind_"+ob.k_id+"' value='"+ob.k_id+"'>"+ob.k_name+"</div>";
					jQ("#listFenlei").append(_div);
				}
			}
			);
		}
		
	});
	//添加标签
	jQ("#addBq").bind("click",function(){
		var biaoqian=jQ("#biaoqian").val();
		if(biaoqian!='')
		{
			//调用AJAX 添加分类
			jQ.ajax(
			{
				type:"GET",
				url:"?do=pd_product_addCategories",
				data:"biaoqian="+encodeURIComponent(biaoqian),
				success: function (response){
					//jQ("#listFenlei")
					eval("var ob="+response);
					
					var _div="<div><input type='checkbox' name='kinds[]' id='kind_"+ob.k_id+"' value='"+ob.k_id+"'>"+ob.k_name+"</div>";
					jQ("#listFenlei").append(_div);
				}
			}
			);
		}
	});
}
);
</script>
<table width="90%" align="center" cellpadding="0" cellspacing="0" class="_grid">
<tr>
<td width="70%" height="400px" valign="top" >
<!---------------添加产品----------------------->
<div class="pDiv">
  <div><b>添加产品</b></div>
  <div>
    <div>
    <form action="" method="get" id="pForm">
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
		<div id="ajaxProductDiv" style="display:none"></div>
        <input type="submit" style="display:none" id="Submit">
        <input type="hidden" name="op" value="1">
        <input type="button" id="s_Submit"   value="<?=Watt_I18n::trans("保存")?>"> 
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
	<div id="pFenlei">
		<div>
		产品分类
		</div>
		<div id="listFenlei">
			
		</div>
	</div>
	<div id="addFenlei">
		<div>
		 添加分类
		</div>
		<div>
		<input type="text" id="kindFenlei" name="kindFenlei">
		<input type="button" id="addCg" value="添加">
		</div>
	</div>
	<br>
	<div id="addBiaoqian">
		<div>
		添加标签
		</div>
		<div>
		<input type="text" id="biaoqian" name="biaoqian">
		<input type="button" value="添加" id="addBq">
		</div>
	</div>
</div>
</td>
</tr>
</table>
<?
//include( Pft_Config::getCfg('PATH_ROOT').'inc/view/footer.inc.php' );
?>