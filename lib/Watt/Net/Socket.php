<?php
/**
 * @desc Socket.Class.php
 * @author Marty
 * @version Thu Jan 18 19:12:31 CST 2007 19:12:31 更新确认
 */
class Watt_Net_Socket
{
	private $host;
	private $port;
	private $sock;
	private $isConnected;
	
	public function __construct($host, $port)
	{
		$this->host = $host;
		$this->port = $port;
		$this->sock = '';
		$this->isConnected = '';
	}
	
	public function __destruct(){
		if( $this->isConnected ){
			$this->close();
		}
	}
	
	private function __get($name)
	{
		switch ($name) {
			case 'IsConnected':
				return isset($this->sock);
			case 'sock':
				return $this->sock;
			default:
				throw new Exception(get_class($this)."->$name can not be read");
		}
	}	
	
	public function create()
	{
		$this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);	
		if (false === $this->sock)
		{
			throw new Exception("socket_create() failed: ".socket_strerror(socket_last_error($this->sock)));
		}
		else
		{
			//Log::addLog('create', "success: ".$this->sock);
		}
		return $this->sock;
	}
	public function read($lenth = 2048)
	{		
		$buf = socket_read($this->sock, $lenth);
		//Log::addLog('read', htmlspecialchars($buf));
		return $buf;
	}
	
//	public function recv($len = 2048){
//		$buf = null;
//		socket_recv( $this->sock, $buf, $len, 0 );
//		return $buf;
//	}
	
	public function write($buf, $length = 0)
	{
		$length = intval($length) > 0 ? intval($length) : strlen($buf);
		$rev = socket_write($this->sock, $buf, $length);
		if( false === $rev ){
			$err = socket_strerror(socket_last_error($this->sock));
			Watt_Log::addLog('write socket error.['.$err.']');
			throw new Exception("socket_write() failed: ".$err);
		}
	}
	/**
	 * 关闭链接
	 *
	 */	
	public function close()
	{
		if( $this->isConnected ){
			socket_shutdown($this->sock, 2);
			socket_close($this->sock);
			$this->isConnected = false;
			//Log::addLog('close', $this->sock." socket_close() ok.");			
		}
	}
	public function __toString()
	{
		return $this->sock.' @'.$this->host.':'.$this->port;
	}
}
?>