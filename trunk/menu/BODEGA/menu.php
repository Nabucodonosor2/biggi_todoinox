<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

//////////////////////
$archivo = array( new item_menu('Cambio password', '0505', "../../../commonlib/trunk/php/change_password.php"),
				new item_menu('-'),
				new item_menu('Mantencion', '0507', "../../../commonlib/trunk/php/mantenedor.php?modulo=mantencion_sw&cod_item_menu=0507"),
				new item_menu('Informe Mantenci�n', '0508', "../../../commonlib/trunk/php/informe.php?informe=inf_mantencion_sw&cod_item_menu=0508"),
				new item_menu('-'),
				new item_menu('Salir', '0510', "../../../commonlib/trunk/php/cerrar_sesion.php"));
				
$maestro = array( new item_menu('Empresas', '1005', "../../../commonlib/trunk/php/mantenedor.php?modulo=empresa&cod_item_menu=1005"),
				new item_menu('Productos', '1010', "../../../commonlib/trunk/php/mantenedor.php?modulo=producto&cod_item_menu=1010"),
				new item_menu('-'),
				new item_menu('Usuarios', '1015', "../../../commonlib/trunk/php/mantenedor.php?modulo=usuario&cod_item_menu=1015"),
				new item_menu('Perfiles', '1020', "../../../commonlib/trunk/php/mantenedor.php?modulo=perfil&cod_item_menu=1020"),
				new item_menu('-'),
				new item_menu('Par�metros', '1025', "../appl/parametro/wi_parametro.php?cod_item_menu=1025"));

$ventas = array( new item_menu('Cotizaci�n', '1505', "../../../commonlib/trunk/php/mantenedor.php?modulo=cotizacion&cod_item_menu=1505"),
				new item_menu('Solicitud de OC', '1517', "../../../commonlib/trunk/php/mantenedor.php?modulo=solicitud_compra&cod_item_menu=1517"),
				new item_menu('Orden Compra', '1520', "../../../commonlib/trunk/php/mantenedor.php?modulo=orden_compra&cod_item_menu=1520"),
				new item_menu('Gu�a Despacho', '1525', "../../../commonlib/trunk/php/mantenedor.php?modulo=guia_despacho&cod_item_menu=1525"),
				new item_menu('Gu�a Recepci�n', '1530', "../../../commonlib/trunk/php/mantenedor.php?modulo=guia_recepcion&cod_item_menu=1530"),
				new item_menu('Factura', '1535', "../../../commonlib/trunk/php/mantenedor.php?modulo=factura&cod_item_menu=1535"),
				new item_menu('Nota Cr�dito', '1540', "../../../commonlib/trunk/php/mantenedor.php?modulo=nota_credito&cod_item_menu=1540"));
								
$administracion = array( new item_menu('Ingreso Pago', '2505', "../../../commonlib/trunk/php/mantenedor.php?modulo=ingreso_pago&cod_item_menu=2505"),
				new item_menu('-'),
				new item_menu('Dep�sito', '2515', "../../../commonlib/trunk/php/mantenedor.php?modulo=deposito&cod_item_menu=2515"),
				new item_menu('-'),
				new item_menu('Asignaci�n Documentos', '2520', "../../../commonlib/trunk/php/mantenedor.php?modulo=asig_nro_doc_sii&cod_item_menu=2520"),
				new item_menu('-'),
				new item_menu('FA Proveedor', '2525', "../../../commonlib/trunk/php/mantenedor.php?modulo=faprov&cod_item_menu=2525"),
				new item_menu('NC Proveedor', '2526',"../../../commonlib/trunk/php/mantenedor.php?modulo=ncprov&cod_item_menu=2526"),
				new item_menu('Pago Proveedor', '2530', "../../../commonlib/trunk/php/mantenedor.php?modulo=pago_faprov&cod_item_menu=2530"),
				new item_menu('-'),
				new item_menu('Traspaso Softland', '2545', "../../../commonlib/trunk/php/mantenedor.php?modulo=envio_softland&cod_item_menu=2545"),
				new item_menu('-'),
				new item_menu('Gasto Fijo', '2550', "../../../commonlib/trunk/php/mantenedor.php?modulo=gasto_fijo&cod_item_menu=2550"));
																				
$bodega = array(new item_menu('Bodega', '3005',  "../../../commonlib/trunk/php/mantenedor.php?modulo=bodega&cod_item_menu=3005"),
				new item_menu('-'),
				//new item_menu('Registro Ingreso', '3010', "../../../commonlib/trunk/no_implementado.php"), = VM 13/07/2010 mover a COMEX
				new item_menu('Entrada', '3015', "../../../commonlib/trunk/php/mantenedor.php?modulo=entrada_bodega&cod_item_menu=3015"),
				new item_menu('Salida', '3020', "../../../commonlib/trunk/php/mantenedor.php?modulo=salida_bodega&cod_item_menu=3020")
				//new item_menu('Traspaso', '3025', "../../../commonlib/trunk/no_implementado.php"), = VM 13/07/2010 dice no VA
				//new item_menu('Ajuste', '3030', "../../../commonlib/trunk/php/mantenedor.php?modulo=ajuste_bodega&cod_item_menu=3030"),
				);
																				
$informes = array(new item_menu('Facturas por Equipo', '4015', "../../../commonlib/trunk/php/mantenedor.php?modulo=inf_ventas_por_equipo&cod_item_menu=4015"),
				new item_menu('Facturas por Cobrar', '4035', "../../../commonlib/trunk/php/mantenedor.php?modulo=inf_facturas_por_cobrar&cod_item_menu=4035"),
				new item_menu('Facturas por Mes', '4065', "../../../commonlib/trunk/php/mantenedor.php?modulo=inf_facturas_por_mes&cod_item_menu=4065"),
				new item_menu('Inventario Valorizado', '4080', "../../../commonlib/trunk/php/informe.php?informe=inf_bodega_stock&cod_item_menu=4080"),
				new item_menu('Inventario', '4085', "../../../commonlib/trunk/php/informe.php?informe=inf_bodega_inventario&cod_item_menu=4085"),
				new item_menu('Tarjeta Existencia', '4086', "../../../commonlib/trunk/php/informe.php?informe=inf_bodega_tarjeta_existencia&cod_item_menu=4086"),
				new item_menu('Informe Bodega por Recibir', '4090', "../../../commonlib/trunk/php/informe.php?informe=inf_bodega_por_recibir&cod_item_menu=4090"),
				new item_menu('Informe para compras', '4087', "../../../commonlib/trunk/php/informe.php?informe=inf_bodega_inv_compras&cod_item_menu=4087"),
				new item_menu('Por Despachar Comercial', '4088', "../../../commonlib/trunk/php/mantenedor.php?modulo=inf_por_despachar_comercial&cod_item_menu=4088"));
$menu = new menu(array(new item_menu('Archivo', '05', '', $archivo), 
						new item_menu('Maestros', '10', '', $maestro),
						new item_menu('Ventas', '10', '', $ventas),
						new item_menu('Administraci�n', '10', '', $administracion),
						new item_menu('Bodega', '10', '', $bodega),
						new item_menu('Informes', '10', '', $informes))
				,280);
?>