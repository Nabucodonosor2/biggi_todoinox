function request_crear_desde(ve_prompt,ve_valor){
	var url = "../../../../commonlib/trunk/php/request.php?prompt="+URLEncode(ve_prompt)+"&valor="+URLEncode(ve_valor);
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 140,
		 width: 400,
		 scrollable: false,
		 onClose: function(){ 
		 	var returnVal = this.returnValue;
		 	
		 	if (returnVal == null){		
				return false;
			}			
			else {
				var ajax = nuevoAjax();
				ajax.open("GET", "ajax_que_precio_usa.php?regreso="+returnVal, false);
				ajax.send(null);
	
				var vl_resp = ajax.responseText;
				var resp = vl_resp.split('|');
				var cod_cotizacion = resp[0];
				var cambio_precio = resp[1];
				if(cambio_precio == 'SI'){
					var url = "../common_appl/que_precio_usa.php?cod_cotizacion="+cod_cotizacion;
					$.showModalDialog({
						 url: url,
						 dialogArguments: '',
						 height: 330,
						 width: 710,
						 scrollable: false,
						 onClose: function(){ 
						 	var returnVal2 = this.returnValue;
						 	if (returnVal2=='1'){
						 		var ajax = nuevoAjax();
								ajax.open("GET", "../common_appl/setear_sesion.php?", false);
								ajax.send(null);
						 	}
						 	var input = document.createElement("input");
							input.setAttribute("type", "hidden");
							input.setAttribute("name", "b_create_x");
							input.setAttribute("id", "b_create_x");
							document.getElementById("output").appendChild(input);
							
							document.getElementById('wo_hidden').value = returnVal;
							document.output.submit();
					   		return true;
						}
					});	
				}else{
					var input = document.createElement("input");
					input.setAttribute("type", "hidden");
					input.setAttribute("name", "b_create_x");
					input.setAttribute("id", "b_create_x");
					document.getElementById("output").appendChild(input);
					
					document.getElementById('wo_hidden').value = returnVal;
					document.output.submit();
			   		return true;
			   	}	
			}
		}
	});	
}	 
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
		var url = "dlg_print_cotizacion.php?cod_cotizacion="+document.getElementById('COD_COTIZACION_H_0').value;
		$.showModalDialog({
			 url: url,
			 dialogArguments: '',
			 height: 290,
			 width: 500,
			 scrollable: false,
			 onClose: function(){ 
			 	var returnVal = this.returnValue;
			 	if (returnVal == null){		
					return false;
				}			
				else {
					var input = document.createElement("input");
					input.setAttribute("type", "hidden");
					input.setAttribute("name", "b_print_x");
					input.setAttribute("id", "b_print_x");
					document.getElementById("input").appendChild(input);
					
					document.getElementById('wi_hidden').value = returnVal;
					document.input.submit();
			   		return true;
				}
			}
		});		
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
		if(!confirm("Este Cliente esta asignado a: "+nom_usuario_empresa+", esta cotización será informada a "+nom_usuario_empresa+".\n \n ¿Desea Continuar?")){
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
	
	var vl_ajax = nuevoAjax();
	vl_ajax.open("GET", "../cotizacion/TODOINOX/ajax_descuento_permitido.php?cod_empresa="+ve_cod_empresa.value, false);
	vl_ajax.send(null);		
	var resp = vl_ajax.responseText;
	if(parseFloat(resp) == 0){
		/////////////Validacion Porc Maximo x Usuario//////////////////////	
		var porc_monto_dscto = ((parseFloat(monto_dscto1) + parseFloat(monto_dscto2))/parseFloat(sum_total.value)) * 100;
		var monto_permitido = (parseFloat(sum_total.value) * parseFloat(porc_dscto_max.value)) / 100 ;
		
		if(parseFloat(porc_monto_dscto) > parseFloat(porc_dscto_max.value)){
			alert('La suma de los descuentos es mayor al permitido (máximo '+number_format(porc_dscto_max.value, 1, ',', '.')+' % entre los dos descuentos, equivalente a '+number_format(monto_permitido, 0, ',', '.')+')');
			document.getElementById('PORC_DSCTO1_0').focus();
			return false;
		}
	  	///////////////////////////////////////////////////////////////////
	}else{
		var porc_monto_dscto = ((parseFloat(monto_dscto1) + parseFloat(monto_dscto2))/parseFloat(sum_total.value)) * 100;
		var monto_permitido = (parseFloat(sum_total.value) * parseFloat(resp)) / 100 ;
		
		if(parseFloat(porc_monto_dscto) > parseFloat(resp)){
			alert('La suma de los descuentos es mayor al permitido (máximo '+number_format(resp, 1, ',', '.')+' % entre los dos descuentos, equivalente a '+number_format(monto_permitido, 0, ',', '.')+')');
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
				alert('Debe ingresar la descripción del compromiso');
				return false;
			}
		}
	}
	
	return true;
	//return validate_cot_nv('ITEM_COTIZACION');
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
	var sc_precio_compra_value = ve_cod_proveedor.options[ve_cod_proveedor.selectedIndex].dataset.dropdown; 
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

