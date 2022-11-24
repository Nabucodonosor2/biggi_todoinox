<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

/*
Clase : wi_cx_observacion_tipo
*/
class wi_cx_observacion_tipo extends w_input {
	function wi_cx_observacion_tipo($cod_item_menu) {
		parent::w_input('cx_observacion_tipo', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica

		$sql = "SELECT	COD_CX_OBSERVACION_TIPO
						,NOM_CX_OBSERVACION_TIPO
						,TEXTO
				FROM	CX_OBSERVACION_TIPO
				WHERE 	COD_CX_OBSERVACION_TIPO={KEY1}
				ORDER 	BY COD_CX_OBSERVACION_TIPO";
		
		$this->dws['dw_observacion_tipo'] = new datawindow($sql);

		// asigna los formatos				
		$this->dws['dw_observacion_tipo']->add_control(new edit_text_upper('NOM_CX_OBSERVACION_TIPO', 80, 250));			
		$this->dws['dw_observacion_tipo']->add_control(new edit_text_multiline('TEXTO', 85,2));
		// asigna los mandatorys
		$this->dws['dw_observacion_tipo']->set_mandatory('COD_CX_OBSERVACION_TIPO', 'Código Observacion');	
		$this->dws['dw_observacion_tipo']->set_mandatory('NOM_CX_OBSERVACION_TIPO', 'Observación');	
		$this->dws['dw_observacion_tipo']->set_mandatory('TEXTO', 'Texto');
	}
	function new_record() {
		$this->dws['dw_observacion_tipo']->insert_row();
		$this->dws['dw_observacion_tipo']->add_control(new edit_text('COD_CX_OBSERVACION_TIPO', 12, 10));	
	}
	function load_record() {
		$cod_observacion_tipo = $this->get_item_wo($this->current_record, 'COD_CX_OBSERVACION_TIPO');
		$this->dws['dw_observacion_tipo']->retrieve($cod_observacion_tipo);
	}
	function get_key() {
		return $this->dws['dw_observacion_tipo']->get_item(0, 'COD_CX_OBSERVACION_TIPO');
	}
	function save_record($db) {
		$cod_observacion = $this->get_key();
		$nom_observacion = $this->dws['dw_observacion_tipo']->get_item(0, 'NOM_CX_OBSERVACION_TIPO');	
		$texto	= $this->dws['dw_observacion_tipo']->get_item(0, 'TEXTO');  
		
		$cod_observacion = ($cod_observacion=='') ? "null" : $cod_observacion;		
    
		$sp = 'spu_cx_observacion_tipo';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    	$param	= "'$operacion', $cod_observacion, '$nom_observacion','$texto'"; 	
		
	    if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_observacion = $this->get_key();
				$this->dws['dw_observacion_tipo']->set_item(0, 'COD_CX_OBSERVACION_TIPO', $cod_observacion);
			}
			return true;
		}
		return false;
	}
}
?>