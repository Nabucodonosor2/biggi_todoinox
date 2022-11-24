-------------------- spu_item_orden_despacho ---------------------------------
CREATE PROCEDURE spu_item_orden_despacho(@ve_operacion					varchar(20)
										,@ve_cod_item_orden_despacho	numeric(10)
										,@ve_cod_orden_despacho			numeric(10)=NULL
										,@ve_orden						numeric(10)=NULL
										,@ve_item						varchar(10)=NULL
										,@ve_cod_producto				varchar(100)=NULL
										,@ve_nom_producto				varchar(100)=NULL
										,@ve_cantidad					numeric(10)=NULL
										,@ve_cantidad_recibida			numeric(10)=NULL)	
AS
BEGIN
	if (@ve_operacion='INSERT')begin
		insert into item_orden_despacho(cod_orden_despacho,
										orden,
										item,
										cod_producto,
										nom_producto,
										cantidad,
										cantidad_recibida)
								values (@ve_cod_orden_despacho,
										@ve_orden,
										@ve_item,
										@ve_cod_producto,
										@ve_nom_producto,
										@ve_cantidad,
										@ve_cantidad_recibida) 
	end 
	else if (@ve_operacion='UPDATE')begin
		update item_orden_despacho
		set orden				=	@ve_orden,
			item				=	@ve_item,
			cod_producto		=	@ve_cod_producto,
			nom_producto		=	@ve_nom_producto,
			cantidad			=	@ve_cantidad,
			cantidad_recibida	=	@ve_cantidad_recibida	    				
		where cod_item_orden_despacho = @ve_cod_item_orden_despacho
	end	
	else if (@ve_operacion='DELETE')begin
		delete  item_orden_recibida 
		where cod_item_orden_despacho = @ve_cod_item_orden_despacho
	end 
END
go