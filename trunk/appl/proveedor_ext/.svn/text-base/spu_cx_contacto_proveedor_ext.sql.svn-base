-------------------- spu_cx_contacto_proveedor_ext ---------------------------------
CREATE PROCEDURE dbo.spu_cx_contacto_proveedor_ext(@ve_operacion						varchar(20)
												 ,@ve_cod_cx_contacto_proveedor_ext	numeric
												 ,@ve_cod_proveedor_ext				numeric	= NULL
												 ,@ve_nom_contacto_proveedor_ext	varchar(100)	= NULL
												 ,@ve_mail							varchar(100)	= NULL
												 ,@ve_telefono_fijo					varchar(100)	= NULL
												 ,@ve_telefono_movil				varchar(100)	= NULL
												 ,@ve_it_fax						varchar(100)	= NULL)
AS
BEGIN
	if (@ve_operacion='INSERT') 
		begin
			insert into cx_contacto_proveedor_ext
						(cod_proveedor_ext
						,nom_contacto_proveedor_ext
						,mail				
						,telefono				
						,telefono_movil					
						,fax)
				values	(@ve_cod_proveedor_ext
						,@ve_nom_contacto_proveedor_ext
						,@ve_mail				
						,@ve_telefono_fijo				
						,@ve_telefono_movil					
						,@ve_it_fax)
		end 
	else if (@ve_operacion='UPDATE') 
		begin
			UPDATE	cx_contacto_proveedor_ext
			SET	cod_proveedor_ext = @ve_cod_proveedor_ext
				,nom_contacto_proveedor_ext = @ve_nom_contacto_proveedor_ext
				,mail = @ve_mail
				,telefono = @ve_telefono_fijo
				,telefono_movil = @ve_telefono_movil
				,fax = @ve_it_fax
 			WHERE cod_cx_contacto_proveedor_ext = @ve_cod_cx_contacto_proveedor_ext
		end	
	else if (@ve_operacion='DELETE') 
		begin
			DELETE  cx_contacto_proveedor_ext
    		WHERE cod_cx_contacto_proveedor_ext = @ve_cod_cx_contacto_proveedor_ext
		end 
END
