<?
class Watt_Date_BizManager{
	const YEAR_SPLIT_POINT  = '07-01';
	const MONTH_SPLIT_POINT = 16;	// 以 >= 16 来计算
	const WEEK_SPLIT_POINT  = 5;    // 以 >= 5  来计算 未实现
	const FULL_WEEK_SECONDS = 604800;	//86400 * 7;

	private $_bizYearStart;		//01-01 格式
	private $_bizMonthStart;	//1 - 28
	private $_bizWeekStart;		//0 - 6 周日 - 周六

	private $_monthOfYearStart;	//设置 _bizYearStart 后计算生成
	private $_dayOfYearStart;	//设置 _bizYearStart 后计算生成

	private $_dateOfFirstBizWeekOfBizYear;	//设置 _bizWeekStart 后计算生成
	
	private $_timestampsOfFirstBizDayOfBizYear = array();					//缓存商务年起始天的时间戳
	private $_timestampsOfFirstBizDayOfFirstBizMonthOfBizYear = array();	//缓存商务年起始商务月起始商务天的时间戳
	private $_timestampsOfFirstBizDayOfFirstBizWeekOfBizYear  = array();	//缓存商务年起始商务周起始商务天的时间戳
	
	private static $_dataManagerPool = array();

	/**
	 * $dateManager = Watt_Date_BizManager::factory('01-01',1,1);
	 * 
	 * @param string $yearStart
	 * @param int $monthStart
	 * @param int $weekStart
	 * @return Watt_Date_BizManager
	 */
	public static function factory( $yearStart=null, $monthStart=null, $weekStart=null ){
		$key = $yearStart.'|'.$monthStart.'|'.$weekStart;
		if( !key_exists( $key, self::$_dataManagerPool ) ){
			self::$_dataManagerPool[$key] = new Watt_Date_BizManager( $yearStart, $monthStart, $weekStart );
		}
		return self::$_dataManagerPool[$key];
	}

	/**
	 * @param string $yearStart
	 * @param int $monthStart
	 * @param int $weekStart
	 * @return Watt_Date_BizManager
	 */
	public static function bizManagerFactory( $yearStart=null, $monthStart=null, $weekStart=null ){
		return self::factory( $yearStart, $monthStart, $weekStart );
	}
	
	/**
	 * 不允许创建实例，只允许通过 bizManagerFactory 生成
	 * 如果未设置年月周的起始天，那么按自然年、月、周来处理
	 * 
	 * @param string $yearStart
	 * @param int $monthStart
	 * @param int $weekStart
	 */
	private function __construct( $yearStart=null, $monthStart=null, $weekStart=null ){
		$config_date = TpmStatYingye::_configDate();
		if( !$yearStart  ){ $yearStart = $config_date['year']; }
		if( !$monthStart ){ $monthStart = $config_date['month']; }
		if( $weekStart===null ){ $weekStart = $config_date['week']; }		
		
		$this->setBizYearStart( $yearStart );
		$this->setBizMonthStart( $monthStart );
		$this->setBizWeekStart( $weekStart );
	}

	/**
	 * 设置商务年的起始日期
	 * @param string $yearStart 形如 01-01 的形式
	 */
	public function setBizYearStart( $yearStart ){
		if( $yearStart ){
			$this->_bizYearStart = $yearStart;
		}else{
			$this->_bizYearStart = '01-01';
		}
		
		$arrTmp = explode( '-', $this->_bizYearStart );
		$this->_monthOfYearStart = intval( $arrTmp[0] );	//计算出商务年起始商务月对应的自然月
		$this->_dayOfYearStart   = intval( $arrTmp[1] );	//计算出商务年起始日对应的自然日
	}

	/**
	 * 设置月的起始日期， 1 - 28 考虑到2月的情况，最大到28
	 * @param int $monthStart
	 */
	public function setBizMonthStart( $monthStart ){
		if( $monthStart ){
			$this->_bizMonthStart = intval( $monthStart );
			if( $this->_bizMonthStart < 1 ){
				$this->_bizMonthStart = 1;
			}elseif ( $this->_bizMonthStart > 28 ){
				$this->_bizMonthStart = 28;
			}
		}else{
			$this->_bizMonthStart = 1;
		}
	}

	/**
	 * 设置周的起始日期 1 - 7
	 * @param int $weekStart
	 */
	public function setBizWeekStart( $weekStart ){
		if( $weekStart ){
			$this->_bizWeekStart = intval( $weekStart );
		}else{
			$this->_bizWeekStart = 0;	//从星期天算起
		}
	}

