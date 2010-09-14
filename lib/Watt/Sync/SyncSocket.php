<?
/**
 * socket控制类
 * 
 */
class Watt_Sync_SyncSocket{
	
	public static function syncInterface($type,$date){
		/*
		$aa = new Watt_Net_SocketClient('59.151.23.90', 5023);
		$aa->write($date);
		return $aa->read();
		*/
		$date_new = '';		
		
		
		if ($type=='InitiativeInput'){
			
		}else if ($type=='InitiativeOutput'){
			
		}else if ($type=='PassiveInput'){//被动输入
			$aa->write($date);
		}else if ($type=='PassiveOutput'){
			$aa->write('aa');
		}else if ($type == "send")
		{//传送数据（将内部的消息序列传送的外部的unknown目录下，存储在msgListfile.msg文件中
			$date_new = $date;
		}else if ($type == "execmsgfile")
		{//执行文件（从内部传送到外部后，将组合成的文件生成消息序列，在unknown目录下）
			$order_str = strtoupper(trim("execmsgfile"."_0_0"));		
			$count = strlen($order_str);
			$order_str = str_split($order_str,1);
			$temp_arr = array_fill($count, (20-$count), ' ');
			$order_str_arr = array_merge($order_str, $temp_arr);
			$order_str = '';
			foreach ($order_str_arr as $k =>$v){			
				$order_str .= $v;
			}						
			$date_new = $order_str;			
			
		}else if ($type == "movemsglistoutsite")
		{//移动外部消息序列到outsite
			$order_str = strtoupper(trim("movemsglist"."_outsite"));		
			$count = strlen($order_str);
			$order_str = str_split($order_str,1);
			$temp_arr = array_fill($count, (20-$count), ' ');
			$order_str_arr = array_merge($order_str, $temp_arr);
			$order_str = '';
			foreach ($order_str_arr as $k =>$v){			
				$order_str .= $v;
			}
			if(is_array($date) && count($date)){
				$date = urlencode(serialize($date));
			}
			$date_new = $order_str.$date;
			
		}else if ($type == "execmsglist")
		{//执行外部outsite目录下的消息序列
			$order_str = strtoupper(trim("execmsglist"."_0_0"));		
			$count = strlen($order_str);
			$order_str = str_split($order_str,1);
			$temp_arr = array_fill($count, (20-$count), ' ');
			$order_str_arr = array_merge($order_str, $temp_arr);
			$order_str = '';
			foreach ($order_str_arr as $k =>$v){			
				$order_str .= $v;
			}						
			$date_new = $order_str;			
			
		}else if ($type == "getorder")
		{//向外部发送获取消息序列的命令（在外部sync目录下创建msgListfile.msg文件存放指定数目的消息序列）
			$order_str = strtoupper(trim("getorder"."_0_0"));		
			$count = strlen($order_str);
			$order_str = str_split($order_str,1);
			$temp_arr = array_fill($count, (20-$count), ' ');
			$order_str_arr = array_merge($order_str, $temp_arr);
			$order_str = '';
			foreach ($order_str_arr as $k =>$v){			
				$order_str .= $v;
			}						
			$date_new = $order_str;			
			
		}else if ($type == "get")
		{//
			$order_str = $date;					
			$count = strlen($order_str);
			$order_str = str_split($order_str,1);
			$temp_arr = array_fill($count, (20-$count), ' ');
			$order_str_arr = array_merge($order_str, $temp_arr);
			$order_str = '';
			foreach ($order_str_arr as $k =>$v){			
				$order_str .= $v;
			}						
			$date_new = $order_str;			
			
		}else if ($type == "del")
		{//
			$order_str = strtoupper(trim("del"."_0_0"));					
			$count = strlen($order_str);
			$order_str = str_split($order_str,1);
			$temp_arr = array_fill($count, (20-$count), ' ');
			$order_str_arr = array_merge($order_str, $temp_arr);
			$order_str = '';
			foreach ($order_str_arr as $k =>$v){			
				$order_str .= $v;
			}						
			$date_new = $order_str;
			
		}else if ($type == "delmsglistsync")
		{//删除sync
			$order_str = strtoupper(trim("delmsglist_sync"));					
			$count = strlen($order_str);
			$order_str = str_split($order_str,1);
			$temp_arr = array_fill($count, (20-$count), ' ');
			$order_str_arr = array_merge($order_str, $temp_arr);
			$order_str = '';
			foreach ($order_str_arr as $k =>$v){			
				$order_str .= $v;
			}	
								
			if(is_array($date) && count($date)){
				$date = urlencode(serialize($date));
			}
			$date_new = $order_str.$date;			
		}else if ($type =='quit')
		{//关闭连接
			$order_str = strtoupper(trim($type."_0_0"));
			$count = strlen($order_str);
			$order_str = str_split($order_str,1);
			$temp_arr = array_fill($count, (20-$count), ' ');
			$order_str_arr = array_merge($order_str, $temp_arr);
			$order_str = '';
			foreach ($order_str_arr as $k =>$v){			
				$order_str .= $v;
			}						
			$date_new = $order_str;
		}
				
		//echo "<br>".$date_new."<br>";
		$aa = new Watt_Net_SocketClient('59.151.23.90', 5033);
		$aa->write($date_new);
		return $aa->read();
	}
}
?>