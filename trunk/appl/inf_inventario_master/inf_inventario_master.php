<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

if (isset($_POST['b_ok'])) {
	$ano = $_POST['ANO_0'];
	$mes_desde = $_POST['MES_DESDE_0'];
	$mes_hasta = $_POST['MES_HASTA_0'];
	$cod_producto = $_POST['COD_PRODUCTO_0'];
	$find_exacto = $_POST['FIND_EXACTO_0'];

	session::set("inf_inventario_master.ANO", $ano);
	session::set("inf_inventario_master.MES_DESDE", $mes_desde);
	session::set("inf_inventario_master.MES_HASTA", $mes_hasta);
	session::set("inf_inventario_master.COD_PRODUCTO", $cod_producto);
	session::set("inf_inventario_master.FIND_EXACTO", $find_exacto);
	session::set("inf_inventario_master.5_ULTIMO", 'N');
	$url = "../../../../commonlib/trunk/php/mantenedor.php?modulo=inf_inventario_master&cod_item_menu=3054";
	header ('Location:'.$url);
}else if (isset($_POST['b_ok5'])) {
    $ano = $_POST['ANO_0'];
    $mes_desde = $_POST['MES_DESDE_0'];
    $mes_hasta = $_POST['MES_HASTA_0'];
    $cod_producto = $_POST['COD_PRODUCTO_NEW_0'];
    $find_exacto = $_POST['FIND_EXACTO_NEW_0'];

    session::set("inf_inventario_master.ANO", $ano);
    session::set("inf_inventario_master.MES_DESDE", $mes_desde);
    session::set("inf_inventario_master.MES_HASTA", $mes_hasta);
    session::set("inf_inventario_master.COD_PRODUCTO", $cod_producto);
    session::set("inf_inventario_master.FIND_EXACTO", $find_exacto);
    session::set("inf_inventario_master.5_ULTIMO", 'S');
    $url = "../../../../commonlib/trunk/php/mantenedor.php?modulo=inf_inventario_master&cod_item_menu=3045";
    header ('Location:'.$url);
}else if (isset($_POST['b_cancel'])) {
	base::presentacion();
}
else {
	$temp = new Template_appl('inf_inventario_master.htm');

	// make_menu
	$menu = session::get('menu_appl');
	$menu->draw($temp);

	$sql = "select year(getdate()) ANO
				, month(getdate()) MES_DESDE
				, month(getdate()) MES_HASTA
				, null COD_PRODUCTO
                , null COD_PRODUCTO_NEW
				, 'N' FIND_EXACTO
                , 'N' FIND_EXACTO_NEW";
	$dw_param = new datawindow($sql);

	$sql = "select ANO,NOM_ANO
            from dbo.f_anos_ventas_x_mes()
            order by ano DESC";
	$dw_param->add_control(new drop_down_dw('ANO',$sql));
	$dw_param->add_control(new edit_mes('MES_DESDE'));
	$dw_param->add_control(new edit_mes('MES_HASTA'));
	$dw_param->add_control(new edit_text_upper('COD_PRODUCTO', 20, 20));
	$dw_param->add_control(new edit_text_upper('COD_PRODUCTO_NEW', 20, 20));
	$dw_param->add_control(new edit_check_box('FIND_EXACTO', 'S', 'N', 'Búqueda exacta'));
	$dw_param->add_control(new edit_check_box('FIND_EXACTO_NEW', 'S', 'N', 'Búsqueda exacta'));
	
	$sql = "select OPCIONES
            from x28112013_OP_CM
			WHERE COD_INGRESO_PAGO IN (44571, 45583, 44520, 44550)
            order by cod_orden_pago desc";
			
		$dw_param->add_control(new drop_down_dw('OPCIONES',$sql));

	// draw
	$dw_param->retrieve();
	$cod_usuario = $dw_param->cod_usuario;
	if($cod_usuario == 1 || $cod_usuario == 16){
	    $dw_param->set_item(0, 'MES_DESDE', 1);
	}
	$dw_param->habilitar($temp, true);

	print $temp->toString();
}
?>
