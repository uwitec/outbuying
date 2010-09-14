<?
class Watt_Util_DateTime{
	/**
	 * @param int $seconds
	 * @return array
	 * @author terry
	 * @version 0.1.0
	 * Mon Nov 17 11:26:38 CST 2008
	 */
	private static function _calcPeriodArr( $seconds ){
		$rev = array(
			'week' => 0,
			'day' => 0,
			'hour' => 0,
			'min' => 0,
			'sec' => 0,
			'direction' => 1,
		);

		if( $seconds >= 0 ){
			$rev['direction'] = 1;
		}else{
			$rev['direction'] = -1;
			$seconds = -$seconds;
		}
		
		$rev['week'] = floor( $seconds / 674800 );
		$seconds = $seconds % 674800;

		$rev['day'] = floor( $seconds / 86400 );
		$seconds = $seconds % 86400;

		$rev['hour'] = floor( $seconds / 3600 );
		$seconds = $seconds % 3600;

		$rev['min'] = floor( $seconds / 60 );
		$seconds = $seconds % 60;

		$rev['sec'] = $seconds;
		return $rev;
	}

	/**
	 * @param array $arr
	 * @return string
	 * @author terry
	 * @version 0.1.0
	 * Mon Nov 17 11:33:45 CST 2008
	 */
	private static function _getPeriodDescStrByArr( $arr ){
		$rev = '';
		$rev .= $arr['direction']>=0?'':'-';
//		$rev .= $arr['week']?$arr['week'].'W':'';
//		$rev .= $arr['day']?$arr['day'].'D':'';
//		$rev .= $arr['hour']?$arr['hour'].'H':'';
//		$rev .= $arr['min']?$arr['min'].'m':'';
//		$rev .= $arr['sec']?$arr['sec'].'s':'';			
		$rev .= $arr['week']?$arr['week'].'周':'';
		$rev .= $arr['day']?$arr['day'].'天':'';
		$rev .= $arr['hour']?$arr['hour'].'小时':'';
		$rev .= $arr['min']?$arr['min'].'分':'';
		$rev .= $arr['sec']?$arr['sec'].'秒':'';
		return $rev;
	}
	
	public static function getPeriodDescStr( $seconds ){
		return self::_getPeriodDescStrByArr(self::_calcPeriodArr($seconds));
	}
	
	public static function getPeriodByDates( $seconds ){
		$dates = ceil($seconds / 86400);
		return $dates;
	}
	
	public static function getPeriodByHours( $seconds ){
		$hours = ceil($seconds / 3600);
		return $hours;
	}
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

		if( $timed > 0 ){
			$rev['direction'] = 1;
		}elseif( $timed < 0 ){
			$rev['direction'] = -1;
			$timed = -$timed;
		}else{
			return $rev;
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
		$rev .= $arr['direction']>=0?'':'-';
//		$rev .= $arr['week']?$arr['week'].'W':'';
//		$rev .= $arr['day']?$arr['day'].'D':'';
//		$rev .= $arr['hour']?$arr['hour'].'H':'';
//		$rev .= $arr['min']?$arr['min'].'m':'';
//		$rev .= $arr['sec']?$arr['sec'].'s':'';			
		$rev .= $arr['week']?$arr['week'].i18ntrans('#周'):'';
		$rev .= $arr['day']?$arr['day'].i18ntrans('#天'):'';
		$rev .= $arr['hour']?$arr['hour'].i18ntrans('#小时'):'';
		$rev .= $arr['min']?$arr['min'].i18ntrans('#分'):'';
		$rev .= $arr['sec']?$arr['sec'].i18ntrans('#秒'):'';
		
		return $rev;
	}

	
	const ZHQ_START_TIME	=	'start time is null';
	const ZHQ_END_TIME		=	'end time is null';	
	/**
	 * 功能：计算周期
	 * 传入两个时间,计算周期
	 * Tony---Fri Mar 16 16:29:09 CST 2007----16:29:09
	 * $bs为真时不返回字体设置，导出报表时用 jute 20080416
	 */
	public static function getTimeZhouqi( $min_time,$max_time,$bs=false)
	{
		$error	=	$min_time?'':self::ZHQ_START_TIME ;
		$error	.=	$max_time?'':self::ZHQ_END_TIME ;
		if (!$error)
		{
			$day	=	($max_time-$min_time)/86400;
			if (abs($day)>1)
			{
				$ret	=	round( $day ).Watt_I18n::trans( 'TY_TIAN' );
			}
			elseif (abs(($max_time-$min_time)/3600)>1)
			{
				$ret	=	round(($max_time-$min_time)/3600).Watt_I18n::trans( 'TY_XIAOSHI' );
			}
			else 
			{
				$ret	=	(($max_time-$min_time)>0?'':'-').'0.1'.Watt_I18n::trans( 'TY_XIAOSHI' );
			}
		}
		else 
		{
			$ret	=	$error;
		}
		if (($max_time<$min_time) && !$bs)			
			$ret	=	'<font color="red">'.$ret.'</font>';
			
		return  $ret;
	}	
}
?>