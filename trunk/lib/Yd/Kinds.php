<?
/*
*options: 产品分类表对应的类
*param:  
*author:df      
*date:Sat Sep 18 18:14:46 CST 2010
*/
class Yd_Kinds extends Pft_Om_BaseObject{
	protected $_tableName   = 'kinds';
	protected $_pkName      = 'k_id';
	protected $_autoincName = 'k_id';
	
	protected static $_className   = 'Yd_Kinds';

	protected $_fields = array(
						'k_id',
						'k_parent_id',
						'k_root_id',
						'k_name',
						'k_info',
						'created_at',
						'updated_at',
						'is_del',
					);
    /**
	 * @return Kw_Keywords
	 */
	public static function getPeer(){
		return parent::getPeer( self::$_className );
	}
	
	/**
	 * @param string $pk
	 * @return Kw_Keywords
	 */
	public function retrieveByPk( $pk ){
		return parent::retrieveByPk( $pk );
	}
}
?>