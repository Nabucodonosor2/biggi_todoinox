<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_output_biggi.php");

class wo_pago_comision_tdnx extends w_output_biggi{
	function wo_pago_comision_tdnx(){
		$sql = "SELECT PCT.COD_PAGO_COMISION_TDNX
					  ,CONVERT(VARCHAR, FECHA_DESDE, 103) FECHA_DESDE
					  ,FECHA_DESDE DATE_FECHA_DESDE
					  ,CONVERT(VARCHAR, FECHA_HASTA, 103) FECHA_HASTA
					  ,FECHA_HASTA DATE_FECHA_HASTA
					  ,PCT.COD_ESTADO_PAGO_COMISION_TDNX
					  ,EPCT.NOM_ESTADO_PAGO_COMISION_TDNX
				FROM PAGO_COMISION_TDNX PCT
					,ESTADO_PAGO_COMISION_TDNX EPCT
				WHERE PCT.COD_ESTADO_PAGO_COMISION_TDNX = EPCT.COD_ESTADO_PAGO_COMISION_TDNX	
				ORDER BY COD_PAGO_COMISION_TDNX DESC";
			
      parent::w_output_biggi('pago_comision_tdnx', $sql, $_REQUEST['cod_item_menu']);

      // headers
      $this->add_header(new header_num('COD_PAGO_COMISION_TDNX', 'COD_PAGO_COMISION_TDNX', 'Código'));
      $this->add_header($control = new header_date('FECHA_DESDE', 'CONVERT(VARCHAR, FECHA_DESDE, 103)', 'Fecha Desde'));
      $control->field_bd_order = 'DATE_FECHA_DESDE';
      $this->add_header($control = new header_date('FECHA_HASTA', 'CONVERT(VARCHAR, FECHA_HASTA, 103)', 'Fecha Hasta'));
      $control->field_bd_order = 'DATE_ORDEN_PAGO';
      $sql = "SELECT COD_ESTADO_PAGO_COMISION_TDNX ,NOM_ESTADO_PAGO_COMISION_TDNX FROM ESTADO_PAGO_COMISION_TDNX";  
	  $this->add_header(new header_drop_down('NOM_ESTADO_PAGO_COMISION_TDNX', 'PCT.COD_ESTADO_PAGO_COMISION_TDNX', 'Estado', $sql));

	}
   
	function agregar_pago_comision($valores){
		session::set('ADD_VALORES', $valores);
		$this->add();
	}
   	
	function habilita_boton(&$temp, $boton, $habilita){
		if ($boton=='add'){
			if ($habilita){
				$ruta_over = "'../../../../commonlib/trunk/images/b_add_over.jpg'";
				$ruta_out = "'../../../../commonlib/trunk/images/b_add.jpg'";
				$ruta_click = "'../../../../commonlib/trunk/images/b_add_click.jpg'";
				$temp->setVar("WO_ADD", '<input name="b_'.$boton.'" id="b_'.$boton.'" type="button" onmouseover="entrada(this,'.$ruta_over.')" onmouseout="salida(this,'.$ruta_out.')" onmousedown="down(this,'.$ruta_click.')"'.
											'style="cursor:pointer;height:68px;width:66px;border: 0;background-image:url(../../../../commonlib/trunk/images/b_'.$boton.'.jpg);background-repeat:no-repeat;background-position:center;border-radius: 15px;"'.
											'onClick="return dlg_agrega_comision();"/>');
			}else
				$temp->setVar("WO_".strtoupper($boton), '<img src="../../../../commonlib/trunk/images/b_add_d.jpg"/>');
		}
		else
			parent::habilita_boton($temp, $boton, $habilita);
	}
	
	function procesa_event(){		
		if(isset($_POST['b_add_x'])){
			$this->agregar_pago_comision($_POST['wo_hidden']);
		}
		else
			parent::procesa_event();
	}
}
?>