<?php
/**
 * @desc Socket.Class.php
 * @author Marty
 * @version Thu Jan 18 19:12:31 CST 2007 19:12:31 更新确认
 */

class Watt_Net_SocketServer extends Watt_Net_Socket
{
	private $host;
	private $port;
	private $sock;
	private $isConnected;
	
	public function __construct($host = 'localhost', $port = 10000)
	{
		$this->host=$host;
		$this->port=$port;
		$this->sock = $this->create();
		self::bind();
		self::listen();
	}
	
	public function ready()
	{	
		while(true){
			$msg = socket_accept($this->sock);               // 接受一个SOCKET
		    if (!$msg)
		    {
		        echo "socket_accept() failed:".socket_strerror ($msg)."\n";
		        break;
		    }
		    $welcome =parent::read();		   
		    socket_write ($msg, $welcome, strlen ($welcome));		    
		        $command = strtoupper (trim (socket_read ($msg, 1024)));
		        if (!$command)
		            break;
		        switch ($command)
		        {
		            case "HELLO":
		                $writer = "Hello Everybody!";
		                break;
		            case "QUIT":
		                $writer = "Bye-Bye";
		                break;
		            case "HELP":
		                $writer = "HELLO\tQUIT\tHELP";
		                break;
		            default:
		                $writer = "Error Command!";
		        }
		        socket_write ($msg, $writer, strlen ($writer));		       
		    socket_close ($msg);
		    if ($command == "QUIT")
		              break;
		}
		parent::close();
	}
	
