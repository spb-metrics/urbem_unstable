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
    * Arquivo de mapeamento para a função que busca os dados de receita previdenciária
    * Data de Criação   : 22/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_receita_prev(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    inMes                   ALIAS FOR $3;
    stSql                   VARCHAR := '';
    dtInicial               VARCHAR := '';
    dtFinal                 VARCHAR := '';    
    arDatas                 VARCHAR[];
    reRegistro              RECORD;

BEGIN
    
    arDatas   := publico.mes(stExercicio,inMes);
    dtInicial := arDatas[0];    
    dtFinal   := arDatas[1];

    stSql :='CREATE TEMPORARY TABLE tmp_balancete_receita AS 
            (
                SELECT
                        cod_estrutural                                                 
                        ,ABS(valor_previsto) as valor_previsto
                        ,ABS(arrecadado_periodo) as arrecadado_periodo
                FROM orcamento.fn_balancete_receita('''||stExercicio||''','''','''||dtInicial||''','''||dtFinal||''','''||stCodEntidades||'''
                                                    ,'''','''','''','''','''','''','''') 
                as retorno(                      
                        cod_estrutural      varchar,                                           
                        receita             integer,                                           
                        recurso             varchar,                                           
                        descricao           varchar,                                           
                        valor_previsto      numeric,                                           
                        arrecadado_periodo  numeric,                                           
                        arrecadado_ano      numeric,                                           
                        diferenca           numeric                                           
                )
                ORDER BY cod_estrutural
            )
    ';
    EXECUTE stSql;

    stSql :='
            SELECT 
                    *
            FROM (
                    SELECT 
                            ''01''::VARCHAR AS cod_tipo
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.01.00.00.00%'' ) AS contrib_pat
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.07%''          ) AS contrib_serv_ativo
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.09%''          ) AS contrib_serv_inat_pens
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.3%''                    ) AS rec_patrimoniais
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''2.2%''                    ) AS alienacao_bens
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''2.5%''                    ) AS outras_rec_cap
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.99.00.10.00.00%'' ) AS comp_prev
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.9.9.0.99%''             ) AS outras_rec
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''9.%''                     ) AS deducoes_receita
                    
                    UNION

                    SELECT
                            ''02''::VARCHAR AS cod_tipo
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.01.00.00.00%'' ) AS contrib_pat
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.07%''          ) AS contrib_serv_ativo
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.09%''          ) AS contrib_serv_inat_pens
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.3%''                    ) AS rec_patrimoniais
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''2.2%''                    ) AS alienacao_bens
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''2.5%''                    ) AS outras_rec_cap
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.99.00.10.00.00%'' ) AS comp_prev
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.9.9.0.99%''             ) AS outras_rec
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''9.%''                     ) AS deducoes_receita

                    UNION

                    SELECT
                            ''04''::VARCHAR AS cod_tipo
                            ,( SELECT COALESCE(SUM(arrecadado_periodo),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.01.00.00.00%'' ) AS contrib_pat
                            ,( SELECT COALESCE(SUM(arrecadado_periodo),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.07%''          ) AS contrib_serv_ativo
                            ,( SELECT COALESCE(SUM(arrecadado_periodo),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.09%''          ) AS contrib_serv_inat_pens
                            ,( SELECT COALESCE(SUM(arrecadado_periodo),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.3%''                    ) AS rec_patrimoniais
                            ,( SELECT COALESCE(SUM(arrecadado_periodo),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''2.2%''                    ) AS alienacao_bens
                            ,( SELECT COALESCE(SUM(arrecadado_periodo),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''2.5%''                    ) AS outras_rec_cap
                            ,( SELECT COALESCE(SUM(arrecadado_periodo),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.99.00.10.00.00%'' ) AS comp_prev
                            ,( SELECT COALESCE(SUM(arrecadado_periodo),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.9.9.0.99%''             ) AS outras_rec
                            ,( SELECT COALESCE(SUM(arrecadado_periodo),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''9.%''                     ) AS deducoes_receita

            ) AS retorno
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_balancete_receita;

    RETURN;
END;
$$ language 'plpgsql';
