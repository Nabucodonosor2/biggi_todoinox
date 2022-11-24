<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");

$prompt = $_REQUEST['prompt'];
$valor =  $_REQUEST['valor'];
$cod_usuario = session::get("COD_USUARIO");

$temp = new Template_appl('dlg_crear_desde.htm');
//MS SOLICITA QUE POR PERIODO COVID PUEDAN FACTURAR SIN PISTOLA LORETO Y LIFEN 15032020
if($cod_usuario == 1 || $cod_usuario == 7 || $cod_usuario == 34 || $cod_usuario == 20 || $cod_usuario == 31 || $cod_usuario == 34){
	$temp->setVar("DESDE_BODEGA", '<input id="DESDE_BODEGA" type="radio" name="group" value="BODEGA"> Desde Bodega');
	$temp->setVar("DESDE_COMERCIAL", '<input id="DESDE_COMERCIAL" type="radio" name="group" value="BODEGA" checked> Desde Comercial');
	$temp->setVar("DESDE_RENTAL", '<input id="DESDE_RENTAL" type="radio" name="group" value="BODEGA"> Desde Rental');
}else{
	$temp->setVar("DESDE_BODEGA", '');
	$temp->setVar("DESDE_COMERCIAL", '');
	$temp->setVar("DESDE_RENTAL", '');
}
$temp->setVar("PROMPT", $prompt);
$temp->setVar("VALOR", $valor);
$temp->setVar("COD_USUARIO", "<input id=\"COD_USUARIO_0\" class=\"input_text\" type=\"hidden\" value=\"$cod_usuario\" name=\"COD_USUARIO_0\">");

print $temp->toString();
?>