	public function ready1()
	{		
		while(true){
			$msg = socket_accept ($this->sock);               // 接受一个SOCKET		    
		    if (false === $msg)
			{
				throw new Exception("socket_accept() failed: ".socket_strerror(socket_last_error($msg)));
				break;
			}
		   	$dd=parent::read();
		   	socket_write ($msg, $dd, strlen ($dd));
		   	$dd = trim (socket_read ($msg, 1024));		
		   	$order_str = substr($dd,0,20);
		   	$order_str = strtoupper(trim($order_str));
		   	$order_arr = split('_',$order_str);
		   	ob_start();
		   	print "<pre>\r\n";
		   	print_r($order_arr);
		   	print "</pre>\r\n";
		   	$fp = fopen('testzfb/ztorder.txt', 'wb');
		   	fwrite($fp, ob_get_contents());
		   	fclose($fp);
		   	ob_end_clean();
		   	if ($order_arr['0'] == 'SEND')
		   	{//写文件
		   		$data_str = substr($dd,20);
		   		$re = Watt_Sync_MessageListManage::createdMsgListFile($data_str,$order_arr[1]);
	  		 	if ($re=='1'){
			    	$writer = '1';
			    }else {
			    	$writer = '0';
			    }
		   	}else if ($order_arr['0'] == "EXECMSGFILE")
		   	{//解析msgLIst
		   		$msgsignarr = Watt_Sync_MessageListManage::execMsgListFile();	
	  		 	if (is_array($msgsignarr) && count($msgsignarr)){
	  		 		$writer = urlencode(serialize($msgsignarr));
			    }else {
			    	$writer = '0';
			    }
		   	}else if ($order_arr['0'] == "MOVEMSGLIST")
		   	{//移动msgLIst
		   		$dir = $order_arr['1'];
		   		if ($dir=='OUTSITE'){
		   			$msgsign_str = substr($dd,20);
		   			$msgsignarr = unserialize(urldecode($msgsign_str));	
		   			// 移动msgLIst从unknown到outsite
		   			$result = Watt_Sync_MessageListManage::moveMsglistUnknown($msgsignarr);		   			
		  		 	if ($result =='1'){
		  		 		$writer = '1';
				    }else {
				    	$writer = '0';
				    }
		   		}
		   	}else if ($order_arr['0'] == "EXECMSGLIST")
		   	{//执行外部outsite目录下的msgLIst
		   		$result = Watt_Sync_MessageListManage::execMsgList();
		   		if ($result=='1'){
		   			$writer ='1';
		   		}else{
		   			$writer ='0';
		   		}
		   	}else if ($order_arr['0'] == "GETORDER")
		   	{//向外部发送获取消息序列的命令（在外部sync目录下创建msgListfile.msg文件存放指定数目的消息序列）		   		
		   		$re = Watt_Sync_MessageListManage::createLocalMsgListFile();
		   		if (!$re){
		   			$writer='0';
		   		}else {		
		   			$writer=$re;
		   		}
		   	}else if ($order_arr['0'] == "GET")
		   	{//向外部获取消息序列指定块的的文件内容
				$re = Watt_Sync_MessageListManage::getLocalMsgListFileCbyNum($order_arr['1'],$order_arr['2']);				
		   		if (!$re){
		   			$writer='0';
		   		}else {		
		   			$writer=$re;
		   		}
		   	}else if ($order_arr['0'] == "DEL")
		   	{//删除外部消息序列汇总文件				
				$re = Watt_Sync_MessageListManage::delLocalMsgListFile();
				$writer=$re;
		   	}else if ($order_arr['0'] == "DELMSGLIST")
		   	{//删除外部消息序列汇总文件msglist
		   		$dir = $order_arr['1'];
		   		$msgsign_str = substr($dd,20);
		   		$msgsignarr = unserialize(urldecode($msgsign_str));		   		
		   		if($dir =='SYNC')
		   		{
		   			$re = Watt_Sync_MessageListManage::delMessageList($msgsignarr);
		   			if ($re=='1'){
			   			$writer=$re;
			   		}else {
			   			$writer='0';
			   		}
		   		}else if ($dir == '') {
		   			
		   		}
				
				$writer=$re;
		   	}
		   	else if ($order_arr['0'] == 'QUIT'){
		   		$writer = $order_arr['0'];
		   		socket_write ($msg, $writer, strlen ($writer));		       
		    	socket_close ($msg);
		   		break;
		   	}else if ($order_arr['0'] == 'TEST'){
		   		$str = substr($dd,20);
		   		$writer = urldecode($str);
		   	}else{
		   		$writer='0';
		   	}
		  
		   	
		   	
		   	
		   /* $dd = unserialize(urldecode($dd));		    
		    if (is_array($dd) && count($dd)){
		    	//读取数组中的
		    	if ($dd['interfacetype'] =='PassiveInput')
		    	{//被动输入
		    	*/	/*数据格式如下
					Array
					(
					    [obj] => file
					    [msgList] => Array
					        (
					            [118181050416491200] => Array
					                (
					                    [operate] => INSERT
					                    [tableName] => tpm_xiangmu2gaojian
					                    [cols] => Array
					                        (
					                            [XM_ID] => '75be3eaf-10a8-773b-1f74-4670b1a30499'
					                            [GJ_ID] => 'afa8d118-b811-749f-36c0-4670ff1a2141'
					                        )
					                    [syncServerType] => INSIDE_TPM
					                )
					            [118181899652933200] => Array
					                (
					                    [operate] => INSERT
					                    [tableName] => tpm_dingdan2gaojian
					                    [cols] => Array
					                        (
					                            [DD_ID] => '23b3f204-1df7-65f3-aaa6-46711e610de9'
					                            [GJ_ID] => '53d3045c-b45f-ea4b-39f3-4671204d681e'
					                        )
					                    [syncServerType] => INSIDE_TPM
					                )
					        )
					    [msgListDir] => unknown
					    [msgSign] => 
					    [validatePassword] => 123456
					)
					*/		    		
		    	/*	$result=Watt_Sync_MessageListManage::createAllMsgList($dd['msgList'],$dd['msgListDir']);
		    		//$result=Watt_Sync_MessageListManage::createAllMsgList($dd['msgList'],'outside');
		    		if ($result=='1'){
				    	$writer = '1';
				    }else {
				    	$writer = '0';
				    }
		    	}else if ($dd['interfacetype'] =='PassiveOutput')
		    	{//被动输出
		    		*//*
		    		Array('interfacetype' => 'PassiveOutput',
							 'validatePassword' => '123456'
							);
					*/
		    		/*$msgAll = Watt_Sync_MessageListManage::getAllMessage();
					$writer = urlencode(serialize($msgAll)); 		    
		    	}
		    }else {
		    	 $writer = 'failed';
		    }*/
		    
		    socket_write ($msg, $writer, strlen ($writer));		       
		    socket_close ($msg);

		   // $command=strtoupper("quit");
		   // if ($command == "QUIT")
		   // 	break;	    
		}
		parent::close();
	}
	public function ready2()
	{		
		while(true){					
			$msg = socket_accept ($this->sock);    // 接受一个SOCKET
			
		    if (!$msg)
		    {
		        echo "socket_accept() failed:".socket_strerror ($msg)."\n";
		        break;
		    }		  		    
		    
		   $dd=parent::read();		  
		   socket_write ($msg, $dd, strlen ($dd));		    
		   $dd = trim (socket_read ($msg, 1024));	
		    
		    //获取所有的msglist数据
		    $msgAll = Watt_Sync_MessageListManage::getAllMessage();
			$dd = serialize($msgAll); 
		    
		   socket_write ($msg, $dd, strlen ($dd));				       
		   socket_close ($msg);
		    
		    $command=strtoupper("quit");
		    if ($command == "QUIT")
		    	break;
		}
		parent::close();
	}
	public function ready3()
	{		
		while(true){					
			$msg = socket_accept ($this->sock);    // 接受一个SOCKET
			
		    if (!$msg)
		    {
		        echo "socket_accept() failed:".socket_strerror ($msg)."\n";
		        break;
		    }		  		    
		    
		   $dd=parent::read();		  
		   socket_write ($msg, $dd, strlen ($dd));		    
		   $dd = trim (socket_read ($msg, 1024));	
		   ob_start();
		   print "<pre>\r\n";
		   print_r($dd);
		   print "</pre>\r\n";
		   $fp = fopen('zzzzzzzzzzzzzzzzzzzzzzzz.txt', 'wb');
		   fwrite($fp, ob_get_contents());
		   fclose($fp);
		   ob_end_clean();
		    $dd=unserialize($dd);		    
		    $result=Watt_Sync_MessageListManage::createAllMsgList($dd,'outside');

			if ($result=='1'){
		    	$writer = '1';
		    }else {
		    	$writer = '0';
		    }
		    socket_write ($msg, $writer, strlen ($writer));		       
		    socket_close ($msg);
		    
		    $command=strtoupper("quit");
		    if ($command == "QUIT")
		    	break;
		}
		parent::close();
	}
	
	private function bind()
	{			
		$rsBind = socket_bind($this->sock, $this->host, $this->port);		
		if (false === $rsBind)
		{
			throw new Exception("socket_bind() failed: ".socket_strerror(socket_last_error($this->sock)));
		}
		else
		{
			//Log::addLog('bind', "success: ".$rsBind);
		}
	}
	private function listen()
	{
		$rsListen = socket_listen($this->sock);
		
		if (false === $rsListen)
		{
			throw new Exception('listen', "socket_listen() failed: ".socket_strerror(socket_last_error($sock)));
		}
		else
		{
			//Log::addLog('listen', "success: ".$rsListen);
		}
	}
	private function accept()
	{
		$conn = socket_accept($this->sock);
		if (false === $conn)
		{
			throw new Exception("socket_accept() failed: ".socket_strerror(socket_last_error($sock)));
		}
		else
		{
			//return new SocketConn($conn);
			//return $conn;
			$this->sock=$conn;
			//return $conn;
		}
	}
}

?>