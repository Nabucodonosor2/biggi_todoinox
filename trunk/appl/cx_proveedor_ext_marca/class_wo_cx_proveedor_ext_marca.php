<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_output_biggi.php");

class wo_cx_proveedor_ext_marca extends w_output_biggi{
	function wo_cx_proveedor_ext_marca(){
		
		$sql=  "SELECT CPE.COD_CX_PROVEEDOR_EXT_MARCA 
                    ,PE.COD_PROVEEDOR_EXT
                    ,ALIAS_PROVEEDOR_EXT
                    ,NOM_PROVEEDOR_EXT
                    ,M.NOM_MARCA
                FROM PROVEEDOR_EXT PE
                    ,CX_PROVEEDOR_EXT_MARCA CPE
                    ,MARCA M
                WHERE INCLUIR_REV_STOCK = 'S'
                AND PE.COD_PROVEEDOR_EXT = CPE.COD_PROVEEDOR_EXT
                AND CPE.COD_MARCA = M.COD_MARCA
                ORDER BY COD_PROVEEDOR_EXT DESC";
			
		parent::w_output_biggi('cx_proveedor_ext_marca', $sql, $_REQUEST['cod_item_menu']);
		
		// headers
        $this->add_header(new header_num('COD_PROVEEDOR_EXT', 'COD_PROVEEDOR_EXT', 'Cod'));
        $this->add_header(new header_text('ALIAS_PROVEEDOR_EXT', 'ALIAS_PROVEEDOR_EXT', 'Alias'));
        $this->add_header(new header_text('NOM_PROVEEDOR_EXT', 'NOM_PROVEEDOR_EXT', 'Nombre Proveedor'));
        $this->add_header(new header_text('NOM_MARCA', 'NOM_MARCA', 'Marca'));

		/*$MONTO_PESOS = new static_num('MONTO_TOTAL',2);
		$this->dw->add_control($MONTO_PESOS);
		
		$this->add_header(new header_num('COD_CX_OC_EXTRANJERA', 'COD_CX_OC_EXTRANJERA', 'Code'));
		$this->add_header(new header_text('CORRELATIVO_OC', 'CORRELATIVO_OC', 'Correlative P. Order'));
		$this->add_header($control = new header_date('FECHA_CX_OC_EXTRANJERA', 'CONHVERT(VARCHAR, C.FECHA_CX_OC_EXTRANJERA, 103)', 'Date'));
		$control->field_bd_order = 'DATE_FECHA_CX_OC_EXTRANJERA';
		$this->add_header(new header_text('ALIAS_PROVEEDOR_EXT', 'ALIAS_PROVEEDOR_EXT', 'Alias'));
		$this->add_header(new header_text('NOM_PROVEEDOR_EXT', 'NOM_PROVEEDOR_EXT', 'Provider Name'));
		$this->add_header(new header_num('MONTO_TOTAL', 'MONTO_TOTAL', 'Total'));*/
	}
}
?>