CREATE PROCEDURE spu_cx_item_cot_extranjera(@ve_operacion					VARCHAR(20)
										   ,@ve_cod_cx_item_cot_extranjera	NUMERIC(10)=NULL
										   ,@ve_cod_cx_cot_extranjera		NUMERIC(10)=NULL
										   ,@ve_orden						NUMERIC(10)=NULL
										   ,@ve_item						VARCHAR(10)=NULL
										   ,@ve_cod_producto				VARCHAR(30)=NULL
										   ,@ve_nom_producto				VARCHAR(30)=NULL
										   ,@ve_cod_equipo_oc_ex			VARCHAR(30)=NULL
										   ,@ve_desc_equipo_oc_ex			VARCHAR(100)=NULL
										   ,@ve_cantidad					NUMERIC(15,2)=NULL
										   ,@ve_precio						NUMERIC(15,2)=NULL)			   
AS
BEGIN
	IF (@ve_operacion='INSERT') BEGIN
		INSERT INTO CX_ITEM_COT_EXTRANJERA VALUES(@ve_cod_cx_cot_extranjera
												,@ve_orden
												,@ve_item
												,@ve_cod_producto
												,@ve_nom_producto
												,@ve_cod_equipo_oc_ex
												,@ve_desc_equipo_oc_ex
												,@ve_cantidad
												,@ve_precio)		  
	END 
	IF (@ve_operacion='UPDATE') BEGIN
		UPDATE CX_ITEM_COT_EXTRANJERA
		SET COD_CX_COT_EXTRANJERA	= @ve_cod_cx_cot_extranjera,
			ORDEN					= @ve_orden,
			ITEM					= @ve_item,
			COD_PRODUCTO			= @ve_cod_producto,
			NOM_PRODUCTO			= @ve_nom_producto,
			COD_EQUIPO_OC_EX		= @ve_cod_equipo_oc_ex,
			DESC_EQUIPO_OC_EX		= @ve_desc_equipo_oc_ex,
			CANTIDAD				= @ve_cantidad,
			PRECIO					= @ve_precio
		WHERE COD_CX_ITEM_COT_EXTRANJERA = @ve_cod_cx_item_cot_extranjera
	END
	IF (@ve_operacion='DELETE') BEGIN
		DELETE CX_ITEM_COT_EXTRANJERA
		WHERE COD_CX_ITEM_COT_EXTRANJERA = @ve_cod_cx_item_cot_extranjera
	END		      
END