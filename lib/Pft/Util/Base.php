<?php
/**
 * 常用函数包
 *
 * 作用域: public
 * 作者: Londex
 * 版本: 1.0
 * 日期: 2006-5-19
 */
class Pft_Util_Base
{
	/**
 	* @return Array
 	* @param Array $N2Array
 	* @param String $keyname
 	* @desc 根据给出的字段名称返回一维数组
 	*/
	function N2toN1($N2Array,$keyname)
	{
		foreach ($N2Array as $key =>$val)
		{
			$N1Array[]	=	$val[$keyname];

		}
		return $N1Array;
	}

	function REQUESTtoQueryString($except)
	{
		if(is_array($_REQUEST))
		{
			foreach ($_REQUEST as $key	=>$val)
			{
				if(is_array($val))
				{
					continue;
				}
				elseif($key != $except)
				{
					$String	.=	$key."=".$val."&";
				}
			}
			return $String;
		}
	}

	function findInfo_From_N2_By_Value($value,$Array)
	{
		foreach ($Array as $key =>$val)
		{
			if($i=array_search($value,$val))
			{

				return $val;
			}
			else
			{
				continue;
			}
		}
	}

	function subStringByDescCount($String,$Count=null)
	{
		if($Count==null)
		{
			$Count=1;
		}
		return $String   =   substr($String,0,(strlen($String)-$Count));
	}

	/**
 	* @return void
 	* @param string $FileName 输出的文件名
 	* @param string $String 输出的字符串
 	* @param string $OpenMode 打开文件的模式
 	* @desc 将字符串输出到文件中
 	*/
	function saveToFile($FileName,$String,$OpenMode='r')
	{
		$fp	=	fopen($FileName,$OpenMode);
		fputs($fp,$String);
		fclose($fp);
	}

	function readFromFile($FileName)
	{
		return @readfile($FileName);
	}

	/**
 	* @return void
 	* @param string $filename 文件名
 	* @param array $inputArray 要输出的数组
 	* @desc 将数组输出到文件中
 	*/
	function saveArrayToFile($filename, $inputArray)
	{
		$str = var_export($inputArray);
		SaveToFile($filename, $str, 'w');
	}

	/**
     * Enter description here...
     *
     */
	function ConvertArray_GB2UTF8(&$array,$CharEncoding=null)
	{
		include_once SYSTEM_LIBRARY.'GB2312toUTF8.Class.php';
		$CharEncoding = new GB2312toUTF8;
		if(is_array($array))
		{
			foreach ($array as $key => $val)
			{

				BaseOption::ConvertArray_GB2UTF8(&$array[$key]);
			}
		}
		else
		{
			$array = $CharEncoding -> gb2utf8(&$array);
		}
		return $array;
	}
	function N12String($N1Array)
	{
		$res = @implode(",", $N1Array);
		return $res;
	}

