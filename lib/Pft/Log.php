<?
/**
 * Enter description here...
 *
 * @author Terry
 * @package Pft
 */

/**
 * 日志管理类
 * 
 * 在任何地方，只要使用
 * Pft_Log::addLog( $msg ) 即可增加一条日志。
 * 而不用管是文件日志还是数据库日志
 * 
 */

class Pft_Log{
	
	const LEVEL_DEBUG  = 1; //调试信息
	const LEVEL_SYSTEM = 2; //系统信息
	const LEVEL_ERROR  = 4;	//系统错误
	const LEVEL_INFO   = 8;	//业务级记录的信息
	const LEVEL_EXCEPION = 16; // 业务级用户异常操作（即非正常流程操作）
	
	/**
	 * 默认的loger
	 *
	 * @var Pft_Log
	 */
	private static $_defaultLoger;	//debuger loger 记录 调试日志和系统日志
	
	private static $_systemLoger;	//系统Loger 记录系统运行日志 2 4 8
	/**
	 * 记录：
	 * 使用默认的 loger 记录日志
	 * 
	 * 时间 - 服务器自动记录时间
	 *
	 * 起因 - msg
	 * 经过
	 * 结果
	 * 
	 * 地点 - sourceName
	 * 人物 - userId
	 *      - actorName (userName)
	 *
	 * @param string $msg 记录的信息
	 * @param int $level
	 * @param string $sourceName
	 * @param string $actorName
	 * @param string $actorId
	 * @param mix $exts
	 * @return boolean
	 */
	public static final function addLog( $msg, $level=0, $sourceName=""
	                             , $actorName="", $actorId=""
	                             , $exts=null )
	{
		if( is_array( $msg ) ){
			$msg = var_export( $msg, true );
		}
		
		//默认所有日志就记录入 debuger 日志
		self::getDefaultLogger()->log( $msg, $level, $sourceName
	                                 , $actorName, $actorId
	                                 , $exts );

	    if( $level > self::LEVEL_DEBUG ){
	    	//self::getDefaultLogger()->log( 'Before DB Log' );

		    self::getSystemLogger()->log( $msg, $level, $sourceName
		                                 , $actorName, $actorId
		                                 , $exts );	

		    //self::getDefaultLogger()->log( 'After DB Log' );
	    }
	    /**
	     * 如果是Debug模式，同时输出到屏幕 / 判断debug模式在 addInfoToDefault 中实现
	     * @author terry
	     * @version 0.1.0
	     * Mon Sep 24 21:06:25 CST 2007
	     */
		Pft_Debug::addInfoToDefault( 'Pft_Log::addLog', $msg );
	    return true;
	}
	
	/**
	 * 返回默认的 日志记录器
	 *
	 * @return Pft_Log
	 */
	public static final function getDefaultLogger()
	{
		if( !is_object( self::$_defaultLoger ) )
		{
			self::$_defaultLoger = new Pft_Log_File();
		}
		return self::$_defaultLoger;
	}
	
	/**
	 * 返回项目日志对象
	 *
	 * @return Pft_Log
	 */
	public static final function getXiangmuLogger()
	{
		return Pft_Log_Db::getXmLogger();
	}
	/**
	 * 返回资源日志对象
	 *
	 * @return Pft_Log
	 */
	public static final function getZiyuanLogger()
	{
		return Pft_Log_Db::getZyLogger();
	}
	/**
	 * 返回岗位日志对象
	 *
	 * @return Pft_Log
	 */
	public static final function getGangWeiLogger()
	{
		return Pft_Log_Db::getGwLogger();
	}
	/**
	 * 获得系统日志程序
	 *
	 * @return Pft_Log
	 */
	public static final function getSystemLogger()
	{
		if( !is_object( self::$_systemLoger ) )
		{
			self::$_systemLoger = new Pft_Log_Db();
		}
		return self::$_systemLoger;
	}
	
	/**
	 * 记录日志， 子类必须覆盖
	 *
	 * @param string $msg 记录的信息
	 * @param int $level
	 * @param string $sourceName
	 * @param string $actorName
	 * @param string $actorId
	 * @param mix $exts
	 * @return boolean
	 */
	public function log( $msg, $level, $sourceName
	                                 , $actorName, $actorId
	                                 , $exts ){
		return true;
	}
	
	/**
	 * 获得日志 Level 列表
	 *
	 * @return array
	 */
	public static function getLevelList(){
		$arrLevel = array(
			self::LEVEL_DEBUG  => 'DEBUG',
			self::LEVEL_SYSTEM => 'SYSTEM',
			self::LEVEL_ERROR  => 'ERROR',
			self::LEVEL_INFO   => 'INFO'
		);
		return $arrLevel;
	}
	
	
	const DB_OPRATE_TYPE_PREFIX = 'DB';
	const DB_OPRATE_TYPE_INSERT = 'C';
	const DB_OPRATE_TYPE_UPDATE = 'U';
	const DB_OPRATE_TYPE_DELETE = 'D';
	const DB_OPRATE_TYPE_UNKNOWN = 'UNKNOWN';
	const DB_OPRATE_MSG_SPLITER = ' |*| ';
	
