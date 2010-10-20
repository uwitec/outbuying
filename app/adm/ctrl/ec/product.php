<?
/**
 * 功能：
 * Yd_Products controller
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
class AdmEcProductController extends Pft_Controller_Action{
	function __construct(){
		//$this->addPrivilegeMap();
		$this->setCtrlLevel(Pft_Rbac::LEVEL_PUBLIC);
	}

	function indexAction(){
		$this->redirectToSelfAction( "list" );
	}
	
	/**
	 * 功能：
	 * 增加一个Yd_Products
     * 
	 * 输入：
	 * 
	 * 输出：
	 * 
	 * $products
	 * 
	 * @author 
	 */
	function addAction(){
		$products = new Yd_Products();
		//此处输出
	
		//自动获得对应名称的输入字段
		//注意删掉不应该接受用户输入的字段
		$products->autoGetRequestVar( "p_id,k_id,p_name,p_price,p_info,p_img_link,p_unit,created_at,updated_at,is_del" );

		if( $op = $this->in( "op" ) ){
			//此处设置一些其他的值如
			//$products->products_reg_time = time();
			$nextUrl = Pft_Session::getSession()->getLastRefererPage()?Pft_Session::getSession()->getLastRefererPage():'?do=adm_ec_product_list';
			if( $products->save() ){
				$this->addTip( Pft_I18n::trans('OPRATE_SUCCESS'), $nextUrl );
				//$this->redirectToSelfAction( "list" );
			}else{
				$this->addTip( Pft_I18n::trans('OPRATE_FAIL') );
				//保存失败，做其它处理
			}
		}
		Pft_Session::getSession()->recordRefererPage();
		$this->products = $products->toArray();
	}
	
	/**
	 * 功能：
	 * 提供编辑Yd_Products所需的数据
	 * 
	 * 输入：
	 * p_id
	 * 
	 * 输出：
	 * $products
	 * 
	 * @author 
	 */
	function editAction(){
		$products = Yd_Products::getPeer()->retrieveByPK( $this->in( "p_id" ) );
		if( !$products ) $this->redirectTo404();

		if( $op = $this->in( "op" ) ){
			//这是在用户修改信息后返回的处理
			
			//自动获得对应名称的输入字段
			//注意删掉不应该接受用户输入的字段			
			$products->autoGetRequestVar( "p_id,k_id,p_name,p_price,p_info,p_img_link,p_unit,created_at,updated_at,is_del" );
			
			if( $products->save() ){
				$nextUrl = Pft_Session::getSession()->getLastRefererPage()?Pft_Session::getSession()->getLastRefererPage():'?do=adm_ec_product_list';
				$this->addTip( Pft_I18n::trans('OPRATE_SUCCESS'), $nextUrl );
				//$this->redirectToSelfAction( "list" );
			}else{
				$this->addTip( Pft_I18n::trans('OPRATE_FAIL') );
				//没有更新数据，做其它处理
			}
		}else{
			//这是没有输入op的处理
		}
		Pft_Session::getSession()->recordRefererPage();
		//输出信息
		$this->products = $products->toArray();
	}

	/**
	 * 功能：
	 * 提供显示Yd_Products所需的数据
	 * 
	 * 输入：
	 * p_id
	 * 
	 * 输出：
	 * $products
	 * 
	 * @author 
	 */
	function detailAction(){
		$products = Yd_Products::getPeer()->retrieveByPK( $this->in( "p_id" ) );
		if( !$products ) $this->redirectTo404();
		//此处输出 detail信息
		$this->products = $products->toArray();
	}
	
	/**
	 * 功能：
	 * 提供Yd_Products列表数据
	 * 
	 * 输入：
	 * 
	 * 输出：
	 * $productss
	 * 
	 * @author 
	 */
	function listAction(){
		/**
		 * 下面是使用grid的形式
		 */
		$sql = "select p_id,k_id,p_name,p_price,p_info,p_img_link,p_unit,created_at,updated_at,is_del from products";
		$grid = new Pft_Util_Grid_Searchs();
		$grid->setSql($sql);
		$grid->addSearch("p_name", "p_name");
		$this->productss_grid = $grid->excuteAndReturnGrid();
	}

	/**
	 * 输入：
	 * p_id
	 * 
	 * 输出:
	 * 
	 * 如成功，转到 $products_list
	 */
	function deleteAction(){
		$products = Yd_Products::getPeer()->retrieveByPK( $this->in( "p_id" ) );
		if( !$products ) $this->redirectTo404();
		
		Pft_Session::getSession()->recordRefererPage();
		$nextUrl = Pft_Session::getSession()->getLastRefererPage()?Pft_Session::getSession()->getLastRefererPage():'?do=adm_ec_product_list';
		
		try{
			$products->delete();
			$this->addTip( Pft_I18n::trans('OPRATE_SUCCESS'), $nextUrl );
			//$this->redirectToSelfAction( "list" );
		}catch ( Exception $e ){
			$this->addTip( Pft_I18n::trans('OPRATE_FAIL'), $nextUrl );
			//保存失败，做其它处理
		}
	}
}