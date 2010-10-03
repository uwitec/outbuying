<?
/**
 * 这是对Watt Grid进行显示的类
 *
 * Fri May 25 09:42:35 CST 2007	显示增加htmlspecialchars
 * 
 * @since 1
 * @author Terry, Tony
 * @package Pft_View_Helper
 * @version 2.0.1
 */
class Pft_View_Helper_Grid{
	/**
	 * 根据符合Watt:Data里的 Grid 的Schema 的数据创建一个HTML的 Grid显示
	 *
	 * @param unknown_type $gridData
	 */
	public static function buildGrid( $grid, $show=true, $formAttribs="" ){
		$outArr = self::buildGridToOutArray( $grid, $formAttribs );
		$out = "<div>";
		foreach ( $outArr as $val ){
			$out .= $val;
		}
		$out .= "</div>";
		
		if( $show )echo $out;
		return $out;
	}
	
	/**
	 * 将建立grid并输出到数组
	 * @param array $grid
	 * @param array $params	[formAttribs,id]
	 */
	public static function buildGridToOutArray( $grid, $params="", $searchCols=4 ){
		/**
		 * 这里判断数据是否符合规则
		 * 应该根据 Shema 判断
		 */
		if( !is_array( $grid ) ){
			$e = new Pft_Exception(Pft_I18n::trans("ERR_INVALID_DATATYPE"));
			throw $e;
		}
		
		if( is_array( $params ) ){
			$gridId = @$params['id'];
			$formAttribs = @$params['formAttribs'];
		}else{
			$formAttribs = $params;
			$gridId = null;
		}
		
		//随机生成一个 search form 的id
		$specSearchFormId = mt_rand( 1000, 9999 );
		
		/**
		 * 用来记录orderby了的字段和orderbyorder
		 */
		$orderByCols = array();
		
		if( isset( $grid[ Pft_Util_Grid::GRID_COLS ] )
		 && count( $grid[ Pft_Util_Grid::GRID_COLS ] ) > 0 ){
			$isDefCols = true;
			$cols = $grid[ Pft_Util_Grid::GRID_COLS ];
		}else{
			$isDefCols = false;
			$cols = null;
		}

		$datas = $grid[ Pft_Util_Grid::GRID_DATAS ];
		
		$output_searchs = "";
		/**
		 * 开始输出查询信息
		 */
		$output_searchs .= '<form method="get" action="'.$_SERVER['PHP_SELF'].'" id="searchform'.$specSearchFormId.'" '.$formAttribs.' onsubmit="if($(\'searchFormPageTotal\')){$(\'searchFormPageTotal\').value=\'\';}">';
		//初始化参数
		$initParams = $grid[Pft_Util_Grid::GRID_SEARCHS][Pft_Util_Grid_Searchs::DEF_INITPARAMS];
		foreach ( $initParams as $initKey => $initValue ) {
			$output_searchs .= '<input type="hidden" name="'.$initKey.'" value="'.h($initValue).'">';
			$output_searchs .= '<input type="hidden" name="'.Pft_Util_Grid_Searchs::INIT_PARAM_NAME.'[]" value="'.h($initKey).'">';
		}

		if( ( isset( $grid[Pft_Util_Grid::GRID_SEARCHS] ) && is_array( $grid[Pft_Util_Grid::GRID_SEARCHS] ) )
		  ||( isset( $grid[Pft_Util_Grid::GRID_PAGER] ) && is_array( $grid[Pft_Util_Grid::GRID_PAGER] ) )
		  ||( isset( $grid[Pft_Util_Grid::GRID_ORDERBYS] ) && is_array( $grid[Pft_Util_Grid::GRID_ORDERBYS] ) )
		  ){						
			$output_searchs .= '<div class="search_container">'."\n";
			//$output_searchs .= '<div class="search"><form method="post" id="searchform" '.$formAttribs.'>';
			
			/* 070322 暂时给演示注销*/
			$output_searchs .= '<div class="search">';
			
			/*
			$output_searchs .= '<input type="hidden" name="do" value="'.$_REQUEST['do'].'">';
			*/

			$SCHEMA_SEARCHS = $grid[Pft_Util_Grid::GRID_SEARCHS];
			$output_searchs .= '<div class="grid">';
			
			//$output_searchs .= '<div style="float:left;width:33%">';
			//高级搜索
			//if( isset( $grid[Pft_Util_Grid::GRID_SEARCHS][Pft_Util_Grid_Searchs::SEARCH_ADV_SIGN] ) ){
			if( key_exists( Pft_Util_Grid_Searchs::SEARCH_ADV_SIGN, $SCHEMA_SEARCHS ) ){
				$advSign = $SCHEMA_SEARCHS[Pft_Util_Grid_Searchs::SEARCH_ADV_SIGN];
				$output_searchs .= '<input name="'.Pft_Util_Grid_Searchs::SEARCH_ADV_SIGN.'" id="'.Pft_Util_Grid_Searchs::SEARCH_ADV_SIGN.'" value="'.h($advSign).'">';
			}
			
			$searchs = $grid[Pft_Util_Grid::GRID_SEARCHS][Pft_Util_Grid_Searchs::DEF_SEARCHS];			
			if(isset($grid[Pft_Util_Grid::GRID_SEARCHS][Pft_Util_Grid_Searchs::DEF_SEARCHGROUP])){
			$searchgroup = $grid[Pft_Util_Grid::GRID_SEARCHS][Pft_Util_Grid_Searchs::DEF_SEARCHGROUP];	
			}
			if(isset($grid[Pft_Util_Grid_Searchs::DEF_EXPORT])){
				$isexport = $grid[Pft_Util_Grid_Searchs::DEF_EXPORT];	
			}	
			if(isset($grid[Pft_Util_Grid_Searchs::DEF_EXPORTFILE])){
				$exportfile = $grid[Pft_Util_Grid_Searchs::DEF_EXPORTFILE];	
			}	
			if(isset($grid[Pft_Util_Grid_Searchs::DEF_EXPORTFILE_NAME])){
				$exportfile_name = $grid[Pft_Util_Grid_Searchs::DEF_EXPORTFILE_NAME];	
			}
			if(isset($grid[Pft_Util_Grid_Searchs::DEF_EXPORTFILE_FORMAT])){
				$exportfile_format = $grid[Pft_Util_Grid_Searchs::DEF_EXPORTFILE_FORMAT];	
			}
			$exportfilestr = '';
			if(isset($exportfile) && $exportfile){
				$exportfilestr = "&nbsp;<font><a href=".$exportfile.">".Pft_I18n::trans('JT_DINGDAN_XIAZAIWENJIAN')."</a></font>";
			}
			
			//$output_searchs .= '高级</div>';
			$output_searchs .= '</div>';

			$searchsCounter = 0;
			$output_searchs .= '<div id="search_searchs" style="clear:both">';
			$output_searchs .= '<table cellspacing="1" cellpadding="0"><tr>';
			
			$number =2;//一行显示几组查询
			
			//搜索条件			
			if(isset($searchgroup) && count($searchgroup) && !key_exists( Pft_Util_Grid_Searchs::SEARCH_ADV_SIGN, $SCHEMA_SEARCHS )){//按分组搜索
				$group = $searchgroup;
				//$itemdata = array();
				//组关系
				$groupgx = array();
				$x = 0;
				if(is_array($group) && count($group)){
					foreach ($group as $key=>$val){
						$groupgx[$key] = array();
						if(is_array($val['item']) && count($val['item'])){
							foreach ($val['item'] as $key1=>$val1){
								//$itemdata[] = array($key1=>$val1);
								$groupgx[$key][] = $x;
								$x++;
							}
						}
					}
				}
				//$output_searchs .= '<td >';				
				$table_group = '';				
				foreach ( $searchs as $xb =>$search ){		
					//判断在那个分组
					//几个分组
					$groupnum = count($group);//共几个分组
					$groupon = 0;//第几个分组
					$groupdjg = 0;//分组中的第几个
					if(is_array($groupgx) && count($groupgx)){
						foreach ($groupgx as $key=>$val){
							if(is_array($val) && count($val)){
								foreach ($val as $key1=>$val1){
									if($val1 == $xb){
										$groupon = $key;
										$groupdjg = $key1;
									}	
								}
							}													
						}						
					}
					//$table_group .= '<table>';
												
					$opration = $search[Pft_Util_Grid_Search::DEF_OPERATION];
					if( $opration == Pft_Util_Grid_Searchs::LIKE ){
						$oprationTip = i18ntrans('#模糊匹配');
					}elseif( $opration == Pft_Util_Grid_Searchs::EQUAL || $opration == Pft_Util_Grid_Searchs::IN ){
						$oprationTip = i18ntrans('#精确匹配');
					}else{
						$oprationTip = sprintf(i18ntrans('#规则为(%s)'),$opration);;
					}
					//$output_searchs .= '<div style="float:left;width:200px;text-align:right;">';
					$contstr = '';
					//搜索项
					if( is_array( $search[Pft_Util_Grid_Search::DEF_ITEM] ) && count($search[Pft_Util_Grid_Search::DEF_ITEM])){
						//and or
						$isviewandor=$search[Pft_Util_Grid_Search::DEF_ISVIEWANDOR];
						if($isviewandor && $isviewandor == 'Y'){
							$contstr .= '<select name="isor_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" id="isor_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" style="width:50px;">';
							if($search[Pft_Util_Grid_Search::DEF_ISOR]){
								$contstr .= '<option value="0">'.Pft_I18n::trans('并且').'</option>';
								$contstr .= '<option value="1" selected >'.i18ntrans('#或者').'</option>';
							}else{
								$contstr .= '<option value="0" selected >'.i18ntrans('#并且').'</option>';
								$contstr .= '<option value="1" >'.i18ntrans('#或者').'</option>';
							}
							$contstr .= '';
							$contstr .= '</select>';						
						}else if($isviewandor && $isviewandor != 'Y'){
							$contstr .= '<span style="width:50px;">&nbsp;&nbsp;&nbsp;'.htmlspecialchars($isviewandor).'&nbsp;&nbsp;&nbsp;</span>';
						}
						
						$contstr .= '<select name="item_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" id="item_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'">';
						foreach ( $search[Pft_Util_Grid_Search::DEF_ITEM] as $key=>$value ){
							$selSign = ( ($value == $search[Pft_Util_Grid_Search::DEF_TITLE])&&( $search[Pft_Util_Grid_Search::DEF_TITLE] !== '' ) && !is_null( $search[Pft_Util_Grid_Search::DEF_TITLE] ) )?"selected":"";
							$contstr .= '<option value="'.htmlspecialchars($key).'" '.$selSign.'>'.htmlspecialchars($value).'</option>';
						}
						$contstr .= '';
						$contstr .= '</select>';
					}else{
						$contstr .= '<span class="search_name" title="'.$oprationTip.'">'.$search[Pft_Util_Grid_Search::DEF_TITLE].': </span>';
					}
					//$contstr .= '</td><td style="text-align:left">';
					
					//搜索条件设置
					if( is_array( $search[Pft_Util_Grid_Search::DEF_COND] ) && count($search[Pft_Util_Grid_Search::DEF_COND])){
						//这里用下拉列表显示
						$contstr .= '<select name="cond_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" id="cond_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'">';
						foreach ( $search[Pft_Util_Grid_Search::DEF_COND] as $key=>$value ){
							
							$selSign = ( ($value == $search[Pft_Util_Grid_Search::DEF_OPERATION])&&( $search[Pft_Util_Grid_Search::DEF_OPERATION] !== '' ) && !is_null( $search[Pft_Util_Grid_Search::DEF_OPERATION] ) )?"selected":"";
							$contstr .= '<option value="'.htmlspecialchars($value).'" '.$selSign.'>'.htmlspecialchars($key).'</option>';
						}
						$contstr .= '';
						$contstr .= '</select>';
					}
					
					if( is_array( $search[Pft_Util_Grid_Search::DEF_REFERENCE] ) ){
						//这里用下拉列表显示
						$contstr .= '<select class="search_input" name="'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" id="'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'">';
						$contstr .= '<option>               </option>';
						foreach ( $search[Pft_Util_Grid_Search::DEF_REFERENCE] as $key=>$value ){
							$selSign = ( ($key == $search[Pft_Util_Grid_Search::DEF_VALUE])&&( $search[Pft_Util_Grid_Search::DEF_VALUE] !== '' ) && !is_null( $search[Pft_Util_Grid_Search::DEF_VALUE] ) )?"selected":"";
							$contstr .= '<option value="'.htmlspecialchars($key).'" '.$selSign.'>'.htmlspecialchars($value).'</option>';
						}
						$contstr .= '';
						$contstr .= '</select>';
					}else{
						switch ( $search[Pft_Util_Grid_Search::DEF_SHOWTYPE] ){
							case Pft_Util_Grid_Search::SHOW_TYPE_DATE;
								$dateselector = new Pft_View_Helper_DateSelector();
								$contstr .=  $dateselector->build($search[Pft_Util_Grid_Search::DEF_COLNAME], $search[Pft_Util_Grid_Search::DEF_VALUE], array('class' => 'dateselector') );
								break;
							case Pft_Util_Grid_Search::SHOW_TYPE_TIMESTAMP;
								$dateselector = new Pft_View_Helper_DateSelector();
								$dateselector->setShowTimes( true );
								$contstr .=  $dateselector->build($search[Pft_Util_Grid_Search::DEF_COLNAME], $search[Pft_Util_Grid_Search::DEF_VALUE], array('class' => 'dateselector') );
								break;
							case Pft_Util_Grid_Search::SHOW_TYPE_TIMESEC;
								$dateselector = new Pft_View_Helper_DateSelector();
								$dateselector->setShowTimeSecs( true );
								$contstr .=  $dateselector->build($search[Pft_Util_Grid_Search::DEF_COLNAME], $search[Pft_Util_Grid_Search::DEF_VALUE], array('class' => 'dateselector') );
								break;
							case Pft_Util_Grid_Search::SHOW_TYPE_SELECTOR_PERSON;
								$dateselector = new Pft_View_Helper_PersonSelector();
								$contstr .=  $dateselector->build($search[Pft_Util_Grid_Search::DEF_COLNAME], $search[Pft_Util_Grid_Search::DEF_VALUE], array('class' => 'personselector') );
								break;
							case Pft_Util_Grid_Search::SHOW_TYPE_SELECTOR_DINGDAN;
								$dateselector = new Pft_View_Helper_DingdanSelector();
								$contstr .=  $dateselector->build($search[Pft_Util_Grid_Search::DEF_COLNAME], $search[Pft_Util_Grid_Search::DEF_VALUE], array('class' => 'dingdanselector') );
								break;
							case Pft_Util_Grid_Search::SHOW_TYPE_SEARCHTIP;
								
							default:
								$contstr .= '<input style="border:1px solid #000;width:100px;height:19px;" name="'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" id="'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" value="'.h($search[Pft_Util_Grid_Search::DEF_VALUE]).'">';	
						}
					}
					//$contstr .= '</td>';
					//$output_searchs .= '</div>';
					//$searchsCounter++;
					//if( $searchsCounter > 0 && $searchsCounter%$searchCols==0 )$output_searchs .= '</tr><tr>';

					$joinstr = "";					
					if (!$groupdjg) {//分组中的第一个
						if (!$groupon) {//第一个分组										
							$joinstr = "<td>".$joinstr."</td><td>";
						}else {
							if ($group[$groupon]['join'] == 'and'){
								$joinstr = '<input type="radio" id="groupjone_'.$groupon.'" name="groupjone_'.$groupon.'" checked  value="and" style="border:none;"/>'.i18ntrans('#并且').'<br><input type="radio" id="groupjone_'.$groupon.'" name="groupjone_'.$groupon.'" value="or" style="border:none;"/>'.i18ntrans('#或者');
							}else if ($group[$groupon]['join'] == 'or'){
								$joinstr = '<input type="radio" id="groupjone_'.$groupon.'" name="groupjone_'.$groupon.'" value="and" style="border:none;"/>'.i18ntrans('#并且').'<br><input type="radio" id="groupjone_'.$groupon.'" name="groupjone_'.$groupon.'" checked value="or" style="border:none;"/>'.i18ntrans('#或者');
							}
							$joinstr = "<td>".$joinstr."</td><td>";
														
						}
						
						$table_group .= $joinstr.$contstr."<br>";
					}else if(isset($group[$groupon]['item']) && $groupdjg ==(count($group[$groupon]['item'])-1)){
						//分组中最后一个
						if($groupon && (($groupon+1)%$number)==0 && ($groupon != ($groupnum-1)) ){ 
						 	$contstr .= "</td></tr><tr>";
						 }else{
						 	
							 $contstr .= "</td>";
						 }		
						 $table_group .= $joinstr.$contstr;				 
					}else{
						$table_group .= $joinstr.$contstr."<br>";
					}
					
				}
				
				//$table_group.='</td></tr></table>';
				$output_searchs .= $table_group;
				$output_searchs .= '</td ></tr>';	
				
				if( count( $searchs ) ){
					//导出报表按钮设置
					$exportstr ='';
					if(isset($isexport) && $isexport){
						$exportstr ='&nbsp;<input type="submit" id="searchFormExport"  name="searchFormExport" value="'.Pft_I18n::trans("ec_dd_daochubaobiao").'" class="btn">';
					}
										
					//如果没有修改搜索条件，不会影响总记录的条数
					$output_searchs .= '<tr><td colspan="'.($number*4).'">
					<div style="clear:both;text-align:center">
					<input type="submit" id="searchFormSubmit" value="'.Pft_I18n::trans("SEARCH").'" class="btn">'.$exportstr.$exportfilestr.'
					</div>
					</td>'."\n";	
				}
			}else if (!key_exists( Pft_Util_Grid_Searchs::SEARCH_ADV_SIGN, $SCHEMA_SEARCHS )){//普通搜索
				foreach ( $searchs as $search ){									
					$opration = $search[Pft_Util_Grid_Search::DEF_OPERATION];
					if( $opration == Pft_Util_Grid_Searchs::LIKE ){
						$oprationTip = i18ntrans('#模糊匹配');
					}elseif( $opration == Pft_Util_Grid_Searchs::EQUAL || $opration == Pft_Util_Grid_Searchs::IN ){
						$oprationTip = i18ntrans('#精确匹配');
					}else{
						$oprationTip = sprintf(i18ntrans('#规则为(%s)'),$opration);
					}
					//$output_searchs .= '<div style="float:left;width:200px;text-align:right;">';
					$output_searchs .= '<td >';		
					
					if($searchsCounter && $search['isviewandor']){
						$output_searchs .= '<select name="isor_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" id="isor_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'">';
						if($search[Pft_Util_Grid_Search::DEF_ISOR]){
							$output_searchs .= '<option value="0">'.Pft_I18n::trans('并且').'</option>';
							$output_searchs .= '<option value="1" selected >'.i18ntrans('#或者').'</option>';
						}else{
							$output_searchs .= '<option value="0" selected >'.i18ntrans('#并且').'</option>';
							$output_searchs .= '<option value="1" >'.i18ntrans('#或者').'</option>';
						}
						
						
						$output_searchs .= '';
						$output_searchs .= '</select>';	
					}
						
					//搜索项
					if( is_array( $search[Pft_Util_Grid_Search::DEF_ITEM] ) && count($search[Pft_Util_Grid_Search::DEF_ITEM])){
						//and or
						$isviewandor=$search[Pft_Util_Grid_Search::DEF_ISVIEWANDOR];
						if($isviewandor && $isviewandor == 'Y'){
							$output_searchs .= '<select name="isor_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" id="isor_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'">';
							if($search[Pft_Util_Grid_Search::DEF_ISOR]){
								$output_searchs .= '<option value="0">'.Pft_I18n::trans('并且').'</option>';
								$output_searchs .= '<option value="1" selected >'.i18ntrans('#或者').'</option>';
							}else{
								$output_searchs .= '<option value="0" selected >'.i18ntrans('#并且').'</option>';
								$output_searchs .= '<option value="1" >'.i18ntrans('#或者').'</option>';
							}
							$output_searchs .= '';
							$output_searchs .= '</select>';						
						}else if($isviewandor && $isviewandor != 'Y'){
							$output_searchs .= '<span>'.htmlspecialchars($isviewandor).'</span>';
						}
						$output_searchs .= '<select name="item_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" id="item_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'">';
						foreach ( $search[Pft_Util_Grid_Search::DEF_ITEM] as $key=>$value ){
							$selSign = ( ($value == $search[Pft_Util_Grid_Search::DEF_TITLE])&&( $search[Pft_Util_Grid_Search::DEF_TITLE] !== '' ) && !is_null( $search[Pft_Util_Grid_Search::DEF_TITLE] ) )?"selected":"";
							$output_searchs .= '<option value="'.htmlspecialchars($key).'" '.$selSign.'>'.htmlspecialchars($value).'</option>';
						}
						$output_searchs .= '';
						$output_searchs .= '</select>';
					}else{
						$output_searchs .= '<span class="search_name" title="'.$oprationTip.'">'.$search[Pft_Util_Grid_Search::DEF_TITLE].': </span>';
					}
					$output_searchs .= '</td><td style="text-align:left">';
					
					//搜索条件设置
					//Pft_Util_Grid_Search::DEF_OPERATIONVIEW;		
					//$search['operationview']=array('等于'=>'=','小于'=>'<','包含'=>Pft_Util_Grid_Searchs::LIKE);
					if( is_array( $search[Pft_Util_Grid_Search::DEF_COND] ) && count($search[Pft_Util_Grid_Search::DEF_COND])){
						//这里用下拉列表显示
						$output_searchs .= '<select name="cond_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" id="cond_'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'">';
						foreach ( $search[Pft_Util_Grid_Search::DEF_COND] as $key=>$value ){
							$selSign = ( ($value == $search[Pft_Util_Grid_Search::DEF_OPERATION])&&( $search[Pft_Util_Grid_Search::DEF_OPERATION] !== '' ) && !is_null( $search[Pft_Util_Grid_Search::DEF_OPERATION] ) )?"selected":"";
							$output_searchs .= '<option value="'.htmlspecialchars($value).'" '.$selSign.'>'.htmlspecialchars($key).'</option>';
						}
						$output_searchs .= '';
						$output_searchs .= '</select>';
					}
					
					if( is_array( $search[Pft_Util_Grid_Search::DEF_REFERENCE] ) ){
						//这里用下拉列表显示
						$output_searchs .= '<select class="search_input" name="'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" id="'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'">';
						$output_searchs .= '<option>               </option>';
						foreach ( $search[Pft_Util_Grid_Search::DEF_REFERENCE] as $key=>$value ){
							$selSign = ( ($key == $search[Pft_Util_Grid_Search::DEF_VALUE])&&( $search[Pft_Util_Grid_Search::DEF_VALUE] !== '' ) && !is_null( $search[Pft_Util_Grid_Search::DEF_VALUE] ) )?"selected":"";
							$output_searchs .= '<option value="'.htmlspecialchars($key).'" '.$selSign.'>'.htmlspecialchars($value).'</option>';
						}
						$output_searchs .= '';
						$output_searchs .= '</select>';
					}else{
						switch ( $search[Pft_Util_Grid_Search::DEF_SHOWTYPE] ){
							case Pft_Util_Grid_Search::SHOW_TYPE_DATE;
								$dateselector = new Pft_View_Helper_DateSelector();
								$output_searchs .=  $dateselector->build($search[Pft_Util_Grid_Search::DEF_COLNAME], $search[Pft_Util_Grid_Search::DEF_VALUE], array('class' => 'dateselector') );
								break;
							case Pft_Util_Grid_Search::SHOW_TYPE_TIMESTAMP;
								$dateselector = new Pft_View_Helper_DateSelector();
								$dateselector->setShowTimes( true );
								$output_searchs .=  $dateselector->build($search[Pft_Util_Grid_Search::DEF_COLNAME], $search[Pft_Util_Grid_Search::DEF_VALUE], array('class' => 'dateselector') );
								break;
							case Pft_Util_Grid_Search::SHOW_TYPE_TIMESEC ;
								$dateselector = new Pft_View_Helper_DateSelector();
								$dateselector->setShowTimeSecs( true );
								$output_searchs .=  $dateselector->build($search[Pft_Util_Grid_Search::DEF_COLNAME], $search[Pft_Util_Grid_Search::DEF_VALUE], array('class' => 'dateselector') );
								break;
							case Pft_Util_Grid_Search::SHOW_TYPE_SELECTOR_PERSON;
								$dateselector = new Pft_View_Helper_PersonSelector();
								$output_searchs .=  $dateselector->build($search[Pft_Util_Grid_Search::DEF_COLNAME], $search[Pft_Util_Grid_Search::DEF_VALUE], array('class' => 'personselector') );
								break;
							case Pft_Util_Grid_Search::SHOW_TYPE_SELECTOR_DINGDAN;
								$dateselector = new Pft_View_Helper_DingdanSelector();
								$output_searchs .=  $dateselector->build($search[Pft_Util_Grid_Search::DEF_COLNAME], $search[Pft_Util_Grid_Search::DEF_VALUE], array('class' => 'dingdanselector') );
								break;
							case Pft_Util_Grid_Search::SHOW_TYPE_SEARCHTIP;
								//此功能尚未完善，暂时屏蔽
								//$dateselector = new Pft_View_Helper_SearchTip();
								//$output_searchs .=  $dateselector->build($search[Pft_Util_Grid_Search::DEF_COLNAME],$search[Pft_Util_Grid_Search::DEF_VALUE],  array('class' => 'search_input'),null,$search[Pft_Util_Grid_Search::DEF_EXTEND]);			
								//break;
							default:
								$output_searchs .= '<input class="search_input" name="'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" id="'.$search[Pft_Util_Grid_Search::DEF_COLNAME].'" value="'.h($search[Pft_Util_Grid_Search::DEF_VALUE]).'">'."\n";	
						}
					}
					$output_searchs .= '</td>';
					//$output_searchs .= '</div>';
					$searchsCounter++;
					if( $searchsCounter > 0 && $searchsCounter%$searchCols==0 )$output_searchs .= '</tr><tr>';
				}
//				if( count( $searchs ) ){
//					//如果没有修改搜索条件，不会影响总记录的条数
//					$output_searchs .= '<td colspan="'.(( $searchCols-( $searchsCounter % $searchCols ) ) * 2).'">
//					<div style="clear:both;text-align:center">
//					<input type="submit" id="searchFormSubmit" value="'.Pft_I18n::trans("SEARCH").'" class="btn">
//					</div>
//					</td>'."\n";	
//				}
				
				if( count( $searchs ) ){
					//导出报表按钮设置
					$exportstr ='';
					if(isset($isexport) && $isexport){
						$exportstr ='&nbsp;<input type="button" id="searchFormExport1" onmouseover="document.getElementById(\'exportDiv\').style.display=\'\';" onclick="document.getElementById(\'exportDiv\').style.display=\'\';"  name="searchFormExport1" value="'.Pft_I18n::trans("ec_dd_daochubaobiao").'" class="btn">';
						$exportstr.="<div id='exportDiv' style='display:none;position:absolute;height:25px;width:60px;background-color:#ffe;' onmouseout='this.style.display=\"none\"' onmouseover='this.style.display=\"\"' >
						
						<ul style='list-style:none'>
						<li>".i18ntrans('#选择格式')."</li>
						<li><input type='radio' name='exportformat' id='exportformat' value='csv' checked>.csv</li>
						<li><input type='radio' name='exportformat' id='exportformat' value='xls'>.xls&nbsp;</li>
						<li><input type='submit' class='btn' id='searchFormExport' name='searchFormExport' value='".i18ntrans('#确定')."'></li>
						</ul>
						</div>";
						//$exportstr ='&nbsp;<input type="submit" id="searchFormExport"  name="searchFormExport" value="'.Pft_I18n::trans("ec_dd_daochubaobiao").'" class="btn">';
					}
					//如果没有修改搜索条件，不会影响总记录的条数<tr>
					$output_searchs .= '<td colspan="'.($number*4).'">
					<div style="clear:both;text-align:center;border:1px">
					<input type="submit" id="searchFormSubmit" value="'.Pft_I18n::trans("SEARCH").'" class="btn">'.$exportstr.$exportfilestr.'
					</div>
					</td>'."\n";	
				}
			}else{
				//导出报表按钮设置
					$exportstr ='';
					if(isset($isexport) && $isexport){
						//$exportstr ='&nbsp;<input type="submit" id="searchFormExport"  name="searchFormExport" value="'.Pft_I18n::trans("ec_dd_daochubaobiao").'" class="btn">';
						$exportstr ='&nbsp;<input type="button" id="searchFormExport1" onmouseover="document.getElementById(\'exportDiv\').style.display=\'\';" onclick="document.getElementById(\'exportDiv\').style.display=\'\';"  name="searchFormExport1" value="'.Pft_I18n::trans("ec_dd_daochubaobiao").'" class="btn">';
						$exportstr.="<div id='exportDiv' style='display:none;position:absolute;height:25px;width:60px;background-color:#ffe;' onmouseout='this.style.display=\"none\"' onmouseover='this.style.display=\"\"' >
						
						<ul style='list-style:none'>
						<li>".i18ntrans('#选择格式')."</li>
						<li><input type='radio' name='exportformat' id='exportformat' value='csv' checked>.csv</li>
						<li><input type='radio' name='exportformat' id='exportformat' value='xls'>.xls&nbsp;</li>
						<li><input type='submit' class='btn' id='searchFormExport' name='searchFormExport' value='".i18ntrans('#确定')."'></li>
						</ul>
						</div>";
					}
										
					//如果没有修改搜索条件，不会影响总记录的条数<tr>
					$output_searchs .= '<td colspan="'.($number*4).'">
					<div style="clear:both;text-align:center;border:1px">
					<input type="submit" id="searchFormSubmit" value="'.Pft_I18n::trans("SEARCH").'" class="btn">'.$exportstr.$exportfilestr.'
					</div>
					</td>'."\n";	
			}
			$output_searchs .= '</tr></table>';
			//$output_searchs .= '<div style="clear:both">&nbsp;</div>';
			$output_searchs .= '</div>';
			
			/**
			 * 如果有排序定义，输出排序表单域
			 */
			if( is_array( $grid[Pft_Util_Grid::GRID_ORDERBYS] )
			 && count( $grid[Pft_Util_Grid::GRID_ORDERBYS][Pft_Util_Grid_Searchs::DEF_ORDERBYS] ) ){
				$output_searchs .= '<input type="hidden" name="'.Pft_Util_Grid_Searchs::DEF_ORDERBYS.'" id="searchFormOrderBy'.$specSearchFormId.'" value="'.$grid[Pft_Util_Grid::GRID_ORDERBYS][Pft_Util_Grid_Searchs::DEF_ORDERBYS][0].'">';
				$output_searchs .= '<input type="hidden" name="'.Pft_Util_Grid_Searchs::DEF_ORDERBYORDERS.'" id="searchFormOrderByOrder'.$specSearchFormId.'" value="'.$grid[Pft_Util_Grid::GRID_ORDERBYS][Pft_Util_Grid_Searchs::DEF_ORDERBYORDERS][0].'">';
				$orderByCols[$grid[Pft_Util_Grid::GRID_ORDERBYS][Pft_Util_Grid_Searchs::DEF_ORDERBYS][0]] 
				      = $grid[Pft_Util_Grid::GRID_ORDERBYS][Pft_Util_Grid_Searchs::DEF_ORDERBYORDERS][0]==Pft_Util_Grid_Searchs::DESC?"↓":"↑";
			}else{
				$output_searchs .= '<input type="hidden" name="'.Pft_Util_Grid_Searchs::DEF_ORDERBYS.'" id="searchFormOrderBy'.$specSearchFormId.'" value="">'."\n";
				$output_searchs .= '<input type="hidden" name="'.Pft_Util_Grid_Searchs::DEF_ORDERBYORDERS.'" id="searchFormOrderByOrder'.$specSearchFormId.'" value="">'."\n";
			}
			/**
			 * 这是order by 的js脚本
			 * //已写到 common.js 里了
			 */
			$descSign = Pft_Util_Grid_Searchs::DESC;
			$ascSign  = Pft_Util_Grid_Searchs::ASC;
			$output_searchs .= <<<EOT
<script>
function orderby{$specSearchFormId}(colName){
	document.getElementById("searchFormOrderBy{$specSearchFormId}").value=colName;
	var orderSignObj = document.getElementById("searchFormOrderByOrder{$specSearchFormId}");
	if(orderSignObj.value=="{$ascSign}"){
		orderSignObj.value="{$descSign}"
	}else{
		orderSignObj.value="{$ascSign}"
	}
	document.getElementById("searchform{$specSearchFormId}").submit();}
</script>
EOT;

			/**
			 * 如果有页码定义，输出页码表单域
			 * @todo page的显示也要带上form id
			 */
			if( is_array( $grid[Pft_Util_Grid::GRID_PAGER] ) ){
				//这是翻页的js脚本
				//已写到 common.js 里了
				$output_searchs .= <<<EOT
						<script>function gotoPage(pn){document.getElementById("searchFormPageNum").value=pn;document.getElementById("searchform").submit();}</script>
EOT;
				//这里不显示 PAGER_VAR_PAGE_NUM 是为了 按 search 后进入到第1页
				$output_searchs .= '<input type="hidden" name="'.Pft_Util_Pager::PAGER_VAR_PAGE_NUM.'" id="searchFormPageNum" value="">';
				$output_searchs .= '<input type="hidden" name="'.Pft_Util_Pager::PAGER_VAR_PAGE_SIZE.'" id="searchFormPageSize" value="'.$grid[Pft_Util_Grid::GRID_PAGER][Pft_Util_Pager::PAGER_VAR_PAGE_SIZE].'">';
				$output_searchs .= '<input type="hidden" name="'.Pft_Util_Pager::PAGER_VAR_TOTAL.'" id="searchFormPageTotal" value="'.$grid[Pft_Util_Grid::GRID_PAGER][Pft_Util_Pager::PAGER_VAR_TOTAL].'">';
				//var_dump( $output_searchs );				
			}
			$output_searchs .= "</div>\n";
			$output_searchs .= "</div>\n";
		}
		$output_searchs .= '</form>';
		
		/**
		 * 这里开始输出数据信息
		 */
		$output_body  = "";
		$output_body .= '<div class="grid_body">'."\n";
		// style="width:100%;overflow:auto;" height:600px;
		$output_body .= '<table class="grid" cellspacing="1" '.($gridId?'id="'.$gridId.'"':'').' >'."\n";
		/**
		 * 输出 header col 头信息
		 */
		$output_body .= "<thead>\n";
		if( $isDefCols ){
			$output_body .= "<tr>";
			foreach ( $cols as $col ){
				$col_title = ( is_null( $col["title"] ) )?Pft_I18n::trans($col["colname"]):$col["title"];
				if( trim($col[Pft_Util_Grid::COL_COLNAME]) != "" && $col["sortable"] ){
					$orderBySign = ( key_exists( $col[Pft_Util_Grid::COL_COLNAME], $orderByCols ) )?$orderByCols[$col[Pft_Util_Grid::COL_COLNAME]]:"";

					if( $output_searchs ){
						//如果在 $col["sortable"] 中不是 boolean，那么就是填写的 order by 的值
						if( is_bool( $col["sortable"] ) ){
							$orderByColname = $col["colname"];
						}else{
							$orderByColname = $col["sortable"];
						}
						
						//如果有search信息，则输出order by
						$output_body .= "<th nowrap=\"true\"><a href=\"javascript:orderby{$specSearchFormId}('".addslashes($orderByColname)."')\">".$col_title."</a>".$orderBySign.$col["colext"]."</th>";
					}else{
						//否则不输出order by脚本
						$output_body .= "<th nowrap=\"true\">".$col_title."</th>";
					}
				}else{
					$output_body .= "<th nowrap=\"true\">".$col_title.$col["colext"]."</th>";					
				}
			}
			$output_body .= "</tr>\n";
		}else{
			if( count( $datas ) ){
				$row = current( $datas );
				if( is_array( $row ) ){
					$output_body .= "<tr>";
					foreach ( $row as $key => $col ){
						$output_body .= "<th>".Pft_I18n::trans($key)."</th>";
					}
					$output_body .= "</tr>\n";
				}			
			}
		}
		$output_body .= "</thead>\n";
		
		//导出报表，jute 20080529
		/**
		 * @todo 此处判断不对
		 */
		if(isset($_REQUEST['searchFormExport']) && $_REQUEST['searchFormExport']){//导出报表
			$baobiaodata = array();
			$biaotou = array();
			
			$exportCol = $grid[Pft_Util_Grid::GRID_EXPORT_COL];
			//报头设置
			if( $isDefCols ){
				if( is_array($exportCol) ){
					foreach ($cols as $colName => $val){
						if( !in_array($colName,$exportCol) ){
							unset($cols[$colName]);
						}
					}
				}
				foreach ( $cols as $col ){
					$biaotou[] = self::clearHtml( ( is_null( $col["title"] ) )?Pft_I18n::trans($col["colname"]):$col["title"] );
				}
			}else{
				if( count( $datas ) ){
					$row = current( $datas );
					if( is_array( $row ) ){
						foreach ( $row as $key => $col ){
							$biaotou[] = Pft_I18n::trans($key);
						}
					}			
				}
			}
			
			$baobiaodata[] =$biaotou;
			if( is_array( $datas ) && count( $datas ) ){
				$output_arr = self::_getRenderedDataByGridData( $datas, $cols );
				foreach ( $output_arr as $row ){
					$therow = array();
					if( !is_array( $row ) )continue;
					if( $isDefCols ){
						reset( $cols );
						foreach ( $cols as $col ){
							$therow[] = current( $row );
							next( $row );
						}
					}else{
						foreach ( $row as $col ){
							$therow[] = $col;
						}
					}
					$thenewrow = array();
					foreach ($therow as $col){
						//$thenewrow[] = htmlspecialchars(self::clearHtml($col));
						$thenewrow[] = self::clearHtml($col); //导出时不用htmlspecialchars
					}
					$baobiaodata[] = $thenewrow;
				}
			}
			//导出报表
			if($exportfile_name){
				//$file=Pft_Util_Export::ExporttoCsv($baobiaodata,$exportfile_name,true);
				if($exportfile_format=='csv'){
					$file=Pft_Util_Export::ExportToCsv($baobiaodata,$exportfile_name,true);
				}else{
					$file=Pft_Util_Export::ExportToXls($baobiaodata,$exportfile_name,true);
				}
			}else{
				//$file=Pft_Util_Export::ExporttoCsv($baobiaodata,"Export");
				if($exportfile_format=='csv'){
					$file=Pft_Util_Export::ExportToCsv($baobiaodata,"Export");
				}else{
					$file=Pft_Util_Export::ExportToXls($baobiaodata,"Export");
				}
			}
			if($file){
				$exportfilestr = '';
				//if(isset($exportfile) && $exportfile){
					$exportfilestr = "<div style='text-align:center;' class='notice'>&nbsp;<font><a href='".$file."' class='btn'>".Pft_I18n::trans('JT_DINGDAN_XIAZAIWENJIAN')."</a></font>&nbsp;<a href='#' onclick='window.history.go(-1);return false;' class='btn'>".Pft_I18n::trans('返回')."</a></div>";
				//}
				//$this->_exportfile = $file;
			}
			$rev[Pft_Util_Grid::GRID_DATAS] = $exportfilestr;		
			return $rev;
		}
		/**
		 * Body 信息
		 */
		$output_body .= "<tbody>\n";
		if( is_array( $datas ) && count( $datas ) ){
			$output_arr = self::_getRenderedDataByGridData( $datas, $cols );
			foreach ( $output_arr as $row ){
				if( !is_array( $row ) )continue;
				$output_body .= "<tr>";
				if( $isDefCols ){
					reset( $cols );
					foreach ( $cols as $col ){
						$output_body .= "<td {$col["coltags"]}>".current( $row )."</td>";
						next( $row );
					}
				}else{
					foreach ( $row as $col ){
						$output_body .= "<td>". $col ."</td>";
					}
				}
				$output_body .= "</tr>\n";
			}
			
//			$showText = "";
//			foreach ( $datas as $row ){
//				if( !is_array( $row ) )continue;
//				$output_body .= "<tr>";
//				if( $isDefCols ){
//					reset( $cols );
//					foreach ( $cols as $col )
//					{
//						if( isset($col["render"]) && $col["render"] != "" )
//						{
//							$showText = "";
//							@eval('$showText = '.$col["render"].';');
//						}
//						else
//						{
//							if( isset($col["colname"]) ){
//								$showText = @$row[$col["colname"]];
//							}else{
//								$showText = "";
//							}
//						}
//						$output_body .= "<td {$col["coltags"]}>".$showText."</td>";					
//					}
//				}else{
//					foreach ( $row as $col ){
//						$output_body .= "<td>". $col ."</td>";
//					}
//				}
//				$output_body .= "</tr>\n";
//			}
		}//end if( is_array
		$output_body .= "</tbody>\n";
		$output_body .= "</table>\n";
		$output_body .= "</div>\n";

		$output_page = "";
		//if( is_array( $grid[Pft_Util_Grid::GRID_PAGER] ) && $grid[Pft_Util_Grid::GRID_PAGER][Pft_Util_Pager::PAGER_VAR_PAGE_COUNT] > 1 ){
		if( is_array( $grid[Pft_Util_Grid::GRID_PAGER] ) && $grid[Pft_Util_Grid::GRID_PAGER][Pft_Util_Pager::PAGER_VAR_PAGE_COUNT] > 0 ){	// 只要存在数据就显示 GRID FOOTER，具体显示哪些元素由 toHtml 函数内判断 //bobit Tue Dec 11 10:13:54 CST 200710:13:54
			$output_page  = '<div class="search_container">'."\n";
			$output_page .= Pft_View_Helper_Pager::toHtml( $grid[Pft_Util_Grid::GRID_PAGER], '"javascript:gotoPage($pg)"' );
			$output_page .= '</div>'."\n";
		}
		
		$rev[Pft_Util_Grid::GRID_SEARCHS] = $output_searchs;
		$rev[Pft_Util_Grid::GRID_DATAS] = $output_body;
		$rev[Pft_Util_Grid::GRID_PAGER] = $output_page;		
		return $rev;
	}

