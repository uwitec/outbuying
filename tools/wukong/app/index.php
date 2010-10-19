<?
/**
 * 功能：
 * Tool controller
 * 
 * Actions:
 *
 * index
 * add
 * list
 * edit
 * delete
 * 
 * 
 * 
 * 输入：
 * 
 * 输出：
 * 
 * @author 
 * 
 * 下一步：
 * 不需要数据库表的模板
 */

class IndexController extends Pft_Controller_Action{
	private $_tablePreFix = "";
	private $_tableOmPreFix = "";	//不要修改这个的值
	private $_tableOmPostFix = "Peer";
	
	public function __construct(){
		$this->setCtrlLevel(Pft_Rbac::LEVEL_PUBLIC);
		$this->_tablePreFix = Pft_Config::getCfg("DB_TB_PREFIX");
	}
	
	/**
	 * 功能：
	 * 输出功能列表
     * 
	 * 输入：
	 * 
	 * 输出：
	 * 
	 * $functions
	 * 
	 * @author Terry
	 */
	function indexAction(){
		$this->functions = $this->_getFunctions();
	}
	
	/**
	 * 功能：
	 * 提供创建 controller and view 的功能
	 * 
	 * 输入：
	 * 
	 * 输出：
	 * 
	 * $functions
	 * $inputForm
	 * 
	 * @author 
	 */
	function buildCandVAction(){
		$this->indexAction();
		if( $this->in( "op" ) ){
			//获得输入数据
			$appPath = $this->in( "AppPath" );
			$om_class_name = $this->in("om_class_name");
			$ctrl_name = $this->in("ctrl_name");
			$do = $ctrl_name;
			
			try{
				class_exists($om_class_name);
			}catch(Exception $e){
				echo "Om Class : ".$om_class_name." is not exist.";
				exit;
			}
			
			$peerObj = Pft_Om_BaseObject::getPeer($om_class_name);
			$desc = $peerObj->getDescription();
			
			//$viewPath = $this->in( "ViewPath" );
			//$package_name = trim( $this->in( "package_name" ), "_" );
			
			//$table_name = $this->in( "table_name" );
			//$CtrlName = $this->in( "CtrlName" );
			$PkName = $desc["pk_name"];
			$pk_name = $desc["pk_name"];
			$pre_fix = Pft_Config::getCfg("DB_TB_PREFIX");
			$table_name = $desc["table_name"];

			$disp = new Pft_Dispatcher();
			$ctrlArr = $disp->analyzeDoToControllerAndAction($do."_index");
			$arrPackageName = explode(DIRECTORY_SEPARATOR, $ctrlArr[0]);

			
			//对变量进行格式化
			//$arrPackageName = split( "_", $package_name );
			$PackageName = implode( array_map( "ucfirst", $arrPackageName ) );

			$package_name = $package_name==""?"":rtrim( $package_name, "_" )."_";

			$this->_tablePreFix = $pre_fix;
			if( trim( $pre_fix ) != "" ){
				$this->_tableOmPreFix = implode( array_map( "ucfirst", split( "_", $pre_fix ) ) );
			}
			
			$arrTableName = split( "_", $table_name );
			$TableName = implode( array_map( "ucfirst", $arrTableName ) );

			$var_name = $table_name;
			$VAR_NAME = strtoupper( $var_name );
			
			if( trim( $CtrlName ) == "" ){
				$CtrlName = $TableName;
			}else{
				$CtrlName = ucfirst($CtrlName);
			}
			$ctrl_name = strtolower( $CtrlName );

			if( trim( $PkName ) == "" ) $PkName = $TableName."Id";
			if( trim( $pk_name ) == "" ) $pk_name = $table_name."_id";

			
			/* 格式化好的变量应该有如下这么多个
			$table_name		*
			$TableName
			$var_name
			$VAR_NAME
			$package_name	*
			$PackageName
			$CtrlName		可选
			$ctrl_name
			$PkName			可选
			*/

			//检查路径是否存在，如不存在，则进行创建
			$toAppPath = $this->_checkAndMakeAppFolder( $arrPackageName );	
			//$toViewPath = $this->_checkAndMakeViewFolder( $arrPackageName, $ctrl_name );
			$toViewPath = $toAppPath."view/";
			@mkdir($toViewPath, 0777, true);
			@chmod($toViewPath, 0777);
		
			$editForm    = "";	// editForm 是 输出变量
			$detailTable = "";	// detailTable 是 输出变量
			$gridCols    = "";  // gridCols 是 输出变量
			$fieldList   = "";  // fieldList 是 输出变量
			
			if( strlen( $table_name ) > 0 ){
				$fieldNames = $desc["fields"];
				$fieldList = implode( ",", $fieldNames );
				//echo "<pre>";
				foreach ( $fieldNames as $key => $val ){
					// 为form输出准备变量
					
					//不显示创建时间和更新时间 忽略列表已经存在于 formBuild 中了
					//if( $key == "create_at" || $key == "updated_at" ) continue;
					
					$formDataArr[$val] = $val;
					// 给 gridCols 增加列
					//$gridCols .= "\${$var_name}sGrid->addCol(Pft_I18n::trans(\"$val\"),\"$val\");\n";
					$gridCols .= "\${$var_name}s_grid->addCol(Pft_I18n::trans(\"$val\"),\"$val\");\n";
					
				}
				
				/* 这是使用phpname时的方法
				$omObj = new $omClassName();
				$nameMap = $omObj->getPhpNameMap();
				$fieldList = implode( ",", array_flip( $nameMap ) );
				//echo "<pre>";
				foreach ( $nameMap as $key => $val )
				{
					// 为form输出准备变量
					$formDataArr[$key] = $key;
					// 给 gridCols 增加列
					$gridCols .= "\${$var_name}sGrid->addCol(Pft_I18n::trans(\"$key\"),\"$key\");\n";
					
				}
				*/
				$editForm = Pft_View_Helper_Form::buildFormForWukong( $formDataArr,"","post",true, false, array(), array( $pk_name=>$pk_name) );
				$detailTable = Pft_View_Helper_Form::buildFormForWukong( $formDataArr,"","post",false, false, array(), array( $pk_name=>$pk_name) );
				//echo $form;
				//echo "</pre>";
			}

			/**
			 * 这两行不要放到前面去
			 */
			$table_name = $pre_fix.$table_name;	
			$TableName = $this->_tableOmPreFix.$TableName;
			
			//这里仅仅是为了输出
			$this->output = $arrNames = compact( "table_name"
						                        //,"TableName"
						                        ,"var_name"
						                        ,"VAR_NAME"
						                        ,"package_name"
						                        ,"PackageName"
						                        ,"CtrlName"
						                        ,"ctrl_name"
						                        ,"PkName"
						                        ,"editForm"
						                        ,"detailTable"
						                        ,"gridCols"
						                        ,"fieldList"
						                        ,"pk_name"
						                        ,"om_class_name"
						                       );
			//用变量替换模板中的相关变量              
			$this->_buildPhpFile( "controller.tpl.php", $toAppPath.strtolower($ctrl_name) . ".php"
			                     ,$arrNames);
			if( strlen( $table_name ) > 0 ){
				//只有对数据表的操作才有 增删改查
				$this->_buildPhpFile( "add.html.tpl.php", $toViewPath."add.html.php"
				                     ,$arrNames);
		        $this->_buildPhpFile( "detail.html.tpl.php", $toViewPath."detail.html.php"
				                     ,$arrNames);
		        $this->_buildPhpFile( "edit.html.tpl.php", $toViewPath."edit.html.php"
				                     ,$arrNames);
		        $this->_buildPhpFile( "list.html.tpl.php", $toViewPath."list.html.php"
				                     ,$arrNames);
				$this->_buildPhpFile( "_editform.html.tpl.php", $toViewPath."_editform.html.php"
				                     ,$arrNames);
				$this->_buildPhpFile( "_detailtable.html.tpl.php", $toViewPath."_detailtable.html.php"
				                     ,$arrNames);
			}
			//输出本页再次显示的数据
			$inputForm = array(
			             "AppPath"      => $appPath
			            //,"ViewPath"     => $viewPath
			            //,"package_name" => $package_name	//(wukong_other)
			            //,"CtrlName"     => $CtrlName
			            ,"ctrl_name" 			=> $ctrl_name
			            ,"om_class_name"	=> $om_class_name
			            //,"pre_fix"      => $pre_fix
			            //,"table_name"   => preg_replace( "/^$pre_fix/", "", $table_name )	    //(tpm_user)
			            //,"PkName"      => $PkName
			            //,"pk_name"      => $pk_name
			            );
			             
			//$inputForm["other"] = $tpl;
			//$this->redirectToSelfAction( "index" );
		}
		else
		{
			$inputForm = array(
			             "AppPath"      => Pft_Config::getAppPath(1)
			            //,"ViewPath"     => Pft_Config::getViewPath()
			            //,"package_name" => ""
			            //,"CtrlName"     => ""
			            ,"ctrl_name" => ""
			            ,"om_class_name"     => ""
			            //,"pre_fix"      => $this->_tablePreFix
			            //,"table_name"   => ""
			            //,"PkName"       => ""
			            //,"pk_name"       => ""
			            );
		}
		$this->inputForm = $inputForm;
		$this->inputFormDescc = array(
					             "AppPath"      => "系统app路径，一般不用修改"
					            ,"ViewPath"     => "系统view路径，一般不用修改"
					            ,"package_name" => "包名，使用小写字母+下划线的形式，如 ec , ec_sale"
					            ,"pre_fix"      => "表前缀名，如果前缀有下划线，最后请保留下划线，如pft_；如果没有前缀，则留空"
					            ,"table_name"   => "表名，不带表前缀，如 yonghu， yonhu_wanquan"
					            ,"CtrlName"     => "controller名称，如果有表名，此项无须填写。请使用首字母大写的形式，如 YonghuWanquan。"
					            //,"PkName"       => ""
					            ,"pk_name"       => "表的主关键字名称，如果主关键字是 表名_id 的形式则无须填写。如表名是 yonghu, 如pk是 yonghu_id，则无须填写，如pk是 yh_id，则需要填写。 "
					            ,"om_class_name" => "om对象的名称，如Yd_Order。 "
					            ,"ctrl_name" => "包含package名称的controller的名称，即do的去掉action后的内容，如ec_order。 "
					            );
	}
	
