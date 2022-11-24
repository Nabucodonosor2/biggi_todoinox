<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/class_informe_proveedor_ext.php");

class dw_cx_contacto_proveedor_ext extends datawindow {
	function dw_cx_contacto_proveedor_ext() {
		$sql = "SELECT COD_CX_CONTACTO_PROVEEDOR_EXT
					  ,COD_PROVEEDOR_EXT
					  ,NOM_CONTACTO_PROVEEDOR_EXT
					  ,MAIL
					  ,TELEFONO TELEFONO_FIJO
					  ,TELEFONO_MOVIL
					  ,FAX IT_FAX
				FROM CX_CONTACTO_PROVEEDOR_EXT
				WHERE COD_PROVEEDOR_EXT = {KEY1}";
		
		parent::datawindow($sql, 'CX_CONTACTO_PROVEEDOR_EXT', true, true);
		
		$this->add_control(new edit_text_hidden('COD_CX_CONTACTO_PROVEEDOR_EXT'));
		$this->add_control(new edit_text_upper('NOM_CONTACTO_PROVEEDOR_EXT',35, 35));
		$this->add_control(new edit_text('MAIL',25, 25));
		$this->add_control(new edit_text('TELEFONO_FIJO',25, 25));
		$this->add_control(new edit_text('TELEFONO_MOVIL',25, 25));
		$this->add_control(new edit_text('IT_FAX',25, 25));

		// asigna los mandatorys
		$this->set_mandatory('MAIL', 'E-Mail');
		$this->set_mandatory('NOM_CONTACTO_PROVEEDOR_EXT', 'Nombre');
		$this->set_mandatory('TELEFONO_FIJO', 'Teléfono Fijo');
		
	}
	
	function update($db) {
		$sp = 'spu_cx_contacto_proveedor_ext';
		
		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;
				
			$COD_CX_CONTACTO_PROVEEDOR_EXT	= $this->get_item($i, 'COD_CX_CONTACTO_PROVEEDOR_EXT');
			$COD_PROVEEDOR_EXT 				= $this->get_item($i, 'COD_PROVEEDOR_EXT');
			$NOM_CONTACTO_PROVEEDOR_EXT 	= $this->get_item($i, 'NOM_CONTACTO_PROVEEDOR_EXT');
			$MAIL							= $this->get_item($i, 'MAIL');
			$TELEFONO_FIJO 					= $this->get_item($i, 'TELEFONO_FIJO');
			$TELEFONO_MOVIL 				= $this->get_item($i, 'TELEFONO_MOVIL');
			$IT_FAX 						= $this->get_item($i, 'IT_FAX');
			
			$COD_CX_CONTACTO_PROVEEDOR_EXT	= ($COD_CX_CONTACTO_PROVEEDOR_EXT=='') ? "null" : $COD_CX_CONTACTO_PROVEEDOR_EXT;
			$TELEFONO_MOVIL					= ($TELEFONO_MOVIL=='') ? "null" : "'$TELEFONO_MOVIL'";
			$IT_FAX							= ($IT_FAX=='') ? "null" : "'$IT_FAX'";
			
			if ($statuts == K_ROW_NEW_MODIFIED)
				$operacion = 'INSERT';
			else if ($statuts == K_ROW_MODIFIED)
				$operacion = 'UPDATE';		
				
			$param = "'$operacion'
					  ,$COD_CX_CONTACTO_PROVEEDOR_EXT
					  ,$COD_PROVEEDOR_EXT
					  ,'$NOM_CONTACTO_PROVEEDOR_EXT'
					  ,'$MAIL'
					  ,'$TELEFONO_FIJO'
					  ,$TELEFONO_MOVIL
					  ,$IT_FAX";
			
			if (!$db->EXECUTE_SP($sp, $param)) 
				return false;
			else {
				if ($statuts == K_ROW_NEW_MODIFIED) {
					$COD_CX_CONTACTO_PROVEEDOR_EXT = $db->GET_IDENTITY();
					$this->set_item($i, 'COD_CX_CONTACTO_PROVEEDOR_EXT', $COD_CX_CONTACTO_PROVEEDOR_EXT);		
				}
			}
		}
		for ($i = 0; $i < $this->row_count('delete'); $i++) {
			$statuts = $this->get_status_row($i, 'delete');
			if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
				continue;
				
			$COD_CX_CONTACTO_PROVEEDOR_EXT = $this->get_item($i, 'COD_CX_CONTACTO_PROVEEDOR_EXT', 'delete');
			if (!$db->EXECUTE_SP($sp, "'DELETE', $COD_CX_CONTACTO_PROVEEDOR_EXT"))
				return false;
		}	
		return true;
	}
}	