	/**
	 * 本地服务器提交验证(验证方式:
	 *								all.主机级验证，由设定主机提交的数据全部有效
	 *								dir.目录级验证，由设定目录下提交的数据全部有效
	 *								file.文件级验证，由设定文件提交的数据有效
	 *					  )
	 *
	 * Detail description
	 * @param     string ([HOST_URL . BASE_NAME . ]$dir . $file) path, string method(file|dir|all)
	 * @since     1.0
	 * @access    private
	 * @return    boolean
	 * @update    2003-12-12
	 */
	function RequestCheck($path, $method = "file")
	{
		$request_url = explode("?",$_SERVER['HTTP_REFERER']);
		if ("dir" == $method && dirname($request_url[0]) == HOST_URL . BASE_NAME . $path)
		{
			return true;
		}
		elseif ("all" == $method && substr($request_url[0], 0, strlen(HOST_URL)) == HOST_URL)
		{
			return true;
		}
		elseif ("file" == $method && $request_url[0] == HOST_URL . BASE_NAME . $path) {
			return true;
		}
		$feedback = "非法提交数据";
		print("
		<script>
		alert('" . $feedback . "');
		</script>
		");
		exit();
	} // end func RequestCheck

	/**
	 * 转换< > ' " &为html字符
	 *
	 * Detail description
	 * @param     string|array $s
	 * @since     1.0
	 * @access    private
	 * @return    string|array
	 * @update    2003-12-09
	 */
	function HtmlConvert($s)
	{
		if (is_array($s))
		{
			while (list($key,$val) = each($s))
			{
				$s[$key] = htmlspecialchars($val);
			}

			return $s;
		}
		else
		{
			return htmlspecialchars($s);
		}
	} // end func HtmlConvert

	/**
	 * 在' " \ NUL字符前添加反斜线\
	 *
	 * Detail description
	 * @param     
	 * @since     1.0
	 * @access    private
	 * @return    void
	 * @update    data time
	 */
	function AddSlash($s)
	{
		if (is_array($s))
		{
			while (list($key,$val) = each($s))
			{
				$s[$key] = addslashes($val);
			}

			return $s;
		}
		else
		{
			return addslashes($s);
		}
	} // end func AddSlash

	/**
	 * 将日期转换为unix时间戳格式
	 *
	 * Detail description
	 * @param     date(datetime) date
	 * @since     1.0
	 * @access    public
	 * @return    int
	 * @update    2004-04-14
	 */
	function ConvDateFormat($date)
	{
		$date	=	BaseOption::replace_m($date);
		return	strtotime($date);

		if (strlen($date) < 11)		//short date format s:2003-12-08
		{
			$date = explode('-', $date);
			$date[3] = 0;
			$date[4] = 0;
			$date[5] = 0;
		}
		else							//long date format s:2003-12-08 16:04:15
		{
			$date = explode(" ", $date);
			$time = explode(':', $date[1]);
			$date = explode('-', $date[0]);
			$date[3] = $time[0];
			$date[4] = $time[1];
			$date[5] = $time[2];
		}

		//validity date
		if (!@checkdate($date[1], $date[2], $date[0]))
		{
			return false;
		}
		if (($date[3] < 0 || $date[3] > 23) || ($date[4] < 0 || $date[4] > 59) || ($date[5] < 0 || $date[5] > 59))
		{
			return false;
		}

		if ($date[0] < 1971)
		{
			$date[0] = 1970;
		}
		if ($date[0] > 2038)
		{
			$date[0] = 2038;
		}
		return mktime($date[3], $date[4], $date[5],$date[1],$date[2],$date[0]);
	} // end func ConvDateFormat
	
	
	/*
	 *说明:替换全角为半角
	 *Tue Dec 26 11:51:29 CST 2006--Tony
	 */
	function replace_m($str)
	{
		$arr = array(
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
		return $str;
	}
	/**
	 * 判断两日期时间差
	 *
	 * Detail description
	 * @param     date(mktime) olddate, date(mktime) newdate, format[sec,minute,hour,day,week] format
	 * @since     1.0
	 * @access    public
	 * @return    int
	 * @update    2004-04-15
	 */
	function getNewDate($olddate, $newdate, $format="day")
	{
		$timed = $newdate-$olddate;
		//秒
		if ("sec" == $format)
		{
			return $timed;
		}
		//分
		if ("minute" == $format)
		{
			return floor($timed/60);
		}
		//小时
		if ("hour" == $format)
		{
			return floor($timed/3600);
		}
		//天
		if ("day" == $format)
		{
			return floor($timed/86400);
		}
		//周
		if ("week" == $format)
		{
			return floor($timed/674800);
		}

		return false;
	} // end func getNewDate

	/**
	 * 截去指定长度的中(英)文字符串(以字节计)
	 *
	 * Detail description
	 * @param     string str, int start [,int end]
	 * @since     1.0
	 * @access    public
	 * @return    string
	 * @update    2003-08-28
	 */
	function c_substr($str,$start=0) {
		$ch = chr(127);
		$p = array("/[\x81-\xfe]([\x81-\xfe]|[\x40-\xfe])/","/[\x01-\x77]/");
		$r = array("","");
		if(func_num_args() > 2)
		$end = func_get_arg(2);
		else
		$end = strlen($str);
		if($start < 0)
		$start += $end;

		if($start > 0) {
			$s = substr($str,0,$start);
			if($s[strlen($s)-1] > $ch) {
				$s = preg_replace($p,$r,$s);
				$start += strlen($s);
			}
		}
		$s = substr($str,$start,$end);
		$end = strlen($s);
		if($s[$end-1] > $ch) {
			$s = preg_replace($p,$r,$s);
			$end += strlen($s);
		}
		return substr($str,$start,$end);
	}// end func c_substr

	/**
	 * 中文字符串截取,可截gb2312和UTF-8
	 *
	 * 参数: 
		$str,要截取的字符串
		$start,开始位置
		$end,截取长度
		$code,编码'UTF-8'
	 * @since     1.0
	 * @access    public
	 * @return    string
	 * @update    2003-08-28
	 */
	function m_substr() {
		$str   = func_get_arg(0);
		$start = func_get_arg(1);
		if (func_num_args() >= 4)
		{
			$end   = func_get_arg(2);
			$code  = func_get_arg(3);
		}
		if($code == 'UTF-8')
		{
			$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
			preg_match_all($pa, $str, $t_str);
			if(count($t_str[0]) - $start > $end)
			{
				$new_str	= join('', array_slice($t_str[0], $start, $end))."...";
			} else
			{
				$new_str	= join('', array_slice($t_str[0], $start, $end));
			}
		}
		else
		{
			preg_match_all("/[\x80-\xff]?./",$str,$ar);
			if(func_num_args() >= 3) {
				$end = func_get_arg(2);
				if ($end < count($ar[0])) {
					$new_str	= join("",array_slice($ar[0],$start,$end))."...";
				} else {
					$new_str	= join("",array_slice($ar[0],$start,$end));
				}
			}else {
				$new_str	= join("",array_slice($ar[0],$start));
			}
		}
		return	$new_str;
	}// end func m_substr
	//echo m_substr("有abw人的一要我遥",0,9);

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
	function m_strlen($str)
	{
		preg_match_all("/[\x80-\xff]?./",0,$ar);
		return count($ar[0]);
	} // end func m_strlen
	/**
	 * 说明
	 *
	 * 参数: void
	 * 返回: void
	 * 作用域: public
	 * 日期:
	 */
	function return_project_sign($val)
	{
		
	   global $db;
	   $sql="select incident_project_sign from tpm_incident where incident_order_sign='$val' and incident_project_sign<>'' and incident_type='项目' and incident_process_mark='is_ok'";
	  
		$project_sign=$db->getone($sql);
		return "<a href='index.php?id=".$val."&do=client_feedbacklist'>".$project_sign."</a>";
		
	}

	/**
	说明:生成数字,大小写字母组成的任意位数的字符串
	参数传递:$start和$end为开始和结束的字符串位数,如6,10则生成6到10位之间的密码,包括其本身
			 $t,输出的类型,0为全部小写和数字的组合,1为全部大写和数字的组合,3为全部数字的组合,4为大小写字母的组合
			 (int,int,int)
	返回值:字符串(String)
	*/
	function randStr($start=6,$end=8,$t=0,$return_str=''){
		$type = $t;
		$r = array(
		0=>rand(49,57),//1-9的ASCII码
		1=>rand(65,90),//A-Z的ASCII码
		2=>rand(97,122)//a-z的ASCII码
		);

		if($type == 3)
		{
			$re = chr($r[rand(0,0)]);
		}
		else
		{
			$re = chr($r[rand(0,2)]);
		}

		$s = rand($start,$end);

		while(true){
			if(strlen($return_str)>=$s)
			{
				return substr($return_str,0,$s);
			}
			$tmp = $re;
			if($type == 0)
			{
				$tmp = strtolower($tmp);
			}
			else
			{
				$tmp = $type==1?strtoupper($tmp):$tmp;
			}
			if(stristr($return_str,$tmp) == true)
			{
				BaseOption::randStr($start,$end,$t,&$return_str);
				continue;
			}
			$return_str .= (string)$tmp;
		}
		return $return_str;
	}

	/**
	 * 转换html字符为< > " &
	 *
	 * Detail description
	 * @param     string|array $s
	 * @since     1.0
	 * @access    private
	 * @return    string|array
	 * @update    2003-12-09
	 */
	function HtmlConvertBack($s)
	{
		if (is_array($s))
		{
			foreach ($s as $key => $value)
			{
				$s[$key] = preg_replace(array('/&amp;/', '/&quot;/', '/&lt;/', '/&gt;/'), array('&', '\'', '<', '>'), $value);
			}
			return $s;
		}
		else
		{
			$s = preg_replace(array('/&amp;/', '/&quot;/', '/&lt;/', '/&gt;/'), array('&', '\'', '<', '>'), $s);
			return $s;
		}
	}
	

	/**
	 * 获取时间的微妙数
	 *
	 * Detail description
	 * @param
	 * @since     1.0
	 * @access    private
	 * @return    void
	 * @update    date time
	 */
	function getmicrotime()
	{
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}

	/**
	 * 获取运行时间
	 *
	 * 参数: void
	 * 返回: float
	 * 作用域: public
	 * 日期: 2005-6-8
	 */
	function getTimeLimit()
	{
		return (BaseOption::getmicrotime() - SYS_TIME_SRART);
	} // end func

	/**
	 * alert
	 */
	function js_alert($alert,$url='')
	{
		echo("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />");
		echo("<script>");
		echo("alert(\"".$alert."\")");
		echo("</script>");
		if($url!="")
		{
			echo("<meta HTTP-EQUIV=REFRESH CONTENT=\"0;URL=" .$url. "\">");
			//			header ('Location:'.$url );
			exit;
		}
	}
	/**
	 * php数组获取js数组
	 *
	 * 参数: void
	 * 返回: void
	 * 作用域: public
	 * 日期: 2006-9-29
	 */
	function makeJsArray($array_data,$array_name)
	{
		if (!is_array($array_data))
		{
			$array_data = array();
		}
		$html = "var ".$array_name . "=[];";

		for ($i=0; $i< count($array_data); $i++)
		{
			$strTmp = str_replace(chr(13),"\\n",$array_data[$i]);//JOhn   替换回车、换行2006-12-14
			$strTmp = str_replace(chr(10),"",$strTmp);
			$html .= $array_name."[".$i."]='".$strTmp."';";
		}

		
		return $html;
	} // end func

	/**
	 * 载入模块信息
	 *
	 * 参数:
		$module : 模块id
		$count : 信息数量
	 * 返回: array
	 * 作用域: public
	 * 日期: 2006-5-22
	 */
	function loadMD($module, $count=0)
	{
		global $db;
		$result = $db->_db->GetRow("SELECT ds_data FROM tpm_datastore  where ds_title='" . $module . "'");
		if (!empty($result['ds_data'])) {
			if (empty($count)) {
				return unserialize($result['ds_data']);
			} else {
				$data_t = unserialize($result['ds_data']);
				$data = array();
				if ($count) {
					for ($i=0; $i<count($data_t) && $i<$count; $i++) {
						$data[$i] = $data_t[$i];
					}
				} else {
					$data = $data_t;
				}
				return $data;
			}
		} else {
			return '';
		}
	} // end func

	/**
	 * 保存模块信息
	 *
	 * 参数:
		$module : 模块id
		$data : 数据
	 * 返回: boolean
	 * 作用域: public
	 * 日期: 2006-5-22
	 */
	function saveMD($module, $data)
	{
		global $db;
		$result = $db->_db->GetRow("SELECT ds_title FROM tpm_datastore  where ds_title='" . $module . "'");
		if (!empty($result['ds_title'])) {
			$query = "UPDATE tpm_datastore SET ds_data='" . addslashes(serialize($data)) . "' WHERE (ds_title='" . $module . "')";
		} else {
			$query = "INSERT INTO tpm_datastore SET ds_title='" . $module . "',ds_data='" . addslashes(serialize($data)) . "'";
		}
		return $db->_db->Execute($query);
	} // end func

	/**
	 * post数据
	 */
	function postToHost($url, $data) {
		$url = parse_url($url);
		if (!$url) return "couldn't parse url";
		if (!isset($url['port'])) { $url['port'] = ""; }
		if (!isset($url['query'])) { $url['query'] = ""; }

		$encoded = "";

		while (list($k,$v) = each($data)) {
			$encoded .= ($encoded ? "&" : "");
			$encoded .= rawurlencode($k)."=".rawurlencode($v);
		}

		$fp = fsockopen($url['host'], $url['port'] ? $url['port'] : 80);
		if (!$fp) return "Failed to open socket to $url[host]";

		fputs($fp, sprintf("POST %s%s%s HTTP/1.0\n", $url['path'], $url['query'] ? "?" : "", $url['query']));
		fputs($fp, "Host: $url[host]\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
		fputs($fp, "Content-length: " . strlen($encoded) . "\n");
		fputs($fp, "Connection: close\n\n");

		fputs($fp, "$encoded\n");

		$line = fgets($fp,1024);
		if (!eregi("^HTTP/1\.. 200", $line)) return;

		$results = ""; $inheader = 1;
		while(!feof($fp)) {
		$line = fgets($fp,1024);
		if ($inheader && ($line == "\n" || $line == "\r\n")) {
			$inheader = 0;
		}
		elseif (!$inheader) {
			$results .= $line;
		}
		}
		fclose($fp);

		return $results;
	}

}
?>