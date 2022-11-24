function valida_referencias(ve_control){
	var vl_record = get_num_rec_field(ve_control.id);
	var vl_hem = document.getElementById('REFERENCIA_HEM_0').value;
	var vl_hes = document.getElementById('REFERENCIA_HES_0').value;
	var count1 = 0;
	var count2 = 0;
	var count_cto = 0;
	var count_m_cto = 0;
	var count_802_car = 0;

	var aTR = get_TR('REFERENCIAS');
	for(i = 0; i < aTR.length ; i++){
	 	var vl_rec = get_num_rec_field(aTR[i].id);
	 	
	 	if(document.getElementById('COD_TIPO_REFERENCIA_'+vl_rec).value == 1){//HEM
	 		count1++;
	 	}else if(document.getElementById('COD_TIPO_REFERENCIA_'+vl_rec).value == 2){//HES
	 		count2++;
	 	}else if(document.getElementById('COD_TIPO_REFERENCIA_'+vl_rec).value == 3){//CONTACTO
	 		count_cto++;
	 		//document.getElementById('DOC_REFERENCIA_'+vl_record).value = "";
	 	}else if(document.getElementById('COD_TIPO_REFERENCIA_'+vl_rec).value == 4){//CORREO CONTACTO
	 		count_m_cto++;
	 		
	 		if(count_m_cto == 1){
		 		var theElement = document.getElementById('DOC_REFERENCIA_'+vl_rec);
		 		validate_mail(theElement);
			}
		}else if(document.getElementById('COD_TIPO_REFERENCIA_'+vl_rec).value == 5){//802 CAROZZI
	 		count_802_car++;
	 	}
	}
	
	if(count1 > 1 || count2 > 1){
		alert('No debe ingresar mas de un tipo sea HEM o HES');
		document.getElementById('COD_TIPO_REFERENCIA_'+vl_record).value = "";
		document.getElementById('DOC_REFERENCIA_'+vl_record).value = "";
	}
	
	if(vl_hem == 'S' && count2 > 0){
		alert('Esta empresa tiene como referencia HEM, no puede agregar referencias de tipo HES');
		document.getElementById('COD_TIPO_REFERENCIA_'+vl_record).value = "";
		document.getElementById('DOC_REFERENCIA_'+vl_record).value = "";
	}
	
	if(vl_hes == 'S' && count1 > 0){
		alert('Esta empresa tiene como referencia HES, no puede agregar referencias de tipo HEM');
		document.getElementById('COD_TIPO_REFERENCIA_'+vl_record).value = "";
		document.getElementById('DOC_REFERENCIA_'+vl_record).value = "";
	}
	if(count_cto > 1){
		alert('No debe ingresar mas de un tipo sea Contacto');
		document.getElementById('COD_TIPO_REFERENCIA_'+vl_record).value = "";
		document.getElementById('DOC_REFERENCIA_'+vl_record).value = "";
	}
	if(count_m_cto > 1){
		alert('No debe ingresar mas de un correo de contacto');
		document.getElementById('COD_TIPO_REFERENCIA_'+vl_record).value = "";
		document.getElementById('DOC_REFERENCIA_'+vl_record).value = "";
	}
	if(count_802_car > 1){
		alert('No debe ingresar mas de un tipo sea 802 Carozzi');
		document.getElementById('COD_TIPO_REFERENCIA_'+vl_record).value = "";
		document.getElementById('DOC_REFERENCIA_'+vl_record).value = "";
	}
}
function validate_mail(theElement ) {
	var s = theElement.value;	
	var filter=/^[A-Za-z0-9][A-Za-z0-9_.-]*@[A-Za-z0-9_-]+\.[A-Za-z0-9_.-]+[A-za-z]$/;
	if (s.length == 0 ) return true;
	if (filter.test(s))
		return true;
	else
		alert("Ingrese una dirección de correo válida");
		theElement.value='';
		theElement.focus();
	return false;
}
function add_line_ref(ve_tabla_id, ve_nom_tabla){
	/*var vl_hem = document.getElementById('REFERENCIA_HEM_0').value;
	var vl_hes = document.getElementById('REFERENCIA_HES_0').value;
	
	if(vl_hem == 'S' || vl_hes == 'S'){
		var vl_row = add_line(ve_tabla_id, ve_nom_tabla);
		return vl_row;
	}else{
		alert('Esta empresa no tiene como referencia HES ni HEM, no puede agregar referencias');
		return "";
	}*/
	var vl_row = add_line(ve_tabla_id, ve_nom_tabla);
	return vl_row;
}

