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
		$sql="select * from kinds where is_del<1";
		$datas=Pft_Db::getDb()->getAll($sql);
		$this->list=$datas;
		
		
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