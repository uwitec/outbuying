<?
/**
 * 功能：
 *  controller
 * Actions:
 * 
 * 输入：
 * 
 * 输出：
 * 
 * @author :Tony
 *Tue Apr 10 10:17:32 CST 2007--10:17:32
 */
class Watt_Util_Export
{
	public static function ExportCsv( $arrayOrSting ,$csv_name=false)
	{
		if (!$csv_name)
			$csv_name	=	'dingdanbaobiao'.date( '-ymd-Hi',time() );
		else 
			$csv_name	=	$csv_name.date( '-ymd-Hi',time() );
		
		$str = '';
		if (is_array( $arrayOrSting )){
			$jc	=	current( $arrayOrSting );
			if (is_array($jc)){
				foreach($arrayOrSting as $key => $value){
					$str.=self::_outcsv($value)."\r\n";
				}
			}else{
				$str=self::_outcsv($arrayOrSting)."\r\n";
			}
		}else{
			$str = $arrayOrSting;
		}
		
		if( $str ){
			ob_clean();			//header之前清除一下,否则会保存一些html
			//print"<pre>Terry :";var_dump( $csv_name );print"</pre>";
			//exit();
			
			//header('Content-type: application/csv');
			//header("Accept-Ranges: bytes");
			//header('Content-Disposition: attachment; filename="XXX20070410.csv"');			
			//header('Content-Disposition: attachment; filename="'.$csv_name.'.csv"');	
					
			header("Accept-Ranges: bytes");
			header("Content-Length: ".strlen( $str ));
			header("Expires: 0");
			header("Cache-Control: private");
			header("Content-type: application/csv" );
			header("Content-Disposition: attachment; filename=\"".$csv_name.".csv\"" );
		
//			header( "Content-type: application/octetstream" );
//			header( "Content-Disposition: attachment; filename=".$csv_name.".csv" );

			//echo iconv("UTF-8", "GB2312", $str);
			echo $str;
			exit;
		}else{
			echo 'error';
			exit;
		}
	}



	private function _outcsv( $row, $fd=',', $quot='"')
	{
		$str='';
		foreach ($row as $cell)
		{
			$cell = str_replace($quot, $quot.$quot, $cell);
			//$cell	=	iconv("UTF-8", "GB2312", $cell);
			$cell	=	iconv("UTF-8", "GBK", $cell);

			if (strchr($cell, $fd) !== FALSE || strchr($cell, $quot) !== FALSE || strchr($cell, "\n") !== FALSE)
			{
				//$str .= $quot.$cell.$quot.$fd;
				$str .= $quot.str_replace($quot,$quot.$quot,$cell).$quot.$fd;
			}
			else
			{
				$str .= $cell.$fd;
			}
		}
		return $str;
	}
	