	function formbuilderAction()
	{
		$this->indexAction();
		if( $this->in( "op" ) ){
			
		}else{
			
		}
	}
	
	function buildOmAction(){
		$this->indexAction();
	}
	
	/**
	 * 生成功能列表
	 * @todo 完成创建CAndV
	 * @return array
	 */
	private function _getFunctions(){
		$functions = array();
		$functions[] = array( "name" => "根据模板创建默认controller和view(html)"
		                     ,"do" => $this->_controllerName."_buildCandV" );
		$functions[] = array( "name" => "根据数据库表创建om对象"
		                     ,"do" => $this->_controllerName."_buildOm" );
		/*
		$functions[] = array( "name" => "自动集成工具"
		                     ,"do" => "wukong_integration_index" );
		$functions[] = array( "name" => "form创建器"
		                     ,"do" => $this->_controllerName."_formbuilder" );
		$functions[] = array( "name" => "遍历app自动生成Action及do的列表"
		                     ,"do" => "" );
		*/
		return $functions;
	}

	/**
	 * Enter description here...
	 *
	 * @param string $tplFileName 在本文件所在目录的 tpl 目录下
	 * @param string $outFileName 要求绝对路径
	 * @param unknown_type $packageNameForCtrl
	 * @param unknown_type $packageNameForDo
	 * @param unknown_type $tableNameForTable
	 * @param unknown_type $tableNameForVar
	 */
	private function _buildPhpFile( $tplFileName, $outFileName
	                              , $arrNames
	                              ){
		$tpl = file_get_contents( dirname(__FILE__)."/tpl/".$tplFileName );
		/*
		if( preg_match_all( "/(\\$\\{[\\w_]+\})/", $tpl, $match ) )
		{
			var_dump( $match );
		}
		*/
		
		foreach ( $arrNames as $key => $varname ){
			$tpl = str_replace( "\${{$key}}", $varname, $tpl);
		}

		/**
		 * $arrNames 应包含如下key
		 * 
		 * $packageNameForCtrl
		 * $packageNameForDo
		 * $tableNameForTable
		 * $tableNameForVar
		 * $ctrlName
		 * 
		 */
		//extract( $arrNames );		
		/*
		$tpl = str_replace( "\${PackageName}", $packageNameForCtrl, $tpl);
		$tpl = str_replace( "\${package}", $packageNameForDo, $tpl);
		$tpl = str_replace( "\${TableName}", $tableNameForTable, $tpl);
		$tpl = str_replace( "\${var_name}", $tableNameForVar, $tpl);
		$tpl = str_replace( "\${VAR_NAME}", strtoupper( $tableNameForVar ), $tpl);
		$tpl = str_replace( "\${ctrlname}", strtolower( $ctrlName ), $tpl);
		*/
		
		//file_put_contents( dirname(__FILE__)."/output/". $outFileName, $tpl );
		if( file_exists( $outFileName ) ){
			//检验是否已存在输出文件，如果存在，则增加一个时间戳
			$outFileName = $outFileName.".".date( "YmdHis", time() );
		}
		
		
		if( file_put_contents( $outFileName, $tpl ) ){
			Pft_Debug::addInfoToDefault( get_class( $this ), "Create $outFileName success!" );
		}else{
			Pft_Debug::addInfoToDefault( get_class( $this ), "Create $outFileName fail!" );
		}
		chmod ( $outFileName, 0777 );
	}
	
	/**
	 * 检查和创建相关目录
	 *
	 * @param 
	 * @param 
	 */
	private function _checkAndMakeAppFolder( $arrPackage ){
		//创建
		$toCheckAppPath = Pft_Config::getAppPath();

		foreach ( $arrPackage as $subFolder ){
			$toCheckAppPath .= $subFolder."/";
			$this->_mkdir( $toCheckAppPath );
		}
		return $toCheckAppPath;
	}
	
	private function _checkAndMakeViewFolder( $arrPackage, $ctrlName ){
		//创建
		$toCheckViewPath = Pft_Config::getViewPath();

		foreach ( $arrPackage as $subFolder ){
			$toCheckViewPath .= $subFolder."/";
			$this->_mkdir( $toCheckViewPath );
		}
		
		$toCheckViewPath .= strtolower( $ctrlName )."/";
		$this->_mkdir( $toCheckViewPath );
		return $toCheckViewPath;
	}
	
	private function _mkdir( $path ){
		if( !(file_exists( $path ) && is_dir( $path ) ) ){
//			echo $path;
			mkdir( $path );
			chmod( $path, 0777 );
		}
	}
}