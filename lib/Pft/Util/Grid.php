<?
/**
 * 构造一个grid
 * 可以生成一个
 * grid 的数组
 *
 * @author Terry
 * @package Pft_Util
 * @version 0.0.2
 */

class Pft_Util_Grid
{
	const GRID_SEARCHS  = "searchs";
	const GRID_SEARCHGROUP = "searchgroup";
	const GRID_ORDERBYS = "orderbys";
	const GRID_COLS     = "cols";
	const GRID_DATAS    = "grid_datas";
	const GRID_PAGER    = "pager";
	const GRID_EXPORT   = "export";
	const GRID_EXPORTFILE = "exportfile";
	const GRID_EXPORTFILE_NAME = "exportfile_name";
	const GRID_EXPORT_COL = 'export_col';
	const GRID_EXPORT_FORMAT='exportfile_format';
	
	protected $_gridData = array();
	private $_id = "";
	/**
	 * 
	 *
	 * @var Pft_Util_Searchs
	 */
	private $_searchs;

	function __construct( $data = null, $id = "" )
	{
		
//		$this->_gridData[self::GRID_SEARCHS]  = array();
//		$this->_gridData[self::GRID_ORDERBYS] = array();
//		$this->_gridData[self::GRID_COLS]     = array();
//		$this->_gridData[self::GRID_DATAS]    = array();
//		$this->_gridData[self::GRID_PAGER]    = array();

		$this->_gridData[self::GRID_SEARCHS]  = null;
		$this->_gridData[self::GRID_ORDERBYS] = null;
		$this->_gridData[self::GRID_COLS]     = null;
		$this->_gridData[self::GRID_DATAS]    = null;
		$this->_gridData[self::GRID_PAGER]    = null;
		$this->_gridData[self::GRID_EXPORT]    = false;
		$this->_gridData[self::GRID_EXPORTFILE]    = null;
		$this->_gridData[self::GRID_EXPORTFILE_NAME]    = null;
		$this->_gridData[self::GRID_EXPORT_COL]    = null;
		$this->_gridData[self::GRID_EXPORT_FORMAT]    = 'csv';//默认是csv格式

		if( $data instanceof Pft_Util_Grid ){
			$this->setData( $data->getGridData() );
		}else{
			if( $data )
			{
				$this->setData( $data );
			}			
		}
		$this->_id = $id;
	}
	
	const COL_TITLE 	= "title";
	const COL_COLNAME 	= "colname";
	const COL_SORTABLE 	= "sortable";
	const COL_RENDER 	= "render";
	const COL_COLEXT 	= "colext";
	const COL_TAGS		= "coltags";

	/**
	 * 添加列的定义
	 * 
	 * <code>
	 * $grid = new Pft_Util_Grid( $list_data );
	 * $grid->addCol( Pft_I18n::trans("email"),"email", true, '"<a href=\"mailto:".$row["email"]."\">".$row["email"]."</a>"' );
	 * </code>
	 *
	 * @param string $title
	 * @param string $colname
	 * @param string $sortable
	 * @param string $render
	 * @param string $coltags	显示在每一列的TD里的内容,直接给
	 * @param string $colext	显示在标题里的选项
	 */
	public function addCol( $title    = null
						  , $colname  = ""
						  , $sortable = true
//						  , $callback = ""
//						  , $linkto   = ""
//						  , $isext    = false
						  , $render   = ""
						  , $coltags  = ""
						  , $colext   = ""
						  )
	{
							 
		//这里的变量名是 Grid Schema 的一部分，很重要
		$this->_gridData["cols"][$colname] = compact("title"
					                        ,"colname" 
					                        ,"sortable" 
//					                        ,"callback" 
//					                        ,"linkto" 
//					                        ,"isext"
											,"coltags"
					                        ,"render"
					                        ,"colext"
					                        );

											 if(@$_REQUEST['debug_z']){
			print "<pre>";
			print_r($this->_gridData["cols"][$colname]);
			print "</pre>";
							  }
	}

	public function addColNameFirst( $colname  = ""
						  , $title    = null
						  , $sortable = true
//						  , $callback = ""
//						  , $linkto   = ""
//						  , $isext    = false
						  , $render   = ""
						  , $coltags  = ""
						  , $colext   = ""
						  )
	{
		$this->addCol( $title, $colname, $sortable, $render, $coltags, $colext);
	}
	
	/**
	 * 获取Grid数据
	 *
	 * @return array
	 */
	public function getGridData(){
		return $this->_gridData;
	}
	
