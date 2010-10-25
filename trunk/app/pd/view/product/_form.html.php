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
</style>
<script src="js/jquery.min.js" ></script>
<script>
$(document).ready(function(){
	$.ajax({
	   type: "POST",
	   url: "?do=tools_uploader_upload",
	   data: "",
	   success: function(msg){
		 $('#imgDiv').append(msg);
		 
	   }
	}
	)
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
      <table width="100%" cellpadding="0" cellspacing="0" border="1">
        <tr>
          <td width="20%">名称</td>
          <td width="40%"><input type="text" id="p_name" name="p_name"></td>
          <td width="40%"></td>
        </tr>
         <tr>
          <td>价格</td>
          <td><input type="text" id="p_price" name="p_price"></td>
          <td></td>
        </tr>
        <tr>
          <td>市场价格</td>
          <td><input type="text" id="p_market_price" name="p_market_price"></td>
          <td></td>
        </tr>
         <tr>
          <td>会员价格</td>
          <td><input type="text" id="p_mem_price" name="p_mem_price"></td>
          <td></td>
        </tr>
         <tr>
          <td>单位</td>
          <td><input type="text" id="p_unit" name="p_unit"></td>
          <td></td>
        </tr>
         <tr>
          <td>产品图片</td>
          <td><div id="imgDiv"></div></td>
          <td></td>
        </tr>
         <tr>
          <td>产品说明</td>
          <td><textarea id="p_info" name="p_info" rows="2" cols="20"></textarea></td>
          <td></td>
        </tr>
      </table>
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
//include( Pft_Config::getCfg('PATH_ROOT').'inc/view/footer.inc.php' );
?>