	/**
	 * 记录操作数据库日志
	 *
	 * @param string $sql
	 */
	public static function logDbOprate( $sql ){
		$sql = trim( $sql );
		if( !$sql )return false;
		
		//生成消息序列 by jute 
		$enableSync = Pft_Config::getCfgInFile( 'EnableSync', 'sync/sync.conf.php');
		if( $enableSync == '1' ){
			Pft_Sync_MessageListManage::createDbMsgList($sql);			
		}
		
		$tableName = '';
		$logName   = '';
		$msg       = '';
		
		//忽略列表优先于允许列表
		$ignoreTalbenameList = array(
			'tpm_rizhi' => 'rz_id',
			'tpm_xiangmu_rizhi' => 'rz_id',
			'tpm_ziyuan_rizhi' => 'rz_id',
			'tpm_rizhi_fangwen' => 'rz_id',
			);
		
		$toLogTablenameList = array(
			'tpm_dingdan' => 'dd_id',
			'tpm_shengchandingdan' => 'sd_id',
			'tpm_xiangmu' => 'xm_id',
			'tpm_renwu'   => 'rw_id',
			'tpm_gaojian'   => 'gj_id',
			'tpm_yonghukuozhan'   => 'yh_id',
			'tpm_yonghu'   => 'yh_id',
			'tpm_kehu_yonghu'   => 'yh_id',
			);
			
		//因为之前已经trim了，所以 === 0
		if( stripos( $sql, 'SELECT' ) === 0 ){
			//不记录Select
			return false;
			//$sql = str_replace( array("\n","\r") , array(" ", "" ) , $sql );
		}elseif( stripos( $sql, 'UPDATE' ) === 0 ){
			/*
			/update[\s]+(\w+)[\s]+set(.*)where[\s]+(.*)/
                          ^^table name ^^col         ^^cond
             */
			if( preg_match( "/update[\\s]+(\\w+)[\\s]+set(.*)where[\\s]+(.*)/i", $sql, $matchs ) ){
				$tableName = $matchs[1];
				$cols      = $matchs[2];
				$cond      = $matchs[3];
				$logName   = self::DB_OPRATE_TYPE_PREFIX.'-'.$tableName.'-'.self::DB_OPRATE_TYPE_UPDATE;
				$msg       = $cols.self::DB_OPRATE_MSG_SPLITER.$cond;
			}
		}elseif( stripos( $sql, 'INSERT' ) === 0 ){
			/*
			/insert into[\s]+(\w+)[\s]+\((.*)\)[\s]+values[\s]+\((.*)\)/
			*/
			if( preg_match( '/insert into[\s]+(\w+)[\s]+\((.*)\)[\s]+values[\s]+\((.*)\)/i', $sql, $matchs ) ){
				$tableName = $matchs[1];
				$cols      = $matchs[2];
				$values    = $matchs[3];
				$logName   = self::DB_OPRATE_TYPE_PREFIX.'-'.$tableName.'-'.self::DB_OPRATE_TYPE_INSERT;
				$msg       = $cols.self::DB_OPRATE_MSG_SPLITER.$values;
			}
		}elseif( stripos( $sql, 'DELETE' ) === 0 ){
			/*
			/insert into[\s]+(\w+)[\s]+\((.*)\)[\s]+values[\s]+\((.*)\)/
			*/
			if( preg_match( '/DELETE FROM[\s]+(\w+)[\s]+WHERE(.*)/i', $sql, $matchs ) ){
				$tableName = $matchs[1];
				$cond      = $matchs[2];
				$logName   = self::DB_OPRATE_TYPE_PREFIX.'-'.$tableName.'-'.self::DB_OPRATE_TYPE_DELETE;
				$msg       = $cond;
			}
		}else{
			/**
			 * 对未解析的SQL 也记录在案
			 * @author terry
			 * @version 0.1.0
			 * Wed Sep 26 13:38:08 CST 2007
			 */
			$logName   = self::DB_OPRATE_TYPE_UNKNOWN;
			$msg       = $sql;
		}
		//strtolower( $tableName ) != 'tpm_rizhi'
		if( $msg && $logName 
		 && !key_exists( strtolower( $tableName ), $ignoreTalbenameList )
		 &&  key_exists( strtolower( $tableName ), $toLogTablenameList )
		 && !( stripos( $sql, 'UPDATE tpm_yonghu SET YH_ZAIXIAN_ZHUANGTAI' ) === 0 )
		  ){
		  	/**
		  	 * 更新用户状态的不记录在数据库
		  	 * @author terry
		  	 * @version 0.1.0
		  	 * Sat Sep 29 19:41:27 CST 2007
		  	 */
		  	
//			print"<pre>Terry :";var_dump( $logName );print"</pre>";
//			print"<pre>Terry :";print ( $msg );print"</pre>";
//			exit();
			self::addLog( "Execute [ $sql ]".self::DB_OPRATE_MSG_SPLITER.$msg, self::LEVEL_SYSTEM, $logName );	
			return true;
		}else{
			/**
			 * 忽略列表中的数据表操作亦将不被写入文件Log
			 * @author terry
			 * @version 0.1.0
			 * Wed Sep 05 09:46:56 CST 2007
			 */			
			if( !key_exists( strtolower( $tableName ), $ignoreTalbenameList ) ){
				self::addLog( "Execute [ $sql ]", Pft_Log::LEVEL_DEBUG, $logName );				
			}
			return false;
		}
	}
	
}