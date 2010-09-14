<?
class Watt_Log_Db extends Watt_Log{
	protected  $_logName = '';
	
	private static $_loggerArr = array();
	
	/**
	 * 获取项目日志对象
	 *
	 * @return Watt_Log_Db
	 */
	public static function getXmLogger()
	{
		return self::getXmoneLogger('tpm_xiangmu_rizhi');
	}
	/**
	 * 获取项目日志对象
	 *
	 * @return Watt_Log_Db
	 */
	public static function getGzLogger()
	{
		return self::getLogger('twf_gongzuo_rizhi');
	}
	/**
	 * 获取资源日志对象
	 *
	 */
	public static function getZyLogger()
	{
		return self::getLogger("tpm_ziyuan_rizhi");
	}
	/**
	 * 获取岗位日志对象
	 *
	 */
	public static function getGwLogger()
	{
		return self::getLogger("tpm_gangwei_rizhi");
	}
	/**
	 * 按日志文件名获得相对的日志对象
	 * @param string $logName
	 * @return Watt_Log_Db
	 */
	public static function getLogger( $logName = '' ){
		if( !key_exists( $logName, self::$_loggerArr ) ){
			self::$_loggerArr[$logName] = new Watt_Log_Db( $logName );
		}
		
		return self::$_loggerArr[$logName];
	}
	/**
	 * 按日志文件名获得相对的日志对象
	 * @param string $logName
	 * @return Watt_Log_Db
	 */
	public static function getXmoneLogger( $logName = '' ){
		if( !key_exists( $logName, self::$_loggerArr ) ){
			self::$_loggerArr[$logName] = new Watt_Log_Xiangmu( $logName );
		}
		return self::$_loggerArr[$logName];
	}
	
	/**
	 * 
	 *
	 * @param 日志文件名 $logName
	 */
	public function __construct( $logName = '' ){
		if( $logName ){
			$this->_logName = $logName;
		}else{
			$this->_logName = 'tpm_rizhi';
		}
	}
	
	/**
	 * 记录日志
	 *
	 * @param string $msg 记录的信息
	 * @param int $level
	 * @param string $sourceName
	 * @param string $actorName
	 * @param string $actorId
	 * @param mix $exts
	 * @return boolean
	 */
	public function log( $msg, $level=0, $sourceName=""
                       , $actorName="", $actorId=""
                       , $exts=null, $extsInt=null )
	{
		$rev = false;

		$datetime  = date( "Y-m-d H:i:s" );
		$timestamp = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		if( Watt_Session::getSession() ){
			//如果是岗位用户  那么还用原来的用户ID  2007-10-24 john
			if(@$_SESSION["shanggang"])
			{
				$yh_id=$_SESSION["old_user_id"];
				$yhs=TpmYonghuPeer::retrieveByPK($yh_id);
				$user_id=$yhs->getYhId();
				$user_name=$yhs->getYhZhanghu();
				$c=new Criteria();
				$c->add(TpmYonghu2juesePeer::YH_ID ,$yh_id);
				$jsids=TpmYonghu2juesePeer::doSelectOne($c);
				if($jsids)
				{
					$js_id=$jsids->getJsId();
					$c=new Criteria();
					$c->add(TpmJuesePeer::JS_ID ,$js_id);
					$jueses=TpmJuesePeer::doSelectOne($c);
					if($jueses)
					{
						$user_js_id=$jueses->getJsId();
						$user_js_mingcheng=$jueses->getJsMingcheng();
					}
				}
				
			}
			else 
			{
				$user_id=Watt_Session::getSession()->getUserId();
				$user_name=Watt_Session::getSession()->getUserName();
				$user_js_id=Watt_Session::getSession()->getRoleId();
				$user_js_mingcheng= Watt_Session::getSession()->getRoleName();
			}
			
			/*$session_id   = Watt_Session::getSession()->getUserId();
			$session_name = Watt_Session::getSession()->getUserName();
			$js_id        = Watt_Session::getSession()->getRoleId();
			$js_mingcheng = Watt_Session::getSession()->getRoleName();
			*/
			$session_id   =$user_id;
			$session_name = $user_name?$user_name:$actorName;
			$js_id        = $user_js_id;
			$js_mingcheng = $user_js_mingcheng;
		}else{
			$session_id   = '';
			$session_name = $actorName;
			$js_id        = '';
			$js_mingcheng = '';
		}

		$app = App::getApp();
		$app->_add( $this->_logName ,
			array(
				'yh_id'         => $session_id,
				'yh_zhanghu'    => chks( $session_name ),
				'js_id'         => $js_id,
				'js_mingcheng'  => chks( $js_mingcheng ),
				'rz_level'      => $level,
				'rz_ip'         => $ip,
				'rz_type'       => chks( $sourceName ),
				'rz_ruanjian'   => chks( $_SERVER["HTTP_USER_AGENT"] ),
				'rz_laiyuan'    => isset( $_SERVER["HTTP_REFERER"] )?chks( $_SERVER["HTTP_REFERER"] ):null,
				'rz_neirong'    => chks( $msg ),
				'rz_dizhi'      => chks( $_SERVER['REQUEST_URI'] ),
				'rz_qita_vchar' => chks( $exts ),
				'rz_qita_int'   => chks( $extsInt ),
				'created_at'    => $timestamp,
			)
		);
		
/*
		$log = new TpmRizhi();

		$log->setYhId( $session_id );
		//用户名
		$log->setYhZhanghu( $session_name );
		$log->setRzLevel( $level );
		$log->setRzIp( $ip );
		$log->setRzRuanjian( $_SERVER["HTTP_USER_AGENT"] );
		$log->setRzType( $sourceName ); //即日志的逻辑标示
		if( isset( $_SERVER["HTTP_REFERER"] ) ){
			$log->setRzLaiyuan( $_SERVER["HTTP_REFERER"] );
		}
		$log->setRzNeirong( $msg );
		$log->setRzDizhi($_SERVER['REQUEST_URI']);
		$log->setRzQitaVchar( $exts );
		$log->setCreatedAt( $timestamp );
		$rev = $log->save();*/

		/*
		rz_id
		yh_id
		yh_zhanghu
		rz_level
		rz_ip
		rz_type
		rz_ruanjian
		rz_laiyuan
		rz_neirong
		rz_dizhi
		rz_qita_vchar
		rz_qita_int
		created_at
		 */
		return $rev;
	}
	
}