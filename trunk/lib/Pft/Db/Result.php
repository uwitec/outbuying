<?
/**
 * 数据库查询结果集
 *
 */
Class Watt_Db_Result{
	const FETCH_ASSOC = 1;
	const FETCH_NUM   = 2;
	const FETCH_BOTH  = 3;

	private $_result;
	
	public function fetchArray( $fetch_type = FETCH_BOTH ){
		if( is_resource( $this->_result ) ){
			return mysql_fetch_array( $this->_result, $fetch_type );
		}
		return false;
	}

	public function free(){
		if( is_resource( $this->_result ) ){
			mysql_free_result( $this->_result );
		}
	}
	
	function __destruct(){
		$this->free();
	}
}
