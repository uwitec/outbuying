<?php
class AdmEcOrderController extends Pft_Controller_Action{
	function __construct(){
		//$this->setActionLevel()
		$this->setCtrlLevel(Pft_Rbac::LEVEL_PUBLIC);
	}
	
	function indexAction(){
		
	}
	
	function listAction(){
		$sql = "select o_id,cr_id,o_time,o_use_time,o_amount,o_free_amount,o_real_amount,o_state 
				from ".Pft_Db::getTbName('orders');
		$grid = new Pft_Util_Grid_Searchs();
		$grid->addSearch("o_amount");
		$grid->addSearch("cr_id",'客户ID','=',null,null,true);
		$grid->setSql($sql);
		$this->grid = $grid->excuteAndReturnGrid();
	}
}