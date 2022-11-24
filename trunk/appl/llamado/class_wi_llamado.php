<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../biggi/trunk/appl/llamado/envio_mail/funciones.php");


class dw_llamado extends datawindow {
	function dw_llamado() {		
		$sql = "SELECT LL.COD_LLAMADO
						,CONVERT (VARCHAR(10), LL.FECHA_LLAMADO, 103)FECHA_LLAMADO
						,U.NOM_USUARIO
						,LL.COD_USUARIO
						,LL.COD_CONTACTO
						,LL.COD_CONTACTO_PERSONA
						,C.NOM_CONTACTO
						,C.RUT
						,C.DIG_VERIF
						,C.DIRECCION
						,CI.NOM_CIUDAD
						,CO.NOM_COMUNA
						,CP.NOM_PERSONA
						,'' DD_NOM_PERSONA
						,CP.CARGO
						,CP.CARGO CARGO_H 
						,LL.MENSAJE
						,LL.COD_LLAMADO_ACCION
						,LLA.NOM_LLAMADO_ACCION
						,LL.LLAMAR_TELEFONO
						,LL.REALIZADO
						,CONVERT(VARCHAR(10),LL.FECHA_REALIZADO,103)+'  '+CONVERT(VARCHAR(5),LL.FECHA_REALIZADO,108) FECHA_REALIZADO
						,LL.GLOSA_REALIZADO
						,LL.TIPO_DOC_REALIZADO
						,LL.COD_DOC_REALIZADO
						,'' VISIBLE_TXT_PERSONA
						,'none' VISIBLE_DD_PERSONA
						,'none' VISIBLE_CREAR
						,'' VISIBLE_NO_CREAR
						,dbo.f_llamado_telefono(LL.COD_CONTACTO, 'EMPRESA') TELEFONO_CONTACTO
						,dbo.f_llamado_telefono(LL.COD_CONTACTO_PERSONA, 'PERSONA') TELEFONO_PERSONA
						,'' DD_TELEFONO_CONTACTO
						,'' DD_TELEFONO_PERSONA
					FROM LLAMADO LL
						,USUARIO U
						,CONTACTO C LEFT OUTER JOIN CIUDAD CI ON CI.COD_CIUDAD = C.COD_CIUDAD
								LEFT OUTER JOIN COMUNA CO ON CO.COD_COMUNA = C.COD_COMUNA
						,CONTACTO_PERSONA CP
						,LLAMADO_ACCION LLA
				   WHERE LL.COD_LLAMADO = {KEY1}
					 AND U.COD_USUARIO = LL.COD_USUARIO
					 AND C.COD_CONTACTO = LL.COD_CONTACTO
					 AND CP.COD_CONTACTO_PERSONA = LL.COD_CONTACTO_PERSONA
					 AND LLA.COD_LLAMADO_ACCION = LL.COD_LLAMADO_ACCION";

		parent::datawindow($sql);
		
		$this->add_control(new static_num('COD_LLAMADO'));
		$this->add_control(new static_text('NOM_USUARIO'));
		$this->add_control(new edit_text('COD_USUARIO',10,10, 'hidden'));
		$this->add_control(new edit_text('NOM_LLAMADO_ACCION',10,10, 'hidden'));
		$this->add_control(new static_text('FECHA_LLAMADO'));
		
		$this->add_control(new edit_text('COD_CONTACTO',10,10, 'hidden'));
		$this->add_control($control = new edit_text_upper('NOM_CONTACTO',70, 100));
		$control->set_onChange("help_contacto(this);");
		
		$this->add_control($control = new edit_rut('RUT'));
		$control->set_onChange("help_contacto(this);");
		
		$this->add_control(new static_text('DIG_VERIF'));
		$this->add_control(new static_text('DIRECCION'));
		$this->add_control(new static_text('NOM_CIUDAD'));
		$this->add_control(new static_text('NOM_COMUNA'));
		
		$this->add_control(new static_text('TELEFONO_CONTACTO'));
		
		$this->add_control(new edit_text('COD_CONTACTO_PERSONA',10,10, 'hidden'));
		
		
		$this->add_control($control = new edit_text_upper('NOM_PERSONA',70, 100));
		$control->set_onChange("help_contacto(this);");
		
		$sql = "select 0, ''";
		$this->add_control($control = new drop_down_dw('DD_NOM_PERSONA', $sql, 200));
		$control->set_onChange("select_dd_persona(this);");
		
		$this->add_control(new drop_down_dw('DD_TELEFONO_CONTACTO', $sql, 200));
		$this->add_control(new drop_down_dw('DD_TELEFONO_PERSONA', $sql, 200));
		
		$this->add_control(new static_text('CARGO'));
		$this->add_control(new edit_text('CARGO_H',10,10, 'hidden'));
		
		$this->add_control(new static_text('TELEFONO_PERSONA'));
		
		$this->add_control(new edit_text_multiline('MENSAJE', 70, 6));
		$this->add_control(new edit_text_multiline('LLAMAR_TELEFONO', 10, 6));
		
		$sql_accion = "select COD_LLAMADO_ACCION, 
							  NOM_LLAMADO_ACCION
						 from LLAMADO_ACCION
					 order by COD_LLAMADO_ACCION";
		$this->add_control(new drop_down_dw('COD_LLAMADO_ACCION', $sql_accion, 100));
		//$this->add_control(new edit_text_upper('LLAMAR_TELEFONO',20, 100));
		$this->add_control($control = new edit_check_box('REALIZADO', 'S', 'N'));
		$control->set_onChange("realizado(this);");
				
		$this->add_control(new static_text('FECHA_REALIZADO'));
		
		$this->add_control(new edit_text_upper('GLOSA_REALIZADO',100, 100));
		$this->add_control(new edit_text_upper('TIPO_DOC_REALIZADO',20, 100));
		$this->add_control(new drop_down_list('TIPO_DOC_REALIZADO',array('','COTIZACION','NOTA VENTA','GUIA DESPACHO','FACTURA'),array('','COTIZACION','NOTA VENTA','GUIA DESPACHO','FACTURA'),150));
		$this->add_control(new edit_num('COD_DOC_REALIZADO', 10,10));
		
		$this->set_mandatory('NOM_CONTACTO', 'Razón Social');
		$this->set_mandatory('MENSAJE', 'Mensaje');
		$this->set_mandatory('COD_LLAMADO_ACCION', 'Acción');
		$this->set_mandatory('LLAMAR_TELEFONO', 'Llamar al');
		
	}	
}
class radio_responsable extends edit_radio_button {	
	function radio_responsable($field) {
		parent::edit_radio_button($field, 'S', 'N', '', $field);
	}
	function draw_entrable($dato, $record) {
		$value_true = $this->value_true;
		$this->value_true = $this->value_true.'_'.$record;
		$dato = $dato.'_'.$record;
		$draw = parent::draw_entrable($dato, $record);
		$this->value_true = $value_true;
		return $draw;
	}		
	function get_values_from_POST($record) {
		$value_true = $this->value_true.'_'.$record;
		$field_post = $this->group;
		if (isset($_POST[$field_post]) && $_POST[$field_post]==$value_true)
			return $this->value_true;
		else 
			return $this->value_false;
	}
}
class dw_destinatario extends datawindow {
	
