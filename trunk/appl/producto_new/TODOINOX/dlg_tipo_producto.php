<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");

$nom_header = $_REQUEST['nom_header'];
$valor_filtro = $_REQUEST['valor_filtro'];

$temp = new Template_appl('dlg_tipo_producto.htm');	

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "select COD_TIPO_PRODUCTO 
				,NOM_TIPO_PRODUCTO 
		from TIPO_PRODUCTO 
		order by	ORDEN";
$result = $db->build_results($sql);
if ($valor_filtro=='') {	//todos
	for ($i=0 ; $i < count($result); $i++) {
		$valor_filtro = $valor_filtro.$result[$i]['COD_TIPO_PRODUCTO'].',';
	}
	$valor_filtro = substr($valor_filtro, 0, strlen($valor_filtro)-1);	//borra ultima coma
}

$check_box = new edit_check_box('SELECCION', 'S', 'N');
$edit_text = new edit_text_hidden('COD_TIPO_PRODUCTO');
$a_values = explode(",", $valor_filtro);
for ($i=0 ; $i < count($result); $i++) {
	$temp->gotoNext("TIPO_PRODUCTO");		

	if ($i % 2 == 0)
		$temp->setVar("TIPO_PRODUCTO.DW_TR_CSS", datawindow::css_claro);
	else
		$temp->setVar("TIPO_PRODUCTO.DW_TR_CSS", datawindow::css_oscuro);

	$cod_tipo_producto = $result[$i]['COD_TIPO_PRODUCTO'];
	if ($a_values[$i] == $cod_tipo_producto)
		$html = $check_box->draw_entrable('S', $i);
	else			
		$html = $check_box->draw_entrable('N', $i);
		
	
	$temp->setVar("TIPO_PRODUCTO.SELECCION", $html);
		
	$html = $edit_text->draw_entrable($cod_tipo_producto, $i);
	$temp->setVar("TIPO_PRODUCTO.COD_TIPO_PRODUCTO", $html);			

	$nom_tipo_producto = $result[$i]['NOM_TIPO_PRODUCTO'];
	$temp->setVar("TIPO_PRODUCTO.NOM_TIPO_PRODUCTO", $nom_tipo_producto);			
}
print $temp->toString();

?>