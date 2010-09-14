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
class Pft_Util_Export
{
	public static function ExportCsv( $array ,$csv_name=false)
	{
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
			ob_clean();			//header之前清除一下,否则会保存一些html
			//print"<pre>Terry :";var_dump( $csv_name );print"</pre>";
			//exit();
			
			header('Content-type: application/csv');
			//header("Accept-Ranges: bytes");
			//header('Content-Disposition: attachment; filename="XXX20070410.csv"');			
			header('Content-Disposition: attachment; filename="'.$csv_name.'.csv"');			
			
//			header( "Content-type: application/octetstream" );
//			header( "Content-Disposition: attachment; filename=".$csv_name.".csv" );

			//echo iconv("UTF-8", "GB2312", $str);
			echo $str;
			exit;
		}
		else
		{
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
			$cell	=	iconv("UTF-8", "GB2312", $cell);

			if (strchr($cell, $fd) !== FALSE || strchr($cell, $quot) !== FALSE || strchr($cell, "\n") !== FALSE)
			{
				$str .= $quot.$cell.$quot.$fd;
			}
			else
			{
				$str .= $cell.$fd;
			}
		}
		return $str;
	}
	/**
	 * john 添加
	 * 生成文件再下载的方法
	 * 2007-4-10
	 *
	 * @param unknown_type $array
	 * @param unknown_type $csv_name
	 */
	public static function ExportToCsv($array ,$csv_name=false)
	{
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
		
		$file=Pft_Config::getUploadPath().$csv_name.".csv";
		
		
		$handle=fopen($file,'w+');
		fputs($handle,$str);
		fclose($handle);
		$path=Pft_Config::getSiteRoot()."upload/".$csv_name.".csv";
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
			$rootpath=Pft_Config::getUploadPath();
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
		
		$file=$rootpath.$csv_name.".csv";
		
		
		$handle=fopen($file,'w+');
		fputs($handle,$str);
		fclose($handle);
		//$path=$rootpath.$csv_name.".csv";
		$path=Pft_Config::getSiteRoot()."upload/res/error/".$csv_name.".csv";
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
}


?>