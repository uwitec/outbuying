<?

/**
 * 消息序列管理类
 * 
 *  @author jute
 */
class Watt_Sync_MessageListManage
{
	private static $_config;

	/**
	 * 调用配置文件的方法
	 *
	 * @return array
	 */
	private function _getConfig(){
		if( !self::$_config ){			
			self::$_config = Watt_Config::getCfgFromFile('sync/sync.conf.php');
		}
		return self::$_config;
	}

	/**
	 * 解析SQL语句生成消息序列
	 *
	 * @param string $sql
	 * @return true or false
	 */
	public static function createDbMsgList( $sql ){	
		$config = self::_getConfig();
		$sql = trim( $sql );
		if( !$sql )return false;
		$tableName = '';
		$logName   = '';
		$msg       = '';
		$msgList = array();
		
		//忽略列表优先于允许列表
		$ignoreTalbenameList = array(
			'tpm_rizhi' => 'rz_id',
			'tpm_sync_log' => 'id',
			'tpm_stat_yingye' => 'stat_id',
			);
		//
		$toLogTablenameList = array(
			'tpm_dingdan' => 'dd_id',
			'tpm_shengchandingdan' => 'sd_id',
			'tpm_xiangmu' => 'xm_id',
			'tpm_renwu'   => 'rw_id',
			'tpm_gaojian'   => 'gj_id',
			'tpm_yonghukuozhan'   => 'yh_id',
			);
			
		//因为之前已经trim了，所以 === 0
		if( stripos( $sql, 'SELECT' ) === 0 ){
			//不记录Select
			return false;
		}elseif( stripos( $sql, 'UPDATE' ) === 0 ){
			if (preg_match("/UPDATE tpm_yonghu SET YH_ZAIXIAN_ZHUANGTAI = 0,YH_ZAIXIANSHIJIAN =/i",$sql)){
					return false;
			}
			if( preg_match( "/update[\\s]+(\\w+)[\\s]+set(.*)where[\\s]+(.*)/i", $sql, $matchs ) ){
				$tableName = $matchs[1];
				$cols      = $matchs[2];
				$cond      = $matchs[3];
				//将字段值部分拆分分为数组
				//$cols=explode(',',$cols);
				 $cols = self::sqlStrSplit1( $cols);
				 
				 $newcols = array();
				 if (is_array($cols) && count($cols))
				 {
				 	foreach ($cols as $acol)
				 	{
				 		//$col_ = explode('=',$acol);								 		
						preg_match( "/\s*(\w+)\s*=\s*(.*)/i", $acol, $t );
						$col_=array($t[1],$t[2]);
						
				 		if (is_array($col_) && count($col_)){				 		
				 			$newcols[trim($col_[0])] = trim($col_[1]);	
				 		}
				 	}
				 }
				$msgList = array(
								'operate'=> 'UPDATE',
								'tableName' => $tableName,
								'cols' => $newcols,
								'cond' => $cond
								);
			}
		}elseif( stripos( $sql, 'INSERT' ) === 0 ){			
			
			if( preg_match( '/insert into[\s]+(\w+)[\s]+\((.*)\)[\s]+values[\s]+\((.*)\)/i', $sql, $matchs ) ){
				$tableName = $matchs[1];
				$cols      = $matchs[2];
				$values    = $matchs[3];
				$cols = explode(',',$cols);
				//$values = explode(',',$values);
				//$values = preg_split('/,(?!\w+\',?)/i',$values);
				
				$values = self::sqlStrSplit($values);
				//$values=eval('return array('.$values.');');					
				$newcols = array();
				if (is_array($cols) && count($cols) &&is_array($values) && count($values))
				{
				 	foreach ($cols as $key => $val)
				 	{
				 		/**
				 		 * add a '@' at $values
				 		 * @author terry
				 		 * @version 0.1.0
				 		 * Tue Jul 03 14:32:52 CST 2007
				 		 */
				 		//$newcols[$val] = "'".@$values[$key]."'";
				 		$newcols[$val] = @$values[$key];
				 	}
				}
				 				
				$msgList = array(
								'operate'=> 'INSERT',
								'tableName' => $tableName,
								'cols' => $newcols
								);
			}
		}elseif( stripos( $sql, 'DELETE' ) === 0 ){
			if( preg_match( '/DELETE FROM[\s]+(\w+)[\s]+WHERE(.*)/i', $sql, $matchs ) ){
				$tableName = $matchs[1];
				$cond      = $matchs[2];
				$msgList = array(
								'operate'=> 'DELETE',
								'tableName' => $tableName,
								'cond' =>$cond
								);
			}
		}
		if( $msgList && count($msgList) 
		 && !key_exists( strtolower( $tableName ), $ignoreTalbenameList )
		 //&&  key_exists( strtolower( $tableName ), $toLogTablenameList )
		  ){		  	
			//服务器的类型设置
			$msgList['syncServerType'] = $config['SyncServerType'];
			return self::createMsgList($msgList);
		}else{
			return false;
		}
	}
		
	/**
	 * 创建SELECT语句的消息序列
	 *
	 * @param unknown_type $msgtype : 'msgList','msgSql'
	 * @return unknown
	 */
	/*public static function createMsgList1( $msgType='',$msgList='',$msgSign='' )
	{
		if( $msgType=='' || $msgList=='' )return false;		
		if( $msgSign=='' )$msgSign=self::getMsgSign();
		
		//读取配置
		$config = self::_getConfig();
		if ($msgType=='msgList')
		{
			$TpmMsgListDir = $config['TpmMsgListDir'];
			$TpmMsgListExtName = $config['TpmMsgListExtName'];
			$msgList = serialize($msgList);
		}
		else if ($msgType=='msgSql')
		{				
			$TpmMsgSqlDir = $config['TpmMsgSqlDir'];
			$TpmMsgSqlExtName = $config['TpmMsgSqlExtName'];			
		}
		else
		{
			return false;
		}
		//文件名设置
		$filename = $TpmMsgSqlDir.$msgSign.".".$TpmMsgSqlExtName;
		echo $filename."<br>";
		//return false;
		if (!is_file($filename))
		{//文件不存在
			//目录不存在则创建
			if (!is_dir(dirname($filename)))BaseOption::create_dir($filename);
			
			//写消息序列文件
			ob_start();
			print_r($msgList);
			$fp = fopen($filename, 'wb');
			fwrite($fp, ob_get_contents());
			fclose($fp);
			ob_end_clean();			
			if (is_file($filename))
			{//写文件成功
				return true;
			}
		}
		return false;	
	}
	*/
	public static function createMsgSql($msgSql='',$msgSign='' )
	{
		if($msgSql=='' )return false;
		if( $msgSign=='' )$msgSign=self::getMsgSign();
		
		//读取配置
		$config = self::_getConfig();			
		$TpmMsgSqlDir = $config['TpmMsgListDir']."msgsql/";
		$TpmMsgSqlExtName = "msgsql";	
		//文件名设置
		$filename = $TpmMsgSqlDir.$msgSign.".".$TpmMsgSqlExtName;
		if (!is_file($filename))
		{//文件不存在
			//目录不存在则创建
			if (!is_dir(dirname($filename)))BaseOption::create_dir($filename);
			
			//写消息序列文件
			//ob_start();
			//print_r($msgSql);
			$fp = fopen($filename, 'ab');
			//fwrite($fp, ob_get_contents());
			fwrite( $fp, $msgSql."\r\n" );
			fclose($fp);
			//ob_end_clean();			
			if (is_file($filename))
			{//写文件成功
				return $msgSign;
			}else{
				return false;
			}
		}
		return $msgSign;	
	}
	
