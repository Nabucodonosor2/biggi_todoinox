<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");
$cod_orden_compra	= $_REQUEST['cod_orden_compra'];
$sistema			= $_REQUEST['sistema'];
$inventario			= $_REQUEST['inventario'];
$cod_usuario		= session::get("COD_USUARIO");

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$temp = new Template_appl('dlg_orden_compra_tdnx.htm');	

$sql = "SELECT SISTEMA
			  ,URL_WS
			  ,USER_WS
			  ,PASSWROD_WS
		FROM PARAMETRO_WS
		WHERE SISTEMA = '$sistema'";
$result = $db->build_results($sql);

$user_ws		= $result[0]['USER_WS'];
$passwrod_ws	= $result[0]['PASSWROD_WS'];
$url_ws			= $result[0]['URL_WS'];

$biggi = new client_biggi("$user_ws", "$passwrod_ws", "$url_ws");
$result = $biggi->cli_orden_compra($cod_orden_compra);

if($result['ORDEN_COMPRA'][0]['COD_NOTA_VENTA'] == '')
	$result['ORDEN_COMPRA'][0]['COD_NOTA_VENTA'] = 'NULL';

$COD_NOTA_VENTA	= ($result['ORDEN_COMPRA'][0]['COD_NOTA_VENTA'] =='') ? "null" : $result['ORDEN_COMPRA'][0]['COD_NOTA_VENTA'];	
$PORC_DSCTO1	= ($result['ORDEN_COMPRA'][0]['PORC_DSCTO1'] =='') ? "null" : $result['ORDEN_COMPRA'][0]['PORC_DSCTO1'];
$PORC_DSCTO2	= ($result['ORDEN_COMPRA'][0]['PORC_DSCTO2'] =='') ? "null" : $result['ORDEN_COMPRA'][0]['PORC_DSCTO2'];	
$OBS			= ($result['ORDEN_COMPRA'][0]['OBS'] =='') ? "null" : "'".$result['ORDEN_COMPRA'][0]['OBS']."'";

$sql = "SELECT ".$result['ORDEN_COMPRA'][0]['COD_ORDEN_COMPRA']." COD_ORDEN_COMPRA
			  ,".$result['ORDEN_COMPRA'][0]['COD_ORDEN_COMPRA']." COD_ORDEN_COMPRA_H
			  ,'".$result['ORDEN_COMPRA'][0]['FECHA_ORDEN_COMPRA']."' FECHA_ORDEN_COMPRA
			  ,'".$result['ORDEN_COMPRA'][0]['OC_NOM_USUARIO']."' OC_NOM_USUARIO
			  ,'".$result['ORDEN_COMPRA'][0]['OC_NOM_MONEDA']."' OC_NOM_MONEDA
			  ,'".$result['ORDEN_COMPRA'][0]['ESTADO_OC']."' ESTADO_OC
			  ,$COD_NOTA_VENTA COD_NOTA_VENTA
			  ,'".$result['ORDEN_COMPRA'][0]['REFERENCIA']."' REFERENCIA
			  ,".$result['ORDEN_COMPRA'][0]['SUBTOTAL']." SUBTOTAL
			  ,$PORC_DSCTO1 PORC_DSCTO1
			  ,".$result['ORDEN_COMPRA'][0]['MONTO_DSCTO1']." MONTO_DSCTO1
			  ,$PORC_DSCTO2 PORC_DSCTO2
			  ,".$result['ORDEN_COMPRA'][0]['MONTO_DSCTO2']." MONTO_DSCTO2
			  ,".$result['ORDEN_COMPRA'][0]['TOTAL_NETO']." TOTAL_NETO
			  ,".$result['ORDEN_COMPRA'][0]['MONTO_IVA']." MONTO_IVA
			  ,((".$result['ORDEN_COMPRA'][0]['MONTO_IVA']." * 100)/".$result['ORDEN_COMPRA'][0]['TOTAL_NETO'].") PORC_IVA
			  ,".$result['ORDEN_COMPRA'][0]['TOTAL_CON_IVA']." TOTAL_CON_IVA
			  ,".$result['ORDEN_COMPRA'][0]['TOTAL_CON_IVA']." TOTAL_CON_IVA_H
			  ,$OBS OBS";

