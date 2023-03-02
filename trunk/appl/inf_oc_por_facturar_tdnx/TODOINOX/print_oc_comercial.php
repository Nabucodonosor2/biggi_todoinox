<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");
require_once("class_print_reporte_comercial.php");
require_once("class_print_reporte_bodega.php");
$sistema			= $_REQUEST['sistema'];
$cod_orden_compra	= $_REQUEST['cod_orden_compra'];
$bd_sistema			= '';

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
if($sistema == 'COMERCIAL')
	$bd_sistema = 'BIGGI';
else
	$bd_sistema = 'BODEGA_BIGGI';

$sql = "SELECT  OC.COD_ORDEN_COMPRA,
				OC.COD_NOTA_VENTA,
				OC.SUBTOTAL,
				OC.PORC_DSCTO1,
				OC.MONTO_DSCTO1,
				OC.PORC_DSCTO2,
				OC.MONTO_DSCTO2,
				OC.TOTAL_NETO,
				OC.PORC_IVA,
				OC.MONTO_IVA,
				OC.TOTAL_CON_IVA,
				OC.REFERENCIA,																
				OC.OBS,
				E.NOM_EMPRESA,
				E.RUT,
				E.DIG_VERIF,
				$bd_sistema.dbo.f_get_direccion('SUCURSAL', OC.COD_SUCURSAL, '[DIRECCION] [NOM_COMUNA] [NOM_CIUDAD]') DIRECCION,
				$bd_sistema.dbo.f_format_date(OC.FECHA_ORDEN_COMPRA, 3) FECHA_ORDEN_COMPRA,	
				S.TELEFONO,
				S.FAX,
				P.NOM_PERSONA,
				U.NOM_USUARIO,
				U.MAIL,
				IOC.NOM_PRODUCTO,
				case IOC.COD_PRODUCTO
					when 'T' then ''
					else IOC.COD_PRODUCTO
				end COD_PRODUCTO,
				case IOC.COD_PRODUCTO
					when 'T' then ''
					else IOC.ITEM
				end ITEM,
				IOC.ORDEN,
				IOC.CANTIDAD,
				IOC.PRECIO,
				IOC.CANTIDAD * IOC.PRECIO TOTAL_IOC,			
				M.SIMBOLO,
				$bd_sistema.dbo.f_get_parametro(6) NOM_EMPRESA_EMISOR,
				$bd_sistema.dbo.f_get_parametro(20) RUT_EMPRESA,
				$bd_sistema.dbo.f_get_parametro(10) DIR_EMPRESA,
				$bd_sistema.dbo.f_get_parametro(21) GIRO_EMPRESA,
				$bd_sistema.dbo.f_get_parametro(11) TEL_EMPRESA,	
				$bd_sistema.dbo.f_get_parametro(12) FAX_EMPRESA,
				$bd_sistema.dbo.f_get_parametro(13) MAIL_EMPRESA,
				$bd_sistema.dbo.f_get_parametro(14) CIUDAD_EMPRESA,
				$bd_sistema.dbo.f_get_parametro(15) PAIS_EMPRESA,
				$bd_sistema.dbo.f_get_parametro(25) SITIO_WEB_EMPRESA,
				$bd_sistema.dbo.f_emp_get_cc(NV.COD_EMPRESA) CC_EMPRESA,
				IOC.CANTIDAD - dbo.f_fa_facturado_oc_ws(IOC.COD_ITEM_ORDEN_COMPRA ,'$sistema') CANT_X_FACT
		FROM $bd_sistema.dbo.ORDEN_COMPRA OC LEFT OUTER JOIN $bd_sistema.dbo.PERSONA P ON  OC.COD_PERSONA = P.COD_PERSONA
										LEFT OUTER JOIN $bd_sistema.dbo.NOTA_VENTA NV ON NV.COD_NOTA_VENTA = OC.COD_NOTA_VENTA,
			$bd_sistema.dbo.ITEM_ORDEN_COMPRA IOC,
			$bd_sistema.dbo.EMPRESA E,
			$bd_sistema.dbo.SUCURSAL S,
			$bd_sistema.dbo.USUARIO U,
			$bd_sistema.dbo.MONEDA M
		WHERE OC.COD_ORDEN_COMPRA = $cod_orden_compra
		AND E.COD_EMPRESA = OC.COD_EMPRESA 
		AND S.COD_SUCURSAL = OC.COD_SUCURSAL 
		AND U.COD_USUARIO = OC.COD_USUARIO_SOLICITA 
		AND IOC.COD_ORDEN_COMPRA = OC.COD_ORDEN_COMPRA 
		AND M.COD_MONEDA = OC.COD_MONEDA
		order by IOC.ORDEN ASC";

$result_oc = $db->build_results($sql);

$labels = array();
$labels['strCOD_ORDEN_COMPRA'] = $cod_orden_compra;
$labels['strFECHA_ORDEN_COMPRA'] = $result_oc[0]['FECHA_ORDEN_COMPRA'];

if($sistema == 'COMERCIAL'){
	$rpt = new print_reporte_comercial($sql, K_ROOT_DIR.'appl/orden_compra/orden_compra_wo.xml', $labels, "Orden de Compra ".$cod_orden_compra, 1, true);
}else if($sistema == 'BODEGA')
	$rpt = new print_reporte_bodega($sql, K_ROOT_DIR.'appl/orden_compra/orden_compra.xml', $labels, "Orden de Compra ".$cod_orden_compra, 1, true);
?>