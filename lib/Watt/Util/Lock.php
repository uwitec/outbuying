<?php
/**
 * @abstract  实现秒级锁，如进度好则在ETC领取任务处应用锁机制防重入
 * @author confu
 * @package Watt
 * @create time 2008-7-18
 * @example 
 * $lock = Watt_Util_Lock::getLock('TS_Update_Sechedule');
 * if( !$lock->isLock() ){
 * 	$lock->lock(300);
 * 	Watt_Log::addLog("Do something, lock [ ".$lock->getLockName()." ].");
 * 	
 * 	#do something
 * 
 * 	$lock->unLock();
 * 	Watt_Log::addLog("Do something, unlock [ ".$lock->getLockName()." ].");
 * }else{
 * 	Watt_Log::addLog("Do something, but other procedure have locked [ ".$lock->getLockName()." ], so give up.");
 * }
 * 
 */
class Watt_Util_Lock
{
	
	/**
	 * 锁名字
	 */
	private $lock_name = '这是锁名1';
	
	
	/**
	 * 锁过期时间
	 */
	private static $life_time = 0;
	
	/**
	 * 写入数据表的时间
	 */
	private static $write_time;
	
	/**
	 * 自动清理数据表中过期的锁
	 *
	 */
	private static function autoClearLock()
	{
		//当前最新调用时的时间
		$now_time = time();
		$sql = "DELETE FROM tpm_lock"
				." WHERE life_time<".$now_time."-write_time";
		//也可不要返回值，直接执行SQL或者直接返回影响的行数
		return Watt_Db::getDb()->execute($sql);
	}
	/**
	 * 获得锁
	 * @param  string or int or object or other $lockName
	 * @param int $lifeTime
	 * @return Watt_Util_Lock
	 */
	public static function getLock( $lockName)
	{
		if(empty($lockName))return;
		
		//自定义每隔多少次才自动清理
 		if( 1 == mt_rand( 1,50 ) )
		{
			//先自动从数据表中清理过期的锁
			self::autoClearLock();
		}
		
		$sql = "SELECT * 
				FROM tpm_lock 
				WHERE lock_name='".chks($lockName)."'";
		$result = Watt_Db::getDb()->getRow($sql);
		
		$objLock = new Watt_Util_Lock();
		$objLock->lock_name = $lockName;
		
		//如果锁存在
		if(!empty($result))
		{
			//如果锁过期
			if((time()-$result['write_time']) > $result['life_time'])
			{
				//删除锁
				$objLock->unLock();
			}
		}
		return $objLock;
	}
	
	/**
	 * 设置锁
	 * 0为不超时，直到解锁。单位为秒。同一个锁，再次上锁，以新锁参数为准
	 * @param int $lifeTime
	 * @return int
	 */
	public function lock($lifeTime)
	{
		try{
			$sql = "INSERT INTO tpm_lock"
				." SET lock_name='".chks($this->lock_name)."',"
				." life_time=".$lifeTime.','
				." write_time=".time();
			return Watt_Db::getDb()->execute($sql);
		}catch(Exception $e){
			$sql = "UPDATE tpm_lock
					SET life_time=".$lifeTime.','
					." write_time=".time()
					." WHERE lock_name='".chks($this->lock_name)."'";
			return Watt_Db::getDb()->execute($sql);
		}
				
	}  
	
	/**
	 * 根据锁名解除锁
	 * @return  int
	 */
	public function unLock()
	{
		$sql = "DELETE FROM tpm_lock
				WHERE lock_name='".chks($this->lock_name)."'";
		return Watt_Db::getDb()->execute($sql);	
	}
	
	/**
	 * 根据锁名判断是否处于锁定状态
	 * @return int
	 */
	public function isLock()
	{
		$sql = "SELECT life_time,write_time
				FROM tpm_lock
				WHERE lock_name='".chks($this->lock_name)."'";
		$result = Watt_Db::getDb()->getRow($sql);
		if($result)
		{
			//如果永不过期
			if($result['life_time']==0)return true;
			elseif((time()-$result['write_time']) > $result['life_time'])return false;
			else return true;
		}else{
			return false;			
		}
		

	} 
	
	/**
	 * 根据锁名获得锁的过期时间
	 * @return int
	 */
	public function getExpireTime()
	{
		$sql = "SELECT life_time
				FROM tpm_lock
				WHERE lock_name='".chks($this->lock_name)."'";
		return Watt_Db::getDb()->getOne($sql);		
	}
	
	/**
	 * 设置锁的过期时间
	 * @param  int $time
	 * return int
	 */
	public function setExpireTime( $time )
	{
		$sql = "UPDATE tpm_lock"
				." SET life_time=".$time
				." WHERE lock_name='".chks($this->lock_name)."'";
		return Watt_Db::getDb()->execute($sql);
	}
	
	/**
	 * 获得锁名
	 *
	 * @return string
	 */
	public function getLockName(){
		return $this->lock_name;
	}
	
	/**
	 * 测试锁
	 */
	public static function test()
	{ 
		//先自动从数据表中清理过期的锁
		self::autoClearLock();
		//锁的锁定时间，单位默认为秒
		$lifeTime = 5; 
		$aLock = self::getLock('test'); 
		assert( "false === \$aLock->isLock()" );//以备将来重构此类时进行单元测试
		$aLock->lock( $lifeTime ); 
		assert( "true === \$aLock->isLock()" ); 
		sleep($lifeTime+1); 
		assert( "false === \$aLock->isLock()" ); 
		
		$now = time(); 
		$aLock->lock( $lifeTime ); 
		assert( "(\$now + \$lifeTime) === \$aLock->getExpireTime()" ); 
	} 
}
?>