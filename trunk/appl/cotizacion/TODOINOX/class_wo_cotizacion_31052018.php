<?php
class wo_cotizacion extends wo_cotizacion_base{
	function wo_cotizacion(){
		parent::wo_cotizacion_base();
		$sql = "select	C.COD_COTIZACION
						,convert(varchar(20), C.FECHA_COTIZACION, 103) FECHA_COTIZACION
						,C.FECHA_COTIZACION DATE_COTIZACION
						,E.RUT
						,E.DIG_VERIF
						,E.NOM_EMPRESA
						,C.REFERENCIA
						,U.INI_USUARIO
						,C.COD_USUARIO_VENDEDOR1
						,EC.NOM_ESTADO_COTIZACION
						,C.TOTAL_CON_IVA
						,F.NRO_FACTURA
			from 		COTIZACION C left outer join FACTURA F ON F.COD_COTIZACION = C.COD_COTIZACION
						,EMPRESA E
						,USUARIO U
						,ESTADO_COTIZACION EC
			where		C.COD_EMPRESA = E.COD_EMPRESA and 
						C.COD_USUARIO_VENDEDOR1 = U.COD_USUARIO and 
						C.COD_ESTADO_COTIZACION = EC.COD_ESTADO_COTIZACION and
						dbo.f_get_tiene_acceso (".$this->cod_usuario.", 'COTIZACION',C.COD_USUARIO_VENDEDOR1, C.COD_USUARIO_VENDEDOR2) = 1
			order by	C.COD_COTIZACION desc";
			
     	parent::w_output('cotizacion', $sql, $_REQUEST['cod_item_menu']);
				
		$this->dw->add_control(new edit_nro_doc('COD_COTIZACION','COTIZACION'));
		$this->dw->add_control(new edit_precio('TOTAL_CON_IVA'));
      	$this->dw->add_control(new static_num('RUT'));
			
	      // headers
      	$this->add_header($control = new header_date('FECHA_COTIZACION', 'C.FECHA_COTIZACION', 'Fecha'));
	    $control->field_bd_order = 'DATE_COTIZACION';
	    $this->add_header(new header_num('COD_COTIZACION', 'C.COD_COTIZACION', 'Nº Cot.'));
	    $this->add_header(new header_rut('RUT', 'E', 'Rut'));
	      
	    $this->add_header(new header_text('NOM_EMPRESA', 'E.NOM_EMPRESA', 'Razón Social'));
	    $this->add_header(new header_text('REFERENCIA', 'C.REFERENCIA', 'Referencia'));
		$this->add_header(new header_vendedor('INI_USUARIO', 'C.COD_USUARIO_VENDEDOR1', 'Vend'));

	    $this->add_header(new header_num('NRO_FACTURA', 'F.NRO_FACTURA', 'FA'));
	    $this->add_header(new header_num('TOTAL_CON_IVA', 'C.TOTAL_CON_IVA', 'Total con IVA'));		
  	}
}
?>