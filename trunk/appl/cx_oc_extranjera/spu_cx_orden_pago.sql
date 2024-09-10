alter PROCEDURE spu_cx_orden_pago(@ve_operacion			VARCHAR(20),
								   @ve_cod_cx_carta_op		NUMERIC=NULL,
								   @ve_cod_cx_oc_extranjera	NUMERIC=NULL,
								   @ve_fecha_cx_carta_op	DATETIME=NULL,
								   @ve_porc_pago			NUMERIC(10,2)=NULL,
								   @ve_monto_pago			NUMERIC(14,2)=NULL,
								   @ve_cod_est_cx_carta_op	NUMERIC=NULL,
								   @ve_nom_atencion			VARCHAR(100)=NULL,
								   @ve_nom_atencion_cc		VARCHAR(100)=NULL,
								   @ve_referencia			VARCHAR(100)=NULL,
								   @ve_atencion_carta		VARCHAR(1000)=NULL
								   )			
AS
BEGIN
	IF (@ve_operacion='INSERT') BEGIN
		 INSERT INTO CX_CARTA_OP(COD_CX_OC_EXTRANJERA
								,FECHA_CX_CARTA_OP
								,FECHA_REGISTRO
								,PORC_PAGO
								,MONTO_PAGO
								,NOM_ATENCION
								,NOM_ATENCION_CC
								,REFERENCIA
								,COD_ESTADO_CX_CARTA_OP
								,ATENCION_CARTA)
						  VALUES(@ve_cod_cx_oc_extranjera
						  		,@ve_fecha_cx_carta_op
						  		,GETDATE()
						  		,@ve_porc_pago
						  		,@ve_monto_pago
						  		,@ve_nom_atencion
						  		,@ve_nom_atencion_cc
						  		,@ve_referencia
						  		,@ve_cod_est_cx_carta_op
								,@ve_atencion_carta)		
	END 
	ELSE IF (@ve_operacion='UPDATE') BEGIN
		UPDATE CX_CARTA_OP
		SET FECHA_CX_CARTA_OP = @ve_fecha_cx_carta_op,
			PORC_PAGO = @ve_porc_pago,
			MONTO_PAGO = @ve_monto_pago,
			NOM_ATENCION = @ve_nom_atencion,
			NOM_ATENCION_CC = @ve_nom_atencion_cc,
			REFERENCIA = @ve_referencia,
			COD_ESTADO_CX_CARTA_OP = @ve_cod_est_cx_carta_op,
			ATENCION_CARTA = @ve_atencion_carta
		WHERE COD_CX_CARTA_OP = @ve_cod_cx_carta_op
	END			       
	ELSE IF (@ve_operacion='DELETE') BEGIN
		DELETE CX_CARTA_OP
		WHERE COD_CX_CARTA_OP = @ve_cod_cx_carta_op
	END
	ELSE IF (@ve_operacion='ANULA') BEGIN
		UPDATE CX_CARTA_OP
		SET COD_ESTADO_CX_CARTA_OP = 2 -- anulada
		WHERE COD_CX_CARTA_OP = @ve_cod_cx_carta_op
	END	
END