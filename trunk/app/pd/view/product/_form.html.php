<?
//产品分类
?>
<script src=".js/jquery.min.js" ></script>
<script>


</script>
<style>
/**
D4E8FC
**/
body{font-size:12px}
#mainDiv{width:700px;height:500px;border:1px solid #FF6600;margin:0 auto}
#leftDiv{width:500px;height:500px;float:left;clear:left;overflow:hidden;border-style: solid;border-width: 1px ;border-left-color:#fff;border-top-color:#fff;border-right-color:#D4E8FC;border-bottom-color :#fff;}
#rightDiv{width:200px;height:500px;float:right;clear:right;overflow:hidden;}
#rightDiv_top{width:230px;height:350px;border-style: solid;border-width: 1px ;border-left-color:#fff;border-top-color:#fff;border-right-color:#fff;border-bottom-color :#FF6600;}
#rightDiv_bottom{width:230px;height:150px;}
#bodyDiv{text-align:left;}
#AddCategorie{text-align:left}
#imgTitleBody{width:500px;height:25px;background-color:#FFF7E6;border-style: solid;border-width: 1px ;border-left-color:#fff;border-top-color:#fff;border-right-color:#fff;border-bottom-color :#FF6600;}/*background-image:url("./images/box690bg.gif")*/
.imgTitleTop{width:230px;height:25px;background-color:#FFF7E6;border-style: solid;border-width: 1px ;border-left-color:#fff;border-top-color:#fff;border-right-color:#fff;border-bottom-color :#FF6600;}/*background-image:url("./images/box260bg.gif")*/
</style>
<center>
<div id="mainDiv">
	<div id="leftDiv"><!--------为分类添加产品--------->
		 <div id="bodyDiv">
			 <div id="imgTitleBody"><span style="padding-left:20px;line-height:25px">产品</span></div>
		 </div>
	</div>
	<div id="rightDiv" align="top"><!--------添加分类--------->
		<div id="rightDiv_top">
			<div class="imgTitleTop"><span style="padding-left:10px;line-height:25px">产品分类</span></div>
		</div>
		<div id="rightDiv_bottom">
			<div class="imgTitleTop"><span style="padding-left:10px;line-height:25px">添加分类</span></div>
			<div id="AddCategorie">
				<input type="text" name="categorie" id="categorie"><input type='button' onclick="addCategorie()" value="添加">
			</div>
		</div>
	</div>
</div>
</center>