	//************ definir constantes  y usarlas
    const K_FIJO1 = 2;	//A.Scianca
	const K_FIJO2 = 3;	//J.Jofre
	const K_FIJO3 = 4;	//S.Pechoante
	const K_FIJO4 = 15;	//A.Montecino
	const K_FIJO5 = 26;	//A.Huenante
	const K_FIJO6 = 55;	//M.Correa
 	const K_FIJO7 = 54;	//E.Orozco
 	
	function dw_destinatario() {		

		
		$sql = "SELECT  COD_LLAMADO_DESTINATARIO  
					    ,COD_LLAMADO               
					    ,COD_DESTINATARIO          
					    ,RESPONSABLE   
						,'' TITULO_CORREO            
				  from LLAMADO_DESTINATARIO
				 where COD_LLAMADO = {KEY1}
				   and COD_DESTINATARIO not in (".self::K_FIJO2.",".self::K_FIJO3.",".self::K_FIJO4.",".self::K_FIJO5.",".self::K_FIJO6.",".self::K_FIJO7.")
			  order by COD_LLAMADO_DESTINATARIO";

				
		parent::datawindow($sql, 'DESTINATARIO', true, true);
		
		$sql = "select COD_DESTINATARIO
						,NOM_DESTINATARIO
				  from DESTINATARIO
				  where cod_destinatario <> 2
			  order by NOM_DESTINATARIO";
		$this->add_control(new drop_down_dw('COD_DESTINATARIO',$sql,150));
		$this->add_control(new radio_responsable('RESPONSABLE'));

		$this->set_mandatory('COD_DESTINATARIO', 'Destinatario');
		
	}	
	function update($db, $cod_llamado) {

		$sp = 'spu_llamado_destinatario';	
		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;			
	      	$cod_llamado_destinatario = $this->get_item($i, 'COD_LLAMADO_DESTINATARIO');
	      	$cod_destinatario         = $this->get_item($i, 'COD_DESTINATARIO');
	      	$responsable      		  = $this->get_item($i, 'RESPONSABLE');
	       	$cod_llamado_destinatario	= ($cod_llamado_destinatario=='') ? "null" : $cod_llamado_destinatario;
			
			if ($statuts == K_ROW_NEW_MODIFIED)
				$operacion = 'INSERT';
			elseif ($statuts == K_ROW_MODIFIED)
				$operacion = 'UPDATE';
				
		    $param = "'$operacion'
	    				,$cod_llamado_destinatario  
	    				,$cod_llamado
	    				,$cod_destinatario           
	    				,'$responsable'";

			if (!$db->EXECUTE_SP($sp, $param))
				return false;		
		}
		for ($i = 0; $i < $this->row_count('delete'); $i++) {
			$statuts = $this->get_status_row($i, 'delete');			
			if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
				continue;
			
			$cod_llamado_destinatario = $this->get_item($i, 'COD_LLAMADO_DESTINATARIO', 'delete');
			if (!$db->EXECUTE_SP($sp, "'DELETE', $cod_llamado_destinatario"))
				return false;		
		}			
		return true;
	}
}

