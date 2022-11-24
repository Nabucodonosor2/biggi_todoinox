---------------------- spu_factura_docs --------------------------
CREATE PROCEDURE spu_factura_docs(@ve_operacion varchar(100)
								,@ve_cod_factura_docs numeric
								,@ve_cod_factura numeric=NULL
								,@ve_cod_usuario numeric=NULL
								,@ve_ruta_archivo varchar(500)=NULL
								,@ve_nom_archivo varchar(100)=NULL
								,@ve_obs text=NULL
								,@ve_es_oc varchar(1)=NULL)
AS
BEGIN
	if (@ve_operacion='INSERT') begin
		insert into factura_DOCS
			(COD_FACTURA       
			,COD_USUARIO          
			,RUTA_ARCHIVO         
			,NOM_ARCHIVO          
			,FECHA_REGISTRO       
			,OBS
			,ES_OC
			)
		values		
			(@ve_cod_factura 
			,@ve_cod_usuario 
			,@ve_ruta_archivo 
			,@ve_nom_archivo 
			,getdate()
			,@ve_obs
			,@ve_es_oc
			)
	end 	
	else if (@ve_operacion='UPDATE') begin
		update FACTURA_DOCS
		set OBS = @ve_obs,
			ES_OC = @ve_es_oc
		where COD_factura_DOCS = @ve_cod_factura_docs
	end
	else if (@ve_operacion='DELETE') begin
		delete FACTURA_DOCS
		where COD_FACTURA_DOCS = @ve_cod_factura_docs
	end
END
go