	public static function createAllMsgList($msgList,$dirtype=''){
		
		$config = self::_getConfig();
		if( !$msgList )return false;
		//读取配置		
		$TpmMsgListDir = $config['TpmMsgListDir'];
		if ($dirtype=='unknown'){
			$TpmMsgListDir = $config['TpmMsgListDir']."unknown/";
		}else if ($dirtype=='outside'){
			$TpmMsgListDir = $config['TpmMsgListDir']."outside/";
		}
		$TpmMsgListExtName = $config['TpmMsgListExtName'];
		$rev = true;
		if(is_array($msgList) && count($msgList)){
			foreach ($msgList as $key =>$val){
				//文件名设置
				$filename = $TpmMsgListDir.$key.".".$TpmMsgListExtName;
				
				//目录不存在则创建
				if (!is_dir(dirname($filename)))BaseOption::create_dir($filename);
				
				$fp = fopen($filename, 'ab');
				foreach ($val as $k => $v){					
					
					//写消息序列文件
					fwrite($fp, serialize($v)."\r\n");
				}
				fclose($fp);
				if (is_file($filename))
				{//写文件成功
				}else{
					$rev = false;
				};	
			}			
		}else{
			return false;
		}
		return $rev;
	}
	/**
	 * 根据序列内容生成消息序列
	 *
	 * @param $msgList array 消息序列内容
	 * 		  $msgSign string 消息序列标识
	 * 		  $dirtype string normal:正常 unknown:未知 outside:外部 
	 */
	public static function createMsgList( $msgList,$msgSign='',$dirtype='normal' )
	{	
		$config = self::_getConfig();
		if( !$msgList )return false;
		if($msgSign == '')$msgSign=self::getMsgSign();
		//读取配置
		
		if ($dirtype=='unknown'){
			$TpmMsgListDir = $config['TpmMsgListDir']."unknown/";
		}else if ($dirtype=='outside'){
			$TpmMsgListDir = $config['TpmMsgListDir']."outside/";
		}else if ($dirtype=='normal'){
			$TpmMsgListDir = $config['TpmMsgListDir'];
		}
		
		$TpmMsgListExtName = $config['TpmMsgListExtName'];
		
		//文件名设置
		$filename = $TpmMsgListDir.$msgSign.".".$TpmMsgListExtName;
		//if (!is_file($filename))
		//{//文件不存在
			
			$theMsgListObj = self::_getMsgListObj( $filename );
			if(isset($msgList[0]) && is_array($msgList[0]) && count($msgList[0])){

				foreach ($msgList as $key =>$val){
					//fwrite($fp, serialize($val)."\r\n");
					$rev = $theMsgListObj->write( serialize($val)."\r\n");
				}				

			}else{
				//fwrite($fp, serialize($msgList)."\r\n");
				$rev = $theMsgListObj->write( serialize($msgList)."\r\n");
			}
			//写消息序列文件
			return $rev;
		//}
		return false;		
	}
	
	private static $_msgListObjList=array();
	/**
	 * @return Watt_Sync_MessageListFile
	 */
	private static function _getMsgListObj( $pathFileName ){
		if( !key_exists( $pathFileName , self::$_msgListObjList ) ){
			$aNewMsgListObj = new Watt_Sync_MessageListFile( $pathFileName );
			self::$_msgListObjList[$pathFileName] = $aNewMsgListObj;
		}
		return self::$_msgListObjList[$pathFileName];
	}
	
	/**
	 * 创建消息SQL结果集
	 *
	 * @param unknown_type $msgSql
	 * @param unknown_type $msgSign
	 * @return unknown
	 */
	public static function createMsgSqlValue($msgSql='',$msgSign='' )
	{
		if($msgSql=='' )return false;
		if( $msgSign=='' )$msgSign=self::getMsgSign();
		
		//读取配置
		$config = self::_getConfig();			
		$TpmMsgSqlDir = $config['TpmMsgListDir']."msgsqlValue/";
		$TpmMsgSqlExtName = "msgsqlv";
		//文件名设置
		$filename = $TpmMsgSqlDir.$msgSign.".".$TpmMsgSqlExtName;
		if (is_file($filename)){//如果存在先删除
			unlink($filename);
		}
		if (!is_file($filename))
		{//文件不存在
			//目录不存在则创建
			if (!is_dir(dirname($filename)))BaseOption::create_dir($filename);
			
			//写消息序列文件
			ob_start();
			print_r(serialize($msgSql));
			$fp = fopen($filename, 'wb');
			fwrite($fp, ob_get_contents());
			fclose($fp);
			ob_end_clean();			
			if (is_file($filename))
			{//写文件成功
				return true;
			}
		}
		return false;	
	}
	
	/**
	 * 获取消息SQL值
	 *
	 * @param string $Sign
	 */
	public static function getMsgSqlValueBySign($sign='' ){
		if($sign=='')return false;
		//读取配置
		$config = self::_getConfig();			
		$TpmMsgSqlDir = $config['TpmMsgListDir']."msgsqlValue/";
		$TpmMsgSqlExtName = "msgsqlv";
		//文件名设置
		$filename = $TpmMsgSqlDir.$sign.".".$TpmMsgSqlExtName;
		if (!is_file($filename))
		{//文件不存在	
			return false;
		}
		return file_get_contents($filename);
	}
	
	/**
	 * 删除消息sql值
	 *
	 * @param unknown_type $sign
	 */
	public static function delMsgSqlValueBySign($sign='' ){
		if($sign=='')return false;
		//读取配置
		$config = self::_getConfig();			
		$TpmMsgSqlDir = $config['TpmMsgListDir']."msgsqlValue/";
		$TpmMsgSqlExtName = "msgsqlv";
		//文件名设置
		$filename = $TpmMsgSqlDir.$sign.".".$TpmMsgSqlExtName;
		if (is_file($filename))
		{
			//删除操作
			return unlink($filename);
		}
	}
	/**
	 * 获取所有消息序列
	 *
	 * @return array 所有消息序列的内容
	 * 		$msgList =array(
	 *						'117746869081940300'=>array('operate' => 'UPDATE',
	 *											'tableName' => 'tpm_yonghu',
	 *											 'cols' => ' YH_YOUXIANG = &apos;zj@163.com&apos; ',
	 *											 'cond' => 'tpm_yonghu.YH_ID=&apos;92d835f6-73a8-3d1a-1b30-45c6cc2aeef9&apos;',
	 *											 'syncServerType' => 'INSIDE_TPM'
	 *											),
	 *						'117746871212680400'=>array('operate' => 'UPDATE',
	 *											'tableName' => 'tpm_yonghu',
	 *											 'cols' => ' YH_YOUXIANG = &apos;zj@163.com&apos; ',
	 *											 'cond' => 'tpm_yonghu.YH_ID=&apos;92d835f6-73a8-3d1a-1b30-45c6cc2aeef9&apos;',
	 *											 'syncServerType' => 'INSIDE_TPM'
	 *											),
	 * 						...
	 *						);
	 */
	public static function getAllMessageList($MsgListDir='',$count='')
	{
		$config = self::_getConfig();			
		if ($MsgListDir == ''){
			//获取消息序列文件的存放地址
			$TpmMsgListDir = $config['TpmMsgListDir'];
			//$TpmMsgListExtName = $config['TpmMsgListExtName'];			
		}else if($MsgListDir == 'outside'){
			$TpmMsgListDir = $config['TpmMsgListDir'].'outside/';
		}else if($MsgListDir == 'unknown'){
			$TpmMsgListDir = $config['TpmMsgListDir'].'unknown/';
		}
		
		$rev = array();				
		
		$msgList = self::getAllMsgSign($TpmMsgListDir,$count);	
		if (is_array($msgList) && count($msgList))
		{
			foreach($msgList as $key =>$val)
			{
				$rev[$val] = self::getMsgList($val,$TpmMsgListDir);
			}			
		}
		//获取所有的
		return $rev;
	}
	
	/**
	 * 获取所有SQL消息序列
	 * 		$msgList = array(
	 *						'117746869081940300'='SELECT tpm_yonghu.YH_YOUXIANG,tpm_yonghu.YH_ZAIXIAN_ZHUANGTAI FROM tpm_yonghu WHERE tpm_yonghu.YH_ID=&apos;82e70315-69c7-c0b7-500f-45c6cdf4196f&apos;',
	 *						'117746871212680400'='SELECT tpm_yonghu.YH_YOUXIANG,tpm_yonghu.YH_ZAIXIAN_ZHUANGTAI FROM tpm_yonghu WHERE tpm_yonghu.YH_ID=&apos;82e70315-69c7-c0b7-500f-45c6cdf4196f&apos;'
	 * 						)
	 *			
	 */
	public static function getAllMessageSql()
	{
		$rev = array();
				
		$msgSql = self::getAllMsgSqlSign();
		if (is_array($msgSql) && count($msgSql))
		{
			foreach($msgSql as $key =>$val)
			{
				$rev[$val] = self::getMsgSql($val);
			}			
		}
		//获取所有的
		return $rev;
	}
	
