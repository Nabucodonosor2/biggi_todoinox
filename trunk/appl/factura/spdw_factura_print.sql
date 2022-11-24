ALTER PROCEDURE [dbo].[spdw_factura_print](@ve_cod_factura					numeric
									,@ve_print_dte					varchar(20)
									,@ve_cod_usuario_impresion		numeric
									,@ve_total_en_palabras			varchar(200))
AS
/*
retorna una select con todos los datos necesarios para realizar el print, este sp se preocuipa de si la
FA es de rental u otro
@ve_cod_factura : llave de la FA
@ve_print_dte : 'PRINT' indica que es para un print para factura sobre preimpreso (antiguas)
				'DTE' es para fractura electronica = !!! AUN NO ESTA HECHO EN ESTE SP !!!!
*/
BEGIN  
	declare @TEMPO TABLE    
	   (COD_FACTURA				numeric
		,NRO_FACTURA			numeric
		,FECHA_FACTURA			varchar(100)
		,COD_USUARIO_IMPRESION	numeric
		,NRO_ORDEN_COMPRA		varchar(100)
		,NRO_GUIAS_DESPACHO		varchar(100)
		,REFERENCIA				varchar(100)
		,NOM_EMPRESA			varchar(100)
		,GIRO					varchar(100)
		,RUT					numeric
		,DIG_VERIF				varchar(1)
		,DIRECCION				varchar(100)
		,TELEFONO				varchar(100)
		,FAX					varchar(100)
		,COD_DOC				numeric
		,SUBTOTAL				numeric
		,PORC_DSCTO1			numeric(5,2)
		,MONTO_DSCTO1			numeric
		,PORC_DSCTO2			numeric(5,2)
		,MONTO_DSCTO2			numeric
		,TOTAL_DSCTO			numeric
		,TOTAL_NETO				numeric
		,PORC_IVA				numeric(5,2)
		,MONTO_IVA				numeric
		,TOTAL_CON_IVA			numeric
		,RETIRADO_POR			varchar(100)
		,RUT_RETIRADO_POR		numeric
		,DIG_VERIF_RETIRADO_POR	varchar(1)
		,NOM_COMUNA				varchar(100)
		,NOM_CIUDAD				varchar(100)
		,NOM_FORMA_PAGO			varchar(100)
		,NOM_FORMA_PAGO_OTRO	varchar(100)
		,TOTAL_EN_PALABRAS		varchar(200)
		,HORA					varchar(100)
		,GENERA_SALIDA			varchar(1)
		,OBS					text
		,CANCELADA				varchar(1)
		,USUARIO_IMPRESION		numeric
		--items
		,ITEM					varchar(10)
		,CANTIDAD				numeric(10,2)
		,COD_PRODUCTO			varchar(30)
		,NOM_PRODUCTO			varchar(100)
		,PRECIO					numeric
		,TOTAL_FA				numeric
		)

	declare
		@K_FA_VENTA				numeric
		,@K_FA_RENTAL				numeric
		,@vl_cod_tipo_factura		numeric
		,@vl_total_neto				numeric
		,@vl_referencia				varchar(100)
		,@vl_count					numeric
		,@vl_nom_arriendo			varchar(100)
		,@vl_cod_arriendo			numeric
		,@vl_mes_ano				varchar(100)
		
	set @K_FA_RENTAL = 2

	select @vl_cod_tipo_factura = cod_tipo_factura
	from factura
	where cod_factura = @ve_cod_factura

	if (@vl_cod_tipo_factura = @K_FA_RENTAL) begin -- rental
		-- se imprime solo una linea !!
		select @vl_total_neto = isnull(sum(round(cantidad * precio, 0)), 0)
		from item_factura 
		where cod_factura = @ve_cod_factura

		select @vl_referencia = referencia
		from factura
		where cod_factura = @ve_cod_factura

		select @vl_count = count(distinct A.COD_ARRIENDO)
		from ITEM_FACTURA i, ARRIENDO a
		where i.COD_FACTURA = @ve_cod_factura
		  and TIPO_DOC = 'ARRIENDO'
		  and a.COD_ARRIENDO = i.COD_ITEM_DOC
						
		if (@vl_count = 1) begin
			select top 1 @vl_cod_arriendo = A.COD_ARRIENDO
			from ITEM_FACTURA I, ARRIENDO A
			where I.COD_FACTURA = @ve_cod_factura
			  and TIPO_DOC = 'ARRIENDO'
			  and a.COD_ARRIENDO = i.COD_ITEM_DOC
			  
			--select @vl_referencia = 'CONTRATOS DE ARRIENDO  DE ' + upper(m.nom_mes) + ' ' + CONVERT(varchar, year(getdate()))
			set @vl_mes_ano = SUBSTRING(@vl_referencia, len('CONTRATOS DE ARRIENDO  DE')-1, 100)
			set @vl_nom_arriendo = 'CONTRATO DE ARRIENDO Nº ' + convert(varchar, @vl_cod_arriendo) + ' ' + @vl_mes_ano
		end 
		else begin
			set @vl_nom_arriendo = @vl_referencia
		end
			

		insert into @TEMPO
		select F.COD_FACTURA
				,F.NRO_FACTURA
				,dbo.f_format_date(FECHA_FACTURA,3)FECHA_FACTURA
				,F.COD_USUARIO_IMPRESION
				,F.NRO_ORDEN_COMPRA
				,dbo.f_fa_nros_guia_despacho(F.COD_FACTURA) NRO_GUIAS_DESPACHO
				,@vl_referencia		
				,F.NOM_EMPRESA
				,F.GIRO
				,F.RUT
				,F.DIG_VERIF
				,F.DIRECCION
				,F.TELEFONO
				,F.FAX
				,F.COD_DOC
				,F.SUBTOTAL
				,F.PORC_DSCTO1
				,F.MONTO_DSCTO1
				,F.PORC_DSCTO2
				,F.MONTO_DSCTO2
				,F.MONTO_DSCTO1 + F.MONTO_DSCTO2
				,F.TOTAL_NETO
				,F.PORC_IVA
				,F.MONTO_IVA
				,F.TOTAL_CON_IVA
				,F.RETIRADO_POR
				,F.RUT_RETIRADO_POR
				,F.DIG_VERIF_RETIRADO_POR
				,COM.NOM_COMUNA
				,CIU.NOM_CIUDAD
				,FP.NOM_FORMA_PAGO
				,F.NOM_FORMA_PAGO_OTRO
				,@ve_total_en_palabras
				,convert(varchar(5), GETDATE(), 8)
				,F.GENERA_SALIDA
				,F.OBS
				,F.CANCELADA
				,@ve_cod_usuario_impresion				
				--items
				,'1'	 --ITEM
				,1		 --CANTIDAD
				,null	 --COD_PRODUCTO
				,@vl_nom_arriendo	--NOM_PRODUCTO
				,@vl_total_neto	--PRECIO
				,@vl_total_neto	--TOTAL_FA
		FROM 	FACTURA F left outer join COMUNA COM on F.COD_COMUNA = COM.COD_COMUNA, CIUDAD CIU, FORMA_PAGO FP
		WHERE 	F.COD_FACTURA = @ve_cod_factura
		  AND	CIU.COD_CIUDAD = F.COD_CIUDAD
		  AND	FP.COD_FORMA_PAGO = F.COD_FORMA_PAGO
	end
	else begin -- distinto de RENTAL
		insert into @TEMPO
		select F.COD_FACTURA
				,F.NRO_FACTURA
				,dbo.f_format_date(FECHA_FACTURA,3)FECHA_FACTURA
				,F.COD_USUARIO_IMPRESION
				,F.NRO_ORDEN_COMPRA
				,dbo.f_fa_nros_guia_despacho(F.COD_FACTURA) NRO_GUIAS_DESPACHO
				,F.REFERENCIA
				,F.NOM_EMPRESA
				,F.GIRO
				,F.RUT
				,F.DIG_VERIF
				,F.DIRECCION
				,F.TELEFONO
				,F.FAX
				,F.COD_DOC
				,F.SUBTOTAL
				,F.PORC_DSCTO1
				,F.MONTO_DSCTO1
				,F.PORC_DSCTO2
				,F.MONTO_DSCTO2
				,F.MONTO_DSCTO1 + F.MONTO_DSCTO2
				,F.TOTAL_NETO
				,F.PORC_IVA
				,F.MONTO_IVA
				,F.TOTAL_CON_IVA
				,F.RETIRADO_POR
				,F.RUT_RETIRADO_POR
				,F.DIG_VERIF_RETIRADO_POR
				,COM.NOM_COMUNA
				,CIU.NOM_CIUDAD
				,FP.NOM_FORMA_PAGO
				,F.NOM_FORMA_PAGO_OTRO
				,@ve_total_en_palabras
				,convert(varchar(5), GETDATE(), 8)
				,F.GENERA_SALIDA
				,F.OBS
				,F.CANCELADA
				,@ve_cod_usuario_impresion				
				--items
				,ITF.ITEM
				,ITF.CANTIDAD
				,ITF.COD_PRODUCTO
				,ITF.NOM_PRODUCTO
				,ITF.PRECIO
				,ITF.PRECIO * ITF.CANTIDAD
		FROM 	FACTURA F left outer join COMUNA COM on F.COD_COMUNA = COM.COD_COMUNA, ITEM_FACTURA ITF, CIUDAD CIU, FORMA_PAGO FP
		WHERE 	F.COD_FACTURA = @ve_cod_factura
		  AND	ITF.COD_FACTURA = F.COD_FACTURA
		  AND	CIU.COD_CIUDAD = F.COD_CIUDAD
		  AND	FP.COD_FORMA_PAGO = F.COD_FORMA_PAGO
	end

	select * from @TEMPO
END
