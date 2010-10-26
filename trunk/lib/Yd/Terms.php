<?
/*
*options: 术语表(标签)
*param:  
*author:df      
*date:Sat Sep 18 18:14:46 CST 2010
*/
class Yd_Terms extends Pft_Om_BaseObject{
	protected $_tableName   = 'terms';
	protected $_pkName      = 'term_id';
	protected $_autoincName = 'term_id';
	
	protected static $_className   = 'Yd_Terms';

	protected $_fields = array(
						'term_id',
						'term_name',
						'term_slug',
						'term_group',
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