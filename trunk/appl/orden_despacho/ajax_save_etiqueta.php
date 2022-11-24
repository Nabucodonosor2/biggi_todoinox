<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if(session::is_set('_AJAX_ETIQUETA_'))
	$result = session::get('_AJAX_ETIQUETA_');
else
	$result = array();
	
$my_row['COD_PRODUCTO'] = $_REQUEST['cod_prod'];
$my_row['NORMAL_CHECK'] = $_REQUEST['nom_check'];
$my_row['BULTO1'] = $_REQUEST['bulto1'];
$my_row['BULTO2'] = $_REQUEST['bulto2'];
$my_row['BULTO3'] = $_REQUEST['bulto3'];
$my_row['BULTO4'] = $_REQUEST['bulto4'];
$my_row['BULTO5'] = $_REQUEST['bulto5'];
$my_row['BULTO6'] = $_REQUEST['bulto6'];

$result[] = $my_row;

session::set('_AJAX_ETIQUETA_', $result);
?>