	/**
	 * 设置一个数据
	 * 要求是二维数组
	 * 此处需要校验一下
	 * 如果不是二维数组，抛出异常
	 *
	 * @param array $dataArr
	 */
	public function setData( $dataArr )
	{
		//$this->_gridData["datas"] = $dataArr;
		if( !is_array( $dataArr ) )
		{
			throw (new Exception("ERR_INVALID_DATA"));
		}
		
		/**
		 * 如果是标准的数据定义，则将各定义复制过来
		 */
		if( key_exists( self::GRID_DATAS, $dataArr ) ){
			/**
			 * @todo 改为直接循环自己的数据数组,将对方的key复制过来
			 */
			/*
			* 说明：本意是为了检测数据正确性,但实际意义不大,所以注释掉了
			* 参数：
			* 作者：John
			* 时间：Tue Sep 23 15:00:35 CST 2008
			*/
//			$datas = $dataArr[self::GRID_DATAS];
//			if( key_exists( self::GRID_SEARCHS, $dataArr ) && is_array( $dataArr[self::GRID_SEARCHS] ) ){
//				$this->_gridData[self::GRID_SEARCHS] = $dataArr[self::GRID_SEARCHS];
//			}
//			if( key_exists( self::GRID_ORDERBYS, $dataArr ) && is_array( $dataArr[self::GRID_ORDERBYS] ) ){
//				$this->_gridData[self::GRID_ORDERBYS] = $dataArr[self::GRID_ORDERBYS];
//			}
//			if( key_exists( self::GRID_COLS, $dataArr ) && is_array( $dataArr[self::GRID_COLS] ) ){
//				$this->_gridData[self::GRID_COLS] = $dataArr[self::GRID_COLS];
//			}
//			if( key_exists( self::GRID_PAGER, $dataArr ) && is_array( $dataArr[self::GRID_PAGER] ) ){
//				$this->_gridData[self::GRID_PAGER] = $dataArr[self::GRID_PAGER];
//			}
//
//			if( key_exists( self::GRID_EXPORT, $dataArr )){
//				$this->_gridData[self::GRID_EXPORT] = $dataArr[self::GRID_EXPORT];
//			}
//			if( key_exists( self::GRID_EXPORTFILE, $dataArr )){
//				$this->_gridData[self::GRID_EXPORTFILE] = $dataArr[self::GRID_EXPORTFILE];
//			}
//			if( key_exists( self::GRID_EXPORT_COL, $dataArr )){
//				$this->_gridData[self::GRID_EXPORT_COL] = $dataArr[self::GRID_EXPORT_COL];
//			}
//			$this->_gridData[self::GRID_DATAS] = $datas;
			
			$this->_gridData = $dataArr;
		}else{
			$datas = $dataArr;
			//这种方式仿佛还不如下面的快..其实几乎没差距
			$this->_gridData[self::GRID_DATAS] = $datas;
		}
		
		/*要求输入必须是 array，所以不用一行一行增加了*/
//		foreach ( $datas as $row )
//		{
//			if( is_array( $row ) )
//			{
//				$this->addRow( $row );
//			}
//			elseif( is_object($row) && method_exists( $row, "toArrayTpm" ) )
//			{
//				$this->addRow( $row->toArrayTpm() );
//			}
//			elseif( is_object($row) && method_exists( $row, "toArray" ) )
//			{
//				$this->addRow( $row->toArray() );
//			}
//			else
//			{
//				throw (new Exception("ERR_INVALID_DATA"));
//			}
//		}
	}
	function setExportFormat($eFormat)
	{
		$this->_gridData[self::GRID_EXPORT_FORMAT]=$eFormat;
	}
	/**
	 * 增加一行
	 *
	 * @param array $arr
	 */
	public function addRow( $arr )
	{
		$this->_gridData["datas"][] = $arr;
	}
	
	/**
	 * 把自己显示出来
	 *
	 * @param $formAttribs form的附加属性
	 */
	public function showMe( $formAttribs="" )
	{
		$out = Pft_View_Helper_Grid::buildGrid( $this->getGridData(), false, $formAttribs );		
		echo $out;
	}
	
	/**
	 *
	 * 用$grid->getOutputArray() 替换 $grid->showMe()
	 * 分段获取grid的输出数据
	 * 
	 * 包括:
	 * $rev[Pft_Util_Grid::GRID_SEARCHS]
	 * $rev[Pft_Util_Grid::GRID_DATAS]
	 * $rev[Pft_Util_Grid::GRID_PAGER]
	 *
	 * @return array
	 */
	public function getOutputArray()
	{
		return Pft_View_Helper_Grid::buildGridToOutArray( $this->getGridData() );
	}

	/**
	 * 将自己的data渲染后输出
	 *
	 * @return array
	 */
	public function getRenderedArray(){

		return Pft_View_Helper_Grid::buildGridToDataArray( $this->getGridData() );
	}
	
	/**
	 * 获得 Grid 的显示结果
	 *
	 */
	public function getGridShows()
	{
		return Pft_View_Helper_Grid::buildGrid( $this->getGridData(), false );
	}
	
	/**
	 * 将所有的内部数据以array的形式输出
	 *
	 * @return array
	 */
	public function toArray(){
		return $this->_gridData;
	}

	public function setSearchs( $searchs ){
		if( is_array( $searchs ) ){
			$this->_gridData[self::GRID_SEARCHS] = $searchs;
		}
	}
	public function setSearchgroup( $v ){
		if( is_array( $v ) ){
			$this->_gridData[self::GRID_SEARCHGROUP] = $v;
		}
	}
	
	public function setPager( $pager ){
		if( is_array( $pager ) ){
			$this->_gridData[self::GRID_PAGER] = $pager;
		}
	}
	public function setExport( $v ){		
		$this->_gridData[self::GRID_EXPORT] = $v;
	}
	public function setExportfile( $v ){		
		$this->_gridData[self::GRID_EXPORTFILE] = $v;
	}
	public function setExportfileName( $v ){
		$this->_gridData[self::GRID_EXPORTFILE_NAME] = $v;
	}
	public function setExportCol( $v ){
		$this->_gridData[self::GRID_EXPORT_COL] = $v;
	}
	public function setOrderBys( $orderBys )
	{
		if( is_array( $orderBys ) ){
			$this->_gridData[self::GRID_ORDERBYS] = $orderBys;
		}
	}
	
	/**
	 * 
	 *
	 * @return Pft_Util_Searchs
	 */
	private function _getSearchs(){
		if( !$this->_searchs ){
			$this->_searchs = new Pft_Util_Searchs();
		}
		return $this->_searchs;
	}
	private function _getSearchgroup(){
		if( !$this->_searchgroup ){
			return null;
		}
		return $this->_searchgroup;
	}
}