function ajax_load_ref_hidden(){
	var vl_cod_empresa = document.getElementById('COD_EMPRESA_0').value;

	if(vl_cod_empresa != ""){
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_load_ref_hidden.php?vl_cod_empresa="+vl_cod_empresa, false);
		ajax.send(null);
		var resp = ajax.responseText.split("|");
		
		document.getElementById('REFERENCIA_HEM_0').value = resp[0];
		document.getElementById('REFERENCIA_HES_0').value = resp[1];
	}
}

function request_factura(ve_prompt, ve_valor) 
{	
	var url = "../../../trunk/appl/factura/TODOINOX/request_factura.php?prompt="+URLEncode(ve_prompt)+"&valor="+URLEncode(ve_valor);
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 320,
		 width: 360,
		 scrollable: false,
		 onClose: function(){ 
		 	var returnVal = this.returnValue;
		 	if (returnVal == null)		
				return false;		
			else{
				var dato = returnVal.split("|");
	  			var cod_nota_venta_value= dato[1];
	  			var opcion= dato[0];
	  			if (opcion == 'desde_nv' || opcion == 'desde_cot' || opcion == 'desde_comercial' || opcion == 'desde_bodega' || opcion == 'desde_servindus') //
				{  
					var input = document.createElement("input");
					input.setAttribute("type", "hidden");
					input.setAttribute("name", "b_create_x");
					input.setAttribute("id", "b_create_x");
					document.getElementById("output").appendChild(input);
					//debe crear la FA para todos los itemsNV que tengan pendiente por facturar usar f_nv_por_facturar
					document.getElementById('wo_hidden').value = returnVal;
					document.output.submit();
			   		return true;
				}
				else{
					//si selecciona desde GD debe presentar una 2da ventana
					var url = "../../../trunk/appl/factura/request_factura_desdeGd.php?cod_nota_venta="+ cod_nota_venta_value;
					$.showModalDialog({
						 url: url,
						 dialogArguments: '',
						 height: 430,
						 width: 920,
						 scrollable: false,
						 onClose: function(){ 
						 	var returnVal = this.returnValue;
						 	if (returnVal == null)		
								return false;		
							else {
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
					});	
				
				}
			}	
		}
	});	
}
function select_1_producto(valores, record) {
	// para BODEGA se debe usar el precio INTERNO
	var vl_cod_empresa = document.getElementById('COD_EMPRESA_0').value;
	var vl_cantidad = document.getElementById('CANTIDAD_' + record).value;
	var vl_alert = '';
	var ajax = nuevoAjax();
	var vl_cod_producto_value = valores[1];
	ajax.open("GET", "../factura/TODOINOX/ajax_producto_precio.php?cod_producto="+vl_cod_producto_value+"&cod_empresa="+vl_cod_empresa, false);
	ajax.send(null);		
	var resp = URLDecode(ajax.responseText);
	valores[3] = resp;
	//Si la cantidad del producto viene vacio se setea a 0 la variable
	if(vl_cantidad == '')
		vl_cantidad = 0;
	//Se ejecuta la validacion de stock por Producto
	ajax.open("GET", "../factura/TODOINOX/ajax_valida_stock.php?cod_producto="+vl_cod_producto_value+'&cantidad='+vl_cantidad, false);
	ajax.send(null);
	var resp2 = ajax.responseText;
	resp2 = resp2.split('|');
	
	if(resp2.length > 1){
		var resp3 = resp2[1].split(';');
		for(k = 0 ; k < resp3.length ; k++)
			vl_alert = vl_alert + resp3[k]+'\n\n';
	}
	
	if(resp2[0] == 'ALERTA_NO_TIENE_STOCK'){
		set_values_producto(valores, record);
		alert('Este producto no tiene stock disponible para facturar.\nSin embargo, usted está autorizado para facturar sin stock.');
	}else if(resp2[0] == 'NO_TIENE_STOCK'){
		alert('Este producto no tiene stock disponible para facturar.\nUsted NO está autorizado para facturar sin stock.');
		set_vacio_producto(record);
	}else if(resp2[0] == 'ALERTA_MAYOR_CANTIDAD'){
		set_values_producto(valores, record);
		alert('La cantidad ingresada es mayor a la cantidad en stock.\nSin embargo, usted está autorizado para facturar sin stock.');
	}else if(resp2[0] == 'MAYOR_CANTIDAD'){
		alert('La cantidad ingresada es mayor a la cantidad en stock.\nUsted NO está autorizado para facturar sin stock., Favor de ingresar una cantidad correcta');
		document.getElementById('CANTIDAD_' + record).value = 0;
		set_values_producto(valores, record);
	}else if(resp2[0] == 'ALRT_NO_STOCK_COMP'){
		set_values_producto(valores, record);
		alert('No hay suficiente stock para facturar el '+vl_cod_producto_value+' ya que es un equipo compuesto.\n\nLas partes de este producto compuesto que no tiene stock suficiente son:\n\n'+vl_alert+'Sin embargo, usted está autorizado para facturar sin stock.');
	}else if(resp2[0] == 'NO_STOCK_COMP'){
		alert('No hay suficiente stock para facturar el '+vl_cod_producto_value+' ya que es un equipo compuesto.\n\nLas partes de este producto compuesto que no tiene stock suficiente son:\n\n'+vl_alert+'Usted NO está autorizado para facturar sin stock., Favor de ingresar una cantidad correcta.');
		document.getElementById('CANTIDAD_' + record).value = 0;
		set_values_producto(valores, record);
	}else{
		set_values_producto(valores, record);
	}
}