	/**
	 * 将数组输出为 CSV
	 *
	 * @param unknown_type $array
	 */
	public function ArrayToCsv( $array ){
		if (is_array( $array ))
		{
			$jc	=	current( $array );
			if (is_array($jc))
			{
				foreach($array as $key => $value)
				{
					$str.=self::_outcsv($value)."\r\n";
				}
			}
			else 
			{
				$str=self::_outcsv($array)."\r\n";
			}
		}
		else
		{
			$str = 'error';
		}
		return $str;
	}
	/*
	说明：保存任意文件 
	参数：
	作者：john
	时间：Tue Jul 15 14:26:13 CST 2008
	*/
	public static function ExportToDocument($body ,$csv_name=false,$expand_name='txt')
	{
		$str="";
	
		if (!$csv_name)
			$csv_name	=	'Export'.date( '-ymd-His',time() );
		else 
			$csv_name	=	$csv_name.date( '-ymd-His',time() );
		
		//防止重名
		$guid = Watt_Util_Utils::getGuId();
		$csv_name .= '-'.substr( $guid, 0, 8 );
		
		$file=Watt_Config::getUploadPath().$csv_name.".".$expand_name;
		$handle=fopen($file,'w+');
		fputs($handle,$body);
		fclose($handle);
		$path=Watt_Config::getSiteRoot()."upload/".$csv_name.".".$expand_name;
		return $path;
		
	}
	/**
	 * john 添加
	 * 生成文件再下载的方法
	 * 2007-4-10
	 *
	 * @param unknown_type $array
	 * @param unknown_type $csv_name
	 */
	public static function ExportToCsv($array ,$csv_name=false,$is_fullname=false)
	{
		$str="";
		if($is_fullname && $csv_name && $csv_name!=''){
			$csv_name.=".csv";
		}else{
			if (!$csv_name)
				$csv_name	=	'Export'.date( '-ymd-His',time() );
			else 
				$csv_name	=	$csv_name.date( '-ymd-His',time() );
			
			//防止重名
			$guid = Watt_Util_Utils::getGuId();
			$csv_name .= '-'.substr( $guid, 0, 8 ).".csv";
		}
		
		
		if (is_array( $array ))
		{
			$jc	=	current( $array );
			if (is_array($jc))
			{
				foreach($array as $key => $value)
				{
					$str.=self::_outcsv($value)."\r\n";
				}
			}
			else 
			{
				$str=self::_outcsv($array)."\r\n";
			}
		}
		else 
		{
			$str=$array;
		}
		$file=Watt_Config::getUploadPath().$csv_name;
		
		
		$handle=fopen($file,'w+');
		fputs($handle,$str);
		fclose($handle);
		$path=Watt_Config::getSiteRoot()."upload/".$csv_name;
		return $path;
		/*if($str!="")
		{
			
		$file1 = fopen($file,"rb"); // 打开文件 
  
		// 输入文件标签
		//ob_clean();		
		header("Content-Type:application/octet-stream");
		//header('Content-type: application/csv');
		header("Accept-Ranges:bytes");
		
		header("Accept-Length:".filesize($file));
		
		header('Content-Disposition:attachment;filename='.$csv_name.'.csv');
		//header('Content-Description: PHP3 Generated Data');

		// 输出文件内容
		
		echo  fread($file1,filesize($file));
		
		fclose($file1);
		
		exit;
		}*/
	}
	/**
	 * john 添加
	 * 生成文件再下载的方法(改变路径)
	 * 2007-4-10
	 *
	 * @param unknown_type $array
	 * @param unknown_type $csv_name
	 */
	public static function ExportToCsvPath($array ,$csv_name=false,$path=false)
	{
		if($path)
		{
			$rootpath=$path;
		}
		else 
		{
			$rootpath=Watt_Config::getUploadPath();
		}
		$str="";
	
		if (!$csv_name)
			$csv_name	=	'dingdanbaobiao'.date( '-ymd-Hi',time() );
		else 
			$csv_name	=	$csv_name.date( '-ymd-Hi',time() );
			
		if (is_array( $array ))
		{
			$jc	=	current( $array );
			if (is_array($jc))
			{
				foreach($array as $key => $value)
				{
					$str.=self::_outcsv($value)."\r\n";
				}
			}
			else 
			{
				$str=self::_outcsv($array)."\r\n";
			}
		}
		else 
		{
			$str=$array;
		}
		
		$file=$rootpath.$csv_name.".csv";
		
		
		$handle=fopen($file,'w+');
		fputs($handle,$str);
		fclose($handle);
		//$path=$rootpath.$csv_name.".csv";
		$path=Watt_Config::getSiteRoot()."upload/res/error/".$csv_name.".csv";
		return $path;
		/*if($str!="")
		{
			
		$file1 = fopen($file,"rb"); // 打开文件 
  
		// 输入文件标签
		//ob_clean();		
		header("Content-Type:application/octet-stream");
		//header('Content-type: application/csv');
		header("Accept-Ranges:bytes");
		
		header("Accept-Length:".filesize($file));
		
		header('Content-Disposition:attachment;filename='.$csv_name.'.csv');
		//header('Content-Description: PHP3 Generated Data');

		// 输出文件内容
		
		echo  fread($file1,filesize($file));
		
		fclose($file1);
		
		exit;
		}*/
	}
	/**
	 * john 添加
	 * 生成文件再下载的方法
	 * 2007-4-10
	 *
	 * @param unknown_type $array
	 * @param unknown_type $csv_name
	 */
	public static function ExportToCsvByTest($array ,$csv_name=false)
	{
		$str="";
	
		if (!$csv_name)
			$csv_name	=	'Export'.date( '-ymd-His',time() );
		else 
			$csv_name	=	$csv_name.date( '-ymd-His',time() );
		
		//防止重名
		$guid = Watt_Util_Utils::getGuId();
		$csv_name .= '-'.substr( $guid, 0, 8 );
		
		if (is_array( $array ))
		{
			foreach($array as $key => $value)
			{
				if(is_array($value)&&count($value)>0)
				{
					$str.=self::_outcsv($value)."\r\n";
				}
				else 
				{
				$str.=iconv("UTF-8", "GBK", $value)."\r\n";
				}
			}

		}
		else 
		{
			$str=iconv("UTF-8", "GBK", $array);
		}
		
		$file=Watt_Config::getUploadPath().$csv_name.".csv";
		
		
		$handle=fopen($file,'w+');
		fputs($handle,$str);
		fclose($handle);
		$path=Watt_Config::getSiteRoot()."upload/".$csv_name.".csv";
		return $path;
		
	}

