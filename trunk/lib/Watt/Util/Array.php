<?
/**
 * 数组工具集
 *
 * @author Terry
 * @package Watt_Util
 * @version 1.0.2
 */
class Watt_Util_Array{
	/**
	 * 递归的将一个变量变为数组
	 * 关键是让数组中的对象变成数组
	 * 对于对象，如果有 toArray 方法，用 toArray 方法返回
	 * 如没有 toArray 方法 则返回 类名
	 * 
	 * @return array
	 */
	public static function toArray( $var )
	{
		if( is_array( $var ) ){
			foreach ( $var as $key => $val ){
				$var[$key] = self::toArray( $val );
			}
		}elseif( is_object( $var ) ){
			if( $var instanceof SimpleXMLElement  ){
				return self::XmlToArray( $var );
			}elseif( method_exists( $var, "toArrayTpm" ) ){	//优先使用 to ArrayTpm
				$var = $var->toArrayTpm();
			}elseif( method_exists( $var, "toArray" ) ){
				$var = $var->toArray();
			}else{
				/**
				 * 这种只显示一个类名
				 */
				$var = "class:".get_class($var);
			}
		}else{
			//其他类型，什么都不用处理
		}
		return $var;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param SimpleXMLElement|sting $xml
	 * @return array
	 */
	public static function XmlToArray_V1($xml)
	{
		if ($xml instanceof SimpleXMLElement) {
			$children = $xml->children();
			$return = null;
		}else{
			$new_xml = simplexml_load_string( $xml );
			if( $new_xml instanceof SimpleXMLElement ){
				$children = $new_xml->children();
				$return = null;			
			}else{
				return null;
			}
		}

		foreach ($children as $element => $value) {
			if ($value instanceof SimpleXMLElement) {
				$values = (array)$value->children();
				if (count($values) > 0) {				
					$return[$element] = self::XmlToArray($value);
				} else {			
					if (!isset($return[$element])) {
						$return[$element] = (string)$value;
					} else {
						if (!is_array($return[$element])) {
							$return[$element] = array($return[$element], (string)$value);
						} else {
							$return[$element][] = (string)$value;
						}
					}
				}
			}
		}

		if (is_array($return)) {
			return $return;
		} else {
			return false;
		}
	}
	
	public static function XmlToArray_V2($xml) {
		$arr = array();
		$x = 0;
		foreach($xml as $a=>$b) {
			$arr[$a][$x] =  array();

			// Looking for ATTRIBUTES
			$att = $b->attributes();
			foreach($att as $c=>$d) {
				$arr[$a][$x]['@'][$c] = (string) $d;
			}

			// Getting CDATA
			$arr[$a][$x]['cdata'] = trim((string) utf8_decode($b));

			// Processing CHILD NODES
			$arr[$a][$x]['nodes'] = self::XmlToArray($b);
			$x++;
		}

		return $arr;
	}
	
	/**
	 * @param SimpleXMLElement $xml
	 * @return array
	 */
	public static function XmlToArray($xml) {
		//很奇怪，simplexml_load_string在这里就不好用，在XmlToArray_V1里就好用 by terry at Thu Sep 10 20:00:35 CST 2009
//		if( !is_object( $xml ) ){
//			//$xml = trim($xml);
//			$new_xml = simplexml_load_string( $xml );
//			$xml = $new_xml;
//		}
		
		if (get_class($xml) == 'SimpleXMLElement') {
			$attributes = $xml->attributes();
			foreach($attributes as $k=>$v) {
				if ($v) $a[$k] = (string) $v;
			}
			$x = $xml;
			$xml = get_object_vars($xml);
		}

		if (is_array($xml)) {
			if (count($xml) == 0) return (string) $x; // for CDATA
			foreach($xml as $key=>$value) {
				$r[$key] = self::XmlToArray($value);
				// original line instead of the following if statement:
				//$r[$key] = simplexml2ISOarray($value);
				if ( !is_array( $r[$key] ) ) $r[$key] =  $r[$key];
			}
			if (isset($a)) $r['@'] = $a;    // Attributes
			return $r;
		}
		return (string) $xml;
	}

	
	/**
	 * 在一个数组的开始插入一个元素
	 *
	 * @param array $array
	 * @param string $key
	 * @param mixed $value
	 */
	public static function insertBeforeStart( $array, $key=null, $value=null )
	{
		if( is_null( $key ) )
		{
			return array_unshift( $array, $value );
		}
		else
		{
			$revArr = array();
			$revArr[$key] = $value;
			foreach ( $array as $k=>$v )
			{
				$revArr[$k] = $v;
			}
			return $revArr;
		}
	}
	
	/**
	 * 将一个变量转成xml形式
	 *
	 * @param mix $var
	 * @param string $key
	 * @param 深度 $depth
	 * @return string XML
	 */
	public static function varToXml( $var, $key="WattData", $depth = 0 )
	{
		//增加空格,格式化显示
		$xml = str_repeat("  ", $depth)."<$key>";
		if( is_array( $var ) )
		{
			$xml .= self::_arrToXml( $var, $key, $depth );
		}
		elseif( is_object( $var ) )
		{
			if( method_exists( $var, "toArray" ) ){
				$arrObj = $var->toArray();
				$xml .= self::_arrToXml( $arrObj, $key, $depth );
			}
			else
			{
				/**
				 * 这种只显示一个类名
				 */
				$xml .= "class:".get_class($var);
			}
			
			//$xml .= Watt::dump( $var, null, false );
			/*  这种只显示 public 的属性
			$arrAtt = get_object_vars( $var );
			while( list( $key2 ) = each( $arrAtt ) ){
				$xml .= "\n".self::arrToXml( $var->$key2, $key2, $depth+1 );
			}
			$xml .= "\n".str_repeat("  ", $depth);
			*/
		}
		elseif( $var !== null )
		{
			$xml .= htmlspecialchars( $var );
		}
		$xml .= "</$key>";
		return $xml;
	}
	
	/**
	 * 这是重构出的函数
	 *
	 * @param arr $arr
	 * @param string $key
	 * @param int $depth
	 * @return string
	 */
	private static function _arrToXml( $var, $key, $depth )
	{
		$xml = "";
		
		//如果 $key 是复数，且$key2是数字，则用 $key 的单数形式作为$key2
		$singleKey = "";
		if( strrpos( $key, "s" ) == strlen( $key ) - 1 ){
			$singleKey = substr( $key, 0, strlen($key) - 1 );
		}

		foreach ( $var as $key2 => $val )
		{
			if( is_numeric( $key2 ) )
			{
				if( $singleKey ){
					$key2 = $singleKey;
				}else{
					$key2 = $key.$key2;
					/**
					 * 改为不加数字序号
					 * 没时间测试,先不改
					 * @author terry
					 * @version 0.1.0
					 * Mon Jul 23 16:11:43 CST 2007
					 */
					//$key2 = $key;
				}
			}
			$xml .= "\n".self::varToXml( $val, $key2, $depth+1 );
		}
		$xml .= "\n".str_repeat("  ", $depth);
		return $xml;
	}

	public static function doSelectPeerToArray($omPeerName, Criteria $criteria, $con = null){
		eval("\$rs = $omPeerName::doSelectRs( \$criteria, \$con );");

		eval("\$fields = $omPeerName::getFieldNames( BasePeer::TYPE_FIELDNAME );");	
		//var_dump( $fields );
		$grid = null;
		$fields_count = count( $fields );
		while($rs->next()) {
			//var_dump( $rs );
			for ( $i=1;$i<$fields_count+1;$i++ ){
				$row[$fields[$i-1]] = $rs->get($i);
			}
			$grid[] = $row;
	    }
		return $grid;
	}
	
	/**
	 * 去除数组中空的数组元素
	 *
	 * @param array $array
	 * @return array
	 */
	public static function clearBlackArrayItem( $array ){
		$newArray = null;
		if( is_array( $array ) ){
			$isAllItemBlank = true;
			foreach ($array as $key => $val) {
				if( is_array( $val ) ){
					$newVal = self::clearBlackArrayItem( $val );
					if( $newVal ){
						$newArray[$key] = $newVal;
					}
				}else{
					$newVal = $val;
					$newArray[$key] = $val;
				}
				if( $newVal ){
					$isAllItemBlank = false;					
				}
			}
			if( $isAllItemBlank ){
				//unset( $array );
				$newArray = null;
			}else{
				//$newArray = $array;
			}
		}else{
			$newArray = $array;
		}
		return $newArray;
	}
	
	/**
	 * 无用了
	 *
	 * @param unknown_type $arr
	 * @param unknown_type $depth
	 * @return unknown
	 */
//	private static function arrToXmlX( $arr, $depth = 0 )
//	{
//		$xml = "";
//		if( is_array( $arr ) )
//		{
//			foreach ( $arr as $key => $val )
//			{
//				if( is_array( $val ) )
//				{
//					$xml .= str_repeat("\t", $depth)."<$key>\n";
//					$xml .= self::arrToXml( $val, $depth+1 );
//					$xml .= str_repeat("\t", $depth)."</$key>\n";
//				}
//				elseif( is_object( $val ) )
//				{
//					/**
//					 * 这种只显示一个类名
//					 * $xml .= str_repeat("\t", $depth)."<$key>class:".get_class($val)."</$key>\n";
//					 */
//					$xml .= str_repeat("\t", $depth)."<".get_class($val).">\n";
//					$arr = get_object_vars( $val );
//					while( list( $key1 ) = each( $arr ) ){
//						$xml .= "<$key1>".$val->$key."</$key1>\n";
//						//Watt_Debug::addInfoToDefault(__FILE__.__LINE__, " get request $key");
//					}
//					$xml .= str_repeat("\t", $depth)."</".get_class($val).">\n";
//				}
//				else
//				{
//					$xml .= str_repeat("\t", $depth)."<$key>".htmlspecialchars( $val )."</$key>\n";
//				}
////				elseif( $val !== null )
////				{
////					$xml .= str_repeat("\t", $depth)."<$key>".htmlspecialchars( $val )."</$key>\n";
////				}
////				else
////				{
////					$xml .= str_repeat("\t", $depth)."<$key/>\n";
////				}
//			}
//		}
//		return $xml;
//	}
	//输入一个数组，和一个字段名
	public static function getSqlIn($shuju,$ziduan=false){
		$zong = count($shuju);
		$sql = '';
		if($ziduan){
			$i=0;
			foreach ($shuju as $val){
				$sql .= "'".$val[$ziduan]."'";
				$i++;
				
				if($i<$zong){
					$sql .= ',';
				}
			}
		}else{
			$i=0;
			foreach ($shuju as $val){
				$sql .= "'".$val."'";
				$i++;
				
				if($i<$zong){
					$sql .= ',';
				}
			}
		}
		return $sql;
	}
}