$dw = new datawindow($sql);
$dw->add_control(new static_num('SUBTOTAL'));
$dw->add_control(new static_num('MONTO_DSCTO1'));
$dw->add_control(new static_num('MONTO_DSCTO2'));
$dw->add_control(new static_num('TOTAL_NETO'));
$dw->add_control(new static_num('MONTO_IVA'));
$dw->add_control(new static_num('TOTAL_CON_IVA'));
$dw->add_control(new edit_text_hidden('TOTAL_CON_IVA_H'));
$dw->add_control(new edit_text_hidden('COD_ORDEN_COMPRA_H'));
$dw->add_control(new edit_text_multiline('OBS',44,4));

$dw->retrieve();
$dw->habilitar($temp, false);

/*$sql_count = "SELECT COUNT(*) REGISTRO
			  FROM INF_OC_POR_FACTURAR_TDNX
			  WHERE COD_USUARIO = $cod_usuario";
$result_count = $db->build_results($sql_count);*/

$biggi->cli_oc_por_facturar_indiv($cod_usuario, $inventario='' ,$sistema, $cod_orden_compra);

for ($i=0 ; $i < count($result['ITEM_ORDEN_COMPRA']) ; $i++){
	$temp->gotoNext("ITEM_ORDEN_COMPRA");		

	$temp->setVar("ITEM_ORDEN_COMPRA.ORDEN", $result['ITEM_ORDEN_COMPRA'][$i]['ORDEN']);
	$temp->setVar("ITEM_ORDEN_COMPRA.ITEM", $result['ITEM_ORDEN_COMPRA'][$i]['ITEM']);
	$temp->setVar("ITEM_ORDEN_COMPRA.COD_PRODUCTO", $result['ITEM_ORDEN_COMPRA'][$i]['COD_PRODUCTO']);
	$temp->setVar("ITEM_ORDEN_COMPRA.NOM_PRODUCTO", $result['ITEM_ORDEN_COMPRA'][$i]['NOM_PRODUCTO']);
	$temp->setVar("ITEM_ORDEN_COMPRA.CANTIDAD", $result['ITEM_ORDEN_COMPRA'][$i]['CANTIDAD']);
	
	$sql = "SELECT CANT_FA
				  ,CANT_POR_FACT
			FROM INF_OC_POR_FACTURAR_TDNX
			WHERE COD_USUARIO = $cod_usuario
			AND COD_ITEM_ORDEN_COMPRA = ".$result['ITEM_ORDEN_COMPRA'][$i]['COD_ITEM_ORDEN_COMPRA'];
	$result2 = $db->build_results($sql);



	if($result2[0]['CANT_POR_FACT'] > 0){
		if ($i % 2 == 0)
			$temp->setVar("ITEM_ORDEN_COMPRA.DW_TR_CSS", "claro2");
		else
			$temp->setVar("ITEM_ORDEN_COMPRA.DW_TR_CSS", "oscuro2");
	}else{
		if ($i % 2 == 0)
			$temp->setVar("ITEM_ORDEN_COMPRA.DW_TR_CSS", datawindow::css_claro);
		else
			$temp->setVar("ITEM_ORDEN_COMPRA.DW_TR_CSS", datawindow::css_oscuro);
	}
	
	$temp->setVar("ITEM_ORDEN_COMPRA.CANTIDAD_FACT", $result2[0]['CANT_FA']);
	$temp->setVar("ITEM_ORDEN_COMPRA.CANTIDAD_X_FACT", $result2[0]['CANT_POR_FACT']);
	$temp->setVar("ITEM_ORDEN_COMPRA.PRECIO", number_format($result['ITEM_ORDEN_COMPRA'][$i]['PRECIO'], 0, ',', '.'));
	$temp->setVar("ITEM_ORDEN_COMPRA.TOTAL", number_format($result['ITEM_ORDEN_COMPRA'][$i]['CANTIDAD'] * $result['ITEM_ORDEN_COMPRA'][$i]['PRECIO'], 0, ',', '.'));
}

$print = '<input name="b_print" id="b_print" src="../../../../../commonlib/trunk/images/b_print.jpg" type="image" '.
		 'onMouseDown="MM_swapImage(\'b_print\',\'\',\'../../../../../commonlib/trunk/images/b_print_click.jpg\',1)" '.
		 'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
		 'onMouseOver="MM_swapImage(\'b_print\',\'\',\'../../../../../commonlib/trunk/images/b_print_over.jpg\',1)" '.
		 'onClick="returnValue = true; window.close();"/>';

$temp->setVar("PRINT", $print);
$temp->setVar("SISTEMA", $sistema);
print $temp->toString();
?>