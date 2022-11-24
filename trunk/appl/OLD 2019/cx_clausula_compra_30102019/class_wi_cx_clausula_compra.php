<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

/*
Clase : wi_cx_clausula_compra
*/
class wi_cx_clausula_compra extends w_input {
	function wi_cx_clausula_compra($cod_item_menu) {
		parent::w_input('cx_clausula_compra', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica

		$sql = "SELECT		COD_CX_CLAUSULA_COMPRA
							,NOM_CX_CLAUSULA_COMPRA 
				FROM 		CX_CLAUSULA_COMPRA
				WHERE 		COD_CX_CLAUSULA_COMPRA={KEY1}
				ORDER BY 	COD_CX_CLAUSULA_COMPRA";
		$this->dws['dw_clausula'] = new datawindow($sql);

		// asigna los formatos				
		$this->dws['dw_clausula']->add_control(new edit_text_upper('NOM_CX_CLAUSULA_COMPRA', 80, 100));			
		
		// asigna los mandatorys
		$this->dws['dw_clausula']->set_mandatory('COD_CX_CLAUSULA_COMPRA', 'Código de Clausula');	
		$this->dws['dw_clausula']->set_mandatory('NOM_CX_CLAUSULA_COMPRA', 'Nombre de Clausula');	
		
	}
	function new_record() {
		$this->dws['dw_clausula']->insert_row();
		$this->dws['dw_clausula']->add_control(new edit_num('COD_CX_CLAUSULA_COMPRA', 12, 10));	
	}
	function load_record() {
		$cod_clausula = $this->get_item_wo($this->current_record, 'COD_CX_CLAUSULA_COMPRA');
		$this->dws['dw_clausula']->retrieve($cod_clausula);
	}
	function get_key() {
		return $this->dws['dw_clausula']->get_item(0, 'COD_CX_CLAUSULA_COMPRA');
	}
	
	function save_record($db) {
		$cod_clausula_compra = $this->get_key();
		$nom_clausula_compra = $this->dws['dw_clausula']->get_item(0, 'NOM_CX_CLAUSULA_COMPRA');	
		
		$cod_clausula_compra = ($cod_clausula_compra=='') ? "null" : $cod_clausula_compra;		
    
		$sp = 'spu_cx_clausula_compra';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $cod_clausula_compra, '$nom_clausula_compra'"; 	
			
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_clausula_compra = $this->get_key();
				$this->dws['dw_clausula']->set_item(0, 'COD_CX_CLAUSULA_COMPRA', $cod_clausula_compra);
			}
			return true;
		}
		return false;
	}
}

?>