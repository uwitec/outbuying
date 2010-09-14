<?
/**
 * 排序工具类
 * @author terry
 */

class Pft_Util_Sort{
	const ASC  = 0;
	const DESC = 1;
	
	private static $_countTimes = 0;
	
	/**
	 * 插入排序
	 * 
	 * 如果数组元素是数组 则 $getCompareValueEval 使用
	 *   "[0]" 或 "['key']" 的形式
	 * 如果数组元素是对象 则 $getCompareValueEval 使用
	 *   "->getValue()" 的形式
	 * 
	 */
	public static function InsertionSort( $arrInput, $sort=self::ASC, $getCompareValueEval=null ){
		if(!is_array($arrInput)){
			return null;
		}
		self::$_countTimes = 0;
		for( $j=1; $j<count($arrInput); $j++ ){
			$currentItem = $arrInput[$j];
			if( $getCompareValueEval ){
				eval("\$currentValue = \$arrInput[\$j]{$getCompareValueEval};");
			}else{
				$currentValue = $arrInput[$j];
			}
			//self::$_countTimes++;
			//将$arrInput[$j]插入到排好序的序列A[1..j-1]中
			$i = $j-1;
			while( $i >= 0 ){
				//print "[$compareValue] : [$currentValue]\n";
				if( $getCompareValueEval ){
					eval("\$compareValue = \$arrInput[\$i]{$getCompareValueEval};");
				}else{
					$compareValue = $arrInput[$i];
				}
				if( $sort == self::DESC ){
					if( $compareValue > $currentValue ) break;
				}else{
					if( $compareValue < $currentValue ) break;					
				}

				$arrInput[$i+1] = $arrInput[$i];
				self::$_countTimes++;
				//$i = $j-1;
				$i--;
			}
			
			$arrInput[$i+1] = $currentItem;
		}
		return $arrInput;
	}
}