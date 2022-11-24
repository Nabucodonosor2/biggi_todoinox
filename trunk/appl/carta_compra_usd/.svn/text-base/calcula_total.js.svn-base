function calcula_total(){
	var usd = document.getElementById('CANT_COMPRA_USD_0').value;
	usd = usd.replace(',','.','g');
	var valor_usd = document.getElementById('TIPO_CAMBIO_USD_0').value;
	valor_usd = valor_usd.replace(',','.','g');
	var total =	roundNumber((valor_usd*usd),0);
	if(valor_usd != ''){
	document.getElementById('TOTAL_DEBITO_PESOS_0').value = total;
	}else{
	document.getElementById('CANT_COMPRA_USD_0').value = '';
	document.getElementById('TIPO_CAMBIO_USD_0').focus();
	my_alert('Ingrese primero Valor Dolar CLP');
	}
}