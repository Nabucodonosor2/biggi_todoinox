<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_biggi.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_biggi.php");

class wi_inf_bodega_inv_compras extends w_param_informe_biggi {
	
	function wi_inf_bodega_inv_compras($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_bodega_inv_compras/inf_bodega_inv_compras.xml';
		parent::w_param_informe_biggi('inf_bodega_inv_compras', $cod_item_menu, 'Inventario_compras', $xml, '', 'spi_bodega_inv_compras');

		$sql = "select null NADA";
		$this->dws['dw_param'] = new datawindow($sql);
	}
	
	function make_filtro() {
		//FILTRO
		$this->filtro = "Fecha impresión = ".$this->current_date()."; ";		
		$this->param = '';
	}
}
?>