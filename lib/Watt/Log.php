<?
/**
 * Enter description here...
 *
 * @author Terry
 * @package Watt
 */

/**
 * 日志管理类
 * 
 * 在任何地方，只要使用
 * Watt_Log::addLog( $msg ) 即可增加一条日志。
 * 而不用管是文件日志还是数据库日志
 * 
 */

class Watt_Log{
	
	const LEVEL_DEBUG  = 1; //调试信息
	const LEVEL_SYSTEM = 2; //系统信息
	const LEVEL_ERROR  = 4;	//系统错误
	const LEVEL_INFO   = 8;	//业务级记录的信息
	const LEVEL_LIUCHENG_SHENPI = 10;//审批操作信息
	const LEVEL_EXCEPION = 16; // 业务级用户异常操作（即非正常流程操作）
	
	/**
	 * 默认的loger
	 *
	 * @var Watt_Log
	 */
	private static $_defaultLoger;	//debuger loger 记录 调试日志和系统日志
	
	private static $_systemLoger;	//系统Loger 记录系统运行日志 2 4 8
	
	private static $_disabledAddLog = false;	//是否禁用默认日志，主要是为了生成统计数据时不产生日志
	public static function disable(){
		self::$_disabledAddLog = true;
	}
	public static function enable(){
		self::$_disabledAddLog = false;
	}
	
	protected $logerSn; //loger唯一序号
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
	                             , $exts=null, $extsInt=null )
	{
		//如果禁用日志则不记录 by terry at Wed Aug 18 10:02:05 CST 2010
		if(self::$_disabledAddLog){
			return true;
		}
		
		if( is_array( $msg ) ){
			$msg = var_export( $msg, true );
		}
		
		//默认所有日志就记录入 debuger 日志
		self::getDefaultLogger()->log( $msg, $level, $sourceName
	                                 , $actorName, $actorId
	                                 , $exts, $extsInt );

	    if( $level > self::LEVEL_DEBUG ){
	    	//self::getDefaultLogger()->log( 'Before DB Log' );

		    self::getSystemLogger()->log( $msg, $level, $sourceName
		                                 , $actorName, $actorId
		                                 , $exts, $extsInt );	

		    //self::getDefaultLogger()->log( 'After DB Log' );
	    }
	    /**
	     * 如果是Debug模式，同时输出到屏幕 / 判断debug模式在 addInfoToDefault 中实现
	     * @author terry
	     * @version 0.1.0
	     * Mon Sep 24 21:06:25 CST 2007
	     */
		Watt_Debug::addInfoToDefault( 'Watt_Log::addLog', $msg );
	    return true;
	}
	
	/**
	 * 返回默认的 日志记录器
	 *
	 * @return Watt_Log
	 */
	public static final function getDefaultLogger()
	{
		if( !is_object( self::$_defaultLoger ) )
		{
			self::$_defaultLoger = new Watt_Log_File();
		}
		return self::$_defaultLoger;
	}
	
	/**
	 * 返回项目日志对象
	 *
	 * @return Watt_Log
	 */
	public static final function getXiangmuLogger()
	{
		return Watt_Log_Db::getXmLogger();
	}
	/**
	 * 返回资源日志对象
	 *
	 * @return Watt_Log
	 */
	public static final function getZiyuanLogger()
	{
		return Watt_Log_Db::getZyLogger();
	}
	/**
	 * 返回岗位日志对象
	 *
	 * @return Watt_Log
	 */
	public static final function getGangWeiLogger()
	{
		return Watt_Log_Db::getGwLogger();
	}
	/**
	 * 获得系统日志程序
	 *
	 * @return Watt_Log
	 */
	public static final function getSystemLogger()
	{
		if( !is_object( self::$_systemLoger ) )
		{
			self::$_systemLoger = new Watt_Log_Db();
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
	                                 , $exts, $extsInt ){
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
	const DB_OPRATE_TYPE_SELECT = 'S';
	const DB_OPRATE_TYPE_UNKNOWN = 'UNKNOWN';
	const DB_OPRATE_MSG_SPLITER = ' |*| ';
	
	/**
	 * 记录操作数据库日志
	 *
	 * @param string $sql
	 */
	public static function logDbOprate( $sql ){
		//如果禁用日志则不记录 by terry at Wed Aug 18 10:02:05 CST 2010
		if(self::$_disabledAddLog){
			return true;
		}

		$sql = trim( $sql );
		if( !$sql )return false;
		
		//生成消息序列 by jute 
		$enableSync = Watt_Config::getCfgInFile( 'EnableSync', 'sync/sync.conf.php');
		if( $enableSync == '1' ){
			Watt_Sync_MessageListManage::createDbMsgList($sql);			
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
			/**
			 * debug模式同时记录select便于调试
			 * @author terry
			 * @version 0.1.0
			 * Tue Oct 21 09:33:14 CST 2008
			 */
			 /*
			if( defined('DEBUG') && DEBUG ){
				$logName   = self::DB_OPRATE_TYPE_SELECT;
				$msg       = $sql;
			}else{
			*/
				//不记录Select
				return false;
				//$sql = str_replace( array("\n","\r") , array(" ", "" ) , $sql );	
			/*
			}
			*/
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
		 && ( stripos( $sql, 'UPDATE tpm_yonghu SET YH_ZAIXIANSHIJIAN' ) === false )
		  ){
		  	//self::addLog( "stripos".stripos( $sql, 'UPDATE tpm_yonghu SET YH_ZAIXIANSHIJIAN' ) );
		  	/**
		  	 * 更新用户状态的不记录在数据库，因为频率太高，太常规了
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
				self::addLog( "Execute [ $sql ]", Watt_Log::LEVEL_DEBUG, $logName );				
			}
			return false;
		}
	}
	 /**
	 * 更新给定日当天的访问数量
	 * @author bobbing
	 * @version 0.1.0
	 *  2008-5-16
	 * $riqi  格式应如下20080516 
	 * date('Ymd',time()-86400)
	 * 取前一天的ymd格式
	 */
	public static function jilucishu($riqi)
	{

		$file_name="../log/".'processTime'.$riqi.".log";
		$fp=fopen($file_name,'r');
		$fwriqi = strtotime($riqi);
		$fwtongji = array();
		while(!feof($fp))
		{
			$buffer=fgets($fp,4096);
			//echo $buffer."<br>";
			$linshi = explode('|',$buffer);
			$userid = trim(str_replace('uid:','',$linshi[7]));
			if($userid == ''){
			}else{
				if(array_key_exists($userid,$fwtongji)){
					$fwtongji[$userid] = $fwtongji[$userid] + 1;
				}else{
					$fwtongji[$userid] = 1;
				}
			}
		}

		foreach ($fwtongji as $key => $val){
			$sql = "select count(id) from tpm_fwcishu where yh_id = '".$key."' and fw_riqi = ".$fwriqi;
			if(Watt_Db::getDb()->getOne($sql) <= 0){
				$sql = "insert into tpm_fwcishu (yh_id,fw_cishu,fw_riqi,yh_zhanghu) values ('".$key."','".$val."',".$fwriqi.",'".TpmYonghuPeer::getYhZhanghuByYhId($key)."')";
				Watt_Db::getDb()->execute($sql);
			}else{
				$sql = "update tpm_fwcishu set fw_cishu = '".$val."' where yh_id = '".$key."' and fw_riqi = ".$fwriqi;
				Watt_Db::getDb()->execute($sql);
			}
		}
		fclose($fp);
	}
}