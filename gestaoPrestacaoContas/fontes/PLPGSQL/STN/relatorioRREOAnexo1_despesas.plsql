/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
* Script de função PLPGSQL - Relatório STN - RREO - Anexo 1
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 29316 $
* $Name$
* $Author: lbbarreiro $
* $Date: 2008-04-17 18:13:29 -0300 (Qui, 17 Abr 2008) $
*
* Casos de uso: uc-04.05.28
*/


/*$Log$
 *Revision 1.1  2006/09/26 17:33:42  bruce
 *Colocada a tag de log
 **/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo1_despesas( varchar, integer,varchar ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    	ALIAS FOR $1;
    inBimestre     	ALIAS FOR $2;
    stCodEntidades 	ALIAS FOR $3;

    dtInicial  		varchar := '';
    dtFinal    		varchar := '';
    dtIniExercicio 	VARCHAR := '';
    
    arDatas 		varchar[] ;
    reRegistro 		record ;
    stSql 			varchar := '';

BEGIN

    arDatas := publico.bimestre ( stExercicio, inBimestre );   
    dtInicial := arDatas [ 0 ];
    dtFinal   := arDatas [ 1 ];
    
    dtIniExercicio := '01/01/'|| stExercicio;

	-- TABELAS TEMPORARIAS
	
	-- Total das Suplementações	
    -- Suplementado
    
    stSql := 'CREATE TEMPORARY TABLE tmp_suplementacao_suplementada AS
               SELECT   sups.cod_despesa
                       ,sum(sups.valor)  as vl_suplementado
               FROM     orcamento.suplementacao                as sup
                       ,orcamento.suplementacao_suplementada   as sups
                       ,orcamento.despesa                      as de
               WHERE   sup.exercicio           = ' || quote_literal(stExercicio) || ' 
                 AND   sup.dt_suplementacao BETWEEN to_date('|| quote_literal(dtIniExercicio) ||',''dd/mm/yyyy'') 
                                                AND to_date('|| quote_literal(dtFinal)        ||',''dd/mm/yyyy'') ';
    
    if ( stCodEntidades != '' ) then
        stSql := stSql || 'AND   de.cod_entidade IN ('|| stCodEntidades ||' )';
    end if;
    
    stSql := stSql|| ' AND   sup.exercicio           = sups.exercicio
			AND   sup.cod_suplementacao   = sups.cod_suplementacao
			AND   sups.exercicio          = de.exercicio
			AND   sups.cod_despesa        = de.cod_despesa
		GROUP BY sups.cod_despesa;
	CREATE INDEX unq_tmp_suplementacao_suplementada ON tmp_suplementacao_suplementada (cod_despesa);
	';

    EXECUTE stSql;

    -- Reduzido
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_suplementacao_reduzida AS
    	SELECT 
    		supr.cod_despesa, 
    		sum(supr.valor) as vl_reduzido 
    	FROM 
    		orcamento.suplementacao as sup, 
    		orcamento.suplementacao_reducao as supr, 
    		orcamento.despesa as de 
    	WHERE 
    		sup.exercicio = '|| quote_literal(stExercicio) ||' AND 
    		sup.dt_suplementacao BETWEEN 	to_date('||quote_literal(dtIniExercicio) ||', ''dd/mm/yyyy'') 
            	                     AND 	to_date('||quote_literal(dtFinal)        ||', ''dd/mm/yyyy'')
	';
    if (stCodEntidades != '' ) then 
         stSql := stSql || ' AND de.cod_entidade IN ('|| stCodEntidades ||' ) ';
    end if ;

    stSql := stSql || 'AND sup.exercicio = supr.exercicio AND 
    		sup.cod_suplementacao   = supr.cod_suplementacao AND 
    		supr.exercicio          = de.exercicio AND 
    		supr.cod_despesa        = de.cod_despesa 
    	GROUP BY supr.cod_despesa ;
    	
    CREATE INDEX unq_tmp_suplementacao_reduzida ON tmp_suplementacao_reduzida (cod_despesa);
    ';
    
    EXECUTE stSql;

    -- Total das Despesas
    
	stSql := '
	CREATE TEMPORARY TABLE tmp_despesa_totais AS
		SELECT 
			de.exercicio, 
			de.cod_despesa, 
			de.vl_original 
		FROM 
			orcamento.despesa de, 
			empenho.pre_empenho_despesa ped 
		WHERE 
			de.exercicio    = '|| quote_literal(stExercicio) ||' AND 
			de.exercicio    = ped.exercicio AND 
			de.cod_despesa  = ped.cod_despesa 
		GROUP BY 
			de.exercicio, 
			de.cod_despesa, 
			de.vl_original 
		ORDER BY 
			de.exercicio, 
			de.cod_despesa ;
			
	CREATE INDEX unq_tmp_despesa_totais ON tmp_despesa_totais (exercicio,cod_despesa);
	';

	EXECUTE stSql;

	
	-- --------------------------------------------
	-- Total por Despesa
	-- --------------------------------------------


	stSql := '
	CREATE TEMPORARY TABLE tmp_despesa AS 
		SELECT 
			de.cod_conta, 
			de.exercicio, 
			ocd.cod_estrutural, 
			sum(coalesce(de.vl_original,0.00)) as vl_original, 			
			(sum(coalesce(sups.vl_suplementado,0.00)) - sum(coalesce(supr.vl_reduzido,0.00))) as vl_suplementacoes, 
			CAST(0.00 AS NUMERIC(14,2)) as vl_empenhado_bimestre, 
			CAST(0.00 AS NUMERIC(14,2)) as vl_liquidado_bimestre, 
			CAST(0.00 AS NUMERIC(14,2)) as vl_empenhado_total, 
			CAST(0.00 AS NUMERIC(14,2)) as vl_liquidado_total 
		FROM 
			orcamento.despesa de 
			INNER JOIN 
			orcamento.conta_despesa ocd ON 
				ocd.exercicio = de.exercicio and 
				ocd.cod_conta = de.cod_conta 
			LEFT JOIN 
			tmp_despesa_totais tdt ON 
				tdt.exercicio = de.exercicio AND 
				tdt.cod_despesa = de.cod_despesa 
			--Suplementacoes
			LEFT JOIN 
			tmp_suplementacao_suplementada sups ON 
				de.cod_despesa = sups.cod_despesa
			LEFT JOIN 
			tmp_suplementacao_reduzida supr ON 
				de.cod_despesa = supr.cod_despesa 
		WHERE 
			de.exercicio = '|| quote_literal(stExercicio) ||' AND 
			de.cod_entidade IN ('|| stCodEntidades ||' ) 
		GROUP BY 
			de.cod_conta, 
			de.exercicio, 
			ocd.cod_estrutural 
		ORDER BY 
			de.cod_conta, 
			de.exercicio 
	';
	
	EXECUTE stSql;
	
	--- FIM DAS TEMPORARIAS
    
	stSql := '
	CREATE TEMPORARY TABLE tmp_rreo_an1_despesa AS (
	
	SELECT * FROM  
		(SELECT
			CAST(1 AS INTEGER) as grupo , 
			ocd.cod_estrutural , 
			ocd.descricao , 
			publico.fn_nivel(ocd.cod_estrutural)  as nivel , 
			CAST(SUM(tmp.vl_original) as numeric(14,2)) as dotacao_inicial , 
			CAST(SUM(tmp.vl_suplementacoes) as numeric(14,2)) as creditos_adicionais , 
			CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes))as numeric(14,2)) as dotacao_atualizada , 
			COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre ,
			COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total    ,
			COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre ,
			COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total    ,
			CAST(0.00 AS NUMERIC(14,2)) AS percentual , 
			CAST(0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
		FROM 
			orcamento.conta_despesa ocd 
			INNER JOIN 
			tmp_despesa tmp ON 
				ocd.exercicio = tmp.exercicio AND 
				tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
		WHERE 
			publico.fn_mascarareduzida(ocd.cod_estrutural) like ''3%'' AND 
			--publico.fn_nivel(ocd.cod_estrutural) >  1 AND 
			publico.fn_nivel(ocd.cod_estrutural) < 3 AND 
			-- Exceto intra
			substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
			substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 			
		GROUP BY 
			ocd.cod_estrutural , 
			ocd.descricao 
		ORDER BY 
			ocd.cod_estrutural 
		) AS tbl 
	
	UNION ALL 

	SELECT * FROM 
		(SELECT 
			CAST(2 AS INTEGER) AS grupo , 
			ocd.cod_estrutural , 
			ocd.descricao , 
			publico.fn_nivel(ocd.cod_estrutural) AS nivel , 
			CAST(SUM(tmp.vl_original) AS numeric(14,2)) AS dotacao_inicial , 
			CAST(SUM(tmp.vl_suplementacoes) AS numeric(14,2)) AS creditos_adicionais , 
			CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) AS numeric(14,2)) as dotacao_atualizada , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total , 
			CAST(0.00 AS NUMERIC(14,2)) AS percentual , 
			CAST(0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
		FROM 
			orcamento.conta_despesa ocd 
			INNER JOIN tmp_despesa tmp ON 
				ocd.exercicio = tmp.exercicio AND 
				tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%''
		WHERE 
			publico.fn_mascarareduzida(ocd.cod_estrutural) like ''4%'' AND 
			-- publico.fn_nivel(ocd.cod_estrutural) >  1 AND 
			publico.fn_nivel(ocd.cod_estrutural) < 3 AND 
			-- Exceto intra
			substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
			substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
		GROUP BY 
			ocd.cod_estrutural , 
			ocd.descricao 
		ORDER BY 
			ocd.cod_estrutural
		) AS tbl
         
	UNION ALL 

	SELECT * FROM 
		(SELECT 
			CAST(3 AS INTEGER) AS grupo , 
			ocd.cod_estrutural , 
			ocd.descricao ,  
			-- publico.fn_nivel(ocd.cod_estrutural) , 
			CAST(1 AS INTEGER) AS nivel , 
			CAST(SUM(tmp.vl_original) AS numeric(14,2)) AS dotacao_inicial , 
			CAST(SUM(tmp.vl_suplementacoes) AS numeric(14,2)) AS creditos_adicionais , 
			CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) AS numeric(14,2)) AS dotacao_atualizada , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total    , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total    , 
			CAST (0.00 AS NUMERIC(14,2)) AS percentual , 
			CAST (0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
		FROM 
			orcamento.conta_despesa ocd 
			INNER JOIN tmp_despesa tmp ON 
				ocd.exercicio = tmp.exercicio AND 
				tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%''
		WHERE  
			ocd.cod_estrutural LIKE ''9.9.9.9.99%'' AND 
			publico.fn_nivel(ocd.cod_estrutural) > 4 AND 
			-- Exceto intra
			substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
			substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
	 	GROUP BY 
	 		ocd.cod_estrutural, 
	 		ocd.descricao 
	 	ORDER BY 
	 		ocd.cod_estrutural 
	 	) AS tbl

	-- Reserva do RPPS
	UNION ALL 

	SELECT * FROM 
		(SELECT 
			CAST(2 AS INTEGER) AS grupo , 
			''a''||ocd.cod_estrutural , 
			ocd.descricao , 
			--	publico.fn_nivel(ocd.cod_estrutural) , 
			1 AS nivel , 
			CAST(COALESCE(SUM(tmp.vl_original), 0.00) as numeric(14,2)) as dotacao_inicial , 
			CAST(COALESCE(SUM(tmp.vl_suplementacoes), 0.00) as numeric(14,2)) as creditos_adicionais , 
			CAST(COALESCE(SUM((tmp.vl_original + tmp.vl_suplementacoes)), 0.00) as numeric(14,2)) as dotacao_atualizada , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total    , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total    , 
			CAST (0.00 AS NUMERIC(14,2)) AS percentual , 
			CAST (0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
		FROM 
			orcamento.conta_despesa ocd 
			INNER JOIN tmp_despesa tmp ON 
				ocd.exercicio = tmp.exercicio AND 
				tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%''
		WHERE 
			ocd.cod_estrutural LIKE ''7.7.9.9.99.99%'' AND 
			publico.fn_nivel(ocd.cod_estrutural) > 5 AND 
			-- Exceto intra
			substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
			substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
		GROUP BY 
			ocd.cod_estrutural , 
			ocd.descricao 
		ORDER BY 
			ocd.cod_estrutural 
	) AS tbl 

	UNION ALL 
		
	SELECT * FROM  
		(SELECT
			CAST(2 AS INTEGER) AS grupo , 
			''a9''||ocd.cod_estrutural , 
			-- cast(''a9.6.9.0.76.01.00.00.00'' AS varchar) AS cod_estrutural , 
			ocd.descricao , 
			-- publico.fn_nivel(ocd.cod_estrutural) , 
			CAST(3 AS INTEGER) AS nivel , 
			CAST(SUM(tmp.vl_original) AS numeric(14,2)) as dotacao_inicial , 
			CAST(SUM(tmp.vl_suplementacoes) AS numeric(14,2)) AS creditos_adicionais , 
			CAST(SUM((tmp.vl_original+tmp.vl_suplementacoes)) AS numeric(14,2)) AS dotacao_atualizada , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total    , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total    , 
			CAST (0.00 AS NUMERIC(14,2)) AS percentual , 
			CAST (0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
		FROM 
			orcamento.conta_despesa ocd 
			INNER JOIN tmp_despesa tmp ON 
				ocd.exercicio = tmp.exercicio AND 
				tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%''
		WHERE 
			(ocd.cod_estrutural LIKE ''4.6.9.0.76%'' OR  ocd.cod_estrutural LIKE ''4.6.9.0.77%'') AND 
			publico.fn_nivel(ocd.cod_estrutural) > 5 AND 
			-- Exceto intra
			substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
			substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
		GROUP BY 
			ocd.cod_estrutural , 
			ocd.descricao 
		ORDER BY 
			ocd.cod_estrutural 
	) AS tbl 
	
	-- Despesas Intra-Orçamentarias
	
	UNION ALL 

	SELECT 
		CAST(4 AS INTEGER) AS grupo , 
		''X.X.9.1.00.00.00.00'' AS cod_estrutural , 
		CAST(''DESPESAS (INTRA-ORÇAMENTÁRIAS)'' AS VARCHAR(150)) AS descricao , 
		CAST(1 as INTEGER) AS nivel , 
		SUM(dotacao_inicial) AS dotacao_inicial , 
		SUM(creditos_adicionais) AS creditos_adicionais , 
		SUM(dotacao_atualizada) AS dotacao_atualizada , 
		SUM(vl_empenhado_bimestre) AS vl_empenhado_bimestre , 
		SUM(vl_empenhado_total) AS vl_empenhado_total , 
		SUM(vl_liquidado_bimestre) AS vl_liquidado_bimestre , 
		SUM(vl_liquidado_total) AS vl_liquidado_total , 
		SUM(percentual) AS percentual , 
		SUM(saldo_liquidar) AS saldo_liquidar  
	 FROM 
		(SELECT 
			CAST(4 AS INTEGER) AS grupo , 
			ocd.cod_estrutural , 
			--CAST(''DESPESAS (INTRA-ORÇAMENTÁRIAS)'' AS VARCHAR(150)) AS descricao , 
			ocd.descricao , 
			publico.fn_nivel(ocd.cod_estrutural) AS nivel , 
			--CAST(1 AS INTEGER) AS nivel , 
			CAST(SUM(tmp.vl_original) AS numeric(14,2)) AS dotacao_inicial , 
			CAST(SUM(tmp.vl_suplementacoes) AS numeric(14,2)) AS creditos_adicionais , 
			CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) AS numeric(14,2)) as dotacao_atualizada , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', true )), 0.00) AS vl_empenhado_bimestre , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', true )), 0.00) AS vl_empenhado_total    , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', true )), 0.00) AS vl_liquidado_bimestre , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', true )), 0.00) AS vl_liquidado_total    , 
			CAST(0.00 AS NUMERIC(14,2)) AS percentual , 
			CAST(0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
		FROM 
			orcamento.conta_despesa ocd 
			INNER JOIN tmp_despesa tmp ON 
				ocd.exercicio = tmp.exercicio AND 
				tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%''
		WHERE 
			--publico.fn_mascarareduzida(ocd.cod_estrutural) like ''4%'' AND 
			publico.fn_nivel(ocd.cod_estrutural) = 4 AND 
			substring(ocd.cod_estrutural, 5, 3) = ''9.1'' --(Somente Intra-Orçamentarias)
		GROUP BY 
			ocd.cod_estrutural , 
			ocd.descricao 
		ORDER BY 
			ocd.cod_estrutural
		) AS tbl 
	) 
	';
	
	EXECUTE stSql;
	
	stSql := '
	UPDATE tmp_rreo_an1_despesa 
	SET percentual = COALESCE(((vl_liquidado_total/dotacao_inicial)*100), 0.00) 
	WHERE vl_liquidado_total > 0 AND dotacao_inicial > 0 ; 
		
	UPDATE tmp_rreo_an1_despesa 
	SET saldo_liquidar = COALESCE((dotacao_atualizada - vl_liquidado_total), 0.00) ;
	';
	EXECUTE stSql;
	
	
	stSql := 'SELECT * FROM tmp_rreo_an1_despesa ORDER BY grupo, nivel';
	
  
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;


	DROP TABLE tmp_suplementacao_suplementada ; 
	DROP TABLE tmp_suplementacao_reduzida ; 
	DROP TABLE tmp_despesa_totais ; 
	DROP TABLE tmp_despesa ; 
	DROP TABLE tmp_rreo_an1_despesa ; 

    RETURN;
 
END;

$$ language 'plpgsql';