function valida_cantidad(ve_control){
	var vl_record = get_num_rec_field(ve_control.id);
	var vl_cod_item_doc = document.getElementById('COD_ITEM_DOC_' + vl_record).value;
	var vl_cod_item_fac = document.getElementById('COD_ITEM_FACTURA_' + vl_record).value;
	var vl_ws_origen = document.getElementById('WS_ORIGEN_0').value;
	var vl_alert = '';
	var vl_cantidad_total = 0;
	
	var vl_cod_producto = get_value('COD_PRODUCTO_'+vl_record);
	var aTR = get_TR('ITEM_FACTURA');
	for (var k = 0; k < aTR.length; k++){
		var vl_rec = get_num_rec_field(aTR[k].id);
		var vl_producto_it = get_value('COD_PRODUCTO_'+vl_rec);
		if(vl_cod_producto == vl_producto_it){
			var vl_cantidad = get_value('CANTIDAD_'+vl_rec);
			if(vl_cantidad == '')
				vl_cantidad = 0;
			
			vl_cantidad_total = parseInt(vl_cantidad_total) + parseInt(vl_cantidad);
		}
	}
	
	if(vl_ws_origen == 'BODEGA' || vl_ws_origen == 'COMERCIAL' || vl_ws_origen == 'RENTAL'){
		//Este valida el stock por web service
		var vl_cod_oc = document.getElementById('NRO_ORDEN_COMPRA_0').innerHTML;
		var vl_ajax = nuevoAjax();
		vl_ajax.open("GET", "../factura/TODOINOX/ajax_valida_cantidad.php?cantidad="+vl_cantidad_total+"&cod_item_doc="+vl_cod_item_doc+"&cod_item_factura="+vl_cod_item_fac+"&cod_orden_compra="+vl_cod_oc+"&ws_origen="+vl_ws_origen+"&cod_producto="+vl_cod_producto, false);
		vl_ajax.send(null);		
		var resp = vl_ajax.responseText;
		resp = resp.split('|');
		
		if(resp[0] == 'STOCK'){
			document.getElementById('CANTIDAD_' + vl_record).focus();
			alert('La cantidad ingresada es mayor a la cantidad en stock.\nUsted NO está autorizado para facturar sin stock., Favor de ingresar una cantidad correcta');
			document.getElementById('CANTIDAD_' + vl_record).value = 0;
			return false;
		}
		
		if(resp[0] == 'ES_MAYOR'){
			document.getElementById('CANTIDAD_' + vl_record).focus();
			alert('No puede ingresar una cantidad superior a la cantidad indicada en la orden de compra.\n\nCantidad indicada según OC: '+resp[1]);
			document.getElementById('CANTIDAD_' + vl_record).value = 0;
			return false;
		}
	}else{
		//Este valida el stock sin web service
		if(vl_cod_producto != ''){
			var vl_ajax = nuevoAjax();
			vl_ajax.open("GET", "../factura/TODOINOX/ajax_valida_stock.php?cod_producto="+vl_cod_producto+"&cantidad="+vl_cantidad_total, false);
			vl_ajax.send(null);		
			var resp = vl_ajax.responseText;
			resp = resp.split('|');
			if(resp.length > 1){
				var resp2 = resp[1].split(';');
				for(k = 0 ; k < resp2.length ; k++)
					vl_alert = vl_alert + resp2[k]+'\n\n';
			}
			
			if(resp[0] == 'ALERTA_NO_TIENE_STOCK'){
				alert('Este producto no tiene stock disponible para facturar.\nSin embargo, usted está autorizado para facturar sin stock.');
				return true;
			}else if(resp[0] == 'NO_TIENE_STOCK'){
				document.getElementById('CANTIDAD_' + vl_record).focus();
				alert('Este producto no tiene stock disponible para facturar.\nUsted NO está autorizado para facturar sin stock.');
				document.getElementById('CANTIDAD_' + vl_record).value = 0;
				return false;
			}else if(resp[0] == 'ALERTA_MAYOR_CANTIDAD'){
				alert('Este item se le esta ingresando una cantidad mayor a lo disponible');
				return true;
			}else if(resp[0] == 'MAYOR_CANTIDAD'){
				document.getElementById('CANTIDAD_' + vl_record).focus();
				alert('Este item se le esta ingresando una cantidad mayor a lo disponible, Favor de ingresar una cantidad correcta');
				document.getElementById('CANTIDAD_' + vl_record).value = 0;
				return false;
			}else if(resp[0] == 'ALRT_NO_STOCK_COMP'){
				alert('No hay suficiente stock para facturar el '+vl_cod_producto+' ya que es un equipo compuesto.\n\nLas partes de este producto compuesto que no tiene stock suficiente son:\n\n'+vl_alert+'Sin embargo, usted está autorizado para facturar sin stock.');
				return true;
			}else if(resp[0] == 'NO_STOCK_COMP'){
				document.getElementById('CANTIDAD_' + vl_record).focus();
				alert('No hay suficiente stock para facturar el '+vl_cod_producto+' ya que es un equipo compuesto.\n\nLas partes de este producto compuesto que no tiene stock suficiente son:\n\n'+vl_alert+'Usted NO está autorizado para facturar sin stock., Favor de ingresar una cantidad correcta');
				document.getElementById('CANTIDAD_' + vl_record).value = 0;
				return false;
			}
		}
	}
		
}