class dw_destinatario_fijo extends datawindow {
	
	//************ definir constantes  y usarlas
    const K_FIJO1 = 2;	//A.Scianca
	const K_FIJO2 = 3;	//J.Jofre
	const K_FIJO3 = 4;	//S.Pechoante
	const K_FIJO4 = 15;	//A.Montecino
	const K_FIJO5 = 26;	//A.Huenante
	const K_FIJO6 = 55;	//M.Correa
 	const K_FIJO7 = 54;	//E.Orozco
	
	function dw_destinatario_fijo() {
	
		$sql = "select 'S' SELECCION
						,LD.COD_LLAMADO COD_LLAMADO_FIJO
						,LD.RESPONSABLE RESPONSABLE_FIJO
						,LD.COD_LLAMADO_DESTINATARIO COD_LLAMADO_DESTINATARIO_FIJO
						,LD.COD_DESTINATARIO COD_DESTINATARIO_FIJO
						,NOM_DESTINATARIO NOM_DESTINATARIO_FIJO
				 FROM LLAMADO_DESTINATARIO LD, DESTINATARIO D
				 where LD.COD_LLAMADO = {KEY1}
				 And LD.COD_DESTINATARIO in (".self::K_FIJO2.",".self::K_FIJO3.",".self::K_FIJO4.",".self::K_FIJO5.",".self::K_FIJO6.",".self::K_FIJO7.")
				 and D.COD_DESTINATARIO = LD.COD_DESTINATARIO
				 union
				 select 'N' SELECCION
						, NULL COD_LLAMADO_FIJO
						,null RESPONSABLE_FIJO
						,null COD_LLAMADO_DESTINATARIO_FIJO
						,COD_DESTINATARIO COD_DESTINATARIO_FIJO
						,NOM_DESTINATARIO NOM_DESTINATARIO_FIJO
				 FROM DESTINATARIO
				 where COD_DESTINATARIO in (".self::K_FIJO2.",".self::K_FIJO3.",".self::K_FIJO4.",".self::K_FIJO5.",".self::K_FIJO6.",".self::K_FIJO7.")
				 and COD_DESTINATARIO not in (select COD_DESTINATARIO  from LLAMADO_DESTINATARIO where COD_LLAMADO = {KEY1})
				 order by COD_DESTINATARIO_FIJO";
		
		parent::datawindow($sql, 'DESTINATARIO_FIJO', false, false);

		$this->add_control(new edit_check_box('SELECCION', 'S', 'N'));
		
		
	}
	//*********** function update($db) => debe insertar o eliminar en llamado destinatario
	function update($db, $cod_llamado) {
		
		$sp = 'spu_llamado_destinatario';	
		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;		
			
	      	$cod_llamado_destinatario_fijo  = $this->get_item($i, 'COD_LLAMADO_DESTINATARIO_FIJO');
	      	$cod_destinatario_fijo          = $this->get_item($i, 'COD_DESTINATARIO_FIJO');
	      	$responsable_fijo    		    = $this->get_item($i, 'RESPONSABLE_FIJO');
			
	      	
	      	$cod_llamado_destinatario_fijo	= ($cod_llamado_destinatario_fijo=='') ? "null" : $cod_llamado_destinatario_fijo;
	        $cod_destinatario_fijo	= ($cod_destinatario_fijo=='') ? "null" : $cod_destinatario_fijo;
	        $responsable_fijo	= ($responsable_fijo=='') ? "null" : $responsable_fijo;
	       
	        $seleccion    		    = $this->get_item($i, 'SELECCION');
	        
			if ($seleccion == 'S'){
		    	$param = "'INSERT'
	    				,$cod_llamado_destinatario_fijo  
	    				,$cod_llamado
	    				,$cod_destinatario_fijo           
	    				,'N'";
	
				if (!$db->EXECUTE_SP($sp, $param))
					return false;
			}		
		}
		for ($i = 0; $i < $this->row_count('delete'); $i++) {
			$statuts = $this->get_status_row($i, 'delete');			
			if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
				continue;
			
			$cod_llamado_destinatario_fijo = $this->get_item($i, 'COD_LLAMADO_DESTINATARIO_FIJO', 'delete');
			if (!$db->EXECUTE_SP($sp, "'DELETE', $cod_llamado_destinatario_fijo"))
				return false;		
		}			
		return true;
	
	}
	
}	

