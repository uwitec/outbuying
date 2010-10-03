<?
class Pft_View_Helper_ProcessBar{
	public static function buildBar( $process, $title='' ){
		$html = "<div style=\"white-space:nowrap;font-size:9px;line-height:9px;width:50px;text-align:left;\" title=\"{$title}\">
		<div style=\"float:left;width:20px;border:1px solid #000;background-color:#FFF;height:5px;font-size:5px;\">
			<div style=\"width:{$process}%;background-color:#006;\"></div>
		</div><div style='float:left;margin-left:1px;'>{$process}%</div></div>";
		//生成进度条形图
//		$progressStr = '<OBJECT ID="ProgressBar1['.$gz_id.']" WIDTH=50  HEIGHT=10 
//						 CLASSID="CLSID:35053A22-8589-11D1-B16A-00C0F0283628"> 
//							<PARAM NAME="_ExtentX" VALUE="10345"> 
//							<PARAM NAME="_ExtentY" VALUE="423"> 
//							<PARAM NAME="_Version" VALUE="393216"> 
//							<PARAM NAME="Appearance" VALUE="0"> 
//							<PARAM NAME="Max" VALUE="100"> 
//							<!--PARAM NAME="Scrolling" VALUE="1"--> 
//						</OBJECT>'.$process.'%';
//		$progressStr .= '<script language="Javascript"> 
//								document.getElementById("ProgressBar1['.$gz_id.']").value ='.$process.';
//						 </script> ';
		return $html;
	}
}