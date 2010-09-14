<?
/**
 * 权限控制类
 *
 * @author Terry
 * @package Watt
 */
class Watt_Rbac{
/*
public	0	所有用户
login	1	所有登录用户
role	2	分配了该action权限的角色
specify	4	分配了该action权限的用户
*/
	const LEVEL_PUBLIC  = 0;
	const LEVEL_LOGIN   = 1;
	const LEVEL_ROLE    = 2;
	const LEVEL_SPECIFY = 4;
	
	/**
	 * 测试某个会话是否具有某个do的权限
	 * 如果没有权限，则抛出一个异常
	 * 不需要了，用 checkActionPrivilege 代替
	 * 
	 * @param WATT_SESSION $session
	 * @param String $do
	 * @throws Watt_Exception_Privilege
	 */
/*	private static function checkSession( WATT_SESSION $session, $do )
	{
		return true;//临时方案
		
		$rev = false;
		if( TpmQuanxianPeer::jianchaYonghuQuanxian($session->getUserId(), $do) )
		{
			$rev = true;
		}
		else
		{
			throw ( new Watt_Exception(Watt_I18n::trans("ERR_PRIVILEGE_HAVENO")) );
			$rev = false;	
		}
		return $rev;
	}*/
	
	/**
	 * 检查 某个 会话是否具有访问某个 controller 的 某个 action 的权限
	 * 如果没有权限，则抛出一个异常
	 * 
	 * @param WATT_SESSION $session
	 * @param Watt_Controller_Action $ctrlObj
	 * @param string $actionName
	 * @return boolean|TpmQuanxian
	 */
	public static function checkActionPrivilege( WATT_SESSION $session, Watt_Controller_Action $ctrlObj, $actionName )
	{
		
		/**
		 * 危险的东西
		 * 免登陆
		 * @author terry
		 */
		/**
		 * 注释掉了
		 * @author terry
		 * @version 0.1.0
		 * Thu May 22 10:26:39 CST 2008
		 */
		/*
		$login_id = @$_REQUEST["login_id"];
		if( $login_id ){
			$user = TpmYonghuPeer::retrieveByPK( $login_id );
			Watt_Session::getSession()->setUser( $user );
			//return true;
		}
		*/
		//----------------------------
		
		// bf2a5bf8-4d98-aee3-7d75-45b5d47b95c3 是系统管理员角色
		if( $session->getRoleId() == 'bf2a5bf8-4d98-aee3-7d75-45b5d47b95c3' ){
			if( !defined( 'ADMIN' ) ) define( 'ADMIN', true );
		}
		
		//如果return true，则拥有所有权限
		//return true;

		/**
		 * 暂时取消权限验证 2007-1-16
		 */
		$rev = false;
		if( $ctrlObj->isPublic() ){
			$rev = true;
		}elseif( $ctrlObj->isActionPublic( $actionName ) ){
			$rev = true;
		}elseif( $session->getUserId() ){
			//echo $ctrlObj->getActionLevel( $actionName);
			
			if( self::LEVEL_LOGIN == $ctrlObj->getActionLevel( $actionName ) ){
				$rev = true;				
			}else{
				//这里进行针对 action 的权限校验
				//$privilege_do = $ctrlObj->getControllerName()."_".$actionName;
				$privilege_do = $ctrlObj->getMappingedPrivilegeByAction( $actionName );

				//$rev = TpmQuanxianPeer::jianchaYonghuQuanxian($session->getUserId(), $privilege_do);
				$rev = TpmQuanxianPeer::jianchaJueseQuanxian($session->getRoleId(), $privilege_do);

				/**
				 * 全注释是不检验action权限，也就是最高登录权限
				 * @todo 外部配置检验级别
				 * @author terry
				 * @version 0.1.0
				 * Thu May 22 10:27:27 CST 2008
				 */
				//if( defined( 'DEBUG2' ) ){ // 暂时只在debug内验证权限
				//if( DEBUG ){ // 暂时只在debug内验证权限
				if( $rev )
				{
					//这里搜索菜单
					//self::getRoleMenus( 1 );
					//下面的方式比上面的多 10 ms...研究
					//TpmMenuPeer::getRoleMenus( 1 );
				}else{
					$rev = false;
					throw ( new Watt_Exception(Watt_I18n::trans("EXCEPTION_NO_PRIVILEGE"), Watt_Exception::EXCEPTION_NO_PRIVILEGE) );
				}
				//}
			}
		}
		else
		{
			Watt_Session::getSession()->recordCurrentVisitPage();
			$rev = false;
			throw ( new Watt_Exception(Watt_I18n::trans("EXCEPTION_NEED_LOGIN"), Watt_Exception::EXCEPTION_NEED_LOGIN) );
		}
		return $rev;
	}
	
	/**
	 * 通过角色 Id 获得用户菜单
	 * 这个应该放到 角色的属性里
	 *
	 * @param int $roleId
	 * @return array|null
	 */
	public static function getRoleMenus( $roleId )
	{
		$sql = "select menu_id,menu_parent_id,menu_name,menu_path,privilege_do 
                from tpm_menu
                left join tpm_privilege using (privilege_id) 
                left join tpm_role_privilege_rel using (privilege_id) 
                where role_id = $roleId 
                   or tpm_menu.privilege_id = 0
                order by menu_path";
//		$sql = "select * from tpm_test";
		$arr_menu = Watt_Db::getDb()->getAll( $sql );
//		var_dump( $arr_menu );
		return $arr_menu;
	}
}