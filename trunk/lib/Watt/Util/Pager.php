<?
/**
 * Pager类
 *
 * @author Yan
 * @package Watt_Util
 */

class Watt_Util_Pager
{
	
	const PAGER_VAR_TOTAL      = "total";
	const PAGER_VAR_PAGE_NUM   = "pn";//"page_num";
	const PAGER_VAR_PAGE_COUNT = "page_count";
	const PAGER_VAR_PAGE_START = "page_start";
	const PAGER_VAR_PAGE_END   = "page_end";
	const PAGER_VAR_PAGE_SIZE  = "pz";//"page_size";
	const PAGER_VAR_PAGE_GOTONUM  = "pager_goto_num";
	
	private $_total     = 0;
	private $_pageSize  = 15;
	private $_pageNum   = 1;
	private $_pageCount = 0;
	/*
	private $_pageStart = 0;
	private $_pageEnd   = 0;
	*/
	
	function __construct(){
		if( isset( $_REQUEST[self::PAGER_VAR_PAGE_NUM] ) ){
			$this->setPageNum( $_REQUEST[self::PAGER_VAR_PAGE_NUM] );
		}
		if( isset( $_REQUEST[self::PAGER_VAR_PAGE_SIZE] ) && ($_REQUEST[self::PAGER_VAR_PAGE_SIZE]) != "" ){
			$this->setPageSize( $_REQUEST[self::PAGER_VAR_PAGE_SIZE] );
		}
		if( isset( $_REQUEST[self::PAGER_VAR_TOTAL] ) && ($_REQUEST[self::PAGER_VAR_TOTAL]) != "" ){
			$this->setTotal( $_REQUEST[self::PAGER_VAR_TOTAL] );
		}
	}
	
	/**
	 * 
	 *
	 * @param int $total
	 */
	public function setTotal( $total ){
		$this->_total = ($total < 0)?0:intval( $total );
		$this->_reCalcPageCount();
	}
	
	public function getTotal(){
		return $this->_total;
	}
	
	public function setPageNum( $pageNum ){
		if( $pageNum < 1 ){
			$this->_pageNum = 1;
		}else {
			$this->_pageNum = intval( $pageNum );
		}
	}
	
	public function getPageNum(){
		//return $this->_pageNum;

		//这是为了解决 开始页大，然后页小，导致总是第1页的问题
		return( $this->_pageNum > $this->_pageCount )?$this->_pageCount:$this->_pageNum;
	}
	
	public function setPageSize( $pageSize ){
		$this->_pageSize = ( $pageSize < 1 )?1:intval($pageSize);	
		$this->_reCalcPageCount();
	}
	
	public function getPageSize(){
		return $this->_pageSize;
	}
	
	public function getPageCount(){
		return $this->_pageCount;
	}

	public function getPageStart(){
		return max( ($this->getPageNum() - 1) * $this->_pageSize + 1, 0);
	}
	
	public function getPageEnd(){
		$pageEnd = $this->getPageNum() * $this->_pageSize;
		return ($pageEnd > $this->_total)?$this->_total:$pageEnd;
	}

	/**
	 * @return array
	 *
	 */
	public function toArray(){
		$rev = array();
		$rev[self::PAGER_VAR_TOTAL]      = $this->getTotal();
		$rev[self::PAGER_VAR_PAGE_NUM]   = $this->getPageNum();
		$rev[self::PAGER_VAR_PAGE_COUNT] = $this->getPageCount();
		$rev[self::PAGER_VAR_PAGE_START] = $this->getPageStart();
		$rev[self::PAGER_VAR_PAGE_END]   = $this->getPageEnd();
		$rev[self::PAGER_VAR_PAGE_SIZE]  = $this->getPageSize();
		return $rev;
	}
	
	private function _reCalcPageCount(){
		$this->_pageCount = intval(( $this->_total + $this->_pageSize - 1)/ $this->_pageSize );
		/**
		 * pageNum总是记录用户设置的页码
		if( $this->_pageNum > $this->_pageCount ){
			$this->_pageNum = $this->_pageCount;
		}
		**/
	}
	
	function __toString(){
		return "page {$this->_pageNum}/{$this->_pageCount} | {$this->_pageSize}/page | {$this->getPageStart()} - {$this->getPageEnd()} of {$this->_total}";
	}
}