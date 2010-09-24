<?
class PsUserController extends Pft_Controller_Action{
	function __construct(){
		$this->_isPublic = true;
	}
	
	function indexAction(){

	}
	
	function loginAction(){
		$op = $this->getInputParameter('op');
		if( $op ){
			$uname = r('username');
			$pwd = r('password');
			Ofh_Ps_User::checkUserLogin( $uname, $pwd );
		}
	}
	/*
	*options: 用户注册
	*param:  
	*autor:df      
	*date:Thu Sep 23 20:23:08 CST 2010
	*/
	function registerAction()
	{
		$users=new Yd_Users();
		$users->autoGetRequestVar('u_id,u_name,u_nickname,u_pwd,u_sex,u_phone,u_mobile,u_address,u_email,u_last_login,u_logins,u_main_role,created_at,updated_at,is_del');
		if($this->getInputParameter("op"))
		{
			if($users->save())
			{
				
			}
		}
	}
}
?>