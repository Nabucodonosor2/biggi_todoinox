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
					  ,OD.COD_USUARIO_VENDEDOR1
					  ,F.NRO_FACTURA
					  ,OD.RUT
					  ,OD.DIG_VERIF
					  ,OD.NOM_EMPRESA
					  ,OD.NOM_PERSONA
					  ,OD.COD_ESTADO_ORDEN_DESPACHO
					  ,OD.COD_USUARIO_DESPACHA
					  ,U.INI_USUARIO
					  ,E.NOM_ESTADO_ORDEN_DESPACHO
					  ,(SELECT UD.NOM_USUARIO FROM USUARIO UD WHERE UD.COD_USUARIO = OD.COD_USUARIO_DESPACHA) NOM_USUARIO_DESPACHA
				FROM ORDEN_DESPACHO OD
					,USUARIO U
					,FACTURA F
					,ESTADO_ORDEN_DESPACHO E
				WHERE OD.COD_USUARIO_VENDEDOR1 = U.COD_USUARIO
				AND F.COD_FACTURA = OD.COD_DOC_ORIGEN
				AND E.COD_ESTADO_ORDEN_DESPACHO = OD.COD_ESTADO_ORDEN_DESPACHO
				ORDER BY COD_ORDEN_DESPACHO DESC";
				
		parent::w_output_biggi('orden_despacho', $sql, $_REQUEST['cod_item_menu']);
  		
		$this->dw->add_control(new static_num('RUT'));
		
		// headers
		$this->add_header(new header_num('COD_ORDEN_DESPACHO', 'COD_ORDEN_DESPACHO', 'Nº OD'));
		$this->add_header($control = new header_date('FECHA_ORDEN_DESPACHO', 'FECHA_ORDEN_DESPACHO', 'Fecha'));
		$control->field_bd_order = 'DATE_FECHA_ORDEN_DESPACHO';
		$this->add_header($header = new header_rut('RUT', 'OD', 'Rut'));
		$this->add_header(new header_text('NOM_EMPRESA', 'NOM_EMPRESA', 'Razón Social'));
		$sql = "select distinct U.COD_USUARIO, U.NOM_USUARIO from FACTURA F, USUARIO U where F.COD_USUARIO_VENDEDOR1 = U.COD_USUARIO order by NOM_USUARIO";
		$this->add_header(new header_drop_down('INI_USUARIO', 'COD_USUARIO_VENDEDOR1', 'V1', $sql));
		
		$this->add_header(new header_num('NRO_FACTURA', 'NRO_FACTURA', 'Nº Factura'));
		
		$this->add_header(new header_text('NOM_PERSONA', 'NOM_PERSONA', 'Atención'));
		
		$sql_est = "select COD_ESTADO_ORDEN_DESPACHO,NOM_ESTADO_ORDEN_DESPACHO  
				from ESTADO_ORDEN_DESPACHO";
		$this->add_header(new header_drop_down('NOM_ESTADO_ORDEN_DESPACHO', 'OD.COD_ESTADO_ORDEN_DESPACHO', 'Estado', $sql_est));
		$sql = "select  U.COD_USUARIO COD_USUARIO_DESPACHA, U.NOM_USUARIO NOM_USUARIO_DESPACHA
				from USUARIO U 
				where U.ES_DESPACHADOR = 'S'
				 order by NOM_USUARIO";
		$this->add_header(new header_drop_down('NOM_USUARIO_DESPACHA', 'OD.COD_USUARIO_DESPACHA', 'U DESPACHO', $sql));
		
		// Filtro inicial
		/*$header->valor_filtro = '80.112.900-5';
		$this->make_filtros();*/
  	}
}
?>