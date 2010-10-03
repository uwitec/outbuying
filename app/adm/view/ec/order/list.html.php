<?php
include dirname(__FILE__).'/../../inc/header.inc.php';
?>
<div class="list">
<?php 
$theGrid = new Pft_Util_Grid($grid);
$theGrid->addCol('订单ID','o_id');
$theGrid->showMe();
?>
</div>
<?php
include dirname(__FILE__).'/../../inc/footer.inc.php';