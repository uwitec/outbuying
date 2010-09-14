<?

class Watt_Http_Client{
	/**
	 * 一个进程只使用1个Client
	 *
	 * @var resource
	 */
	private static $ch=null;
	
	/**
	 * 使用curlPost
	 * 注意使用 urlencode 对参数进行处理
	 * 
	 * @param url $url
	 * @param string|array $params	&p1=d1&p2=d2
	 * @return string
	 */
	public static function curlPost( $url, $params ){
//		if( is_array( $params ) ){
//			$paramString = '';
//			foreach ($params as $key => $val) {
//				$paramString .= "&$key=".urlencode($val);
//			}
//			$paramString = trim( $paramString, '&' );
//			$params = $paramString;
//		}
		/*if( !self::$ch ){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTP_VERSION, 1.0);
			curl_setopt($ch, CURLOPT_POST, true);
			self::$ch = $ch;
		}else{
			$ch = self::$ch;
		}
		*/
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, 1.0);
		curl_setopt($ch, CURLOPT_POST, true);
		
			
		//if( is_array( $params ) ){
		if( self::_isArrayParam( $params ) ){	
			curl_setopt($ch, CURLOPT_POSTFIELDS, self::data_encode($params));
		}else{
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}
		ob_start();
		curl_exec($ch);
		$data=ob_get_contents();
		ob_end_clean();
		curl_close ($ch);
		return $data;		
	}

	/**
	 * @param mix $param
	 * @return boolean
	 */
	private static function _isArrayParam($param){
		if( is_array($param) ){
			foreach ($param as $key => $val) {
				if( !is_array( $val ) ){
					if( substr( $val, 0, 1 ) == '@' ){
						//如果是file域，则不认为是数组
						return false;
					}					
				}
			}
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * php 4 才有这问题
	 * 将post数据encode
	 */
	public static function data_encode($data, $keyprefix = "", $keypostfix = "") {
		assert( is_array($data) );
		$vars=null;
		foreach($data as $key=>$value) {
			if(is_array($value)){
				$vars .= self::data_encode($value, $keyprefix.$key.$keypostfix.("["), ("]"));
				//$vars .= self::data_encode($value, $keyprefix.$key.$keypostfix.urlencode("["), urlencode("]"));
			}else{
				$vars .= $keyprefix.$key.$keypostfix."=".urlencode($value)."&";
			}
		}
		return $vars;
	}

	public static function post_data($PostUrl,$data=array())  
     {  
         if(count($data)>0)  

         //检测输入  
         $url_file = trim($PostUrl);  
         if (empty($url_file)) { return '1'; }  
         $url_arr = parse_url($url_file);  
         if (!is_array($url_arr) || empty($url_arr)){ return '2'; }  
         //获取请求数据  
         $host = $url_arr['host'];  
         if(!empty($url_arr['query'])&&$url_arr['query']!=null)  
             $path = $url_arr['path'] ."?". $url_arr['query'];  
         else   
             $path = $url_arr['path'];  
         $port = isset($url_arr['port']) ? $url_arr['port'] : "80";  
         //连接服务器  
         $fp = fsockopen($host, $port, $err_no, $err_str, 30);  
         if (!$fp){ return $err_str; }  
         $ret="";  
         $out = "";  
         while (list ($k, $v) = each ($data)) {  
             if(strlen($out) != 0) $out .= "&";  
             $out .= rawurlencode($k). "=" .rawurlencode($v);  
         }  
         $out = trim ($out);  
         $request     =   "POST ".$path."  HTTP/1.0\r\n";  
         $request   .=   "Host: ".$host."\r\n";  
         $request   .=   "User-Agent: Incutio HttpClient v0.9\r\n";  
         $request   .=    "Accept: text/xml,application/xml,application/xhtml+xml,text/html,text/plain,image/png,image/jpeg,image/gif,*/*\r\n";  
         $request   .=   "Accept-encoding: gzip\r\n";  
         $request   .=   "Accept-Language:   zh-cn\r\n";  
         $request   .=   "Content-Type:   application/x-www-form-urlencoded\r\n";  
         $request   .=   "Content-length:   ".strlen($out)."\r\n";  
         $request   .=   "Connection:   Keep-Alive\r\n\r\n";  
         $request.=$out;  
         fputs($fp,$request);  
         unset($request);  
         $inHeaders = true;  
         $atStart = true;  
         while(!feof($fp)){  
             $line = fgets($fp, 4096);  
            if ($atStart) {  
                 // 是否第一次返回数据  
                 $atStart = false;  
                 if (!preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/', $line, $m)) {  
                     return '4';  
                 }  
                 continue;  
             }  
             if ($inHeaders) {  
                 if (trim($line) == '') {  
                     $inHeaders = false;  
                     continue;  
                 }  
                 if (!preg_match('/([^:]+):\\s*(.*)/', $line, $m)) {  
                     continue;  
                 }  
                 continue;  
             }  
             $ret.= $line;  
         }  
         fclose ($fp);  
         return $ret;  
     }
}