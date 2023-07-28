CREATE PROCEDURE spu_cx_packing_oc_extranjera  (@ve_operacion						VARCHAR(20)
											   ,@ve_cod_cx_packing_oc_extranjera	NUMERIC(10)
											   ,@ve_cod_cx_oc_extranjera			NUMERIC=NULL
											   ,@ve_nom_container					VARCHAR(100)=NULL
											   ,@ve_cant							NUMERIC=NULL)			   
AS
BEGIN
	IF(@ve_operacion='INSERT')BEGIN
		INSERT INTO CX_PACKING_OC_EXTRANJERA VALUES (@ve_cod_cx_oc_extranjera
													,@ve_nom_container
													,@ve_cant)		  
	END 
	IF (@ve_operacion='UPDATE') BEGIN
		UPDATE CX_PACKING_OC_EXTRANJERA
		SET NOM_CONTAINER	= @ve_nom_container,
			CANT			= @ve_cant
		WHERE COD_CX_PACKING_OC_EXTRANJERA = @ve_cod_cx_packing_oc_extranjera
	END
	IF (@ve_operacion='DELETE') BEGIN
		DELETE CX_PACKING_OC_EXTRANJERA
		WHERE COD_CX_PACKING_OC_EXTRANJERA = @ve_cod_cx_packing_oc_extranjera
	END		      
END