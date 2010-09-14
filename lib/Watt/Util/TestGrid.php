<?
/**
 * 构造一个grid
 * 可以生成一个
 * grid 的数组
 *
 * @author Tony
 * @package Watt_Util
 */

class Watt_Util_TestGrid
{
	const GRID_SCHEMA_SEARCHS = "searchs";
	const GRID_SCHEMA_COLS    = "cols";
	const GRID_SCHEMA_DATAS   = "datas";
	const GRID_SCHEMA_PAGER   = "pager";

	protected $_gridData = array();
	
	function __construct( $data = null )
	{
		$this->_gridData[self::GRID_SCHEMA_SEARCHS] = array();
		$this->_gridData[self::GRID_SCHEMA_COLS]    = array();
		$this->_gridData[self::GRID_SCHEMA_DATAS]   = array();
		$this->_gridData[self::GRID_SCHEMA_PAGER]   = array();
		
		if( $data )
		{
			$this->setData( $data );
		}
	}
	
	/**
	 * 添加列的定义
	 *
	 * @param string $title
	 * @param string $colName
	 * @param boolean $sortable
	 * @param string $callback
	 * @param string $linkto
	 * @param boolean $isExt
	 */
	public function addCol( $title
						  , $colname
						  , $sortable=true
						  , $callback = ""
						  , $linkto   = ""
						  , $isext    = false
						  , $render   = ""
						  )
	{
		//这里的变量名是 Grid Schema 的一部分，很重要
		$this->_gridData["cols"][$colname] = compact("title"
					                        ,"colname" 
					                        ,"sortable" 
					                        ,"callback" 
					                        ,"linkto" 
					                        ,"isext"
					                        ,"render"
					                        );
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
		//这种方式仿佛还不如下面的快..
		//$this->_gridData[self::GRID_SCHEMA_DATAS] = $dataArr;
		/*要求输入必须是 array，所以不用一行一行增加了*/
		foreach ( $dataArr as $row )
		{
			if( is_array( $row ) )
			{
				$this->addRow( $row );
			}
			elseif( is_object($row) && method_exists( $row, "toArray" ) )
			{
				$this->addRow( $row->toArray() );
			}
			else
			{
				throw (new Exception("ERR_INVALID_DATA"));
			}
		}
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
	
	public function showMe()
	{
		Watt_View_Helper_TestGrid::buildGrid( $this->getGridData() );
	}
}