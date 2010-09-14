<?
/**
 * ID管理器
 * 为全系统提供统一的ID管理，ID生成
 * 
 * @version 0.0.1
 * @author terry
 * Wed Jun 06 09:09:53 CST 2007
 */

class Pft_Util_IdManager{
	
	private static $_managerPool = array();
	
	/**
	 * @return Pft_Util_IdManager
	 */
	public static function factory( $managerName = null ){
		if( !$managerName ){
			$managerName = 'tpm_id_base';
		}
		if( !key_exists( $managerName, self::$_managerPool ) ){
			self::$_managerPool[ $managerName ] = new Pft_Util_IdManager( $managerName );
		}
		return self::$_managerPool[ $managerName ];
	}

	private function __construct( $managerName = null ){
		$this->_managerName = $managerName;
		$this->_db = Pft_Db::getDb();	// 原为getDbx() 导致 Pft_db->_connection 访问出错，因此更改为getDb()
	}

	/**
	 * @var Pft_Dbx
	 */
	private $_db;
	
	private $_managerName;		//在MySql中，为对应的数据表的名称
	
	private $_minSysId = 0;		//最小的SysId 0 为不限制
	private $_maxSysId = 0;		//最大的SysId 0 为不限制

	/**
	 * array(
	 * 		'sys_id' => 1,
	 * 		'gu_id' => 'c08d6299-14fe-8dcd-c908-461f78ba16dd',
	 * )
	 *
	 * @return array
	 */
	public function getNewIdGroup(){
		$aNewGuId = Pft_Util_Utils::getGuId();
		$rev = array();
		if( $aNewGuId ){
			$sql = " insert into {$this->_managerName}(gu_id) values ('".$aNewGuId."')";
			if( $this->_db->execute( $sql ) ){
				$theNewSysId = $this->_db->Insert_ID();
				$rev['sys_id'] = $theNewSysId;
				$rev['gu_id']  = $aNewGuId;
			}
		}
		return $rev;
	}
	
	/**
	 * 获得新的系统ID
	 *
	 * @return SYS_ID
	 */
	public function getNewSysId(){
		$rev = null;
		$theNewIdGroup = $this->getNewIdGroup();
		if( isset( $theNewIdGroup['sys_id'] ) ){
			$rev = $theNewIdGroup['sys_id'];
		}
		return $rev;
	}

	/**
	 * 获得新的GU_ID
	 *
	 * @return GU_ID
	 */
	public function getNewGuId(){
		$rev = null;
		$theNewIdGroup = $this->getNewIdGroup();
		if( isset( $theNewIdGroup['gu_id'] ) ){
			$rev = $theNewIdGroup['gu_id'];
		}
		return $rev;
	}
	
	/**
	 * @param SYS_ID $theSysId
	 * @return GU_ID
	 */
	public function getGuIdBySysId( $theSysId ){
		$sql = "select gu_id from {$this->_managerName} where sys_id = '".$theSysId."'";
		return $this->_db->getOne( $sql );
	}
	
	/**
	 * @param GU_ID $theGuId
	 * @return SYS_ID
	 */
	public function getSysIdByGuId( $theGuId ){
		$sql = "select sys_id from {$this->_managerName} where gu_id = '".$theGuId."'";
		return $this->_db->getOne( $sql );
	}
	
	/**
	 * 创建ID存储的数据表
	 *
	 * @param String $managerName
	 */
	private function _createIdManagerTable( $managerName ){
		//MySql
		$sql = "
				create table {$managerName}
				(
				   sys_id               int(11) not null auto_increment,
				   gu_id                CHAR(36),
				   primary key (sys_id)
				)
				type = MYISAM;
				
				create unique index Index_{$managerName}_GU_ID on {$managerName}
				(
				   gu_id
				);
				";
		$this->_db->execute( $sql );
	}
}
?>