function calculo_descuento(porc1){
///////% DESCTO.1
     //seteamos INGRESO_USUARIO_DSCTO1 siempre que entre a esta funcion sea = P 
	 document.getElementById('INGRESO_USUARIO_DSCTO1_0').value = 'P';
	 porc_descto2 = 0;//Math.round(to_num(document.getElementById('PORC_DSCTO2_0').value));
	 //Se trae porcentaje ingresado 
	 porc_descto = Math.round(to_num(document.getElementById('PORC_DSCTO1_0').value));
	 porc_sum_total = porc_descto / 100;
	 //traigo sum_total = la suma de los montos de los item cotizados
	 sum_total = document.getElementById('SUM_TOTAL_H_0').value;
	 // luego de haber dividido por 100 el porc ingresado lo multiplicamos por la suma_total
	 total = parseInt(porc_sum_total * sum_total);
	 
	 // en total obtenemos el monto descuento y seteamos el control para darle valor .
	 document.getElementById('MONTO_DSCTO1_0').innerHTML = total;
	 // seteamos el monto descuento hidden
	 //document.getElementById('MONTO_DSCTO1_H_0').value = total;
	 //restamos sum_total - total(monto descuento). y le pasamos el valor al total_neto
	 total_neto =  sum_total - total;
	 document.getElementById('TOTAL_NETO_0').innerHTML = total_neto;
	 
	 //calculo iva 
	 iva = Math.round(to_num(document.getElementById('PORC_IVA_0').value)); 
	   if(iva==19){
		   iva_mult_total= total_neto * 0.19;
		   document.getElementById('MONTO_IVA_0').innerHTML = iva_mult_total;
		   total_con_iva = iva_mult_total + total_neto;
		   document.getElementById('TOTAL_CON_IVA_0').innerHTML = total_con_iva;
	   }else{
		   iva_mult_total= total_neto * 0;
		   document.getElementById('MONTO_IVA_0').innerHTML = iva_mult_total;
		   total_con_iva = iva_mult_total + total_neto;
		   document.getElementById('TOTAL_CON_IVA_0').innerHTML = total_con_iva;
		}
	
	 if(porc_descto2 != 0){
		 //traigo suma total monto item
	 sum_total = document.getElementById('SUM_TOTAL_H_0').value;
	 total= parseInt(porc_sum_total * total_neto);
	 // seteo variables
	 document.getElementById('MONTO_DSCTO1_0').innerHTML = total;
	 document.getElementById('MONTO_DSCTO1_H_0').value = total;
	 
	 total_neto =  total_neto - total;
	 document.getElementById('TOTAL_NETO_0').innerHTML = total_neto;
	 //calculo iva 
	 iva = Math.round(to_num(document.getElementById('PORC_IVA_0').value)); 
	   if(iva==19){
		   iva_mult_total= total_neto * 0.19;
		   document.getElementById('MONTO_IVA_0').innerHTML = iva_mult_total;
		   total_con_iva = iva_mult_total + total_neto;
		   document.getElementById('TOTAL_CON_IVA_0').innerHTML = total_con_iva;
	   }else{
		   iva_mult_total= total_neto * 0;
		   alert(iva_mult_total);
		   document.getElementById('MONTO_IVA_0').innerHTML = iva_mult_total;
		   total_con_iva = iva_mult_total + total_neto;
		   document.getElementById('TOTAL_CON_IVA_0').innerHTML = total_con_iva;
	  
	 
	}
  }
}

function dlg_print() {
	var tipo_dispositivo = document.getElementById('TIPO_DISPOSITIVO_0').innerHTML;
	if(tipo_dispositivo == 'IPAD'){
    	alert('Resumen Cotizacion');
    }else{
	var args = "location:no;dialogLeft:400px;dialogTop:300px;dialogWidth:470px;dialogHeight:225px;dialogLocation:0;Toolbar:no;";
	var returnVal = null;
	
	returnVal = window.showModalDialog("dlg_print_cotizacion.php?cod_cotizacion="+document.getElementById('COD_COTIZACION_H_0').value, "_blank", args);
	
 	if (returnVal == null)
 		return false;
	else {
		document.getElementById('wi_hidden').value = returnVal;
		document.input.submit();
   		return true;
		}
	}
}

