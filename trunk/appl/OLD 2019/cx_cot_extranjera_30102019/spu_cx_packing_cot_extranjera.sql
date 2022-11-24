CREATE PROCEDURE spu_cx_packing_cot_extranjera (@ve_operacion						VARCHAR(20)
											   ,@ve_cod_cx_packing_cot_extranjera	NUMERIC(10)
											   ,@ve_cod_cx_cot_extranjera			NUMERIC=NULL
											   ,@ve_nom_container					varchar(100)=NULL
											   ,@ve_cant							NUMERIC=NULL)			   
AS
BEGIN
	IF (@ve_operacion='INSERT') BEGIN
		INSERT INTO CX_PACKING_COT_EXTRANJERA VALUES(@ve_cod_cx_cot_extranjera
													,@ve_nom_container
													,@ve_cant)		  
	END 
	IF (@ve_operacion='UPDATE') BEGIN
		UPDATE CX_PACKING_COT_EXTRANJERA
		SET NOM_CONTAINER	= @ve_nom_container,
			CANT			= @ve_cant
		WHERE COD_CX_PACKING_COT_EXTRANJERA = @ve_cod_cx_packing_cot_extranjera
	END
	IF (@ve_operacion='DELETE') BEGIN
		DELETE CX_PACKING_COT_EXTRANJERA
		WHERE COD_CX_PACKING_COT_EXTRANJERA = @ve_cod_cx_packing_cot_extranjera
	END		      
END