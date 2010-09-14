<?php
$Host = "localhost";
$User = "root";
$PWD  = "123456";

$Conn = mysql_connect($Host,$User,$PWD);

if($Conn)
{
	$sql = "show slave status";
	$query = mysql_query($sql);
	$res = mysql_fetch_array($query);
	
	if($res['Slave_IO_Running'] == 'Yes' && $res['Slave_SQL_Running'] == 'Yes')
	{
		echo "Slave is running!\n";
	}
	else 
	{
		//send Slave down message
		echo "Slave is Down!\n";
		file_get_contents("http://testtpm.transn.net/smscenter/send.php?tmobile=13718323866&msgid=0&msg=10-0-0-196&source=0");
	}
}
else 
{
	//send Mysql Server Down message
	echo "MySQL is down!\n";
	file_get_contents("http://testtpm.transn.net/smscenter/send.php?tmobile=13718323866&msgid=0&msg=10-0-0-196&source=0");
}
?>