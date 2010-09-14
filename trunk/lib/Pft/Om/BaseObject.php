<?
/**
 * 简单Om对象
 * 
 * @author y31
 * @version 0.8.2
 * lastupdate Sat Aug 02 22:48:56 CST 2008
 */
class Pft_Om_BaseObject{
	
	protected $_tableName   = '';
	protected $_pkName      = '';
	protected $_autoincName = '';
	
	protected static $_className = 'Pft_Om_BaseObject';
	protected $_fields = array();
	
	//-- 以上需在子类中重新定义 --

	protected $_data  = array();	//存储核心数据
	protected $_isNew = true;		//是否是新增记录
	protected $_modifiedFileds = array();
	
	/**
	 *
	 * @var Pft_Db
	 */
	protected $_db = null;

	public function __construct(){
		$this->_db = Pft_Db::getDb();

		//初始化数据存储数组
		foreach ( $this->_fields as $field ){
			$this->_data[$field] = null;
		}
	}
	
	/**
	 * 设置是否是新增对象
	 *
	 * @param Boolean $v
	 */
	public function setIsNew( $v ){
		$this->_isNew = $v;
	}

	public function isNew(){
		return $this->_isNew;
	}

	public function autoGetRequestVar( $fieldList='' ){
		if( $fieldList ){
			$array = r( $fieldList, false );
		}else{
			$array = r( $this->_fields, false );
		}
		$this->populateFromArray( $array );
	}

	/**
	 * 通过已有数组给核心对象赋值
	 * @param array $arr
	 */
	public function setDataFromArray( $arrData ){
		return $this->populateFromArray( $arrData );
	}
	