class dw_conversacion extends datawindow {
	function dw_conversacion() {		
		$sql = "SELECT COD_LLAMADO_CONVERSA
					   ,CONVERT(VARCHAR(10),FECHA_LLAMADO_CONVERSA,103)+'  '+CONVERT(VARCHAR(5),FECHA_LLAMADO_CONVERSA,108) FECHA_LLAMADO_CONVERSA
					   ,COD_DESTINATARIO COD_DESTINATARIO_CONV
					   ,GLOSA
					   ,REALIZADO REALIZADO_CONV
					   ,'N' IS_NEW
				  FROM LLAMADO_CONVERSA LL
				 WHERE COD_LLAMADO = {KEY1}
		      ORDER BY COD_LLAMADO_CONVERSA ASC";				
		
		parent::datawindow($sql, 'CONVERSACION', true, true);
		
		$sql = "select COD_DESTINATARIO
					   ,NOM_DESTINATARIO
				  from DESTINATARIO
			  order by NOM_DESTINATARIO";
		$this->add_control(new drop_down_dw('COD_DESTINATARIO_CONV',$sql,160));
		
		$this->add_control(new edit_text_upper('GLOSA',110, 100));
		$this->add_control($control = new edit_check_box('REALIZADO_CONV', 'S', 'N'));
		$control->set_onChange("realizado_conv(this);");
		
		$this->set_mandatory('GLOSA', 'Glosa Conversación');
		$this->set_mandatory('COD_DESTINATARIO_CONV', 'Usuario Conversación');
		
		$this->set_protect('GLOSA', "[IS_NEW]=='N'");
		$this->set_protect('REALIZADO_CONV', "[IS_NEW]=='N'");
		$this->set_protect('COD_DESTINATARIO_CONV', "[IS_NEW]=='N'");
		
	}
	function insert_row($row=-1) {
		$row = parent::insert_row($row);
		$this->set_item($row, 'IS_NEW', 'S');
		
		$sql = "SELECT CONVERT(VARCHAR(10),GETDATE(),103)+'  '+CONVERT(VARCHAR(5),GETDATE(),108)  FECHA_LLAMADO_CONVERSA";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($sql);
		$this->set_item($row, 'FECHA_LLAMADO_CONVERSA', $result[0][0]);
		
		return $row;
	}
	function fill_record(&$temp, $record) {
		parent::fill_record($temp, $record);
		$is_new = $this->get_item($record, 'IS_NEW');
		if ($is_new=='N') {
			$eliminar = '';
			$temp->setVar($this->label_record.".ELIMINAR_".strtoupper($this->label_record), $eliminar);
		}
	}
	function fill_template(&$temp) {
		parent::fill_template($temp);
		if ($this->entrable) {
			$agregar = '<img src="../../../../commonlib/trunk/images/b_add_line.jpg" onClick="add_line_conversacion(\''.$this->label_record.'\', \''.$this->nom_tabla.'\','.$this->cod_usuario.');" style="cursor:pointer">';
			$temp->setVar("AGREGAR_".strtoupper($this->label_record), $agregar);
		}
	}
		function update($db, $cod_llamado) {
		$sp = 'spu_llamado_conversa';	
		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;			
	      	$cod_llamado_conversa 	= $this->get_item($i, 'COD_LLAMADO_CONVERSA');
	      	$cod_destinatario		= $this->get_item($i, 'COD_DESTINATARIO_CONV');
	      	$glosa					= $this->get_item($i, 'GLOSA');
	      	$realizado				= $this->get_item($i, 'REALIZADO_CONV');
	     
	       	$cod_llamado_conversa	= ($cod_llamado_conversa=='') ? "null" : $cod_llamado_conversa;
			
			if ($statuts == K_ROW_NEW_MODIFIED)
				$operacion = 'INSERT';
			elseif ($statuts == K_ROW_MODIFIED)
				$operacion = 'UPDATE';
				
		    $param = "'$operacion'
	    				,$cod_llamado_conversa
	    				,$cod_llamado
	    				,$cod_destinatario
	    				,'$glosa'
	    				,'$realizado'
	    				,'N'";//$desde_mail

			if (!$db->EXECUTE_SP($sp, $param))
				return false;		
		}
		for ($i = 0; $i < $this->row_count('delete'); $i++) {
			$statuts = $this->get_status_row($i, 'delete');			
			if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
				continue;
			
			$cod_llamado_conversa = $this->get_item($i, 'COD_LLAMADO_CONVERSA', 'delete');
			if (!$db->EXECUTE_SP($sp, "'DELETE', $cod_llamado_conversa"))
				return false;		
		}			
		return true;
	}
}
		
