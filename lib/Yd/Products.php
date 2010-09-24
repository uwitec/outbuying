<?
/*
*options: 产品表对应的类
*param:  
*author:df      
*date:Sat Sep 18 18:14:46 CST 2010
*/
class Yd_Products extends Pft_Om_BaseObject{
	protected $_tableName   = 'products';
	protected $_pkName      = 'p_id';
	protected $_autoincName = 'p_id';
	
	protected static $_className   = 'Yd_Products';

	protected $_fields = array(
						'p_id',
						'k_id',
						'p_name',
						'p_price',
						'p_info',
						'p_img_link',
						'p_unit',
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