function select_1_empresa(valores, record){
	var vl_ajax = nuevoAjax();
	vl_ajax.open("GET", "../cotizacion/TODOINOX/ajax_descuento_permitido.php?cod_empresa="+valores[1], false);
	vl_ajax.send(null);		
	var resp = vl_ajax.responseText;
	
	document.getElementById('PORC_DSCTO1_0').value = number_format(resp, 1, ',', '.');
	document.getElementById('DSCTO_CLIENTE_ORIGINAL_0').value = resp;
	document.getElementById('INGRESO_USUARIO_DSCTO1_0').value = 'P';
	set_values_empresa(valores, record);
	
	document.getElementById('MONTO_DSCTO1_0').value = Math.round(to_num(document.getElementById('PORC_DSCTO1_0').value)/100 * document.getElementById('SUM_TOTAL_H_0').value, 0);
	document.getElementById('INGRESO_USUARIO_DSCTO1_0').value = 'P';
	computed(0, 'TOTAL_NETO');
}

function valida_btn_precio(ve_control){
	var vl_ajax = nuevoAjax();
	vl_ajax.open("GET", "../cotizacion/TODOINOX/ajax_valida_btn_precio.php?", false);
	vl_ajax.send(null);		
	var resp = vl_ajax.responseText;
	if(resp == 0)
		alert('Su porcentaje asignado para modificar precios de venta es cero.\nUsted no podrá cambiar los precios cotizados.');
	else
		change_precio(ve_control, 'ITEM_COTIZACION');
}
// DIALOG Required Code
	var prntWindow = getParentWindowWithDialog(); //$(top)[0];

	var $dlg = prntWindow && prntWindow.$dialog;

	function getParentWindowWithDialog() {
		var p = window.parent;
		var previousParent = p;
		while (p != null) {
			if ($(p.document).find('#iframeDialog').length) return p;

			p = p.parent;

			if (previousParent == p) return null;

			// save previous parent

			previousParent = p;
		}
		return null;
	}

	function setWindowReturnValue(value) {
		if ($dlg) $dlg.returnValue = value;
		window.returnValue = value; // in case popup is called using showModalDialog

	}

	function getWindowReturnValue() {
		// in case popup is called using showModalDialog

		if (!$dlg && window.returnValue != null)
			return window.returnValue;

		return $dlg && $dlg.returnValue;
	}

	if ($dlg) window.dialogArguments = $dlg.dialogArguments;
	if ($dlg) window.close = function() { if ($dlg) $dlg.dialogWindow.dialog('close'); };
	// END of dialog Required Code

    function okMe() {
    returnValue=get_returnVal();	
	setWindowReturnValue(returnValue);
	$dlg.dialogWindow.dialog('close');
		
	}
    function closeMe() {
        setWindowReturnValue(null);
        $dlg.dialogWindow.dialog('close');
    }
    function dialogo(cod_cotizacion){
    alert('a'+cod_cotizacion);
    my_alert('b');
    	var url = "../common_appl/que_precio_usa.php?cod_cotizacion="+cod_cotizacion;
		$.showModalDialog({
			 url: url,
			 dialogArguments: '',
			 height: 290,
			 width: 500,
			 scrollable: false
		});
    }