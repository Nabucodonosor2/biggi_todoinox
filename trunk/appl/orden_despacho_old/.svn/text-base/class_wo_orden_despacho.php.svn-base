<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_output_biggi.php");
include(dirname(__FILE__)."/../../appl.ini");

class wo_orden_despacho extends w_output_biggi{
	function wo_orden_despacho(){
		// Llama a w_base para que $this->cod_usuario contenga al usuario actual 
   		parent::w_base('orden_despacho', $_REQUEST['cod_item_menu']);

		$sql = "SELECT COD_ORDEN_DESPACHO
					  ,CONVERT(VARCHAR, FECHA_ORDEN_DESPACHO, 103) FECHA_ORDEN_DESPACHO
					  ,FECHA_ORDEN_DESPACHO DATE_FECHA_ORDEN_DESPACHO
					  ,RUT
					  ,DIG_VERIF
					  ,NOM_EMPRESA
					  ,COD_USUARIO_VENDEDOR1
					  ,U.INI_USUARIO
				FROM ORDEN_DESPACHO OD
					,USUARIO U
				WHERE OD.COD_USUARIO_VENDEDOR1 = U.COD_USUARIO
				ORDER BY COD_ORDEN_DESPACHO DESC";
				
		parent::w_output_biggi('orden_despacho', $sql, $_REQUEST['cod_item_menu']);
  		
		$this->dw->add_control(new static_num('RUT'));
		
		// headers
		$this->add_header(new header_num('COD_ORDEN_DESPACHO', 'COD_ORDEN_DESPACHO', 'Nº OD'));
		$this->add_header($control = new header_date('FECHA_ORDEN_DESPACHO', 'FECHA_ORDEN_DESPACHO', 'Fecha'));
		$control->field_bd_order = 'DATE_FECHA_ORDEN_DESPACHO';
		$this->add_header(new header_rut('RUT', 'ORDEN_DESPACHO', 'Rut'));
		$this->add_header(new header_text('NOM_EMPRESA', 'NOM_EMPRESA', 'Razón Social'));
		$sql = "select distinct U.COD_USUARIO, U.NOM_USUARIO from FACTURA F, USUARIO U where F.COD_USUARIO_VENDEDOR1 = U.COD_USUARIO order by NOM_USUARIO";
		$this->add_header(new header_drop_down('INI_USUARIO', 'COD_USUARIO_VENDEDOR1', 'V1', $sql));

  	}
}
?>