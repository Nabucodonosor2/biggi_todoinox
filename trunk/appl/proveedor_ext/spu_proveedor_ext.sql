ALTER PROCEDURE [dbo].[spu_proveedor_ext](@ve_operacion 			varchar(20)
										 ,@ve_cod_proveedor_ext 	numeric = NULL
										 ,@ve_nom_proveedor_ext 	varchar(100)=NULL
										 ,@ve_alias_proveedor_ext 	varchar(100)=NULL
										 ,@ve_web_site 				varchar(100)=NULL
										 ,@ve_direccion 			varchar(100)=NULL
										 ,@ve_cod_ciudad 			numeric = NULL
										 ,@ve_cod_pais 				numeric = NULL
										 ,@ve_telefono 				varchar(100) = NULL
										 ,@ve_fax 					varchar(100) = NULL
										 ,@ve_post_office_box 		varchar(100) = NULL
										 ,@ve_obs 					text = NULL
										 ,@ve_cod_proveedor_ext_4d 	varchar(100) = NULL
										 ,@ve_nom_ciudad			varchar(100) = NULL
										 ,@ve_nom_pais				varchar(100) = NULL
										 ,@ve_beneficiary_dirbank	varchar(100) = NULL
										 ,@ve_beneficiary_namebank	varchar(100) = NULL
										 ,@ve_beneficiary_nameemp	varchar(100) = NULL
										 ,@ve_beneficiary_diremp	varchar(100) = NULL
										 ,@ve_bp_account_number		varchar(100) = NULL
										 ,@ve_bp_swift				varchar(100) = NULL
										 ,@ve_bp_iban				varchar(100) = NULL
										 ,@ve_bp_abi				varchar(100) = NULL
										 ,@ve_bp_cab				varchar(100) = NULL
										 ,@ve_bp_cbu				varchar(100) = NULL)
AS
BEGIN
	if (@ve_operacion='INSERT') begin
		insert into proveedor_ext (nom_proveedor_ext
								  ,alias_proveedor_ext
								  ,web_site
								  ,direccion
								  ,cod_ciudad
								  ,cod_pais
								  ,telefono
								  ,fax
								  ,post_office_box
								  ,obs
								  ,cod_proveedor_ext_4d
								  ,nom_ciudad_4d
								  ,nom_pais_4d
								  ,beneficiary_dirbank
								  ,beneficiary_namebank
								  ,beneficiary_nameemp
								  ,beneficiary_diremp
								  ,bp_account_number
								  ,bp_swift
								  ,bp_iban
								  ,bp_abi
								  ,bp_cab
								  ,bp_cbu)
						values (@ve_nom_proveedor_ext
							   ,@ve_alias_proveedor_ext
							   ,@ve_web_site
							   ,@ve_direccion
							   ,@ve_cod_ciudad
							   ,@ve_cod_pais
							   ,@ve_telefono
							   ,@ve_fax
							   ,@ve_post_office_box
							   ,@ve_obs
							   ,@ve_cod_proveedor_ext_4d
							   ,@ve_nom_ciudad
							   ,@ve_nom_pais
							   ,@ve_beneficiary_dirbank
							   ,@ve_beneficiary_namebank
							   ,@ve_beneficiary_nameemp
							   ,@ve_beneficiary_diremp
							   ,@ve_bp_account_number
							   ,@ve_bp_swift
							   ,@ve_bp_iban
							   ,@ve_bp_abi
							   ,@ve_bp_cab
							   ,@ve_bp_cbu)
		set @ve_cod_proveedor_ext = @@identity
		update proveedor_ext
		set cod_proveedor_ext_4d = convert(varchar, @ve_cod_proveedor_ext)
		where cod_proveedor_ext = @ve_cod_proveedor_ext
	end 
	if (@ve_operacion='UPDATE') begin
		update proveedor_ext 
		set nom_proveedor_ext 		= @ve_nom_proveedor_ext
			,alias_proveedor_ext 	= @ve_alias_proveedor_ext
			,web_site 				= @ve_web_site
			,direccion 				= @ve_direccion
			,cod_ciudad 			= @ve_cod_ciudad
			,cod_pais 				= @ve_cod_pais
			,telefono 				= @ve_telefono
			,fax 					= @ve_fax
			,post_office_box 		= @ve_post_office_box
			,obs 					= @ve_obs
			,nom_ciudad_4d 			= @ve_nom_ciudad
			,nom_pais_4d			= @ve_nom_pais
			,beneficiary_dirbank	= @ve_beneficiary_dirbank
		    ,beneficiary_namebank	= @ve_beneficiary_namebank
		    ,beneficiary_nameemp	= @ve_beneficiary_nameemp
		    ,beneficiary_diremp		= @ve_beneficiary_diremp
		    ,bp_account_number		= @ve_bp_account_number
		    ,bp_swift				= @ve_bp_swift
		    ,bp_iban				= @ve_bp_iban
		    ,bp_abi					= @ve_bp_abi
		    ,bp_cab					= @ve_bp_cab
		    ,bp_cbu					= @ve_bp_cbu
	    where cod_proveedor_ext 	= @ve_cod_proveedor_ext
	end
	else if (@ve_operacion='DELETE') begin
		delete proveedor_ext 
    	where cod_proveedor_ext = @ve_cod_proveedor_ext
	end
END
