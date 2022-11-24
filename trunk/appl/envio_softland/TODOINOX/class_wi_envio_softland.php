<?php
//////////////////////////////////////////////////////////////////
/////////////////////////// TODOINOX ///////////////////////////
//////////////////////////////////////////////////////////////////

require_once(dirname(__FILE__) . "/../../../../../commonlib/trunk/php/auto_load.php");

class wi_envio_softland extends wi_envio_softland_base{
	function wi_envio_softland($cod_item_menu) {
		parent::wi_envio_softland_base($cod_item_menu);
		$this->cod_cuenta_otro_ingreso = 7002005;	//para TODOINOX
		$this->cod_cuenta_otro_gasto = 8005001;	//para TODOINOX 
		$this->cuenta_por_pagar_boleta = 2112013;	//para TODOINOX 
		$this->cc_otro_ingreso = '""';				//para TODOINOX
	}
	function send_venta_iva($handle, $tipo_doc, $i, $cuenta, $monto, $centro_costo) {
		// para TODOINOX no tiene CENTRO COSTO
		parent::send_venta_iva($handle, $tipo_doc, $i, $cuenta, $monto, '');
	}
}
?>