<?
class PdProductController extends Pft_Controller_Action{
	function __construct(){
		$this->_isPublic = true;
	}
	
	function indexAction(){
		
	}
	/*
	*options: 添加产品分类
	*param:  
	*autor:df      
	*date:Sat Sep 18 17:57:01 CST 2010
	*/
	function addProductAction()
	{
		$products=new Yd_Products();
		$products->autoGetRequestVar('k_id','p_name','p_price','p_info','p_img_link','p_unit');
		if($this->getInputParameter("op"))
		{

			if($products->save())
			{
				  
			}
		}
	}
	/*
		添加分类
	*/
	function addCategoriesAction()
	{
		$returnData=array();
		$kindFenlei=$this->getInputParameter("kindFenlei");
		if($kindFenlei)
		{
			$kinds=new Yd_Kinds();
			$kinds->k_name=$kindFenlei;
			if($kinds->save())
			{
				$kinds->k_parent_id=$kinds->k_id;
				$kinds->k_root_id=$kinds->k_id;
				$kinds->save();
			}
			$returnData["k_id"]=$kinds->k_id;
			$returnData["k_name"]=$kinds->k_name;

		}
		echo json_encode($returnData);
		exit;
	}
	
}
?>