function set_vacio_producto(record) {
	set_value('COD_PRODUCTO_' + record, '', '');
	set_value('NOM_PRODUCTO_' + record, '', '');
	set_value('PRECIO_' + record, '', '');
}

function valida_nro_oc(){
	if(valida_factura_oc() == false){
		alert('No se puede facturar 2 veces la misma OC a un mismo cliente');
		document.getElementById('NRO_ORDEN_COMPRA_0').value = '';
	}		
}	

function valida_factura_oc(){
	var vl_cod_factura	= document.getElementById('COD_FACTURA_0').value;
	var vl_nro_oc		= document.getElementById('NRO_ORDEN_COMPRA_0').value;
	var vl_cod_empresa	= document.getElementById('COD_EMPRESA_0').value;	
	
	var vl_ajax	= nuevoAjax();
	vl_ajax.open("GET", "../factura/TODOINOX/ajax_valida_factura_oc.php?nro_oc="+vl_nro_oc+"&cod_empresa="+vl_cod_empresa+"&cod_factura="+vl_cod_factura, false);
	vl_ajax.send(null);		
	var resp = vl_ajax.responseText;
	if(resp == 'EXISTE')
		return false;
	else
		return true;
		
}

function select_1_empresa(valores, record){
	if(valores[1] != '7'){
		set_values_empresa(valores, record);
		precio_prod_empresa();
		centro_costo();
		valida_nro_oc();
	}else{
		alert('Usted no puede generar una factura para: COMERCIAL TODOINOX LTDA.\n\nFavor asegúrese de indicar el cliente correcto de esta factura');
		set_value('COD_EMPRESA_' + record, '', '');
		set_value('RUT_' + record, '', '');
		set_value('ALIAS_' + record, '', '');
		set_value('NOM_EMPRESA_' + record, '', '');
		set_value('DIG_VERIF_' + record, '', '');
		set_value('DIRECCION_FACTURA_' + record, '', '');
		set_value('DIRECCION_DESPACHO_' + record, '', '');
		set_value('GIRO_' + record, '', '');
		set_value('SUJETO_A_APROBACION_' + record, '', '');
		set_drop_down_vacio('COD_SUCURSAL_FACTURA_' + record);
		set_drop_down_vacio('COD_SUCURSAL_DESPACHO_' + record);
		set_drop_down_vacio('COD_PERSONA_' + record);
		set_value('MAIL_CARGO_PERSONA_' + record, '', '');
		set_value('COD_CUENTA_CORRIENTE_' + record, '', '');
		set_value('NOM_CUENTA_CORRIENTE_' + record, '', '');
		set_value('NRO_CUENTA_CORRIENTE_' + record, '', '');
	}
	/*
	if(valores[1] == '1' || valores[1] == '37' || valores[1] == '38'){ //COMERCIAL //BODEGA //SERVINDUS
		document.getElementById('COD_FORMA_PAGO_0').value = 7;
		document.getElementById('COD_FORMA_PAGO_0').disabled=true;
	}else if(valores[1] == '44'){//CATERING
		document.getElementById('COD_FORMA_PAGO_0').value = 19;
		document.getElementById('COD_FORMA_PAGO_0').disabled=true;
	}else{
		document.getElementById('COD_FORMA_PAGO_0').value = 2;
		document.getElementById('COD_FORMA_PAGO_0').disabled=false;
	}*/	
}

