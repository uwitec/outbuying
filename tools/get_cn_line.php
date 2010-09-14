<?php
set_time_limit(0);
ini_set('display_errors','on');
error_reporting(E_ALL ^ E_NOTICE);

class ToolsGetCNLine
{
	public function __construct()
	{
		$this->m_OutFile = NULL;
		$this->m_Path = "";
		$this->m_SkipList = array();
		
		mb_internal_encoding("UTF-8");
	}
	
	public function __destruct()
	{
		if($this->m_OutFile != NULL)
		{
			fclose($this->m_OutFile);
		}
	}
	
	public function Init($outfilename)
	{
		$Ret = 0;
		$Ret = $this->m_OutFile = fopen($outfilename, "wb");
		if($Ret === FALSE)
		{
			return -1;
		}
		
		//// 初始化排除列表，排除不需要扫描的目录、文件类型
		$this->m_SkipList[] = ".svn";
		$this->m_SkipList[] = ".gif";
		$this->m_SkipList[] = ".css";
		$this->m_SkipList[] = ".jpg";
		$this->m_SkipList[] = ".csv";
		$this->m_SkipList[] = ".txt";
		$this->m_SkipList[] = ".bmp";
		$this->m_SkipList[] = ".png";
		$this->m_SkipList[] = ".xml";
		$this->m_SkipList[] = ".bak";
		$this->m_SkipList[] = ".swf";
		$this->m_SkipList[] = ".sql";
		$this->m_SkipList[] = ".zip";
		$this->m_SkipList[] = ".rar";
		$this->m_SkipList[] = ".dat";
		$this->m_SkipList[] = ".db";
		$this->m_SkipList[] = "includes/adodb";
		$this->m_SkipList[] = ".ttf";
		$this->m_SkipList[] = ".tif";
		$this->m_SkipList[] = "includes/jpgraph";
		$this->m_SkipList[] = ".doc";
		$this->m_SkipList[] = ".xls";
		$this->m_SkipList[] = ".mso";
		$this->m_SkipList[] = ".cur";
		$this->m_SkipList[] = ".table";
		$this->m_SkipList[] = ".ds_store";
		$this->m_SkipList[] = "/lib/adodb/lang";
		$this->m_SkipList[] = ".parse";
		$this->m_SkipList[] = ".emz";
		$this->m_SkipList[] = ".jpeg";
		$this->m_SkipList[] = ".mdb";
		$this->m_SkipList[] = ".dll";
		$this->m_SkipList[] = "miniwcat/wsapi1";
		$this->m_SkipList[] = "miniwcat";
		$this->m_SkipList[] = "templates/demo";
		$this->m_SkipList[] = "smarty/demo";
		$this->m_SkipList[] = "miniwtm_dev/temp/";
		$this->m_SkipList[] = "scriptaculous/CHANGELOG";
		$this->m_SkipList[] = "MIT-LICENSE";
		$this->m_SkipList[] = "README";
		$this->m_SkipList[] = "复件";
		$this->m_SkipList[] = "simpletest/test";
		return 0;
	}
	
	//// 扫描单个文件
	public function Route($filename)
	{
		$fp = fopen($filename, "rb");
		if($fp === FALSE)
		{
			echo ">>> Error: Open File Failed Filename = $filename<br/>\n";
			return -1;
		}
		
		$Stat = 0;
		$LineCount = 0;
		fprintf($this->m_OutFile, ">>> %s\n", $filename);
		while(!feof($fp))
		{
			$Line = fgets($fp, 10240);
			$LineCount = $LineCount + 1;
			
			$Line = str_replace("　", "", trim($Line));
			$NewLine = $Line;
			
			$Pos = mb_strpos($Line, "*/"); //// 这个符号后边一般不会有其它字符串吧？
			if($Pos !== FALSE)
			{
				$Stat = 2;
			}
			
			$Pos = mb_strpos($Line, "/*");
			if($Pos !== FALSE)
			{
				$Stat = 1;
			}
			
			if($Stat != 0)
			{
				if($Stat == 2)
				{
					$Stat = 0;
				}
				continue;
			}
			
			$Pos = mb_strpos($Line, "//");
			if($Pos !== FALSE)
			{
				$NewLine = mb_substr($Line, 0, $Pos);
				if($NewLine == "")
				{
					continue;
				}
			}
			
			$Ret = preg_match("/.*([^\x{0001}-\x{0080}]+).*/", $NewLine);
			if($Ret <= 0)
			{
				continue;
			}
			
			$NewLine = "[$LineCount:] " . $NewLine . "\n";
			fprintf($this->m_OutFile, "%s", $NewLine);
			//printf("%s\n", $NewLine);
		}
		
		return 0;
	}
	
	//// 检查是否需要跳过当前文件
	public function IsSkip($filename)
	{
		$Result = false;
		$filename = strtolower($filename);
		foreach($this->m_SkipList as $Item)
		{
			$Pos = strpos($filename, $Item);
			if($Pos !== false)
			{
				$Result = true;
				break;
			}
		}
		
		return $Result;
	}
	
	
	//// 类入口函数，传入需要扫描的目录名称
	public function Run($path)
	{
		$handle = opendir($path);
		if($handle == false)
		{
			echo ">>> Error: Open dir failed! <br/>\n";
			return false;
		}
		
		while(true)
		{
			$item = readdir($handle);
			if($item == false)
				break;
			
			if($item == "." || $item == "..")
				continue;
			
			$fullfilename = $path . "/" . $item;
			
			if(is_dir($fullfilename))
			{
				$this->Run($fullfilename);
			}
			else
			{
				//echo ">>> $fullfilename <br/>\n";
				if($this->IsSkip($fullfilename) == true)
					continue;
				
				echo ">>> $fullfilename <br/>\n";
				$this->Route($fullfilename);
			}
		}
	}
};

$Tool = new ToolsGetCNLine();
$Tool->Init("/data/home/steven/public_html/result.txt");
$Tool->Run("/data/home/steven/public_html/miniwtm_dev");
//$Tool->Route("/data/home/steven/public_html/miniwtm_dev/app/miniwtm/FileImport.php");



?>