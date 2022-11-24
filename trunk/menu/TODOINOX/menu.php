<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

//////////////////////
$archivo = array( new item_menu('Cambio password', '0505', "../../../commonlib/trunk/php/change_password.php"),
    new item_menu('-'),
    new item_menu('Salir', '0510', "../../../commonlib/trunk/php/cerrar_sesion.php"));

$maestro = array( new item_menu('Empresas', '1005', "../../../commonlib/trunk/php/mantenedor.php?modulo=empresa&cod_item_menu=1005"),
    new item_menu('-'),
    new item_menu('Productos', '1010', "../../../commonlib/trunk/php/mantenedor.php?modulo=producto&cod_item_menu=1010"),
    new item_menu('Productos new', '1011', "../../../commonlib/trunk/php/mantenedor.php?modulo=producto_new&cod_item_menu=1011"),

    new item_menu('-'),
    new item_menu('Usuarios', '1015', "../../../commonlib/trunk/php/mantenedor.php?modulo=usuario&cod_item_menu=1015"),
    new item_menu('Perfiles', '1020', "../../../commonlib/trunk/php/mantenedor.php?modulo=perfil&cod_item_menu=1020"),
    new item_menu('-'),
    new item_menu('Parámetros', '1025', "../appl/parametro/wi_parametro.php?cod_item_menu=1025"));

$ventas = array(new item_menu('Cotización', '1505', "../../../commonlib/trunk/php/mantenedor.php?modulo=cotizacion&cod_item_menu=1505"),
    new item_menu('Nota Venta', '1510', "../../../commonlib/trunk/php/mantenedor.php?modulo=nota_venta&cod_item_menu=1510"),
    new item_menu('Orden Compra', '1520', "../../../commonlib/trunk/php/mantenedor.php?modulo=orden_compra&cod_item_menu=1520"),
    new item_menu('-'),
    new item_menu('Factura', '1535', "../../../commonlib/trunk/php/mantenedor.php?modulo=factura&cod_item_menu=1535"),
    new item_menu('Orden Despacho', '1550', "../../../commonlib/trunk/php/mantenedor.php?modulo=orden_despacho&cod_item_menu=1550"),
    new item_menu('Nota Crédito', '1540', "../../../commonlib/trunk/php/mantenedor.php?modulo=nota_credito&cod_item_menu=1540"),

    new item_menu('-'),
    new item_menu('Guía Despacho', '1525', "../../../commonlib/trunk/php/mantenedor.php?modulo=guia_despacho&cod_item_menu=1525"),
    new item_menu('-'),
    new item_menu('Guía Recepción', '1530', "../../../commonlib/trunk/php/mantenedor.php?modulo=guia_recepcion&cod_item_menu=1530"));

$administracion = array(new item_menu('Ingreso Pago', '2505', "../../../commonlib/trunk/php/mantenedor.php?modulo=ingreso_pago&cod_item_menu=2505"),
    new item_menu('Ingreso Pago Web Service', '2570', "../../../commonlib/trunk/php/mantenedor.php?modulo=pago_faprov_comercial&cod_item_menu=2570"),
    new item_menu('-'),
    new item_menu('Asignación Documentos', '2520', "../../../commonlib/trunk/php/mantenedor.php?modulo=asig_nro_doc_sii&cod_item_menu=2520"),
    new item_menu('-'),
    new item_menu('FA Proveedor', '2525', "../../../commonlib/trunk/php/mantenedor.php?modulo=faprov&cod_item_menu=2525"),
    new item_menu('NC Proveedor', '2526',"../../../commonlib/trunk/php/mantenedor.php?modulo=ncprov&cod_item_menu=2526"),
    new item_menu('Pago Proveedor', '2530', "../../../commonlib/trunk/php/mantenedor.php?modulo=pago_faprov&cod_item_menu=2530"),
    new item_menu('-'),
    new item_menu('Traspaso Softland', '2545', "../../../commonlib/trunk/php/mantenedor.php?modulo=envio_softland&cod_item_menu=2545"),
    new item_menu('-'),
    new item_menu('Gasto Fijo', '2550', "../../../commonlib/trunk/php/mantenedor.php?modulo=gasto_fijo&cod_item_menu=2550"),
    new item_menu('-'),
    new item_menu('Cheque Todoinox', '2555', "../appl/cheque_todoinox/cheque_todoinox.php"),
    new item_menu('Informe', '2565', "../../../commonlib/trunk/php/mantenedor.php?modulo=pago_comision_tdnx&cod_item_menu=2565"),
    new item_menu('-'),
    new item_menu('Facturas Rechazadas', '2590', "../../../commonlib/trunk/php/mantenedor.php?modulo=factura_rechazada&cod_item_menu=2590")
);