function precio_prod_empresa(){

	var aTR = get_TR('ITEM_FACTURA');
	for (var i = 0; i < aTR.length; i++){
		var cod_prod = document.getElementById('COD_PRODUCTO_' + i).value;
		var cod_empresa = document.getElementById('COD_EMPRESA_0').value;
		var cantidad = document.getElementById('CANTIDAD_'+ i).value;
		
		var ajax = nuevoAjax();
		ajax.open("GET", "../factura/TODOINOX/ajax_producto_precio.php?cod_producto="+cod_prod+"&cod_empresa="+cod_empresa, false);
		ajax.send(null);
		var resp = URLDecode(ajax.responseText);
		
		precio = resp.replace(".","");
		set_value('PRECIO_'+i, resp, resp);
		set_value('PRECIO_H_'+i, precio, precio);
		document.getElementById('PRECIO_' + i).innerHTML = resp;
		computed(i, 'TOTAL');
		
		
	} 
}

function validate(){

	var cc_cliente		= document.getElementById('CENTRO_COSTO_CLIENTE_0').value;
	var vl_no_tiene_ccc	= document.getElementById('NO_TIENE_CC_CLIENTE_0').checked;


	var vl_aTR = get_TR('ITEM_FACTURA');
	
	var cod_estado_doc_sii_value = get_value('COD_ESTADO_DOC_SII_0');
	
	if(to_num(cod_estado_doc_sii_value) == 1){//EMITIDA
		for(k=0 ; k < vl_aTR.length ; k++){
			var vl_rec			= get_num_rec_field(vl_aTR[k].id);
			var vl_cod_producto = document.getElementById('COD_PRODUCTO_'+vl_rec);
			
			if(vl_cod_producto){
				if(valida_cantidad(vl_cod_producto) == false)
					return false;
			}	
		}
	}
	
	var vl_count = 0;
	for(j=0 ; j < vl_aTR.length ; j++){
		var vl_cantidad = get_value('CANTIDAD_'+j);
		if(vl_cantidad != 0)
			vl_count++;
	}
	if(vl_count == 0){
		alert('Debe ingresar al menos 1 item antes de grabar.');
		return false;
	}
	
	//Validacion de los item (Segun parametro 29)
	var ajax = nuevoAjax();
	ajax.open("GET", "../factura/TODOINOX/ajax_valida_item.php?cantidad="+vl_count, false);
	ajax.send(null);
	var resp = ajax.responseText;
	
	if(resp == 'ALERTA'){
		alert('ERROR: No puede ingresar mas de 18 item en una Factura.');
		return false;
	}
	
	if (to_num(cod_estado_doc_sii_value) == 4){	
		var motivo_anula = document.getElementById('MOTIVO_ANULA_0');
		if (motivo_anula.value == '') {
			alert('Debe ingresar el motivo de Anulación antes de grabar.');
			motivo_anula.focus();
			return false;
		}
	}


	var vl_rut			= document.getElementById('RUT_0').value;

	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_valida_sodexo.php?ve_rut="+vl_rut, false);
	ajax.send(null);
	var resp = ajax.responseText;
	
	if(resp == 'ES_SODEXO' && cc_cliente == '' && vl_no_tiene_ccc == false){
		alert('Debe ingresar un Centro Costo Cliente para esta empresa.');
		document.getElementById('CENTRO_COSTO_CLIENTE_0').focus();
		return false;
	}


	var porc_dscto1 = get_value('PORC_DSCTO1_0');
	var monto_dscto1 = get_value('MONTO_DSCTO1_0');
	var sum_total = document.getElementById('SUM_TOTAL_H_0');		
	var porc_dscto_max = document.getElementById('PORC_DSCTO_MAX_0');
	if (sum_total.value=='') sum_total.value = 0;
	
	var cod_empresa = get_value('COD_EMPRESA_0');
	var ajax = nuevoAjax();
	ajax.open("GET", "../factura/TODOINOX/ajax_valida_dscto.php?cod_empresa="+cod_empresa, false);
	ajax.send(null);
	var resp = ajax.responseText;
	
  	var resp = resp.split('|');
  	var porc_dscto = resp[0];
  	var tabla = resp[1];
  	 
  	 var porc_dscto1 = parseFloat(findAndReplace(porc_dscto1, ',', '.'));
  	 var porc_dscto =  parseFloat(findAndReplace(porc_dscto, ',', '.'));
  	 
  	 if(porc_dscto1>porc_dscto){
  	 	alert('El descuento es mayor al permitido (máximo '+porc_dscto+'%) para '+tabla);
  	 	return false;
  	 }
	
	var aTR = get_TR('BITACORA_FACTURA');
	for (var i = 0; i < aTR.length; i++){
		var tiene_compromiso = document.getElementById('TIENE_COMPROMISO_' + i).checked;
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
	
	var aTR = get_TR('REFERENCIAS');
	for(i = 0; i < aTR.length ; i++){
	 	var vl_rec = get_num_rec_field(aTR[i].id);
	 	
	 	if(document.getElementById('COD_TIPO_REFERENCIA_'+vl_rec).value == 1){//HEM
	 		if(document.getElementById('FECHA_REFERENCIA_'+vl_rec).value == ''){
		 		alert('Debe ingresar una fecha para el tipo de referencia HEM');
		 		document.getElementById('FECHA_REFERENCIA_'+vl_rec).focus();
		 		return false;
		 	}	
	 	}else if(document.getElementById('COD_TIPO_REFERENCIA_'+vl_rec).value == 2){//HES
	 		if(document.getElementById('FECHA_REFERENCIA_'+vl_rec).value == ''){
		 		alert('Debe ingresar una fecha para el tipo de referencia HES');
		 		document.getElementById('FECHA_REFERENCIA_'+vl_rec).focus();
		 		return false;
		 	}	
	 	}
	 }
	 
	var vl_no_tiene_OC = document.getElementById('NO_TIENE_OC_0');
	var vl_nro_OC = document.getElementById('NRO_ORDEN_COMPRA_0').value;
	if (!vl_no_tiene_OC.checked && vl_nro_OC=='') {
		alert('Debe ingresar el nro OC');
		document.getElementById('NRO_ORDEN_COMPRA_0').focus();
		return false;
	}
	
	 if (document.getElementById('COD_FORMA_PAGO_0')){
		var cod_forma_pago = document.getElementById('COD_FORMA_PAGO_0').options[document.getElementById('COD_FORMA_PAGO_0').selectedIndex].value;
		var nom_forma_pago_otro = document.getElementById('NOM_FORMA_PAGO_OTRO_0').value;
		
		if (parseFloat(cod_forma_pago) == 1 && nom_forma_pago_otro == ''){
			alert ('Debe ingresar la Descripción de la forma de pago seleccionada.');
			document.getElementById('NOM_FORMA_PAGO_OTRO_0').focus();
			return false;
		}
	}
	
	var vl_no_tiene_OC = document.getElementById('NO_TIENE_OC_0');
	var vl_orden_compra = document.getElementById('NRO_ORDEN_COMPRA_0').value;
	var vl_fecha_orden_compra = document.getElementById('FECHA_ORDEN_COMPRA_CLIENTE_0').value;
		
	if (!vl_no_tiene_OC.checked) {
		if(vl_orden_compra == ''){
			alert('Debe Ingresar Orden Compra Cliente');
			return false;
		}
		
		if(vl_fecha_orden_compra == ''){
			alert('Debe Ingresar Fecha Orden Compra Cliente');
			return false;
		}
	}

	return true;
}

function centro_costo() {
	var vl_cod_centro_costo = document.getElementById('COD_CENTRO_COSTO_0');
	vl_cod_centro_costo.length = 0;
	
	var vl_rut = document.getElementById('RUT_0').value;
	if (vl_rut==91462001) {	// COMERCIAL
		var vl_option = new Option('', ''); 
		vl_cod_centro_costo.appendChild(vl_option);
		 
		var vl_option = new Option('COMERCIAL BIGGI RENTAL', '010'); 
		vl_cod_centro_costo.appendChild(vl_option);

		var vl_option = new Option('COMERCIAL BIGGI SODEXO', '011'); 
		vl_cod_centro_costo.appendChild(vl_option);

		var vl_option = new Option('COMERCIAL BIGGI COMPASS', '012'); 
		vl_cod_centro_costo.appendChild(vl_option);

		var vl_option = new Option('COMERCIAL BIGGI CDR', '013'); 
		vl_cod_centro_costo.appendChild(vl_option);

		var vl_option = new Option('COMERCIAL BIGGI', '014'); 
		vl_cod_centro_costo.appendChild(vl_option);
	}
	else if (vl_rut==80112900) {	// BODEGA
		var vl_option = new Option('BODEGA BIGGI', '015'); 
		vl_cod_centro_costo.appendChild(vl_option);
	}
	else if (vl_rut==77773650) {	// SERVINDUS
		var vl_option = new Option('SERVINDUS', '016'); 
		vl_cod_centro_costo.appendChild(vl_option);
	}
	else {
		var vl_option = new Option('VENTAS TODOINOX', '017'); 
		vl_cod_centro_costo.appendChild(vl_option);
	}
}

function valida_dsct(){

	porc_dscto 	= document.getElementById('PORC_DSCTO1_0').value;
	monto_dscto = document.getElementById('MONTO_DSCTO1_0').value;
	cod_empresa = document.getElementById('COD_EMPRESA_0').value;

	var ajax = nuevoAjax();
		ajax.open("GET", "../factura/TODOINOX/valida_dscto.php?porc_dscto="+porc_dscto+"&monto_dscto="+monto_dscto+"&cod_empresa"+cod_empresa, false);
		ajax.send(null);
		var resp = URLDecode(ajax.responseText);

}
function otros_sistemas(){

	var comercial = document.getElementById('comercial_biggi');
	var bodega = document.getElementById('bodega_biggi');
	var servindus = document.getElementById('servindus');
	
	if (document.getElementById('otros_sistem').checked){
		comercial.style.display = '';
		bodega.style.display = '';
		servindus.style.display = '';
	}else{
		comercial.style.display = 'none';
		bodega.style.display = 'none';
		servindus.style.display = 'none';
	}
			
}

function dlg_crear_desde(ve_prompt, ve_valor){
	
	var url = "../../../trunk/appl/factura/TODOINOX/dlg_crear_desde.php?prompt="+URLEncode(ve_prompt)+"&valor="+URLEncode(ve_valor);
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 360,
		 width: 360,
		 scrollable: false,
		 onClose: function(){ 
		 	var returnVal = this.returnValue;
		 	if (returnVal == null)		
				return false;		
			else {
			 	var input = document.createElement("input");
				input.setAttribute("type", "hidden");
				input.setAttribute("name", "b_crear_desde_x");
				input.setAttribute("id", "b_crear_desde_x");
				document.getElementById("output").appendChild(input);
				
				document.getElementById('wo_hidden').value = returnVal;
				document.output.submit();
		   		
		   		return true;
		   	}	
		}
	});	
}