	/**
	 * 获取所有消息
	 * 		$msgList = array('msgList' => array('117746869081940300'=>array('operate' => 'UPDATE',
	 *																		'tableName' => 'tpm_yonghu',
	 *																		 'cols' => ' YH_YOUXIANG = &apos;zj@163.com&apos; ',
	 *																		 'cond' => 'tpm_yonghu.YH_ID=&apos;92d835f6-73a8-3d1a-1b30-45c6cc2aeef9&apos;',
	 *																		 'syncServerType' => 'INSIDE_TPM'
	 *																		),
	 * 											'117746871212680400'=>array('operate' => 'UPDATE',
	 *																		'tableName' => 'tpm_yonghu',
	 *																		'cols' => ' YH_YOUXIANG = &apos;zj@163.com&apos; ',
	 *																		'cond' => 'tpm_yonghu.YH_ID=&apos;92d835f6-73a8-3d1a-1b30-45c6cc2aeef9&apos;',
	 *											 							'syncServerType' => 'INSIDE_TPM'
	 *																		)
	 * 											),
	 * 						 'msgSql' => array(	'117746869081940300'='SELECT tpm_yonghu.YH_YOUXIANG,tpm_yonghu.YH_ZAIXIAN_ZHUANGTAI FROM tpm_yonghu WHERE tpm_yonghu.YH_ID=&apos;82e70315-69c7-c0b7-500f-45c6cdf4196f&apos;',
	 * 											'117746871212680400'='SELECT tpm_yonghu.YH_YOUXIANG,tpm_yonghu.YH_ZAIXIAN_ZHUANGTAI FROM tpm_yonghu WHERE tpm_yonghu.YH_ID=&apos;82e70315-69c7-c0b7-500f-45c6cdf4196f&apos;'
	 *											)
	 * 						)
	 *			
	 */
	public static function getAllMessage()
	{
		$msgList = self::getAllMessageList();
		$msgSql = self::getAllMessageSql();
		return array('msgList' =>$msgList,
					 'msgSql' =>$msgSql
					);
	}
	/**
	 * 执行消息序列
	 *
	 * @param array $msgList 消息序列
	 * @return true or false
	 */
	public static function executeMessageList($msgList)
	{		
		if (!$msgList || !is_array($msgList) || !count($msgList))return false;
		//组合SQL
		$sql ="";
		if ($msgList['operate']=='UPDATE')
		{
			$cols = array();
			foreach ($msgList['cols'] as $key => $val){
				$cols[] = $key.'='.$val;
			}
			$cols = implode(',',$cols);
			
			$sql = $msgList['operate']." ".$msgList['tableName']." SET ".$cols." WHERE ".$msgList['cond'];
		}
		else if ($msgList['operate']=='INSERT')
		{
			$cols =array();
			$values = array();
			foreach ($msgList['cols'] as $key => $val){
				$cols[] = $key;
				$values[] = $val;
			}
			$cols = implode(',',$cols);
			$values = implode(',',$values);
			$sql = $msgList['operate']." INTO ".$msgList['tableName']." (".$cols.") VALUES (".$values.")";
		}
		else if ($msgList['operate']=='DELETE')
		{
			$sql = $msgList['operate']." FROM ".$msgList['tableName']." WHERE ".$msgList['cond'];
		}
		//return $sql;
		
		//执行SQL
		if ($sql != ''){		
			//写日志
			$loger = new Watt_Log_File( 'sync_dbsql' );
			$loger->log( $sql );
			try {
				Watt_Db::getDb()->execute($sql,false);
				//return Watt_Sync_MessageListManage::getmsg1();
				return true;
			}catch (Exception $e){
				//出错处理		
				$loger->log( 'Failed because ['.$e->getMessage().']' );
				/**
				 * @todo 增加出错锁，或使用 exec 锁作为 出错锁
				 * by terry
				 */
				return false;
			}		
		}
		return false;
	}
	/**
	 * 执行消息SQL
	 *
	 * @param string $Sql
	 * @return 成功返回array,失败返回false
	 */
	public static function executeMessageSql($sql)
	{
		//执行SQL
		if ($sql != ''){
			try {
				return Watt_Db::getDb()->getAll($sql);
			}catch (Exception $e){
				//出错处理
				return false;
			}		
		}
		return false;
	}
	
	/**
	 *	删除消息序列
	 *  参数：string 消息序列标识
	 *  @return true or false
	 */
	public static function delMessageList($msgSign,$TpmMsgListDir='')
	{
		if (!$msgSign)return false;
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		if ($TpmMsgListDir==''){			
			$TpmMsgListDir = $config['TpmMsgListDir'];
		}else if ($TpmMsgListDir=='unknown') {
			$TpmMsgListDir = $config['TpmMsgListDir'].'unknown/';
		}
		$TpmMsgListExtName = $config['TpmMsgListExtName'];
		
		$rev = false;
		if(is_array($msgSign) && count($msgSign)){
			foreach ($msgSign as $key=>$val){
				//文件名设置
				$filename = $TpmMsgListDir.$val.".".$TpmMsgListExtName;
				if (is_file($filename))
				{
					//删除操作
					if(unlink($filename)){
						$rev = true;
					}
				}else {
					$rev = true;
				}
			}				
		} else {
			//文件名设置
			$filename = $TpmMsgListDir.$msgSign.".".$TpmMsgListExtName;
			if (is_file($filename))
			{
				//删除操作
				if(unlink($filename)){
					$rev = true;
				}
			}else {
				$rev =true;
			}
		}
		return $rev;
	}
	/**
	 *	删除消息序列
	 *  参数：string 消息序列标识
	 *  @return true or false
	 */
	public static function delMessageSql($msgSign)
	{
		if (!$msgSign)return false;
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		$TpmMsgListDir = $config['TpmMsgListDir']."msgsql/";
		$TpmMsgListExtName = "msgsql";
		
		$rev = '0';
		if(is_array($msgSign) && count($msgSign)){
			foreach ($msgSign as $key=>$val){
				//文件名设置
				$filename = $TpmMsgListDir.$val.".".$TpmMsgListExtName;
				if (is_file($filename))
				{
					//删除操作
					if(!unlink($filename)){
						return '0';
					}else {
						$rev ='1';
					}
				}else {
					$rev = '1';
				}
			}
		}else{
			//文件名设置
			$filename = $TpmMsgListDir.$msgSign.".".$TpmMsgListExtName;
			if (is_file($filename))
			{
				//删除操作
				if (!unlink($filename)){
					return '0';
				}else {
					$rev = '1';
				}
			}else {
				$rev = '1';
			}
		}
		return $rev;
	}
	/**
	 *	获取消息序列标识
	 * 	
	 * @return array：已排序的消息序列标识数组
	 * 
	 */
	public static function getAllMsgSign($TpmMsgListDir='',$count='')
	{
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		if ($TpmMsgListDir == ''){
			$TpmMsgListDir = $config['TpmMsgListDir'];
		}
		$TpmMsgListExtName = $config['TpmMsgListExtName'];
		//获取所有序列文件
		$msgSign = array();
		
		if (is_dir($TpmMsgListDir))
		{//目录
			$handle = opendir($TpmMsgListDir); 
			while ($file = readdir($handle)) {//循环目录
				if (is_file($TpmMsgListDir.$file))
				{//文件
					if (preg_match('/(.*)\.'.$TpmMsgListExtName.'$/',strtolower($file),$cf))
					{
						$msgSign[] = $cf[1];
					}
				}
			}
		}
		sort($msgSign);
		if($count != '')
		{			
			$msgSign = array_slice($msgSign, 0, $count);
		}
		//返回消息序列		
		return $msgSign;		
	}
	/**
	 *	获取SQL消息标识
	 * 	
	 * @return array：已排序的消息序列标识数组
	 * 
	 */
	public static function getAllMsgSqlSign()
	{
		$config = self::_getConfig();
		//获取消息序列文件的存放地址	
		$TpmMsgListDir = $config['TpmMsgListDir']."msgsql/";
		$TpmMsgListExtName = "msgsql";
		//获取所有序列文件
		$msgSign = array();
		
		if (is_dir($TpmMsgListDir))
		{//目录
			$handle = opendir($TpmMsgListDir); 
			while ($file = readdir($handle)) {//循环目录
				if (is_file($TpmMsgListDir.$file))
				{//文件
					if (preg_match('/(.*)\.'.$TpmMsgListExtName.'$/',strtolower($file),$cf))
					{
						$msgSign[] = $cf[1];
					}
				}
			}
		}
		sort($msgSign);
		//返回消息序列		
		return $msgSign;		
	}
	/**
	 *	获取SQL消息标识
	 * 	
	 * @return array：已排序的消息序列标识数组
	 * 
	 */
	public static function getAllUnknownMsgListSign()
	{
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		$TpmMsgListDir = $config['TpmMsgListDir']."unknown/";
		$TpmMsgListExtName = $config['TpmMsgListExtName'];		
		//获取所有序列文件
		$msgSign = array();
		
		if (is_dir($TpmMsgListDir))
		{//目录
			$handle = opendir($TpmMsgListDir); 
			while ($file = readdir($handle)) {//循环目录
				if (is_file($TpmMsgListDir.$file))
				{//文件
					if (preg_match('/(.*)\.'.$TpmMsgListExtName.'$/',strtolower($file),$cf))
					{
						$msgSign[] = $cf[1];
					}
				}
			}
		}
		sort($msgSign);
		//返回消息序列		
		return $msgSign;	
	}
	/**
	 *	根据消息序列标识获取消息序列内容
	 * 	
	 */
	/*
	public static function getMsgList($msgSign,$TpmMsgListDir='')
	{
		if( !$msgSign )return false;
		//文件名设置
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		if ($TpmMsgListDir==''){
			$TpmMsgListDir = $config['TpmMsgListDir'];
		}
		$TpmMsgListExtName = $config['TpmMsgListExtName'];
		
		$filename = $TpmMsgListDir.$msgSign.".".$TpmMsgListExtName;
		$msgList = file_get_contents($filename);
		
		if (!$msgList)return false;
		//反序列化
		$msgList = unserialize($msgList);
		return $msgList;	
	}
	*/
	public static function getMsgList($msgSign,$TpmMsgListDir='')
	{
		if( !$msgSign )return false;
		//文件名设置
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		if ($TpmMsgListDir==''){
			$TpmMsgListDir = $config['TpmMsgListDir'];
		}
		$TpmMsgListExtName = $config['TpmMsgListExtName'];
		
		$filename = $TpmMsgListDir.$msgSign.".".$TpmMsgListExtName;
		//$msgList = file_get_contents($filename);
		$rev =array();
		//$rev[$msgSign] =array();
		
		//分行读取文件，一行产生一个序列
		$handle = @fopen($filename, "r");
		if ($handle) {
		    while (!feof($handle)) {
		        $buffer = fgets($handle, 65536);
		        if($buffer!=''){
		        	//echo $buffer.'<br>';
		        	$rev_ = unserialize($buffer);
		        	//$rev[] = $rev_[0];
		        	$rev[] = $rev_;
		        }
		    }
		    fclose($handle);
		}
		/*
		print "<pre>";
		print_r($rev);
		print "</pre>";
		*/
		return $rev;		
	}
	/**
	 *	根据SQL消息标识获取SQL消息内容
	 * 	
	 */
	public static function getMsgSql($msgSign)
	{
		if( !$msgSign )return false;
		//文件名设置
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		$TpmMsgListDir = $config['TpmMsgListDir']."msgsql/";
		$TpmMsgListExtName = "msgsql";
		$filename = $TpmMsgListDir.$msgSign.".".$TpmMsgListExtName;
		$msgList = file_get_contents($filename);
		if (!$msgList)return false;		
		return $msgList;	
	}	
	/**
	 * 获取被动输入接口
	 *
	 */
	public static function getPassiveInputInterface($serverType='')
	{
		$config = self::_getConfig();
		if ($config['SyncMode'] == '1')
		{//主动输入主动输出
			if ($config['SyncServerType'] == 'SYNC_TPM')
			{//同步TPM
				if($serverType=='')return false;
				if ($serverType == 'INSIDE_TPM')
				{//内部TPM
					//返回外部tpm的被动输入接口
					return $config['outsideTpmPassiveInputAddress'];
				}
				else if ($serverType == 'OUTSIDE_TPM')
				{//外部TPM
					//返回内部tpm的被动输入接口
					return $config['insideTpmPassiveInputAddress'];
				}
			}
		}
		else if ($config['SyncMode'] == '2')
		{//主动输入被动输出
			if ($config['SyncServerType'] == 'INSIDE_TPM')
			{//内部TPM
				if($serverType=='')return false;
				if ($serverType == 'INSIDE_TPM')
				{//内部TPM
					//返回外部tpm的被动输入接口
					return $config['outsideTpmPassiveInputAddress'];
				}
				else if ($serverType == 'OUTSIDE_TPM')
				{//外部TPM
					//返回内部tpm的被动输入接口
					return $config['insideTpmPassiveInputAddress'];
				}
			}
		}
		else if ($config['SyncMode'] == '3')
		{//被动输入主动输出
			if ($config['SyncServerType'] == 'INSIDE_TPM')
			{//内部TPM
				//返回同步tpm的被动输入接口
				return $config['syncTpmPassiveInputAddress'];
			}
			else if ($config['SyncServerType'] == 'OUTSIDE_TPM')
			{//外部TPM				
				//返回同步tpm的被动输入接口
				return $config['syncTpmPassiveInputAddress'];
			}
			else if ($config['SyncServerType'] == 'SYNC_TPM')
			{//同步TPM
				if($serverType=='')return false;
				if ($serverType == 'INSIDE_TPM')
				{//内部TPM
					//返回外部tpm的被动输入接口
					return $config['outsideTpmPassiveInputAddress'];
				}
				else if ($serverType == 'OUTSIDE_TPM')
				{//外部TPM
					//返回内部tpm的被动输入接口
					return $config['insideTpmPassiveInputAddress'];
				}
			}
		}
		else if ($config['SyncMode'] == '4')
		{//被动输入被动输出
			if ($config['SyncServerType'] == 'INSIDE_TPM')
			{//内部TPM
				if($serverType=='')return false;
				if ($serverType == 'INSIDE_TPM')
				{//内部TPM
					//返回外部tpm的被动输入接口
					return $config['outsideTpmPassiveInputAddress'];
				}
				else if ($serverType == 'OUTSIDE_TPM')
				{//外部TPM
					//返回内部tpm的被动输入接口
					return $config['insideTpmPassiveInputAddress'];
				}
			}
			else if ($config['SyncServerType'] == 'OUTSIDE_TPM')
			{//外部TPM
				//返回同步tpm的被动输入接口
				return $config['syncTpmPassiveInputAddress'];
			}
		}
	}
	
