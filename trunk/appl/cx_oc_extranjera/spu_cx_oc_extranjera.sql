ALTER PROCEDURE [dbo].[spu_cx_oc_extranjera](@ve_operacion						VARCHAR(20),
											  @ve_cod_cx_oc_extranjera			NUMERIC=NULL,
											  @ve_fecha_cx_oc_extranjera		DATETIME=NULL,
											  @ve_cod_cx_estado_oc_extranjera	NUMERIC(10)=NULL,
											  @ve_cod_proveedor_ext				NUMERIC(10)=NULL,
											  @ve_cod_cx_contacto_proveedor_ext NUMERIC(10)=NULL,
											  @ve_cod_cx_clausula_compra		NUMERIC(10)=NULL,
											  @ve_cod_cx_cot_extranjera			NUMERIC(10)=NULL,
											  @ve_cod_cx_moneda					NUMERIC(10)=NULL,
											  @ve_cod_cx_puerto_arribo			NUMERIC(10)=NULL,
											  @ve_cod_cx_puerto_salida			NUMERIC(10)=NULL,
											  @ve_cod_cx_termino_pago			NUMERIC(10)=NULL,
											  @ve_cod_usuario					NUMERIC(3)=NULL,
											  @ve_correlativo_oc				VARCHAR(100)=NULL,
											  @ve_delivery_date					DATETIME=NULL,
											  @ve_referencia					VARCHAR(100)=NULL,
											  @ve_observaciones					VARCHAR(2200)=NULL,
											  @ve_packing						VARCHAR(100)=NULL,
											  @ve_subtotal						NUMERIC(15,2)=NULL,
											  @ve_monto_embalaje				NUMERIC(15,2)=NULL,
											  @ve_monto_flete_interno			NUMERIC(15,2)=NULL,
											  @ve_porc_descuento				NUMERIC(15,2)=NULL,
											  @ve_monto_descuento				NUMERIC(15,2)=NULL,
											  @ve_monto_total					NUMERIC(15,2)=NULL,
											  @ve_alias							VARCHAR(100)=NULL,
											  @ve_eta_date						DATETIME=NULL)			
