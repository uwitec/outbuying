<?php
//die( '因流量问题,下载暂停.' );

include( 'ebook.conf.php' );
/*
$downloadCounter = Pft_Ebook_Download_Counter::factory();
if( !$downloadCounter->startDownload() ){
	header( "HTTP/1.1 403.9 To Many Users" );
	exit();
	//die( '达到了每分钟请求上限['.DOWNLOAD_COUNT_PER_MIN.']，请稍候再下载.' );
}
*/
$thePathFileName = $_REQUEST['path_file_name'];

/**
 * 为了安全，去掉不规范路径
 * @author terry
 * @version 0.1.0
 * Sat Sep 29 12:30:07 CST 2007
 */
$thePathFileName = str_replace( '../', '', $thePathFileName );
	
//$theFileName     = $_REQUEST['file_name'];
$theFileName     = basenameEx( $thePathFileName );

Pft_Log::addLog( 'Start download ['.$thePathFileName.' as ['.$theFileName.']. ', Pft_Log::LEVEL_INFO );

//setlocale(LC_ALL,"zh_CN");

//print "<pre>";var_dump( $thePathFileName );print "</pre>";
//print "<pre>";var_dump( $theFileName );print "</pre>";
//exit;

output_file( $thePathFileName, $theFileName );

function basenameEx( $filename ){
	$pos = strrpos( $filename, '/' );
	if( $pos !== false ){
		return substr( $filename, $pos + 1 );
	}else{
		return $filename;
	}
}

function output_file($file,$name)
{
	$file = PATH_UPLOAD.$file;
	//do something on download abort/finish
	//register_shutdown_function( 'function_name'  );
	if(!file_exists($file))
	die('file not exist!');
	$size = filesize($file);
	$name = rawurldecode($name);

	if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
	$UserBrowser = "Opera";
	elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
	$UserBrowser = "IE";
	else
	$UserBrowser = '';

	/// important for download im most browser
	$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ?
	'application/octetstream' : 'application/octet-stream';
	@ob_end_clean(); /// decrease cpu usage extreme
	header('Content-Type: ' . $mime_type);
	header('Content-Disposition: attachment; filename="'.$name.'"');
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header('Accept-Ranges: bytes');
	header("Cache-control: private");
	header('Pragma: private');

	/////  multipart-download and resume-download
	if(isset($_SERVER['HTTP_RANGE']))
	{
		list($a, $range) = explode("=",$_SERVER['HTTP_RANGE']);
		str_replace($range, "-", $range);
		$size2 = $size-1;
		$new_length = $size-$range;
		header("HTTP/1.1 206 Partial Content");
		header("Content-Length: $new_length");
		header("Content-Range: bytes $range$size2/$size");
	}
	else
	{
		$size2=$size-1;
		header("Content-Length: ".$size);
	}
	$chunksize = 1*(1024*1024);
	$bytes_send = 0;
	if ($file = fopen($file, 'r'))
	{
		if(isset($_SERVER['HTTP_RANGE']))
		fseek($file, $range);
		while(!feof($file) and (connection_status()==0))
		{
			$buffer = fread($file, $chunksize);
			print($buffer);//echo($buffer); // is also possible
			flush();
			$bytes_send += strlen($buffer);
			//sleep(1);//// decrease download speed
		}
		fclose($file);
	}
	else
	die('error can not open file');
	if(isset($new_length))
	$size = $new_length;
	die();
}