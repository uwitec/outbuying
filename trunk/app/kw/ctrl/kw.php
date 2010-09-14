<?
class KwKwController extends Pft_Controller_Action{
	function __construct(){
		$this->_isPublic = true;
	}
	
	function indexAction(){
		$this->hot_keywords = Kw_Keywords::getPeer()->peerGetHotKeywords();
		$this->newest_keywords = Kw_Keywords::getPeer()->peerGetNewestKeywords();
	}
	
	function searchAction(){
		$kwsId = $this->getInputParameter( 'kws_id' );
		if( $kwsId ){
			$theKeywords = Kw_Keywords::getPeer()->retrieveByPk( $kwsId );
		}else{
			$s = $this->getInputParameter( 's' );
			$theKeywords = Kw_Keywords::getPeer()->searchKeywords( $s );
		}

		if( $theKeywords ){
			$this->kw_keywords  = $theKeywords->toArray();
			$this->rel_contents = $theKeywords->getRelContents();			
		}else{
			$this->kw_keywords  = null;
			$this->rel_contents = null;
		}
	}
	
	function addContentAction(){
		$kwsId = $this->getInputParameter( 'kws_id' );
		$this->kws_id = $kwsId;
		
		$op = $this->getInputParameter('op');
		if( 1 == $op ){
			$aContent = new Kw_Content();
			$aContent->autoGetRequestVar( 'kws_id,ct_content,ct_adduser,ct_email' );
			if( $aContent->save() ){
				$this->addTip( 'OK', "?do=kw_kw_search&kws_id=".$kwsId );
			}
		}
	}
}
?>