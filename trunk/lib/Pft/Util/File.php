<?
/**
 * 文件管理类
 *
 */
class Pft_Util_File{
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
		$dir_name=dirname($file);
		mkdir($dir_name,0777,true);
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
						Pft_Util_File::delfilebytime($dir.$file.'/',$time);
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
			//Pft_Util_File::create_dir($todir.'asd');
			/**
			 * 之前仿佛是错误吧..
			 * @author terry
			 * @version 0.1.0
			 * Sat Sep 15 12:51:42 CST 2007
			 */
			Pft_Util_File::create_dir($todir);
		}
		
		if (is_dir($dir))
		{//目录
			$handle = opendir($dir); 
			while ($file = readdir($handle)) {//循环目录
				if($file=='.' || $file=='..' || $file=='.svn'){//不操作的文件或目录
					
				}else {		
					if(is_dir($dir.$file)){	
						Pft_Util_File::delfilebytime($dir.$file.'/',$time);
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
	
}
?>