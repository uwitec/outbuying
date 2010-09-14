<?
class KwContentController extends Pft_Controller_Action{
	function __construct(){
		$this->_isPublic = true;
	}
	
	function indexAction(){
		
	}
	
	/**
	 * 增加内容
	 * @author y31
	 * Sat Jan 12 23:24:14 CST 2008
	 */
	function addContentAction(){
		$kwsId = $this->getInputParameter( 'kws_id' );
		$this->kws_id = $kwsId;
		
		$op = $this->getInputParameter('op');
		if( 1 == $op ){
			$aContent = new Kw_Content();
			$aContent->autoGetRequestVar( 'kws_id,ct_content,ct_adduser,ct_email' );
			if( $aContent->save() ){
				//$this->addTip( 'OK', "?do=kw_kw_search&kws_id=".$kwsId );
				$_REQUEST['kws_id'] = $kwsId;
				$this->setNeedView( false );
				$this->goToDo('kw_kw_search');
			}else{
				$this->addTip('Fail');
			}
		}
	}
	
	/**
	 * 打分
	 * @author y31
	 * Sat Jan 12 23:24:05 CST 2008
	 */
	function markAction(){
		$mark = $this->getInputParameter( 'mk' );
		/**
		 * 防止恶意加减分
		 * @author y31
		 * Tue Feb 05 22:09:09 CST 2008
		 */
		switch ( $mark ){
			case 3:
				$addScore = 3;
				break;
			case 1:
				$addScore = 1;
				break;
			case -1:
				$addScore = -1;
				break;
			case -3:
				$addScore = -3;
				break;
			default:
				$addScore = 0;
		}
		if( $addScore ){
			$ctId = $this->getInputParameter( 'ct_id' );
			$theContent = Kw_Content::getPeer()->retrieveByPk( $ctId );
			if( $theContent ){
				$rev = $theContent->addScore( $addScore );
			}else{
				$rev = 0;
			}
		}else{
			$rev = 1;
		}
		if( $rev ){
			$this->addTip( 'OK', "javascript:history.go(-1)" );
		}else{
			$this->addTip( 'Fail', "javascript:history.go(-1)" );
		}
	}
}
?>