	/**
	 * 获取被动输出接口
	 *
	 */
	public static function getPassiveOutputInterface()
	{
		$config = self::_getConfig();
		if ($config['SyncMode'] == '1')
		{//主动输入主动输出
			if ($config['SyncServerType'] == 'SYNC_TPM')
			{//同步TPM
				//返回内部，外部tpm的被动输出接口
				return array($config['insideTpmPassiveOutputAddress'],$config['outsideTpmPassiveOutputAddress']);
			}
		}
		else if ($config['SyncMode'] == '2')
		{//主动输入被动输出
			if ($config['SyncServerType'] == 'INSIDE_TPM')
			{//内部TPM				
				//返回同步tpm的被动输出接口
				return $config['syncTpmPassiveOutputAddress'];
			}
			else if ($config['SyncServerType'] == 'SYNC_TPM')
			{//同步TPM			
				//返回外部tpm的被动输出接口
				return $config['outsideTpmPassiveOutputAddress'];	
			}
		}
		else if ($config['SyncMode'] == '4')
		{//被动输入被动输出
			if ($config['SyncServerType'] == 'INSIDE_TPM')
			{//内部TPM
				//返回同步tpm的被动输出接口
				return $config['syncTpmPassiveOutputAddress'];
			}
		}
		return false;
	}
	
	/**
	 * 获取主动输出接口是对文件操作还是对数据库操作
	 * @return string 'db' or 'file'
	 */
	public static function getInitiativeOutputMsgListObj()
	{
		$config = self::_getConfig();
		//判断是写数据库还是写文件
		if ($config['SyncMode']=='1' && $config['SyncServerType']=='SYNC_TPM')
		{
			return 'db';
		}
		else if ($config['SyncMode']=='2' && $config['SyncServerType']=='INSIDE_TPM')
		{
			return 'db';
		}
		else if ($config['SyncMode']=='3' && $config['SyncServerType']=='OUTSIDE_TPM')
		{
			return 'file';
		}
		else if ($config['SyncMode']=='3' && $config['SyncServerType']=='INSIDE_TPM')
		{
			return 'file';
		}
		else if ($config['SyncMode']=='3' && $config['SyncServerType']=='SYNC_TPM')
		{
			return 'db';
		}					
		else if ($config['SyncMode']=='4' && $config['SyncServerType']=='INSIDE_TPM')
		{
			return 'db';
		}
		else if ($config['SyncMode']=='4' && $config['SyncServerType']=='OUTSIDE_TPM')
		{
			return 'file';
		}
		return false;
	}
	
	/**
	 * 权限认证
	 * @param $interfaceType string 接口类型
	 * @return true or false
	 */
	public static function interfaceAuth($interfaceType='')
	{
		if ($interfaceType=='')return false;
		$config = self::_getConfig();
		
		if ($config['SyncMode']=='1')
		{
			if ($config['SyncServerType']=='INSIDE_TPM' || $config['SyncServerType']=='OUTSIDE_TPM')
			{
				if ($interfaceType=='PassiveInput' || $interfaceType=='PassiveOutput')
				{//被动输入,被动输出
					return true;
				}
			}
			else if ($config['SyncServerType']=='SYNC_TPM')
			{
				if ($interfaceType=='InitiativeInput' || $interfaceType=='InitiativeOutput')
				{//主动输入,主动输出
					return true;
				}
			}
		}
		else if ($config['SyncMode']=='2')
		{
			if ($config['SyncServerType']=='INSIDE_TPM')
			{
				if ($interfaceType=='InitiativeInput' || $interfaceType=='InitiativeOutput' || $interfaceType=='PassiveInput')
				{//主动输入,主动输出,被动输入
					return true;	
				}
			}
			else if ($config['SyncServerType']=='OUTSIDE_TPM')
			{
				if ($interfaceType=='PassiveInput' || $interfaceType=='PassiveOutput')
				{//被动输入,被动输出
					return true;	
				}
			}
			else if ($config['SyncServerType']=='SYNC_TPM')
			{
				if ($interfaceType=='InitiativeInput' ||$interfaceType=='PassiveOutput')
				{//主动输入,被动输出
					return true;
				}
			}
		}
		else if ($config['SyncMode']=='3')
		{
			if ($config['SyncServerType']=='INSIDE_TPM' || $config['SyncServerType']=='OUTSIDE_TPM' || $config['SyncServerType']=='SYNC_TPM')
			{
				if ($interfaceType=='InitiativeOutput' || $interfaceType=='PassiveOutput')
				{//主动输出,被动输出
					return true;
				}
			}
		}
		else if ($config['SyncMode']=='4')
		{
			if ($config['SyncServerType']=='INSIDE_TPM')
			{
				if ($interfaceType=='InitiativeInput' || $interfaceType=='InitiativeOutput' || $interfaceType=='PassiveInput')
				{//被动输入,被动输出,被动输入
					return true;
				}
			}
			else if ($config['SyncServerType']=='OUTSIDE_TPM')
			{
				if ($interfaceType=='InitiativeOutput' || $interfaceType=='PassiveInput')
				{//主动输出,被动输入
					return true;
				}
			}
			else if ($config['SyncServerType']=='SYNC_TPM')
			{
				if ($interfaceType=='PassiveInput' || $interfaceType=='PassiveOutput')
				{//被动输入,被动输出
					return true;
				}
			}
		}
		return false;
	}
	
