<?
class Pft_Om_BasePeer{
	/**
	 * Enter description here...
	 *
	 * @param mix $pk
	 * @return Pft_Om_BaseObject
	 */
	public static function retrieveByPk( $tableName, $pk ){
		$data = self::getDataByPk( $tableName, $pk );
		if( $data ){
			$aBaseObject = new Pft_Om_BaseObject();
			//$aBaseObject = new self::$_className;
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
	public static function insert( $tableName, $data ){
		if( !(is_array( $data ) && count( $data )) ){
			return false;
		}
		
		foreach ( $data as $key => $value ) {
			$arrKey[] = $key;
			$arrValue[] = "'".chks($value)."'";
		}
		$sql = "insert into ".$tableName."(".implode(',', $arrKey ).") values(".implode( ',', $arrValue ).")";
		$db = Pft_Dbx::getDbx();
		return $db->execute( $sql );
	}
	
	public static function updateByPk( $tableName, $pkName, $pk, $data ){
		if( !(is_array( $data ) && count( $data )) ){
			return false;
		}
	
		foreach ( $data as $key => $value ) {
			$strUpdate[] = $key.'='."'".chks($value)."'";
		}
		$sql = "update ".$tableName." set ".implode(',', $strUpdate )." where ".$pkName."='".chks($pk)."'";
		$db = Pft_Dbx::getDbx();
		return $db->execute( $sql );
	}
	
	public static function updateByCondition( $tableName, $condition, $data ){
		if( !(is_array( $data ) && count( $data )) ){
			return false;
		}
	
		foreach ( $data as $key => $value ) {
			$strUpdate[] = $key.'='."'".chks($value)."'";
		}
		$sql = "update ".$tableName." set ".implode(',', $strUpdate ).($condition?" where $condition":'');
		$db = Pft_Dbx::getDbx();
		return $db->execute( $sql );
	}
	
	public static function deleteByPk( $tableName, $pkName, $pk ){
		$sql = "delete from ".$tableName." where ".$pkName."='".chks($pk)."'";
		$db = Pft_Dbx::getDbx();
		return $db->execute( $sql );
	}

	public static function deleteByPks( $tableName, $pkName, $pks ){
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
			$sql = "delete from ".$tableName." where ".$pkName." in (".$strPks.")";
			$db = Pft_Dbx::getDbx();
			return $db->execute( $sql );
		}else{
			return false;
		}
	}
	
	public static function getDataByPk( $tableName, $pkName, $pk ){
		$sql = "select * from ".$tableName." where ".$pkName."='".chks($pk)."'";
		$db = Pft_Dbx::getDbx();
		return $db->getRow( $sql );
	}
	
	public static function doSelectByCondition( $tableName, $condition='', $limit=0, $offset=0, $orderBySql='' ){
		if( $condition ){
			$sql = "select * from ".$tableName
			      ." where $condition "
			      .$orderBySql
			      .self::_formatLimit( $limit, $offset );
			$db = Pft_Dbx::getDbx();
			return $db->getAll( $sql );
		}else{
			return self::getAll( $limit, $offset, $orderBySql );
		}
	}
	
	public static function doCountByCondition( $tableName, $condition='' ){
		if( $condition ){
			$sql = "select count(*) from ".$tableName." where $condition ";
			$db = Pft_Dbx::getDbx();
			return $db->getOne( $sql );
		}else{
			return self::getAllCount( $tableName );
		}
	}
	
	public static function getAll( $tableName, $limit=0, $offset=0, $orderBySql='' ){
		$sql = "select * from ".$tableName
		      .$orderBySql
		      .self::_formatLimit( $limit, $offset );
		$db = Pft_Dbx::getDbx();
		return $db->getAll( $sql );
	}
	
	public static function getAllCount( $tableName ){
		$sql = "select count(*) from ".$tableName;
		$db = Pft_Dbx::getDbx();
		return $db->getOne( $sql );
	}
	
	private static function _formatLimit( $limit=0, $offset=0 ){
		if( $limit ){
			if( $offset < 0 )$offset = 0;
			return " limit $offset, $limit";
		}else{
			return '';
		}
	}
}
?>