	/**
	 * 获取本商务年份
	 * @return int
	 */
	public function getCurrentBizYear(){
		return $this->getBizYearOfDate( time() );
	}

	/**
	 * 获取本商务月份
	 * @return int
	 */
	public function getCurrentBizMonth(){
		return $this->getBizMonthOfDate( time() );
	}

	/**
	 * 获取本商务周周数
	 * @return int
	 */
	public function getCurrentBizWeek (){
		return $this->getBizWeekOfDate( time() );
	}

	/**
	 * 获取一自然天所在的商务年
	 * @return int
	 */
	public function getBizYearOfDate( $dateOrTimestamp ){
		/*
		得到当前自然年 及 当前自然月 当前自然日期
		if 当前自然月+当前自然日期>= [年起始自然月+年起始自然天] then
			本年 = 当前自然年
		else
			本年 = 当前自然年 – 1
		end if
		*/
		$dateOrTimestamp = $this->_formatDateToTimestamp( $dateOrTimestamp );

		$year  = intval( date( 'Y', $dateOrTimestamp ) );
		$monthAndDate = date( 'm-d', $dateOrTimestamp );
		if( $monthAndDate >= $this->_bizYearStart ){
			return $year;
		}else{
			return $year - 1;
		}
	}

	/**
	 * 获取一自然天所在的商务月
	 * @return int
	 */
	public function getBizMonthOfDate( $dateOrTimestamp ){
		/*
		//获得当前商务年
		获得当前商务年的商务起始月对应的自然月
		if 月起始天 >= 16 then
			当前商务月 = 当前自然月 - 当前商务年的商务起始月对应的自然月 + 2
		else
			当前商务月 = 当前自然月 - 当前商务年的商务起始月对应的自然月 + 1
		end if
		if 天 < 月起始天 then
			当前商务月 = 当前商务月 - 1
		end if
		*/
		$dateOrTimestamp = $this->_formatDateToTimestamp( $dateOrTimestamp );
		//$bizYear = $this->getBizYearOfDate( $dateOrTimestamp );
		$month = intval( date( 'm', $dateOrTimestamp ) );
		$day   = intval( date( 'd', $dateOrTimestamp ) );
		if( $this->_bizMonthStart >= self::MONTH_SPLIT_POINT /*&& $month<>'12'*/){
			if($month==12&&$day>=$this->_bizMonthStart )//John  解决当12月并且超过_bizMonthStart号时 取下一年的1月问题
			{
				$theBizMonth = $month - $this->_monthOfYearStart + 1;
			}
			else 
			{
				$theBizMonth = $month - $this->_monthOfYearStart + 2;
			}
		}else{
			$theBizMonth = $month - $this->_monthOfYearStart + 1;
		}
		
		if( $day < $this->_bizMonthStart ){
			$theBizMonth--;
		}
		//if( $theBizMonth > 12 ) $theBizMonth = $theBizMonth - 12;//此处不能减12，因为没有数据表示年份的进一
		return $theBizMonth;
	}

	/**
	 * 获取一自然天所在的商务周
	 * @return int
	 */
	public function getBizWeekOfDate( $dateOrTimestamp, $bizYear=null ){
		/*
		当前商务周 = int( (当前自然天 - 年商务周起始自然天 + 1 + (1周时间 – 1) )/1周时间 )
		//来源于分页算法
		*/
		$dateOrTimestamp = $this->_formatDateToTimestamp( $dateOrTimestamp );

		if( !$bizYear ){
			$bizYear = $this->getBizYearOfDate( $dateOrTimestamp );			
		}
		$firstDayOfFirstBizWeekOfBizYear = $this->getTimestampOfFirstBizDayOfFirstBizWeekOfBizYear( $bizYear );

		$rev = floor( ( floor( ( $dateOrTimestamp - $firstDayOfFirstBizWeekOfBizYear )/86400 ) + 1 + 6 ) / 7 );
		
		return $rev;
	}
	
	/**
	 * 某日期所在商务年时间范围
	 *
	 * @param string|int $dateOrTimestamp
	 * @return Watt_Date_Range
	 */
	public function getBizYearRangeOfDate( $dateOrTimestamp ){
		$bizYear = $this->getBizYearOfDate( $dateOrTimestamp );
		return $this->getRangeOfBizYear( $bizYear );
	}
	
	/**
	 * 某日期所在商务月时间范围
	 *
	 * @param string|int $dateOrTimestamp
	 * @return Watt_Date_Range
	 */
	public function getBizMonthRangeOfDate( $dateOrTimestamp ){
		$bizYear = $this->getBizYearOfDate( $dateOrTimestamp );
		$bizMonth = $this->getBizMonthOfDate( $dateOrTimestamp );
		return $this->getRangeOfBizMonthOfBizYear( $bizYear, $bizMonth );
	}
	