$bodega = array(new item_menu('Registro Ingreso', '3010', "../../../commonlib/trunk/php/mantenedor.php?modulo=registro_ingreso&cod_item_menu=3010"),
    new item_menu('-'),
    new item_menu('Entrada', '3015', "../../../commonlib/trunk/php/mantenedor.php?modulo=entrada_bodega&cod_item_menu=3015"),
    new item_menu('Salida', '3020', "../../../commonlib/trunk/php/mantenedor.php?modulo=salida_bodega&cod_item_menu=3020"),
    new item_menu('-'),
    new item_menu('Tarjeta Existencia', '3035', "../../../commonlib/trunk/php/informe.php?informe=inf_bodega_tarjeta_existencia&cod_item_menu=3035"),
    new item_menu('-'),
    new item_menu('Inventario', '3025', "../../../commonlib/trunk/php/informe.php?informe=inf_tdnx_inventario&cod_item_menu=3025"),
    new item_menu('Inventario Valorizado', '3030', "../../../commonlib/trunk/php/informe.php?informe=inf_tdnx_inventario_valorizado&cod_item_menu=3030"),
    new item_menu('Inventario Valorizado New', '3031', "../../../commonlib/trunk/php/informe.php?informe=inf_tdnx_inventario_valorizado_new&cod_item_menu=3031"),
    new item_menu('Inventario Master', '3045', "../appl/inf_inventario_master/inf_inventario_master.php")
);

$comex = array( new item_menu('Quote', '3505', "../../../commonlib/trunk/php/mantenedor.php?modulo=cx_cot_extranjera&cod_item_menu=3505"),
    new item_menu('Purchase Order', '3510', "../../../commonlib/trunk/php/mantenedor.php?modulo=cx_oc_extranjera&cod_item_menu=3510"),
    new item_menu('Proveedor EXT', '3515', "../../../commonlib/trunk/php/mantenedor.php?modulo=proveedor_ext&cod_item_menu=3515"),
    new item_menu('-'),
    new item_menu('Carta Compra USD','3516',"../../../commonlib/trunk/php/mantenedor.php?modulo=carta_compra_usd&cod_item_menu=3516"),
    new item_menu('-'),
    new item_menu('Parametros COMEX', '3517', "../appl/cx_parametro/wi_cx_parametro.php?cod_item_menu=3517"),
    new item_menu('-'),
    new item_menu('Dolar', '3565', "../../../commonlib/trunk/php/mantenedor.php?modulo=dolar&cod_item_menu=3565"),
    new item_menu('-'),
    new item_menu('Cuadro de Embarque', '3570', "../appl/cx_archivo_excel/descarga_cuadro_embarque.php"),
    new item_menu('Cuadro de Pago', '3575', "../appl/cx_archivo_excel/descarga_cuadro_pago.php")
);

$informes = array(new item_menu('Facturas por Equipo', '4005', "../appl/inf_ventas_por_equipo/inf_ventas_por_equipo.php"),
    new item_menu('Facturas por Cobrar', '4035', "../../../commonlib/trunk/php/mantenedor.php?modulo=inf_facturas_por_cobrar&cod_item_menu=4035"),
    new item_menu('Facturas por Cliente', '4060', "../appl/inf_facturas_por_cliente/inf_facturas_por_cliente.php"),
    new item_menu('Facturas por Mes', '4065', "../appl/inf_facturas_por_mes/inf_facturas_por_mes.php"),
    new item_menu('Resumen Ventas', '4092', "../appl/inf_resumen_venta/inf_resumen_venta.php"),
    new item_menu('Venta Diaria', '4094', "../appl/inf_venta_diaria/inf_venta_diaria.php"),
    new item_menu('OC Pendientes por Facturar TDNX', '4095', "../appl/inf_oc_por_facturar_tdnx/TODOINOX/inf_oc_por_facturar_tdnx.php?cod_item_menu=4098"),
    new item_menu('OC Pendientes Por Facturar desde Servindus', '4096', "../../../commonlib/trunk/php/mantenedor.php?modulo=inf_oc_por_facturar_servindus&cod_item_menu=4096"),
    new item_menu('OC Pendientes por Facturar desde Bodega Biggi', '4097', "../../../commonlib/trunk/php/mantenedor.php?modulo=inf_oc_por_facturar_bodega&cod_item_menu=4097")
);

$menu = new menu(array(new item_menu('Archivo', '15', '', $archivo),
    new item_menu('Maestros', '15', '', $maestro),
    new item_menu('Ventas', '15', '', $ventas),
    new item_menu('Administración', '15', '', $administracion),
    new item_menu('Bodega', '15', '', $bodega),
    new item_menu('Comex', '15', '', $comex),
    new item_menu('Informes', '15', '', $informes))
    ,188);
?>