class wi_proveedor_ext extends w_input {
	function wi_proveedor_ext($cod_item_menu) {
		parent::w_input('proveedor_ext', $cod_item_menu);

		$sql = "select COD_PROVEEDOR_EXT,
					   NOM_PROVEEDOR_EXT,
					   ALIAS_PROVEEDOR_EXT,
					   WEB_SITE,
					   DIRECCION,
					   COD_CIUDAD,
					   COD_PAIS,
					   TELEFONO,
					   FAX,
					   POST_OFFICE_BOX,
					   OBS,
					   COD_PROVEEDOR_EXT_4D,
					   NOM_CIUDAD_4D,
					   NOM_PAIS_4D,
					   COD_PROVEEDOR_EXT COD_PROVEEDOR_EXT_L,
					   ALIAS_PROVEEDOR_EXT ALIAS_PROVEEDOR_EXT_L,
					   BENEFICIARY_DIRBANK,
					   BENEFICIARY_NAMEBANK,
					   BENEFICIARY_NAMEEMP,
					   BENEFICIARY_DIREMP,
					   BP_ACCOUNT_NUMBER,
					   BP_SWIFT,
					   BP_IBAN,
					   BP_ABI,
					   BP_CAB,
					   BP_CBU
				from PROVEEDOR_EXT
				where COD_PROVEEDOR_EXT = {KEY1}";
						
		$this->dws['wi_proveedor_ext'] = new datawindow($sql);
		$this->dws['dw_cx_contacto_proveedor_ext'] = new dw_cx_contacto_proveedor_ext();

		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('ALIAS_PROVEEDOR_EXT', 30, 80));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('NOM_PROVEEDOR_EXT', 80, 80));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('DIRECCION', 80, 80));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('NOM_PAIS_4D', 30, 80));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('NOM_CIUDAD_4D', 30, 80));	
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('TELEFONO', 21, 80));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('FAX', 21, 80));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('WEB_SITE', 43, 80));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('POST_OFFICE_BOX', 43, 80));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_multiline('OBS',54,4));
		
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_multiline('BENEFICIARY_NAMEEMP',85,2));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_multiline('BENEFICIARY_DIREMP',85,2));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_multiline('BENEFICIARY_NAMEBANK',85,2));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_multiline('BENEFICIARY_DIRBANK',85,2));
		
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('BP_ACCOUNT_NUMBER', 43, 80));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('BP_SWIFT', 43, 80));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('BP_IBAN', 43, 80));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('BP_ABI', 43, 80));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('BP_CAB', 43, 80));
		$this->dws['wi_proveedor_ext']->add_control(new edit_text_upper('BP_CBU', 43, 80));
		
		//asigna los mandatorys
		$this->dws['wi_proveedor_ext']->set_mandatory('COD_PROVEEDOR_EXT', 'Código');
		$this->dws['wi_proveedor_ext']->set_mandatory('NOM_PROVEEDOR_EXT', 'Proveedor_ext');
			
	}
	function new_record() {
		$this->dws['wi_proveedor_ext']->insert_row();
	}
	function load_record() {
		$cod_proveedor_ext = $this->get_item_wo($this->current_record, 'COD_PROVEEDOR_EXT');
		$this->dws['wi_proveedor_ext']->retrieve($cod_proveedor_ext);
		$this->dws['dw_cx_contacto_proveedor_ext']->retrieve($cod_proveedor_ext);
	}
	function get_key() {
		return $this->dws['wi_proveedor_ext']->get_item(0, 'COD_PROVEEDOR_EXT');
	}
	
	function print_record(){

		$sql = "SELECT COD_PROVEEDOR_EXT,
					   NOM_PROVEEDOR_EXT,
					   ALIAS_PROVEEDOR_EXT,
					   WEB_SITE,
					   DIRECCION,
					   TELEFONO,
					   FAX,
					   POST_OFFICE_BOX,
					   OBS,
					   COD_PROVEEDOR_EXT_4D,
					   NOM_CIUDAD_4D,
					   NOM_PAIS_4D,
					   BENEFICIARY_DIRBANK,
					   BENEFICIARY_NAMEBANK,
					   BENEFICIARY_NAMEEMP,
					   BENEFICIARY_DIREMP,
					   BP_ACCOUNT_NUMBER,
					   BP_SWIFT,
					   BP_IBAN,
					   BP_ABI,
					   BP_CAB,
					   BP_CBU
				FROM PROVEEDOR_EXT
				WHERE COD_PROVEEDOR_EXT = ".$this->get_key();

		$file_name = $this->find_file('proveedor_ext', 'proveedor_ext.xml');
		$rpt = new informe_proveedor_ext($sql, $file_name, $labels, "Proveedor EXT.pdf", 1);												
		$this->_load_record();
	}
	
	function save_record($db) {
		$COD_PROVEEDOR_EXT 		= $this->get_key();
		$NOM_PROVEEDOR_EXT 		= $this->dws['wi_proveedor_ext']->get_item(0, 'NOM_PROVEEDOR_EXT');
		$ALIAS_PROVEEDOR_EXT 	= $this->dws['wi_proveedor_ext']->get_item(0, 'ALIAS_PROVEEDOR_EXT');
		$WEB_SITE 				= $this->dws['wi_proveedor_ext']->get_item(0, 'WEB_SITE');
		$DIRECCION 				= $this->dws['wi_proveedor_ext']->get_item(0, 'DIRECCION');
		$TELEFONO 				= $this->dws['wi_proveedor_ext']->get_item(0, 'TELEFONO');
		$FAX 					= $this->dws['wi_proveedor_ext']->get_item(0, 'FAX');
		$POST_OFFICE_BOX 		= $this->dws['wi_proveedor_ext']->get_item(0, 'POST_OFFICE_BOX');
		$OBS 					= $this->dws['wi_proveedor_ext']->get_item(0, 'OBS');
		$COD_PROVEEDOR_EXT_4D	= 'N';
		$NOM_CIUDAD 			= $this->dws['wi_proveedor_ext']->get_item(0, 'NOM_CIUDAD_4D');
		$NOM_PAIS 				= $this->dws['wi_proveedor_ext']->get_item(0, 'NOM_PAIS_4D');
		$BENEFICIARY_DIRBANK 	= $this->dws['wi_proveedor_ext']->get_item(0, 'BENEFICIARY_DIRBANK');
		$BENEFICIARY_NAMEBANK 	= $this->dws['wi_proveedor_ext']->get_item(0, 'BENEFICIARY_NAMEBANK');
		$BENEFICIARY_NAMEEMP 	= $this->dws['wi_proveedor_ext']->get_item(0, 'BENEFICIARY_NAMEEMP');
		$BENEFICIARY_DIREMP 	= $this->dws['wi_proveedor_ext']->get_item(0, 'BENEFICIARY_DIREMP');
		$BP_ACCOUNT_NUMBER 		= $this->dws['wi_proveedor_ext']->get_item(0, 'BP_ACCOUNT_NUMBER');
		$BP_SWIFT 				= $this->dws['wi_proveedor_ext']->get_item(0, 'BP_SWIFT');
		$BP_IBAN 				= $this->dws['wi_proveedor_ext']->get_item(0, 'BP_IBAN');
		$BP_ABI 				= $this->dws['wi_proveedor_ext']->get_item(0, 'BP_ABI');
		$BP_CAB 				= $this->dws['wi_proveedor_ext']->get_item(0, 'BP_CAB');
		$BP_CBU 				= $this->dws['wi_proveedor_ext']->get_item(0, 'BP_CBU');
		
		$COD_PROVEEDOR_EXT 		= ($COD_PROVEEDOR_EXT=='') ? "null" : $COD_PROVEEDOR_EXT;
		$NOM_PROVEEDOR_EXT 		= ($NOM_PROVEEDOR_EXT=='') ? "null" : "'$NOM_PROVEEDOR_EXT'";
		$ALIAS_PROVEEDOR_EXT 	= ($ALIAS_PROVEEDOR_EXT=='') ? "null" : "'$ALIAS_PROVEEDOR_EXT'";
		$WEB_SITE 				= ($WEB_SITE=='') ? "null" : "'$WEB_SITE'";
		$DIRECCION 				= ($DIRECCION=='') ? "null" : "'$DIRECCION'";
		$TELEFONO 				= ($TELEFONO=='') ? "null" : "'$TELEFONO'";
		$FAX 					= ($FAX=='') ? "null" : "'$FAX'";
		$POST_OFFICE_BOX 		= ($POST_OFFICE_BOX=='') ? "null" : "'$POST_OFFICE_BOX'";
		$OBS 					= ($OBS=='') ? "null" : "'$OBS'";
		$NOM_CIUDAD 			= ($NOM_CIUDAD=='') ? "null" : "'$NOM_CIUDAD'";
		$NOM_PAIS 				= ($NOM_PAIS=='') ? "null" : "'$NOM_PAIS'";
		$BENEFICIARY_DIRBANK 	= ($BENEFICIARY_DIRBANK=='') ? "null" : "'$BENEFICIARY_DIRBANK'";
		$BENEFICIARY_NAMEBANK 	= ($BENEFICIARY_NAMEBANK=='') ? "null" : "'$BENEFICIARY_NAMEBANK'";
		$BENEFICIARY_NAMEEMP 	= ($BENEFICIARY_NAMEEMP=='') ? "null" : "'$BENEFICIARY_NAMEEMP'";
		$BENEFICIARY_DIREMP 	= ($BENEFICIARY_DIREMP=='') ? "null" : "'$BENEFICIARY_DIREMP'";
		$BP_ACCOUNT_NUMBER 		= ($BP_ACCOUNT_NUMBER=='') ? "null" : "'$BP_ACCOUNT_NUMBER'";
		$BP_SWIFT 				= ($BP_SWIFT=='') ? "null" : "'$BP_SWIFT'";
		$BP_IBAN 				= ($BP_IBAN=='') ? "null" : "'$BP_IBAN'";
		$BP_ABI 				= ($BP_ABI=='') ? "null" : "'$BP_ABI'";
		$BP_CAB 				= ($BP_CAB=='') ? "null" : "'$BP_CAB'";
		$BP_CBU 				= ($BP_CBU=='') ? "null" : "'$BP_CBU'";
		
			$sp = 'spu_proveedor_ext';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion'
	    		  ,$COD_PROVEEDOR_EXT
	    		  ,$NOM_PROVEEDOR_EXT
	    		  ,$ALIAS_PROVEEDOR_EXT
	    		  ,$WEB_SITE
	    		  ,$DIRECCION
	    		  ,NULL
	    		  ,NULL
	    		  ,$TELEFONO
	    		  ,$FAX
	    		  ,$POST_OFFICE_BOX
	    		  ,$OBS
	    		  ,'$COD_PROVEEDOR_EXT_4D'
	    		  ,$NOM_CIUDAD
	    		  ,$NOM_PAIS
	    		  ,$BENEFICIARY_DIRBANK
				  ,$BENEFICIARY_NAMEBANK
				  ,$BENEFICIARY_NAMEEMP
				  ,$BENEFICIARY_DIREMP
				  ,$BP_ACCOUNT_NUMBER
				  ,$BP_SWIFT
				  ,$BP_IBAN
				  ,$BP_ABI
				  ,$BP_CAB
				  ,$BP_CBU";
	    		  
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_PROVEEDOR_EXT = $db->GET_IDENTITY();
				$this->dws['wi_proveedor_ext']->set_item(0, 'COD_PROVEEDOR_EXT', $COD_PROVEEDOR_EXT);				
			}
			
			for ($i=0; $i<$this->dws['dw_cx_contacto_proveedor_ext']->row_count(); $i++)
				$this->dws['dw_cx_contacto_proveedor_ext']->set_item($i, 'COD_PROVEEDOR_EXT', $COD_PROVEEDOR_EXT);

			if (!$this->dws['dw_cx_contacto_proveedor_ext']->update($db))
				return false;
			
			return true;
		}
		return false;		
	}
}
?>