<?php
class wo_nota_credito extends wo_nota_credito_base {
	const K_ESTADO_SII_EMITIDA	= 1;
	const K_PARAM_MAX_IT_NC		= 40;
	const K_ESTADO_SII_ANULADA	= 4;
	const K_AUTORIZA_AGREGAR	= '993505';
	const K_AUTORIZA_CREAR_DESDE = '993510';
	const K_AUTORIZA_EXPORTAR = '993515';
	
	function wo_nota_credito(){
		parent::wo_nota_credito_base();
	}
	/*function redraw(&$temp){
		parent::redraw(&$temp);
		$privilegio=$this->get_privilegio_opcion_usuario(self::K_AUTORIZA_CREAR_DESDE, $this->cod_usuario);
  		if ($this->b_add_visible)
			$this->habilita_boton($temp, 'create',$privilegio);
	}*/	
	function habilita_boton(&$temp, $boton, $habilita) {
		if ($boton=='create') {
			if ($habilita){
				$ruta_over = "'../../../../commonlib/trunk/images/b_create_over.jpg'";
				$ruta_out = "'../../../../commonlib/trunk/images/b_create.jpg'";
				$ruta_click = "'../../../../commonlib/trunk/images/b_create_click.jpg'";
				$temp->setVar("WO_CREATE", '<input name="b_'.$boton.'" id="b_'.$boton.'" type="button" onmouseover="entrada(this, '.$ruta_over.')" onmouseout="salida(this, '.$ruta_out.')" onmousedown="down(this, '.$ruta_click.')"'.
								'style="cursor:pointer;height:68px;width:66px;border: 0;background-image:url(../../../../commonlib/trunk/images/b_'.$boton.'.jpg);background-repeat:no-repeat;background-position:center;border-radius: 15px;"'.
								'onClick="return request_nota_credito(\'Ingrese Nº de Documento\',\'\');" />');
			}else{			
				$temp->setVar("WO_".strtoupper($boton), '<img src="../../../../commonlib/trunk/images/b_create_d.jpg"/>');
			}
		}
		else
			parent::habilita_boton($temp, $boton, $habilita);
	}
	function redraw(&$temp) {
		parent::redraw($temp);
		 $priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_CREAR_DESDE, $this->cod_usuario);

		if ($priv=='E'){
			$this->habilita_boton($temp, 'create', true);
		}else{	

			$this->habilita_boton($temp, 'create', false);
		}
			
		//$this->dw_check_box->habilitar($temp, true);
	}
}
?>