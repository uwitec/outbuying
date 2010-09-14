<?
class PsUserController extends Pft_Controller_Action{
	function __construct(){
		$this->_isPublic = true;
	}
	
	function indexAction(){

	}
	
	function registerAction(){
		
	}
	
	function loginAction(){
		$op = $this->getInputParameter('op');
		if( $op ){
			$uname = r('username');
			$pwd = r('password');
			Ofh_Ps_User::checkUserLogin( $uname, $pwd );
		}
	}
}
?>