	//保证一个会话内只产生一个列标识
	private static $_msgSign;

	/**
	 *	生成消息序列标识
	 *
	 * @return string 微秒
	 */
	public static function getMsgSign()
	{
		if( !self::$_msgSign ){
			list($usec, $sec) = explode(" ", microtime());		
			list($head, $end) = explode(".", $usec);		
			self::$_msgSign = $sec.$end;
		}
		return self::$_msgSign;
	}
	
	/**
	 * 获取消息序列过滤器 
	 * @param $serverType 服务器类型
	 */
	public static function getMsgListFilter($serverType='')
	{		
		if($serverType =='INSIDE_TPM')
		{
			return array('TpmInToOut');
		}
		else if($serverType =='OUTSIDE_TPM')
		{
			return array('TpmOutToIn');
		} else {
			return array();
		}
	}
	
	/**
	 * 验证密码
	 *
	 * @param string $pwd
	 * @return true or false
	 */
	public static function passwordAuth($pwd='')
	{
		if ($pwd=='')return false;
		//获取配置
		$config = self::_getConfig();
		if(isset($config['validatePassword']) && ($config['validatePassword'] == $pwd))return true;
		return false;
	}
	
	/**
	 * 获取验证密码
	 *
	 * @param string $pwd
	 * @return true or false
	 */
	public static function getValidatePassword()
	{
		//获取配置
		$config = self::_getConfig();
		if(isset($config['validatePassword']))return $config['validatePassword'];
		return '';
	}
	
	/**
	 * 移动消息序列(从外部移动到执行错误目录 
	 *
	 * @param unknown_type $msgSign
	 */
	public static function moveMsglist($msgSign,$TpmMsgListDir=''){
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		if ($TpmMsgListDir == ''){
			$TpmMsgListDir = $config['TpmMsgListDir'];	
		}
				
		$TpmMsgListExtName = $config['TpmMsgListExtName'];
		
		//文件名设置
		$filename = $TpmMsgListDir.$msgSign.".".$TpmMsgListExtName;
		$filename_new = $TpmMsgListDir.'f/'.$msgSign.".".$TpmMsgListExtName;	
		
		//目录不存在则创建
		if (!is_dir(dirname($filename_new)))BaseOption::create_dir($filename_new);		
		if (is_file($filename))
		{
			copy($filename,$filename_new);
			//删除操作
			unlink($filename);
		}
	}
	/**
	 * 移动消息序列(从sync下移动到old/年月日目录 
	 *
	 * @param unknown_type $msgSign
	 */
	public static function moveMsglistOld($msgSign,$TpmMsgListDir=''){
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		if ($TpmMsgListDir == ''){
			$TpmMsgListDir = $config['TpmMsgListDir'];	
		}		
				
		$TpmMsgListExtName = $config['TpmMsgListExtName'];
		if (is_array($msgSign) && count($msgSign)){
			foreach ($msgSign as $key => $val){
				//文件名设置
				$filename = $TpmMsgListDir.$val.".".$TpmMsgListExtName;
				$filename_new = $TpmMsgListDir.'old/'.date("Ymd").'/'.$val.".".$TpmMsgListExtName;	
				//目录不存在则创建
				if (!is_dir(dirname($filename_new)))BaseOption::create_dir($filename_new);		
				if (is_file($filename))
				{
					copy($filename,$filename_new);
					//删除操作
					unlink($filename);
				}
			}
		}		
		return '1';
	}
	
		/**
	 * 移动消息序列(从sync下移动到old/年月日目录 
	 *
	 * @param unknown_type $msgSign
	 */
	public static function moveOutsideMsglistOld($msgSign,$TpmMsgListDir=''){
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		if ($TpmMsgListDir == ''){
			$TpmMsgListDir = $config['TpmMsgListDir'];	
		}		
				
		$TpmMsgListExtName = $config['TpmMsgListExtName'];
		if (is_array($msgSign) && count($msgSign)){
			foreach ($msgSign as $key => $val){
				//文件名设置
				$filename = $TpmMsgListDir.'outside/'.$val.".".$TpmMsgListExtName;
				$filename_new = $TpmMsgListDir.'outside/old/'.date("Ymd").'/'.$val.".".$TpmMsgListExtName;	
				//目录不存在则创建
				if (!is_dir(dirname($filename_new)))BaseOption::create_dir($filename_new);		
				if (is_file($filename))
				{
					copy($filename,$filename_new);
					//删除操作
					unlink($filename);
				}
			}
		}else {
			//文件名设置
				$filename = $TpmMsgListDir.'outside/'.$msgSign.".".$TpmMsgListExtName;
				$filename_new = $TpmMsgListDir.'outside/old/'.date("Ymd").'/'.$msgSign.".".$TpmMsgListExtName;	
				//目录不存在则创建
				if (!is_dir(dirname($filename_new)))BaseOption::create_dir($filename_new);		
				if (is_file($filename))
				{
					copy($filename,$filename_new);
					//删除操作
					unlink($filename);
				}
		}
		return '1';
	}
	/**
	 * 移动未知消息序列
	 *
	 * @param unknown_type $msgSign
	 */
	public static function moveUnknownMsglist($msgSign){
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		$TpmMsgListDir = $config['TpmMsgListDir'];
		$TpmMsgListExtName = $config['TpmMsgListExtName'];
		
		if (is_array($msgSign) && count($msgSign)){
			foreach ($msgSign as $key => $val){
				//文件名设置
				$filename = $TpmMsgListDir.$val.".".$TpmMsgListExtName;
				//$filename_new = $TpmMsgListDir.'unknown/'.$syncServerType.'-'. $val.".".$TpmMsgListExtName;	
				$filename_new = $TpmMsgListDir.'unknown/'. $val.".".$TpmMsgListExtName;	
				
				//目录不存在则创建
				if (!is_dir(dirname($filename_new)))BaseOption::create_dir($filename_new);		
				if (is_file($filename))
				{
					copy($filename,$filename_new);
					//删除操作
					unlink($filename);
				}		
			}
		}
		
	}
	/**
	 * 移动消息序列(从未知到外部)
	 *
	 * @param unknown_type $msgSign
	 */
	public static function moveMsglistUnknown($msgSign){
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		$TpmMsgListDir = $config['TpmMsgListDir'];
		$TpmMsgListExtName = $config['TpmMsgListExtName'];
		
		$msgSign = self::getAllMsgSign($TpmMsgListDir.'unknown/');
		//将移动制定的改为移动所有的		
		if (is_array($msgSign) && count($msgSign)){
			foreach ($msgSign as $key => $val){
				//文件名设置
				$filename = $TpmMsgListDir.'unknown/'. $val.".".$TpmMsgListExtName;	
				$filename_new = $TpmMsgListDir.'outside/'.$val.".".$TpmMsgListExtName;
				
				//目录不存在则创建
				if (!is_dir(dirname($filename_new)))BaseOption::create_dir($filename_new);
				
				copy($filename,$filename_new);
				unlink($filename);
				/*
				if (is_file($filename))return false;
				if(!copy($filename,$filename_new))return false;
				//删除操作
				if(!unlink($filename))return false;
				*/
			}
		}
		
		return true;		
	}
	/**
	 * 移动消息SQL
	 *
	 * @param string $msgSign
	 */
	public static function moveMsgSql($msgSign){
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		$TpmMsgListDir = $config['TpmMsgListDir']."msgsql/";
		$TpmMsgListExtName = "msgsql";
		
		//文件名设置
		$filename = $TpmMsgListDir.$msgSign.".".$TpmMsgListExtName;
		$filename_new = $TpmMsgListDir.'f/'.$msgSign.".".$TpmMsgListExtName;	
		
		//目录不存在则创建
		if (!is_dir(dirname($filename_new)))BaseOption::create_dir($filename_new);		
		if (is_file($filename))
		{
			copy($filename,$filename_new);
			//删除操作
			unlink($filename);
		}
	}
	