AS
BEGIN
	DECLARE
		@vl_max_numero NUMERIC(18,0)
	
	SELECT @vl_max_numero = ISNULL(MAX(CORRELATIVO_OC_NUMERO),0)
	FROM CX_OC_EXTRANJERA C, PROVEEDOR_EXT P
	WHERE P.ALIAS_PROVEEDOR_EXT = @ve_alias
	AND YEAR(C.FECHA_CX_OC_EXTRANJERA) = YEAR(@ve_fecha_cx_oc_extranjera)
	AND C.COD_PROVEEDOR_EXT = P.COD_PROVEEDOR_EXT
	
	set @vl_max_numero = @vl_max_numero + 1
	
	IF (@ve_operacion='INSERT') BEGIN
		INSERT INTO CX_OC_EXTRANJERA (FECHA_REGISTRO
									 ,FECHA_CX_OC_EXTRANJERA
									 ,COD_CX_ESTADO_OC_EXTRANJERA
									 ,COD_PROVEEDOR_EXT
									 ,COD_CX_CONTACTO_PROVEEDOR_EXT
									 ,COD_CX_CLAUSULA_COMPRA
									 ,COD_CX_COT_EXTRANJERA
									 ,COD_CX_MONEDA
									 ,COD_CX_PUERTO_ARRIBO
									 ,COD_CX_PUERTO_SALIDA
									 ,COD_CX_TERMINO_PAGO
									 ,COD_USUARIO
									 ,CORRELATIVO_OC
									 ,DELIVERY_DATE
									 ,REFERENCIA
									 ,OBSERVACIONES
									 ,PACKING
									 ,SUBTOTAL
									 ,MONTO_EMBALAJE
									 ,MONTO_FLETE_INTERNO
									 ,PORC_DESCUENTO
									 ,MONTO_DESCUENTO
									 ,MONTO_TOTAL
									 ,CORRELATIVO_OC_NUMERO
									 ,CORRELATIVO_OC_LETRA
									 ,ETA_DATE)
							   VALUES(GETDATE()
									  ,@ve_fecha_cx_oc_extranjera
									  ,@ve_cod_cx_estado_oc_extranjera
									  ,@ve_cod_proveedor_ext
									  ,@ve_cod_cx_contacto_proveedor_ext
									  ,@ve_cod_cx_clausula_compra
									  ,@ve_cod_cx_cot_extranjera
									  ,@ve_cod_cx_moneda
									  ,@ve_cod_cx_puerto_arribo
									  ,@ve_cod_cx_puerto_salida
									  ,@ve_cod_cx_termino_pago
									  ,@ve_cod_usuario
									  ,@ve_alias + ' ' + CONVERT(VARCHAR,@vl_max_numero,103) + '/' + CONVERT(VARCHAR, YEAR(@ve_fecha_cx_oc_extranjera), 103)
									  ,@ve_delivery_date
									  ,@ve_referencia
									  ,@ve_observaciones
									  ,@ve_packing
									  ,@ve_subtotal
									  ,@ve_monto_embalaje
									  ,@ve_monto_flete_interno
									  ,@ve_porc_descuento
									  ,@ve_monto_descuento
									  ,@ve_monto_total
									  ,@vl_max_numero
									  ,''
									  ,@ve_eta_date)		  
	END 
	
	ELSE IF (@ve_operacion='UPDATE') BEGIN
		UPDATE CX_OC_EXTRANJERA
		SET	FECHA_CX_OC_EXTRANJERA			= @ve_fecha_cx_oc_extranjera,
			COD_CX_ESTADO_OC_EXTRANJERA		= @ve_cod_cx_estado_oc_extranjera,
			COD_PROVEEDOR_EXT				= @ve_cod_proveedor_ext,
			COD_CX_CONTACTO_PROVEEDOR_EXT	= @ve_cod_cx_contacto_proveedor_ext,
			COD_CX_CLAUSULA_COMPRA			= @ve_cod_cx_clausula_compra,
			COD_CX_COT_EXTRANJERA			= @ve_cod_cx_cot_extranjera,
			COD_CX_MONEDA					= @ve_cod_cx_moneda,
			COD_CX_PUERTO_ARRIBO			= @ve_cod_cx_puerto_arribo,
			COD_CX_PUERTO_SALIDA			= @ve_cod_cx_puerto_salida,
			COD_CX_TERMINO_PAGO				= @ve_cod_cx_termino_pago,
			COD_USUARIO						= @ve_cod_usuario,
			CORRELATIVO_OC					= @ve_correlativo_oc,
			DELIVERY_DATE					= @ve_delivery_date,
			REFERENCIA						= @ve_referencia,
			OBSERVACIONES					= @ve_observaciones,
			PACKING							= @ve_packing,
			SUBTOTAL						= @ve_subtotal,
			MONTO_EMBALAJE					= @ve_monto_embalaje,
			MONTO_FLETE_INTERNO				= @ve_monto_flete_interno,
			PORC_DESCUENTO					= @ve_porc_descuento,
			MONTO_DESCUENTO					= @ve_monto_descuento,
			MONTO_TOTAL						= @ve_monto_total,
			ETA_DATE						= @ve_eta_date
		WHERE COD_CX_OC_EXTRANJERA = @ve_cod_cx_oc_extranjera
	END			      
	ELSE IF (@ve_operacion='DUPLICAR') BEGIN
		DECLARE
			@i			NUMERIC,
			@ve_cant	NUMERIC,
			@vl_letra	VARCHAR(1),
			@vl_cod		NUMERIC
			
		SET @ve_cant = @ve_cod_cx_estado_oc_extranjera	
			
		SET @i = 2
		WHILE @i <= @ve_cant BEGIN
			SET @vl_letra = CHAR(64 + @i)
			INSERT INTO CX_OC_EXTRANJERA (FECHA_REGISTRO
										 ,FECHA_CX_OC_EXTRANJERA
										 ,COD_CX_ESTADO_OC_EXTRANJERA
										 ,COD_PROVEEDOR_EXT
										 ,COD_CX_CONTACTO_PROVEEDOR_EXT
										 ,COD_CX_CLAUSULA_COMPRA
										 ,COD_CX_COT_EXTRANJERA
										 ,COD_CX_MONEDA
										 ,COD_CX_PUERTO_ARRIBO
										 ,COD_CX_PUERTO_SALIDA
										 ,COD_CX_TERMINO_PAGO
										 ,COD_USUARIO
										 ,CORRELATIVO_OC
										 ,DELIVERY_DATE
										 ,REFERENCIA
										 ,OBSERVACIONES
										 ,PACKING
										 ,SUBTOTAL
										 ,MONTO_EMBALAJE
										 ,MONTO_FLETE_INTERNO
										 ,PORC_DESCUENTO
										 ,MONTO_DESCUENTO
										 ,MONTO_TOTAL
										 ,CORRELATIVO_OC_NUMERO
										 ,CORRELATIVO_OC_LETRA
										 ,ETA_DATE)
								 
								 SELECT  FECHA_REGISTRO
										 ,FECHA_CX_OC_EXTRANJERA
										 ,COD_CX_ESTADO_OC_EXTRANJERA
										 ,COD_PROVEEDOR_EXT
										 ,COD_CX_CONTACTO_PROVEEDOR_EXT
										 ,COD_CX_CLAUSULA_COMPRA
										 ,COD_CX_COT_EXTRANJERA
										 ,COD_CX_MONEDA
										 ,COD_CX_PUERTO_ARRIBO
										 ,COD_CX_PUERTO_SALIDA
										 ,COD_CX_TERMINO_PAGO
										 ,COD_USUARIO
										 ,CORRELATIVO_OC + @vl_letra
										 ,DELIVERY_DATE
										 ,REFERENCIA
										 ,OBSERVACIONES
										 ,PACKING
										 ,SUBTOTAL
										 ,MONTO_EMBALAJE
										 ,MONTO_FLETE_INTERNO
										 ,PORC_DESCUENTO
										 ,MONTO_DESCUENTO
										 ,MONTO_TOTAL
										 ,CORRELATIVO_OC_NUMERO
										 ,@vl_letra
										 ,ETA_DATE
								 FROM CX_OC_EXTRANJERA
								 WHERE COD_CX_OC_EXTRANJERA = @ve_cod_cx_oc_extranjera
		
			SET @vl_cod = @@IDENTITY 
			INSERT INTO CX_ITEM_OC_EXTRANJERA(COD_CX_OC_EXTRANJERA
											  ,ORDEN
											  ,ITEM
											  ,COD_PRODUCTO
											  ,NOM_PRODUCTO
											  ,COD_EQUIPO_OC_EX
											  ,DESC_EQUIPO_OC_EX
											  ,CANTIDAD
											  ,PRECIO)
				
									  SELECT  @vl_cod 
											 ,ORDEN
											 ,ITEM
											 ,COD_PRODUCTO
											 ,NOM_PRODUCTO
											 ,COD_EQUIPO_OC_EX
											 ,DESC_EQUIPO_OC_EX
											 ,CANTIDAD
											 ,PRECIO
									  FROM CX_ITEM_OC_EXTRANJERA
									  WHERE COD_CX_OC_EXTRANJERA = @ve_cod_cx_oc_extranjera
		
			SET @i = @i + 1
		END	
		UPDATE CX_OC_EXTRANJERA
		SET CORRELATIVO_OC_LETRA = 'A',
		CORRELATIVO_OC = CORRELATIVO_OC + 'A'
		WHERE COD_CX_OC_EXTRANJERA = @ve_cod_cx_oc_extranjera
	END 
END