	/**
	 * 导出任意文件并下载
	 * @author terry
	 * @version 0.1.0
	 * Wed Dec 17 20:52:32 CST 2008
	 */
	public static function ExportToDocumentAndDownload($body ,$csv_name=false,$expand_name='txt')
	{
		if (!$csv_name)
			$csv_name	=	'Export'.date( '-ymd-His',time() );
		else 
			$csv_name	=	$csv_name.date( '-ymd-His',time() );
		
		ob_clean();			//header之前清除一下,否则会保存一些html
		header("Accept-Ranges: bytes");
		header("Content-Length: ".strlen( $body ));
		header("Expires: 0");
		header("Cache-Control: private");
//		header("Content-type: application/csv" );
		header( "Content-type: application/octetstream" );
		header("Content-Disposition: attachment; filename=\"".$csv_name.".".$expand_name."\"" );
//		header( "Content-Disposition: attachment; filename=".$csv_name.".csv" );

		$body = <<<EOT
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
{$body}
</body>
</html>
EOT;
		//echo iconv("UTF-8", "GBK", $body);
		echo $body;
		exit;
	}
	/*
	* 说明：使用EXCEL组件导出文件 生成扩展名为 .xls
	* 参数：$arr 将要导出的数据  $path 路径 $file_name 文件的名字 $is_fullname 是否不自动生成文件名 $expand_name 文件的扩展名 $is_model 是否提供模板(只是修改模板) $is_output 直接输出不生成文件
	* 作者：John
	* 时间：Tue Apr 07 09:16:02 CST 2009
	*/
	/*
	构成数组的方法:$arr
	例如:
	一:
	老数据(原来组成的CSV数组格式) 可以直接使用  自动生成XLS
	$arr=array("0"=>array("标题一","标题二"),"1"=>array("数据a","数据b"),"2"=>array("数据a","数据b"),.....);
	说明: (1),数据中的"0"键值代表 标题    
	     (2), 1,2,...键值代表了数据
	     
	二:
	新数据(新的数组)
	$arr=array
	(
		"0"=>array("标题一","标题二"),"1"=>array("数据a","数据b"),"2"=>array("数据a","数据b"),......
	);
	说明: (1),数据中的"0"键值代表 标题    
	     (2), 1,2,...键值代表了数据
	     (3),数据a,数据b等 数据 可以是构造好的数据 也可以是EXCEL函数 如:
	     字符串 连接:		'=B1 & " " & B2'
	     相加:			'=SUM(C1:B1)'
	三:
	根据已经提供的模板 修改XLS格式
	$arr=array('A1'=>'数据1','B2'=>'=SUM(C1:D1)',......);
	说明:
	A1:对应的EXCEL中哪一行 哪一列   数据 对应的值  可以是数据也可以是EXCEL函数
	总的来说在构造数组时 想要改 EXCEL哪一行 哪一列  就写入数据中 传递过来即可
	*/
	public static function ExportToXls($arr,$file_name=null,$is_fullname=false,$expand_name='xls',$path=null,$is_model=false,$is_output=false)
	{
		set_time_limit(36000000);
		if(!$expand_name||$expand_name=='')
		{
			$expand_name='xls';
		}
		if(strtolower($expand_name)!='xls'&&strtolower($expand_name)!='pdf')//暂时不支持Pdf格式
		{
			$expand_name='xls';
		}
		if($is_fullname && $file_name && $file_name!=''){
			
		}else{
		if (!$file_name)
			$file_name	=	'Export'.date( '-ymd-His',time() );
		else 
			$file_name	=	$file_name.date( '-ymd-His',time() );
		
		//防止重名
		$guid = Watt_Util_Utils::getGuId();
		$file_name .= '-'.substr( $guid, 0, 8 );
		}
		if($path)
		{
			if(!$is_model)
			{
				$new_path=$path.$file_name.".".$expand_name;
			}
		}
		else 
		{
			$new_path=Watt_Config::getUploadPath().$file_name.".".$expand_name;
		}
		//输出的路径
		$out_path=Watt_Config::getSiteRoot()."upload/".$file_name.".".$expand_name;
		if($is_model)//使用模板(只是修改)
		{
			self::getXlsByModel($arr,$path,$new_path,$file_name,$expand_name,$is_output);
		}
		else 
		{
			self::getXlsPathByArr($arr,$new_path,$file_name,$expand_name,$is_output);
		}
		return $out_path;
	}
	private static function getXlsPathByArr($arr,$path,$file_name,$expand_name,$is_output)
	{
		include Watt_Config::getLibPath().'Third/Excel/PHPExcel.php';
		include Watt_Config::getLibPath().'Third/Excel/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();
		/*$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
		$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
		$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
		$objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
		$objPHPExcel->getProperties()->setCategory("Test result file");
		*/
		$objPHPExcel->setActiveSheetIndex(0);
		if(count($arr)>0)
		{
			for($i=0;$i<count($arr);$i++)
			{
				for($j=0;$j<count($arr[$i]);$j++)
				{
					
					$_E=self::getExcelE($j+1);
					$sheet_sp=$_E.($i+1);
					//不使用 ICONV转换字符集
					//$objPHPExcel->getActiveSheet()->setCellValue($sheet_sp, iconv("UTF-8", "GBK", $arr[$i][$j]));
					$objPHPExcel->getActiveSheet()->setCellValue($sheet_sp,  $arr[$i][$j]);
				}
			}
		}
		//$objPHPExcel->getActiveSheet()->setTitle($file_name);
		$objPHPExcel->setActiveSheetIndex(0);
		if($expand_name=='pdf')
		{
			$out_geshi='PDF';
		}
		else {
			$out_geshi='Excel5';
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $out_geshi);
		if($is_output)
		{
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
			header("Content-Type:application/force-download");
			if($expand_name=='pdf')
			{
				header("Content-Type:application/pdf");
			}
			else 
			{
			header("Content-Type:application/vnd.ms-execl");
			}
			header("Content-Type:application/octet-stream");
			header("Content-Type:application/download");
			header('Content-Disposition:attachment;filename="'.iconv("UTF-8","GBK",$file_name).".".$expand_name.'"');
			header("Content-Transfer-Encoding:binary");
			$objWriter->save('php://output');

		}
		else 
		{
			$objWriter->save($path);
		}
		
		//return $path;
		
	}
	/*
	* 说明：实现EXCEL横坐标
	* 参数：
	* 作者：John
	* 时间：Wed Apr 15 15:43:09 CST 2009
	*/
	private static function getExcelE($sum)
	{
		$s=floor($sum/26);
		$y=floor($sum%26);
		if($y==0)
		{
			$s=$s-1;
			$y=26;
		}
		$L=64+$s;
		$R=64+$y;
		$L_S=null;
		$R_S=null;
		if($L>=65&&$L<=90)
		{
			$L_S=chr($L);
		}
		if($R>=65&&$R<=90)
		{
			$R_S=chr($R);
		}
		
		if($L_S&&$R_S)
		{
			$_zuobiao=$L_S.$R_S;
		}
		else 
		{
			$_zuobiao=$R_S;
		}
		return $_zuobiao;
	}
	/*
	* 说明：根据提供的模板 修改传入的EXCEL文件
	* 参数：
	* 作者：John
	* 时间：Tue Apr 07 13:45:26 CST 2009
	*/
	private static function getXlsByModel($arr,$old_path,$new_path,$file_name,$expand_name,$is_output)
	{
		include Watt_Config::getLibPath().'Third/Excel/PHPExcel.php';
		include Watt_Config::getLibPath().'Third/Excel/PHPExcel/IOFactory.php';
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objPHPExcel = $objReader->load($old_path);
		if(!$objPHPExcel)
		{
			throw new Exception('Load Excel Path error.');
		}
		if(count($arr)>0)
		{
			foreach ($arr as $key => $val)
			{
				$objPHPExcel->getActiveSheet()->setCellValue($key,  $val);
			}
		}
		//$objPHPExcel->getActiveSheet()->setTitle($file_name);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		if($is_output)
		{
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
			header("Content-Type:application/force-download");
			header("Content-Type:application/vnd.ms-execl");
			header("Content-Type:application/octet-stream");
			header("Content-Type:application/download");
			header('Content-Disposition:attachment;filename="'.iconv("UTF-8","GBK",$file_name).".".$expand_name.'"');
			header("Content-Transfer-Encoding:binary");
			$objWriter->save('php://output');

		}
		else 
		{
			$objWriter->save($path);
		}
		//$objWriter->save($new_path);
		//return $new_path;
	}
}
?>