	/**
	 * 获取数据库信息
	 *
	 * @return string
	 */
	public static function getmsg1()
	{
		$tableName = array('tpm_dingdan',
							'tpm_xiangmu',
							'tpm_renwu',
							'tpm_gaojian'
							);
		$sql = "select count(dd_id) as count from tpm_dingdan";	
		$dingdan_count = Watt_Db::getDb()->getOne($sql,false);
		
		$sql = "select count(xm_id) as count from tpm_xiangmu";	
		$xiangmu_count = Watt_Db::getDb()->getOne($sql,false);
		
		$sql = "select count(rw_id) as count from tpm_renwu";	
		$renwu_count = Watt_Db::getDb()->getOne($sql,false);
				
		$sql = "select count(gj_id) as count from tpm_gaojian";	
		$gaojian_count = Watt_Db::getDb()->getOne($sql,false);	
		
		return serialize(array('dingdan'=>$dingdan_count,
					'xiangmu'=>$xiangmu_count,	
					'renwu'=>$renwu_count,	
					'gaojian'=>$gaojian_count,	
		));
		//return "订单表：".$dingdan_count."条 项目表：".$xiangmu_count."条 任务表：".$renwu_count."条 稿件表：".$gaojian_count."条 ";
		
	}
	
	/**
	 * 格式化消息，为输出显示用
	 *
	 * @param unknown_type $val
	 */
	public static function formatMsg($msgList){
		
		if (!$msgList || !is_array($msgList) || !count($msgList))return '';
		//组合SQL
		$str='';
		$sql ="";
		if ($msgList['operate']=='UPDATE')
		{		
			$cols = array();
			foreach ($msgList['cols'] as $key => $val){
				$cols[] = $key.'='.$val;
			}
			$cols = implode(',',$cols);
			
			$sql = $msgList['operate']." ".$msgList['tableName']." SET ".$cols." WHERE ".$msgList['cond'];
			
			$str = '操作：更改'. self::returnNameTable($msgList['tableName'])."<br>SQL:".$sql;
		}
		else if ($msgList['operate']=='INSERT')
		{
			$cols =array();
			$values = array();
			foreach ($msgList['cols'] as $key => $val){
				$cols[] = $key;
				$values[] = $val;
			}
			$cols = implode(',',$cols);
			$values = implode(',',$values);
			$sql = $msgList['operate']." INTO ".$msgList['tableName']." (".$cols.") VALUES (".$values.")";
			
			$str = '操作：增加'. self::returnNameTable($msgList['tableName'])."<br>SQL:".$sql;
		}
		else if ($msgList['operate']=='DELETE')
		{						
			$sql = $msgList['operate']." FROM ".$msgList['tableName']." WHERE ".$msgList['cond'];
			$str = '操作：删除'. self::returnNameTable($msgList['tableName'])."<br>SQL:".$sql;
		}
		return $str;
	}
	
	public static function returnNameTable($tableName){
		$table = array('tpm_yonghu'=>'用户',
						'tpm_dingdan'=>'订单',
						'tpm_xiangmu'=>'项目',
						'tpm_renwu'=>'任务',
						'tpm_gaojian'=>'稿件',
						);
						
		if($table[$tableName]){
			return $table[$tableName];
		}else{
			return $tableName.'表';
		}
	}
	

	/**
	 * 获取外部站点信息
	 *
	 * @return unknown
	 */
	public static function getOutsiteinfo(){
		$config = self::_getConfig();
		$outsite_get_Address = $config['outsideTpmGetDbInfoAddress'];
		$url = $outsite_get_Address."&validatePassword=".Watt_Sync_MessageListManage::getValidatePassword();		
		return file_get_contents($url);
		
		//$data =array('validatePassword'=>self::getValidatePassword());
		//return BaseOption::postToHost($outsite_get_Address,$data);
	}
	
	/**
	 * 获取数据库所有表的记录数
	 *
	 * @return unknown
	 */
	public static function getDbAllTableCount(){
		$re =array();
		$sql = "show tables";
		$dd = Watt_Db::getDb()->getAll($sql);
		
		if (is_array($dd) && count($dd)){
			foreach ($dd as $k =>$v){
				$v_ = array_values($v);
				try {
					Watt_Db::getDb()->getAll( "SHOW CREATE VIEW " . $v_[0]);
				}catch (Exception $e){
					$sql = "select count(*) as count from ".$v_[0];	
					$dingdan_count = Watt_Db::getDb()->GetOne($sql);
					$re[$v_[0]] = $dingdan_count;
				}
			}
		}
		return $re;
	}
	/**
	 * 获取外部站点数据库所有表的记录数
	 *
	 * @return unknown
	 */
	public static function getOutDbAllTableCount(){
		$config = self::_getConfig();
		$outsite_get_Address = $config['outsideTpmDomain'];
		$url = $outsite_get_Address."index.php?do=sync_sync_getDbAllTableCount&validatePassword=".Watt_Sync_MessageListManage::getValidatePassword();
		$re = unserialize(urldecode(file_get_contents($url)));
		return $re;
	}	
	
	/**
	 * 判断是否是外部站点
	 * @return ture or false
	 */
	public static function isOutsite(){		
		$config = self::_getConfig();
		if ($config['SyncServerType']=='OUTSIDE_TPM'){
			return true;
		}
		return false;
	}	
	
	/**
	 * 加锁
	 *
	 * @param unknown_type $act
	 */
	public static function AddLock($act){
		$config = self::_getConfig();
		$filename = $config['TpmMsgListDir'].$act.'.lock';
		
		ob_start();
		print_r();
		$fp = fopen($filename, 'wb');
		fwrite($fp, ob_get_contents());
		fclose($fp);
		ob_end_clean();
	}
	public static function DelLock($act){
		$config = self::_getConfig();
		$filename = $config['TpmMsgListDir'].$act.'.lock';
		
		if(is_file($filename))unlink($filename);
	}
	/**
	 * 判断指定接口是否锁定
	 *
	 * @return ture or false
	 */
	public static function isLock($act){
		$config = self::_getConfig();
		$filename = $config['TpmMsgListDir'].$act.'.lock';
		$lockovertime = '600';//默认超时10分钟自动删除
		if($config['LockOverTime'])$lockovertime=$config['LockOverTime'];
		
		if (is_file($filename)){
			//获取锁文件的创建时间
			if(time()-filemtime($filename)>$lockovertime){//超时
				//删除所文件
				self::DelLock($act);
				return false;
			}
			return true;
		}
		return false;		
	}

	/**
	 * sql字符串分割
	 *
	 */
	public static function sqlStrSplit($str){
		if(preg_match("/[^\\\]\\\\\\\\'/",$str)){
			$str = preg_replace("/([^\\\])\\\\\\\\'/","\${1}#@@@@@#-----#=====#=====#-----#@@@@@#'",$str);
			$str = str_replace('\\\'','====================--------------------',$str);	
			$pattern = "/('[^']*')|,?([^',]+),?/";
			//$pattern = "/('.*?(?<![\\\\])')|,?([^,]+),?/";
			$res = array();
			if (preg_match_all($pattern, $str, $match))
			{
				foreach($match[1] as $key=>$val)
				{
					if ($val == '')
					{
						$res[$key] = $match[2][$key];
					}
					else{
						$val = str_replace('====================--------------------','\\\'',$val);
						$val = str_replace('#@@@@@#-----#=====#=====#-----#@@@@@#','\\\\',$val);	
						$res[$key] = $val;
					}
				}
			}
			
		}else{
			$str = str_replace('\\\'','====================--------------------',$str);	
			$pattern = "/('[^']*')|,?([^',]+),?/";
			//$pattern = "/('.*?(?<![\\\\])')|,?([^,]+),?/";
			$res = array();
			if (preg_match_all($pattern, $str, $match))
			{
				foreach($match[1] as $key=>$val)
				{
					if ($val == '')
					{
						$res[$key] = $match[2][$key];
					}
					else{
						$val = str_replace('====================--------------------','\\\'',$val);
						$res[$key] = $val;
					}
						
				}
			}
		}
		
		return $res;
	}
	public static function sqlStrSplit1($str){
		if(preg_match("/[^\\\]\\\\\\\\'/",$str)){
			$str = preg_replace("/([^\\\])\\\\\\\\'/","\${1}#@@@@@#-----#=====#=====#-----#@@@@@#'",$str);
			$str = str_replace('\\\'','====================--------------------',$str);	
			$pattern = "/,?(\s*\w*\s*=\s*'[^']*'),?|,?(\s*\w*\s*=\s*[^',]+),?/";
			$res = array();
			if (preg_match_all($pattern, $str, $match))
			{		
				foreach($match[1] as $key=>$val)
				{
					if ($val == '')
					{
						$res[$key] = $match[2][$key];
					}
					else{					
						$val = str_replace('====================--------------------','\\\'',$val);
						$val = str_replace('#@@@@@#-----#=====#=====#-----#@@@@@#','\\\\',$val);	
						$res[$key] = $val;
					}
				}			
			}
		}else{
			$str = str_replace('\\\'','====================--------------------',$str);	
			$pattern = "/,?(\s*\w*\s*=\s*'[^']*'),?|,?(\s*\w*\s*=\s*[^',]+),?/";
			$res = array();
			if (preg_match_all($pattern, $str, $match))
			{		
				foreach($match[1] as $key=>$val)
				{
					if ($val == '')
					{
						$res[$key] = $match[2][$key];
					}
					else{					
						$val = str_replace('====================--------------------','\\\'',$val);						
						$res[$key] = $val;
					}
				}			
			}
		}
		
		
		$str = str_replace('\\\\\'','#@@@@@#-----#=====#=====#-----#@@@@@#\'',$str);		
		
		return $res;
	}
	
