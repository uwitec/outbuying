<?
/**
 * 对searchs进行定义
 * 可以生成条件 对象，规范化的Grid定义数组
 * 
 * 在action中这样使用
 * <code>
 * 		$searchs = new Watt_Util_Grid_Searchs( "TpmYonghuPeer" );
 *		$searchs->addSearch( TpmYonghuPeer::YH_ZHANGHU );
 *		$this->grid = $searchs->excuteAndReturnGridData();
 * </code>
 *
 * 接口已经稳定，可以投入使用
 * 
 * 下一步要解决的问题
 * \/	1) 使用下拉框选择的 search item 的处理
 * \/	2) order by 的处理
 * \/	3) 支持Sql
 * \/	4) 支持时间选择器
 * \/	5) 支持高级检索
 * 
 * @since 1
 * @author Terry
 * @package Watt_Util
 * @version 0.9.2
 */

class Watt_Util_Grid_Searchs{

    /** Comparison type. */
    const EQUAL = "=";

    /** Comparison type. */
    const NOT_EQUAL = "<>";

    /** Comparison type. */
    const ALT_NOT_EQUAL = "!=";

    /** Comparison type. */
    const GREATER_THAN = ">";

    /** Comparison type. */
    const LESS_THAN = "<";

    /** Comparison type. */
    const GREATER_EQUAL = ">=";

    /** Comparison type. */
    const LESS_EQUAL = "<=";

    /** Comparison type. */
    const LIKE = " LIKE ";

    /** Comparison type. */
    const NOT_LIKE = " NOT LIKE ";

    /** PostgreSQL comparison type */
    const ILIKE = " ILIKE ";
    
    /** PostgreSQL comparison type */
    const NOT_ILIKE = " NOT ILIKE ";

    /** Comparison type. */
    const CUSTOM = "CUSTOM";

    /** Comparison type. */
    const DISTINCT = "DISTINCT ";

    /** Comparison type. */
    const IN = " IN ";

    /** Comparison type. */
    const NOT_IN = " NOT IN ";

    /** Comparison type. */
    const ALL = "ALL ";
    
    /** Comparison type. */
    const JOIN = "JOIN";

    /** Binary math operator: AND */
    const BINARY_AND = "&";

    /** Binary math operator: OR */
    const BINARY_OR = "|";

    /** "Order by" qualifier - ascending */
    const ASC = "ASC";

    /** "Order by" qualifier - descending */
    const DESC = "DESC";

    /** "IS NULL" null comparison */
    const ISNULL = " IS NULL ";

    /** "IS NOT NULL" null comparison */
    const ISNOTNULL = " IS NOT NULL ";

	
	const DEF_SEARCHS       = "searchs";
	const DEF_SEARCHGROUP       = "searchgroup";
	const DEF_ORDERBYS      = "orderbys";
	const DEF_ORDERBYORDERS = "orderbyorders";
	const DEF_PAGERINFO     = "pager";
	const DEF_INITPARAMS	= "initparams";	
	const DEF_EXPORT     = "export";
	const DEF_EXPORTFILE     = "exportfile";
	const DEF_EXPORTFILE_NAME     = "exportfile_name";
	const DEF_EXPORTFILE_FORMAT     = "exportfile_format";
	
	const DEF_SUM   = 'grid_sum';
	const DEF_TOTAL = 'grid_total';
	const DEF_SUM_FORMAT = 'grid_sum_format';
	const DEF_SUM_TOTAL  = 'grid_sum_total';
	/**
	 * 搜索字段名使用的前缀
	 *
	 */
	const SEARCH_NAME_PREFIX = "s";
	const SEARCH_ADV_SIGN    = "searchGridAdv";
	
	private $_searchs       = array();	
	private $_searchgroup   = array();
	private $_orderBys      = array();
	private $_orderByOrders = array();
	private $_sumCols       = array();	//小计列 其 value 与 row 的 key 一致
	private $_totalCols     = array();	//总计列 其 value 与 row 的 key 一致
	private $_sumFormat		= '';
	private $_totalFormat	= '';
	
	private $_omPeerName;
	private $_selectMethod;
	private $_countMethod;
	private $_otherParam;			//调用 $_selectMethod 和 $_countMethod 时附加的参数
	
	private $_defaultOrderBy;		//默认排序
	private $_defaultOrderyByOrder;	//默认排序顺序
	
	/**
	 * @var Watt_Util_Pager
	 */
	private $_pager;
	
	private $_expressMode = false;
	private $_viewandor = false;
	
	private $_con;
	//所有条件使用 or 进行组合 默认为 false
	private $_allIsOr = false;
	
	private $_usePager = true;	//是否分页
	/**
	 * 条件
	 *
	 * @var Criteria
	 */
	private $_criteria;
	
	private $_normal_criterion;
	private $_group_criterion;
	/**
	 * Hidden search
	 * @var Criterion
	 */
	private $_hidden_criterion;
	
	private $_is_init = true; //是否是初始化
	const INIT_PARAM_NAME = 'initp';
	
	private $_initParams = array();
	
	//Sql mode 的Sql
	private $_sql = null;
	private $_export = false; //是否显示导出文件
	private $_exportfile = null;
	private $_exportfile_name = null;
	private $_parsed_sql = null;
	private $_count_sql = null;	 //获取总数的Sql
	private $_export_col = null;
	private $_export_format='csv';//默认导出格式为csv格式
	//const SQL_WHERE_STANDER = ' *|Where|* ';
	const SQL_WHERE_STANDER = ' /*|Where|*/ ';
	const SQL_LIMIT_STANDER = ' /*|Limit|*/ ';
	const SQL_ORDERBY_STANDER = ' /*|OrderBy|*/ ';
	const SQL_COUNT_STANDER   = ' /*|Count|*/ ';
	
	function __construct( $omPeerName   = null, $con = null
	                    , $selectMethod = "doSelect"
	                    , $countMethod  = "doCount"
	                    , $otherParam   = null
	                    , $advMode      = false
	                    ){
	    //默认有高级 高级还有问题，以后再弄
	    if( $advMode ){
	    	$this->enableAdvSearch();	    	
	    }
	    
		if( $omPeerName ) $this->setOmPeerName( $omPeerName );

		$this->_selectMethod = $selectMethod;
		$this->_countMethod  = $countMethod;
		$this->_con          = $con;
		$this->_criteria     = new Criteria( "propel" );
		//$this->_hidden_criterion = $this->_criteria->getNewCriterion();
		//if( $userPager )
		$this->_pager        = new Watt_Util_Pager();
		$this->_otherParam   = $otherParam;
		
		$initParms = r( self::INIT_PARAM_NAME );
		if( $initParms ){
			//$initParms 的显示在 View 里
			$params = array();
			foreach ( $initParms as $paramname ){
				$params[$paramname] = r( $paramname );
			}
			$this->_initParams = $params;
		}else{
			//如果没有初始化参数,则将 $_GET 变成初始化参数。
			$this->_initParams = $_GET;
		}
		//接收导出的格式
		if(@$_REQUEST["exportformat"])
		{
			$this->_export_format=$_REQUEST["exportformat"];
		}
	}

