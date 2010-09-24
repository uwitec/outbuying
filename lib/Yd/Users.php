<?
/*
*options: 产品分类表对应的类
*param:  
*author:df      
*date:Sat Sep 18 18:14:46 CST 2010
*/
class Yd_Users extends Pft_Om_BaseObject{
	protected $_tableName   = 'users';
	protected $_pkName      = 'u_id';
	protected $_autoincName = 'u_id';
	
	protected static $_className   = 'Yd_Users';

	protected $_fields = array(
						'u_id',
						'u_name',
						'u_nickname',
						'u_pwd',
						'u_sex',
						'u_phone',
						'u_mobile',
						'u_address',
						'u_email',
						'u_last_login',
						'u_logins',
						'u_main_role',
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