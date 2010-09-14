<?
class Pft_Util_Grid_Db
{
	//private		$sql;
	var			$z_language;								//语种数组
	var			$sqlstring;									//解析后的 SQL数组
	var			$html_style;								//样式
	var			$nub_str	;									//记录自增
	var			$error;										//记录错误
	var			$full_html;									//全部的html
	var			$originally_sql;								//最初的SQL 语句
	var			$temp_sql;									//临时存放的SQL语句
	var			$total_num;								//总数据条数
	var			$originally_title;							//最初的	表头
	var			$request_uri;								//第一次 解析后存放的URL
	var			$originally_uri;								//原始的URL
	var			$return_type;								//返回类型 FALSE 为完整HTML 1=>分开的HTML 2=>数组
	var			$ex_exe_var		=	'#*#';			//运行函数的分割符号
	var			$word_type			=	'UTF-8';				//SQL语句中的字符类型
	var			$searches_time;							//时间类型检索
	var			$time_type			='UNIX';							//时间类型
	var			$lock_table			='OFF';							//是否锁定表格
	var			$csv_dump			='OFF';							//是否导出csv格式表格

	var			$html_title;									//基本元素的 表头数据
	var			$searches_input;							//基本元素的 检索数据
	var			$link_page;									//基本元素的 分页数据
	var			$pure_data;								//基本元素的 数据库数据
	var 		$full_data;								//全部的数据

	var			$finally_data_arr;									//最终 的返回数组
	//var         $db;                                   // 数据库连接
	var $tiaoshi;
	var $format;//财务格式化输出
	var $sys_search=true;//控制显示查询头
	/**
	 * 说明
	 *
	 */
	function __construct($language='')				//__construct也不能用		语言
	{
		global $db;
		$this->db = $db;
		if ($language&&is_array($language))
		{
			$this	->	 z_language	 =$language;
		}
		else
		{
			$this -> language_type();
		}
		$this -> parse_uri();
		$remove_arr = array(
		'field_name'=>'',
		'range'=>'',
		'nowpg'=>'',
		'searches_name'=>'',
		'searches_time'=>'',
		'searches_start'=>'',
		'nowpg_start'=>'',
		'full_searches'=>'',
		'relat_searches'=>'',
		'contain_searches'=>''
		);
		$this -> originally_uri =  $this -> remove_url($remove_arr);
	} // end func

	/**
	 * 说明
	 *	语种
	 */
	private function language_type()
	{
		$this	->	 z_language		=	array(
		'高级检索'		=>	Pft_I18n::trans( 'DBGRID_GAOJIJIANSUO' ),
		'首页'			=>	Pft_I18n::trans( 'DBGRID_SHOUYE' ),
		'上一页'		=>	Pft_I18n::trans( 'DBGRID_SHANGYIYE' ),
		'下一页'		=>	Pft_I18n::trans( 'DBGRID_XIAYIYE' ),
		'尾页'			=>	Pft_I18n::trans( 'DBGRID_WEIYE' ),
		'页'			=>	Pft_I18n::trans( 'DBGRID_YE' ),
		'共'			=>	Pft_I18n::trans( 'DBGRID_GONG' ),
		'项'			=>	Pft_I18n::trans( 'DBGRID_XIANG' ),
		'跳转到'		=>	Pft_I18n::trans( 'DBGRID_TIAOZHUANDAO' ),
		'每页'			=>	Pft_I18n::trans( 'DBGRID_MEIYE' ),
		'返回初始状态'	=>	Pft_I18n::trans( 'DBGRID_CHUSHI' ),
		'隐藏检索'		=>	Pft_I18n::trans( 'DBGRID_YCJIANSUO' ),
		'精确查询'		=>	Pft_I18n::trans( 'DBGRID_JQCHAXUN' ),
		'关联查询'		=>	Pft_I18n::trans( 'DBGRID_GLCHAXUN' ),
		'检索时间格式'	=>	Pft_I18n::trans( 'DBGRID_JSSHIJIANGESHI' ),
		'检索'			=>	Pft_I18n::trans( 'DBGRID_JIANSUO' ),
		'从'			=>	Pft_I18n::trans( 'DBGRID_CONG' ),
		'到'			=>	Pft_I18n::trans( 'DBGRID_DAO' ),
		'小计'			=>	Pft_I18n::trans( '小计' ),
		'合计'			=>	Pft_I18n::trans( '合计' )
		);
		/*$this	->	 z_language		=	array(
		'高级检索'		=>	'高级检索',
		'首页'			=>	'首页',
		'上一页'		=>	'上一页',
		'下一页'		=>	'下一页',
		'尾页'			=>	'尾页',
		'页'			=>	'页',
		'共'			=>	'共',
		'项'			=>	'项',
		'跳转到'		=>	'跳转到',
		'每页'			=>	'每页',
		'返回初始状态'	=>	'返回初始状态',
		'隐藏检索'		=>	'隐藏检索',
		'精确查询'		=>	'精确查询',
		'关联查询'		=>	'关联查询',
		'检索时间格式'	=>	'检索时间格式',
		'检索'			=>	'检索',
		'从'			=>	'从',
		'到'			=>	'到'
		);*/
		/*$this	->	 z_language		=	array(
		'高级检索'	=>	'高级检索',
		'首页'			=>	'first',
		'上一页'		=>	'pre',
		'下一页'		=>	'next',
		'尾页'			=>	'last',
		'页'			=>	'page',
		'共'			=>	'together',
		'项'			=>	'item',
		'跳转到'		=>	'jump',
		'每页'			=>	'page',
		'返回初始状态'	=>	'返回初始状态',
		'隐藏检索'	=>	'隐藏检索',
		'精确查询'	=>	'精确查询',
		'关联查询'	=>	'关联查询',
		'检索时间格式'	=>	'检索时间格式',
		'检索'			=>	'检索',
		'从'			=>	'从',
		'到'			=>	'到'
		);*/
	} // end func

	/**
	 * 说明
	 * 样式
	 */
	function html_style($style	=	false)
	{
		$this	->	 html_style['table']	 	=	' align=center  border="0" cellpadding="0" cellspacing="1"  class="grid" ';
		$this	->	 html_style['title']		=	' class="GridTH" height=25';
		$this	->	 html_style['data']		=	'bgcolor=#ffffff height="20"';
		$this	->	 html_style['searches_title']	=	'';
		$this	->	 html_style['searches_content']	=	'bgcolor=#ffffff ';
		$this	->	 html_style['page']	=	'bgcolor=#ffffff';
		if (is_array($style))
		{
			foreach ($style as $key => $var)
			{
				$this	->	 html_style[$key]	=	$style[$key];
			}
		}
	} // end func



	/**
	 * 说明
	 * 解析url
	 */
	private function parse_uri()
	{
		$this -> request_uri = array();
		$url_arr = parse_url($_SERVER['REQUEST_URI']);
		$this -> request_uri['path'] = $url_arr['path'];

		if ($url_arr['query'])
		{
			//parse_str($ex[1],$urljq);
			$urljq = explode("&",$url_arr['query']);
			foreach ($urljq as $key => $value)
			{
				$this -> request_uri['query'][] = $value;
			}
		}
	} // end func






	/**  remove
	 * 说明 剔除URL中不需要的值
	 */
	private function remove_url($remove_arr	 =	false)
	{
		if ($remove_arr&&!is_array($remove_arr))
		{
			return false;
		}
		if ($remove_arr)
		{
			//$xy_getarr = array_diff_key($_GET, $remove_arr);		//PHP 5 >= 5.1.0RC1/用GET方法剔除
			/*/
			$get_keys	=	array_keys($_GET);

			$xch_keys	=	array_keys($remove_arr);
			$sxd			=	array_diff($get_keys,$xch_keys);

			foreach ($sxd as $key=>$var)
			{
			$jfget	[$var] =	$_GET[$var];
			}
			$xy_getarr	=	$jfget;
			/*/
			//这个剔除不了数组.对此类还能用.凑合着吧.
			$xy_getarr	=	$this -> request_uri['query'];
			if (is_array($xy_getarr))
			{

				foreach ($xy_getarr as $key => $var)
				{
					$ex	=	explode("=", $var);
					$zex	=	explode('%5', $ex[0]);
					$zex2	=	explode('[', $ex[0]);
					if (array_key_exists($ex[0],$remove_arr)||array_key_exists($zex[0],$remove_arr)||array_key_exists($zex2[0],$remove_arr))
					{
						unset($xy_getarr[$key]);
					}
				}
			}

		}
		else
		{
			$xy_getarr = $_GET;
		}
		if (!is_array($xy_getarr)) {
			$xy_getarr	=	array();
		}
		$url_h	=	'';
		foreach ($xy_getarr as $key => $var)
		{
			$url_h	.=	$var.'&';
		}
		$url_h = rtrim($url_h,"&");
		/*/
		foreach ($xy_getarr as $key => $var)
		{
		$url_h	.=	$key."=".$var.'&';
		}
		$url_h = rtrim($url_h,"&");
		echo $url_h."<br>";
		/*/
		//$url_h = http_build_query($xy_getarr);							//PHP 5
		return $first_url = $this -> request_uri['path'].'?'.$url_h;
	} // end func

