<?
/**
 * 文件管理类
 *
 */
class Watt_Util_File{
	/**
	 * 根据路径创建目录
	 *
	 * 参数: void
	 * 返回: void
	 * 作用�?: public
	 * 日期: 2007-02-07
	 * 作�??: Jute
	 */
	function create_dir($file)
	{		
		//$dir_name=dirname($file);
		//mkdir($dir_name,0777,true);
		mkdir($file,0777,true);
		return true;
	}
	/**
	 * 删除指定目录下修改时间小于指定时间戳的文件
	 * jute 2007-09-14
	 */
	function delfilebytime($dir='',$time=false){		
		//默认当前时间
		if (!$time) $time = time();		
		if (is_dir($dir))
		{//目录
			$handle = opendir($dir); 
			while ($file = readdir($handle)) {//循环目录
				if($file=='.' || $file=='..' || $file=='.svn'){//不删除的文件或目录
					
				}else {		
					if(is_dir($dir.$file)){	
						Watt_Util_File::delfilebytime($dir.$file.'/',$time);
					}else{
						if(filemtime($dir.$file) < $time ){//文件更新时间小于指定时间
							//删除文件
							unlink($dir.$file);
						}
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * 移动指定目录下修改时间小于指定时间戳的文件到另一目录
	 * jute 2007-09-14
	 */
	function movefilebytime($dir='',$todir='',$time=false){
		//默认当前时间
		if (!$time) $time = time();	
		
		if($dir=='' || $todir==''){
			return false;
		}
			
		if(!is_dir($todir)){//目录不存在创建
			//Watt_Util_File::create_dir($todir.'asd');
			/**
			 * 之前仿佛是错误吧..
			 * @author terry
			 * @version 0.1.0
			 * Sat Sep 15 12:51:42 CST 2007
			 */
			Watt_Util_File::create_dir($todir);
		}
		
		if (is_dir($dir))
		{//目录
			$handle = opendir($dir); 
			while ($file = readdir($handle)) {//循环目录
				if($file=='.' || $file=='..' || $file=='.svn'){//不操作的文件或目录
					
				}else {		
					if(is_dir($dir.$file)){	
						Watt_Util_File::delfilebytime($dir.$file.'/',$time);
					}else{
						
						if(filemtime($dir.$file) < $time ){//文件更新时间小于指定时间
							copy($dir.$file,$todir.$file);
							//删除文件
							unlink($dir.$file);
						}
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * 上传指定目录下的文件到文件管理器（http）
	 *
	 * @param string $filepath 文件路径
	 * jute
	 * 20080115
	 */
	function  httpuploadfile($filepath=''){
		if(is_file($filepath)){			
			if( Watt_Util_Net::isLANIp( $_SERVER['SERVER_ADDR'] ) ){
				$filemanager = Watt_Config::getCfg( 'TQ_FTP_MANAGER' );
				$ftp_upload_path = Watt_Config::getFtpDir();
			}else{
				$filemanager = Watt_Config::getCfg('FtpServerOuterManager');	
				$ftp_upload_path = Watt_Config::getCfg('FtpDirOuter');		
			}
			//	$filemanager ="http://filemanager.transn.net/";
			$url = $filemanager.'?do=upload_tq';
			$par = array('userfile' => "@".realpath($filepath),
						 'ftpdir' => $ftp_upload_path,
						 //'flag' => 'tpm.transn.net'
						 );
			return Watt_Http_Client::curlPost( $url, $par );			
		}else{
			return '';
		}
		return '';
	}
	/**
	 * 下载文件（http）
	 *
	 * @param unknown_type $filepath
	 * @return unknown
	 * jute
	 * 20080115
	 */
	function  httpdownloadfile($filepath=''){
		if( Watt_Util_Net::isLANIp( $_SERVER['SERVER_ADDR'] ) ){
			$filemanager = Watt_Config::getCfg( 'TQ_FTP_MANAGER' );
			$ftp_upload_path = Watt_Config::getFtpDir();
		}else{
			$filemanager = Watt_Config::getCfg('FtpServerOuterManager');	
			$ftp_upload_path = Watt_Config::getCfg('FtpDirOuter');		
		}
		//$filemanager ="http://filemanager.transn.net/";
		$url = $filemanager.'?do=download';
		$url = $url."&ftpRelPath=".$ftp_upload_path."&ftpName=".$filepath;
		
	    return file_get_contents($url);
	}
	
	/**
	 * 检查http方式上传下载是否正常函数
	 *
	 * @return unknown
	 * jute
	 * 20080115
	 */
	function checkhttpfile(){
		$testuploadfiledir = Watt_Config::getRootPath().'htdocs/upload/';
		$testuploadfilepath = $testuploadfiledir.'testupload.txt';
		
		if(!file_exists($testuploadfilepath)){//上传测试文件不存在则创建
			$fp = fopen($testuploadfilepath, 'wb');
			fwrite($fp, 'aa');
			fclose($fp);			
		}
		
		$file = Watt_Util_File::httpuploadfile($testuploadfilepath);
		if($file){//上传文件成功
			//下载文件
			$rev = Watt_Util_File::httpdownloadfile($file);	
		    if(!strcmp($rev,file_get_contents($testuploadfilepath))){//返回结果相同    	
					return true;	    	
		    }else{//
		    	 return false;
		    }
		}
		return false;
	}
	/**
	 * ftp方式上传指定目录下的文件
	 *
	 * @param unknown_type $filepath
	 * @return unknown
	 * jute
	 * 20080115
	 */
	function ftpuploadfile($filepath=''){		
		if(is_file($filepath)){
			$tpmwenjian = new TpmWenjian();
			return $tpmwenjian->uploadftpfile($filepath);
		}else{
			return '';
		}
		return '';
	}
	
	/**
	 *  检查ftp方式上传下载是否正常函数
	 * 下载使用的是http方式
	 * @return unknown
	 * jute
	 * 20080115
	 */
	function checkftpfile(){
		$testuploadfiledir = Watt_Config::getRootPath().'htdocs/upload/';
		$testuploadfilepath = $testuploadfiledir.'testupload.txt';
		
		if(!file_exists($testuploadfilepath)){//上传测试文件不存在则创建
			$fp = fopen($testuploadfilepath, 'wb');
			fwrite($fp, 'aa');
			fclose($fp);			
		}
		
		$file = Watt_Util_File::ftpuploadfile($testuploadfilepath);
		if($file){//上传文件成功	
			//下载文件
			$rev = Watt_Util_File::httpdownloadfile($file);	
		    if(!strcmp($rev,file_get_contents($testuploadfilepath))){//返回结果相同  	
					return true;	    	
		    }else{//
		    	 return false;
		    }
		}
		return false;
	}
	
	public static function resize_bytes($size)
	{
		$count = 0;
		$format = array("B","KB","MB","GB","TB","PB","EB","ZB","YB");
		while(($size/1024)>1 && $count<8)
		{
			$size=$size/1024;
			$count++;
		}
		$return = number_format($size,0,'','.')." ".$format[$count];
		return $return;
	}
	
	/**
	 * @param string $filename
	 * @return string
	 */
	public static function getFileExt($filename){
		$path_parts = pathinfo($filename);
		return $path_parts["extension"];
	}
	
	/**
	 * @return array
	 */
	public static function getAllowPreviewFileExts(){
		return array(
			'doc','xls','ppt','pps',
			'pdf',
			'txt','html',
		);
	}
	
	/**
	 * @param string $filename
	 * @return unknown
	 * @author terry
	 * Mon Feb 23 10:35:52 CST 2009
	 */
	public static function isAllowPreviewFile( $filename ){
		$ext = strtolower(self::getFileExt($filename));
		$allowExts = self::getAllowPreviewFileExts();
		return in_array( $ext, $allowExts );
	}

	const PREVIEW_TYPE_OFFICE = 'office';
	const PREVIEW_TYPE_PDF = 'pdf';
	const PREVIEW_TYPE_TXT = 'txt';
	const PREVIEW_TYPE_UNKNOWN = 'unknown';
	
	/**
	 * 获取文件预览类型
	 * @param string $extname 文件扩展名
	 * @return Watt_Util_File::PREVIEW_TYPE_*
	 * @author terry
	 * Mon Feb 23 10:28:59 CST 2009
	 */
	public static function getExtPreviewTypes( $extName ){
		$officeFile = array(
			'doc','xls','ppt','pps'
		);
		$pdfFile = array(
			'pdf'
		);
		$txtFile = array(
			'txt','html'
		);

		$extName = strtolower( $extName );
				
		if(in_array( $extName, $officeFile )){			
			$rev = self::PREVIEW_TYPE_OFFICE;
		}elseif(in_array( $extName, $pdfFile )){
			$rev = self::PREVIEW_TYPE_PDF;
		}elseif(in_array( $extName, $txtFile )){
			$rev = self::PREVIEW_TYPE_TXT;
		}else{
			$rev = self::PREVIEW_TYPE_UNKNOWN;
		}

		return $rev;
	}
}
?>