	/**
	 * 将建立grid并输出到数组
	 *
	 * @param array $grid
	 */
	public static function buildGridToDataArray( $grid ){
		/**
		 * 这里判断数据是否符合规则
		 * 应该根据 Shema 判断
		 */
		if( !is_array( $grid ) ){
			$e = new Pft_Exception(Pft_I18n::trans("ERR_INVALID_DATATYPE"));
			throw $e;
		}

		if( isset( $grid[ Pft_Util_Grid::GRID_COLS ] )
		 && count( $grid[ Pft_Util_Grid::GRID_COLS ] ) > 0 ){
			$isDefCols = true;
			$cols = $grid[ Pft_Util_Grid::GRID_COLS ];
		}else{
			$isDefCols = false;
			$cols = null;
		}

		$datas = $grid[ Pft_Util_Grid::GRID_DATAS ];

		/**
		 * 这里开始输出数据信息
		 */
		$output_arr  = array();

/*
		$row_arr = array();
		if( $isDefCols )
		{
			foreach ( $cols as $col )
			{
				$col_title = ( is_null( $col["title"] ) )?Pft_I18n::trans($col["colname"]):$col["title"];
				if( trim($col[Pft_Util_Grid::COL_COLNAME]) != "" && $col["sortable"] ){
					$row_arr[] = $col_title;
				}else{
					$row_arr[] = $col_title.$col["colext"];
				}
			}
		}
		else
		{
			$row = current( $datas );
			if( is_array( $row ) )
			{
				foreach ( $row as $key => $col )
				{
					$row_arr[] = Pft_I18n::trans($key);
				}
			}
		}
		$output_arr[] = $row_arr;
*/

		/**
		 * Body 信息
		 */
//		if( is_array( $datas ) )
//		{
//			$showText = "";
//			foreach ( $datas as $row ){
//				$row_arr = array();
//				
//				if( !is_array( $row ) )continue;
//				if( $isDefCols ){
//					reset( $cols );
//					foreach ( $cols as $col ){
//						if( isset($col["render"]) && $col["render"] != "" ){
//							$showText = "";
//							@eval('$showText = '.$col["render"].';');
//						}else{
//							if( isset($col["colname"]) ){
//								$showText = @$row[$col["colname"]];
//							}else{
//								$showText = "";
//							}
//						}
//						$row_arr[] = $showText;					
//					}
//				}else{
//					foreach ( $row as $col ){
//						$row_arr[] = $col;
//					}
//				}
//				$output_arr[] = $row_arr;
//			}
//		}//end if( is_array
		$output_arr = self::_getRenderedDataByGridData( $datas, $cols );

		return $output_arr;
	}
	
