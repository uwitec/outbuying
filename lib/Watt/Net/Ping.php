<?php
class Watt_Net_Ping
{
	/**
	 * @var Watt_Net_Ping
	 */
	private static $_pingObj;
	public static function ping( $dst_addr,$timeout=5,$percision=3 ){
		if( !self::$_pingObj ){
			self::$_pingObj = new Watt_Net_Ping();
		}
		return self::$_pingObj->PingAddr( $dst_addr,$timeout,$percision );
	}
	
	private $icmp_socket;
	private $request;
	private $request_len;
	private $reply;
	private $errstr;
	private $time;
	private $timer_start_time;

	function __construct()
	{
		$this->icmp_socket = socket_create(AF_INET, SOCK_RAW, 1);
		socket_set_block($this->icmp_socket);
	}

	function ip_checksum($data)
	{
		for($i=0;$i<strlen($data);$i += 2)
		{
			if($data[$i+1]) $bits = unpack('n*',$data[$i].$data[$i+1]);
			else $bits = unpack('C*',$data[$i]);
			$sum += $bits[1];
		}

		while ($sum>>16) $sum = ($sum & 0xffff) + ($sum >> 16);
		$checksum = pack('n1',~$sum);
		return $checksum;
	}

	function start_time()
	{
		$this->timer_start_time = microtime();
	}

	function get_time($acc=2)
	{
		// format start time
		$start_time = explode (" ", $this->timer_start_time);
		$start_time = $start_time[1] + $start_time[0];
		// get and format end time
		$end_time = explode (" ", microtime());
		$end_time = $end_time[1] + $end_time[0];
		return number_format ($end_time - $start_time, $acc);
	}

	function Build_Packet()
	{
		$data = "abcdefghijklmnopqrstuvwabcdefghi"; // the actual test data
		$type = "\x08"; // 8 echo message; 0 echo reply message
		$code = "\x00"; // always 0 for this program
		$chksm = "\x00\x00"; // generate checksum for icmp request
		$id = "\x00\x00"; // we will have to work with this later
		$sqn = "\x00\x00"; // we will have to work with this later

		// now we need to change the checksum to the real checksum
		$chksm = $this->ip_checksum($type.$code.$chksm.$id.$sqn.$data);

		// now lets build the actual icmp packet
		$this->request = $type.$code.$chksm.$id.$sqn.$data;
		$this->request_len = strlen($this->request);
	}

	function PingAddr($dst_addr,$timeout=5,$percision=3)
	{
		// lets catch dumb people
		if ((int)$timeout <= 0) $timeout=5;
		if ((int)$percision <= 0) $percision=3;

		// set the timeout
		socket_set_option($this->icmp_socket,
		SOL_SOCKET,  // socket level
		SO_RCVTIMEO, // timeout option
		array(
		"sec"=>$timeout, // Timeout in seconds
		"usec"=>0  // I assume timeout in microseconds
		)
		);

		if ($dst_addr)
		{
			if (@socket_connect($this->icmp_socket, $dst_addr, NULL))
			{

			} else {
				$this->errstr = "Cannot connect to $dst_addr";
				return FALSE;
			}
			$this->Build_Packet();
			$this->start_time();
			socket_write($this->icmp_socket, $this->request, $this->request_len);
			//if (@socket_recv($this->icmp_socket, &$this->reply, 256, 0))
			if (@socket_recv($this->icmp_socket, $this->reply, 256, 0))
			{
				$this->time = $this->get_time($percision);
				return $this->time;
			} else {
				$this->errstr = "Timed out";
				return FALSE;
			}
		} else {
			$this->errstr = "Destination address not specified";
			return FALSE;
		}
	}
}
/*set pid
$user = "daemon";
$script_name = "uid"; //the name of this script

/////////////////////////////////////////////
//try creating a socket as a user other than root
echo "\n__________________________________________\n";
echo "Trying to start a socket as user $user\n";
$uid_name = posix_getpwnam($user);
$uid_name = $uid_name['uid'];

if(posix_seteuid($uid_name))
{
        echo "SUCCESS: You are now $user!\n";
        if($socket = @socket_create(AF_INET, SOCK_RAW, 1))
        {
                echo "SUCCESS: You are NOT root and created a socket! This should not happen!\n";
        } else {
                echo "ERROR: socket_create() failed because you're not root!\n";
        }
        $show_process = shell_exec("ps aux | grep -v grep | grep $script_name");
        echo "Current process stats::-->\t $show_process";
} else {
        exit("ERROR: seteuid($uid_name) failed!\n");
}

/////////////////////////////////////////////
//no try creating a socket as root
echo "\n__________________________________________\n";
echo "Trying to start a socket as user 'root'\n";
if(posix_seteuid(0))
{
        echo "SUCCESS: You are now root!\n";
        $show_process = shell_exec("ps aux | grep -v grep | grep $script_name");
        echo "Current process stats::-->\t $show_process";
        if($socket = @socket_create(AF_INET, SOCK_RAW, 1))
        {
                echo "SUCCESS: You created a socket as root and now should seteuid() to another user\n";
                /////////////////////////////////////////
                //now modify the socket as another user
                echo "\n__________________________________________\n";
                echo "Switching to user $user\n";
                if(posix_seteuid($uid_name))
                {
                        echo "SUCCESS: You are now $user!\n";
                        if(socket_bind($socket, 0, 8000))
                        {
                                echo "SUCCESS: socket_bind() worked as $user!\n";
                        } else {
                                echo "ERROR: Must be root to user socket_bind()\n";
                        }
                        $show_process = shell_exec("ps aux | grep -v grep | grep $script_name");
                        echo "Current process stats::-->\t $show_process";
                        socket_close($socket); //hard to error check but it does close as this user
                        echo "SUCCESS: You closed the socket as user $user!\n";
                } else {
                        echo "ERROR: seteuid($uid_name) failed while socket was open!\n";
                }

        } else {
                echo "ERROR: Socket failed for some reason!\n";
        }
} else {
        exit("ERROR: Changing to root failed!\n");
}
*/
?>