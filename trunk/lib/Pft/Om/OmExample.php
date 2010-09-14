<?php
/**
 * 注意修改peer的返回值类型
 */
class Kw_Keywords extends Pft_Om_BaseObject{
	protected $_tableName   = 'kw_keywords';
	protected $_pkName      = 'kws_id';
	protected $_autoincName = 'kws_id';
	
	protected static $_className   = 'Kw_Keywords';

	protected $_fields = array(
						'kws_id',
						'kws_words',
						'kws_hash',
						'kws_stimes',
						'kws_ct_count',
						'created_at',
						'updated_at',
					);
/*
Name	Code	Data Type	Primary	Foreign Key	Mandatory
关键字组ID	kws_id	int	TRUE	FALSE	TRUE
关键字组内容	kws_words	varchar(128)	FALSE	FALSE	TRUE
关键字组Hash	kws_hash	char(32)	FALSE	FALSE	FALSE
查询次数	kws_stimes	int	FALSE	FALSE	FALSE
相关内容数量	kws_ct_count	int	FALSE	FALSE	FALSE
created_at	created_at	int(11)	FALSE	FALSE	FALSE
updated_at	updated_at	int(11)	FALSE	FALSE	FALSE
*/
	//========= 以下方法所有子类都复制一份 ============
	
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

	//=======================================
}
?>