function validate() {
	ve_cod_empresa = document.getElementById('COD_EMPRESA_0');
	ve_cod_usuario1 = document.getElementById('COD_USUARIO_VENDEDOR1_0').value;
	ve_cod_usuario2 = document.getElementById('COD_USUARIO_VENDEDOR2_0').value;
	
	if(!ve_cod_empresa)
		ve_cod_empresa = document.getElementById('COD_EMPRESA_H_0');
	
 	var ajax = nuevoAjax();
	ajax.open("GET", "../cotizacion/get_vendedor.php?cod_empresa="+URLEncode(ve_cod_empresa.value), false);
	ajax.send(null);
		
	var resp = ajax.responseText.split('|');
	var cod_usuario_empresa = resp[0]; 
	var nom_usuario_empresa = resp[1];
	
	/*
	VMC, 7-01-2011
	se elimina el envio de mail cuando se cotiza a un cliente no asignado 
	
	
	if(ve_cod_usuario1 != cod_usuario_empresa && ve_cod_usuario2 != cod_usuario_empresa)   
	{			
		if(!confirm("Este Cliente esta asignado a: "+nom_usuario_empresa+", esta cotizaci�n ser� informada a "+nom_usuario_empresa+".\n \n �Desea Continuar?")){
			document.getElementById('COD_USUARIO_VENDEDOR1_0').focus();
			return false;
		}   				
	}
	*/
	var cod_cotizacion = get_value('COD_COTIZACION_H_0');
	var porc_dscto1 = get_value('PORC_DSCTO1_0');
	var monto_dscto1 = get_value('MONTO_DSCTO1_0');
	var monto_dscto2 = get_value('MONTO_DSCTO2_0');
	var sum_total = document.getElementById('SUM_TOTAL_H_0');		
	var porc_dscto_max = document.getElementById('PORC_DSCTO_MAX_0');
	if (sum_total.value=='') sum_total.value = 0;
	if (monto_dscto1=='') monto_dscto2 = 0;
	if (monto_dscto2=='') monto_dscto2 = 0;
	
		
	var ajax = nuevoAjax();
	ajax.open("GET", "../cotizacion/TODOINOX/ajax_valida_dscto.php?cod_cotizacion="+cod_cotizacion, false);
	ajax.send(null);
	var resp = ajax.responseText;
	
  	var resp = resp.split('|');
  	var porc_dscto = resp[0];
  	var dscto_bd = resp[1];
  	 
  	var porc_dscto1 = parseFloat(porc_dscto1.replace(',', '.', 'g'));
  	var porc_dscto =  parseFloat(porc_dscto.replace(',', '.', 'g'));
  	
  	if(porc_dscto1 != dscto_bd){
	  	if(porc_dscto1 > porc_dscto){
	  		alert('El descuento ingresado es mayor a su maximo permitido (Su descuento m�ximo es '+porc_dscto+'%)');
	  		document.getElementById('PORC_DSCTO1_0').focus();
	  		return false;
	  	}
  	}
	
	var aTR = get_TR('BITACORA_COTIZACION');
	for (var i = 0; i < aTR.length; i++){
		var tiene_compromiso = document.getElementById('BC_TIENE_COMPROMISO_' + i).checked;
		
		if (tiene_compromiso == true){
			var fecha_compromiso = document.getElementById('FECHA_COMPROMISO_E_' + i).value;
			var hora_compromiso = document.getElementById('HORA_COMPROMISO_E_' + i).value;
			var glosa_compromiso = document.getElementById('GLOSA_COMPROMISO_E_' + i).value;
			if(fecha_compromiso == ''){
				alert('Debe ingresar la fecha del compromiso');
				return false;
			}
			else if (hora_compromiso == ''){
				alert('Debe ingresar la hora del compromiso');
				return false;
			}
			else if (glosa_compromiso == ''){
				alert('Debe ingresar la descripci�n del compromiso');
				return false;
			}
		}
	}
		
	return validate_cot_nv('ITEM_COTIZACION');
}

function add_line_item(tabla_id, nom_tabla) {
	var row = add_line(tabla_id, nom_tabla);
	var aTR = get_TR('ITEM_COTIZACION');
	var item = 1;
	var letra = '';
	for (var i=aTR.length - 2; i >=0; i--, item++) {
		var cod_producto_value = document.getElementById('COD_PRODUCTO_' + get_num_rec_field(aTR[i].id)).value
		if (cod_producto_value=='T') {
			letra = document.getElementById('ITEM_' + get_num_rec_field(aTR[i].id)).value; 
			break;
		}
	}	
	document.getElementById('ITEM_' + row).value = letra + item;
}

function get_precio_compra(ve_cod_proveedor) {
	var sc_precio_compra_value = ve_cod_proveedor.options[ve_cod_proveedor.selectedIndex].label; 
	var record = get_num_rec_field(ve_cod_proveedor.id);
	set_value('PRECIO_COMPRA_' + record, sc_precio_compra_value, number_format(sc_precio_compra_value, 0, ',', '.'));
}

function mostrarOcultar(ve_cod_forma_pago) {
	var cod_forma_pago = ve_cod_forma_pago.options[ve_cod_forma_pago.selectedIndex].value; 	
	if (parseFloat(cod_forma_pago) == 1){
    	document.getElementById('NOM_FORMA_PAGO_OTRO_0').type='text';
		document.getElementById('NOM_FORMA_PAGO_OTRO_0').setAttribute('onblur', "this.style.border=''");				
		document.getElementById('NOM_FORMA_PAGO_OTRO_0').setAttribute('onfocus', "this.style.border='1px solid #FF0000'");				
    }
    else{
    	document.getElementById('NOM_FORMA_PAGO_OTRO_0').type='hidden';
    }
}

/*function select_1_empresa(valores, record){
	var vl_ajax = nuevoAjax();
	vl_ajax.open("GET", "../cotizacion/TODOINOX/ajax_descuento_permitido.php?cod_empresa="+valores[1], false);
	vl_ajax.send(null);		
	var resp = vl_ajax.responseText;
	document.getElementById('PORC_DSCTO1_0').value = number_format(resp, 0, ',', '.');
	document.getElementById('INGRESO_USUARIO_DSCTO1_0').value = 'P';
	set_values_empresa(valores, record);
}*/

function valida_btn_precio(ve_control){
	var vl_ajax = nuevoAjax();
	vl_ajax.open("GET", "../cotizacion/TODOINOX/ajax_valida_btn_precio.php?", false);
	vl_ajax.send(null);		
	var resp = vl_ajax.responseText;
	if(resp == 0)
		alert('Su porcentaje asignado para modificar precios de venta es cero.\nUsted no podr� cambiar los precios cotizados.');
	else
		change_precio(ve_control, 'ITEM_COTIZACION');
}