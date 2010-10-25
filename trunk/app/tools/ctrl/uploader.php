<?php
class ToolsUploaderController extends Pft_Controller_Action{
	function __construct(){
		//$this->setActionLevel()
		$this->setCtrlLevel(Pft_Rbac::LEVEL_PUBLIC);
	}
	
	function indexAction(){
		
	}
	function uploadfileAction()
	{
		$result = array();
		if (isset($_FILES['Filedata']) )
		{
			
			$file = $_FILES['Filedata']['tmp_name'];
			$temp_file=iconv('utf-8','gbk',$_FILES['Filedata']['name']);
			
			$toFileFloder = Pft_Config::getCfg('PATH_ROOT')."images/caixi/";
			if(!is_dir($toFileFloder))
			{
				@mkdir($toFileFloder);
				@chmod($toFileFloder,0777);
			}
			$toFile=$toFileFloder.$temp_file;
			if( move_uploaded_file( $file, $toFile ) ){
				//$_SESSION["uploadFile"]="./images/caixi/".$temp_file;
			}else{
				//$_SESSION["uploadFile"]='error';
			}
		
		}
		exit;
	}
	function uploadAction()
	{
		//是否能够上传多个文档  Y N 默认不能上传多个
		if($this->getInputParameter("isMore"))
		{
			$this->isMore=$this->getInputParameter("isMore");
		}
		else
		{
			$this->isMore='N';
		}
		
	}

	
}
?>