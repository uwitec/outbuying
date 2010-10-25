<?
/**
 * 这是一个上传文件的页面
 */
?>
<?
$file_size=ini_get('upload_max_filesize');
$file_size=str_replace("M","",$file_size);

$post_size = str_replace("M","",ini_get('post_max_size'));
if( $post_size <= $file_size ){
	$file_size = $post_size - 0.1;
}
?>
<style>

</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!--<script type="text/javascript" src="source/mootools-trunk.js"></script>-->
	<script type="text/javascript" src="./js/FancyUpload/mootools.js"></script>
	<script type="text/javascript" src="./js/FancyUpload/Fx.ProgressBar.js"></script>
	<script type="text/javascript" src="./js/FancyUpload/Swiff.Uploader.js"></script>
	<script type="text/javascript" src="./js/FancyUpload/FancyUpload3.Attach.js"></script>
	<script type="text/javascript">
	try
	{
	var isMore="<?=$isMore?>";
var filzesize='<?=$file_size?>';
window.addEvent('domready', function() {
 
	/**
	 * Uploader instance
	 */
	var up = new FancyUpload3.Attach('demo-list', '#uploadView', {
		path: './js/FancyUpload/Swiff.Uploader.swf',
		//url: 'showcase/script.php',
		url: '?do=tools_uploader_uploadfile',
		//url: $('form-demo').action,
		fileSizeMax: filzesize * 1024 *1024,
		typeFilter: {   
        'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'  
		 },   
		verbose: true,
		onSelectFail:function(files) {

			files.each(function(file) {
				new Element('li', {
					'class': 'file-invalid',
					events: {
						click: function() {
							this.destroy();
						}
					}
				}).adopt(
					new Element('span', {html: file.validationErrorMessage || file.validationError})
				).inject(this.list, 'bottom');
			}, this);	
		},
		
		
 
		onFileError:function(file) {
			/*file.ui.cancel.set('html', 'Retry').removeEvents().addEvent('click', function() {
				file.requeue();
				return false;
			});
 
			new Element('span', {
				html: file.errorMessage,
				'class': 'file-error'
			}).inject(file.ui.cancel, 'after');
			*/
			return false;
			
		},
 
		onFileRequeue:function(file) {
			
			/*file.ui.element.getElement('.file-error').destroy();
 
			file.ui.cancel.set('html', 'Cancel').removeEvents().addEvent('click', function() {
				file.remove();
				return false;
			});
			
 
			this.start();
			*/
			return false;
		}
 
	});
 
});
	}
	catch(e){}
/*function viewUpload()
{
	
	if(isMore=="N" || isMore=="n")
	{
		
		var dCount=document.getElementsByName("dName[]");
		if(dCount)
		{
			if(dCount.length==1)
			{
				document.getElementById("uploadView").style.height ='1px';
				document.getElementById("uploadView").style.width ='1px';
				document.getElementById("isMoreTitle").style.display='';
				document.getElementById("isMoreTitle").innerHTML="文档只能上传一个";
				
				return false;
			}
		}
	}
	//document.getElementById("uploadView").click();
	
}
*/
</script>
<div>

<div id="uploadView" ><a href="#" id="demo-attach">[上传]</a><span>(上传文档最大:<?=$file_size?>M)</span></div>
<div id="demo-list"></div>
			
				
</div>


