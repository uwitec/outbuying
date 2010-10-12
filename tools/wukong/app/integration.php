<?
/**
 * 功能：
 *  controller
 * 
 * Actions:
 *
 * index
 * add
 * list
 * edit
 * delete
 * 
 * 
 * 
 * 输入：
 * 
 * 输出：
 * 
 * @since 2
 * @author Terry
 */

class WukongIntegrationController extends Watt_Controller_Action
{

/**
 * 集成工具
 * 将个人目录中的
 * app/
 * lib/
 * view/
 *
 * 目录中的文件复制到集成目录
 *
 * @author terry
 * @version v1.0
 */
	function indexAction()
	{
		return false;
		/**
		 * 禁止提交；
		 */
		//error_reporting(E_ALL);
		//include '/home/terry/integration.php';

		//$fromModelDir = dirname( __FILE__ );

		$fromModelDir = Watt_Config::getRootPath();
		$toModelDir   = Watt_Config::getRootPath(1);
		//$toModelDir   = "/site/includes/tpm/";
		if( $toModelDir == "" ){
			echo "Target dir is blank!";
			exit();
		}

		echo "<pre>";
		echo "1) copying app files...\n\n";
		$fromFolder = $fromModelDir.'app/';
		$toFolder   = $toModelDir.'app/';
		folderFilesCopy( $fromFolder, $toFolder, true );

		echo "2) copying lib files...\n\n";
		$fromFolder = $fromModelDir.'lib/';
		$toFolder   = $toModelDir.'lib/';
		folderFilesCopy( $fromFolder, $toFolder, true );

		echo "3) copying view files...\n\n";
		$fromFolder = $fromModelDir.'view/';
		$toFolder   = $toModelDir.'view/';
		folderFilesCopy( $fromFolder, $toFolder, true );

		echo "4) copying language files...\n\n";
		$fromFolder = $fromModelDir.'language/';
		$toFolder   = $toModelDir.'language/';
		folderFilesCopy( $fromFolder, $toFolder, true );
		
		echo "5) copying js files...\n\n";
		$fromFolder = $fromModelDir.'htdocs/js/';
		$toFolder   = $toModelDir.'htdocs/js/';
		folderFilesCopy( $fromFolder, $toFolder, true );

		echo "6) copying css files...\n\n";
		$fromFolder = $fromModelDir.'htdocs/css/';
		$toFolder   = $toModelDir.'htdocs/css/';
		folderFilesCopy( $fromFolder, $toFolder, true );
		
		echo "</pre>";
	}
}


/**
 * 说明
 * 将来源目录中的文件复制到目标目录中
 *
 * 参数: void
 * 返回: void
 * 修饰: public
 * 日期:
 */
function folderFilesCopy( $fromFolder, $toFolder, $overwrite = false )
{
	//echo( $fromFolder . "|" . $toFolder . "\n" );
	$d = dir( $fromFolder );
	if( is_object( $d ) )
	{
		$path = $d->path;
		while (false !== ($entry = $d->read())) {
			if( $entry == "." || $entry == ".." || $entry == ".svn" )continue;
	
			//$filename = $path . DIRECTORY_SEPARATOR . $entry;
			$filename = $path . $entry;
			//$toFileName = $toFolder . DIRECTORY_SEPARATOR . $entry;
			$toFileName = $toFolder . $entry;
			if( is_file( $filename ) )
			{
				if( !file_exists( $toFileName )
				  || ( $overwrite && md5_file( $filename ) != md5_file( $toFileName ) ) )
				{
					$sinceFrom = fileSince( $filename );
					$sinceTo   = fileSince( $toFileName );
					if( $sinceFrom >= $sinceTo )
					{
						//如果文件不同since相同，仍然覆盖
						echo( "Copying [" . $filename . "](Since " . $sinceFrom . ") to [" . $toFileName . "](Since " . $sinceTo . ")\n" );
						copy( $filename, $toFileName ); 
						@chmod( $toFileName, 0777 );
						//此处应记录完成信息					
					}
					else
					{
						echo( "<b>Can't Copy [" . $filename . "](Since " . $sinceFrom . ") to [" . $toFileName . "](Since " . $sinceTo . ")!</b>\n" );
					}
				}
			}
			elseif( is_dir( $filename ) )
			{
				_makeFolders( $entry, $toFolder );
				folderFilesCopy( $filename."/", $toFolder.$entry."/", $overwrite );
			}
		}
		$d->close();		
	}
} // end func

/**
 * 获得文件的构建顺序
 *
 */
function fileSince( $filename )
{
	$rev = 0;
	$fp = @fopen( $filename, "r" );
	if( $fp )
	{
		$content = fread( $fp, 1024 );
		fclose( $fp );
		//假定 @since 信息只存在于前 1024字节
		if( preg_match( "/@since ([+-]?[0-9]+)/", $content, $match ) )
		{
			$rev = $match[1];
		}
	}
	return $rev;
}

function _makeFolders( $pathname, $ouputFolder )
{
	//echo $pathname . "|" . $ouputFolder;
	if( !file_exists( $ouputFolder.$pathname ) )
	{
		$arrFolder = split( "[\\/\\\\]", $pathname );
		//var_dump( $arrFolder );
		$basePath = $ouputFolder;
		foreach ( $arrFolder as $folder )
		{
			$basePath .= "/".$folder;
			if( !file_exists( $basePath ) )
			{
				mkdir( $basePath );
				chmod( $basePath, 0777 );
			}
		}
	}
}