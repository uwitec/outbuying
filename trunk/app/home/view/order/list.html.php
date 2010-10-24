<?
include( Pft_Config::getCfg('PATH_ROOT').'inc/view/header.inc.php' );
?>
<style>
body{font-size:10px}
/*FF902A*/
#main{padding-left:20px}
#productCenter{padding-left:3px;}
#productRight{padding-left:15px}
.pDiv{width:20px;float:left;overflow:hidden;}
.cDiv{width:162px;height:25px;/*border:1px solid #000;*/text-align:center;line-height:30px;background:url("./images/web-page/buy-title.png") repeat-x;/*background:#FF902A*/}
.plDiv{padding-left:3px}
.plDiv-buy{border:1px solid #FF6600;}
.imgDiv{width:72px;height:30px;float:left;overflow:hidden;background:url("./images/web-page/1-76+29.png");text-align:center;line-height:30px;color:#FFF;cursor:pointer;}
.sImgDiv{width:72px;height:30px;float:left;overflow:hidden;text-align:center;line-height:30px;color:#F7841C;font-weight :bold;background-color:#F1F1F1;cursor:pointer;clear:right}
#imgDivLine{width:100%;height:3px;background:url("./images/web-page/line-1.png") repeat-x;}
#pStar{color:RED}
#productRight{padding-left:5px}
#prducts{border-style: solid;border-width: 1px ;border-left-color:#fff;border-top-color:#fff;border-right-color:#fff;border-bottom-color :#D4E8FC;}

#buyTitle{width:100%;height:25px;/*background:url("./images/web-page/buy-title.png") repeat-x;*/}
#pZongji{text-align:left;padding-left:35px;padding-top:10px}
</style>
<script>
function qianhuan(obDiv1,obDiv2,bj)
{
	var _Div=document.getElementById(obDiv1);
	var _Div1=document.getElementById(obDiv2);
	_Div.className='imgDiv';
	_Div1.className='sImgDiv';
	if(bj==1)
	{
		isDisplay('n');
	}
	else
	{
		isDisplay('y');
	}
}
function isDisplay(isCheck)
{
	var pids=document.getElementsByName("p_id[]");
	if(isCheck=='y')
	{
		for(var i=0;i<pids.length;i++)
		{
			var caixi=document.getElementById("caixi_"+pids[i].value);
			caixi.style.display='';
		}
	}
	else
	{
		for(var i=0;i<pids.length;i++)
		{
			var caixi=document.getElementById("caixi_"+pids[i].value);
			caixi.style.display='none';
		}
	}
}
function addbuy(pId)
{
	var buyTable=document.getElementById("tr_"+pId);
	if(buyTable)
	{
		buyTable.style.display="";
	}
	
	var p_id=document.getElementById("p_id_"+pId);
	var k_id=document.getElementById("k_id_"+pId);
	var p_name=document.getElementById("p_name_"+pId);
	var p_price=document.getElementById("p_price_"+pId);
	var p_unit=document.getElementById("p_unit_"+pId);
	var p_info=document.getElementById("p_info_"+pId);
	var t_count=2;//添加行索引(默认)
	//添加菜品时 先判断 是否存在该菜品 如果存在 增加数量
	
	var isCunzai='n';
	var _obPid=document.getElementsByName("pId[]");
	if(_obPid.length>0)
	{
		t_count=t_count+1;
	}
	if(_obPid)
	{
		for(var i=0;i<_obPid.length;i++)
		{
			if(_obPid[i].value==pId)
			{
				isCunzai='y';
			}
		}
	}
	if(isCunzai=='y')
	{
		var _slHtml=document.getElementById("shuliang_"+pId);
		var _slXj=document.getElementById("xiaoji_"+pId);
		var _slInput=document.getElementById("pCount_"+pId);
		var pprice=document.getElementById("pPrice_"+pId);
		var pCalator=document.getElementById("pCalator_"+pId);
		var c_count=parseInt(_slInput.value);
		c_count++;
		_slInput.value=c_count;
		_slHtml.innerHTML=c_count;
		var xiaoji=Math.floor((parseFloat(c_count)*parseFloat(pprice.value))*100)/100;
		_slXj.innerHTML=xiaoji;
		pCalator.value=xiaoji;
	}
	else
	{
	var buyTable=document.getElementById("buyTable");
	var _tr=buyTable.insertRow(t_count);
	_tr.id="tr_"+p_id.value;
	var d1=_tr.insertCell(0);
	d1.id='pname_'+p_id.value;
	d1.innerHTML=p_name.value;
	var d1=_tr.insertCell(1);
	d1.id='pprice_'+p_id.value;
	d1.innerHTML=p_price.value;
	var d1=_tr.insertCell(2);
	d1.id='shuliang1_'+p_id.value;
	
	var ob=document.createElement("span");
	ob.id='shuliang_'+p_id.value;
	
	ob.innerHTML="1";
	d1.appendChild(ob);
	/*var ob=document.createElement("img");
	ob.id='imgAdd1_'+p_id.value;
	ob.src='./images/web-page/add-1.png';
	ob.title='增加该菜品';
	d1.appendChild(ob);
	var ob=document.createElement("img");
	ob.id='imgAdd2_'+p_id.value;
	ob.src='./images/web-page/add-2.png';
	ob.title='减少该菜品';
	d1.appendChild(ob);
	*/
	var ob=document.createElement("span");
	ob.id='imgAdd1_'+p_id.value;
	ob.title='增加菜品数量';
	ob.innerHTML="&nbsp;&nbsp;<span style='cursor:pointer' onclick='addTotal("+p_id.value+");'><FONT COLOR='RED'><strong>+</strong></FONT></span>";
	d1.appendChild(ob);
	var ob=document.createElement("span");
	ob.id='imgAdd1_'+p_id.value;
	ob.title='减少菜品数量';
	ob.innerHTML="&nbsp;&nbsp;<span style='cursor:pointer' onclick='delTotal("+p_id.value+");'><FONT COLOR='RED'><strong>-</strong></FONT></span>";
	d1.appendChild(ob);
	var d1=_tr.insertCell(3);
	d1.id='xiaoji_'+p_id.value;
	d1.innerHTML=p_price.value
	var d1=_tr.insertCell(4);
	d1.id='caozuo_'+p_id.value;
	d1.innerHTML="<div style='cursor:pointer' onclick='delBuy("+p_id.value+")'><img title='删除该菜品' src='./images/web-page/ico_delete.gif' /></div>";
	/*var ob=document.createElement("img");
	ob.id='img_'+p_id.value;
	ob.src='./images/web-page/ico_delete.gif';
	ob.title='删除该菜品';
	d1.appendChild(ob);
	*/
	var ob=document.createElement("input");
	ob.type='hidden';
	ob.name='pIdCheck[]';
	ob.id='pIdCheck_'+p_id.value;
	ob.value='y';
	d1.appendChild(ob);
	var ob=document.createElement("input");
	ob.type='hidden';
	ob.name='pId[]';
	ob.id='pID_'+p_id.value;
	ob.value=p_id.value;
	d1.appendChild(ob);
	var ob=document.createElement("input");
	ob.type='hidden';
	ob.name='kId[]';
	ob.id='kId'+p_id.value;
	ob.value=k_id.value;
	d1.appendChild(ob);
	var ob=document.createElement("input");
	ob.type='hidden';
	ob.name='pName[]';
	ob.id='pName_'+p_id.value;
	ob.value=p_name.value;
	d1.appendChild(ob);
	var ob=document.createElement("input");
	ob.type='hidden';
	ob.name='pPrice[]';
	ob.id='pPrice_'+p_id.value;
	ob.value=p_price.value;
	d1.appendChild(ob);
	var ob=document.createElement("input");
	ob.type='hidden';
	ob.name='pUnit[]';
	ob.id='pUnit_'+p_id.value;
	ob.value=p_unit.value;
	d1.appendChild(ob);
	var ob=document.createElement("input");
	ob.type='hidden';
	ob.name='pInfo[]';
	ob.id='pInfo_'+p_id.value;
	ob.value=p_info.value;
	d1.appendChild(ob);
	var ob=document.createElement("input");
	ob.type='hidden';
	ob.name='pCount[]';
	ob.id='pCount_'+p_id.value;
	ob.value=1;
	d1.appendChild(ob);
	var ob=document.createElement("input");
	ob.type='hidden';
	ob.name='pCalator[]';
	ob.id='pCalator_'+p_id.value;
	ob.value=p_price.value;
	d1.appendChild(ob);
	}
	
	cTotal();
	
}

function cTotal()
{
	var _total=0;
	var pCalator=document.getElementsByName("pCalator[]");
	var zTotal=document.getElementById("zTotal");
	var Total=document.getElementById("Total");
	for(var i=0;i<pCalator.length;i++)
	{
		_total=_total+parseFloat(pCalator[i].value);
	}
	_total=Math.floor(_total*100)/100;
	Total.value=_total;
	zTotal.innerHTML=_total;
	
}
function addTotal(pId)
{
	var _slHtml=document.getElementById("shuliang_"+pId);
		var _slXj=document.getElementById("xiaoji_"+pId);
		var _slInput=document.getElementById("pCount_"+pId);
		var pprice=document.getElementById("pPrice_"+pId);
		var pCalator=document.getElementById("pCalator_"+pId);
		var c_count=parseInt(_slInput.value);
		c_count++;
		_slInput.value=c_count;
		_slHtml.innerHTML=c_count;
		var xiaoji=Math.floor((parseFloat(c_count)*parseFloat(pprice.value))*100)/100;
		_slXj.innerHTML=xiaoji;
		pCalator.value=xiaoji;
		cTotal();
}
function delTotal(pId)
{
	var _slHtml=document.getElementById("shuliang_"+pId);
		var _slXj=document.getElementById("xiaoji_"+pId);
		var _slInput=document.getElementById("pCount_"+pId);
		var pprice=document.getElementById("pPrice_"+pId);
		var pCalator=document.getElementById("pCalator_"+pId);
		var c_count=parseInt(_slInput.value);
		if(c_count==1)
		{
			return false;
		}
		if(c_count>1)
		{
		   c_count--;
		  
		}
		else
		{
			c_count=1;
			
		}
		_slInput.value=c_count;
		_slHtml.innerHTML=c_count;
		 var xiaoji=Math.floor((parseFloat(c_count)*parseFloat(pprice.value))*100)/100;
		_slXj.innerHTML=xiaoji;
		pCalator.value=xiaoji;
		cTotal();
}
function delBuy(pId)
{
	var pIdCheck=document.getElementById("pIdCheck_"+pId);
	var buyTable=document.getElementById("tr_"+pId);
	buyTable.style.display="none";
	pIdCheck.value='n';
	var _slHtml=document.getElementById("shuliang_"+pId);
		var _slXj=document.getElementById("xiaoji_"+pId);
		var _slInput=document.getElementById("pCount_"+pId);
		var pprice=document.getElementById("pPrice_"+pId);
		var pCalator=document.getElementById("pCalator_"+pId);
		var c_count=0;
		
		_slInput.value=c_count;
		_slHtml.innerHTML=c_count;
		 var xiaoji=0;
		_slXj.innerHTML=xiaoji;
		pCalator.value=xiaoji;
		cTotal();
	
}
</script>
<body>
<center>
<div id="main">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="15%">
</td>
<td width="55%" valign="bottom" align="left">
<div class="plDiv">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
       <tr>
       <td align="left" valign="bottom">
        <div class="imgDiv" id='fristImgDiv' onclick="qianhuan('fristImgDiv','secondImgDiv','1');">菜系列表</div>
        <div  id="secondImgDiv" class="sImgDiv"  onclick="qianhuan('secondImgDiv','fristImgDiv','2');">菜系图示</div>
        
       </td>
       <td align="right">
       <input type="text" id="manSearch" name="manSearch"><img width="50px" height="25px" align="absmiddle" src="./images/web-page/sousuo.png" />
       </td>
       </tr>
       <tr style="display:none">
       <td colspan="2" valign="top">
       <div id="imgDivLine"></div>
       </td>
       </tr>
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
    
    <td width="50%" valign="top" align="left">
      <div id="productCenter">
       <div id="listBody" class="plDiv-buy">
       <?
       if(is_array($products) && count($products))
       {
       	foreach ($products as $row)
       	{
       		?>
       		
       		<div id="prducts" >
       		  <table width="100%" cellpadding="2" cellspacing="2" border="0">
       		    <tr>
       		      <td width="30%">
       		       <div>
       		       <div style="display:none" id="caixi_<?=$row["p_id"]?>"> <img align="absmiddle" src="<?=$row["p_img_link"]?>" /></div>
       		      
       		        <div><b><?=$row["p_name"]?></b></div>
       		       </div>
       		       <div id="hiddenData">
       		       <input type="hidden" id="p_id_<?=$row["p_id"]?>" name="p_id[]" value="<?=$row["p_id"]?>">
       		       <input type="hidden" id="k_id_<?=$row["p_id"]?>" name="k_id[]" value="<?=$row["k_id"]?>">
       		       <input type="hidden" id="p_name_<?=$row["p_id"]?>" name="p_name[]" value="<?=$row["p_name"]?>">
       		       <input type="hidden" id="p_price_<?=$row["p_id"]?>" name="p_price[]" value="<?=$row["p_price"]?>">
       		       <input type="hidden" id="p_info_<?=$row["p_id"]?>" name="p_info[]" value="<?=$row["p_info"]?>">
       		       <input type="hidden" id="p_unit_<?=$row["p_id"]?>" name="p_unit[]" value="<?=$row["p_unit"]?>">
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
       		      <td>
       		      单价:<?=$row["p_price"]?>/<?=$row["p_unit"]?>
       		      </td>
       		    </tr>
       		    <tr>
       		    <td colspan="2">
       		    <?=$row["p_info"]?>
       		    </td>
       		    <td align="right" valign="bottom">
       		     <span onclick="addbuy('<?=$row["p_id"]?>')">
       		     <img src="./images/web-page/btn_buy1.gif" />&nbsp;
       		     </span>
       		     <span><img src="./images/web-page/btn_keep.gif" /></span>
       		    </td>
       		    </tr>
       		  </table>
       		</div>
       		<?
       	}
       }
       ?>
       </div>
       <div id="imgBody">
       <!---------------存放图片的列表---------------->
       </div>
      </div>
    </td>
    <td width="35%" valign="top" align="center" >
    <div>
        <div id="buyTitle" align="center"><b>购物车</b></div>
        <div id="productRight">
          <div>
          <table width="100%" cellpadding="0" cellspacing="0" border="0" id="buyTable">
          
          <tr>
           <td width="30%"><b>菜品</td>
           <td width="15%"><b>单价</td>
           <td width="20%"><b>数量</td>
           <td width="20%"><b>小计</td>
           <td width="15%"><b>操作</td>
          </tr>
          <tr>
           
          </tr>
          </table>
          </div>
          <div id="pZongji">
          合计:<span id="zTotal"></span>
          <input type="hidden" id="Total" name="Total">
          </div>
       </div>
     </div>
    </td>
  </tr>
</table>
</div>
</center>
</body>
<?
include( Pft_Config::getCfg('PATH_ROOT').'inc/view/footer.inc.php' );
?>