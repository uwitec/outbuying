<?
include( 'ebook.conf.php' );
/**
 * 遍历显示所有文件
 *
 * @param string $dirName
 */
function showFiles( $dirName )
{
	/**
	 * 为了安全，去掉不规范路径
	 * @author terry
	 * @version 0.1.0
	 * Sat Sep 29 12:30:07 CST 2007
	 */
	$dirName = str_replace( '../', '', $dirName );
	
	
	$d = dir( $dirName );

	while (false !== ($entry = $d->read()))
	{
		if( $entry == "." || $entry == ".." )continue;
		
		$other = "";
   		if( is_file( $d->path.$entry ) )
   		{
   			//-----------------
   			//$relPath = str_replace( PATH_SITE, "", $d->path);
   			//$link = "/".urlencode(hideSlash($relPath). $entry );
   			//以上为直接下载的代码
   			
   			$relPath = str_replace( PATH_UPLOAD, "", $d->path);
   			$link = "download.php?path_file_name=".urlencode(hideSlash($relPath). $entry );
   			$other .= " ( ".filesize($d->path.$entry)." bytes ) ";
			$class = "file";
   		}
   		else
   		{
   			$relPath = str_replace( PATH_UPLOAD, "", $d->path);
   			$link = "?d=".urlencode(hideSlash($relPath). $entry )."/";
			$class = "folder";
   		}
   		echo "<a href=\"".unhideSlash( $link )."\" class=\"{$class}\">"
   		    .$entry.$other."</a><br/>\n";
	}
	$d->close();
}

function hideSlash( $str )
{
	return str_replace( "/", "-xbx-", $str);
}

function unhideSlash( $str )
{
	return str_replace( "-xbx-", "/", $str);
}

$d = isset( $_REQUEST["d"] )?PATH_UPLOAD.urldecode($_REQUEST["d"]):PATH_UPLOAD;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/pft.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Logviewer</title>
<style>
.filename{
	width:120px;height:120px;border:1px solid #369;text-align:center;
	margin:2px 2px 2px 2px;
	padding:2px 2px 2px 2px;
	line-height:200%;
}
</style>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<!--link href="../css/default/pft.css" rel="stylesheet" type="text/css" /-->
</head>
<body>
<div id="top">
	<div id="logo"></div>
</div>
<div id="menu">

</div>
<div id="mainbody">
<div class="filelist">
<?
showFiles( $d );
?>
</div>
<div class="cls"></div>
<!-- InstanceEndEditable -->
<!--mainbody-->
</div>
<div id="footer">

</div>
</body>
<!-- InstanceEnd --></html>