function valida_ct_x_facturar(ve_campo) {
	ws_origen = document.getElementById('WS_ORIGEN_0').value;
	// valida solo si la GD es creada desde y que no sea desde WS
	if(ws_origen == ''){
		var cod_doc = to_num(document.getElementById('COD_DOC_0').innerHTML);
		
		if (cod_doc != 0){
			var record = get_num_rec_field(ve_campo.id);
			var cant_por_facturar = to_num(document.getElementById('CANTIDAD_POR_FACTURAR_' + record).innerHTML);
			var cant_ingresada = to_num(ve_campo.value);
				if (parseFloat(cant_por_facturar) < parseFloat(cant_ingresada)) {
					alert('El valor ingresado no puede ser mayor que la cantidad "por Facturar": '+ number_format(cant_por_facturar, 1, ',', '.'));
					return number_format(cant_por_facturar, 1, ',', '.');
				}
				else
					return ve_campo.value;
		}
		else
			return ve_campo.value;
	}
	return ve_campo.value;		
}

function change_forma_pago(){
	var vl_cod_forma_pago = document.getElementById('COD_FORMA_PAGO_0').value;
	
	if(vl_cod_forma_pago == 1 || vl_cod_forma_pago == 19 || vl_cod_forma_pago == 27)
		document.getElementById('NOM_FORMA_PAGO_OTRO_0').type= 'text';
	else
		document.getElementById('NOM_FORMA_PAGO_OTRO_0').type= 'hidden';
}
function change_option(){
	var aTR = get_TR('FA_DOCS');
	for(i=0 ; i < aTR.length ; i++){
		var vl_record = get_num_rec_field(aTR[i].id);
		if(document.getElementById('D_ES_OC_'+ vl_record).checked)
			document.getElementById('D_VALUE_OPTION_'+vl_record).value = 'S';
		else
			document.getElementById('D_VALUE_OPTION_'+vl_record).value = 'N';	
	}
}