	/**
	 * 获取一次删除消息序列的个数
	 * 默认10个
	 * @return int 
	 * 
	 */
	public static function getNumOnceDel(){
		$config = self::_getConfig();
		if ($config['numOnceDel']!=''){			
			return $config['numOnceDel'];
		}
		return '10';
	}
	
	public static function getReportTime(){
		$config = self::_getConfig();		
		//获取消息序列文件的存放地址
		$filename = $config['TpmMsgListDir'].'alert.report';
		if (is_file($filename))
		{//读取
			$cont = file_get_contents($filename);
			if (!$cont || $cont==''){
				
			}else{
				return $cont;
			}			
		}else
		{
			ob_start();
			print_r(time());
			$fp = fopen($filename, 'wb');
			fwrite($fp, ob_get_contents());
			fclose($fp);
			ob_end_clean();			
			if (is_file($filename))
			{//写文件成功
				return time();
			}
		}
	}
		
	public static function setReportTime(){
		$config = self::_getConfig();		
		//获取消息序列文件的存放地址
		$filename = $config['TpmMsgListDir'].'alert.report';
		//写文件
		ob_start();
		print_r(time());
		$fp = fopen($filename, 'wb');
		fwrite($fp, ob_get_contents());
		fclose($fp);
		ob_end_clean();			
	}
	
	/**
	 * 执行本地消息序列
	 *
	 * @param unknown_type $pwd
	 */
	public static function execMsgList()
	{
		//判断是否锁定
		if (Watt_Sync_MessageListManage::isLock('exec'))
		{//如果锁定
			return false;
		}
		
		$result=false;
		Watt_Sync_MessageListManage::AddLock('exec');
		$config = self::_getConfig();					
		//$TpmMsgListDir = $config['TpmMsgListDir'];
		$TpmMsgListDir = $config['TpmMsgListDir'].'outside/';		
		
		$msglistArr = self::getAllMessageList('outside');
		if(is_array($msglistArr) && count($msglistArr)){
			foreach ($msglistArr as $key => $val){
				foreach ($val as $k=>$v){
					if (self::executeMessageList($v))
					{//执行成功
						//删除序列
						//Watt_Sync_MessageListManage::delMessageList($key,$TpmMsgListDir);	
						$result = '1';
					}else{
						//移动序列
						$result = '0';
						break;
						//Watt_Sync_MessageListManage::moveMsglist($key,$TpmMsgListDir);
					}	
				}
				if($result =='1'){
					//执行成功，删除序列
					//Watt_Sync_MessageListManage::delMessageList($key,$TpmMsgListDir);
					//将消息序列移动到outside/old/年月日/目录下
					Watt_Sync_MessageListManage::moveOutsideMsglistOld($key);
				}
				else
				{//移动序列
					Watt_Sync_MessageListManage::moveMsglist($key,$TpmMsgListDir);
				}
				/*
				if (self::executeMessageList($val))
				{//执行成功
					//删除序列
					Watt_Sync_MessageListManage::delMessageList($key,$TpmMsgListDir);	
					$result = '1';
				}else{
					//移动序列
					Watt_Sync_MessageListManage::moveMsglist($key,$TpmMsgListDir);
				}
				*/
			}
		}
		
		Watt_Sync_MessageListManage::DelLock('exec');
		return $result;
		//executeMessageList
	}
	
	/**
	 * 将socket传过来的数据存入文件
	 *
	 * @param unknown_type $data
	 * @return unknown
	 */
	public static function createdMsgListFile($data,$i=1)
	{
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		$filename = $config['TpmMsgListDir'].'unknown/msgListfile.msg';
		if ($i==1){//如果是第一段
			ob_start();
				print_r($data);
				$fp = fopen($filename, 'wb');
				fwrite($fp, ob_get_contents());
				fclose($fp);
				ob_end_clean();
				/*
			if (is_file($filename))
			{
				return false;		
			}else{				
				
			}
			*/
		}else if ($i){
			if (is_file($filename))
			{
				$fp = fopen($filename, 'ab');
				fwrite($fp, $data);
				fclose($fp);		
			}else{
				return false;
			}
		}else{//$i=0
			return false;
		}
		
		if(is_file($filename)){
			return true;
		}else {
			return false;
		}
	}
	/**
	 * 创建本地sync目录下的消息序列汇总文件
	 *
	 * @param unknown_type $data
	 * @param unknown_type $i
	 * @return unknown
	 */
	public static function createdMsgListFileSync($data)
	{
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		$filename = $config['TpmMsgListDir'].'msgListfile.msg';
		if (is_file($filename))
		{
			return false;		
		}else{
			$fp = fopen($filename, 'wb');
			fwrite($fp, $data);
			fclose($fp);		
		}
		if(is_file($filename)){
			return true;
		}else {
			return false;
		}
	}
	/**
	 * 解析socket生成消息序列文件
	 *
	 * @return unknown
	 */
	public static function execMsgListFile(){
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		$filename = $config['TpmMsgListDir'].'unknown/msgListfile.msg';		
		$data = file_get_contents($filename);
		$data = unserialize(urldecode($data));
		if (is_array($data) && count($data)){
			$msgSignArr = array_keys($data['msgList']);
			$result=Watt_Sync_MessageListManage::createAllMsgList($data['msgList'],$data['msgListDir']);
			if ($result=='1'){
				unlink($filename);//删除文件
				if (!is_file($filename)){
					//return '1';		
					return $msgSignArr;
				}else{
					return '0';
				}
			}else {
				return '0';
			}
		}
	}
	
	/**
	 * 读取指定个数的消息序列生成文件
	 * 
	 * 返回值：成功返回文件大小，失败返回false
	 */
	public static function createLocalMsgListFile(){
		$config = self::_getConfig();
		$count = $config['numOnceDel'];
		$msglist = self::getAllMessageList('',$count);		
		$data = array(
							'msgList' => $msglist,
							'msgListDir' => 'unknown',
							'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword()
						);
		$data = urlencode(serialize($data));
		//写文件，目录sync下
		$filename = $config['TpmMsgListDir'].'msgListfile.msg';
		if(!is_file($filename))
		{//不存在
			$fp = fopen($filename, 'wb');
			fwrite($fp, $data);
			fclose($fp);	
			if(is_file($filename)){
				return strlen($data);
			}else {
				return false;
			}
		}
		else
		{//存在
			
		}
	}
	/**
	 * //获取消息序列文件指定块的内容
	 *
	 * @param unknown_type $num
	 * @return unknown
	 */
	public static function getLocalMsgListFileCbyNum($num='',$allcount=0)
	{
		if($num=='')return false;
		$config = self::_getConfig();
		$filename = $config['TpmMsgListDir'].'msgListfile.msg';
		if(!is_file($filename))return false;
		
		$dd = file_get_contents($filename);
		$dd_chunk = str_split($dd,1000);
		$count = count($dd_chunk);
		
		//如果要获取的块数比总数大或者跟传过来的总数不一样
		if(($count<$num) || ($allcount!=$count))return false;				
		return $dd_chunk[($num-1)];		
	}
	
	/**
	 * 获取unknown 目录下消息序列汇总文件的大小
	 *	$dir::从sync目录开始往下的目录路径
	 *  $fn::文件名
	 * @return 成功返回文件字节数,失败返回false;
	 */
	public static function getLocalMsgListFileSize($dir='',$fn='')
	{
		$config = self::_getConfig();
		if ($fn == '')return false;
		
		$filename = $config['TpmMsgListDir'].$dir.$fn;
		if (is_file($filename)){
			$data = file_get_contents($filename);
			return strlen($data);
		}else {
			return false;
		}		
	}
	
