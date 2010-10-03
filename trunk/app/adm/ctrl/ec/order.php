<?php
class AdmEcOrderController extends Pft_Controller_Action{
	function __construct(){
		//$this->setActionLevel()
		$this->setCtrlLevel(Pft_Rbac::LEVEL_PUBLIC);
	}
	
	function indexAction(){
		
	}
	
	function listAction(){
		$sql = "select * from ".Pft_Db::getTbName('oders');
		$grid = new Pft_Util_Grid_Searchs();
		$grid->setSql($sql);
		$this->grid = $grid->excuteAndReturnGrid();
	}
}