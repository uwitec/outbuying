<?php
class HomeOrderController extends Pft_Controller_Action{
	function __construct(){
		//$this->setActionLevel()
		$this->setCtrlLevel(Pft_Rbac::LEVEL_PUBLIC);
	}
	
	function indexAction(){
		
	}
	/*
	*options: 购物车
	*param:  
	*autor:df      
	*date:Mon Oct 04 18:54:44 CST 2010
	*/
	function listAction(){
		//找出产品
		$sql="select * from kinds where is_del<1 order by k_id asc";
		$datas=Pft_Db::getDb()->getAll($sql);
		$this->list=$list=$datas;
		//得到默认的产品  (分类中的第一个)
		$k_id=null;
		foreach ($list as $row)
		{
			$k_id=$row["k_id"];
			break;
		}
		$this->products=null;
		if($k_id)
		{
			$sql="select * from products where k_id='".$k_id."'";
			$products=Pft_Db::getDb()->getAll($sql);
			
		}
		$this->products=$products;
		
	}
	/*
	*options: 根据分类得到产品
	*param:  
	*autor:df      
	*date:Mon Oct 04 22:44:33 CST 2010
	*/
	function getProductAction()
	{
		
	}
}