class wi_llamado extends w_input {
	

	function wi_llamado($cod_item_menu) {
		parent::w_input('llamado', $cod_item_menu);

		$this->dws['dw_llamado'] = new dw_llamado();
		$this->dws['dw_destinatario'] = new dw_destinatario();
		$this->dws['dw_conversacion'] = new dw_conversacion();
		$this->dws['dw_destinatario_fijo'] = new dw_destinatario_fijo();
	}
	function new_record() {
		$this->dws['dw_llamado']->insert_row();
		$this->dws['dw_llamado']->set_item(0, 'FECHA_LLAMADO', $this->current_date());
        $this->dws['dw_llamado']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
        $this->dws['dw_llamado']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
        $this->dws['dw_llamado']->set_item(0, 'VISIBLE_TXT_PERSONA', '');
        $this->dws['dw_llamado']->set_item(0, 'VISIBLE_DD_PERSONA', 'none');
        $this->dws['dw_llamado']->set_item(0, 'VISIBLE_CREAR', '');
        $this->dws['dw_llamado']->set_item(0, 'VISIBLE_NO_CREAR', 'none');
        
		$this->dws['dw_destinatario_fijo']->retrieve(0);
		 for ($i=0; $i<$this->dws['dw_destinatario_fijo']->row_count();$i++)
          $this->dws['dw_destinatario_fijo']->set_item($i,'SELECCION', 'S');
		
	}
	function load_record() {
		$cod_llamado = $this->get_item_wo($this->current_record, 'COD_LLAMADO');
		$this->dws['dw_llamado']->retrieve($cod_llamado);
		$this->dws['dw_destinatario']->retrieve($cod_llamado);
		$this->dws['dw_conversacion']->retrieve($cod_llamado);
		$this->dws['dw_destinatario_fijo']->retrieve($cod_llamado);
		
		$this->dws['dw_llamado']->set_entrable('NOM_CONTACTO', false);
		$this->dws['dw_llamado']->set_entrable('RUT', false);
		$this->dws['dw_llamado']->set_entrable('NOM_PERSONA', false);
		$this->dws['dw_llamado']->set_entrable('MENSAJE', false);
		$this->dws['dw_llamado']->set_entrable('COD_LLAMADO_ACCION', false);
		$this->dws['dw_llamado']->set_entrable('LLAMAR_TELEFONO', false);
		$this->dws['dw_llamado']->set_entrable('FECHA_REALIZADO', false);
		
		$this->dws['dw_destinatario']->set_entrable_dw(false);
		$this->dws['dw_destinatario_fijo']->set_entrable_dw(false);
		
		
        $this->dws['dw_llamado']->set_item(0, 'VISIBLE_TXT_PERSONA', '');
        $this->dws['dw_llamado']->set_item(0, 'VISIBLE_DD_PERSONA', 'none');
        $this->dws['dw_llamado']->set_item(0, 'VISIBLE_CREAR', 'none');
        $this->dws['dw_llamado']->set_item(0, 'VISIBLE_NO_CREAR', '');
        
        $realizado = $this->dws['dw_llamado']->get_item(0, 'REALIZADO');
        if ($realizado == 'S'){
			$this->b_no_save_visible = false;
			$this->b_save_visible 	 = false;
			$this->b_modify_visible  = false;
			
        }
        
                   
	}
	function get_key() {
		return $this->dws['dw_llamado']->get_item(0, 'COD_LLAMADO');
	}
	
