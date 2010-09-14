<?
class ServiceRestController extends Pft_Controller_Action{
	function __construct(){
		$this->_isPublic = true;
	}
	
	function indexAction(){
		
		$this->_needView = false;
	}
	
	/**
	 * 
	 * @author y31
	 * Tue Dec 11 22:40:45 CST 2007
	 */
	function provideAction(){
		$dbName = $this->getInputParameter( 'db_name' );
		$param  = $this->getInputParameter( 'param' );
		$method = $this->getInputParameter( 'method' );
		
		if( $dbName ){
			$sql = "select * from $dbName";
			$this->$dbName = Pft_Db::getDb()->getAll( $sql );
		}
	}
}
?>