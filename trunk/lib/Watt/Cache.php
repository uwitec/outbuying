<?php
/*
* 缓存类 cache
* 实      例：
include( "cache.php" );//包含次类的文件
$cache = new cache(30);//设置缓存时间
$cache->cacheCheck();  //检查缓存是否存在
echo date("Y-m-d H:i:s");
**这里是中间程序代码**
$cache->caching();     //创建缓存
*/
class Watt_Cache {
	//缓存目录
	//private $cacheRoot="./cache/cachefiles/";
	//缓存更新时间秒数，0为不缓存
	private $cacheLimitTime=0;
	//缓存文件名
	private $cacheFileName= "";
	//缓存扩展名
	private $cacheFileExt="php";

	/*
	* 构造函数
	* int $cacheLimitTime 缓存更新时间
	*/
	function __construct($cacheLimitTime) {
		$this->cacheRoot = Watt_Config::getRootPath().'cache/cachefiles/';
		if(intval($cacheLimitTime))
		$this->cacheLimitTime=$cacheLimitTime;
		$this->cacheFileName=$this->getCacheFileName();

		ob_start();
	}
	/**
	 * 检查缓存文件是否在设置更新时间之内
	 * 返回：如果在更新时间之内则返回文件内容，反之则返回失败
	 * @return boolean
	 */
	function cacheCheck(){
		/**
		 * 如果是 refresh，则不缓存
		 * @author terry
		 * @version 0.1.0
		 * Wed Dec 17 21:12:43 CST 2008
		 */
//		if( r('refresh') ){
//			return false;
//		}
		if(file_exists($this->cacheFileName)) {
			$cTime=$this->getFileCreateTime($this->cacheFileName );
			if($cTime+$this->cacheLimitTime>time()) {
				//chmod( $this->cacheFileName, 0777 );
				echo file_get_contents( $this->cacheFileName );
				ob_end_flush();
				//exit;
				/**
				 * 改为返回 true 的形式
				 * @author terry
				 * @version 0.1.0
				 * Mon Jan 14 14:30:06 CST 2008
				 */
				return true;
			}
		}
		return false;
	}
	/*
	* 缓存文件或者输出静态
	* string $staticFileName 静态文件名（含相对路径）
	*/
	function caching($staticFileName=""){
		if($this->cacheFileName){
			$cacheContent = ob_get_contents();
			//echo $cacheContent;
			ob_end_flush();

			if($staticFileName){
				$this->saveFile($staticFileName,$cacheContent );
			}
			if($this->cacheLimitTime)
				$this->saveFile($this->cacheFileName,$cacheContent );
		}
	}
	/*
	* 清除缓存文件
	* string $fileName 指定文件名(含函数)或者all（全部）
	* 返回：清除成功返回true，反之返回false
	*/
	function clearCache($fileName="all") {
		if($fileName!="all"){
			$fileName=$this->cacheFileName;
			if( file_exists($fileName)){
				return @unlink($fileName);
			}else return false;
		}
		if(is_dir($this->cacheRoot)){
			if($dir=opendir($this->cacheRoot)){
				while($file=@readdir($dir)){
					$check=is_dir($file);
					if(!$check)
					@unlink($this->cacheRoot.$file);
				}
				closedir($dir);
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	/*
	* 根据当前动态文件生成缓存文件名
	*/
	function getCacheFileName(){
		//return	$this->cacheRoot.strtoupper(md5($_SERVER["REQUEST_URI"].Watt_Session::getSession()->getUserId())).".".$this->cacheFileExt;
		return	$this->cacheRoot.strtoupper(md5($_SERVER["REQUEST_URI"])).".".$this->cacheFileExt;
	}
	/*
	* 缓存文件建立时间
	* string $fileName     缓存文件名（含相对路径）
	* 返回：文件生成时间秒数，文件不存在返回0
	*/
	function getFileCreateTime( $fileName ) {
		if( ! trim($fileName) ) return 0;
		if( file_exists( $fileName ) ) {
			return intval(filemtime( $fileName ));
		}else return 0;
	}
	/*
	* 保存文件
	* string $fileName      文件名（含相对路径）
	* string $text          文件内容
	* 返回：成功返回ture，失败返回false
	*/
	function saveFile($fileName,$text) {
		if(!$fileName||!$text) return false;

		if( !file_exists( dirname($fileName) ) ){
			if(@mkdir(dirname($fileName), 0777, true)) {
				@chmod( dirname($fileName), 0777 );
			}else{
				return false;
			}
		}
		//chmod( dirname($fileName), 0777 );
		if($fp=fopen($fileName,"w")){
			if(fwrite($fp,$text)) {
				@fclose($fp);
				@chmod( $fileName, 0777 );
				return true;
			}else {
				@fclose($fp);
				return false;
			}
		}

		return false;
	}
}
?>