	function envio_mail(){
		require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/clases/class_PHPMailer.php");	
					
		$cod_llamado 			= $this->dws['dw_llamado']->get_item(0, 'COD_LLAMADO');
		$cargo					= $this->dws['dw_llamado']->get_item(0, 'CARGO_H');
		$nom_contacto			= $this->dws['dw_llamado']->get_item(0, 'NOM_CONTACTO');
	    $nom_persona 			= $this->dws['dw_llamado']->get_item(0, 'NOM_PERSONA');
	    $mensaje				= $this->dws['dw_llamado']->get_item(0, 'MENSAJE');
	    $cod_llamado_accion		= $this->dws['dw_llamado']->get_item(0, 'COD_LLAMADO_ACCION');
	    $llamar_telefono		= $this->dws['dw_llamado']->get_item(0, 'LLAMAR_TELEFONO');

	    $cod_llamado_enc = encriptar_url($cod_llamado, 'envio_mail_llamado');

	    $link = "http://190.196.2.10/sysbiggi/comercial_biggi/biggi/trunk/appl/llamado/envio_mail/formulario.php?";
		//$link = "http://192.168.2.13/desarrolladores/jmino/biggi/trunk/appl/llamado/envio_mail/formulario.php?";
		//$link = "http://201.238.210.133/sysbiggi/envio_mail/biggi/trunk/appl/llamado/envio_mail/formulario.php?";

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		$sql = "select dbo.f_get_parametro(53)         URL_SMTP
		                        ,dbo.f_get_parametro(54)      USER_SMTP
		                        ,dbo.f_get_parametro(55)      PASS_SMTP
		                        ,dbo.f_get_parametro(71)      PORT_SMTP";
		
		$result = $db->build_results($sql);
		$URL_SMTP   = $result[0]['URL_SMTP'];
		$USER_SMTP  = $result[0]['USER_SMTP'];
		$PASS_SMTP  = $result[0]['PASS_SMTP'];
		$PORT_SMTP  = $result[0]['PORT_SMTP'];
 		
 	$sql_accion ="SELECT C.RUT,C.DIG_VERIF ,C.DIRECCION,CP.MAIL,E.GIRO		 
				    FROM CONTACTO C LEFT OUTER JOIN EMPRESA E ON C.COD_EMPRESA = E.COD_EMPRESA,
				       		LLAMADO LL, LLAMADO_ACCION LLA, CONTACTO_PERSONA CP 
				   WHERE LL.COD_LLAMADO = $cod_llamado
				     AND LL.COD_LLAMADO_ACCION = LLA.COD_LLAMADO_ACCION
				     AND C.COD_CONTACTO = LL.COD_CONTACTO
				    AND CP.COD_CONTACTO_PERSONA = LL.COD_CONTACTO_PERSONA";
				    
	$result_accion = $db->build_results($sql_accion);					
	// nuevos datos 
	$rut_emp = $result_accion[0]['RUT'];
	$giro = $result_accion[0]['GIRO'];
	$direccion = $result_accion[0]['DIRECCION'];
	$mail_contac = $result_accion[0]['MAIL'];
	$dig_verif = $result_accion[0]['DIG_VERIF'];
 		
	if($cargo == '')
		$cargo = '<i>No registrado</i>';
	if($rut_emp == '')
		$rut_emp = '<i>No registrado</i>';
	if($direccion == '')
		$direccion = '<i>No registrado</i>';
	if($mail_contac == '')
		$mail_contac = '<i>No registrado</i>';
	if($giro == '')
		$giro = '<i>No registrado</i>';	
		
		$mail = new phpmailer();
		$mail->PluginDir = dirname(__FILE__)."/../../../../commonlib/trunk/php/clases/";
		$mail->Mailer 	= "smtp";
		$mail->SMTPAuth = true;
		
		$mail->Host = $URL_SMTP;
		$mail->Username = $USER_SMTP;
		$mail->Password = $PASS_SMTP;
		$mail->Port = $PORT_SMTP;
		$mail->SMTPSecure= 'ssl';
		  
		$mail->From 	="registrollamados@biggi.cl";		
		$mail->FromName = "Biggi - Registro de Llamados";
		$mail->Timeout=30;		
		
		$sql_accion ="SELECT NOM_LLAMADO_ACCION 
						FROM LLAMADO_ACCION LLA, LLAMADO LL
					   WHERE LL.COD_LLAMADO = $cod_llamado
						 AND LL.COD_LLAMADO_ACCION = LLA.COD_LLAMADO_ACCION";
		$result_accion = $db->build_results($sql_accion);					
		$nom_llamado_accion = $result_accion[0]['NOM_LLAMADO_ACCION'];
		
		$sql = "SELECT  LL.COD_DESTINATARIO
						,NOM_DESTINATARIO
						,MAIL
				  from LLAMADO_DESTINATARIO LL 
						LEFT OUTER JOIN DESTINATARIO D ON D.COD_DESTINATARIO = LL.COD_DESTINATARIO
				 where COD_LLAMADO = $cod_llamado
				 ORDER BY RESPONSABLE DESC, COD_LLAMADO_DESTINATARIO ASC";//deja en primera posición responsable = 'S'
		
		$result = $db->build_results($sql);
		$nom_responsable = $result[0]['NOM_DESTINATARIO'];
		$row_count = $db->count_rows();
		
		//listado de todos a los que se enviara mail
		$nom_todos_destinatario = "";
		for($i=0;$i<$row_count;$i++){
			$nom_todos_destinatario = $nom_todos_destinatario.$result[$i]['NOM_DESTINATARIO'].",";
		}
		
		$nom_todos_destinatario = substr ($nom_todos_destinatario, 0, strlen($nom_todos_destinatario) - 1);
		
		$mail->Subject = "[$cod_llamado] $nom_contacto : $nom_llamado_accion";
		$n_usuario = $this->nom_usuario;
		$body = "<html>
<head>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<style type='text/css'>
<!--
.Estilo13 {color: #663300}
.Estilo15 {color: #999999}
.Estilo22 {color: #003366}
-->
</style>
</head>

<body>
<table width='440' height='171' border='2' bordercolor='#660033'>
   <tr>
     <td bgcolor='#FFF3E8'> <table width='341' border='0'>
         <tr>
           <td width='73'><h4 class='Estilo22'>Estimado(a)</h4></td>
           <td width='3'><h4 class='Estilo22'>:</h4></td>
            <td width='243'><h4><span class='Estilo22'>$nom_responsable </span></h4></td>
        </tr>
     </table>     </td>
   </tr>
   <tr>
     <td bgcolor='#FFF1EC'><table width='400' border='0' align='center'>
        <tr>
          <td width='147' bordercolor='#993399'><h5 class='Estilo22'>Mensaje</h5></td>
          <td width='10'><h5 class='Estilo22'>:</h5></td>
          <td width='362' bordercolor='#9933FF'><h5 class='Estilo22'>$mensaje</h5></td>
        </tr>
        <tr>
          <td><h5 class='Estilo13'>Llamado N&ordm;</h5></td>
          <td><h5 class='Estilo13'>:</h5></td>
          <td><h5 class='Estilo13'>$cod_llamado</h5></td>
        </tr>
        <tr>
          <td><h5 class='Estilo13'>Raz&oacute;n Social</h5></td>
          <td><h5 class='Estilo13'>:</h5></td>
          <td><h5 class='Estilo13'>$nom_contacto</h5></td>
        </tr>
          <tr>
          <td><h5 class='Estilo13'>Rut</h5></td>
          <td><h5 class='Estilo13'>:</h5></td>
          <td><h5 class='Estilo13'>$rut_emp-$dig_verif</h5></td>
        </tr>
        <tr>
          <td><h5 class='Estilo13'>Direccion</h5></td>
          <td><h5 class='Estilo13'>:</h5></td>
          <td><h5 class='Estilo13'>$direccion</h5></td>
        </tr>
        
        <tr>
          <td><h5 class='Estilo13'>Giro</h5></td>
          <td><h5 class='Estilo13'>:</h5></td>
          <td><h5 class='Estilo13'>$giro</h5></td>
        </tr>
        <tr>
        <tr>
          <td><h5 class='Estilo13'>Cont&aacute;cto</h5></td>
          <td><h5 class='Estilo13'>:</h5></td>
          <td><h5 class='Estilo13'>$nom_persona</h5></td>
        </tr>
        <tr>
          <td><h5 class='Estilo13'>Cargo</h5></td>
          <td><h5 class='Estilo13'>:</h5></td>
          <td><h5 class='Estilo13'>$cargo</h5></td>
        </tr>
         <tr>
          <td><h5 class='Estilo13'>mail</h5></td>
          <td><h5 class='Estilo13'>:</h5></td>
          <td><h5 class='Estilo13'>$mail_contac</h5></td>
        </tr>
        <tr>
          <td><h5 class='Estilo13'>Acci&oacute;n</h5></td>
          <td><h5 class='Estilo13'>:</h5></td>
          <td><h5 class='Estilo13'>$nom_llamado_accion</h5></td>
        </tr>
        <tr>
          <td><h5 class='Estilo13'>Llamar a</h5></td>
          <td><h5 class='Estilo13'>:</h5></td>
          <td><h5 class='Estilo13'>$llamar_telefono</h5></td>
        </tr>
        <tr>
          <td><h5 class='Estilo13'>Registrado por</h5></td>
          <td><h5 class='Estilo13'>:</h5></td>
          <td><h5 class='Estilo13'>$n_usuario</h5></td>
        </tr>
        <tr>
          <td colspan='3'><h6 class='Estilo15'>Enviado a los siguientes destinatarios:</h6>
          <p class='claro'><h6>$nom_todos_destinatario<h6></p></td>
        </tr>
        <tr>
          ";
        		
		/*"Estimado(a): $nom_responsable 
		
		Mensaje       : $mensaje
		
		Llamado Nº    : $cod_llamado
		
		Razon Social  : $nom_contacto			
		
		Contacto       : $nom_persona  Cargo: $cargo
		
		Acción         : $nom_llamado_accion		Llamar a: $llamar_telefono
	
		Registrado por: $this->nom_usuario
		
		Enviado a los siguientes destinatarios: $nom_todos_destinatario
		
		
		Si desea responder, dé clic en el siguiente link ";*/
		
		$altbody = "";
		for($i=0;$i<$row_count;$i++){
			$mail->ClearAddresses();
			$cod_destinatario = $result[$i]['COD_DESTINATARIO'];
			$cod_destinatario_enc = encriptar_url($cod_destinatario, 'envio_mail_llamado');
			$param_enc = "ll=".$cod_llamado_enc."&d=".$cod_destinatario_enc;
			
			$link_final = $link.$param_enc;		
			$final_html= "<td colspan='3' bgcolor='#FFF1EC'><table width='350' border='1'>
				            <tr>
				              <td bgcolor='#EAFDFD' class='Estilo15'><h5>Si desea responder, d&eacute; clic en el <em><a href='$link_final'>link</a></em><h5></td>
				              </tr>
				          </table>          
				          <h6 class='Estilo15'>&nbsp;</h6>
				          </td>
				        </tr>
				          </table></td>
				   </tr>
				</table>
				</blockquote>
				</body>
					</html>";

			$mail->AddAddress($result[$i]['MAIL'], $result[$i]['NOM_DESTINATARIO']);
			
			//$mail->AddCC("jmino@integrasystem.cl", "Javier Miño");			
			$mail->Body = $body.$final_html;
			$mail->AltBody = $altbody.$link.$cod_destinatario_enc;
			$exito = $mail->Send();

			if(!$exito){
				echo "Problema al enviar correo electrónico a ".$result[$i]['MAIL'];
			}
		
			
		}
		
		/*
		$mail->AddCC("jjofre@biggi.cl", "Jorge Jofré");
		$mail->AddCC("ascianca@biggi.cl", "Angel Scianca");
		$mail->AddCC("sergio.pechoante@biggi.cl", "Sergio Pechoante");
		*/
		
		return 0; 
	}
	
	function save_record($db) {
		$cod_llamado 		= $this->get_key();		
	    $cod_usuario		= $this->dws['dw_llamado']->get_item(0, 'COD_USUARIO');
	    $cod_contacto		= $this->dws['dw_llamado']->get_item(0, 'COD_CONTACTO');
	    $cod_contacto_persona = $this->dws['dw_llamado']->get_item(0, 'COD_CONTACTO_PERSONA');
	    $mensaje			= $this->dws['dw_llamado']->get_item(0, 'MENSAJE');
	    $cod_llamado_accion	= $this->dws['dw_llamado']->get_item(0, 'COD_LLAMADO_ACCION');
		$llamar_telefono	= $this->dws['dw_llamado']->get_item(0, 'LLAMAR_TELEFONO');
	    $realizado			= $this->dws['dw_llamado']->get_item(0, 'REALIZADO');
		$glosa_realizado	= $this->dws['dw_llamado']->get_item(0, 'GLOSA_REALIZADO');
		$tipo_doc_realizado	= $this->dws['dw_llamado']->get_item(0, 'TIPO_DOC_REALIZADO');
		$cod_doc_realizado	= $this->dws['dw_llamado']->get_item(0, 'COD_DOC_REALIZADO');
////  falta cargo 5658*569  ////
		$cod_llamado	= ($cod_llamado=='') ? "null" : $cod_llamado;
		
		$glosa_realizado = ($glosa_realizado=='') ? "null" : "'$glosa_realizado'";
		$tipo_doc_realizado = ($tipo_doc_realizado=='') ? "null" : "'$tipo_doc_realizado'";
		$cod_doc_realizado = ($cod_doc_realizado=='') ? "null" : $cod_doc_realizado;
		
		$sp = 'spu_llamado';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
   
	    $param	= "'$operacion'
	    			,$cod_llamado		
				    ,$cod_usuario
				    ,$cod_contacto
				    ,$cod_contacto_persona
				    ,'$mensaje'
				    ,$cod_llamado_accion
					,'$llamar_telefono'
				    ,'$realizado'
					,$glosa_realizado
					,$tipo_doc_realizado
					,$cod_doc_realizado";
					
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_llamado = $db->GET_IDENTITY();
				$this->dws['dw_llamado']->set_item(0, 'COD_LLAMADO', $cod_llamado);
			}
			
			if (!$this->dws['dw_destinatario']->update($db, $cod_llamado))
				return false;
			if (!$this->dws['dw_conversacion']->update($db, $cod_llamado))
				return false;
			if (!$this->dws['dw_destinatario_fijo']->update($db, $cod_llamado))
				return false;				
				
			//si es nuevo registro y grabó dw_llamado, dw_destinatario y dw_conversacion ->envia mail
			if ($this->is_new_record()) {
				$this->envio_mail();
				$this->alert("Mensaje Enviado Correctamente");
				
			}
				
			return true;
		}
		return false;			
	}
}
?>