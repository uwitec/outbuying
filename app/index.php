<?
class IndexController extends Pft_Controller_Action{
	function __construct(){
		$this->_isPublic = true;
	}
	
	function indexAction(){
		//$this->goToDo( "kw_kw_index" );
		/*
		if( $session = Pft_Session::getSession()->getUserId() ){
			$this->goToDo( "main_home" );
		}else{
			$this->goToDo( "login" );
		}
		*/
		//$this->_needView = false;
		//$rev = Pft_Db::getDb()->getAll("show tables;");
		//var_dump($rev);
	}
}