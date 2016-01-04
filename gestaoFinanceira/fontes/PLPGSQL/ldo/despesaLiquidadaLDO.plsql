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
CREATE OR REPLACE FUNCTION ldo.fn_despesa_liquidada(VARCHAR, VARCHAR, BOOLEAN, VARCHAR) RETURNS NUMERIC(14,2) AS $$
DECLARE 
    stMascRed   ALIAS FOR $1;
    stExercicio ALIAS FOR $2;
    boRPPS      ALIAS FOR $3;   
    stFiltro    ALIAS FOR $4;
     
	stSQL 		VARCHAR;
	reReg 		RECORD;
	nuLiquidado NUMERIC(14,2);
	nuEstornado	NUMERIC(14,2);
	nuTotal     NUMERIC(14,2);
    crCursor 	REFCURSOR;
    
BEGIN 

    stSql := '
	    SELECT SUM(COALESCE(vl_total,0)) AS vl_total 
          FROM empenho.pre_empenho  
     LEFT JOIN ( SELECT pre_empenho_despesa.exercicio
                      , pre_empenho_despesa.cod_pre_empenho
                      , conta_despesa.cod_estrutural
                   FROM orcamento.conta_despesa  
             INNER JOIN empenho.pre_empenho_despesa
                     ON pre_empenho_despesa.cod_conta   = conta_despesa.cod_conta 
                    AND pre_empenho_despesa.exercicio   = conta_despesa.exercicio 
             INNER JOIN orcamento.despesa 
                     ON pre_empenho_despesa.cod_despesa = despesa.cod_despesa 
                    AND pre_empenho_despesa.exercicio   = despesa.exercicio 
                  WHERE pre_empenho_despesa.exercicio = ''' || stExercicio || '''';
    IF boRPPS = TRUE THEN
        stSql := stSql || ' AND despesa.cod_recurso = 50 ';
    ELSE
        stSql := stSql || ' AND despesa.cod_recurso <> 50 ';
    END IF;
    stSql := stSql || '                  
               ) AS pre_empenho_despesa
            ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio 
           AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho 
	INNER JOIN empenho.empenho 
	        ON empenho.exercicio = pre_empenho.exercicio 
	       AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho 
	INNER JOIN empenho.nota_liquidacao 
	        ON nota_liquidacao.exercicio_empenho = empenho.exercicio 
	       AND nota_liquidacao.cod_entidade      = empenho.cod_entidade 
	       AND nota_liquidacao.cod_empenho       = empenho.cod_empenho 
	INNER JOIN empenho.nota_liquidacao_item 
	        ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio 
	       AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade 
	       AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota 
         WHERE empenho.exercicio = ''' || stExercicio || ''' 
           AND TO_CHAR(nota_liquidacao.dt_liquidacao,''yyyy'') = ''' || stExercicio || ''' 
           AND pre_empenho_despesa.cod_estrutural LIKE ''' || stMascRed || '%''
           ' || stFiltro || ' ';


    OPEN crCursor FOR EXECUTE stSql;
    	FETCH crCursor INTO nuLiquidado;
    CLOSE crCursor;

    stSql := '
	    SELECT SUM(COALESCE(vl_anulado,0)) AS valor
          FROM empenho.pre_empenho  
     LEFT JOIN ( SELECT pre_empenho_despesa.exercicio
                      , pre_empenho_despesa.cod_pre_empenho
                      , conta_despesa.cod_estrutural 
                   FROM orcamento.conta_despesa  
             INNER JOIN empenho.pre_empenho_despesa
                     ON pre_empenho_despesa.cod_conta   = conta_despesa.cod_conta 
                    AND pre_empenho_despesa.exercicio   = conta_despesa.exercicio 
             INNER JOIN orcamento.despesa 
                     ON pre_empenho_despesa.cod_despesa = despesa.cod_despesa 
                    AND pre_empenho_despesa.exercicio   = despesa.exercicio 
                  WHERE pre_empenho_despesa.exercicio = ''' || stExercicio || '''';
    IF boRPPS = TRUE THEN
        stSql := stSql || ' AND despesa.cod_recurso = 50 ';
    ELSE
        stSql := stSql || ' AND despesa.cod_recurso <> 50 ';
    END IF;
    stSql := stSql || '                  
               ) AS pre_empenho_despesa
            ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio 
           AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho 
	INNER JOIN empenho.empenho 
	        ON empenho.exercicio = pre_empenho.exercicio 
	       AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho 
	INNER JOIN empenho.nota_liquidacao 
	        ON nota_liquidacao.exercicio_empenho = empenho.exercicio 
	       AND nota_liquidacao.cod_entidade      = empenho.cod_entidade 
	       AND nota_liquidacao.cod_empenho       = empenho.cod_empenho 
	INNER JOIN empenho.nota_liquidacao_item 
	        ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio 
	       AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade 
	       AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota 
    INNER JOIN empenho.nota_liquidacao_item_anulado
            ON nota_liquidacao_item.exercicio       = nota_liquidacao_item_anulado.exercicio 
           AND nota_liquidacao_item.cod_nota        = nota_liquidacao_item_anulado.cod_nota 
           AND nota_liquidacao_item.cod_entidade    = nota_liquidacao_item_anulado.cod_entidade 
           AND nota_liquidacao_item.num_item        = nota_liquidacao_item_anulado.num_item 
           AND nota_liquidacao_item.cod_pre_empenho = nota_liquidacao_item_anulado.cod_pre_empenho 
           AND nota_liquidacao_item.exercicio_item  = nota_liquidacao_item_anulado.exercicio_item 
         WHERE empenho.exercicio = ''' || stExercicio || ''' 
           AND TO_CHAR(nota_liquidacao_item_anulado.timestamp,''yyyy'') = ''' || stExercicio || ''' 
           AND pre_empenho_despesa.cod_estrutural LIKE ''' || stMascRed || '%''
           ' || stFiltro || ' ';

    OPEN crCursor FOR EXECUTE stSql;
    	FETCH crCursor INTO nuEstornado;
    CLOSE crCursor;

	nuTotal := COALESCE(nuLiquidado,0) - COALESCE(nuEstornado,0);

	if (nuTotal is null) then 
		nuTotal := 0.00;
	end if;

    RETURN nuTotal;
    
END;

$$ LANGUAGE 'plpgsql';
