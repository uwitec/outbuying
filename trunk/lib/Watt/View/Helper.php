<?
/**
 * Hepler 的管理类
 *
 * @author Terry
 * @package Watt_View
 */

class Watt_View_Helper
{
	private static $_helpers = array();
	
	/**
	 * Types
	 * 
From adodb
C: Character fields that should be shown in a <input type="text"> tag. 
X: Clob (character large objects), or large text fields that should be shown in a <textarea> 
D: Date field 
T: Timestamp field 
L: Logical field (boolean or bit-field) 
N: Numeric field. Includes decimal, numeric, floating point, and real. 
I:  Integer field. 
R: Counter or Autoincrement field. Must be numeric. 
B: Blob, or binary large objects. 
	 * 
	 * 
	 */
	
	/**
	 * 通过 helper 的名称（类名称）建立一个 element
	 * 
	 * @param string $vartype
	 * @param string $name
	 * @param string $value
	 * @param mix $attribs
	 * @return string XHTML
	 */
	public static function buildElmentByVartype( $vartype, $name, $value = null, $attribs = null )
	{
		if( !is_array($attribs) || (is_array( $attribs ) && !key_exists( "id", $attribs )) )
		{	
			//默认为所有的 element 增加 id
			$attribs["id"] = $name;
		}
		
		switch ( $vartype )
		{
			case "D":
				//$helper = new Watt_View_Helper_DateSelector();
				$helper = self::_getHelper( "Watt_View_Helper_DateSelector" );
				$helper->setShowTimes(false);
				return $helper->build( $name, $value, $attribs );
				break;
			case "T":
				//$helper = new Watt_View_Helper_DateSelector();
				$helper = self::_getHelper( "Watt_View_Helper_DateSelector" );
				$helper->setShowTimes(true);
				return $helper->build( $name, $value, $attribs );
				break;
			case "PERSON":
				/**
				 * param 属性可以指定显示参数，如 role=SR 只显示销售角色
				 * @author terry
				 * Fri Aug 07 19:55:35 CST 2009
				 */
				//$helper = new Watt_View_Helper_DateSelector();
				$helper = self::_getHelper( "Watt_View_Helper_PersonSelector" );
				return $helper->build( $name, $value, $attribs );
				break;
			case "C":
			default:
				//$helper = new Zend_View_Helper_FormText();
				$helper = self::_getHelper( "Zend_View_Helper_FormText" );
				return $helper->formText( $name, $value, $attribs );
		}
	}
	
	private static function _getHelper( $helperClassName )
	{
		if( !isset( self::$_helpers[$helperClassName] ) )
		{
			 self::$_helpers[$helperClassName] = new $helperClassName();
		}
		return self::$_helpers[$helperClassName];
	}
}