	/**
	 * 功能：//添加新功能,用第二个参数来传所有的值
	 * Tony---Fri Mar 16 20:34:11 CST 2007----20:34:11
	 */
	public function NewDb( $sql , $he , $page=false , $type='',$format1=false )
	{
		Pft_Debug::addInfoToDefault('', 'startUseReadonlyDb');
		Pft_Db::startUseReadonlyDb();
		
		try
		{
			if($format1)
			{
				$this->format=$format1;
			}
			else 
			{
				$this->format=false;
			}
			$error_self	=	error_reporting(E_ALL ^ E_NOTICE);
				foreach ( $he as $key=>$v )
				{
					$title[$key]	=	$v[0];
					if ($v[1])
					{
						$field[$key]	=	$v[1];
					}
					if ($v[2])
					{
						$data[$key]		=	$v[2];
					}
					if ($v[3])
					{
						$field_t[$key]	=	$v[3];
					}
					if ($v[4])
					{
						$ti[$key]	=	$v[4];
					}
				}
			
			if ($ti)
			{
				$this->addTot( $ti ,$this->heji_type );
			}
			
			$result = $this->simple( $sql , $page , $title , $field ,$data ,$field_t ,$type,"#*#",'', $format1);
			Pft_Debug::addInfoToDefault('', 'endUseReadonlyDb');
			Pft_Db::endUseReadonlyDb();
			
			return $result;
		}
		catch(Exception $e)
		{
			Pft_Db::endUseReadonlyDb();
			throw $e;
		}
	}


	/**
	 * 功能：计算没列合计
	 * Tony---Tue Apr 03 14:17:18 CST 2007----14:17:18
	 */
	public function addTot( $ti=false , $type=false )
	{
		if ($ti)
		{
			$this->listtot	=	$ti;
		}
		
		if (!$type)
		{
			$this->xs_xiaoji	=	true;
			$this->xs_heji		=	true;
		}
		elseif ($type==1||$type=='xiaoji')
		{
			$this->xs_xiaoji	=	true;
		}
		elseif($type==2||$type=='heji') 
		{
			$this->xs_heji		=	true;
		}
		elseif ( $type==3 )
		{
			$this->last_heji	=	true;
		}
		$this->heji_type	=	$type;
	}
	
	/**
	 * 功能：合计
	 * Tony---Tue Apr 03 15:26:56 CST 2007----15:26:56
	 */
	private function totList($getdata)
	{
		
		
		if ($this->xs_heji)
		{
			
			
			//$this->tot_arr=$this->GetAll($this->sqlstring);
			$retudb	=	$this	->	GetAll ($this -> combination(array("LIMIT")));
			//$retudb=Pft_Db::getDb()->getAll($this -> combination(array("LIMIT")));
			
			//$retudb	=	$this	->	GetAll ($this->sqlstring);
			
			
			$this->tot_arr	=	$this->execute_data( $retudb , $getdata , true );
		}
	}


	//修改页数
	public function setpage( $page )
	{
		$this	->	 is_fanye_tiao	=	$page;
	}

	public function simple($sql,$perpg = false,$title = false,$searches_field = false,$getdata = false,$searches_time = false,$type = false,$exp_exe = "#*#",$style = false,$format1=false)
	{
		if($this->format)
		{
			
		}
		else 
		{
			$this->format=$format1;
		}
		$error_self	=	error_reporting(E_ALL ^ E_NOTICE);
		$this	->	 ex_exe_var		=	$exp_exe;
		$this	->	 return_type		= $type;
		$this	->	 searches_time	=	$searches_time;
		$this	->	 html_style($style);
		$this	->	 sqlstring = $this -> parse_sql($sql);
		
		$perpg	=	$perpg?$perpg:18;
		$this->setpage( $perpg );										//为了只有一页就不显示翻页条
		if (!$title)
		{
			return	$this	->	parse_getdata($getdata);
		}
		else
		{
			$title	=	$this	->	parse_width($title);
			if (!$searches_field)
			{
				$searches_field	=	$title;
			}
			$getdata	=	$this->jiexiDateList( $title , $getdata );
		}
		
		
		if ($this->xs_heji||$this->xs_xiaoji)
		{
			$this->format_tot	=	$this->jiexiDateList( $title , $this->listtot );
			//if ($this->xs_heji)
				//$this->totList( $this->format_tot );//注释,john改写 2007-7-27 查询时 如果在这里计算合计错误
		}
		$this -> judge($perpg,$title,$searches_field,$getdata,$type);
		//jute
		return	$this	->	finally_data_arr;
	} // end func

	
	/**
	 * 功能：把数据的执行数组解析出来
	 * Tony---Tue Apr 03 16:06:47 CST 2007----16:06:47
	 */
	private function jiexiDateList( $title , $getdata=false )
	{
		if (!$getdata)
		{																									//未填写显示数据
			foreach ($title as $key=>$value)
			{
				$exz		=	explode(".", $value);
				$pure		=	$exz[1]?$exz[1]:$exz[0];
				$pure		=	str_replace("`","",$pure);
				$getdata[$pure]	=	'';
			}
		}
		else
		{
			if (!array_key_exists('vastroc',$getdata))
			{																							//填写和标题不同的数据数组
				foreach ($title as $key=>$value)
				{
					$exz		=	explode(".", $value);
					$pure		=	$exz[1]?$exz[1]:$exz[0];
					$pure		=	str_replace("`","",$pure);
					if ($getdata[$key])
					{
						$roc[$key]	=	$getdata[$key];
					}
					else
					{
						$roc[$pure]	=	'';
					}
				}
				$getdata	=	$roc;
			}
			else
			{																							//填写和标题无关的数据数组
				unset($getdata['vastroc']);
			}
		}
		return $getdata;
	}

	/**
	 * 说明
	 * 解吸title中的列样式
	 */
	private function parse_width($title_arr)
	{
		foreach ($title_arr as $key => $value)
		{
			$ex_t		=	explode("*", $value);
			$this	->	width_html[$key]	=	isset($ex_t[1])?$ex_t[1]:'';
			$new_title[$key]	=	$ex_t[0];
		}
		//为了末页显示合计
		//$this->parse_hou_title	=	$new_title;
		return	$new_title;
	} // end func


	public function getAllData()
	{
		return $this->GetAll("SELECT *   FROM (".$this->combination().") as roc ");
	}
	/**
	 * 说明
	 * 判断参数
	 */
	private function judge($perpg = false,$title = false,$searches_field = false,$getdata = false,$type = false)
	{
		
		if ($searches_field)
		{
			$this	->	parse_field($searches_field);
		}
		if ($this->xs_heji)
				$this->totList( $this->format_tot );//john改写 2007-7-27
		if ($title)
		{
			$this	->	parse_title($title);
		}
		if ($perpg)
		{
			//$jud			=	array("SELECT","LIMIT");
			$num_arr	=	$this->GetAll("SELECT count(*) as con  FROM (".$this->combination().") as roc ");
			//$this->full_data=$this->GetAll("SELECT *   FROM (".$this->combination().") as roc ");
			//$num_arr	=	@mysql_fetch_row(@mysql_query("SELECT count(*)  ".$this->combination($jud)));
			$this	->	total_num = $num_arr[0]['con'];
			$this	->	pagination ($perpg);
		}
		$this	->	 pure_data	=	$this	->	parse_getdata($getdata);
		$this	->	 parse_type();
	
		if ($_REQUEST[debug_z])
		{
			print "<pre>";
			//print_r ($this	->	 sqlstring);
			//print_r($this->format_tot);
			//print_r($this -> combination(array("LIMIT")));
			print_r($this	->	 pure_data);
			//print_r($this->tot_arr);
			//print_r($this->tiaoshi);
			//print_r($this -> combination);
			print "</pre>";
		}
	} // end func



	/**
	 * 说明
	 * 解析数据
	 */
	private function parse_getdata($getdata)
	{
		$retudb	=	$this	->	GetAll ($this -> combination());
		if (!$getdata)
		{
			return $retudb;
		}
		if (!is_array($getdata))
		{
			$this	-> error	.=	'<br>参数getdata不是数组';
			return	false;
		}
		if (!is_array($retudb))
		{
			$this	-> error	.=	'执行SQL语句为空';
			return	false;
		}
		if ($this->xs_xiaoji)
		{
			$this->m_tot_arr	=	$this->execute_data( $retudb ,$this->format_tot ,true );
		}
		return	$this->execute_data( $retudb , $getdata );
	} // end func