	/**
	 * 通过已有数组给组装对象
	 * @param array $arr
	 */
	public function populateFromArray( $arrData ){
		if( is_array( $arrData ) ){
			foreach ( $this->_fields as $field ){
				if( key_exists( $field, $arrData ) ){
					//$this->_data[$field] = $arrData[$field];
					$this->$field = $arrData[$field];
				}
			}
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 直接设置核心数据，只允许 peer 设置
	 *
	 * @param array $data
	 * @return boolean
	 */
	protected function setCoreData( $data ){
		if( is_array( $data ) ){
			$this->_data = $data;
			return true;
		}else{
			return false;
		}
	}

	/**
	 * @return array
	 */
	public function toArray(){
		return $this->_data;
	}
	
	public function clearModifiedFields( $fields = null ){
		if( $fields ){
			if( !is_array( $fields ) ){
				$fields = explode( ',', $fields );
			}
			foreach( $fields as $field ){
				if( $this->isFieldModified($field) ){
					unset( $this->_modifiedFileds[$field] );
				}
			}
		}else{
			$this->_modifiedFileds = array();
		}
	}
	
	public function isFieldModified( $fieldName ){
		return key_exists( $fieldName, $this->_modifiedFileds );
	}
	
	public function __set( $nm, $val ){
		if( $this->_isFieldName( $nm ) ){
			if( $val != @$this->_data[$nm] ){
				$this->_data[$nm] = $val;
				$this->_modifiedFileds[$nm] = 1;
			}
		}else{
			throw ( new Exception('Cannot set Property ['.$nm.'] that not exist!') );
		}
	}
	
	public function __get( $nm ){
		if( in_array( $nm, $this->_fields ) ){
			return @$this->_data[$nm];
		}else{
			throw ( new Exception('Cannot get Property ['.$nm.'] that not exist!') );
		}
	}

	/**
	 * @return int affectrows
	 */
	public function save(){
		if( $this->_isFieldName( 'updated_at' ) ){
			$this->updated_at = time();
		}	
			
		if( $this->_isNew ){
			if( $this->_isFieldName( 'created_at' ) ){
				$this->created_at = time();
			}
			
			$rev = $this->insert( $this->_data, $this->_modifiedFileds );
			if( $rev && $this->_autoincName ){
				//写入自增ID的值
				$this->_data[$this->_autoincName] = $this->_db->Insert_ID();
			}
			$this->setIsNew( false );
		}else{
			$rev = $this->updateByPk( $this->_data[$this->_pkName], $this->_data, $this->_modifiedFileds );
		}
		
		if( $rev ){
			$this->clearModifiedFields();
		}
		return $rev;
	}
	
	private function _isFieldName( $fieldName ){
		return in_array( $fieldName, $this->_fields );
	}
	
	//****************
	// 以下为 Peer 方法
	//----------------
	
	private static $_peerPool = array();	//PeerObject Pool
	
	/**
	 * @return Pft_Om_BaseObject
	 */
	public static function getPeer( $baseObjectClassName ){
		if( !key_exists( $baseObjectClassName, self::$_peerPool ) ){
			self::$_peerPool[$baseObjectClassName] = new $baseObjectClassName();
		}
		return self::$_peerPool[$baseObjectClassName];
	}
	
	/**
	 * @param mix $pk
	 * @return Pft_Om_BaseObject
	 */
	public function retrieveByPk( $pk ){
		$data = $this->getDataByPk( $pk );
		if( $data ){
			$className =get_class( $this );
			//$aBaseObject = new $this->_className();
			$aBaseObject = new $className();
			$aBaseObject->setCoreData( $data );
			$aBaseObject->setIsNew( false );
			return $aBaseObject;
		}else{
			return null;
		}
	}

	/**
	 *
	 * @param array $data
	 * @return 0|1
	 */	
	public function insert( $data, $modifiedFields=null ){
		if( !(is_array( $data ) && count( $data )) ){
			return false;
		}
		
		foreach ( $data as $key => $value ) {
			if( !$modifiedFields || key_exists( $key, $modifiedFields ) ){
				$arrKey[] = $key;
				$arrValue[] = "'".chks($value)."'";				
			}
		}
		
		if( isset( $arrKey ) ){
			$sql = "insert into ".$this->_tableName."(".implode(',', $arrKey ).") values(".implode( ',', $arrValue ).")";
			return $this->_db->execute( $sql );			
		}else{
			return 0;	
		}
	}
	
	public function updateByPk( $pk, $data, $modifiedFields=null ){
		if( !(is_array( $data ) && count( $data )) ){
			return false;
		}
	
		foreach ( $data as $key => $value ) {
			if( !$modifiedFields || key_exists( $key, $modifiedFields ) ){
				if( is_null( $value ) ){
					$strUpdate[] = $key.'='."null";
				}else{
					$strUpdate[] = $key.'='."'".chks($value)."'";
				}
			}
		}
		
		if( isset( $strUpdate ) ){
			$sql = "update ".$this->_tableName." set ".implode(',', $strUpdate )." where ".$this->_pkName."='".chks($pk)."'";
			return $this->_db->execute( $sql );
		}else{
			return 0;
		}
	}
	
	public function updateByCondition( $condition, $data ){
		if( !(is_array( $data ) && count( $data )) ){
			return false;
		}
	
		foreach ( $data as $key => $value ) {
			if( is_null( $value ) ){
				$strUpdate[] = $key.'='."null";
			}else{
				$strUpdate[] = $key.'='."'".chks($value)."'";				
			}
		}
		$sql = "update ".$this->_tableName." set ".implode(',', $strUpdate ).($condition?" where $condition":'');
		return $this->_db->execute( $sql );
	}
	
	public function deleteByPk( $pk ){
		$sql = "delete from ".$this->_tableName." where ".$this->_pkName."='".chks($pk)."'";
		return $this->_db->execute( $sql );
	}

	public function deleteByPks( $pks ){
		if( is_array( $pks ) ){
			$newPks = array();
			foreach ( $pks as $pk ){
				$newPks[] = "'".chks($pk)."'";
			}
			$strPks = implode( ',', $newPks );
		}else{
			$strPks = "'".chks($pks)."'";
		}
		
		if( $strPks ){
			$sql = "delete from ".$this->_tableName." where ".$this->_pkName." in (".$strPks.")";
			return $this->_db->execute( $sql );
		}else{
			return false;
		}
	}
	
	public function getDataByPk( $pk ){
		$sql = "select * from ".$this->_tableName." where ".$this->_pkName."='".chks($pk)."'";
		return $this->_db->getRow( $sql );
	}
	
	public function doSelectByCondition( $condition='', $limit=0, $offset=0, $orderBySql='', $fields='*' ){
		if( $condition ){
			$sql = "select ".$fields." from ".$this->_tableName
			      ." where $condition "
			      .$orderBySql
			      .$this->_formatLimit( $limit, $offset );
			return $this->_db->getAll( $sql );
		}else{
			return $this->getAll( $limit, $offset, $orderBySql, $fields );
		}
	}
	
	public function doCountByCondition( $condition='' ){
		if( $condition ){
			$sql = "select count(*) from ".$this->_tableName." where $condition ";
			return $this->_db->getOne( $sql );
		}else{
			return $this->getAllCount();
		}
	}
	
	public function getAll( $limit=0, $offset=0, $orderBySql='', $fields='*' ){
		$sql = "select ".$fields." from ".$this->_tableName
		      .$orderBySql
		      .$this->_formatLimit( $limit, $offset );
		return $this->_db->getAll( $sql );
	}
	
	public function getAllCount(){
		$sql = "select count(*) from ".$this->_tableName;
		return $this->_db->getOne( $sql );
	}
	
	private function _formatLimit( $limit=0, $offset=0 ){
		if( $limit ){
			if( $offset < 0 )$offset = 0;
			return " limit $offset, $limit";
		}else{
			return '';
		}
	}
}
?>