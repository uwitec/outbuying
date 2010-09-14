<?
class Pft_DateTime_Util{
	/**
	 * 获取时间描述符
	 * @return array
	 * @since 0.0.1
	 * @author y31
	 * Tue Feb 05 23:03:55 CST 2008
	 */
	public static function getTimeDiff( $newTime, $oldTime ){
		$timed = $newTime - $oldTime;
		
		$rev = array(
			'week' => 0,
			'day' => 0,
			'hour' => 0,
			'min' => 0,
			'sec' => 0,
			'direction' => 1,
		);

		if( $timed >= 0 ){
			$rev['direction'] = 1;
		}else{
			$rev['direction'] = -1;
			$timed = -$timed;
		}
		
		$rev['week'] = floor( $timed / 674800 );
		$timed = $timed % 674800;

		$rev['day'] = floor( $timed / 86400 );
		$timed = $timed % 86400;

		$rev['hour'] = floor( $timed / 3600 );
		$timed = $timed % 3600;

		$rev['min'] = floor( $timed / 60 );
		$timed = $timed % 60;

		$rev['sec'] = $timed;

		return $rev;
	}
	
	/**
	 * 获取时间差距描述字符串
	 * @author y31
	 * Tue Feb 05 23:16:03 CST 2008
	 */
	public static function getTimeDiffString( $newTime, $oldTime ){
		$arr = self::getTimeDiff( $newTime, $oldTime );
		$rev = '';
		$rev .= $arr['week']?$arr['week'].'周':'';
		$rev .= $arr['day']?$arr['day'].'天':'';
		$rev .= $arr['hour']?$arr['hour'].'小时':'';
		$rev .= $arr['min']?$arr['min'].'分':'';
		$rev .= $arr['sec']?$arr['sec'].'秒':'';
		$rev .= $arr['direction']>=0?'前':'后';
		
		return $rev;
	}
}

?>