	/**
	 * 功能：执行
	 * Tony---Tue Apr 03 14:38:24 CST 2007----14:38:24
	 */
	private function execute_data( $retudb , $getdata , $type=false )
	{
		if(!is_array($retudb))
		{
			$retudb=array();
		}
		
	
		foreach ($retudb as $key => $var)
		{
			foreach ($getdata as $keyz => $varz)
			{
				if (!$varz)
				{

					$along_arr[$keyz]	=	$type?'':htmlspecialchars($var[$keyz]);
				}
				else
				{	
				//  $var[dd_bianhao] = htmlspecialchars($var[dd_bianhao]);
				  //$var[dd_mingcheng] = htmlspecialchars($var[dd_mingcheng]);
					
					
					if($_REQUEST[debug_z]){
						print "<pre>";
						//print_r($varz);
						//print_r($retudb);
						//print_r($getdata);
						print "</pre>";
					}	
					
					$endsv	=	'';
					
					//为了双引号的问题注释了一下两行，不知道会有什么问题，暂时没有发现
					
					$varz	=	addslashes($varz);
					$varz	=	str_replace("\'","'",$varz);
						
					
					$exr = explode($this	->	 ex_exe_var, $varz);							//按函数分界符分割.默认#*#
					if($_REQUEST[debug_z]){
						print "<pre>";
						//print_r($exr);
						//print_r($retudb);
						//print_r($getdata);
						
						print "</pre>";
					}
					foreach ($exr as $ke => $va)
					{
						//$endsv = '';
						if($ke%2==1)
						{																		//需要运行部分的---即分界符之间的部分
							if($_REQUEST[debug_z]){
								eval('$showText = '.$va.';');
								
								print "<pre>";
								//print_r($va);
								//print_r($showText);
								print "</pre>";
							}
							
							@eval("\$vb = \"$va\";");
							@eval('$rslt = '.$vb.';');

if($_REQUEST[debug_z]){
print "<pre>";
print_r($va);
print_r($vb);
print "</pre>";
}



							//@eval('$rslt = '.$va.';');
						
							/*
								如果作为函数的字段值中包含双引号将出错，字段中包含'<','>'显示错误
								还没解决 jute 20070809
							*/
							
							
							
							if (is_array($rslt))
							{
								$outer_temp	=	$rslt;
								$exr[$ke]		=	'';
							}
							else
							{
								$exr[$ke]	=	$rslt;
							}
							

						}
						else
						{																		//非运行部分的
						
							//$varz	=	addslashes($varz);
					//$varz	=	str_replace("\'","'",$varz);

					
							@eval("\$vb = \"$va\";");
							//@eval('$vb = '.htmlspecialchars($va).';');
							//$vb = $va;
							//htmlspecialchars(@$row[$col["colname"]])
							/*
							字段中包含'<','>'显示错误
								jute 20070809
							*/
							$exr[$ke]	=	$vb;
						}
						if($_REQUEST[debug_z]){
							print "<pre>";
							print_r($exr[$ke]);
							print "</pre>";
						}
						$endsv	.=	$exr[$ke];
						

						if($_REQUEST[debug_z]){
						print "<pre>";
						//print_r($endsv);
						print "</pre>";
						}
					}
					$along_arr[$keyz]	=	$endsv;
				}
				$tot_arr[$keyz]	+=	$along_arr[$keyz];											//每列合计
			}
			$end_arr[]	=	$along_arr;
		}
		if($_REQUEST[debug_z]){
			print "<pre>";
			//print_r($end_arr);
			//print_r($getdata);
			print "</pre>";
		}
		if($type)
		{
			
		return $tot_arr;
		
		}
		else
		{
			
		return $end_arr;
		}
	}