	/**
	 * 删除文件
	 *
	 * @param unknown_type $dir
	 * @param unknown_type $fn
	 * @return unknown
	 */
	public static function delFile($dir='',$fn='')
	{
		$config = self::_getConfig();
		if ($fn == '')return false;
		
		$filename = $config['TpmMsgListDir'].$dir.$fn;
		if(!is_file($filename)){
			return '1';
		}else{
			unlink($filename);
			if (!is_file($filename)){
				return '1';		
			}else{
				return false;
			}
		}
	}
	/**
	 * 判断unknown是否存在消息序列汇总文件
	 *
	 * @return unknown
	 */
	public static function isexistLocalMsgListFile($dir='',$fn='')
	{
		$config = self::_getConfig();
		if ($fn == '')return false;
		
		$filename = $config['TpmMsgListDir'].$dir.$fn;
		if (is_file($filename)){
			return '1';
		}else {
			return false;
		}		
	}
	
	/**
	 * 删除本地消息序列汇总文件
	 *	返回值：成功返回true,失败返回false
	 */
	public static function delLocalMsgListFile()
	{
		$config = self::_getConfig();
		$filename = $config['TpmMsgListDir'].'msgListfile.msg';
		if(!is_file($filename)){
			return '1';
		}else{
			unlink($filename);
			if (!is_file($filename)){
				return '1';		
			}else{
				return '0';
			}
		}	
	}	
	
	
	
	
	
	/************测试使用************/
	public static function getErrMsgList($msgSign)
	{
		if( !$msgSign )return false;
		//文件名设置
		$config = self::_getConfig();
		//获取消息序列文件的存放地址
		$TpmMsgListDir = $config['TpmMsgListDir'].'outside/f/';
		$TpmMsgListExtName = $config['TpmMsgListExtName'];
		
		$filename = $TpmMsgListDir.$msgSign.".".$TpmMsgListExtName;
		//分行读取文件，一行产生一个序列
		$handle = @fopen($filename, "r");
		if ($handle) {
		    while (!feof($handle)) {
		        $buffer = fgets($handle, 65536);
		        if($buffer!=''){
		        	$rev_ = unserialize($buffer);
		        	echo self::executeMessageListttt($rev_)."<br>";
		        	//echo $rev_."<br>";
		        }
		    }
		    fclose($handle);
		}
		
		/*if (!$msgList)return false;
		//反序列化
		$msgList = unserialize($msgList);
		return $msgList;	
		*/
	}
	
	public static function executeMessageListttt($msgList)
	{		
		if (!$msgList || !is_array($msgList) || !count($msgList))return false;
		//组合SQL
		$sql ="";
		if ($msgList['operate']=='UPDATE')
		{
			$cols = array();
			foreach ($msgList['cols'] as $key => $val){
				$cols[] = $key.'='.$val;
			}
			$cols = implode(',',$cols);
			
			$sql = $msgList['operate']." ".$msgList['tableName']." SET ".$cols." WHERE ".$msgList['cond'];
		}
		else if ($msgList['operate']=='INSERT')
		{
			$cols =array();
			$values = array();
			foreach ($msgList['cols'] as $key => $val){
				$cols[] = $key;
				$values[] = $val;
			}
			print "<pre>";
			print_r($values);
			print "</pre>";
			$cols = implode(',',$cols);
			$values = implode(',',$values);
			$sql = $msgList['operate']." INTO ".$msgList['tableName']." (".$cols.") VALUES (".$values.")";
		}
		else if ($msgList['operate']=='DELETE')
		{
			$sql = $msgList['operate']." FROM ".$msgList['tableName']." WHERE ".$msgList['cond'];
		}
		return $sql;		
	}
	
	
	public static function createDbMsgListtt( $sql ){	
		$config = self::_getConfig();
		$sql = trim( $sql );
		if( !$sql )return false;
		$tableName = '';
		$logName   = '';
		$msg       = '';
		$msgList = array();
		
		//忽略列表优先于允许列表
		$ignoreTalbenameList = array(
			'tpm_rizhi' => 'rz_id',
			);
		
		$toLogTablenameList = array(
			'tpm_dingdan' => 'dd_id',
			'tpm_shengchandingdan' => 'sd_id',
			'tpm_xiangmu' => 'xm_id',
			'tpm_renwu'   => 'rw_id',
			'tpm_gaojian'   => 'gj_id',
			'tpm_yonghukuozhan'   => 'yh_id',
			);
			
		//因为之前已经trim了，所以 === 0
		if( stripos( $sql, 'SELECT' ) === 0 ){
			//不记录Select
			return false;
		}elseif( stripos( $sql, 'UPDATE' ) === 0 ){
			if (preg_match("/UPDATE tpm_yonghu SET YH_ZAIXIAN_ZHUANGTAI = 0,YH_ZAIXIANSHIJIAN =/i",$sql)){
					return false;
			}
			if( preg_match( "/update[\\s]+(\\w+)[\\s]+set(.*)where[\\s]+(.*)/i", $sql, $matchs ) ){
				$tableName = $matchs[1];
				$cols      = $matchs[2];
				$cond      = $matchs[3];
				print $cols.'<br>';
				//将字段值部分拆分分为数组
				//$cols=explode(',',$cols);
				$cols = self::sqlStrSplit1( $cols);	
				 $newcols = array();
				 if (is_array($cols) && count($cols))
				 {
				 	foreach ($cols as $acol)
				 	{
				 		//$col_ = explode('=',$acol);
				 		preg_match( "/\s*(\w+)\s*=\s*(.*)/i", $acol, $t );
						$col_=array($t[1],$t[2]);
				 		if (is_array($col_) && count($col_)){				 		
				 			$newcols[trim($col_[0])] = trim($col_[1]);	
				 		}
				 	}
				 }
				$msgList = array(
								'operate'=> 'UPDATE',
								'tableName' => $tableName,
								'cols' => $newcols,
								'cond' => $cond
								);
			}
		}elseif( stripos( $sql, 'INSERT' ) === 0 ){
			if( preg_match( '/insert into[\s]+(\w+)[\s]+\((.*)\)[\s]+values[\s]+\((.*)\)/i', $sql, $matchs ) ){
				$tableName = $matchs[1];
				$cols      = $matchs[2];
				$values    = $matchs[3];
				$cols = explode(',',$cols);
				//$values = explode(',',$values);
				//$values = preg_split('/,(?!\w+\',?)/i',$values);
				$values = self::sqlStrSplit($values);
				//$values=eval('return array('.$values.');');	
				$newcols = array();
				if (is_array($cols) && count($cols) &&is_array($values) && count($values))
				{
				 	foreach ($cols as $key => $val)
				 	{
				 		$newcols[$val] = $values[$key];
				 	}
				}
				 				
				$msgList = array(
								'operate'=> 'INSERT',
								'tableName' => $tableName,
								'cols' => $newcols
								);
			}
		}elseif( stripos( $sql, 'DELETE' ) === 0 ){
			if( preg_match( '/DELETE FROM[\s]+(\w+)[\s]+WHERE(.*)/i', $sql, $matchs ) ){
				$tableName = $matchs[1];
				$cond      = $matchs[2];
				$msgList = array(
								'operate'=> 'DELETE',
								'tableName' => $tableName,
								'cond' =>$cond
								);
			}
		}
		if( $msgList && count($msgList) 
		 && !key_exists( strtolower( $tableName ), $ignoreTalbenameList )
		 //&&  key_exists( strtolower( $tableName ), $toLogTablenameList )
		  ){		  	
			//服务器的类型设置
			$msgList['syncServerType'] = $config['SyncServerType'];
			return $msgList;
			//return self::createMsgList($msgList);
		}else{
			return false;
		}
	}
}

class Watt_Sync_MessageListFile{
	private $_fPoint;
	private $_fname;
	private $_tmpFilename;
	
	public function setFilename( $v ){$this->_fname = $v;}
	public function getFilename(){return $this->_fname;}
	
	public function __construct( $pathFilename ){
		//目录不存在则创建
		if (!is_dir(dirname( $pathFilename )))BaseOption::create_dir( $pathFilename );

		$this->setFilename( $pathFilename );
		$this->_tmpFilename = $pathFilename.".tmp";
		$this->_fPoint = @fopen( $this->_tmpFilename, 'a' );
		if( !$this->_fPoint ){
			Watt_Log::addLog( 'Cannot create file ['.$this->_tmpFilename.']' );
		}
	}
	
	/**
	 * @return boolean
	 * @author terry
	 * @version 0.1.0
	 * Fri Jun 29 19:46:02 CST 2007
	 */
	public function write( $line ){
		if( $this->_fPoint ){
			return @fwrite( $this->_fPoint, $line );
		}else{
			return false;
		}
	}
	
	public function __destruct(){
		if( $this->_fPoint ){
			@fclose( $this->_fPoint );
			@rename( $this->_tmpFilename, $this->_fname );
		}
	}
}
?>