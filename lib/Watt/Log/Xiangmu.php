<?
//include_once('db.php');
class Watt_Log_Xiangmu extends Watt_Log_Db {
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
			$session_name = $user_name;
			$js_id        = $user_js_id;
			$js_mingcheng = $user_js_mingcheng;
		}else{
			$session_id   = '';
			$session_name = '';
			$js_id        = '';
			$js_mingcheng = '';
		}
		
		//通过项目id或订单id获取项目编号订单编号项目执行者
		$xmbddbxmzxzid = TpmDingdanPeer::getXmBianhaoDdBianhaoByXmidorDdid($exts);
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
				'rz_qita_int'   => $xmbddbxmzxzid['bm_id'],
				'created_at'    => $timestamp,
				'xm_bianhao'	=> $xmbddbxmzxzid['xm_bianhao'],
				'dd_bianhao'	=> $xmbddbxmzxzid['dd_bianhao'],
				'xm_zhixingzhe_id'	=> $xmbddbxmzxzid['xm_zhixingzhe_id'],
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
?>