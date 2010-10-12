<?
/**
 * 功能：
 * ${TableName} controller
 * 
 * Actions:
 *
 * index
 * add
 * list
 * edit
 * delete
 * 
 * 输入：
 * 
 * 输出：
 * 
 * @author 
 */

/**
 * Template Update
 * @version 1.0.2
 * @author terry
 * Wed Jun 06 14:44:47 CST 2007
 * 增加编辑后返回搜索前的列表
 */
class ${PackageName}${CtrlName}Controller extends Watt_Controller_Action
{
	function indexAction()
	{
		$this->redirectToSelfAction( "list" );
	}
	
	/**
	 * 功能：
	 * 增加一个${TableName}
     * 
	 * 输入：
	 * 
	 * 输出：
	 * 
	 * $${var_name}
	 * 
	 * @author 
	 */
	function addAction()
	{
		$${var_name} = new ${TableName}();
		//此处输出
	
		//自动获得对应名称的输入字段
		//注意删掉不应该接受用户输入的字段
		$${var_name}->autoGetRequestVar( "${fieldList}" );

		if( $op = $this->getInputParameter( "op" ) )
		{
			//此处设置一些其他的值如
			//$${var_name}->${table_name}_reg_time = time();
			$nextUrl = Watt_Session::getSession()->getLastRefererPage()?Watt_Session::getSession()->getLastRefererPage():'?do=${package_name}${ctrl_name}_list';
			if( $${var_name}->save() ){
				$this->addTip( Watt_I18n::trans('OPRATE_SUCCESS'), $nextUrl );
				//$this->redirectToSelfAction( "list" );
			}else{
				$this->addTip( Watt_I18n::trans('OPRATE_FAIL') );
				//保存失败，做其它处理
			}
		}
		Watt_Session::getSession()->recordRefererPage();
		$this->${var_name} = $${var_name}->toArrayTpm();
	}
	
	/**
	 * 功能：
	 * 提供编辑${TableName}所需的数据
	 * 
	 * 输入：
	 * ${pk_name}
	 * 
	 * 输出：
	 * $${var_name}
	 * 
	 * @author 
	 */
	function editAction()
	{
		$${var_name} = ${TableName}Peer::retrieveByPK( $this->getInputParameter( "${pk_name}" ) );
		if( !$${var_name} ) $this->redirectTo404();

		if( $op = $this->getInputParameter( "op" ) )
		{
			//这是在用户修改信息后返回的处理
			
			//自动获得对应名称的输入字段
			//注意删掉不应该接受用户输入的字段			
			$${var_name}->autoGetRequestVar( "${fieldList}" );
			
			if( $${var_name}->save() ){
				$nextUrl = Watt_Session::getSession()->getLastRefererPage()?Watt_Session::getSession()->getLastRefererPage():'?do=${package_name}${ctrl_name}_list';
				$this->addTip( Watt_I18n::trans('OPRATE_SUCCESS'), $nextUrl );
				//$this->redirectToSelfAction( "list" );
			}else{
				$this->addTip( Watt_I18n::trans('OPRATE_FAIL') );
				//没有更新数据，做其它处理
			}
		}
		else
		{
			//这是没有输入op的处理
		}
		Watt_Session::getSession()->recordRefererPage();
		//输出信息
		$this->${var_name} = $${var_name}->toArrayTpm();
	}

	/**
	 * 功能：
	 * 提供显示${TableName}所需的数据
	 * 
	 * 输入：
	 * ${pk_name}
	 * 
	 * 输出：
	 * $${var_name}
	 * 
	 * @author 
	 */
	function detailAction()
	{
		$${var_name} = ${TableName}Peer::retrieveByPK( $this->getInputParameter( "${pk_name}" ) );
		if( !$${var_name} ) $this->redirectTo404();
		//此处输出 detail信息
		$this->${var_name} = $${var_name}->toArrayTpm();
	}
	
	/**
	 * 功能：
	 * 提供${TableName}列表数据
	 * 
	 * 输入：
	 * 
	 * 输出：
	 * $${var_name}s
	 * 
	 * @author 
	 */
	function listAction()
	{
		/**
		 * 下面是使用grid的形式
		 */
		$grid = new Watt_Util_Grid_Searchs( "${TableName}Peer" );
		//$grid->addSearch();
		$this->${var_name}s = $grid->excuteAndReturnGridData();

		/**
		 * 下面是不使用grid的形式
		 */
		//$c = new Criteria();
		//$${var_name}s = ${TableName}Peer::doSelect( $c );
		//$this->${var_name}s = Watt_Util_Array::toArray( $${var_name}s );
	}

	/**
	 * 输入：
	 * ${pk_name}
	 * 
	 * 输出:
	 * 
	 * 如成功，转到 $${var_name}_list
	 */
	function deleteAction()
	{
		$${var_name} = ${TableName}Peer::retrieveByPK( $this->getInputParameter( "${pk_name}" ) );
		if( !$${var_name} ) $this->redirectTo404();
		
		Watt_Session::getSession()->recordRefererPage();
		$nextUrl = Watt_Session::getSession()->getLastRefererPage()?Watt_Session::getSession()->getLastRefererPage():'?do=${package_name}${ctrl_name}_list';
		
		try{
			$${var_name}->delete();
			$this->addTip( Watt_I18n::trans('OPRATE_SUCCESS'), $nextUrl );
			//$this->redirectToSelfAction( "list" );
		}catch ( Exception $e ){
			$this->addTip( Watt_I18n::trans('OPRATE_FAIL'), $nextUrl );
			//保存失败，做其它处理
		}
	}
}