<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");
$cod_guia_despacho = $_REQUEST['cod_guia_despacho'];
$cod_empresa = $_REQUEST['cod_empresa'];
$array_cod = explode('-', $cod_guia_despacho);
$temp = new Template_appl('dlg_despliega_gd.htm');

$sql = "SELECT 'N' CHK_GUIA_DESPACHO
			  ,COD_GUIA_DESPACHO
			  ,NRO_GUIA_DESPACHO
			  ,CONVERT(VARCHAR, FECHA_GUIA_DESPACHO, 103) FECHA_GUIA_DESPACHO
			  ,CONVERT(VARCHAR, RUT) +'-'+ DIG_VERIF RUT
			  ,NOM_EMPRESA
			  ,REFERENCIA
		FROM GUIA_DESPACHO
		WHERE FECHA_GUIA_DESPACHO >= {ts '2016-08-01 01:00:00.000'}		--rango
		AND COD_EMPRESA = $cod_empresa"; 

$dw = new datawindow($sql, 'GUIA_DESPACHO');
$dw->add_control(new edit_check_box('CHK_GUIA_DESPACHO', 'S', 'N'));
$dw->add_control(new static_text('NRO_GUIA_DESPACHO'));
$dw->add_control(new edit_text_hidden('COD_GUIA_DESPACHO'));
$dw->retrieve();

for($i=0 ; $i < $dw->row_count(); $i++){
	$set_value = false;
	$cod_guia_despacho_bd = $dw->get_item($i, 'COD_GUIA_DESPACHO');
	
	for($j=0 ; $j < count($array_cod) ; $j++){
		$cod_guia_despacho = $array_cod[$j];
		
		if($cod_guia_despacho == $cod_guia_despacho_bd)
			$set_value = true;
	}
	
	if($set_value)
		$dw->set_item($i, 'CHK_GUIA_DESPACHO', 'S');
}

$dw->habilitar($temp, true);
print $temp->toString();	
?>