	/**
	 * 某日期所在商务周时间范围
	 *
	 * @param string|int $dateOrTimestamp
	 * @return Watt_Date_Range
	 */
	public function getBizWeekRangeOfDate( $dateOrTimestamp ){
		$bizYear = $this->getBizYearOfDate( $dateOrTimestamp );
		$bizWeek = $this->getBizWeekOfDate( $dateOrTimestamp );
		return $this->getRangeOfBizWeekOfBizYear( $bizYear, $bizWeek );
	}
	
	/**
	 * 获得商务年起始商务天的自然天时间戳
	 * @return int
	 */
	public function getTimestampOfFirstBizDayOfBizYear( $bizYear ){
		if( !key_exists( $bizYear, $this->_timestampsOfFirstBizDayOfBizYear ) ){
			$this->_timestampsOfFirstBizDayOfBizYear[$bizYear]
				 = strtotime( $bizYear . '-' . $this->_bizYearStart . ' 00:00:00' );
		}
		return $this->_timestampsOfFirstBizDayOfBizYear[$bizYear];
	}
	
	/**
	 * 获得商务年第一商务月起始商务天的自然天时间戳
	 * @return int
	 */
	public function getTimestampOfFirstBizDayOfFirstBizMonthOfBizYear( $bizYear ){
		
	}

	/**
	 * 获得商务年某一商务月起始商务天的自然天时间戳
	 * @return int
	 */
	public function getTimestampOfFirstBizDayOfBizMonthOfBizYear( $bizYear, $bizMonth ){
		
	}

	/**
	 * 获得商务年第一商务周起始商务天的自然天时间戳
	 * @return int
	 */
	public function getTimestampOfFirstBizDayOfFirstBizWeekOfBizYear( $bizYear ){
		/*
		if 商务年起始天对应的自然天的星期几 = 周起始星期几 then
			第1商务周起始天 = 商务年起始天
		else
			第1商务周起始天 = 商务年起始天 + ( 周起始星期几 - 商务年起始天对应的自然天的星期几 + 7 ) mod 7
			//商务年起始天 + 1周时间 – ( 1周时间 - 周起始星期几 ) – 1
		end if
		*/
		$firstDayOfBizYear = $this->getTimestampOfFirstBizDayOfBizYear( $bizYear );
		$weekDayOfBizYearStartDay = intval( date( 'w' , $firstDayOfBizYear) );

		if ( $weekDayOfBizYearStartDay == $this->_bizWeekStart ) {
			$firstDayOfFirstBizWeek = $firstDayOfBizYear;
		}else{
			$firstDayOfFirstBizWeek = $firstDayOfBizYear + (( $this->_bizWeekStart - $weekDayOfBizYearStartDay + 7 ) % 7) * 86400;
		}

//		print"<pre>Terry :";var_dump( '$firstDayOfBizYear:' . date( 'Y-m-d H:i:s', $firstDayOfBizYear ) );print"</pre>";
//		print"<pre>Terry :";var_dump( '$weekDayOfBizYearStartDay:' . $weekDayOfBizYearStartDay );print"</pre>";
//		print"<pre>Terry :";var_dump( '$this->_bizWeekStart:' . $this->_bizWeekStart );print"</pre>";
//		print"<pre>Terry :";var_dump( '$firstDayOfFirstBizWeek:' . date( 'Y-m-d H:i:s', $firstDayOfFirstBizWeek ) );print"</pre>";
//		exit();
		
		return $firstDayOfFirstBizWeek;
	}

	/**
	 * 获得一个日期对应的商务日期组
	 *
	 * @param int|string $dateOrTimestamp
	 * @return array
	 */
	public function getBizDateOfDate( $dateOrTimestamp ){
		//$dateOrTimestamp = $this->_formatDateToTimestamp( $dateOrTimestamp );
		$rev = array();
		$rev['bizYear']  = $this->getBizYearOfDate( $dateOrTimestamp );
		$rev['bizMonth'] = $this->getBizMonthOfDate( $dateOrTimestamp );
		$rev['bizWeek']  = $this->getBizWeekOfDate( $dateOrTimestamp );
		
		if( $rev['bizMonth'] > 12 ){
			$rev['bizYear'] += 1;
			$rev['bizMonth'] -= 12;
			$rev['bizWeek'] -= 52;	//此处还不严谨
		}
		return $rev;
	}
	
