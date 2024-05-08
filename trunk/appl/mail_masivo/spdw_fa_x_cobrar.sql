ALTER PROCEDURE spdw_fa_x_cobrar(@ve_operacion		varchar(100))
/*
exec spdw_fa_x_cobrar 'RESUMEN'
exec spdw_fa_x_cobrar 'OTROS_MONTO_ALTO'
exec spdw_fa_x_cobrar 'OTROS_DETALLE'
exec spdw_fa_x_cobrar 'SERVINDUS_ANTIGUAS'
exec spdw_fa_x_cobrar 'SERVINDUS_MONTO_ALTO'
exec spdw_fa_x_cobrar 'SERVINDUS_DETALLE'
*/
AS
BEGIN
	declare @TEMP_DETALLE TABLE     
	   (NRO_FACTURA					numeric
		,FECHA_FACTURA				datetime
		,COD_EMPRESA				numeric
		,NOM_EMPRESA				varchar(100)
		,MONTO						numeric
		,PORC						numeric
		,NOM_EMPRESA_COPIA			varchar(100)
		)

	declare
		@vl_por_cobrar_otros			numeric
		,@vl_por_cobrar_arriendo		numeric
		,@vl_por_cobrar_servindus		numeric
		,@vl_por_cobrar_relacionadas	numeric

	--------------------
	if (@ve_operacion = 'OTROS_MONTO_ALTO') begin
		SELECT @vl_por_cobrar_otros = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		and     F.COD_TIPO_FACTURA <> 2 --ARRIENDO
		and		F.COD_EMPRESA not in (38, 1, 37)	--SERVINDUS, COMERCIAL, BODEGA
		AND		F.COD_ESTADO_DOC_SII in (2,3)	

	
		SELECT TOP 5 F.NRO_FACTURA
				,dbo.f_format_date(F.FECHA_FACTURA, 1) FECHA_FACTURA
				,F.NOM_EMPRESA
				,dbo.f_fa_saldo(F.COD_FACTURA) MONTO
				,dbo.f_fa_saldo(F.COD_FACTURA) * 100/@vl_por_cobrar_otros PORC
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		and     F.COD_TIPO_FACTURA <> 2 --ARRIENDO
		and		F.COD_EMPRESA not in (38, 1, 37)	--SERVINDUS, COMERCIAL, BODEGA
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
		ORDER BY dbo.f_fa_saldo(F.COD_FACTURA) desc
	end
	else if (@ve_operacion = 'OTROS_DETALLE') begin
		SELECT @vl_por_cobrar_otros = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		--and     F.COD_TIPO_FACTURA <> 2 --ARRIENDO  => ya no se usa
		and		F.COD_EMPRESA not in (1, 37, 38, 605, 4988, 62, 63, 5388, 4178)
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
	
		insert into @TEMP_DETALLE 
		   (NRO_FACTURA					
			,FECHA_FACTURA		
			,COD_EMPRESA		
			,NOM_EMPRESA				
			,MONTO						
			,PORC						
			,NOM_EMPRESA_COPIA				
			)
		SELECT F.NRO_FACTURA
				,FECHA_FACTURA
				,F.COD_EMPRESA
				,F.NOM_EMPRESA
				,dbo.f_fa_saldo(F.COD_FACTURA) MONTO
				,dbo.f_fa_saldo(F.COD_FACTURA) * 100/@vl_por_cobrar_otros PORC
				,F.NOM_EMPRESA
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		--and     F.COD_TIPO_FACTURA <> 2 --ARRIENDO  => ya no se usa
		and		F.COD_EMPRESA not in (1, 37, 38, 605, 4988, 62, 63, 5388, 4178)
		AND		F.COD_ESTADO_DOC_SII in (2,3)	

		DECLARE C_TEMPO CURSOR FOR  
		SELECT COD_EMPRESA, MONTO, NOM_EMPRESA
		from @TEMP_DETALLE
		order by COD_EMPRESA

		declare
			@vc_cod_empresa			numeric
			,@vc_monto				numeric
			,@vc_nom_empresa		varchar(100)
			,@vl_nom_empresa_old	varchar(100)
			,@vl_cod_empresa_old	numeric
			,@vl_total_empresa		numeric

		set @vl_cod_empresa_old = 0
		set @vl_nom_empresa_old = ''
		set @vl_total_empresa = 0
		OPEN C_TEMPO
		FETCH C_TEMPO INTO @vc_cod_empresa, @vc_monto, @vc_nom_empresa
		WHILE @@FETCH_STATUS = 0 BEGIN	
			if (@vl_cod_empresa_old <> @vc_cod_empresa) begin
				if (@vl_cod_empresa_old <> 0) begin
					insert into @TEMP_DETALLE 
					   (NRO_FACTURA					
						,FECHA_FACTURA				
						,COD_EMPRESA				
						,NOM_EMPRESA				
						,MONTO						
						,PORC						
						,NOM_EMPRESA_COPIA				
						)
					values 
					   (null		--NRO_FACTURA				
						,null		--FECHA_FACTURA	
						,@vl_cod_empresa_old	--COD_EMPRESA			
						,'SUBTOTAL'	--NOM_EMPRESA				
						,@vl_total_empresa		--MONTO						
						,@vl_total_empresa * 100/@vl_por_cobrar_otros		--PORC		
						,@vl_nom_empresa_old				
						)
				end
				
				set @vl_cod_empresa_old = @vc_cod_empresa
				set @vl_nom_empresa_old = @vc_nom_empresa
				set @vl_total_empresa = 0
			end
			set @vl_total_empresa = @vl_total_empresa + @vc_monto
			
			FETCH C_TEMPO INTO @vc_cod_empresa, @vc_monto, @vc_nom_empresa
		END
		CLOSE C_TEMPO
		DEALLOCATE C_TEMPO

		if (@vl_cod_empresa_old <> 0) begin
			insert into @TEMP_DETALLE 
			   (NRO_FACTURA					
				,FECHA_FACTURA				
				,COD_EMPRESA				
				,NOM_EMPRESA				
				,MONTO						
				,PORC	
				,NOM_EMPRESA_COPIA					
				)
			values 
			   (null					--NRO_FACTURA				
				,null					--FECHA_FACTURA				
				,@vl_cod_empresa_old	--COD_EMPRESA			
				,'SUBTOTAL'				--NOM_EMPRESA				
				,@vl_total_empresa		--MONTO						
				,@vl_total_empresa * 100/@vl_por_cobrar_otros		--PORC		
				,@vl_nom_empresa_old				
				)
		end

		select NRO_FACTURA					
			,CONVERT(VARCHAR, FECHA_FACTURA, 103)FECHA_FACTURA		
			,COD_EMPRESA		
			,NOM_EMPRESA				
			,MONTO						
			,PORC						
			,NOM_EMPRESA_COPIA	
		from @TEMP_DETALLE
		ORDER BY NOM_EMPRESA_COPIA, isnull(FECHA_FACTURA, dbo.f_makedate(31,12,9999))
	end
	--------------------
	else if (@ve_operacion = 'SERVINDUS_ANTIGUAS') begin
		SELECT @vl_por_cobrar_servindus = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		and     F.COD_EMPRESA in (38)	--SERVINDUS
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
	
		SELECT TOP 5 F.NRO_FACTURA
				,dbo.f_format_date(F.FECHA_FACTURA, 1) FECHA_FACTURA
				,F.NOM_EMPRESA
				,dbo.f_fa_saldo(F.COD_FACTURA) MONTO
				,dbo.f_fa_saldo(F.COD_FACTURA) * 100/@vl_por_cobrar_servindus PORC
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		and     F.COD_EMPRESA in (38)	--SERVINDUS
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
		ORDER BY F.FECHA_FACTURA asc
	end
	else if (@ve_operacion = 'SERVINDUS_MONTO_ALTO') begin
		SELECT @vl_por_cobrar_servindus = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		and     F.COD_EMPRESA in (38)	--SERVINDUS
		AND		F.COD_ESTADO_DOC_SII in (2,3)	

	
		SELECT TOP 5 F.NRO_FACTURA
				,dbo.f_format_date(F.FECHA_FACTURA, 1) FECHA_FACTURA
				,F.NOM_EMPRESA
				,dbo.f_fa_saldo(F.COD_FACTURA) MONTO
				,dbo.f_fa_saldo(F.COD_FACTURA) * 100/@vl_por_cobrar_servindus PORC
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		and     F.COD_EMPRESA in (38)	--SERVINDUS
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
		ORDER BY dbo.f_fa_saldo(F.COD_FACTURA) desc
	end
	else if (@ve_operacion = 'SERVINDUS_DETALLE') begin
		SELECT @vl_por_cobrar_servindus = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		and     F.COD_EMPRESA in (38)	--SERVINDUS
		AND		F.COD_ESTADO_DOC_SII in (2,3)	

	
		SELECT F.NRO_FACTURA
				,dbo.f_format_date(F.FECHA_FACTURA, 1) FECHA_FACTURA
				,F.NOM_EMPRESA
				,dbo.f_fa_saldo(F.COD_FACTURA) MONTO
				,dbo.f_fa_saldo(F.COD_FACTURA) * 100/@vl_por_cobrar_servindus PORC
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		and     F.COD_EMPRESA in (38)	--SERVINDUS
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
		ORDER BY F.FECHA_FACTURA
	end
	--------------------
	else if (@ve_operacion = 'RESUMEN') begin
		declare @TEMP_RELACIONADA TABLE     
		   (COD_EMPRESA					numeric
			,NOM_EMPRESA					varchar(100)
			,MAS_90_TOTAL					numeric
			,MAS_60_TOTAL					numeric
			,MAS_30_TOTAL					numeric
			,MENOS_30_TOTAL					numeric
			,TOTAL							numeric
			,ORDEN							numeric
			)

		insert into @TEMP_RELACIONADA
		   (COD_EMPRESA						
			,NOM_EMPRESA					
			,MAS_90_TOTAL							
			,MAS_60_TOTAL							
			,MAS_30_TOTAL							
			,MENOS_30_TOTAL						
			,TOTAL
			,ORDEN
			)
		select COD_EMPRESA						
			,CASE COD_EMPRESA
				WHEN 1 then 'Comercial Biggi'
				WHEN 37 then 'Biggi Chile'
				WHEN 38 then 'Servindus'
				WHEN 605 then 'Comercial BYS'
				WHEN 4988 then 'Guillermo Espejo'
				WHEN 62 then 'Sodexo Servicios'
				WHEN 63 then 'Oppici '
				WHEN 5388 then 'Ingtec Spa'
				WHEN 4178 then 'Metinox Spa'
			END CT_NOM_EMPRESA
			,0		--MAS_90							
			,0		--MAS_60							
			,0		--MAS_30							
			,0		--MENOS_30						
			,0		--TOTAL	
			,CASE COD_EMPRESA
				WHEN 1 then 1
				WHEN 37 then 2
				WHEN 38 then 3
				WHEN 605 then 4
				WHEN 4988 then 5
				WHEN 62 then 6
				WHEN 63 then 7
				WHEN 5388 then 8
				WHEN 4178 then 9
			END ORDEN
		from EMPRESA
		where COD_EMPRESA in (1, 37, 38, 605, 4988, 62, 63, 5388, 4178)
		order by ORDEN ASC

		DECLARE C_TEMPO CURSOR FOR  
		SELECT COD_EMPRESA
		from @TEMP_RELACIONADA

		declare
			@vl_mas_90			numeric				
			,@vl_mas_60			numeric			
			,@vl_mas_30			numeric			
			,@vl_menos_30		numeric
			,@vl_fecha_30		datetime
			,@vl_fecha_60		datetime
			,@vl_fecha_90		datetime


		set @vl_fecha_30 = DATEADD(day, -30, getdate())
		set @vl_fecha_60 = DATEADD(day, -60, getdate())
		set @vl_fecha_90 = DATEADD(day, -90, getdate())
		OPEN C_TEMPO
		FETCH C_TEMPO INTO @vc_cod_empresa
		WHILE @@FETCH_STATUS = 0 BEGIN	
			SELECT @vl_mas_90 = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
			FROM	FACTURA F
			WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
			and     F.COD_EMPRESA = @vc_cod_empresa
			AND		F.COD_ESTADO_DOC_SII in (2,3)	
			and		F.FECHA_FACTURA <= @vl_fecha_90
			
			SELECT @vl_mas_60 = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
			FROM	FACTURA F
			WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
			and     F.COD_EMPRESA = @vc_cod_empresa
			AND		F.COD_ESTADO_DOC_SII in (2,3)	
			and		F.FECHA_FACTURA <= @vl_fecha_60
			and		F.FECHA_FACTURA > @vl_fecha_90
			
			SELECT @vl_mas_30 = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
			FROM	FACTURA F
			WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
			and     F.COD_EMPRESA = @vc_cod_empresa
			AND		F.COD_ESTADO_DOC_SII in (2,3)	
			and		F.FECHA_FACTURA <= @vl_fecha_30
			and		F.FECHA_FACTURA > @vl_fecha_60
			
			SELECT @vl_menos_30 = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
			FROM	FACTURA F
			WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
			and     F.COD_EMPRESA = @vc_cod_empresa
			AND		F.COD_ESTADO_DOC_SII in (2,3)	
			and		F.FECHA_FACTURA > @vl_fecha_30
			
			update @TEMP_RELACIONADA
			set MAS_90_TOTAL		= @vl_mas_90
				,MAS_60_TOTAL		= @vl_mas_60
				,MAS_30_TOTAL		= @vl_mas_30	
				,MENOS_30_TOTAL	= @vl_menos_30				
			where COD_EMPRESA = @vc_cod_empresa
			
			FETCH C_TEMPO INTO @vc_cod_empresa
		END
		CLOSE C_TEMPO
		DEALLOCATE C_TEMPO
		
		-----------------------
		-- SODEXO ARRIENDO
		/*SELECT @vl_mas_90 = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		and     F.COD_TIPO_FACTURA = 2	--arriendo
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
		and		F.FECHA_FACTURA <= @vl_fecha_90
		
		SELECT @vl_mas_60 = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		and     F.COD_TIPO_FACTURA = 2	--arriendo
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
		and		F.FECHA_FACTURA <= @vl_fecha_60
		and		F.FECHA_FACTURA > @vl_fecha_90
		
		SELECT @vl_mas_30 = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		and     F.COD_TIPO_FACTURA = 2	--arriendo
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
		and		F.FECHA_FACTURA <= @vl_fecha_30
		and		F.FECHA_FACTURA > @vl_fecha_60
		
		SELECT @vl_menos_30 = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		and     F.COD_TIPO_FACTURA = 2	--arriendo
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
		and		F.FECHA_FACTURA > @vl_fecha_30

		insert into @TEMP_RELACIONADA
		   (COD_EMPRESA						
			,NOM_EMPRESA					
			,MAS_90_TOTAL							
			,MAS_60_TOTAL							
			,MAS_30_TOTAL							
			,MENOS_30_TOTAL						
			,TOTAL							
			)
		values 
			(-1					--COD_EMPRESA						
			,'SODEXO ARRIENDO'	--NOM_EMPRESA					
			,@vl_mas_90			--MAS_90							
			,@vl_mas_60			--MAS_60							
			,@vl_mas_30			--MAS_30							
			,@vl_menos_30		--MENOS_30						
			,0		--TOTAL							
			)
		*/

		-----------------------
		-- OTROS
		SELECT @vl_mas_90 = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		--and     F.COD_TIPO_FACTURA <> 2	--arriendo => ya no se usa
		and		F.COD_EMPRESA not in (1, 37, 38, 605, 4988, 62, 63, 5388, 4178)
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
		and		F.FECHA_FACTURA <= @vl_fecha_90
		
		SELECT @vl_mas_60 = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		--and     F.COD_TIPO_FACTURA <> 2	--arriendo => ya no se usa
		and		F.COD_EMPRESA not in (1, 37, 38, 605, 4988, 62, 63, 5388, 4178)
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
		and		F.FECHA_FACTURA <= @vl_fecha_60
		and		F.FECHA_FACTURA > @vl_fecha_90
		
		SELECT @vl_mas_30 = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		--and     F.COD_TIPO_FACTURA <> 2	--arriendo => ya no se usa
		and		F.COD_EMPRESA not in (1, 37, 38, 605, 4988, 62, 63, 5388, 4178)
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
		and		F.FECHA_FACTURA <= @vl_fecha_30
		and		F.FECHA_FACTURA > @vl_fecha_60
		
		SELECT @vl_menos_30 = isnull(sum(dbo.f_fa_saldo(F.COD_FACTURA)), 0)
		FROM	FACTURA F
		WHERE	dbo.f_fa_saldo(F.COD_FACTURA) > 0
		--and     F.COD_TIPO_FACTURA <> 2	--arriendo => ya no se usa
		and		F.COD_EMPRESA not in (1, 37, 38, 605, 4988, 62, 63, 5388, 4178)
		AND		F.COD_ESTADO_DOC_SII in (2,3)	
		and		F.FECHA_FACTURA > @vl_fecha_30

		insert into @TEMP_RELACIONADA
		   (COD_EMPRESA						
			,NOM_EMPRESA					
			,MAS_90_TOTAL							
			,MAS_60_TOTAL							
			,MAS_30_TOTAL							
			,MENOS_30_TOTAL						
			,TOTAL							
			)
		values 
			(-2					--COD_EMPRESA						
			,'OTROS'			--NOM_EMPRESA					
			,@vl_mas_90			--MAS_90							
			,@vl_mas_60			--MAS_60							
			,@vl_mas_30			--MAS_30							
			,@vl_menos_30		--MENOS_30						
			,0					--TOTAL							
			)
		-----------------------------

		update @TEMP_RELACIONADA
		set TOTAL = MAS_90_TOTAL + MAS_60_TOTAL	+ MAS_30_TOTAL + MENOS_30_TOTAL

		select * from @TEMP_RELACIONADA
	end
END