	/**
	 * 说明
	 * 检索数组
	 */
	private function parse_field($searches_field)
	{
		// +-----------------------------去掉因为等于title数组而产生的宽度---------------------------------------+
		if (is_array($searches_field))
		{
			foreach ($searches_field as $key => $value)
			{
				$ex_t		=	explode("*", $value);
				$searches_field[$key]	=	$ex_t[0];
			}
		}
		// +------------------------------------------------------------------------------------------------------+
		if (is_array($this->searches_time))
		{																	//时间检索
			foreach ($this->searches_time as $key => $var)
			{
				$jj++;
				$this -> searches_input[$key.'：<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$this	->	 z_language['从'].'</b>']	=	array('textname'=>"searches_time[".$var."a]",'value'=>$_REQUEST['searches_time'][$var.'a']);
				$this -> searches_input['<b>'.$this	->	 z_language['到'].'</b>('.$key.')']	=	array('textname'=>"searches_time[".$var."b]",'value'=>$_REQUEST['searches_time'][$var.'b']);
				$js_qude		.=	"jc_time".$jj." = document.getElementById(\"searches_time[".$var."a]\").value;
									jc_time".$jj." = encodeURI(jc_time".$jj.");
									";
				$jj++;
				$js_qude		.=	"jc_time".$jj." = document.getElementById(\"searches_time[".$var."b]\").value;
									jc_time".$jj." = encodeURI(jc_time".$jj.");
									";
				$var=str_replace("`","%60",$var);
				$fijj	=	$jj-1;
				$js_get		.=	"searches_time%5B".$var."a%5D=\"+jc_time".$fijj."+\"&searches_time%5B".$var."b%5D=\"+jc_time".$jj."+\"&";
			}
		}
		if (is_array($searches_field))
		{
			$i=0;
			foreach ($searches_field as $key => $va)
			{																		//普通检索
				if (!$va) {
					$this	-> error	.=	'<br>检索字段不能为空';
					continue;
				}

				$i++;

				$this -> searches_input[$key]		=	array('textname'=>"searches_name[".$va."]",'value'=>$_REQUEST['searches_name'][$va]);
				$js_qude		.=	"searches".$i." = document.getElementById(\"searches_name[".$va."]\").value;
									searches".$i." = encodeURI(searches".$i.");
									";
				$va=str_replace("`","%60",$va);
				$js_get		.=	"searches_name%5B".$va."%5D=\"+searches".$i."+\"&";
			}
		}
		$fuck_ri =	 "<SCRIPT language=javascript>
				function searches()
				{
				".$js_qude."
				vfull = document.getElementById(\"full_searches\").value;
				vfull = encodeURI(vfull);
				if(document.all.relat_searches.checked)
				{
					vrelat = 'checked';
				}else{
					vrelat = '';
				}
				if (document.all.contain_searches.checked)
				{
					contain	 =	'checked';
				}
				else
				{
					contain	 =	'';
				}
				window.location=\"".$this	->	 originally_uri."&".$js_get."searches_start=rzt&relat_searches=\"+vrelat+\"&full_searches=\"+vfull+\"&contain_searches=\"+contain;
				}
				</SCRIPT>
				";
		$this -> searches_input['js']	=	trim($fuck_ri);
		$this -> searches_input['jsfunctionname']=	'searches';
		$this -> searches_input['full_inputname']=	'full_searches';
		$this -> searches_input['relat_checkboxname']=	'relat_searches';
		$this -> searches_input['contain_checkboxname']=	'contain_searches';
		//window.location="index.php?o=\$user_name[1]=444;";
		//<input name="checkbox" type="checkbox" value="checkbox" checked>

		if ($_REQUEST[relat_searches])
		{																							//检索条件之间的关系
			$relat_type	 =	'and';
		}
		else
		{
			$relat_type	 =	'or';
		}

		// +--------------------------------------------------------------------+
		//改变sql
		if ($_REQUEST[searches_name]||$_REQUEST[full_searches])
		{
			if (is_array($_REQUEST[searches_name]))
			{
				foreach ($_REQUEST[searches_name] as $key => $var)
				{
					$yb_fut_type	=	 substr_count($key,"function:");				//是否需要函数转换
					if ($yb_fut_type)
					{
						$yb_fut_sql		=	substr($key,9, strlen($key)-9);
						$yb_fut_ex	= explode("||",$yb_fut_sql);
						$yb_fut_sta	=	$yb_fut_ex[0].'("'.$var.'")';				//执行函数
						@eval('$var = '.$yb_fut_sta.';');
						$key	=	$yb_fut_ex[1];
					}
					if ($_REQUEST[full_searches])
					{																			//全检索
						if ($_REQUEST[contain_searches])
						{																						//精确查询
							$new_sql	.=	$key ."  =  '". $_REQUEST[full_searches] ."' ".$relat_type." ";
						}
						else
						{																						//默认包含查询
							$nbtc	=	str_replace("  "," ",$_REQUEST[full_searches]);
							$nbex	=	explode(' ',$nbtc);
							foreach ($nbex as $smex)
							{
								if ($smex)
								{
									$new_sql	.=	$key ."  like  '%". $smex ."%' ".$relat_type." ";
								}
							}
						}
					}
					else
					{																		//选择检索
						if ($var)
						{
							if ($_REQUEST[contain_searches])
							{																			//精确查询
								$new_sql	.=	$key ."  =  '". $var ."' ".$relat_type." ";
							}
							else
							{																			//默认包含查询
								$nbtc	=	str_replace("  "," ",$var);
								$nbex	=	explode(' ',$nbtc);
								foreach ($nbex as $smex)
								{
									if ($smex)
									{
										$new_sql	.=	$key ."  like  '%". $smex ."%' ".$relat_type." ";
									}
								}
							}
						}
					}
				}
			}
			if (is_array($_REQUEST[searches_time]))
			{																					//时间类型检索
				$t_time			=	$_REQUEST[searches_time];
				$t_key			=	array_keys($_REQUEST[searches_time]);	//获得key值数组
				for ($t=0;$t<count($t_time);$t+=2)
				{
					if ($t_time[$t_key[$t]]<>''||$t_time[$t_key[$t+1]]<>'')
					{
						$sta_k		=	$t_key[$t];								//第一个key值
						$end_k		=	$t_key[$t+1];							//第二个key值
						$only_one	=	false;
						if ($t_time[$sta_k]===''||$t_time[$end_k]==='')
						{																//如果有一个为空
							$only_one	=	$t_time[$sta_k]?1:2;
							$t_time[$sta_k]	=	$t_time[$sta_k]?$t_time[$sta_k]:$t_time[$end_k];
							$t_time[$end_k]	=	$t_time[$sta_k];
						}
						$new_type	=	 substr_count($sta_k, "span:");				//是否是区间类型
						// +------------------------------区间类型--------------------------------------+
						if ($new_type)
						{
							$span_k_sql		=	substr($sta_k,5, strlen($sta_k)-6);
							$fut_type	=	 substr_count($span_k_sql, "function:");				//是否需要函数转换
							// +--------------需要函数转换----------------------------------+
							if ($fut_type)
							{
								$fut_sql		=	substr($span_k_sql,9, strlen($span_k_sql)-9);
								$fut_ex	= explode("||",$fut_sql);
								$fut_sta	=	$fut_ex[0].'("'.$t_time[$sta_k].'")';				//第一个值
								@eval('$futslt_sta = '.$fut_sta.';');
								$fut_end	=	$fut_ex[0].'("'.$t_time[$end_k].'")';				//第二个值
								@eval('$futslt_end = '.$fut_end.';');
								$t_field	= $fut_ex[1];

								$age_type	=	 substr_count($fut_ex[1], "age:");				//是否是年龄类型
								// +--------------特殊的年龄类型-------------+
								if ($age_type)
								{
									$t_field	=	substr($t_field,4, strlen($t_field)-4);
									if ($only_one)
									{
										$futslt_sta	=	$futslt_sta.'-01-01';
										$futslt_end	=	($futslt_sta+1).'-01-01';
										$new_sql	.=	"(".$t_field."  BETWEEN  '". $futslt_sta ."' AND '".$futslt_end."') ".$relat_type." ";
									}
									else
									{
										$futslt_sta	=	($futslt_sta+1).'-01-01';
										$futslt_end	=	($futslt_end).'-01-01';							//年龄在反过来检索
										$new_sql	.=	"(".$t_field."  BETWEEN  '". $futslt_end ."' AND '".$futslt_sta."') ".$relat_type." ";
									}
								}
								// +--------------正常类型-------------+
								else
								{
									if ($only_one)
									{																//只有一个为真
										$new_sql	.=	"(".$t_field."  =  '".$futslt_sta."') ".$relat_type." ";
									}
									else
									{
										$new_sql	.=	"(".$t_field."  BETWEEN  '". $futslt_sta ."' AND '".$futslt_end."') ".$relat_type." ";
									}
								}
							}
							// +--------------需要函数转换----end-------------------------+
							// +--------------正常区间-------------------------------------+
							else
							{
								if ($only_one)
								{																//只有一个为真
									$cfuh			=	($only_one==1)?'>':'<';
									$new_sql	.=	"(".$span_k_sql."  ".$cfuh	."  '".$t_time[$sta_k]."') ".$relat_type." ";
								}
								else
								{
									$new_sql	.=	"(".$span_k_sql."  BETWEEN  '". $t_time[$sta_k] ."' AND '".$t_time[$end_k]."') ".$relat_type." ";
								}
							}
						}
						//+--------------时间类型--------+
						else
						{
							$this->replace_m($t_time[$sta_k]);
							$this->replace_m($t_time[$end_k]);		//全角转换未半角
							$sta_t		=	strtotime($t_time[$sta_k]." 00:00:00");
							$end_t		=	strtotime($t_time[$end_k]." 23:59:59");
							if($this->time_type!='UNIX')
							{
								$sta_t	=	data("Y-m-d H:i:s",$sta_t);
								$end_t	=	data("Y-m-d H:i:s",$end_t);
							}
							$sta_k_sql		=	substr($sta_k,0, strlen($sta_k)-1);
							$new_sql	.=	"(".$sta_k_sql."  BETWEEN  '". $sta_t ."' AND '".$end_t."') ".$relat_type." ";
						}
					}
				}
			}
			if ($new_sql)
			{														//组合最后结果
				$new_sql_one	=	rtrim($new_sql," ".$relat_type." ");
				$new_sql_tow	=	"   and  (".rtrim($new_sql," ".$relat_type." ").")  ";
				if ($this -> sqlstring[WHERE])
				{
					$this -> sqlstring[WHERE] .= $new_sql_tow;
				}
				else
				{
					$this -> sqlstring[WHERE]  = "WHERE   ".$new_sql_one;
				}
			}
		}
	} // end func

	/*
	*说明:替换全角为半角
	*Tue Dec 26 11:51:29 CST 2006--Tony
	*/
	private function replace_m(&$str)
	{
		$arr	=	array(
		'0'=>'０','a'=>'ａ','k'=>'ｋ',
		'1'=>'１','b'=>'ｂ','l'=>'ｌ',
		'2'=>'２','c'=>'ｃ','m'=>'ｍ',
		'3'=>'３','d'=>'ｄ','n'=>'ｎ',
		'4'=>'４','e'=>'ｅ','o'=>'ｏ',
		'5'=>'５','f'=>'ｆ','p'=>'ｐ',
		'6'=>'６','g'=>'ｇ','q'=>'ｑ',
		'7'=>'７','h'=>'ｈ','r'=>'ｒ',
		'8'=>'８','i'=>'ｉ','s'=>'ｓ',
		'9'=>'９','j'=>'ｊ','t'=>'ｔ',
		'u'=>'ｕ','v'=>'ｖ','w'=>'ｗ',
		'x'=>'ｘ','y'=>'ｙ','z'=>'ｚ',
		'!'=>'！','#'=>'＃','$'=>'￥',
		'@'=>'＠','%'=>'％',
		'^'=>'＾','&'=>'＆','*'=>'＊',
		'('=>'（',')'=>'）','_'=>'＿',
		'+'=>'＋','|'=>'｜','{'=>'｛',
		'}'=>'｝','"'=>'＂',':'=>'：',
		'<'=>'＜','>'=>'＞','?'=>'？',
		'-'=>'－','='=>'＝',
		';'=>'；',','=>'，',
		'.'=>'．','/'=>'／');
		foreach ($arr as $key=>$van)
		{
			$str	=	str_replace($van,$key,$str);
		}
		//return $str;
	}



	// +--------------------------------------------------------------------+


	/**
	 * 说明&uarr;&darr;&spades;&hearts;&uArr;&dArr;&and;&or;
	 */
	private function parse_title($title)
	{
		if (!is_array($title))
		{
			$this	-> error	.=	'<br>参数title不是数组';
			return	false;
		}
		$this -> originally_title    = $title;
		if ($_REQUEST['range']=='DESC')
		{
			$xpx	= 'ASC';
			$img_zt	= '<b>&uarr;</b>';
		}
		else
		{
			$xpx	= 'DESC';
			$img_zt	= '<b>&darr;</b>';
		}
		$this -> html_title	= array();
		$remove_arr	= array	('field_name'=>'','range'=>'');
		$now_url			= $this -> remove_url($remove_arr);
		foreach ($title as $key => $var)
		{																						//....以后可以检测输入的字段值是否真确（.....）
			$echo			= ($_REQUEST['field_name'] == $var&&$var)?$img_zt:'';
			$this -> html_title[name][$key]		= $key.$echo;
			$this -> html_title[linkadd][$key]	= $var?($now_url."&field_name=".$var."&range=".$xpx):'';
		}
		//改变sql
		if ($_REQUEST['field_name'])
		{
			$this -> sqlstring[ORDER]  = "ORDER BY  ".$_REQUEST['field_name']."  ".$_REQUEST['range'];
		}
	} // end func




	/**
	 * 说明
	 * 分页 
	 */
	private function pagination($perpg)
	{
		$allrow		= $this -> total_num;
		$allpge		= ceil($allrow/$perpg);
		$nowpg		= isset($_REQUEST['nowpg'])?$_REQUEST['nowpg']:1;
		$nowpg		= ($nowpg > $allpge)?$allpge:$nowpg;
		$nowpg		= ($nowpg < 1)?1:$nowpg;
		$nextpg		= ($nowpg ==$allpge)?$nowpg:$nowpg+1;
		$alongpg	= ($nowpg ==1)?1:$nowpg-1;
		$firstpg	= 1;
		$lastpg		= $allpge;

		$remove_arr = array('nowpg'=>'','nowpg_start'=>'');
		$first_url = $this -> remove_url($remove_arr).'&';
		$this -> link_page['页面']			= $first_url;
		$this -> link_page['首页']	 		= $first_url."nowpg=".$firstpg;
		$this -> link_page['上一页']			= $first_url."nowpg=".$alongpg;
		$this -> link_page['下一页']			= $first_url."nowpg=".$nextpg;
		$this -> link_page['尾页']			= $first_url."nowpg=".$lastpg;
		$this -> link_page['当前页']			= $nowpg;
		$this -> link_page['每页数']			= $perpg;
		$this -> link_page['总页']			= $allpge;
		$this -> link_page['总条数']			= $allrow;
		$this -> link_page['now_num_num']	=	"(".(($nowpg-1)*$perpg+1)."-".($nowpg ==$allpge?$allrow:($nowpg*$perpg)).")";
		$this -> link_page['button']			= 'nowpg_start';
		$this -> link_page['input_text']		= 'nowpg';

		$this -> link_page['jsfunctionname']= 'pagef';
		$this -> link_page['js']				=	"<SCRIPT language=javascript>
													function pagef()
													{
														v = document.getElementById(\"nowpg\");
														window.location=\"".$first_url."nowpg=\"+v.value;
													}
													</SCRIPT>
												";
		//改变sql
		$start_data = ($nowpg-1)*$perpg;
		$this -> sqlstring[LIMIT]  = "LIMIT   ".$start_data.", ".$perpg;
	} // end func




	/**
	 * 获得当前字符串的长度
	 *
	 * Detail description
	 * @param     string $str
	 * @since     1.0
	 * @access    public
	 * @return    int
	 * @update    2003-12-10
	 */
	private function m_strlen($str,$code	=	'',$arr=false)
	{
		if ($code	 ==	'UTF-8')
		{
			$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
			preg_match_all($pa, $str, $t_str);
			if ($arr)
			{
				return	$t_str[0];
			}
			return count($t_str[0]);
		}
		else
		{
			preg_match_all("/[\x80-\xff]?./",$str,$ar);
			if ($arr)
			{
				return	$ar[0];
			}
			return count($ar[0]);
		}
	} // end func m_strlen



	/**
	 * 说明
	 * 解析sql不支持子查询.2006-09-01
	 *	修改.日期.2006-9-8支持子查询(子语句用()括起来)
	 */
	private function parse_sql($sql)
	{
		//str_split										//php5.0
		if($sql	==	NULL)
		{
			return false;
		}
		$this -> originally_sql = $sql;
		$this	->	 temp_sql	=	array();
		$sql = trim($sql);


		$big_sql	=	$this	->	 big_strsql($sql);			//转换成大写

		$ee	=	explode("SELECT",$big_sql);

		$t_str	=	$this	->	 m_strlen($big_sql,'UTF-8',true);

		if (in_array('(',$t_str)&&count($ee)>2)
		{															//有括号存在解析括号
			$ii	=	0;
			foreach ($t_str as $key => $var)
			{
				if (!$buzhix)
				{
					if (($var=="'"||$var=='"')&&$t_str[$key-1]<>'\\')
					{
						if ($var=="'")
						{
							$dyh	=	$dyh?false:true;
						}
						if ($var=='"')
						{
							$syh	=	$syh?false:true;
						}
						if ($syh||$dyh)
						{
							$buzhix	=	true;
						}
						else
						{
							$buzhix	=	false;
						}
					}
				}
				else
				{
					if ($dyh&&$var=="'"&&$t_str[$key-1]<>'\\')
					{
						$buzhix	=	false;
						$dyh	=	$dyh?false:true;
					}
					elseif ($syh&&$var=='"'&&$t_str[$key-1]<>'\\')
					{
						$buzhix	=	false;
						$syh	=	$syh?false:true;
					}
				}
				if ($var=='('&&!$buzhix)
				{
					$ii++;
					$lef[][$ii]	=	$key;
					if ($ii==1)
					{
						$outside_lef[]	=	$key;
					}
				}
				elseif($var==')'&&!$buzhix)
				{
					$ii--;
					$rig[][$ii]	 =	$key;
					if ($ii===0)
					{
						$outside_rig[]	=	$key;
					}
				}
			}
			// +--------------------------------------------------------------------+
			$this	->	 nub_str	=	0;
			$this	->	 temp_sql['SELECT']	=	$this	->	 nub_str;

			$sql_hl		=	join('', array_slice($t_str, 0,$outside_lef[0]));
			$sql_last		=	$this	->	 son_parse_sql($sql_hl);

			if (count($outside_lef)<>count($outside_rig))
			{
				$this	->	 errro	 .=	 'sql语句括号错误';
			}
			for ($ii=0; $ii<count($outside_lef); $ii++)
			{
				$sql_last		.=	join('',array_slice($t_str,$outside_lef[$ii],$outside_rig[$ii]-$outside_lef[$ii]));
				if (!$outside_lef[($ii+1)])
				{
					$next_lef	=	$outside_rig[$ii];
				}
				else
				{
					$next_lef	=	$outside_lef[($ii+1)];
				}
				$did			 =	join('',array_slice($t_str,$outside_rig[$ii],$next_lef-$outside_rig[$ii]));
				$sql_last		.=	$this	->	 son_parse_sql($did,$this	->	 nub_str);
			}

			$tt1	=	count($outside_lef)-1;
			$tt2	=	$this	->	 m_strlen($sql,'UTF-8');

			$sql_end		=	join('', array_slice($t_str,$outside_rig[$tt1],$tt2));
			$sql_last		.=	$this	->	 son_parse_sql($sql_end,$this	->	 nub_str);
		}
		else
		{																						//mei有括号存在
			$this	->	 nub_str	=	0;
			$this	->	 temp_sql['SELECT']	=	$this	->	 nub_str;
			$sql_last		=	$this	->	 son_parse_sql($big_sql);					//替换SQL关键字
		}

		$cond = explode('=&=',$sql_last);

		$sqlstring	=	$this	->	 temp_sql;

		foreach ($sqlstring	as	$key	=>	$val)
		{
			$sqlstring[$key]	=	trim($cond[$val]);
		}
		$a	=	'';
		foreach ($sqlstring as $key => $value)
		{
			$a .= "	 ".$value;
		}
		//echo trim($a);
		return $sqlstring;
	} // end func


	/**
	 * 说明
	 *
	 */
	private function son_parse_sql($sql)
	{
		$i				=	$this	->	 nub_str;
		if(preg_match('/\s+FROM\s+(\n)*/i',$sql))
		{
			$sql = preg_replace('/\s+FROM\s+/i' , ' =&=FROM ' , $sql);
			$this	->	 temp_sql['FROM']	=	++$i;
		}
		if(preg_match('/\s+WHERE\s+/i',$sql))
		{
			$sql = preg_replace('/\s+WHERE\s+/i', ' =&=WHERE ', $sql);
			$this	->	 temp_sql['WHERE']	=	++$i;
		}
		if(preg_match('/\s+GROUP\s+/i',$sql))
		{
			$sql = preg_replace('/\s+GROUP\s+/i', ' =&=GROUP ', $sql);
			$this	->	 temp_sql['GROUP']	=	++$i;
		}
		if(preg_match('/\s+HAVING\s+/i',$sql))
		{
			$sql = preg_replace('/\s+HAVING\s+/i', ' =&=HAVING ', $sql);
			$this	->	 temp_sql['HAVING']	=	++$i;
		}
		if(preg_match('/\s+ORDER\s+/i',$sql))
		{
			$sql = preg_replace('/\s+ORDER\s+/i', ' =&=ORDER ', $sql);
			$this	->	 temp_sql['ORDER']	=	++$i;
		}
		if(preg_match('/\s+LIMIT\s+/i',$sql))
		{
			$sql = preg_replace('/\s+LIMIT\s+/i', ' =&=LIMIT ', $sql);
			$this	->	 temp_sql['LIMIT']	=	++$i;
		}
		$this	->	 nub_str	=	$i;
		return	$sql;
	} // end func




	/**
	 * 说明
	 *	把SQL中的关键字替换成大写
	 */
	private function big_strsql($sql)
	{

		$sql = preg_replace('/\SELECT\b/i' , ' SELECT ' , $sql);

		if(preg_match('/\s+FROM\s+(\n)*/i',$sql))
		{
			$sql = preg_replace('/\s+FROM\s+/i' , ' FROM ' , $sql);
		}
		if(preg_match('/\s+WHERE\s+/i',$sql))
		{
			$sql = preg_replace('/\s+WHERE\s+/i', ' WHERE ', $sql);
		}
		if(preg_match('/\s+GROUP\s+/i',$sql))
		{
			$sql = preg_replace('/\s+GROUP\s+/i', ' GROUP ', $sql);
		}
		if(preg_match('/\s+HAVING\s+/i',$sql))
		{
			$sql = preg_replace('/\s+HAVING\s+/i', ' HAVING ', $sql);
		}
		if(preg_match('/\s+ORDER\s+/i',$sql))
		{
			$sql = preg_replace('/\s+ORDER\s+/i', ' ORDER ', $sql);
		}
		if(preg_match('/\s+LIMIT\s+/i',$sql))
		{
			$sql = preg_replace('/\s+LIMIT\s+/i', ' LIMIT ', $sql);
		}
		return	$sql;
	} // end func






	/**
	 * 说明
	 * 组合sql
	 */
	private function combination($jud = '')
	{
		if (!$jud || !in_array("SELECT",$jud))
		{
			$a  .= $this -> sqlstring[SELECT];
		}
		$a .= '  '.$this -> sqlstring[FROM];
		if (!$jud || !in_array("WHERE",$jud))
		{
			$a .= '  '.$this -> sqlstring[WHERE];
		}
		if (!$jud || !in_array("GROUP",$jud))
		{
			$a .= '  '.$this -> sqlstring[GROUP];
		}
		if (!$jud || !in_array("HAVING",$jud))
		{
			$a .= '  '.$this -> sqlstring[HAVING];
		}
		if (!$jud || !in_array("ORDER",$jud))
		{
			$a .= '  '.$this -> sqlstring[ORDER];
		}
		if (!$jud || !in_array("LIMIT",$jud))
		{
			$a .= '  '.$this -> sqlstring[LIMIT];
		}
		return  trim($a);
	} // end func



	/**
	 * 说明
	 *
	 */
	function GetAll($sql)
	{
		$jgsz  = Pft_Db::getDb()->getAll( $sql );
		//$jgsz  = $this->db->GetAll($sql);
		/*$qusql = mysql_query($sql);
		if (!$qusql)
		{
		$this	->	error	.=	'无法连接数据库<hr>';
		return   false;
		}
		else
		{
		while($jigu = @mysql_fetch_assoc($qusql))
		{
		$jgsz[] = $jigu;
		}
		}*/
		return $jgsz;
	} // end func

	/* +--------------------------------------------------------------------++--------------------------------------------------------------------+*/

	/**
	 * 说明
	 *	解析类型返回相应数据
	 */
	private function parse_type()
	{
		if (is_array($this -> return_type))
		{
			$zhha_arr	=	array('page'=>'link_page','title'=>'html_title','searches'=>'searches_input','data'=>'pure_data',);
			$v	 =	array('page'=>1,'title'=>1,'searches'=>1,'data'=>1,);
			$w	 =	array('page'=>2,'title'=>2,'searches'=>2,'data'=>2,);

			$typev	=	array_intersect_assoc($v, $this -> return_type);

			$typew	=	array_intersect_assoc($w, $this -> return_type);

			$typeo	=	array_diff_assoc($this -> return_type, $typev);

			$typeo	=	array_diff_assoc($typeo, $typew);

			foreach ($typev as $key=>$var)
			{
				$xky	=	"html_".$key;
				$this	->	 finally_data_arr[$xky]	=	$this	->	 xky();
			}
			foreach ($typew as $key=>$var)
			{
				$xky	=	"html_".$key;$s = $zhha_arr[$key];
				$this	->	 finally_data_arr[$xky]	=	$this	->	 zhha_arr[$key];
			}

			$this	->	 finally_data_arr[html]	 =	"<table ".$this	->	 html_style[table].">";
			array_key_exists('searches',$typeo)?($this	->	 finally_data_arr[html]	.=	$this	->	 html_searches()):'';
			array_key_exists('title',$typeo)?($this	->	 finally_data_arr[html]	.=	$this	->	 html_title()):'';
			array_key_exists('data',$typeo)?($this	->	 finally_data_arr[html]	.=	$this	->	 html_data()):'';
			array_key_exists('page',$typeo)?($this	->	 finally_data_arr[html]	.=	$this	->	 html_page()):'';
			$this	->	 finally_data_arr[html]	.=	"</table>";
		}
		else
		{
			if ($this -> return_type	 ==	1)
			{
				$this	->	 finally_data_arr[html_searches]	=	$this	->	 html_searches();
				$this	->	 finally_data_arr[html_title]		=	$this	->	 html_title();
				$this	->	 finally_data_arr[html_data]		=	$this	->	 html_data();
				$this	->	 finally_data_arr[html_page]		=	($this->total_num>$this->is_fanye_tiao)?$this	->	 html_page():'';//恶心要求小于一页不显示翻页
			}
			elseif ($this -> return_type	 ==	2)
			{
				$this	->	 finally_data_arr[html_title]		=	$this	->	 html_title;
				$this	->	 finally_data_arr[html_searches]	=	$this	->	 searches_input;
				$this	->	 finally_data_arr[html_page]		=	$this	->	 link_page;
				$this	->	 finally_data_arr[html_data]		=	$this	->	 pure_data;
			}
			else
			{
				$this	->	 finally_data_arr	 =	"<table ".$this	->	 html_style[table].">";
				$this	->	 finally_data_arr	.=	$this	->	 html_searches();
				$this	->	 finally_data_arr	.=	$this	->	 html_title();
				$this	->	 finally_data_arr	.=	$this	->	 html_data();
				$this	->	 finally_data_arr	.=	($this->total_num>$this->is_fanye_tiao)?$this	->	 html_page():'';//要求小于一页不显示翻页
				$this	->	 finally_data_arr	.=	"</table>";
			}
		}
	} // end func



	/**
	 * 说明
	 *	HTML的连接
	 */
	private function html_page()
	{
		/*if($cneng == true)
		{
		$firs		=	"firstpage";
		$alon	=	"alongpage";
		$nex		=	"nextpage";
		$las		=	"lastpage";
		$pag		=	"page";
		$tog		=	"together";
		$item	=	"item";
		$jump	=	"jump";
		}
		else{*/
		$firs		=	$this	->	 z_language['首页'];
		$alon		=	$this	->	 z_language['上一页'];
		$nex		=	$this	->	 z_language['下一页'];
		$las		=	$this	->	 z_language['尾页'];
		$pag	 	=	$this	->	 z_language['页'];
		$tog		=	$this	->	 z_language['共'];
		$item		=	$this	->	 z_language['项'];
		$jump	 	=	$this	->	 z_language['跳转到'];

		//}

		$biti="<tr ".$this	->	 html_style[page]."><td colspan=50 align=right><table width=100% border=0 cellspacing=0 cellpadding=0>
		<tr><td width=80%></td><td nowrap><a href=\"".$this -> link_page['首页']."\">".$firs."</a>&nbsp;&nbsp;";
		$biti.="<a href=\"".$this -> link_page['上一页']."\">".$alon."</a>&nbsp;&nbsp;";
		$biti.="<a href=\"".$this -> link_page['下一页']."\">".$nex."</a>&nbsp;&nbsp;";
		$biti.="<a href=\"".$this -> link_page['尾页']."\" >".$las."</a>&nbsp;&nbsp;&nbsp; ";
		$biti.=$this -> link_page['当前页']."/".$this -> link_page['总页']."&nbsp;&nbsp;(".$this -> link_page['每页数']."/".$this	->	 z_language['每页'].")&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$tog."&nbsp;".$this -> link_page['总条数']."&nbsp;".$item;
		$biti.=$this -> link_page['now_num_num']."&nbsp;&nbsp;&nbsp;&nbsp;
			</td><td width=10 nowrap><input class=\"botton_submit\" name=\"nowpg_start\" type=\"button\" onclick='pagef();' value=\"".$jump."\">
			</td><td width=70 nowrap><input class=\"Input1\"  type=\"text\" name=\"nowpg\" size=\"1\" value=\"".$this -> link_page['当前页']."\">&nbsp;".$pag."</td></tr></table></td></tr>";

		$biti.=$this -> link_page[js];

		return  $biti;
	} // end func



	/**
	 * 说明
	 *	返回列表头行
	 */
	private function html_title()
	{
		// +---------------------------锁定表格-----------------------------------------+
		if ($this->lock_table!='OFF')
		{
			$html_title	=	$this->lock_style();
			$html_title.=	'<SCRIPT language=JavaScript >
							var DataTitles=new Array(';
			foreach ($this -> html_title[name] as $key => $var)
			{
				$th_width	=	($this	->	width_html[$key])?$this	->	width_html[$key]:'';
				if ($this -> html_title[linkadd][$key])
				{
					$html_title .= "\"<a href=\\\"".$this -> html_title[linkadd][$key]."\\\">".$var."</a>   ".$th_width."\",";
				}
				else
				{
					$html_title .= "\"".$var."  #".$th_width."\",";
				}
			}
			$html_title = rtrim($html_title,",");
			$html_title .= ")</SCRIPT>";
			return	$html_title;
		}
		// +-------------------------------end-------------------------------------+
		$html_title	=	'<tr '.$this	->	 html_style[title].'>';
		foreach ($this -> html_title[name] as $key => $var)
		{
			$th_width	=	($this	->	width_html[$key])?$this	->	width_html[$key]:'';
			if ($this -> html_title[linkadd][$key])
			{
				$html_title .= "<th ".$th_width."><a href=\"".$this -> html_title[linkadd][$key]."\">".$var."</a></th>";
			}
			else
			{
				$html_title .= "<th ".$th_width.">".$var."</th>";
			}
		}
		$html_title	.=	'</tr>';
		return	$html_title;
	} // end func



	/**
	 * 说明
	 * 返回搜索行
	 */
	private function html_searches()
	{
		$html = "<SCRIPT language=javascript>
			function showMoreTony()
			{
				if(moretypetony.style.display =='')
				{
					moretypetony.style.display='none';
					myFont.innerHTML = \"<b>".$this	->	 z_language['高级检索']."</b>\";
				}
				else
				{
					moretypetony.style.display='';
					myFont.innerHTML = \"<b>".$this	->	 z_language['隐藏检索']."</b>\";
				}
			
			}
			//判断参数是否为数字 john
			function is_num(va)
			{
				var re = /^\-?[0-9]+.?[0-9]*$/; 
				if(re.test(va))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			function formatvalue(tid,va)
			{
				
				var num=va.replace(/\,/g,'');
				if(is_num(num))
				{
				
				document.getElementById(tid).value=num;
				}
			}
			</SCRIPT>
		
			<tr ".$this	->	 html_style['searches_title']."><td colspan=50 align=\"center\">
			<table width=40% cellpadding=0 cellspacing=0 border=0>
				<tr ".$this	->	 html_style['searches_title'].">
				<td width=15% nowrap=nowrap><a href=\"#\" onClick=\"showMoreTony();\"><font id=myFont class=\"SearchTHTitle\"><b>".$this	->	 z_language['高级检索']."</b></font></a>&nbsp;&nbsp;&nbsp;<a href=\"".$this -> originally_uri."\"><b>".$this	->	 z_language['返回初始状态']."</b></a></td>
				<td width=10%>&nbsp;&nbsp;&nbsp;&nbsp;<input class=\"Input1\" type=\"text\" name=\"full_searches\" onkeyup=\"formatvalue(this.id,this.value)\" id=\"full_searches\" size=\"20\"  value=\"".$_REQUEST['full_searches']."\"></td>
				<td width=15%>&nbsp;&nbsp;<input class=\"botton_submit\" name=\"searches_start\" type=\"button\" onclick='searches();' value=\"".$this	->	 z_language['检索']."\"></td>
				
				<td width=30%>&nbsp;</td></tr>
			</table>
			<div style=\"display:none\" id=\"moretypetony\">
				<table cellpadding=0 cellspacing=0 border=0 width=40% >";
		$br	=	0;
		foreach ($this	->	 searches_input as $key => $var)
		{
			if ($key	!=	'jsfunctionname'&&$key	 !=	 'js'&&$key	 !=	 'full_inputname'&&$key	 !=	 'relat_checkboxname'&&$key	 !=	 'contain_checkboxname')
			{
				$html .=	($br%2)?'':'<tr '.$this	->	 html_style[searches_content].'>';
				$html .= "<td nowrap=nowrap>".$key."</td><td><input class=\"Input1\" type=\"text\" id=\"".$var['textname']."\" onkeyup=\"formatvalue(this.id,this.value)\" name=\"".$var['textname']."\" size=\"20\" value=\"".$var[value]."\"></td>";
				$br++;
				$html .=	($br%2)?'':'</tr>';
			}
		}
		$html .=	($br%2)?'<td>&nbsp;</td><td>&nbsp;</td></tr>':'';
		$html_timetype	=	($this	->	 searches_time)?'<b>'.$this	->	 z_language['检索时间格式'].':</b>&nbsp;&nbsp;(2008-08-08)':'';
		$html .= "<tr><td colspan=4>".$html_timetype.$this	->	 z_language['关联查询']."<input name=\"relat_searches\" type=\"checkbox\" value=\"checked\" ".$_REQUEST[relat_searches].">
				&nbsp;&nbsp;".$this	->	 z_language['精确查询']."<input name=\"contain_searches\" type=\"checkbox\" value=\"checked\" ".$_REQUEST[contain_searches]."></td></tr>";
		$html .= "</table></div></td></tr>
					<script>
					document.attachEvent('onkeydown',
					function ()
					{
						if (event.keyCode==13) {
							searches();
						}
					});
					  </script>";
		$html	.=	$this	->	 searches_input['js'];
		if($this->sys_search)
		{
		return	$html;
		}
		else 
		{
			return "";
		}
	} // end func

	/**
	 * 说明
	 *
	 * 返回数据列表
	 */
	private function html_data()
	{
		/*/$html_data	=	'<table border=1 width=100%>';*/
		// +--------------------------锁定表格--------------------------+
		if ($this->lock_table!='OFF')
		{
			$html_data	=	'<SCRIPT language=JavaScript >
			var DataFields=new Array()
			';
			if (is_array($this	->	 pure_data))
			{
				$js_key	=	0;
				foreach ($this	->	 pure_data as $key=>$var)
				{
					$html_data .="
						DataFields[".$js_key."] =new Array(";
					foreach ($var as $key=>$value)
					{
						//$value	=	htmlspecialchars($value);
						$value	=	addslashes($value);
						$value	=	str_replace("\n","\\",$value);
						
						$html_data .= "\"".$value."\",";
					}
					$html_data = rtrim($html_data,",");
					$html_data .= ")
								   ";
					$js_key++;
				}
			}
			$html_data .= '</SCRIPT>';
			$html_data .= $this->lock_js();
			$html_data .= '<TR>
			<TD  colspan=50><TABLE cellSpacing=0 cellPadding=0 border=0>
  <TBODY>
  <TR>
    <TD ><DIV id=TonyDataTable></DIV></TD></TR></TBODY></TABLE></TD></TR>';
			return	$html_data;
		}
		// +--------------------------end锁定表格--------------------------+
		// +-------------------------------js-鼠标单击的颜色------------------------------------+
		$html_data	.=	'<script>
		function R_changeBG(RocID)
		{
			if(document.all)
			{
				var Roc = document.getElementById(RocID)
				var BackGround = Roc.style.background.toUpperCase();
				if (BackGround.match("#FFFADF"))
				{
					Roc.style.background = "#EEF0F6";
				}
				else
				{
					Roc.style.background = "#FFFADF";
				}
			}
		}
						</script>';
		// +-------------------+
		if (is_array($this	->	 pure_data))
		{
			foreach ($this	->	 pure_data as $keyz=>$var)
			{
				$html_data .= "<tr  id=\"Roc".$i."\" ".$this	->	 html_style[data]."  onclick=\"R_changeBG('Roc".$i."');\" onMouseOver=\"this.className ='bg';\" onMouseOut=\"this.className='source'\">";//修改成样式了 Victor 20070613
				foreach ($var as $key=>$value)
				{
					$html_data .= "<td>".$value."</td>";
				}
				$html_data .= "</tr>";
				$i++;
			}
			//小计
			if ($this->xs_xiaoji)
			{
				$html_data .= "<tr  id=\"Roc".$i."\" ".$this	->	 html_style[data]."  onclick=\"R_changeBG('Roc".$i."');\" onMouseOver=\"this.style.backgroundColor ='#FFFFFF';\" onMouseOut=\"this.style.backgroundColor ='#EEF0F6';\">";
				$j	=	0;
				foreach ($this->m_tot_arr as $key=>$value)
				{
					if ($j)
					{
						if($this->format)
						{
							if(is_float($value))
							{
							$html_data .= "<td><b>".($value?Pft_Config::sechof($value):'')."</b></td>";
							}
							else 
							{
								$html_data .= "<td><b>".($value?$value:'')."</b></td>";
							}
						}
						else 
						{
							$html_data .= "<td><b>".($value?$value:'')."</b></td>";
						}
					}
					else
					{
						
					$html_data .= "<td><b>".$this-> z_language['小计']."</b></td>";
					}
					$j++;
				}
				$html_data .= "</tr>";
			}
			
			//合计
			if ($this->xs_heji)
			{
				$html_data .= "<tr  id=\"Roc".$i."\" ".$this	->	 html_style[data]."  onclick=\"R_changeBG('Roc".$i."');\" onMouseOver=\"this.style.backgroundColor ='#FFFFFF';\" onMouseOut=\"this.style.backgroundColor ='#EEF0F6';\">";
				$j	=	0;
				foreach ($this->tot_arr as $key=>$value)
				{
					if ($j)
					{
					if($this->format)
						{
							if(is_float($value))
							{
							$html_data .= "<td><b>".($value?Pft_Config::sechof($value):'')."</b></td>";
							}
							else 
							{
								$html_data .= "<td><b>".($value?$value:'')."</b></td>";
							}
						}
						else 
						{
							$html_data .= "<td><b>".($value?$value:'')."</b></td>";
						}
					}
					else
					$html_data .= "<td><b>".$this-> z_language['合计']."</b></td>";
					$j++;
				}
				$html_data .= "</tr>";
			}
		}
		//$html_data	.=	'</table>';
		return	$html_data;
	} // end func

	/*##########################################################锁定表格###########################################################*/
	/**
	 * 说明
	 * 返回锁定表格所需要的样式
	 */
	private function lock_style()
	{
		return	"<STYLE type=text/css>BODY {
				    FONT: 12px 细明体; CURSOR: default
				}
				TD {
				    FONT: 12px 细明体; CURSOR: default
				}
				.title {
				    BORDER-RIGHT: #555 1px solid; PADDING-RIGHT: 4px; BORDER-TOP: #fff 1px solid; PADDING-LEFT: 4px; BACKGROUND: #ccc; PADDING-BOTTOM: 4px; OVERFLOW: hidden; BORDER-LEFT: #fff 1px solid; CURSOR: hand; PADDING-TOP: 4px; BORDER-BOTTOM: #555 1px solid; WHITE-SPACE: nowrap
				}
				.cdata {
				    BORDER-RIGHT: #ddd 1px solid; PADDING-RIGHT: 3px; BORDER-TOP: #fff 1px solid; PADDING-LEFT: 3px; BACKGROUND: #fff; PADDING-BOTTOM: 3px; OVERFLOW: hidden; BORDER-LEFT: #fff 1px solid; PADDING-TOP: 3px; BORDER-BOTTOM: #ddd 1px solid; WHITE-SPACE: nowrap
				}
				</STYLE>";
	} // end func


	/**
	 * 说明
	 * 锁定表格所需要的JS
	 */
	private function lock_js()
	{
		return	'
			<SCRIPT language=JavaScript>
			var BoxWidthTony = 880    // 资料表显示宽度 ( 不含卷轴 )
			var ShowLine = 10    // 资料表显示列数
			var RsHeight = 21    // 资料列高度
			var LockCols = 3    

			function WriteTable()
			{    // 写入表格
				var iBoxWidthTony=BoxWidthTony
				var NewHTML="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>\
					<td><div style=\"width:100%;overflow-x:scroll\">\
					<table border=\"2\" cellpadding=\"0\" cellspacing=\"0\"><tr>"
				for(i=0;i<DataTitles.length;i++)
				{
					if(i<LockCols)
					{
						var cTitle=DataTitles[i].split("#")
						iBoxWidthTony-=cTitle[1]
						var DynTip=((i+1)==LockCols)?"解除锁定":"锁定此栏位"
						NewHTML+="<td><div class=\"title\" style=\"width:"+cTitle[1]+"px;height:"+RsHeight+"px\" title=\""+DynTip+"\" onclick=\"ResetTable("+i+")\">aa"+cTitle[0]+"</div></td>"
					}
				}
				NewHTML+="</tr>";
				for(i=0;i<DataFields.length;i++)
				{
					NewHTML+="<tr>"
					for(j=0;j<DataTitles.length;j++)
					{
						if(j<LockCols)
						{
							var cTitle=DataTitles[j].split("#")
							NewHTML+="<td><div class=\"cdata\" style=\"width:"+cTitle[1]+"px;height:"+RsHeight+"px;text-align:"+cTitle[2]+"\">ab"+DataFields[i][j]+"</div></td>"
						}
					}
					NewHTML+="</tr>"
				}
				NewHTML+="<tr><td colspan=\""+LockCols+"\">\
					<div id=\"DataFrame1\" style=\"position:relative;width:100%;overflow:hidden\">\
					<div id=\"DataGroup1\" style=\"position:relative\"></div></div>\
					</td></tr></table></div></td>\
					<td valign=\"top\"><div style=\"overflow-x:scroll;width:"+iBoxWidthTony+"px;width:400px;\">\
					<table border=\"2\" cellpadding=\"0\" cellspacing=\"0\"><tr>"
				for(i=0;i<DataTitles.length;i++)
				{
					if(i>=LockCols)
					{
						var cTitle=DataTitles[i].split("#")
						NewHTML+="<td><div class=\"title\" style=\"width:"+cTitle[1]+"px;height:"+RsHeight+"px\" title=\"锁定此栏位\" onclick=\"ResetTable("+i+")\">&nbsp;"+cTitle[0]+"</div></td>"
					}
				}
				NewHTML+="</tr>";
				for(i=0;i<DataFields.length;i++)
				{
					NewHTML+="<tr>"
					for(j=0;j<DataTitles.length;j++)
					{
						if(j>=LockCols)
						{
							var cTitle=DataTitles[j].split("#")
							NewHTML+="<td><div class=\"cdata\" style=\"width:"+cTitle[1]+"px;height:"+RsHeight+"px;text-align:"+cTitle[2]+"\">"+DataFields[i][j]+"</div></td>"
						}
					}
					NewHTML+="</tr>"
				}
				NewHTML+="</tr>\
					<tr><td colspan=\""+(DataTitles.length-LockCols)+"\">\
					<div id=\"DataFrame2\" style=\"position:relative;width:100%;overflow:hidden\">\
					<div id=\"DataGroup2\" style=\"position:relative\"></div>\
					</div></td></tr></table>\
					</div></td><td valign=\"top\">\
					<div id=\"DataFrame3\" style=\"position:relative;background:#000;overflow-y:scroll\" onscroll=\"SYNC_Roll()\">\
					<div id=\"DataGroup3\" style=\"position:relative;width:1px;visibility:hidden\"></div>\
					</div></td></tr></table>"
				TonyDataTable.innerHTML=NewHTML
			//	ApplyData()
			}


			function ResetTable(n)
			{
				var iBoxWidthTony=0
				for(i=0;i<DataTitles.length;i++)
				{
					if(i<(n+1))
					{
						var cTitle=DataTitles[i].split("#")
						iBoxWidthTony+=parseInt(cTitle[1])
					}
				}
				if(iBoxWidthTony>BoxWidthTony)
				{
					//var Sure=confirm("\n锁定栏位的宽度大於资料表显示的宽　　\n\n度，这可能会造成版面显示不正常。\n\n\n您确定要继续吗？")
				}
				else
				{
					Sure=true
				}
				if(Sure)
				{
					LockCols=(LockCols==n+1)?0:n+1
					WriteTable()
				}
			}

			function SYNC_Roll()
			{
				DataGroup1.style.posTop=-DataFrame3.scrollTop
				DataGroup2.style.posTop=-DataFrame3.scrollTop
			}
			window.onload=WriteTable
			</SCRIPT>
';
	} // end func
	/*#####################################################################################################################*/
}

?>