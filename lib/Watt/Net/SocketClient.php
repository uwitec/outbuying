<?php
/**
 * @desc Socket.Class.php
 * @author Marty
 * @version Thu Jan 18 19:12:31 CST 2007 19:12:31 更新确认
 */
class Watt_Net_SocketClient extends Watt_Net_Socket
{	
	private $host;
	private $port;
	private $sock;
	private $isConnected;
	
	public function __construct($host = 'localhost', $port = 10000)
	{
		$this->host = $host;
		$this->port = $port;
		$this->sock = $this->create();
		//$this->isConnected='';
	}
	
	public function write($buf, $length = 0)
	{	
		//$this->isConnected = false;
		if (!isset($this->isConnected))
		{		
			$this->isConnected = self::connect();
		}
		parent::write($buf, $length);
	}
	public function read($len = 2048){
		return parent::read($len);
	}
//	public function recv($len = 2048){
//		return parent::recv($len = 2048);
//	}
	public function connect()
	{	
		if (false === socket_connect($this->sock, $this->host, $this->port))
		{
			throw new Exception("socket_connect(".$this->host.":".$this->port.") failed: ".socket_strerror(socket_last_error($this->sock)));
		}
		//stream_set_timeout ($this->sock,10);
		Watt_Debug::getDefaultDebug()->addInfo("socket_connect(".$this->host.":".$this->port.") success");
		$this->isConnected = true;
		
		return $this->isConnected;
	}
	
}

?>