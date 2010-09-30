<?
class PsUserController extends Pft_Controller_Action{
	function __construct(){
		$this->_isPublic = true;
	}
	
	function indexAction(){

	}
	
	function loginAction(){
		$op = $this->getInputParameter('op');
		if( $op ){
			$uname = r('username');
			$pwd = r('password');
			Ofh_Ps_User::checkUserLogin( $uname, $pwd );
		}
	}
	/*
	*options: 用户注册
	*param:  
	*autor:df      
	*date:Thu Sep 23 20:23:08 CST 2010
	*/
	function registerAction()
	{
		$users=new Yd_Users();
		$users->autoGetRequestVar('u_id,u_name,u_nickname,u_pwd,u_sex,u_phone,u_mobile,u_address,u_email,u_last_login,u_logins,u_main_role,created_at,updated_at,is_del');
		if($this->getInputParameter("op"))
		{
			if($users->save())
			{
				  
			}
		}
	}
	function register1Action()
	{
		
	}
	/**
	**
	**生成验证码
	**/
	function yanzhengmaAction()
	{
		
		//session_register('SafeCode');
		$type = 'png';
		$width= 50;
		$height= 20;
		//header("Content-type: image/".$type);
		@ob_end_clean();
		srand((double)microtime()*1000000);
		$randval = randStr(4,"");
		if($type!='png' && function_exists('imagecreatetruecolor')){ 
			 $im = @imagecreatetruecolor($width,$height);
		}else{ 
			 $im = @imagecreate($width,$height);
		}
			 $r = Array(225,211,255,223);
			 $g = Array(225,236,237,215);
			 $b = Array(225,236,166,125);

			 $key = rand(0,3);
		  
			 $backColor = ImageColorAllocate($im,$r[$key],$g[$key],$b[$key]);//背景色（随机）
			 $borderColor = ImageColorAllocate($im, 0, 0, 0);//边框色
			 $pointColor = ImageColorAllocate($im, 255, 170, 255);//点颜色

			 @imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);//背景位置
			 @imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor); //边框位置
			 $stringColor = ImageColorAllocate($im, 255,51,153);

			 for($i=0;$i<=100;$i++){ 
				   $pointX = rand(2,$width-2);
				   $pointY = rand(2,$height-2);
				   @imagesetpixel($im, $pointX, $pointY, $pointColor);
			 }

			 @imagestring($im, 5, 5, 3, $randval, $stringColor);
			 $ImageFun='Image'.$type;
			 $ImageFun($im);
			 @ImageDestroy($im);
			 $_SESSION['SafeCode'] = $randval;
			//产生随机字符串
			@flush();
			@ob_flush();
			exit();
	}

}
function randStr($len=6,$format='ALL') { 
					   switch($format) { 
							 case 'ALL':
							 $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'; break;
							 case 'CHAR':
							 $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; break;
							 case 'NUMBER':
							 $chars='0123456789'; break;
							 default :
							 $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'; 
							 break;
					   }
				 $string="";
				 while(strlen($string)<$len)
				 $string.=substr($chars,(mt_rand()%strlen($chars)),1);
				 return $string;
			}
?>