<?
class ServiceServicerController extends Pft_Controller_Action{
	function __construct(){
		$this->_isPublic = true;
	}
	
	function indexAction(){
		
		$this->_needView = false;
	}
	
	function provideAction(){
		$this->test = 'Just Test';
	}
}
?>