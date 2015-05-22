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
/**
 * Função que busca os dados de receita capital
 * Data de Criação   : 06/03/2015
 * 
 * @author Analista: Dagiane Vieira
 * @author Desenvolvedor: Michel Teixeira
 *
 * $Id: receitaCapital.plsql 62566 2015-05-20 18:49:20Z arthur $
*/

CREATE OR REPLACE FUNCTION tcemg.fn_receita_capital(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    stDataInicial       ALIAS FOR $3;
    stDataFinal         ALIAS FOR $4;
    stSql               VARCHAR := '';
    reRegistro          RECORD;

BEGIN 

stSql := '  CREATE TEMPORARY TABLE tmp_balancete_receita AS 
                SELECT * 
                FROM orcamento.fn_balancete_receita('''|| stExercicio ||''', ''''
                                                    , '''|| stDataInicial ||'''
                                                    , '''|| stDataFinal ||'''
                                                    , '''|| stCodEntidade ||'''
                                                    , '''', '''', '''', '''', '''', '''', '''')
                AS retorno (
                        cod_estrutural      varchar,                                           
                        receita             integer,                                           
                        recurso             varchar,                                           
                        descricao           varchar,                                           
                        valor_previsto      numeric,                                           
                        arrecadado_periodo  numeric,                                           
                        arrecadado_ano      numeric,                                           
                        diferenca           numeric
                )
        ';
EXECUTE stSql;

stSql :='   SELECT 
                    *
            FROM(                    
                    SELECT 
                            ''01''::VARCHAR AS cod_tipo       
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.2.0.0.00.00.00.00.00%'' 
                            ) AS rec_alienacao
                            
                            ,( SELECT (totais.tabela1 - totais.tabela2)::varchar as valor
                                 FROM (
                                       SELECT ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.3%'' 
                                              ) AS tabela1
                                            ,
                                              ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.3.0.0.40%''
                                               ) AS tabela2
                                     ) AS totais
                            ) AS rec_amort
                            
                            ,( SELECT (totais.tabela1 - totais.tabela2)::varchar as valor
                                 FROM (
                                       SELECT ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.4.0.0.00.00.00.00.00''
                                              ) AS tabela1
                                            ,
                                              ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                  WHERE cod_estrutural LIKE ''2.4.7.0.00.00.00.00.00''
                                               ) AS tabela2
                                     ) AS totais
                            ) AS rec_transf_capital
                            
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.4.7.0%''
                            ) AS rec_convenios
                            
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.5%''                    
                            ) AS out_rec_cap
                            
                            ,( SELECT (totais.tabela1 - totais.tabela2)::varchar as valor
                                 FROM (
                                       SELECT ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.1.0.0.00.00.00.00.00''
                                              ) AS tabela1
                                            ,
                                              ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.1.1.4.99%''
                                               ) AS tabela2
                                     ) AS totais
                            ) AS rec_ret_op_cred
                            
                            ,0::VARCHAR AS rec_privat
                            
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.3.0.0.40%'' 
                            ) AS rec_ref_divida
                            
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.1.1.4.99%''
                            ) AS rec_out_op_cred
                            
                            ,0::VARCHAR AS deducoes
                    
                    UNION

                    SELECT
                            ''02''::VARCHAR AS cod_tipo
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.2.0.0.00.00.00.00.00%'' 
                            ) AS rec_alienacao
                                                 
                            ,( SELECT (totais.tabela1 - totais.tabela2)::varchar as valor
                                 FROM (
                                       SELECT ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.3%'' 
                                              ) AS tabela1
                                            ,
                                              ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.3.0.0.40%''
                                               ) AS tabela2
                                     ) AS totais
                            ) AS rec_amort
                            
                            ,( SELECT (totais.tabela1 - totais.tabela2)::varchar as valor
                                 FROM (
                                       SELECT ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.4.0.0.00.00.00.00.00''
                                              ) AS tabela1
                                            ,
                                              ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                  WHERE cod_estrutural LIKE ''2.4.7.0.00.00.00.00.00''
                                               ) AS tabela2
                                     ) AS totais
                            ) AS rec_transf_capital
                            
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.4.7.0%''          
                            ) AS rec_convenios
                            
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.5%''                    
                            ) AS out_rec_cap
                            
                            ,( SELECT (totais.tabela1 - totais.tabela2)::varchar as valor
                                 FROM (
                                       SELECT ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.1.0.0.00.00.00.00.00''
                                              ) AS tabela1
                                            ,
                                              ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.1.1.4.99%''
                                               ) AS tabela2
                                     ) AS totais
                            ) AS rec_ret_op_cred
                            
                            ,0::VARCHAR AS rec_privat
                            
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.3.0.0.40%'' 
                            ) AS rec_ref_divida
                            
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.1.1.4.99%''
                            ) AS rec_out_op_cred
                            
                            ,0::VARCHAR AS deducoes

                    UNION

                    SELECT
                            ''03''::VARCHAR AS cod_tipo
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.2.0.0.00.00.00.00.00%'' 
                            ) AS rec_alienacao
                                                 
                            ,( SELECT (totais.tabela1 - totais.tabela2)::varchar as valor
                                 FROM (
                                       SELECT ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.3%'' 
                                              ) AS tabela1
                                            ,
                                              ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.3.0.0.40%''
                                               ) AS tabela2
                                     ) AS totais
                            ) AS rec_amort
                            
                            ,( SELECT (totais.tabela1 - totais.tabela2)::varchar as valor
                                 FROM (
                                       SELECT ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.4.0.0.00.00.00.00.00''
                                              ) AS tabela1
                                            ,
                                              ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                  WHERE cod_estrutural LIKE ''2.4.7.0.00.00.00.00.00''
                                               ) AS tabela2
                                     ) AS totais
                            ) AS rec_transf_capital
                            
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.4.7.0%''          
                            ) AS rec_convenios
                            
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.5%''                    
                            ) AS out_rec_cap
                            
                            ,( SELECT (totais.tabela1 - totais.tabela2)::varchar as valor
                                 FROM (
                                       SELECT ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.1.0.0.00.00.00.00.00''
                                              ) AS tabela1
                                            ,
                                              ( SELECT COALESCE(SUM(valor_previsto),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.1.1.4.99%''
                                               ) AS tabela2
                                     ) AS totais
                            ) AS rec_ret_op_cred
                            
                            ,0::VARCHAR AS rec_privat
                            
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.3.0.0.40%'' 
                            ) AS rec_ref_divida
                            
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.1.1.4.99%''
                            ) AS rec_out_op_cred
                            
                            ,0::VARCHAR AS deducoes

                    UNION

                    SELECT
                            ''04''::VARCHAR AS cod_tipo
                            ,( SELECT COALESCE(SUM(arrecadado_periodo * -1),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.2.0.0.00.00.00.00.00%'' 
                            ) AS rec_alienacao
                           
                            ,( SELECT (totais.tabela1 - totais.tabela2)::varchar as valor
                                 FROM (
                                       SELECT ( SELECT COALESCE(SUM(arrecadado_periodo *-1 ),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.3%'' 
                                              ) AS tabela1
                                            ,
                                              ( SELECT COALESCE(SUM(arrecadado_periodo *-1 ),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.3.0.0.40%''
                                               ) AS tabela2
                                     ) AS totais
                            ) AS rec_amort
                                                        
                            ,( SELECT (totais.tabela1 - totais.tabela2)::varchar as valor
                                 FROM (
                                       SELECT ( SELECT COALESCE(SUM(arrecadado_periodo *-1 ),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.4.0.0.00.00.00.00.00''
                                              ) AS tabela1
                                            ,
                                              ( SELECT COALESCE(SUM(arrecadado_periodo *-1),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                  WHERE cod_estrutural LIKE ''2.4.7.0.00.00.00.00.00''
                                               ) AS tabela2
                                     ) AS totais
                            ) AS rec_transf_capital
                            
                            ,( SELECT COALESCE(SUM(arrecadado_periodo *-1),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.4.7.0%''          
                            ) AS rec_convenios
                            
                            ,( SELECT COALESCE(SUM(arrecadado_periodo *-1),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.5%''                    
                            ) AS out_rec_cap
                           
                            ,( SELECT (totais.tabela1 - totais.tabela2)::varchar as valor
                                 FROM (
                                       SELECT ( SELECT COALESCE(SUM(arrecadado_periodo *-1),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.1.0.0.00.00.00.00.00''
                                              ) AS tabela1
                                            ,
                                              ( SELECT COALESCE(SUM(arrecadado_periodo *-1),0) as valor 
                                                  FROM tmp_balancete_receita 
                                                 WHERE cod_estrutural LIKE ''2.1.1.4.99%''
                                               ) AS tabela2
                                     ) AS totais
                            ) AS rec_ret_op_cred
                            
                            ,0::VARCHAR AS rec_privat
                            
                            ,( SELECT COALESCE(SUM(arrecadado_periodo *-1),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.3.0.0.40%'' 
                            ) AS rec_ref_divida
                            
                            ,( SELECT COALESCE(SUM(arrecadado_periodo *-1),0)::VARCHAR as valor 
                                 FROM tmp_balancete_receita 
                                WHERE cod_estrutural LIKE ''2.1.1.4.99%''
                            ) AS rec_out_op_cred
                            
                            ,0::VARCHAR AS deducoes
            ) as retorno
            ORDER BY cod_tipo
        ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_balancete_receita;

    RETURN;
    
END;
$$ LANGUAGE 'plpgsql';