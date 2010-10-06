<?
include( Pft_Config::getCfg('PATH_ROOT').'inc/view/header.inc.php' );
?>
<style>
/*FF902A*/
#main{padding-left:20px}
#productCenter{padding-left:10px}
#productRight{padding-left:5px}
.pDiv{width:20px;float:left;overflow:hidden;}
.cDiv{width:170px;height:30px;border:1px solid #000;text-align:center;line-height:30px;background:#FF902A}
.plDiv{padding-left:10px}
.imgDiv{width:72px;height:30px;float:left;overflow:hidden;background:url("./images/1-76+29.png");text-align:center;line-height:30px;color:#FFF;cursor:pointer;}
.sImgDiv{width:72px;height:30px;float:left;overflow:hidden;text-align:center;line-height:30px;color:#F7841C;font-weight :bold;background-color:#F1F1F1;cursor:pointer;}
#imgDivLine{width:100%;height:3px;background:url("./images/line-1.png") repeat-x;}
#pStar{color:RED}
</style>
<script>
function qianhuan(obDiv1,obDiv2)
{
	var _Div=document.getElementById(obDiv1);
	var _Div1=document.getElementById(obDiv2);
	_Div.className='imgDiv';
	_Div1.className='sImgDiv';
}
</script>
<center>
<div id="main">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="15%">
</td>
<td width="55%" valign="top" align="left">
<div class="plDiv">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
       <tr>
       <td>
        <div class="imgDiv" id='fristImgDiv' onclick="qianhuan('fristImgDiv','secondImgDiv');">菜系列表</div>
        <div  id="secondImgDiv" class="sImgDiv"  onclick="qianhuan('secondImgDiv','fristImgDiv');">菜系图示</div>
        <div id="imgDivLine"></div>
       </td></tr>
       </table>
</div>
</td>
<td width="30%"></td>
</tr>
  <tr>
    <td width="15%" valign="top" align="left">
      <div id="productLeft" >
    <?
      if(count($list))
      {
	    foreach ($list as $row)
	    {
		 echo "<div class='cDiv'>".$row["k_name"]."</div>";
	    }
      }
?>
  </div>
    </td>
    
    <td width="55%" valign="top" align="left">
      <div id="productCenter">
       <div id="listBody">
       <?
       if(is_array($products) && count($products))
       {
       	foreach ($products as $row)
       	{
       		?>
       		
       		<div id="prducts" class="plDiv">
       		  <table width="100%" cellpadding="2" cellspacing="2" border="0">
       		    <tr>
       		      <td width="30%">
       		       <div>
       		        <b><?=$row["p_name"]?></b>
       		       </div>
       		      </td>
       		      <td width="30%">
       		       <div id="pStar">
       		        <?
       		        if($row["p_star"])
       		        {
       		        	for($i=0;$i<$row["p_star"];$i++)
       		        	{
       		        		echo "★";
       		        	}
       		        	for($i=0;$i<5-$row["p_star"];$i++)
       		        	{
       		        		echo "☆";
       		        	}
       		        }
       		        else 
       		        {
       		        	echo "☆☆☆☆☆";
       		        }
       		        ?>
       		        </div>
       		      </td>
       		      <td rowspan="2" align="right" width="40%">
       		       <div>
       		     <img src="./images/btn_buy.png" />&nbsp;
       		     
       		     </div>
       		     <div ><img src="./images/btn_keep.gif" /></div>
       		     <div>
       		     </div>
       		      </td>
       		    </tr>
       		    <tr>
       		    <td colspan="2">
       		    <?=$row["p_info"]?>
       		    </td>
       		    </tr>
       		    <tr>
       		    <td colspan="3"><hr /></td>
       		    </tr>
       		  </table>
       		</div>
       		<?
       	}
       }
       ?>
       </div>
       <div id="imgBody">
       
       </div>
      </div>
    </td>
    <td width="30%" valign="top" align="left">
       <div id="productRight">
          <div id="text-list">aaaa</div>
          <div id="img-list">bbbb</div>
       </div>
    </td>
  </tr>
</table>
</div>
</center>

<?
include( Pft_Config::getCfg('PATH_ROOT').'inc/view/footer.inc.php' );
?>