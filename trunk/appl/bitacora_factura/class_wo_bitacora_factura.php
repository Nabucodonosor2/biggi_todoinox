<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

class wo_bitacora_factura extends w_output {
   function wo_bitacora_factura() {
      	$sql = "select F.COD_FACTURA
						,convert(varchar, B.FECHA_BITACORA_FACTURA, 103) FECHA_BITACORA_FACTURA
						,F.NRO_FACTURA
						,convert(varchar, F.FECHA_FACTURA, 103) FECHA_FACTURA
						,U.INI_USUARIO
						,B.GLOSA
						,B.TIENE_COMPROMISO
						,convert(varchar, B.FECHA_COMPROMISO, 103) FECHA_COMPROMISO
						,B.FECHA_COMPROMISO DATE_COMPROMISO
						,B.GLOSA_COMPROMISO
						,B.COMPROMISO_REALIZADO
						,F.NOM_EMPRESA
				from BITACORA_FACTURA B, FACTURA F, USUARIO U
				where F.COD_FACTURA = B.COD_FACTURA
				  and U.COD_USUARIO = B.COD_USUARIO
				order by DATE_COMPROMISO, F.NRO_FACTURA";
			
      	parent::w_output('bitacora_factura', $sql, $_REQUEST['cod_item_menu']);
      
	    // headers
	    $this->add_header(new header_num('COD_BITACORA_FACTURA', 'B.COD_BITACORA_FACTURA', 'C�d.'));
	    $this->add_header(new header_date('FECHA_BITACORA_FACTURA', 'B.FECHA_BITACORA_FACTURA', 'Fecha'));
	    $this->add_header(new header_num('NRO_FACTURA', 'F.NRO_FACTURA', 'Factura'));
	    $this->add_header(new header_date('FECHA_FACTURA', 'F.FECHA_FACTURA', 'F. FA'));
		$sql = "select distinct U.COD_USUARIO, U.NOM_USUARIO from BITACORA_FACTURA B, USUARIO U where B.COD_USUARIO = U.COD_USUARIO order by NOM_USUARIO";
		$this->add_header(new header_drop_down('INI_USUARIO', 'B.COD_USUARIO', 'Usu.', $sql));
		$this->add_header(new header_text('NOM_EMPRESA', 'F.NOM_EMPRESA', 'Raz�n Social'));

		$this->add_header($compromiso = new header_text('TIENE_COMPROMISO', 'B.TIENE_COMPROMISO', 'C.'));
		$this->add_header(new header_text('GLOSA_COMPROMISO', 'B.GLOSA_COMPROMISO', 'Glosa'));
	    $this->add_header($control = new header_date('FECHA_COMPROMISO', 'B.FECHA_COMPROMISO', 'Fecha'));
	    $control->field_bd_order = 'DATE_BOLETA';
		$this->add_header($realizado = new header_text('COMPROMISO_REALIZADO', 'B.COMPROMISO_REALIZADO', 'R.'));
 
 		// Filtro inicial
		$compromiso->valor_filtro = 'S';
		$realizado->valor_filtro = 'N';
		$this->make_filtros();
   	}
	function detalle_record($rec_no) {
		session::set('DESDE_wo_factura', 'desde output');	// para indicar que viene del output
		session::set('DESDE_wo_bitacora_factura', 'true');
		$ROOT = $this->root_url;
		$url = $ROOT.'appl/factura';
		header ('Location:'.$url.'/wi_factura.php?rec_no='.$rec_no.'&cod_item_menu=1535');
	}
}
?>