	/**
	 * 根据 GridData render一下
	 *
	 */
	private static function _getRenderedDataByGridData( $datas, $cols = null ){
		$output_arr = array();

		if( is_array( $datas ) ){
			$showText = "";
			if( $cols ){
				foreach ( $datas as $rowKey => $row ){
					$row_arr = array();

					if( !is_array( $row ) )continue;
					reset( $cols );
					$colNum = 0;
					foreach ( $cols as $colKey => $col ){
						/**
						 * 增加对合计的处理
						 * @author terry
						 * @version 0.1.0
						 * Wed Oct 10 19:11:55 CST 2007
						 */
						if( $rowKey === Pft_Util_Grid_Searchs::DEF_SUM || $rowKey === Pft_Util_Grid_Searchs::DEF_TOTAL ){
							//注意，这里要用 === ，否则第一行会出错
							if( $colNum == 0 ){
								$showText = '<b>'.Pft_I18n::trans($rowKey).'</b>';
							}else{
								if( @$row[$col["colname"]] && isset($col["render"]) && $col["render"] != "" ){
									//有值才显示
									$showText = "";
									eval('$showText = '.$col["render"].';');
								}else{
									$showText = '<b>'.htmlspecialchars(@$row[$col["colname"]]).'</b>';									
								}
							}
						}else{
							if( isset($col["render"]) && $col["render"] != "" ){
								$showText = "";
								eval('$showText = '.$col["render"].';');

								if(@$_REQUEST['debug_z']){
									print "<pre>";
									print_r($showText);
									print "</pre>";
								}
							}else{
								if( isset($col["colname"]) ){
									$showText = htmlspecialchars(@$row[$col["colname"]]);
								}else{
									$showText = "";
								}
							}
						}
						$row_arr[] = $showText;
						$colNum ++;
					}
					$output_arr[] = $row_arr;
				}
			}else{
				$output_arr = $datas;
			}
		}
		return $output_arr;
	}
	