	/**
	 * 获得一个日期对应的商务日期组[乱]
	 *
	 * @param int|string $dateOrTimestamp
	 * @return array
	 */	
	public function getBizYearAndMonthOfDate( $dateOrTimestamp ){
		$rev = array();
		$rev['bizYear']  = $this->getBizYearOfDate( $dateOrTimestamp );
		$rev['bizMonth'] = $this->getBizMonthOfDate( $dateOrTimestamp );
		
		if( $rev['bizMonth'] > 12 ){
			$rev['bizYear'] += 1;
			$rev['bizMonth'] -= 12;
		}
		return $rev;
	}
	
	/**
	 * 获得一个日期对应的商务日期组[乱]
	 *
	 * @param int|string $dateOrTimestamp
	 * @return array
	 */	
	public function getBizYearAndWeekOfDate( $dateOrTimestamp ){
		//$dateOrTimestamp = $this->_formatDateToTimestamp( $dateOrTimestamp );
		$rev = array();
		$rev['bizYear']  = $this->getBizYearOfDate( $dateOrTimestamp );
		$rev['bizWeek']  = $this->getBizWeekOfDate( $dateOrTimestamp );	
		
		if( $rev['bizWeek'] <= 0 ){		//如果周数小于等于 0 ，回退1年再计算
			$rev['bizYear'] = $rev['bizYear']-1;
			$rev['bizWeek']  = $this->getBizWeekOfDate( $dateOrTimestamp, $rev['bizYear'] );
		}
		
		return $rev;
	}
	
	/**
	 * 获得商务年某一商务周起始商务天的自然天时间戳
	 * @return int
	 */
	public function getTimestampOfFirstBizDayOfBizWeekOfBizYear( $bizYear, $bizWeek ){
		
	}
	
	/**
	 * 获取本商务年时间范围
	 * @return Watt_Date_Range
	 */
	public function getCurrentBizYearRange(){
		return $this->getRangeOfBizYear( $this->getCurrentBizYear() );
	}

	/**
	 * 获取本商务月时间范围
	 * @return Watt_Date_Range
	 */
	public function getCurrentBizMonthRange(){
		return $this->getRangeOfBizMonthOfBizYear( $this->getCurrentBizYear(), $this->getCurrentBizMonth() );
	}

	/**
	 * 获取本商务周时间范围
	 * @return Watt_Date_Range
	 */
	public function getCurrentBizWeekRange(){
		return $this->getRangeOfBizWeekOfBizYear( $this->getCurrentBizYear(), $this->getCurrentBizWeek() );
	}

	/**
	 * 输入一个商务年，获取该商务年时间范围
	 * @param int $year
	 * @return Watt_Date_Range
	 */
	public function getRangeOfBizYear( $bizYear ){
		/*
		如指定2006，
		该年的起始天 = 2006 + 年起始自然月+ [年起始自然天] 
		该年的结束天 = (2006+1年) +年起始自然月+ [年起始自然天]
		*/	
		$startDay = strtotime( $bizYear . '-' . $this->_bizYearStart );
		$endDay   = strtotime( ($bizYear + 1) . '-' . $this->_bizYearStart );
		
		$revRange = new Watt_Date_Range();
		$revRange->setBeginTimestamp( $startDay );
		$revRange->setEndTimestamp( $endDay );
		return $revRange;
	}

	/**
	 * 获取今天的时间范围
	 * @return Watt_Date_Range
	 */
	public function getRangeOfToday(){
		$now = time();
		$beginOfToday = strtotime(date('Y-m-d'),$now);//$now - $now % 86400;
		$endOfToday   = $beginOfToday + 86400;
		$revRange = new Watt_Date_Range();
		$revRange->setBeginTimestamp( $beginOfToday );
		$revRange->setEndTimestamp( $endOfToday );
		return $revRange;
	}
	
