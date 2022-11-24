CREATE PROCEDURE spu_cx_cot_extranjera(@ve_operacion						VARCHAR(20)
									  ,@ve_cod_cx_cot_extranjera			NUMERIC=NULL
									  ,@ve_cod_cx_clausula_compra			NUMERIC(10)=NULL
									  ,@ve_cod_proveedor_ext				NUMERIC(10)=NULL
									  ,@ve_cod_cx_contacto_proveedor_ext	NUMERIC(10)=NULL
									  ,@ve_cod_cx_moneda					NUMERIC(10)=NULL
									  ,@ve_cod_cx_puerto_arribo				NUMERIC(10)=NULL
									  ,@ve_cod_cx_puerto_salida				NUMERIC(10)=NULL
									  ,@ve_cod_cx_termino_pago				NUMERIC(10)=NULL
									  ,@ve_cod_usuario						NUMERIC(10)=NULL
									  ,@ve_cod_cx_estado_cot_extranjera		NUMERIC(10)=NULL
									  ,@ve_correlativo_cot_extranjera		VARCHAR(100)=NULL
									  ,@ve_delivery_date					DATETIME=NULL
									  ,@ve_observaciones					VARCHAR(2000)=NULL
									  ,@ve_packing							VARCHAR(100)=NULL
									  ,@ve_referencia						VARCHAR(100)=NULL
									  ,@ve_fecha_cx_cot_extranjera			DATETIME=NULL
									  ,@ve_monto_total						NUMERIC(15,2)=NULL)			
AS
BEGIN
	IF (@ve_operacion='INSERT') BEGIN
		INSERT INTO CX_COT_EXTRANJERA (COD_CX_CLAUSULA_COMPRA
									 ,COD_PROVEEDOR_EXT
									 ,COD_CX_CONTACTO_PROVEEDOR_EXT
									 ,COD_CX_MONEDA
									 ,COD_CX_PUERTO_ARRIBO
									 ,COD_CX_PUERTO_SALIDA
									 ,COD_CX_TERMINO_PAGO
									 ,COD_USUARIO
									 ,COD_CX_ESTADO_COT_EXTRANJERA
									 ,CORRELATIVO_COT_EXTRANJERA
									 ,DELIVERY_DATE
									 ,OBSERVACIONES
									 ,PACKING
									 ,REFERENCIA
									 ,FECHA_REGISTRO
									 ,FECHA_CX_COT_EXTRANJERA
									 ,MONTO_TOTAL)
		
							   VALUES(@ve_cod_cx_clausula_compra
									  ,@ve_cod_proveedor_ext
									  ,@ve_cod_cx_contacto_proveedor_ext
									  ,@ve_cod_cx_moneda
									  ,@ve_cod_cx_puerto_arribo
									  ,@ve_cod_cx_puerto_salida
									  ,@ve_cod_cx_termino_pago
									  ,@ve_cod_usuario
									  ,@ve_cod_cx_estado_cot_extranjera
									  ,@ve_correlativo_cot_extranjera
									  ,@ve_delivery_date
									  ,@ve_observaciones
									  ,@ve_packing
									  ,@ve_referencia
									  ,getdate()
									  ,@ve_fecha_cx_cot_extranjera
									  ,@ve_monto_total)		  
	END 
	
	ELSE IF (@ve_operacion='UPDATE') BEGIN
		UPDATE CX_COT_EXTRANJERA
		SET	COD_PROVEEDOR_EXT					= @ve_cod_proveedor_ext
			 ,COD_CX_CONTACTO_PROVEEDOR_EXT		= @ve_cod_cx_contacto_proveedor_ext
			 ,COD_CX_MONEDA						= @ve_cod_cx_moneda 
			 ,COD_CX_PUERTO_ARRIBO				= @ve_cod_cx_puerto_arribo
			 ,COD_CX_PUERTO_SALIDA				= @ve_cod_cx_puerto_salida
			 ,COD_CX_TERMINO_PAGO				= @ve_cod_cx_termino_pago
			 ,COD_USUARIO						= @ve_cod_usuario
			 ,COD_CX_ESTADO_COT_EXTRANJERA		= @ve_cod_cx_estado_cot_extranjera
			 ,CORRELATIVO_COT_EXTRANJERA		= @ve_correlativo_cot_extranjera
			 ,DELIVERY_DATE						= @ve_delivery_date
			 ,OBSERVACIONES						= @ve_observaciones
			 ,PACKING							= @ve_packing
			 ,REFERENCIA						= @ve_referencia
			 ,MONTO_TOTAL						= @ve_monto_total
		WHERE COD_CX_COT_EXTRANJERA = @ve_cod_cx_cot_extranjera
	END			      
	IF (@ve_operacion='DELETE') BEGIN
		DELETE CX_COT_EXTRANJERA
		WHERE COD_CX_COT_EXTRANJERA = @ve_cod_cx_cot_extranjera
	END	
END