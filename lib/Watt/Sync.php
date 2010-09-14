<?
/**
 * 数据库同步管理类
 *  @author jute
 */
class Watt_Sync
{
	/**
	 * 主动输入接口
	 * 
	 * 功能：从指定的被动输出接口获取序列文件存放到本地
	 * 参数：无
	 * 返回值：无
	 */
	public static function InitiativeInput()
	{
		
		$loger = new Watt_Log_File( 'sync' );
		//访问权限判断
		if(!Watt_Sync_MessageListManage::interfaceAuth('InitiativeInput')){
			$loger->log( '没有访问主动输入接口的权限' );
			exit;
		}
		//判断是否锁定		
		if (Watt_Sync_MessageListManage::isLock('input'))
		{//如果锁定
			exit;
		}
		//锁定接口		
		Watt_Sync_MessageListManage::AddLock('input');
		//读取本地设置，找到要访问的被动输出接口
		$passiveOutputAddress = Watt_Sync_MessageListManage::getPassiveOutputInterface();
		
		if (is_array($passiveOutputAddress) && count($passiveOutputAddress))
		{//被动输出接口数组，主动输入，主动输出方式 同步模式（SyncMode）为1时执行			
		}
		else if ($passiveOutputAddress)
		{//同步模式（SyncMode）不为1时执行					
			//访问被动输出接口,获取序列数据（消息序列和消息SQL）
			$data = array('validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword());
			$msgALL = BaseOption::postToHost($passiveOutputAddress,$data);			
			$msgALL = unserialize(urldecode($msgALL));
			$msgList = $msgALL['msgList'];
			$msgSql = $msgALL['msgSql'];
			//-------------------------处理sql消息 start-------------------------//	
			//sql消息的处理
			if(is_array($msgSql) && count($msgSql)){
				foreach ($msgSql as $k => $v){
					$re = Watt_Sync_MessageListManage::createMsgSql($v,$k);
					if($re==$k){
						//写日志
						$str = "在内部创建消息SQL:".$k."成功，内容:".$v;
						$loger->log( $str );
						//告诉被访问方的服务器更改序列文件(返回更改成功的序列标识)
						$data = array('msgSign' => urlencode(serialize($k)),
										'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword(),
										'msgType' =>'msgSql'
									);
						$re=BaseOption::postToHost($passiveOutputAddress,$data);
						if($re=='1'){//删除成功
							$loger->log( '删除外部msgsql:'.$k.'成功' );
						}else{
							$loger->log( '删除外部msgsql:'.$k.'失败' );
						}
					}
					else
					{
						//写日志
						$str = "在内部创建消息SQL:".$k."失败，内容：".$v;
						$loger->log( $str );
					}
				}
			}			
			//-------------------------处理sql消息 end-------------------------//	
			
			//-------------------------处理消息序列 start-------------------------//	
			//-----------------处理unknown目录中的 start-----------------//	
			//判断是否有未知的消息序列
			$unknownMsglistArray = Watt_Sync_MessageListManage::getAllUnknownMsgListSign();	
			if (is_array($unknownMsglistArray) && count($unknownMsglistArray)){	
				$loger->log( '存在未知消息序列' );
				//告诉被访问方的服务器更改序列文件(返回更改成功的序列标识)
				$data = array('msgSign' => urlencode(serialize($unknownMsglistArray)),
								'validatePassword' => Watt_Sync_MessageListManage::getValidatePassword(),
								'msgType' => 'msgList'
								);
				
				//删除序列
				$del_result_unknown = BaseOption::postToHost($passiveOutputAddress,$data);			
				$n_unknown=10;							
				while($n_unknown>1){							
					if ($del_result_unknown != 1 ){
						//没有返回删除成功的标记
						$del_result_unknown = BaseOption::postToHost($passiveOutputAddress,$data);
					}else{
						$loger = new Watt_Log_File( 'sync' );
						$loger->log( '未知消息序列确认成功' );
						//从未知目录移到正常目录							
						Watt_Sync_MessageListManage::moveMsglistUnknown($unknownMsglistArray);											
						//Watt_Sync_MessageListManage::moveMsglistUnknown($msgList_execute);
						break;
					}
					$n_unknown--;
				}
				//循环n次后还没有删除成功
				if ($del_result_unknown!=1){								
					//解除锁定
					Watt_Sync_MessageListManage::DelLock('input');
					exit;
				}
			}
			//-----------------处理unknown目录中的 end-----------------//
			
			$msgList_execute=array();	
			$num_del = Watt_Sync_MessageListManage::getNumOnceDel();//一次删除消息序列的个数
			//消息序列的处理			
			if (is_array($msgList) && count($msgList))
			{				
				foreach($msgList as $key => $val)
				{
					$str=Watt_Sync_MessageListManage::formatMsg($val);
					$loger = new Watt_Log_File( 'sync' );
					$loger->log( $str );					
						
					//调用消息序列管理器，更改本地消息序列
					if(Watt_Sync_MessageListManage::createMsgList($val,$key,'unknown'))
					{//成功		
						//exit;					
						//写日志
						$str = "在内部创建消息序列:".$key."成功";	
						$loger = new Watt_Log_File( 'sync' );
						$loger->log( $str );
												
						//记录执行成功的消息序列到数组
						$msgList_execute[] = $key;						
					}
					else
					{//写序列失败
						$str = "在内部创建消息序列:".$key."失败";	
						$loger = new Watt_Log_File( 'sync' );
						$loger->log( $str );
						
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('input');
						exit;
					}
					
					//告诉被访问方的服务器删除执行完成的序列文件(返回更改成功的序列标识)
					if (count($msgList_execute) == $num_del)
					{//达到要求的数目后删除
						//告诉被访问方的服务器更改序列文件(返回更改成功的序列标识)
						$data = array('msgSign' => urlencode(serialize($msgList_execute)),
										'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword(),
										'msgType' =>'msgList'
										);						
						//删除序列
						$del_result = BaseOption::postToHost($passiveOutputAddress,$data);
						$n=10;							
						while($n>1){							
							if ($del_result != 1 ){
								//没有返回删除成功的标记
								$del_result = BaseOption::postToHost($passiveOutputAddress,$data);
							}else{
								break;
							}
							$n--;
						}
						//循环n次后还没有删除成功
						if ($del_result!=1){
							//解除锁定
							Watt_Sync_MessageListManage::DelLock('input');
							exit;
						}else{	
							//转移消息序列到outside目录
							Watt_Sync_MessageListManage::moveMsglistUnknown($msgList_execute);
							$msgList_execute = array();
						}
					}					
				}
				//告诉被访问方的服务器删除执行完成的序列文件(返回更改成功的序列标识)
				if (count($msgList_execute))
				{//循环完成后没有达到要求删除数目的删除
					$data = array('msgSign' => urlencode(serialize($msgList_execute)),
									'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword(),
									'msgType' =>'msgList'
									);						
					//删除序列
					$del_result = BaseOption::postToHost($passiveOutputAddress,$data);
					$n=10;							
					while($n>1){							
						if ($del_result != 1 ){
							//没有返回删除成功的标记
							$del_result = BaseOption::postToHost($passiveOutputAddress,$data);
						}else{
							break;
						}
						$n--;
					}
					//循环n次后还没有删除成功
					if ($del_result!=1){
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('input');
						exit;
					}else{				
						//从未知移动到外部
						Watt_Sync_MessageListManage::moveMsglistUnknown($msgList_execute);
						$msgList_execute = array();
					}
				}					
			}
			//-------------------------处理消息序列 end-------------------------//
		}
		//解除锁定
		Watt_Sync_MessageListManage::execMsgList();	
		Watt_Sync_MessageListManage::DelLock('input');
	}
	
	/**
	 * 主动输出接口
	 * 
	 * 功能：解析本地的序列文件向指定的被动输入接口发送数据
	 * 
	 * 参数：无
	 * 返回值：无
	 */
	public static function InitiativeOutput()
	{		
		//echo microtime()."<br>";
		$loger = new Watt_Log_File( 'sync' );
		//访问权限判断
		if(!Watt_Sync_MessageListManage::interfaceAuth('InitiativeOutput')){
			//写日志
			$str = '没有访问此接口的权限';			
			$loger = new Watt_Log_File( 'sync' );
			$loger->log( $str );
			exit;
		}
		
		//判断是否锁定
		if (Watt_Sync_MessageListManage::isLock('output'))
		{//如果锁定
			exit;
		}
		//锁定接口
		Watt_Sync_MessageListManage::AddLock('output');
		//调用消息序列管理器，获取消息序列
		/*
		$msg = new MSG();
		$msg->openMsgPool();
		while( $msg->getMsg() ){
			
		}
		*/
		//获取所有的SQL消息
		$msgSqlArray = Watt_Sync_MessageListManage::getAllMessageSql();
		$msgSqlValue = array();
		
		if(is_array($msgSqlArray) && count($msgSqlArray)){
			foreach ($msgSqlArray as $k => $v){
				$rev = Watt_Sync_MessageListManage::executeMessageSql($v);
				if ($rev){
					//获取外部被动输入接口地址
					$passiveInputAddress = Watt_Sync_MessageListManage::getPassiveInputInterface('INSIDE_TPM');					
					if ($passiveInputAddress)
					{//获取地址成功
						//以post的方式向接口提交数据
						$data = array(
										'obj' =>'msgSqlValue',
										'msgList' => urlencode(serialize($rev)),
										'msgSign' => urlencode(serialize($k)),
										'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword()
									);
						if (BaseOption::postToHost($passiveInputAddress,$data))
						{//成功
							//删除消息SQL
							Watt_Sync_MessageListManage::delMessageSql($k);			
						}else{
							//移动消息SQL
							Watt_Sync_MessageListManage::moveMsgSql($k);
						}
					}
				}
			}
		}
		//消息序列的处理

		//------------外部unknown中的数据处理  start---------------//	
		//获取外部被动输入接口地址
		$passiveInputAddress = Watt_Sync_MessageListManage::getPassiveInputInterface('INSIDE_TPM');
	
		$passiveOutputAddress = Watt_Sync_MessageListManage::getPassiveOutputInterface();
		
		if($passiveOutputAddress){
			//获取外部unknown目录中的数据
			$data =array(
						'msgType' => 'msglist',
						'msgListDir' => 'unknown',
						'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword()
						);
			$msgList_unknown = BaseOption::postToHost($passiveOutputAddress,$data);			
			$msgSign_outsite_unknown = unserialize($msgList_unknown);
			if(is_array($msgSign_outsite_unknown) && count($msgSign_outsite_unknown))
			{//unknown目录中存在数据
				$msgSignArr = array_keys($msgSign_outsite_unknown);		
				//删除本地的消息
				//$result = Watt_Sync_MessageListManage::delMessageList($msgSignArr)
				$result = Watt_Sync_MessageListManage::moveMsglistOld($msgSignArr);
				if ($result)
				{//删除成功
					//告诉外部，将序列从unknown中移动到outside					
					$data = array(
											'obj' =>'file',
											'msgList' => urlencode(serialize('')),
											'msgListDir' => 'outside',
											'msgSign' => urlencode(serialize($msgSignArr)),
											'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword()
										);				
					$rev1 = BaseOption::postToHost($passiveInputAddress,$data);
					if ($rev1){
					}else{
						$loger->log( '移动消息序列到outside目录失败，访问接口:'.$passiveInputAddress );
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('output');
						exit;
					}
				}else{
					$loger->log( '删除内部消息序列失败' );
					//解除锁定
					Watt_Sync_MessageListManage::DelLock('output');
					exit;
				}
			}
		}			
		//------------外部unknown中的数据处理  end---------------//	
	
		//获取所有的消息序列
		$msgListArray = Watt_Sync_MessageListManage::getAllMessageList();	
		$msgList_execute=array();	
		$num_del = Watt_Sync_MessageListManage::getNumOnceDel();//一次删除消息序列的个数		
		if (is_array($msgListArray) && count($msgListArray) && $passiveInputAddress){
			$str = "访问的被动输入接口:".$passiveInputAddress;
			$loger = new Watt_Log_File( 'sync' );
			$loger->log( $str );
			foreach ($msgListArray as $key=>$val)
			{				
				foreach ($val as $k =>$v){
					//--------------------- 数据过滤器  start---------------------//
					//根据创建消息序列的服务器类型获取过滤器
					$filterNameList = Watt_Sync_MessageListManage::getMsgListFilter($v['syncServerType']);				
					//调用过滤器过滤数据				
					if(is_array($filterNameList) && count($filterNameList))
					{
						foreach ( $filterNameList as $filterName )
						{
							$aFilter = Watt_Sync_Filter::filterFactory( $filterName );
							if( $aFilter ){
								$v = $aFilter->filter( $v );
							}
						}
					}
					
					//如果字段为空，删除消息序列
					if((($v['operate']=='UPDATE'||$v['operate']=='INSERT') && !count($v['cols'])) || !count($v)){
						//删除序列
						//Watt_Sync_MessageListManage::delMessageList($key);
						unset($val[$k]);
						continue;
					}	
					//--------------------- 数据过滤器  end---------------------//
					if (count($msgList_execute)<($num_del-1))
					{
						$msgList_execute[$key] = $val;
					}else
					{//满足个数
						$msgList_execute[$key] = $val;
						$msgSignArr = array_keys($msgList_execute);	
						//向被动输入接口传数据，移动到未明目录
						$data = array(
										'obj' =>'file',
										'msgList' => urlencode(serialize($msgList_execute)),
										'msgListDir' => 'unknown',
										'msgSign' => urlencode(serialize('')),
										'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword()
									);	
						$rev = BaseOption::postToHost($passiveInputAddress,$data);	
						if ($rev == '1'){
							//删除
							//$result = Watt_Sync_MessageListManage::delMessageList($msgSignArr)
							$result = Watt_Sync_MessageListManage::moveMsglistOld($msgSignArr);
							if ($result)
							{//删除成功
								//以post的方式向接口提交数据
								$data = array(
												'obj' =>'file',
												'msgList' => urlencode(serialize('')),
												'msgListDir' => 'outside',
												'msgSign' => urlencode(serialize($msgSignArr)),
												'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword()
											);		
															
								$rev1 = BaseOption::postToHost($passiveInputAddress,$data);	
								if ($rev1 == '1'){
									//清空数组
									$msgList_execute=array();	
								}else{//移动失败								
									$loger->log( '移动消息序列到outside目录失败，访问接口:'.$passiveInputAddress );
									//解除锁定
									Watt_Sync_MessageListManage::DelLock('output');
									exit;
								}
							}else
							{//删除失败
								$loger->log( '删除内部消息序列失败' );
								//解除锁定
								Watt_Sync_MessageListManage::DelLock('output');

								exit;
							}
						}else
						{
							$loger->log( '移动消息序列到unknown目录失败，访问接口:'.$passiveInputAddress );
							//解除锁定
							Watt_Sync_MessageListManage::DelLock('output');
							exit;
						}	
					}
						
				}
				/*
				//--------------------- 数据过滤器  start---------------------//
				//根据创建消息序列的服务器类型获取过滤器
				$filterNameList = Watt_Sync_MessageListManage::getMsgListFilter($val['syncServerType']);				
				//调用过滤器过滤数据				
				if(is_array($filterNameList) && count($filterNameList))
				{
					foreach ( $filterNameList as $filterName )
					{
						$aFilter = Watt_Sync_Filter::filterFactory( $filterName );
						if( $aFilter ){
							$val = $aFilter->filter( $val );
						}
					}
				}
				
				//如果字段为空，删除消息序列
				if((($val['operate']=='UPDATE'||$val['operate']=='INSERT') && !count($val['cols'])) || !count($val)){
					//删除序列
					Watt_Sync_MessageListManage::delMessageList($key);
					continue;
				}	
				//--------------------- 数据过滤器  end---------------------//
				
				if (count($msgList_execute)<($num_del-1))
				{
					$msgList_execute[$key] = $val;
				}else
				{//满足个数
					$msgList_execute[$key] = $val;
					$msgSignArr = array_keys($msgList_execute);	
					//向被动输入接口传数据，移动到未明目录
					$data = array(
									'obj' =>'file',
									'msgList' => urlencode(serialize($msgList_execute)),
									'msgListDir' => 'unknown',
									'msgSign' => urlencode(serialize('')),
									'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword()
								);	
					$rev = BaseOption::postToHost($passiveInputAddress,$data);	
					if ($rev == '1'){
						//删除
						if (Watt_Sync_MessageListManage::delMessageList($msgSignArr))
						{//删除成功
							//以post的方式向接口提交数据
							$data = array(
											'obj' =>'file',
											'msgList' => urlencode(serialize('')),
											'msgListDir' => 'outside',
											'msgSign' => urlencode(serialize($msgSignArr)),
											'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword()
										);		
														
							$rev1 = BaseOption::postToHost($passiveInputAddress,$data);	
							if ($rev1 == '1'){
								//清空数组
								$msgList_execute=array();	
							}else{//移动失败								
								$loger->log( '移动消息序列到outside目录失败，访问接口:'.$passiveInputAddress );
								//解除锁定
								Watt_Sync_MessageListManage::DelLock('output');
								exit;
							}
						}else
						{//删除失败
							$loger->log( '删除内部消息序列失败' );
							//解除锁定
							Watt_Sync_MessageListManage::DelLock('output');
							exit;
						}
					}else
					{
						$loger->log( '移动消息序列到unknown目录失败，访问接口:'.$passiveInputAddress );
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('output');
						exit;
					}	
				}*/			
			}
			//最后不满足个数的操作
			if (is_array($msgList_execute) && count($msgList_execute))
			{
				$msgSignArr = array_keys($msgList_execute);					
				//向被动输入接口传数据，移动到未明目录
				$data = array(
								'obj' =>'file',
								'msgList' => urlencode(serialize($msgList_execute)),
								'msgListDir' => 'unknown',
								'msgSign' => urlencode(serialize('')),
								'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword()
							);	
				$rev = BaseOption::postToHost($passiveInputAddress,$data);					
				if ($rev){
					//删除
					//$result = Watt_Sync_MessageListManage::delMessageList($msgSignArr);
					$result = Watt_Sync_MessageListManage::moveMsglistOld($msgSignArr);
					if ($result)
					{//删除成功
						//以post的方式向接口提交数据
						$data = array(
										'obj' =>'file',
										'msgList' => urlencode(serialize('')),
										'msgListDir' => 'outside',
										'msgSign' => urlencode(serialize($msgSignArr)),
										'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword()
									);				
						$rev1 = BaseOption::postToHost($passiveInputAddress,$data);	
						if ($rev1 == '1'){
							//清空数组
							$msgList_execute=array();	
						}else{//移动失败								
							$loger->log( '移动消息序列到outside目录失败，访问接口:'.$passiveInputAddress );
							//解除锁定
							Watt_Sync_MessageListManage::DelLock('output');
							exit;
						}
					}else
					{//删除失败
						$loger->log( '删除内部消息序列失败' );
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('output');
						exit;
					}
				}else{
					$loger->log( '移动消息序列到unknown目录失败，访问接口:'.$passiveInputAddress );
					//解除锁定
					Watt_Sync_MessageListManage::DelLock('output');
					exit;
				}		
			}
		}
		//解除锁定
		Watt_Sync_MessageListManage::DelLock('output');
		//echo microtime()."<br>";
	}
	
	/**
	 * 被动输入接口	 
	 * 
	 * 功能：将数据写入到本地的序列文件或数据库。供本地tpm或主动输出接口调用
	 * 
	 * 参数：
	 * 	$obj :db表明是对数据库操作,file表明是对序列文件操作,msgSqlValue表明是消息SQL的值
	 *  $msgList：消息序列的内容（分解后的SQL）
	 *  $msgSign:消息序列唯一标识
	 *  $pwd：访问接口的密码
	 * 返回值：成功返回真，失败返回假
	 */
	public static function PassiveInput($obj,$msgList,$msgSign='',$pwd='',$msgListDir='')
	{	
		//访问权限判断
		if(!Watt_Sync_MessageListManage::interfaceAuth('PassiveInput'))return false;
		//验证密码
		if(!Watt_Sync_MessageListManage::passwordAuth($pwd))return false;

		if ($obj=='db')
		{//对数据库的操作							
			return Watt_Sync_MessageListManage::executeMessageList($msgList);
		}
		else if ($obj=='file')
		{//对文件的操作
			if ($msgSign != ''){
				if (is_array($msgSign) && count($msgSign))
				{
					if ($msgListDir=='outside')
					{//从unknown移动到outside
						return Watt_Sync_MessageListManage::moveMsglistUnknown($msgSign);
					}
				}else{						
					return Watt_Sync_MessageListManage::createMsgList($msgList,$msgSign);	
				}
				
			}else{
				return Watt_Sync_MessageListManage::createAllMsgList($msgList,$msgListDir);	
			}
		}
		else if ($obj=='msgSqlValue')
		{
			return Watt_Sync_MessageListManage::createMsgSqlValue($msgList,$msgSign);
		}
	}
	
	/**
	 * 被动输出接口
	 * 
	 * 功能：读取本地序列文件为主动输入接口提供数据
	 * 
	 * 参数：$msgSign:消息序列标识，调用方执行成功后返回时设置的参数。
	 *  	$pwd:访问接口的密码
	 * 		$msgType:消息类型，msgList表示消息序列，msgSQL表示消息SQL
	 * 		$msgListDir :''：所有的msgList,msgSql,'normal'所有的msgList,'unknown':unknown目录下的msgList
	 * 返回值：获取时返回消息序列，更改是无返回值
	 */
	public static function PassiveOutput( $msgSign='',$pwd='',$msgType='msgList',$msgListDir='')
	{	
		//访问权限判断
		//if(!Watt_Sync_MessageListManage::interfaceAuth('PassiveOutput'))return false;
		//验证密码
		if(!Watt_Sync_MessageListManage::passwordAuth($pwd))return false;
		if ($msgSign == '')
		{//获取消息序列，多个
			//调用消息序列管理器，获取消息序列
			if($msgListDir==''){
				return Watt_Sync_MessageListManage::getAllMessage();
			}else if ($msgListDir == 'unknown'){
				return Watt_Sync_MessageListManage::getAllMessageList('unknown');
			}
		}
		else
		{//删除消息序列
			if ($msgType == 'msgList'){
				//return Watt_Sync_MessageListManage::delMessageList($msgSign);	
				return Watt_Sync_MessageListManage::moveMsglistOld($msgSign);
			}
			else if ($msgType == 'msgSql')
			{
				return Watt_Sync_MessageListManage::delMessageSql($msgSign);
			}			
		}
	}
	
	/**
	 * 获取数据库信息
	 * 功能：获取本地的数据库信息
	 * 参数：$pwd 访问接口的密码
	 * @return 成功返回string,失败返回假
	 */
	public static function getDbInfo($pwd='')
	{
		//验证密码
		if(!Watt_Sync_MessageListManage::passwordAuth($pwd))return false;
		return Watt_Sync_MessageListManage::getmsg1();
	}			
	/**
	 * 获取数据库所有表的记录数
	 * 功能：获取本地的数据库信息
	 * 参数：$pwd 访问接口的密码
	 * @return 成功返回string,失败返回假
	 */
	public static function getDbAllTableCount($pwd='')
	{
		//验证密码
		if(!Watt_Sync_MessageListManage::passwordAuth($pwd))return false;		
		$re = Watt_Sync_MessageListManage::getDbAllTableCount();
		if(is_array($re) && count($re)){
			return urlencode(serialize($re));	
		}else {
			return '';
		}
	}	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * socket输入接口
	 *	将外部数据同步到内部服务器
	 */
	public static function sinput(){
		$loger = new Watt_Log_File( 'syncsocket' );
		//访问权限判断
		if(!Watt_Sync_MessageListManage::interfaceAuth('InitiativeInput')){
			$loger->log( '没有访问socket输入接口的权限' );
			exit;
		}
		
		//判断是否锁定
		if (Watt_Sync_MessageListManage::isLock('sinput'))
		{//如果锁定
			$loger->log( 'socket输入接口已锁定' );
			exit;
		}
		//锁定接口
		Watt_Sync_MessageListManage::AddLock('sinput');
		
		//判断服务端是否正常
		//-----------------处理unknown目录中的 start-----------------//
		//判断是否存在消息序列汇总文件,如果存在删除unkonwn下的所有消息序列和汇总文件并删除外部的消息序列汇总文件
		$result = Watt_Sync_MessageListManage::isexistLocalMsgListFile('unknown/','msgListfile.msg');
		if($result=='1'){
			//删除本地unknown下的消息序列汇总文件
			$result = Watt_Sync_SyncSocket::delFile('unknown/','msgListfile.msg');
			if($result != '1'){
				$loger->log( 'socket输入接口删除本地消息序列汇总文件失败' );	
				//解除锁定
				Watt_Sync_MessageListManage::DelLock('sinput');			
				exit;
			}
			
			//判断是否有未知的消息序列
			$unknownMsglistArray = Watt_Sync_MessageListManage::getAllUnknownMsgListSign();	
			if (is_array($unknownMsglistArray) && count($unknownMsglistArray))
			{
				//删除unknown目录下的所有msglist	
				$result = delMessageList($unknownMsglistArray,'unknown');
				if ($result!='1') {
					$loger->log( 'socket输入接口删除本地unknown目录下的消息序列文件失败' );	
					//解除锁定
					Watt_Sync_MessageListManage::DelLock('sinput');			
					exit;
				}
			}
			
			//删除外部sync下的消息序列汇总文件
			$result = Watt_Sync_SyncSocket::syncInterface('del','');
			if ($result != '1'){
				$loger->log( 'socket输入接口删除外部消息序列汇总文件失败' );	
				//解除锁定
				Watt_Sync_MessageListManage::DelLock('sinput');			
				exit;
			}
		}
		else
		{//不存在
			//判断是否有未知的消息序列
			$unknownMsglistArray = Watt_Sync_MessageListManage::getAllUnknownMsgListSign();	
			if (is_array($unknownMsglistArray) && count($unknownMsglistArray))
			{	
				//删除外部sync下的消息序列汇总文件
				$result = Watt_Sync_SyncSocket::syncInterface('del','');
				if ($result != '1'){
					$loger->log( 'socket输入接口删除消息序列汇总文件失败' );	
					//解除锁定
					Watt_Sync_MessageListManage::DelLock('sinput');			
					exit;
				}
							
				//告诉外部删除消息序列
				$result = Watt_Sync_SyncSocket::syncInterface('delmsglistsync',$unknownMsglistArray);
				if($result != '1'){
					$loger->log( 'socket输入接口删除sync目录下的消息序列文件失败' );	
					//解除锁定
					Watt_Sync_MessageListManage::DelLock('sinput');			
					exit;
				}
							
				//将消息序列从unknown移动到outsite
				$result = Watt_Sync_MessageListManage::moveMsglistUnknown($msgList_execute);
				if ($result != '1'){
					$loger->log( 'socket输入接口将消息序列从unknown移动到outsite失败' );	
					//解除锁定
					Watt_Sync_MessageListManage::DelLock('sinput');			
					exit;
				}
				
				$result = Watt_Sync_MessageListManage::execMsgList();
				if ($result !='1'){
					$loger->log( 'socket输入接口执行outsite目录下的消息序列文件失败' );	
					//解除锁定
					Watt_Sync_MessageListManage::DelLock('sinput');			
					exit;
				}			
			}
		}
		//-----------------处理unknown目录中的 end-----------------//		
		
		//向外部发送获取消息序列的命令
		$msgListFile_size = Watt_Sync_SyncSocket::syncInterface('getorder','');
		if (!$msgListFile_size){
			$loger->log( 'socket输入接口生成外部消息序列汇总文件内容失败' );
			//解除锁定
			Watt_Sync_MessageListManage::DelLock('sinput');
			exit;
		}
		
		//-------分块获取文件 start
		$count = ceil($msgListFile_size/1000);//将文件				
		for ($i=1;$i<=$count;$i++){
			$order_str = 'get_'.$i.'_'.$count;
			//获取指定段的文件内容
			$result = Watt_Sync_SyncSocket::syncInterface('get',$order_str);						
			if (!$result){
				$loger->log( 'socket输入接口获取外部消息序列汇总文件内容失败' );	
				//解除锁定
				Watt_Sync_MessageListManage::DelLock('sinput');			
				exit;
			} else {
				//写内部消息序列文件				
				//创建文件的条件判断				
				$re = Watt_Sync_MessageListManage::createdMsgListFile($result,$i);
				if ($re)
				{//创建成功
					
				}else
				{//失败
					$loger->log( 'socket输入接口创建本地消息序列汇总文件内容失败' );	
					//解除锁定
					Watt_Sync_MessageListManage::DelLock('sinput');			
					exit;
				}
			}
		}
		//-------分块获取文件 end
			
		//判断消息序列汇总文件大小是否一致
		$fsize = Watt_Sync_MessageListManage::getLocalMsgListFileSize('unknown/','msgListfile.msg');
		if ($msgListFile_size!=$fsize){
			$loger->log( 'socket输入接口消息序列汇总文件大小不一致' );	
			//解除锁定
			Watt_Sync_MessageListManage::DelLock('sinput');			
			exit;
		}
		
		//执行消息序列汇总文件
		$msgsignarr = Watt_Sync_MessageListManage::execMsgListFile();
		if(is_array($msgsignarr) && count($msgsignarr)){
			//删除外部sync下的消息序列汇总文件
			$result = Watt_Sync_SyncSocket::syncInterface('del','');
			if ($result != '1'){
				$loger->log( 'socket输入接口删除消息序列汇总文件失败' );	
				//解除锁定
				Watt_Sync_MessageListManage::DelLock('sinput');			
				exit;
			}
						
			//告诉外部删除消息序列
			$result = Watt_Sync_SyncSocket::syncInterface('delmsglistsync',$msgsignarr);
			if($result != '1'){
				$loger->log( 'socket输入接口删除sync目录下的消息序列文件失败' );	
				//解除锁定
				Watt_Sync_MessageListManage::DelLock('sinput');			
				exit;
			}
						
			//将消息序列从unknown移动到outsite
			$result = Watt_Sync_MessageListManage::moveMsglistUnknown($msgList_execute);
			if ($result != '1'){
				$loger->log( 'socket输入接口将消息序列从unknown移动到outsite失败' );	
				//解除锁定
				Watt_Sync_MessageListManage::DelLock('sinput');			
				exit;
			}
			
			$result = Watt_Sync_MessageListManage::execMsgList();
			if ($result !='1'){
				$loger->log( 'socket输入接口执行outsite目录下的消息序列文件失败' );	
				//解除锁定
				Watt_Sync_MessageListManage::DelLock('sinput');			
				exit;
			}			
		}else{
			$loger->log( 'socket输入接口执行消息序列汇总文件失败' );	
			//解除锁定
			Watt_Sync_MessageListManage::DelLock('sinput');			
			exit;
		}
		//解除锁定
		Watt_Sync_MessageListManage::DelLock('sinput');		
	}
	
	
	/**
	 * socket输出接口
	 *	将内部数据同步到外部服务器
	 */
	public static function soutput(){		
		echo microtime()."<br>";
		
		$loger = new Watt_Log_File( 'syncsocket' );
		//访问权限判断
		if(!Watt_Sync_MessageListManage::interfaceAuth('InitiativeOutput')){
			$loger->log( '没有访问socket输出接口的权限' );
			exit;
		}
		
		//判断是否锁定
		if (Watt_Sync_MessageListManage::isLock('soutput'))
		{//如果锁定
			$loger->log( 'socket输出接口已锁定' );
			exit;
		}
		//锁定接口
		Watt_Sync_MessageListManage::AddLock('soutput');
		//------------外部unknown中的数据处理  start---------------//	
		//------------外部unknown中的数据处理  end---------------//
		
		//------------外部消息序列的处理  start---------------//	
		//获取所有的消息序列
		$msgListArray = Watt_Sync_MessageListManage::getAllMessageList();		
		$msgList_execute=array();	
		$num_del = Watt_Sync_MessageListManage::getNumOnceDel();//一次删除消息序列的个数		
		if (is_array($msgListArray) && count($msgListArray)){
			foreach ($msgListArray as $key=>$val)
			{				
				//--------------------- 数据过滤器  start---------------------//
				//根据创建消息序列的服务器类型获取过滤器
				$filterNameList = Watt_Sync_MessageListManage::getMsgListFilter($val['syncServerType']);				
				//调用过滤器过滤数据				
				if(is_array($filterNameList) && count($filterNameList))
				{
					foreach ( $filterNameList as $filterName )
					{
						$aFilter = Watt_Sync_Filter::filterFactory( $filterName );
						if( $aFilter ){
							$val = $aFilter->filter( $val );
						}
					}
				}
				
				//如果字段为空，删除消息序列
				if((($val['operate']=='UPDATE'||$val['operate']=='INSERT') && !count($val['cols'])) || !count($val)){
					//删除序列
					Watt_Sync_MessageListManage::delMessageList($key);
					continue;
				}	
				//--------------------- 数据过滤器  end---------------------//
				
				
				
				
				if (count($msgList_execute)<($num_del-1))
				{
					$msgList_execute[$key] = $val;
				}else
				{//满足个数					
					$msgList_execute[$key] = $val;
					$msgSignArr = array_keys($msgList_execute);
					$data_str = '';
					$data=array();
					$data = array('msgList'=>$msgList_execute,
								  'msgListDir'=>'unknown',
								  'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword(),
								);
					$data_str =urlencode(serialize($data));//汇总文件内容					
					$msglistfilesize = strlen($data_str);//汇总文件大小
					
					//生成本地消息序列汇总文件
					$re = Watt_Sync_MessageListManage::createdMsgListFileSync($data_str);
		  		 	if ($re!='1'){		  		 		
				    	$loger->log( 'socket输出接口创建内部消息序列汇总文件失败' );
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('soutput');
						exit;
				    }
				    $dd_chunk = str_split($data_str,1000);	
					$count_arr = count($dd_chunk);//总块数
					foreach ($dd_chunk as $key =>$val)
					{//循环传输数据
						$order_str = "send_".($key+1)."_".$count_arr;		
						$count = strlen($order_str);
						$order_str = str_split($order_str,1);
						$temp_arr = array_fill($count, (20-$count), ' ');
						$order_str_arr = array_merge($order_str, $temp_arr);
						$order_str = '';
						foreach ($order_str_arr as $k =>$v){			
							$order_str .= $v;
						}
						
						$data = $order_str.$val;
						$result = Watt_Sync_SyncSocket::syncInterface('send',$data);
						if ($result!='1'){
							$loger->log( 'socket输出接口传送消息序列汇总文件失败' );
							//解除锁定
							Watt_Sync_MessageListManage::DelLock('soutput');
							exit;
						}
					}
					//获取外部消息序列汇总文件的大小，比较内外汇总文件			
					//删除内部消息序列汇总文件
					$result = Watt_Sync_MessageListManage::delFile('','msgListfile.msg');
					if($result != '1'){
						$loger->log( 'socket输入接口删除本地sync目录消息序列汇总文件失败' );	
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('soutput');			
						exit;
					}
					
					//解析外部消息序列汇总文件成消息序列
					$msgsignstr = Watt_Sync_SyncSocket::syncInterface('execmsgfile','');
					if ($result=='0'){
							$loger->log( 'socket输出接口解析外部消息序列汇总文件失败' );
							//解除锁定
							Watt_Sync_MessageListManage::DelLock('soutput');
							exit;
					}
					//删除内部消息序列
				    $msgsign = unserialize(urldecode($msgsignstr));
				    if(is_array($msgsign) && count($msgsign))
				    {
				    	$re = Watt_Sync_MessageListManage::delMessageList($msgsign);
			   			if ($re!='1'){
					    	$loger->log( 'socket输出接口删除内部消息序列失败' );
							//解除锁定
							Watt_Sync_MessageListManage::DelLock('soutput');
							exit;
				   		}	
				    }else {
				    	$loger->log( 'socket输出接口内部消息序列数据错误' );
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('soutput');
						exit;
				    }				    
				    
				    //移动外部消息序列到outsite目录
				    $result = Watt_Sync_SyncSocket::syncInterface('movemsglistoutsite',$msgsign);
					if ($result!='1'){
						$loger->log( 'socket输出接口移动外部消息序列到outsite目录失败' );
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('soutput');
						exit;
					}
					//执行外部outsite目录下的消息序列
				  /* $result = Watt_Sync_SyncSocket::syncInterface('execmsglist','');
					if ($result!='1'){
						$loger->log( 'socket执行外部outsite目录下的消息序列' );
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('soutput');
						exit;
					}*/
				  // exit;
				  $msgList_execute=array();
				}
			}
				//--- 未满个数end
				//$msgList_execute[$key] = $val;
					$msgSignArr = array_keys($msgList_execute);
					$data_str = '';
					$data=array();
					$data = array('msgList'=>$msgList_execute,
								  'msgListDir'=>'unknown',
								  'validatePassword'=>Watt_Sync_MessageListManage::getValidatePassword(),
								);
					$data_str =urlencode(serialize($data));//汇总文件内容					
					$msglistfilesize = strlen($data_str);//汇总文件大小
					
					//生成本地消息序列汇总文件
					$re = Watt_Sync_MessageListManage::createdMsgListFileSync($data_str);
		  		 	if ($re!='1'){		  		 		
				    	$loger->log( 'socket输出接口创建内部消息序列汇总文件失败' );
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('soutput');
						exit;
				    }
				    $dd_chunk = str_split($data_str,1000);	
					$count_arr = count($dd_chunk);//总块数
					foreach ($dd_chunk as $key =>$val)
					{//循环传输数据
						$order_str = "send_".($key+1)."_".$count_arr;		
						$count = strlen($order_str);
						$order_str = str_split($order_str,1);
						$temp_arr = array_fill($count, (20-$count), ' ');
						$order_str_arr = array_merge($order_str, $temp_arr);
						$order_str = '';
						foreach ($order_str_arr as $k =>$v){			
							$order_str .= $v;
						}
						
						$data = $order_str.$val;
						$result = Watt_Sync_SyncSocket::syncInterface('send',$data);
						if ($result!='1'){
							$loger->log( 'socket输出接口传送消息序列汇总文件失败' );
							//解除锁定
							Watt_Sync_MessageListManage::DelLock('soutput');
							exit;
						}
					}
					//获取外部消息序列汇总文件的大小，比较内外汇总文件			
					//删除内部消息序列汇总文件
					$result = Watt_Sync_MessageListManage::delFile('','msgListfile.msg');
					if($result != '1'){
						$loger->log( 'socket输入接口删除本地sync目录消息序列汇总文件失败' );	
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('soutput');			
						exit;
					}
					
					//解析外部消息序列汇总文件成消息序列
					$msgsignstr = Watt_Sync_SyncSocket::syncInterface('execmsgfile','');
					if ($result=='0'){
							$loger->log( 'socket输出接口解析外部消息序列汇总文件失败' );
							//解除锁定
							Watt_Sync_MessageListManage::DelLock('soutput');
							exit;
					}
					//删除内部消息序列
				    $msgsign = unserialize(urldecode($msgsignstr));
				    if(is_array($msgsign) && count($msgsign))
				    {
				    	$re = Watt_Sync_MessageListManage::delMessageList($msgsign);
			   			if ($re!='1'){
					    	$loger->log( 'socket输出接口删除内部消息序列失败' );
							//解除锁定
							Watt_Sync_MessageListManage::DelLock('soutput');
							exit;
				   		}	
				    }else {
				    	$loger->log( 'socket输出接口内部消息序列数据错误' );
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('soutput');
						exit;
				    }				    
				    
				    //移动外部消息序列到outsite目录
				    $result = Watt_Sync_SyncSocket::syncInterface('movemsglistoutsite',$msgsign);
					if ($result!='1'){
						$loger->log( 'socket输出接口移动外部消息序列到outsite目录失败' );
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('soutput');
						exit;
					}
					//执行外部outsite目录下的消息序列
				  /* $result = Watt_Sync_SyncSocket::syncInterface('execmsglist','');
					if ($result!='1'){
						$loger->log( 'socket执行外部outsite目录下的消息序列' );
						//解除锁定
						Watt_Sync_MessageListManage::DelLock('soutput');
						exit;
					}*/
				  // exit;
				//---未满个数 end 
		}
		
		//------------外部消息序列的处理  end---------------//	
		//解除锁定
		Watt_Sync_MessageListManage::DelLock('soutput');		
		//$end_time = time();
		echo '<br>'.microtime();
	}
	
}
?>