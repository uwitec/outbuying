<?
/*
*options: 术语表(标签)
*param:  
*author:df      
*date:Sat Sep 18 18:14:46 CST 2010
*/
class Yd_TermRef extends Pft_Om_BaseObject{
	protected $_tableName   = 'term_ref';
	protected $_pkName      = 'ref_id';
	protected $_autoincName = 'ref_id';
	
	protected static $_className   = 'Yd_TermRef';

	protected $_fields = array(
	                    'ref_id',
						'term_id',
						'obj_type',
						'obj_id',
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