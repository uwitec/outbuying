<?
/**
 * Result Objet for fetch mode.
 * When you need use mass data, please use this object throw Watt_Db()->getDb()->getResult().
 */
class Watt_Db_Result{
	const FETCH_ASSOC = 1;
	const FETCH_NUM   = 2;
	const FETCH_BOTH  = 3;

	private $_result;
	
	public function setResult( $v ){$this->_result = $v;}
	public function getResult(){return $this->_result;}
	
	/**
	 * @return array
	 */
	public function fetchRow(){
		//$result = $this->_query( $sql );

		if( $this->_result ){
			return mysql_fetch_assoc( $this->_result );
		}else{
			return null;
		}
	}

	public function fetchArray( $fetch_type = FETCH_BOTH ){
		if( is_resource( $this->_result ) ){
			return mysql_fetch_array( $this->_result, $fetch_type );
		}
		return false;
	}

	/**
	 * @return boolean
	 */
	public function free(){
		if( $this->_result ){
			return mysql_free_result( $this->_result );
		}else{
			return true;
		}
	}
	
	/**
	 * @author terry
	 * Wed Jan 07 09:28:35 CST 2009
	 */
	public function __destruct(){
		$this->free();
	}
}
