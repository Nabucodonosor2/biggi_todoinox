<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

/*
Clase : wi_cx_moneda
*/
class wi_cx_moneda extends w_input {
	function wi_cx_moneda($cod_item_menu) {
		parent::w_input('cx_moneda', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica

		$sql = "SELECT	COD_CX_MONEDA
						,NOM_CX_MONEDA
						,NUMERO_DECIMALES 
				FROM 	CX_MONEDA
				WHERE 	COD_CX_MONEDA={KEY1}
				ORDER 	BY COD_CX_MONEDA";
		$this->dws['dw_moneda'] = new datawindow($sql);

		// asigna los formatos				
		$this->dws['dw_moneda']->add_control(new edit_text_upper('NOM_CX_MONEDA', 80, 100));
		$this->dws['dw_moneda']->add_control(new edit_text_upper('NUMERO_DECIMALES', 80, 100));			
		
		// asigna los mandatorys
		$this->dws['dw_moneda']->set_mandatory('COD_CX_MONEDA', 'Código Moneda');	
		$this->dws['dw_moneda']->set_mandatory('NOM_CX_MONEDA', 'Nombre Moneda');	
		$this->dws['dw_moneda']->set_mandatory('NUMERO_DECIMALES', 'Numero Decimales');
	}
	function new_record() {
		$this->dws['dw_moneda']->insert_row();
		$this->dws['dw_moneda']->add_control(new edit_num('COD_CX_MONEDA', 12, 10));	
	}
	function load_record() {
		$cod_moneda = $this->get_item_wo($this->current_record, 'COD_CX_MONEDA');
		$this->dws['dw_moneda']->retrieve($cod_moneda);
	}
	function get_key() {
		return $this->dws['dw_moneda']->get_item(0, 'COD_CX_MONEDA');
	}
	
	function save_record($db) {
		$cod_moneda = $this->get_key();
		$nom_moneda = $this->dws['dw_moneda']->get_item(0, 'NOM_CX_MONEDA');
		$decimales = $this->dws['dw_moneda']->get_item(0, 'NUMERO_DECIMALES');		
		
		$cod_moneda = ($cod_moneda=='') ? "null" : $cod_moneda;		
    
		$sp = 'spu_cx_moneda';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $cod_moneda, '$nom_moneda','$decimales'"; 	
			
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_moneda = $this->get_key();
				$this->dws['dw_moneda']->set_item(0, 'cod_cx_moneda', $cod_moneda);
			}
			return true;
		}
		return false;
	}
}
////////////////////////

?>