function dlg_despliega_gd(){
	
	vl_cod_guia_despacho = document.getElementById('NRO_GUIA_DESPACHO_H_0').value;
	vl_cod_empresa = document.getElementById('COD_EMPRESA_0').value;
	
	if(vl_cod_empresa == ''){
		alert('Debe ingresar una empresa.');
		return;
	}
	
	var url = "../factura/TODOINOX/dlg_despliega_gd.php?cod_guia_despacho="+vl_cod_guia_despacho+"&cod_empresa="+vl_cod_empresa;
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 360,
		 width: 910,
		 scrollable: false,
		 onClose: function(){ 
		 	var returnVal = this.returnValue;
		 	if (returnVal == null)		
				return false;		
			else {
			 	returnVal = returnVal.split('|');
				document.getElementById('NRO_GUIA_DESPACHO_0').innerHTML = returnVal[1];
				document.getElementById('NRO_GUIA_DESPACHO_H_0').value = returnVal[0];
		   		return true;
		   	}	
		}
	});		
	
}
function f_valida_oc(){
	var vl_no_tiene_OC = document.getElementById('NO_TIENE_OC_0');
		
	if (vl_no_tiene_OC.checked) {
		document.getElementById('NRO_ORDEN_COMPRA_0').value = '';
		document.getElementById('NRO_ORDEN_COMPRA_0').readOnly = true;
		
		document.getElementById('FECHA_ORDEN_COMPRA_CLIENTE_0').value = '';
		document.getElementById('FECHA_ORDEN_COMPRA_CLIENTE_0').readOnly = true;
				
	}
	if (!vl_no_tiene_OC.checked) {
		document.getElementById('NRO_ORDEN_COMPRA_0').readOnly = false;
		document.getElementById('FECHA_ORDEN_COMPRA_CLIENTE_0').readOnly = false;
	}
}