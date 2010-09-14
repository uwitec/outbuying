<?
/**
 * 短消息发送工具
 *
 */
class Pft_Util_Msg_Mail{
	
	/**
	 * 发送邮件
	 * 
	 * 参数结构如下
	 * 
	 * array(
	 *   'sender_mail' => 'xxx@abc.com',
	 *   'sender_name' => 'xxx',
	 *   'recievers' => array(
	 *                    [0] => array(
	 *                             'reciever_mail' => 'aaa@abc.com',
	 *                             'reciever_name' => 'aaa'
	 *                           ),
	 *                    [1] => array(
	 *                             'reciever_mail' => 'bbb@abc.com',
	 *                             'reciever_name' => 'bbb'
	 *                           )
	 *                  )
	 *   'title' => 'title',
	 *   'body'  => 'body'
	 * )
	 * 
	 * @param string $to
	 * @param utf8|string $subject
	 * @param utf8|string $body
	 * @param string $from
	 * @param array $other
	 * @return 0
	 */
	public static function sendMail($to, $subject, $body, $from, $other=null){
		$mail = self::getPhpMailerWithDefaultConfig();
		$mail->IsHTML(true);
		$mail->CharSet  = 'GB2312';
		$mail->AddAddress( $to, $to );
		$mail->FromName = $from;
		$mail->Subject = iconv( "UTF-8", "GB2312", $subject );     // 标题
		$mail->Body = @iconv("UTF-8", "GB2312", $body );
		$mail->AltBody = $other;	// 附加内容
		return $mail->Send();
		//return 0;	
	}
	
	/**
	 * 获得一个 PHPMailer 对象，已经作了基本配置，
	 * SMTP
	 *
	 * @return PHPMailer
	 */
	public static function getPhpMailerWithDefaultConfig(){
		include_once(Pft_Config::getLibPath().'Third/phpmailer/class.phpmailer.php');
		
		$mail = new PHPMailer();
		$mail->IsSMTP();                   							// 设置使用 SMTP	 与发件人相同
		$mail->Host 	= Pft_Config::getCfg("MAIL_SMTP_HOST");	// 指定的 SMTP 服务器地址
		$mail->Username = Pft_Config::getCfg("MAIL_SMTP_USERNAME");// SMTP 发邮件人的用户名
		$mail->Password = Pft_Config::getCfg("MAIL_SMTP_PASSWORD");// SMTP 密码
		$mail->SMTPAuth = Pft_Config::getCfg("MAIL_SMTP_AUTH");	// 设置为安全验证方式
		$mail->From     = Pft_Config::getCfg("MAIL_SMTP_ADDR");	// 发件人地址  //"system.watt@163.com"	;
		return $mail;
	}
	/**
	 * 邮件模板函数
	 *
	 * @param unknown_type $model      模板名称
	 * @param unknown_type $varname    替换数据
	 */
	public static function formatMailBody($model,$searchname,$replcename)
	{
		$_searchname=array();
		$path=Pft_Config::getConfigPath()."public/emailtemplates/".$model;
		$str=file_get_contents($path);
		if(count($searchname)>0)
		{
			foreach($searchname as $val)
			{
				$_searchname[]="<?$".$val."?>";
			}
		}
		//return $str;
		$search=str_replace($_searchname,$replcename,$str);
		return $search;
		
	}
}