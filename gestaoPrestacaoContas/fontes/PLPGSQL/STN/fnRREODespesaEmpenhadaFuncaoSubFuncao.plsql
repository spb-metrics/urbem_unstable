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
CREATE OR REPLACE FUNCTION stn.fn_rreo_despesa_empenhada_funcao_subfuncao(stMascRed VARCHAR, stExercicio VARCHAR, stEntidades VARCHAR, stDtIni VARCHAR, stDtFim VARCHAR, stRecurso VARCHAR, stFuncao VARCHAR, stSubFuncao VARCHAR, boIntra BOOLEAN) RETURNS NUMERIC(14,2) AS 
$$

DECLARE 
	stSQL 		VARCHAR;
	reReg 		RECORD;
	nuEmpenhado NUMERIC(14,2);
	nuAnulado 	NUMERIC(14,2);
	nuTotal 	NUMERIC(14,2);
    crCursor 	REFCURSOR;

BEGIN 

	stSql := '
	  SELECT coalesce(sum(vl_total), 0.00) as vl_total 
	    FROM orcamento.conta_despesa AS ocd 
  INNER JOIN orcamento.despesa AS ode
          ON ode.exercicio = ocd.exercicio
         AND ode.cod_conta = ocd.cod_conta 
  INNER JOIN empenho.pre_empenho_despesa AS ped
          ON ped.exercicio = ode.exercicio
         AND ped.cod_despesa = ode.cod_despesa 
  INNER JOIN empenho.pre_empenho AS pe
          ON ped.exercicio = pe.exercicio
         AND ped.cod_pre_empenho = pe.cod_pre_empenho 
  INNER JOIN empenho.item_pre_empenho AS ipe
          ON ipe.cod_pre_empenho = pe.cod_pre_empenho
         AND ipe.exercicio = pe.exercicio 
  INNER JOIN empenho.empenho AS e
          ON e.exercicio = pe.exercicio
         AND e.cod_pre_empenho = pe.cod_pre_empenho 
	   WHERE e.exercicio = '''||stExercicio||'''
         AND e.cod_entidade IN ('||stEntidades||')
         AND e.dt_empenho BETWEEN to_date(''' || stDtIni || ''', ''dd/mm/yyyy'')
                              AND to_date(''' || stDtFim || ''', ''dd/mm/yyyy'')
         AND ocd.cod_estrutural LIKE ''' || stMascRed || '%'' ';
	
	-- não inclui as despesas intra-orçamentárias
	IF (boIntra = TRUE) THEN 
		stSql := stSql || ' AND SUBSTRING(ocd.cod_estrutural, 5, 3) <> ''9.1''  ';
	END IF;
	IF (stFuncao <> '') THEN 
		stSql := stSql || ' AND ode.cod_funcao IN ('||stFuncao||')  ';
	END IF;
	IF (stSubFuncao <> '') THEN 
		stSql := stSql || ' AND ode.cod_subfuncao IN ('||stSubFuncao||')  ';
	END IF; 
	IF (stRecurso <> '') THEN 
		stSql := stSql || ' AND ode.cod_recurso IN ('||stRecurso||')  ';
	END IF;

    OPEN crCursor FOR EXECUTE stSql;
    	FETCH crCursor INTO nuEmpenhado;
    CLOSE crCursor;


	stSQL := '
	  SELECT coalesce(sum(eai.vl_anulado), 0.00) as valor
        FROM orcamento.conta_despesa AS ocd 
  INNER JOIN orcamento.despesa AS ode
          ON ode.exercicio = ocd.exercicio
         AND ode.cod_conta = ocd.cod_conta 
  INNER JOIN empenho.pre_empenho_despesa AS ped
          ON ped.exercicio = ode.exercicio
         AND ped.cod_despesa = ode.cod_despesa 
  INNER JOIN empenho.pre_empenho AS pe
          ON ped.exercicio = pe.exercicio
         AND ped.cod_pre_empenho = pe.cod_pre_empenho 
  INNER JOIN empenho.empenho AS e
          ON e.exercicio = pe.exercicio
         AND e.cod_pre_empenho = pe.cod_pre_empenho 
  INNER JOIN empenho.empenho_anulado AS ea
          ON ea.exercicio = e.exercicio
         AND ea.cod_entidade = e.cod_entidade
         AND ea.cod_empenho = e.cod_empenho 		
  INNER JOIN empenho.empenho_anulado_item AS eai
          ON eai.exercicio = ea.exercicio
         AND eai.cod_entidade = ea.cod_entidade
         AND eai.cod_empenho = ea.cod_empenho
         AND eai.timestamp = ea.timestamp 
	   WHERE e.exercicio = '''||stExercicio||'''
         AND e.cod_entidade IN ('||stEntidades||')
         AND to_date(to_char(ea.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'') BETWEEN to_date(''' || stDtIni || ''', ''dd/mm/yyyy'')
                                                                                AND to_date(''' || stDtFim || ''', ''dd/mm/yyyy'')
         AND ocd.cod_estrutural LIKE '''||stMascRed||'%'' ';
		
	
	-- não inclui as despesas intra-orçamentárias
	
	IF (boIntra = TRUE) THEN 
		stSql := stSql || ' AND SUBSTRING(ocd.cod_estrutural, 5, 3) <> ''9.1''  ';
	END IF;
	IF (stFuncao <> '') THEN 
		stSql := stSql || ' AND ode.cod_funcao IN ('||stFuncao||')  ';
	END IF;
	IF (stSubFuncao <> '') THEN 
		stSql := stSql || ' AND ode.cod_subfuncao IN ('||stSubFuncao||')  ';
	END IF;
    IF (stRecurso <> '') THEN 
		stSql := stSql || ' AND ode.cod_recurso IN ('||stRecurso||')  ';
	END IF;
	
    OPEN crCursor FOR EXECUTE stSql;
    	FETCH crCursor INTO nuAnulado;
    CLOSE crCursor;
    

	if (nuTotal is null) then 
		nuTotal := 0.00;
	end if;

	nuTotal := nuEmpenhado - nuAnulado;

    RETURN nuTotal;
	
END;

$$ LANGUAGE 'plpgsql';
