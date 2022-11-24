<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
	
$fname = session::get('K_ROOT_URL').'appl/cx_archivo_excel/Cuadro_de_Pago.xls';
header("Content-Type: application/x-msexcel; name=\Cuadro de Pago.xls\"");
header("Content-Disposition: inline; filename=\"Cuadro de Pago.xls\"");
$fh=fopen($fname, "rb");
fpassthru($fh);
?>