	public static function clearHtml( $col ){
		return trim(strip_tags( $col ));
//		$rev = '';
//		//if( strpos( $col, '<a' ) !== false ){
//		if( strpos( $col, '<' ) !== false ){
//			if( stripos( $col, '<a' ) !== false
//			|| stripos( $col, '<div' ) !== false
//			|| stripos( $col, '<font' ) !== false
//			|| stripos( $col, '<span' ) !== false
//			|| stripos( $col, '<select' ) !== false
//			|| stripos( $col, '<input' ) !== false
//			|| stripos( $col, '<b' ) !== false
//			){
//				//$col='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$col;
//				$divId = "clear_html_div_".mt_rand(1000,9999);
//				$col = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><div id="'.$divId.'">'.$col.'</div>';
//				$doc = DOMDocument::loadHTML($col);
//				if($doc){
//					//$bodyS = $doc->getElementsByTagName('a');
//					$bodyS = $doc->getElementsByTagName('div');
//					if( $bodyS && $bodyS->length > 0 ){
//						$body = $bodyS->item(0);
//						$rev = $body->textContent;
//					}
//					/**
//					 * 不知为何 getElementById 不起作用..
//					 * @author terry
//					 * @version 0.1.0
//					 * Tue Jun 10 11:27:18 CST 2008
//					 */
//					/*
//					$clearDiv = $doc->getElementById($divId);
//					if( $clearDiv ){
//					$thenewrow[] = htmlspecialchars($clearDiv->textContent);
//					}
//					*/
//				}
//			}else{
//				$rev = $col;
//			}
//		}else{
//			$rev = $col;
//		}
//		
//		return $rev;
	}
}