	/**
	 * 设置 Sql ，如果设置了Sql 则成为 Sql 模式
	 * 要求：
	 * Watt_Util_Grid_Searchs::SQL_WHERE_STANDER 前要有一个条件，如 1=1，因为 Watt_Util_Grid_Searchs::SQL_WHERE_STANDER 生成的条件带了一个 and
	 * $grid->addSearch( 'a.qx_mingcheng' )，列名要带表名
	 * 
	 * <code>
	 * $sql = "sElect * from tpm_quanxian as a 
	 * left join tpm_juese2quanxian as b on a.qx_id = b.qx_id 
	 * where 1=1 and (".Watt_Config::getCond('a').") ".Watt_Util_Grid_Searchs::SQL_WHERE_STANDER;
	 * //$sql = "select qx_mingcheng, count(qx_yemian) as qx_mingcheng_count from tpm_quanxian as a where 1=1 ".Watt_Util_Grid_Searchs::SQL_WHERE_STANDER." group by qx_mingcheng";
	 * $grid = new Watt_Util_Grid_Searchs();
	 * $grid->setSql( $sql );
	 * $grid->addSearch( 'a.qx_mingcheng',Watt_I18n::trans('qx_mingcheng') );
	 * //$grid->addSearch( 'a.qx_yemian',Watt_I18n::trans('qx_yemian') );
	 * $this->data = $grid->excuteAndReturnGridData();
	 * </code>
	 * 
	 * 
	 * @param string $v
	 */
	public function setSql( $v ){
		$this->_sql = $v;
	}
	
	public function getSql(){
		return $this->_sql;
	}
	
	public function setExport($v){
		 $this->_export = $v;
	}
	public function getExport(){
		return $this->_export;
	}
	
	public function setExportfile($v){
		 $this->_exportfile = $v;
	}
	public function getExportfile(){
		return $this->_exportfile;
	}	
	
	public function setExportfileName($v){
		 $this->_exportfile_name = $v;
	}
	public function getExportfileName(){
		return $this->_exportfile_name;
	}
	
	public function setSearchgroup( $v ){
		$this->_searchgroup = $v;
	}
	
	public function getSearchgroup(){
		return $this->_searchgroup;
	}
	public function setViewAndOr( $v ){
		$this->_viewandor = $v;
	}
	
