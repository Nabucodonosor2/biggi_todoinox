<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

/*
Clase : wi_cx_termino_pago
*/
class wi_cx_termino_pago extends w_input {
	function wi_cx_termino_pago($cod_item_menu) {
		parent::w_input('cx_termino_pago', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica

		$sql = "SELECT	COD_CX_TERMINO_PAGO
						,NOM_CX_TERMINO_PAGO 
				FROM 	CX_TERMINO_PAGO
				WHERE 	COD_CX_TERMINO_PAGO={KEY1}
				ORDER 	BY COD_CX_TERMINO_PAGO";
		$this->dws['dw_termino_pago'] = new datawindow($sql);

		// asigna los formatos				
		$this->dws['dw_termino_pago']->add_control(new edit_text_upper('NOM_CX_TERMINO_PAGO', 120, 100));			
		
		// asigna los mandatorys
		$this->dws['dw_termino_pago']->set_mandatory('COD_CX_TERMINO_PAGO', 'Código');	
		$this->dws['dw_termino_pago']->set_mandatory('NOM_CX_TERMINO_PAGO', 'Nombre Termino Pago');	
	}
	function new_record() {
		$this->dws['dw_termino_pago']->insert_row();
		$this->dws['dw_termino_pago']->add_control(new edit_num('COD_CX_TERMINO_PAGO', 12, 10));	
	}
	function load_record() {
		$cod_termino_pago = $this->get_item_wo($this->current_record, 'COD_CX_TERMINO_PAGO');
		$this->dws['dw_termino_pago']->retrieve($cod_termino_pago);
	}
	function get_key() {
		return $this->dws['dw_termino_pago']->get_item(0, 'COD_CX_TERMINO_PAGO');
	}
	
	function save_record($db) {
		$cod_termino_pago = $this->get_key();
		$nom_termino_pago = $this->dws['dw_termino_pago']->get_item(0, 'NOM_CX_TERMINO_PAGO');	
		
		$cod_termino_pago = ($cod_termino_pago=='') ? "null" : $cod_termino_pago;		
    
		$sp = 'spu_cx_termino_pago';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $cod_termino_pago, '$nom_termino_pago'"; 	
			
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_termino_pago = $this->get_key();
				$this->dws['dw_termino_pago']->set_item(0, 'COD_CX_TERMINO_PAGO', $cod_termino_pago);
			}
			return true;
		}
		return false;
	}
}
////////////////////////

?>