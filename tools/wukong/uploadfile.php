<?
/**
 * 这是一个上传文件的页面
 */
 $_PATH=dirname(__FILE__);
 
 $js_path=str_replace('\\','||', $_PATH);
?>
<?
$file_size=ini_get('upload_max_filesize');
$file_size=str_replace("M","",$file_size);

$post_size = str_replace("M","",ini_get('post_max_size'));
if( $post_size <= $file_size ){
	$file_size = $post_size - 0.1;
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!--<script type="text/javascript" src="source/mootools-trunk.js"></script>-->
	<script type="text/javascript" src="<?=$_PATH?>/../../js/FancyUpload/mootools.js"></script>
	<script type="text/javascript" src="<?=$_PATH?>/../../js/FancyUpload/Fx.ProgressBar.js"></script>
	<script type="text/javascript" src="<?=$_PATH?>/../../js/FancyUpload/Swiff.Uploader.js"></script>
	<script type="text/javascript" src="<?=$_PATH?>/../../js/FancyUpload/FancyUpload3.Attach.js"></script>
	<script type="text/javascript">
	
var filzesize='<?=$file_size?>';
window.addEvent('domready', function() {
 
	/**
	 * Uploader instance
	 */
	 var _p="<?=$js_path?>";
	 var zz=/\|\|/g
	 _p=_p.replace(zz,'\\');
	 var _path=_p+"/../../js/FancyUpload/Swiff.Uploader.swf";
	 alert(_path);
	var up = new FancyUpload3.Attach('demo-list', '#demo-attach, #demo-attach-2', {
		path: _path,
		//url: 'showcase/script.php',
		url: 'uploader.php',
		//url: $('form-demo').action,
		fileSizeMax: filzesize * 1024 *1024,
 
		verbose: true,
 
		onSelectFail: function(files) {
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
 
		onFileSuccess: function(file) {
			new Element('input', {type: 'checkbox',name:'wenjian[]','checked': true}).inject(file.ui.element, 'top');
			file.ui.element.highlight('#e6efc2');
		},
 
		onFileError: function(file) {
			file.ui.cancel.set('html', 'Retry').removeEvents().addEvent('click', function() {
				file.requeue();
				return false;
			});
 
			new Element('span', {
				html: file.errorMessage,
				'class': 'file-error'
			}).inject(file.ui.cancel, 'after');
		},
 
		onFileRequeue: function(file) {
			file.ui.element.getElement('.file-error').destroy();
 
			file.ui.cancel.set('html', 'Cancel').removeEvents().addEvent('click', function() {
				file.remove();
				return false;
			});
 
			this.start();
		}
 
	});
 
});
</script>
<div>
<a href="#" id="demo-attach"><input type="button" value='aaa' />[上传文件]</a>
				<i>(文件大小:<?=$file_size?>M)</i>
				<ul id="demo-list"></ul>
				<a href="#" id="demo-attach-2" style="display: none;">[上传文件]</a> </td>
</div>