	public function getViewAndOr(){
		return $this->_viewandor;
	}
	/**
	 * 增加小计列
	 * @param array $arrCols
	 * @return boolean
	 */
	public function setSumCols( $arrCols, $format='' ){
		if( is_array( $arrCols ) ){
			$this->_sumCols = $arrCols;
			$this->_sumFormat = $format;
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 增加合计列
	 * 注意，目前只有sql模式才支持合计列
	 * format不合适,应该在显示层..此处暂时保留吧
	 * @param array $arrCols
	 * @return boolean
	 */
	public function setTotalCols( $arrCols, $format='' ){
		if( is_array( $arrCols ) ){
			$this->_totalCols = $arrCols;
			$this->_totalFormat = $format;
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 是否翻页
	 * @param Boolean $trueOrFalse
	 */
	public function usePager( $trueOrFalse ){
		$this->_usePager = $trueOrFalse;
	}
	/**
	 * usePager的错误表示法
	 * @param Boolean $trueOrFalse
	 */
	public function userPager( $trueOrFalse ){
		$this->usePager( $trueOrFalse );
	}
	
	/**
	 * 为grid_searchs增加一个查询条件
	 * 
	 *
	 * @param string|Watt_Util_Grid_Search $sarchObjOrDbColName
	 * @param string $alias
	 * @param Watt_Util_Grid_Searchs::Const $type
	 * @param array $rels
	 * @param mixed $value default value
	 * @param boolean $isOr
	 * @param int $showType
	 * @param string $customTransformFunc 用户自定义值转换函数名
	 */
	public function addSearch( $sarchObjOrDbColName
	                         , $alias = null
	                         , $type  = self::LIKE	                         
	                         , $rels  = null
	                         , $value = null
	                         , $isOr  = null
	                         , $showType = 0
	                         , $customTransformFunc = null
	                         , $extend = null
	                         , $cond = null
        					, $item =null
        					, $isviewandor=false
	                         ){
//这是原有的顺序
//	public function addSearch( $sarchObjOrDbColName
//	                         , $value = null
//	                         , $type  = self::LIKE
//	                         , $alias = null
//	                         , $rels  = null
//	                         , $isOr  = null
//	                         ){
		
		if( $sarchObjOrDbColName instanceof Watt_Util_Grid_Search ){
			$search = $sarchObjOrDbColName;
		}else{
			$search = new Watt_Util_Grid_Search( $sarchObjOrDbColName
		                                        , $value
		                                        , $type
		                                        , $alias
		                                        , $rels
		                                        , $isOr
		                                        , $showType
		                                        , $customTransformFunc
		                                        , $extend
		                                        , $cond
	                        					, $item
	                        					, $isviewandor);
		}

	    $searchViewName = self::SEARCH_NAME_PREFIX . count( $this->_searchs );	
		$search->setViewName( $searchViewName );

		//setSearch的同时就从request获取一下数据
		if( $this->_advMode && $this->_advSearchValue ){
			//如果是高级搜索，则优先使用高级搜索
			$search->setValue( $this->_advSearchValue );
			//exit();
		}elseif( isset( $_REQUEST[$searchViewName] ) ){
			$search->setValue( $_REQUEST[$searchViewName] );
		}

		$this->_searchs[] = $search;

		$newCriterion = $search->toCriterion( $this->_criteria );

		if( $newCriterion ){
			if( $this->_normal_criterion ){
				if( $this->_allIsOr || $search->getIsOr() ){
					$this->_normal_criterion->addOr( $newCriterion );
				}else{
					$this->_normal_criterion->addAnd( $newCriterion );
				}			
			}else{
				$this->_normal_criterion = $newCriterion;
			}			
		}

//		if( $newCriterion ){
//			if( $this->_allIsOr && $search->getIsOr() ){
//				$this->_criteria->addOr( $newCriterion );
//			}else{
//				$this->_criteria->addAnd( $newCriterion );
//			}
//		}
	}
	/**
	 * 带选择条件的查询
	 * jute
	 */
	public function addSearchSelect( $sarchObjOrDbColName
	                         , $alias = null
	                         , $type  = self::LIKE	                         
	                         , $rels  = null
	                         , $value = null
	                         , $isOr  = null
	                         , $showType = 0
	                         , $customTransformFunc = null
	                         , $extend = null
	                         , $cond = null 
	                         , $item = null
	                         , $isviewandor = null
	                         ){
		if( $sarchObjOrDbColName instanceof Watt_Util_Grid_Search ){
			$search = $sarchObjOrDbColName;
		}else{
			
			$search = new Watt_Util_Grid_Search( $sarchObjOrDbColName
		                                        , $value
		                                        , $type
		                                        , $alias
		                                        , $rels
		                                        , $isOr
		                                        , $showType
		                                        , $customTransformFunc
		                                        , $extend
	                        					, $cond
	                        					, $item
	                        					, $isviewandor
		                                        );
		}
	    $searchViewName = self::SEARCH_NAME_PREFIX . count( $this->_searchs );	
		$search->setViewName( $searchViewName );

		//setSearch的同时就从request获取一下数据
		if( $this->_advMode && $this->_advSearchValue ){
			//如果是高级搜索，则优先使用高级搜索
			$search->setValue( $this->_advSearchValue );
			//exit();
		}elseif( isset( $_REQUEST[$searchViewName] ) ){
			$search->setValue( $_REQUEST[$searchViewName] );
		}
//		print "<pre>";
//		print_r($search);
//		print_r($_REQUEST);
//		print "</pre>";
		
		
		//设置and or
		if (isset($_REQUEST['isor_'.$searchViewName]) && $_REQUEST['isor_'.$searchViewName]){
			$search->setIsOr(true);
		}
		//设置搜索项 jute 20080409
		$theitem =$search->getItem();
		if(is_array($theitem) && count($theitem) && isset( $_REQUEST['item_'.$searchViewName] ) ){
			$search->setdbColName($_REQUEST['item_'.$searchViewName]);
			if($theitem[$_REQUEST['item_'.$searchViewName]]){
				$search->setAlias($theitem[$_REQUEST['item_'.$searchViewName]]);
			}else{
				$search->setAlias();
			}			
		}		
		
		//设置搜索条件
		if( isset( $_REQUEST['cond_'.$searchViewName] ) ){
			$search->setOprate( $_REQUEST['cond_'.$searchViewName] );
		}
		
		$this->_searchs[] = $search;

		$newCriterion = $search->toCriterion( $this->_criteria );
		if( $newCriterion ){
			if( $this->_normal_criterion ){
				if( $this->_allIsOr || $search->getIsOr() ){
					$this->_normal_criterion->addOr( $newCriterion );
				}else{
					$this->_normal_criterion->addAnd( $newCriterion );
				}			
			}else{
				$this->_normal_criterion = $newCriterion;
			}			
		}
		

	}
	/**
	 * 组
	 * jute
	 */
	public function addSearchGroup($item=null,$cond=null){

		$array = $this->getSearchgroup();
		$sql = $this->getSql();	
		$duizhaobiao=$this->splitsql($sql);
		if(is_array($array) && count($array)){
			$i = 1;
			foreach ($array as $key=>$val){
				if(is_array($val['item']) && count($val['item'])){
					$j = 1;
					foreach ($val['item'] as $key1=>$val1){
						if ($j == 1){							
							$isviewandor = "第".$i."组";
						}else{
							$isviewandor = "Y";
						}
						$search = new Watt_Util_Grid_Search( $key1
		                                        , null
		                                        , null
		                                        , null
		                                        , null
		                                        , null
		                                        , 0
		                                        , null
		                                        , null
	                        					, $cond
	                        					, $item
	                        					, $isviewandor
		                                        );
		                $searchViewName = self::SEARCH_NAME_PREFIX . count( $this->_searchs );	
						$search->setViewName( $searchViewName );
				
						//setSearch的同时就从request获取一下数据
						if( $this->_advMode && $this->_advSearchValue ){
							//如果是高级搜索，则优先使用高级搜索
							$search->setValue( $this->_advSearchValue );
							//exit();
						}elseif( isset( $_REQUEST[$searchViewName] ) ){
							$search->setValue( $_REQUEST[$searchViewName] );
						}
						
						//设置and or
						if (isset($_REQUEST['isor_'.$searchViewName]) && $_REQUEST['isor_'.$searchViewName]){
							$search->setIsOr(true);
						}
						//设置搜索项 jute 20080409
						$theitem =$search->getItem();
//						$thenewitem = array();
//						if(is_array($theitem[0]) && count($theitem[0])){
//							foreach ($theitem as $key=>$val){
//								$thenewitem[key($val)] = current($val);						
//							}
//							$theitem = $thenewitem;
//						}
							
						if(is_array($theitem) && count($theitem) && isset( $_REQUEST['item_'.$searchViewName] ) ){	
							
							$search->setdbColName($_REQUEST['item_'.$searchViewName]);
							if($theitem[$_REQUEST['item_'.$searchViewName]]){
								$search->setAlias($theitem[$_REQUEST['item_'.$searchViewName]]);
							}else{
								$search->setAlias();
							}			
						}
								
						//设置搜索条件
						if( isset( $_REQUEST['cond_'.$searchViewName] ) ){
							$search->setOprate( $_REQUEST['cond_'.$searchViewName] );
						}
						
						//$groupjone =$search->getGroup();
						$groupjone = $this->getSearchgroup();
						//设置组的and or
						if ($j == 1){
							if(isset($_REQUEST['groupjone_'.$i])){
								if($_REQUEST['groupjone_'.$i] == 'and'){
									$groupjone[$i]['join'] = 'and';									
								}else if($_REQUEST['groupjone_'.$i] == 'or'){
									$groupjone[$i]['join'] = 'or';
								}
								//$itemarr = $groupjone;
								//$search->setGroup($groupjone);
								$this->setSearchgroup($groupjone);
							}
						}
						
						$this->_searchs[] = $search;
						$thenewsearch = "";
						
						if($search->getValue()){						
							if(array_key_exists( $search->getDbColName(),$duizhaobiao))
							{
								$dbcolname=$duizhaobiao[$search->getDbColName()];
							}
							else 
							{
								$dbcolname= $search->getDbColName();
							}
							if($search->getOprate() == Watt_Util_Grid_Searchs::LIKE){
								//$thenewsearch = $search->getDbColName()." ".$search->getOprate()." '%".$search->getValue()."%'";
								$thenewsearch = $dbcolname." ".$search->getOprate()." '%".$search->getValue()."%'";
							}else {
								//$thenewsearch = $search->getDbColName().$search->getOprate()."'".$search->getValue()."'";
								$thenewsearch = $dbcolname.$search->getOprate()."'".$search->getValue()."'";
							}
						}
						
						if($thenewsearch){
							if(isset($this->_group_criterion[$key]) && $this->_group_criterion[$key]){								
								if($search->getIsOr()){
									$this->_group_criterion[$key] .= ' or '.$thenewsearch; 
								}else{
									$this->_group_criterion[$key] .= ' and '.$thenewsearch; 
								}
							}else{
								$this->_group_criterion[$key] = $thenewsearch;
							}
						}
//						$newCriterion = $search->toCriterion( $this->_criteria );
//						if( $newCriterion ){
//							if( $this->_normal_criterion ){
//								if( $this->_allIsOr || $search->getIsOr() ){
//									$this->_normal_criterion->addOr( $newCriterion );
//								}else{
//									$this->_normal_criterion->addAnd( $newCriterion );
//								}			
//							}else{
//								$this->_normal_criterion = $newCriterion;
//							}			
//						}
						
						
		               $j++;
					}
				}
				$i++;
			}
		}
		
		
		//组合查询			
		$search = $this->_searchs;
				
	
		$itemarr  = $this->getSearchgroup();
		$allcond = '';
		if(is_array($this->_group_criterion) && count($this->_group_criterion)){
			foreach ($this->_group_criterion as $key =>$val){
				if($val){
					$val = '('.$val.')';
					if($allcond){
						if($itemarr[$key]['join'] == 'and'){
							$allcond .= ' and '.$val;
						}else if($itemarr[$key]['join'] == 'or'){
							$allcond .= ' or '.$val;
						}
					}else{
						$allcond = $val;
					}
				}
			}
		}
		
		if($sql && $allcond){
			
			if( strpos($sql, self::SQL_WHERE_STANDER ) ){
				$sql =  str_replace( self::SQL_WHERE_STANDER, ' and ('.$allcond.')', $sql );
			}else{
				if( strpos( $sql, 'where ' ) === false ){
					$sql = $sql. ' where ('. $allcond .')';
				}else{
					$sql = $sql. ' and ('. $allcond .')';
				}
			}
			$this->setSql($sql);
		}	
		
	}
	private function splitsql($sql)
	{
		if(strstr($sql,'from'))
		{
			$sql=explode('from',$sql);
		}
		else if(strstr($sql,'FROM'))
		{
			$sql=explode('FROM',$sql);
		}
		$sql=preg_replace("/\r|\n/",'',$sql[0]);
		$pp=preg_split ("/select|,/i",$sql);
		$temp=array();
		foreach ($pp as $v)
		{
			$v=trim($v);
			$pp1=preg_split ("/\sas\s/i",$v);
			
			
			if(is_array($pp1)&&count($pp1)>1)
			{
				$temp[trim($pp1[1])]=trim($pp1[0]);
			}
			
		}
		return $temp;
		
	}
	
	/**
	 * 高级模式
	 * @var Boolean
	 */
	private $_advMode = false;
	private $_advSearchValue;

	/**
	 * 开启高级模式
	 * 开启高级模式一定在 addSearch 之前，否则无效
	 */
	public function enableAdvSearch(){
		$this->_advMode = true;
		$this->_allIsOr = true;
		$adv_search_value = r(self::SEARCH_ADV_SIGN);
		if( $adv_search_value ){
			$this->_advSearchValue = $adv_search_value;
		}
	}

	/**
	 * 关闭高级模式
	 */
	public function disableAdvSearch(){
		$this->_advMode = false;
	}
	
	/**
	 * 增加默认的搜索条件
	 *
	 * @param string|Criterion $dbColName
	 * @param string $value
	 * @param Watt_Util_Grid_Searchs::Const $type
	 */
	public function addHiddenSearch( $dbColName
                                   , $value = null
                                   , $comparison = self::EQUAL
                                   , $isOr = false
		                           )
	{	
		/**
		 * 这样将覆盖同 $dbColName 的条件
		 */
		//$this->_criteria->add( $dbColName, $value, $type );
		
		/**
		 * 这样将不覆盖同 $dbColName 的条件
		 */
		if( $dbColName instanceof Criterion ){
			$newCriterion = $dbColName;
		}else{
			$newCriterion = $this->_criteria->getNewCriterion( $dbColName, $value, $comparison );
		}
		if( $this->_hidden_criterion ){
			//$this->_hidden_criterion->addAnd( $newCriterion );	// _hidden_criterion 都必须是 and //错误判断，不仅仅如此
			if( $isOr ){
				$this->_hidden_criterion->addOr( $newCriterion );
			}else{
				$this->_hidden_criterion->addAnd( $newCriterion );
			}
		}else{
			$this->_hidden_criterion = $newCriterion;
		}
	}
	
	/**
	 *
     * @param column String full name of column (for example TABLE.COLUMN).
     * @param mixed $value
     * @param string $comparison
     * @return Criterion
	 */
	public function getNewCriterion( $dbColName
                                   , $value = null
                                   , $comparison = self::EQUAL ){
		return $this->_criteria->getNewCriterion( $dbColName, $value, $comparison );
	}
	
	public function addAscendingOrderByColumn( $dbColName ){
		$this->_criteria->addAscendingOrderByColumn( $dbColName );
	}
	
	public function addDescendingOrderByColumn( $dbColName ){
		$this->_criteria->addDescendingOrderByColumn( $dbColName );
	}

	/**
	 * 设置缺省排序字段
	 *
	 * @param string $dbColName
	 * @param string $order
	 */
	public function setDefaultOrderBy( $dbColName, $order=self::ASC ){
		$this->_defaultOrderBy[] = $dbColName;
		$this->_defaultOrderyByOrder[] = $order;
	}
	
	/**
	 * 设置grid 的pagesize
	 * 
	 * 注意：会被 $_REQUEST["pz"] 的值覆盖
	 * 
	 * @param integer $pagesize
	 * @return boolean
	 */
	public function setPageSize( $pagesize )
	{			
		if( $this->_pager )
		{
			//以传递的参数为先 jute 20080417
			if(isset($_REQUEST[Watt_Util_Pager::PAGER_VAR_PAGE_SIZE ]) && $_REQUEST[Watt_Util_Pager::PAGER_VAR_PAGE_SIZE ]){
				$this->_pager->setPageSize( $_REQUEST[Watt_Util_Pager::PAGER_VAR_PAGE_SIZE ] );
			}else{
				$this->_pager->setPageSize( $pagesize );
			}			
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	/**
	 * 自动从 request 中获取参数
	 * 
	 * 包括 search 的值
	 * order by 的值
	 * pager 的值
	 *
	 */
	public function autoGetRequest(){
		/**
		 * 获得条件信息 在addSearch里 有时间再考虑是否妥当！！
		 * 获得条件信息
		 */
		//for ( $i = 0;$i<count( $this->_searchs );$i++ )
		//{
		//	if( isset( $_REQUEST[self::SEARCH_NAME_PREFIX.$i] ) ){
		//		$this->_searchs[$i]->setValue( $_REQUEST[self::SEARCH_NAME_PREFIX.$i] );
		//	}
		//}		
		
		/**
		 * 获得排序信息
		 */
		$orderBy      = @$_REQUEST[self::DEF_ORDERBYS];
		$orderByOrder = @$_REQUEST[self::DEF_ORDERBYORDERS];
		
		if( $orderBy )
		{
			$this->_addOrderByToSearch( $orderBy, $orderByOrder );
		}else{
			if( $this->_defaultOrderBy ){
				for( $i=0;$i<count($this->_defaultOrderBy);$i++){
					$orderBy = $this->_defaultOrderBy[$i];
					$orderByOrder = $this->_defaultOrderyByOrder[$i];

					$this->_addOrderByToSearch( $orderBy, $orderByOrder );
				}				
			}
		}
		
		
		/**
		 * 获得分页信息
		 */
		if( $this->_usePager && ($this->_omPeerName || $this->_sql) && $this->_pager instanceof Watt_Util_Pager ){
			//获得分页信息
			if( !$this->_pager->getTotal() ){
				if( $this->_parsed_sql ){
					$this->_pager->setTotal( $this->_getCountWithSql() );
				}else{
					$omPeerName = $this->_omPeerName;
					eval( "\$this->_pager->setTotal( $omPeerName::".$this->_countMethod."( \$this->_criteria, \$this->_con, \$this->_otherParam) );");				
				}
			}

			//这里应自动获取页码
			//自动获取页码 已经在pager里了
			//$pager->setPageNum( $this->getInputParameter( Watt_Util_Pager::PAGER_VAR_PAGE_NUM ) );
			//$pager->setPageSize( 3 );
			//if( isset( $_REQUEST[Watt_Util_Pager::PAGER_VAR_PAGE_NUM] ) ){
			//	$this->_pager->setPageNum( $_REQUEST[Watt_Util_Pager::PAGER_VAR_PAGE_NUM] );
			//}
					
			$this->_criteria->setOffset( $this->_pager->getPageStart() - 1 );
			$this->_criteria->setLimit( $this->_pager->getPageSize() );
		}
	}
	
	/**
	 * 这是重构出来的方法
	 *
	 * @param string $orderBy
	 * @param string $orderByOrder
	 */
	private function _addOrderByToSearch( $orderBy, $orderByOrder ){
		$orderByOrder = ($orderByOrder ==  self::DESC)?self::DESC:self::ASC;
		if( $orderByOrder == self::DESC ){
			$this->_criteria->addDescendingOrderByColumn( $orderBy );
		}else{
			$this->_criteria->addAscendingOrderByColumn( $orderBy );
		}
		$this->_orderBys[]      = $orderBy;
		$this->_orderByOrders[] = $orderByOrder;
	}
	
	/**
	 * 
	 * @param string $omPeerName
	 */
	public function setOmPeerName( $omPeerName ){
		$this->_omPeerName = $omPeerName;
	}
	
	/**
	 * 根据条件生成一个 Criteria 对象
	 *
	 * @return Criteria
	 */
	public function toCriteria(){
		/**
		 * 注意，此项要在$this->autoGetRequest()前，否则会影响分页的数据
		 */
		if( $this->_normal_criterion ){
			$this->_criteria->addAnd( $this->_normal_criterion );
		}
		
		if( $this->_hidden_criterion ){
			$this->_criteria->addAnd( $this->_hidden_criterion );
		}
		//-------------------
		
		if( $this->_sql ){
			//将 $this->_criteria 并入到 sql 里
			//print"<pre>Terry :";var_dump( $this->_criteria );print"</pre>";
			$this->_parseSql();
			//print"<pre>Terry :";var_dump( $this );print"</pre>";
			//exit();
			//$this->_hidden_criterion->get
			//exit();
		}
		
		//再自动一些
		//对！ 生成条件的时候再读取条件，呵
		$this->autoGetRequest();

		return $this->_criteria;
	}
	
	public function getParsedSql(){
		$this->_parseSql();
		$this->autoGetRequest();
		return $this->_parsed_sql;
	}
	
	private function _parseSql(){
		$where = '';
		
		$maps =   $this->_criteria->getMap();
		foreach ( $maps as $aCriterion ){		
			
			$partWhere = $this->_createSqlFromCriterion( $aCriterion );
			if( $where ){
				//$andOr = $this->_allIsOr?'or':'and';	//$this->_allIsOr 已经在 _criteria 生成的时候就完成了
				//$where = '('. $where. " $andOr ".trim($table.$map->getColumn(),'.').$map->getComparison().$value .')';
				$where = '('. $where. " ) and ". $partWhere;
			}else{
				$where = $partWhere;
			}
		}
		//var_dump( $where );
		$isHaveWhere = false;
		
		/*
		if( strpos( $this->_sql, 'where' ) && preg_match( "/where .* \)\$/i", $this->_sql ) ){
			$isHaveWhere = true;
		}*/
		
		if( $where ){
			if( strpos( $this->_sql, self::SQL_WHERE_STANDER ) ){
				$this->_parsed_sql =  str_replace( self::SQL_WHERE_STANDER, ' and ('.$where.')', $this->_sql );
			}else{
				if( strpos( $this->_sql, 'where ' ) === false ){
					$this->_parsed_sql = $this->_sql. ' where ('. $where .')';
				}else{
					$this->_parsed_sql = $this->_sql. ' and ('. $where .')';
				}
			}
		}else{
			$this->_parsed_sql = str_replace( self::SQL_WHERE_STANDER, '', $this->_sql );
			//$this->_parsed_sql = $this->_sql;
		}
		
		/*
		$orderBy = $criteria->getOrderByColumns();
		$groupBy = $criteria->getGroupByColumns();
		$ignoreCase = $criteria->isIgnoreCase();
		$select = $criteria->getSelectColumns();
		$aliases = $criteria->getAsColumns();
		
		var_dump( $orderBy );
		var_dump( $groupBy );
		var_dump( $ignoreCase );
		var_dump( $select );
		var_dump( $aliases );
		*/
	}
	
	private function _createSqlFromCriterion( Criterion $aCriterion ){
		//var_dump( $aCriterion );
		//$this->_hidden_criterion->getTable()
//		echo $aCriterion->getColumn();
//		echo $aCriterion->getValue();
//		echo $aCriterion->getComparison();
//		var_dump( $aCriterion->getAllTables() );
//		var_dump( $aCriterion->getAttachedCriterion() );
//		var_dump( $aCriterion->getTable() );
		$table = $aCriterion->getTable()?$aCriterion->getTable().'.':'';
		$value = $aCriterion->getValue();
		if( is_array( $value ) ){
			foreach ( $value as $key => $val ) {
				$value[$key] = "'".chks($val)."'";
			}
			if( count( $value ) ){
				$value = '('.implode( ',', $value ).')';
			}else{
				$value = '( NULL )';
			}
		}else{
			if( !is_null($value) && !is_numeric( $value ) )$value = "'".chks($value)."'";			
		}
		
		if( is_null($value) ){
			if( self::EQUAL == $aCriterion->getComparison() || self::ISNULL == $aCriterion->getComparison() || self::IN == $aCriterion->getComparison() ){
				$partWhere = trim($table.$aCriterion->getColumn(),'.')." IS NULL";
			}else{
				$partWhere = trim($table.$aCriterion->getColumn(),'.')." IS NOT NULL";
			}
		}else{
			$partWhere = trim($table.$aCriterion->getColumn(),'.').$aCriterion->getComparison().$value;
		}
		
		$clauses = $aCriterion->getClauses();
		if( is_array( $clauses ) && count( $clauses ) ){
			$conjunctions = $aCriterion->getConjunctions();
			for( $i = 0; $i < count( $clauses ); $i++ ){
				$partWhere = '('.$partWhere.')'.$conjunctions[$i].$this->_createSqlFromCriterion($clauses[$i]);
			}
		}
		
		return $partWhere;
	}
	
	private function _processWhereClauses(){
		
	}
	
	/**
	 * 得到 Searchs 的标准的数组定义
	 *
	 */
	public function toArray(){
		$rev = array();
		$searchs = array();
		for ( $i = 0;$i<count( $this->_searchs );$i++ )
		{
			
			$this->_searchs[$i]->setViewName( self::SEARCH_NAME_PREFIX.$i );
			$searchArr = $this->_searchs[$i]->toArray();
			if($this->_viewandor){
				$searchArr['isviewandor'] = true;
			}
			//如果是高级模式，且高级搜索有值，则每一个条件的值设为空
			if( $this->_advMode && $this->_advSearchValue ){
				$searchArr[Watt_Util_Grid_Search::DEF_VALUE] = '';
			}
			$searchs[] = $searchArr;
			
			/*if( isset( $_REQUEST[self::SEARCH_NAME_PREFIX.$i] ) ){
				$this->_searchs[$i]->setValue( $_REQUEST[self::SEARCH_NAME_PREFIX.$i] );
			}*/
		}
		//$rev[self::DEF_SEARCHS]   = $searchs;
		$rev[self::DEF_SEARCHS]   = array(
									self::DEF_SEARCHS => $searchs,
									self::DEF_INITPARAMS => $this->_initParams,
		);
		//$rev[self::INIT_PARAM_NAME] = $this->_initParams;

		//如果是高级模式，则有 self::SEARCH_ADV_SIGN 这个key出现
		if( $this->_advMode ){
			$rev[self::DEF_SEARCHS][self::SEARCH_ADV_SIGN] = $this->_advSearchValue;
		}
		 
		$rev[self::DEF_ORDERBYS]  = array( self::DEF_ORDERBYS      => $this->_orderBys
		                                 , self::DEF_ORDERBYORDERS => $this->_orderByOrders
		                                 );
		/*
		$rev[self::DEF_ORDERBYS]      = $this->_orderBys;
		$rev[self::DEF_ORDERBYORDERS] = $this->_orderByOrders;
		*/
		if( $this->_usePager ){
			$rev[self::DEF_PAGERINFO] = $this->_pager->toArray();	
		}

		return $rev;
	}
	
	/**
	 * 运行并且返回符合 Grid 定义的数据
	 *
	 * @return array
	 */
	public function excuteAndReturnGridData( $expressMode = false ){
		
		return $this->excuteAndReturnGrid( $expressMode )->toArray();
	}
	
	/**
	 * @param boolean $expressMode
	 * @return Watt_Util_Grid
	 */
	public function excuteAndReturnGrid( $expressMode = false ){
		//支持导出报表，如果是导出报表，设置为不使用分页		
		if(isset($_REQUEST['searchFormExport']) && $_REQUEST['searchFormExport']){//导出报表
			$this->_usePager = false;
		}
//			if($this->_sql && isset($_REQUEST['searchFormExport']) && $_REQUEST['searchFormExport']){//导出报表
//				//导出报表
//				$file=Watt_Util_Export::ExporttoCsv($datas,"baobiao");
//				if($file){
//					$this->_exportfile = $file;
//				}
//			}

		Watt_Db::startUseReadonlyDb();	// 设置“开始使用只读数据库服务器”。Watt_Db 和 Propel 在执行操作时将检测此设置，如果“使用只读数据库”则强制连接到只读数据库服务器	
		
		
		try {
			
			if( $this->_sql ){
//				if($this->_viewandor){
//					
//					print "<pre>";
//					print_r($this->_searchs);
//					print "</pre>";
//				}
				
				$this->toCriteria();//这是为了执行 autoGetRequestVar
				$datas = $this->_getAllWithSql();
			}elseif( $this->_omPeerName ){
				if( $expressMode ){
					$datas = Watt_Util_Array::doSelectPeerToArray($this->_omPeerName, $this->toCriteria(), $this->_con );				
				}else{
					eval( "\$datas = Watt_Util_Array::toArray(".$this->_omPeerName."::".$this->_selectMethod."(\$this->toCriteria(), \$this->_con, \$this->_otherParam));" );
				}
			}

			if( isset( $datas ) && is_array( $datas ) ){
				/**
				 * 增加小计
				 * @author terry
				 * Tue Jun 12 10:49:46 CST 2007
				 */
				if( is_array( $this->_sumCols ) && count( $this->_sumCols ) ){
					$rowSum = current( $datas );
					if( is_array( $rowSum ) ){
						foreach ( $rowSum as $key => $val ) {
							$rowSum[$key] = null;
						}
						
						for( $i = 0; $i < count( $datas ); $i++ ){
							foreach ( $this->_sumCols as $colName ){
								$rowSum[ $colName ] += $datas[$i][$colName];
							}
						}
						
						if( $this->_sumFormat ){
							foreach ( $this->_sumCols as $colName ) {
								$rowSum[$colName] = sprintf($this->_sumFormat, $rowSum[$colName]);
							}							
						}
					}
					$datas[self::DEF_SUM] = $rowSum;
				}
				
				/**
				 * 增加合计
				 * 只有Sql模式才提供合计
				 * @author terry
				 * Tue Jun 12 10:49:54 CST 2007
				 */
				if( $this->_sql && is_array( $this->_totalCols ) && count( $this->_totalCols ) ){
					$rowTotal = current( $datas );
					if( is_array( $rowTotal ) ){
						foreach ( $rowTotal as $key => $val ) {
							$rowTotal[$key] = null;
						}

						$totalCols = "";
						foreach ( $this->_totalCols as $colName ){
							$colName1 = str_replace('+','_tpma',$colName);
							$colName1 = str_replace('-','_tpmb',$colName1);
							$colName1 = str_replace('*','_tpmc',$colName1);
							$colName1 = str_replace('/','_tpmd',$colName1);
							$totalCols .= "sum($colName) as $colName1,";
						}
						$totalCols = trim( $totalCols, ',' );
						
						$totalSql = str_replace( "count(*)", $totalCols, $this->_getCountSql() );
						
						$rowTotalFromSelect = Watt_Db::getDb()->getRow( $totalSql );
						foreach ( $this->_totalCols as $colName ){
							$colName1 = str_replace('+','_tpma',$colName);
							$colName1 = str_replace('-','_tpmb',$colName1);
							$colName1 = str_replace('*','_tpmc',$colName1);
							$colName1 = str_replace('/','_tpmd',$colName1);
							$rowTotal[ $colName ] = @$rowTotalFromSelect[$colName1];
						}

						if( $this->_totalFormat ){
							foreach ($this->_totalCols as $colName) {
								$rowTotal[$colName] = sprintf($this->_totalFormat,$rowTotal[$colName]);
							}							
						}
					}					
					$datas[self::DEF_TOTAL] = $rowTotal;
				}
			}else{
				//如无 data ，则也无需小计合计
				$datas = array();
			}
			
			$grid = new Watt_Util_Grid( $datas );
			$searchsArray = $this->toArray();
			//增加分组数据
			if( $this->_sql && is_array( $this->_searchgroup ) && count( $this->_searchgroup ) ){
				//$searchsArray[self::DEF_SEARCHGROUP] = $this->_searchgroup;
				//$grid->setSearchgroup($this->_searchgroup);
				$searchsArray[self::DEF_SEARCHS][self::DEF_SEARCHGROUP] = $this->_searchgroup;
			}
			//增加导出报表标识
			
			//生成报表,设置下载
//			if($this->_sql && isset($_REQUEST['searchFormExport']) && $_REQUEST['searchFormExport']){//导出报表
//				//导出报表
//				$file=Watt_Util_Export::ExporttoCsv($datas,"baobiao");
//				if($file){
//					$this->_exportfile = $file;
//				}
//			}
			
			$grid->setSearchs( $searchsArray[self::DEF_SEARCHS] );
			$grid->setOrderBys( $searchsArray[self::DEF_ORDERBYS] );
			if( $this->_usePager )$grid->setPager( $searchsArray[self::DEF_PAGERINFO] );
			if($this->_export)$grid->setExport($this->_export);			
			if($this->_exportfile)$grid->setExportfile($this->_exportfile);
			if($this->_exportfile_name)$grid->setExportfileName($this->_exportfile_name);
			if($this->_export_col)$grid->setExportCol($this->_export_col);
			if($this->_export_format)$grid->setExportFormat($this->_export_format);
			Watt_Db::endUseReadonlyDb();	// 设置“停止使用只读数据库服务器”
		}
		catch ( Exception $e )
		{
			Watt_Db::endUseReadonlyDb();
			throw $e;
		}
		
		return $grid;
	}
	
	/**
	 * 根据 Sql 获得全部数组
	 *
	 */
	private function _getAllWithSql(){
		//在此时再 order by ，是为了不浪费 count 的时间
		//var_dump( $this->_orderBys );
		//var_dump( $this->_orderByOrders );
		
		$orderBySql = '';
		foreach ( $this->_orderBys as $orderBy ){
			if( !$orderBySql ){
				$orderBySql = ' order by ';
			}else{
				$orderBySql .= ',';
			}
			$orderBySql .= $orderBy.' '.current( $this->_orderByOrders );
			next( $this->_orderByOrders );
		}
		if( $orderBySql ){
			$this->_parsed_sql .= $orderBySql;
		}
		
		//$newsql = $this->_parsed_sql;
		//$newsql .= " limit ".$this->_pager->getPageSize()*max($this->_pager->getPageNum()-1,0).",".$this->_pager->getPageSize();
		if( $this->_usePager ){
			$limitSql = " limit ".$this->_pager->getPageSize()*max($this->_pager->getPageNum()-1,0).",".$this->_pager->getPageSize();			
		}else{
			$limitSql = '';
		}
		if( strpos( $this->_parsed_sql, self::SQL_LIMIT_STANDER ) ){
			$newsql = str_replace( self::SQL_LIMIT_STANDER, $limitSql, $this->_parsed_sql );
		}else{
			$newsql = $this->_parsed_sql.$limitSql;
		}
//		echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Thu Aug 02 21:19:30 CST 2007] :\n ";
//		var_dump( $newsql );
//		echo "</pre>";
//		exit();
		
		//print $newsql;
		
		$data = Watt_Db::getDb()->getAll( $newsql );
		return $data;
	}
	
	private function _getCountWithSql(){
		return Watt_Db::getDb()->getOne( $this->_getCountSql() );
	}
	
	private function _getCountSql(){
		if( !$this->_count_sql ){
			$parsed_sql = str_replace( "\n", " ", $this->_parsed_sql );
			if( stripos( $parsed_sql, 'group by' ) ){
				$countSql = "select count(*) from (".$this->_parsed_sql.") as a";
			}/*elseif( preg_match("/(select[\\s]+)([\\s\\S]*)([\\s]+from .*)/i", $parsed_sql, $match ) ){
				//var_dump( $match );
				$countSql = $match[1].' count(*) '.$match[3];
				$countSql = str_replace( self::SQL_LIMIT_STANDER, '', $countSql );
				//echo $countSql;
				//用下边的方式基本足够了
			}*/else{
				$countSql = 'select count(*) from '.substr($this->_parsed_sql,stripos($this->_parsed_sql,'from')+4);
				$countSql = str_replace( self::SQL_LIMIT_STANDER, '', $countSql );			
				//echo $this->_parsed_sql;
				//throw( new Exception('Sql is no select sql!') );
			}
			$this->_count_sql = $countSql;
		}
		return $this->_count_sql;
	}
	
	/**
	 * @param array $colArray
	 */
	public function setExportCol( $colArray ){
		$this->_export_col = $colArray;
	}
	/*
	* @说明：默认数据的导出格式
	* @参数：
	* @返回:
	* @作者：John
	* @时间：Tue Apr 27 09:31:48 CST 2010
	*/
	public function setExportFormat($eFormat)
	{
		$this->_export_format=$eFormat;
	}
}

class Watt_Util_Grid_Search
{
	const SHOW_TYPE_TEXT = 0;
	const SHOW_TYPE_DATE = 1;
	const SHOW_TYPE_TIMESTAMP = 2;
	const SHOW_TYPE_TIMESEC=3;
	const SHOW_TYPE_SELECTOR_PERSON = 1024;
	const SHOW_TYPE_SELECTOR_DEPARTMENT = 1025;
	const SHOW_TYPE_SELECTOR_GROUP = 1026;
	const SHOW_TYPE_SELECTOR_DINGDAN = 1027;
	const SHOW_TYPE_SEARCHTIP = 1028;

	//const SEARCH_TYPE_PRE  = 2;
	
	const DEF_COLNAME   = 'colname';
	const DEF_TITLE     = 'title';
	const DEF_OPERATION = 'operation';
	const DEF_VALUE     = 'value';
	const DEF_ISRANGE   = 'isrange';
	const DEF_REFERENCE = 'reference';
	const DEF_ISOR = 'isor';
	const DEF_SHOWTYPE  = 'showtype';
	const DEF_EXTEND = 'extend';	
	const DEF_COND = 'cond';	
	const DEF_ITEM = 'item';
	const DEF_ISVIEWANDOR = 'isviewandor';
	
	private $_dbColName;
	private $_alias;
	private $_oprate;
	private $_value;
	private $_rels;
	private $_isOr;
	private $_viewName;
	private $_showType = self::SHOW_TYPE_TEXT;		//显示类型 默认为 0 
	private $_customTransformFunc;
	private $_extend;
	private $_cond;
	private $_item;
	private $_isviewandor;
	
	public function setCustomTransformFunc( $v ){$this->_customTransformFunc = $v;}
	public function getCustomTransformFunc(){return $this->_customTransformFunc;}
	
	public function setShowType( $v ){$this->_showType = $v;}
	public function getShowType(){return $this->_showType;}
	
	function __construct( $dbColName = null
                        , $value     = null
                        , $type      = Watt_Util_Grid_Searchs::LIKE
                        , $alias     = null
                        , $rels      = null
                        , $isOr      = null
                        , $showType  = self::SHOW_TYPE_TEXT
                        , $customTransformFunc = null
						, $extend = null
						, $cond = null
						, $item = null
						, $isviewandor = null
                        )
	{
		$this->_dbColName = $dbColName;
		$this->_value     = $value;
		$this->_oprate      = $type?$type:Watt_Util_Grid_Searchs::LIKE;
		//$this->_alias     = $alias?$alias:Watt_I18n::trans($dbColName);
		$this->setAlias( $alias );
		$this->_rels      = $rels;
		$this->_isOr      = $isOr;
		$this->_showType  = $showType?$showType:self::SHOW_TYPE_TEXT;
		$this->_customTransformFunc = $customTransformFunc;
		$this->_extend = $extend;
		$this->_cond = $cond;
		$this->_item = $item;
		$this->_isviewandor = $isviewandor;
	}
	
	function toArray()
	{
		$rev = array();
		$rev[self::DEF_TITLE]     = $this->_alias;
		//这里小心 : 这里用了viewName！这是为了不暴露名称
		$rev[self::DEF_COLNAME]   = $this->_viewName;
		$rev[self::DEF_OPERATION] = $this->_oprate;
		$rev[self::DEF_VALUE]     = $this->_value;
		$rev[self::DEF_ISRANGE]   = 0;
		$rev[self::DEF_REFERENCE] = $this->_rels;
		$rev[self::DEF_ISOR] = $this->_isOr;
		$rev[self::DEF_SHOWTYPE]  = $this->_showType;
		$rev[self::DEF_EXTEND]    = $this->_extend;
		$rev[self::DEF_COND]    = $this->_cond;
		$rev[self::DEF_ITEM]    = $this->_item;
		$rev[self::DEF_ISVIEWANDOR] = $this->_isviewandor;
		return $rev;
	}
	
	function getIsOr(){
		return $this->_isOr;
	}
	function getItem(){
		return $this->_item;
	}
	function getDbColName(){
		return $this->_dbColName;
	}
	
	function setValue( $v ){
		//if( $this->_oprate == Criteria::LIKE && strpos( $v, "%" ) === false ) $v = "%".$v."%";
		$this->_value = $v;
	}
	
	function getValue(){
		return $this->_value;
	}
	
	function setOprate($v){
		$this->_oprate = $v;
		
	}	
	function getOprate(){
		return $this->_oprate;		
	}
	
	function setViewName( $v ){
		$this->_viewName = $v;
	}
	
	function setIsOr($v){
		$this->_isOr = $v;
	}
	
	function setAlias( $alias ){
		if( !$alias ){
			//因为数据库字段的 语言键值是小写的，所以用 strtolower转化一下
			$alias = strtolower( preg_replace( "/^[\\w]+\\./", "", $this->_dbColName ) );
			$alias = Watt_I18n::trans($alias);
		}
		$this->_alias = $alias;
	}
	
	function setdbColName( $name ){
		$this->_dbColName = $name;		
	}
	/**
	 * 将自己转化为一个 Criterion
	 *
	 * @param Criteria $c
	 */
	function toCriterion( Criteria $c ){
		//这里是规定，如果值为空(null)，则不生成条件
		/**
		 * 这是为了 0 的情况
		 * @author terry
		 * @version 0.1.0
		 * Fri Aug 24 16:39:03 CST 2007
		 */
		if( !( is_null( $this->_value ) || $this->_value === '' ) ){
			if( $this->_customTransformFunc ){
				eval( "\$v = ".$this->_customTransformFunc."( \$this->_value );" );
			}else{
				$v = $this->_value;
			}
			switch ( $this->_showType ){
				case self::SHOW_TYPE_TIMESTAMP:
				case self::SHOW_TYPE_TIMESEC:
				case self::SHOW_TYPE_DATE:
					//如果是时间戳，则转换为整数
					if( !is_int( $v ) ) $v = strtotime( $v );					
					break;
				default:
					if( !is_array( $v ) ){
						//对like 进行一些处理
						$v =( $this->_oprate == Watt_Util_Grid_Searchs::LIKE && strpos( $v, "%" ) === false )?"%".$v."%":$v;						
					}
			}
			
			return $c->getNewCriterion( $this->_dbColName, $v, $this->_oprate );			
		}else{
			return null;
		}
	}
}