	/**
	 * 输入一个商务年，及商务月数，获取该商务年的指定商务月时间范围
	 * @param int $year
	 * @param int $month
	 * @return Watt_Date_Range
	 */
	public function getRangeOfBizMonthOfBizYear( $bizYear, $bizMonth ){
		/*
		指定目标商务年，目标商务月
		if 月起始天 >= 16 then
			该月的起始天 =  (目标商务年 + (年起始自然月 + 目标商务月 – 2个月) +月起始天)  //从上个月算
		else
			该月的起始天 =  (目标商务年 + (年起始自然月 + 目标商务月 – 1个月) +月起始天)  //从当月算
		end if
		该月的结束天 = 该月的起始天 + 1个月
		*/
		if( $this->_bizMonthStart >= self::MONTH_SPLIT_POINT ){
			$tmpMonth = $this->_monthOfYearStart + $bizMonth - 2;
		}else{
			$tmpMonth = $this->_monthOfYearStart + $bizMonth - 1;
		}
		$year = $bizYear;
		
		
		$startDay = mktime( 0, 0, 0, $tmpMonth, $this->_bizMonthStart, $year );
		$endDay   = mktime( 0, 0, 0, $tmpMonth+1, $this->_bizMonthStart, $year );
		
		//1月份为1月1日至1月25日。12月份为11月26日至12月30日（最后一天）。 jute 20090417
		if(date('m',$endDay) ==1){//
			$startDay = mktime( 0, 0, 0, $tmpMonth+1, 1, $year );
		}else if(date('m',$endDay) ==12){
			$endDay   = mktime( 0, 0, 0, $tmpMonth+1, date('t',$endDay)+1, $year );
		}		
		$revRange = new Watt_Date_Range();
		$revRange->setBeginTimestamp( $startDay );
		$revRange->setEndTimestamp( $endDay );
		return $revRange;
	}

	/**
	 * 输入一个商务年，及商务周数，获取该商务年的指定商务周时间范围
	 * @param int $year
	 * @param int $weekNumber
	 * @return Watt_Date_Range
	 */
	function getRangeOfBizWeekOfBizYear( $bizYear, $bizWeek ){
		/*
		指定目标商务年，目标商务周
		
		目标商务周起始自然天 = 第1商务周起始天 + 1周时间 * (目标商务周周次 – 1)
		目标商务周结束自然天 = 目标商务周起始自然天 + 1周时间
		*/
		$firstDayOfFirstBizWeek = $this->getTimestampOfFirstBizDayOfFirstBizWeekOfBizYear( $bizYear );
		
		$startDay = $firstDayOfFirstBizWeek + ( $bizWeek - 1 ) * self::FULL_WEEK_SECONDS;
		$endDay   = $startDay + self::FULL_WEEK_SECONDS;
		
		$revRange = new Watt_Date_Range();
		$revRange->setBeginTimestamp( $startDay );
		$revRange->setEndTimestamp( $endDay );
		return $revRange;
	}

	/**
	 * 输入一个商务年，获取该商务年的所有商务月的时间范围列表
	 * @param int $year
	 * @return array Watt_Date_Range
	 */
	function getBizMonthRangeListOfBizYear( $bizYear ){
		$revArr = array();
		for( $i = 1; $i <= 12; $i++ ){
			$revArr[$i] = $this->getRangeOfBizMonthOfBizYear( $bizYear, $i );
		}
		return $revArr;
	}

	/**
	 * 输入一个商务年，获取该商务年的所有周的时间范围列表
	 * @param int $year
	 * @return array Watt_Date_Range
	 */
	function getBizWeekRangeListOfBizYear( $bizYear ){
		$revArr = array();
		for( $i = 1; $i <= 53; $i++ ){
			$revArr[$i] = $this->getRangeOfBizWeekOfBizYear( $bizYear, $i );
		}

		$range53   = $revArr[53];
		$startDate = date( 'Y-m-d', $range53->getBeginTimestamp() );
		if( $startDate >= ($bizYear + 1).'-'.$this->_bizYearStart ) {
			unset( $revArr[53] );	//如果53周落在第二商务年，则去掉53周
		}
		return $revArr;
	}
	
	/**
	 * 格式化时间为时间戳
	 *
	 * @param string|int $dateOrTimestamp
	 * @return int
	 */
	private function _formatDateToTimestamp( $dateOrTimestamp ){
		if( !is_numeric( $dateOrTimestamp ) ){
			$dateOrTimestamp = strtotime( $dateOrTimestamp );
		}
		return $dateOrTimestamp;
	}
}

/**
 * 使用时使用 begin <= range < end 的形式
 *
 */
class Watt_Date_Range{
	private $_beginTimestamp;
	private $_endTimestamp;
	
	public function setBeginTimestamp( $timestamp ){
		$this->_beginTimestamp = $timestamp;
	}

	public function setEndTimestamp( $timestamp ){
		$this->_endTimestamp = $timestamp;
	}

	/**
	 * @return int
	 */
	public function getBeginTimestamp(){
		return $this->_beginTimestamp;
	}

	/**
	 * @return int
	 */
	public function getEndTimestamp(){
		return $this->_endTimestamp-1;
	}
	
	/**
	 * @return string
	 */
	public function toString(){
		return date( 'Y-m-d H:i:s', $this->_beginTimestamp ) . ' - ' . date( 'Y-m-d H:i:s', $this->_endTimestamp );
	}
}
?>