<?
class Pft_Files{

	public static function getFileListInDir( $dir ){
		$fileList = array();

		$d = dir($dir);

		if( is_object( $d ) ){
			//echo "Handle: " . $d->handle . "\n";
			//echo "Path: " . $d->path . "\n";
			while (false !== ($entry = $d->read())) {
				if( $entry != '..' && $entry != '.' && is_file( $dir.$entry ) ){
					$fileList[$dir.$entry] = $entry;
				}
				//echo $entry."\n";
			}
			$d->close();
		}

		return $fileList;
	}

	public static function getFileListInDirEx( $dir ){
		$fileList = array();

		$d = dir($dir);

		if( is_object( $d ) ){
			//echo "Handle: " . $d->handle . "\n";
			//echo "Path: " . $d->path . "\n";
			while (false !== ($entry = $d->read())) {
				if( $entry != '..' && $entry != '.' && is_file( $dir.$entry ) ){
					$pathFileName = $dir.$entry;
					$arrTmp['fullpath'] = $pathFileName;
					$arrTmp['name'] = $entry;
					$arrTmp['size'] = formatFileSize( filesize( $pathFileName ) );
					$arrTmp['cdate'] = date( 'Y-m-d H:i:s', filemtime( $pathFileName ) );

					//$fileList[$dir.$entry] = $entry;
					$fileList[$pathFileName] = $arrTmp;
				}
				//echo $entry."\n";
			}
			$d->close();
		}

		return $fileList;
	}

	public static function formatFileSize( $size ){
		$arrSizes = array(
		'MB' => 1048576,
		'KB' => 1024,
		'B'  => 1,
		);
		foreach ( $arrSizes as $name => $sizeX ){
			if( $size >= $sizeX ){
				return intval($size/$sizeX).' '.$name;
			}
		}

		return $size;
	}

	public static function xcopy( $srcPath, $targetPath ){
		$filelist = getFileListInDir( $srcPath );
		foreach ( $filelist as $filename ){
			copy( $srcPath.$filename, $targetPath.$filename );
		}
		return true;
	}

	public static function xunlink( $path ){
		$filelist = getFileListInDir( $path );
		foreach ( $filelist as $filename ){
			unlink( $path.$filename );
		}
		return true;
	}
}
?>