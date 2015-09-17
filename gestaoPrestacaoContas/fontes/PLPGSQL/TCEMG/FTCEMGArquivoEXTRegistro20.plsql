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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
* $Id: FTCEMGArquivoEXTRegistro20.plsql 63539 2015-09-09 19:36:23Z jean $
* $Revision: 63539 $
* $Name$
* $Author: jean $
* $Date: 2015-09-09 16:36:23 -0300 (Qua, 09 Set 2015) $
*
*/

CREATE OR REPLACE FUNCTION tcemg.fn_arquivo_ext_registro20(VARCHAR, VARCHAR, VARCHAR, VARCHAR, CHAR) RETURNS SETOF RECORD AS $$ 
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    chEstilo            ALIAS FOR $5;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];
--  arRetorno           NUMERIC[] := array[0.00];

BEGIN

    stSql := 'CREATE TEMPORARY TABLE tmp_debito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.tipo_valor
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.cod_lote
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.sequencia
                        ,vl.oid as oid_temp
                        ,sc.cod_sistema
                        ,pc.escrituracao
                        ,pc.indicador_superavit
                    FROM
                         contabilidade.plano_conta            as pc
                        ,contabilidade.plano_analitica        as pa
                        ,contabilidade.conta_debito           as cd
                        ,contabilidade.valor_lancamento       as vl
                        ,contabilidade.lancamento             as la
                        ,contabilidade.lote                   as lo
                        ,contabilidade.sistema_contabil       as sc
                        ,contabilidade.historico_contabil     as hc
                        ,tcemg.balancete_extmmaa              AS t_be
                             
                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cd.cod_plano
                    AND     pa.exercicio    = cd.exercicio
                    AND     cd.cod_lote     = vl.cod_lote
                    AND     cd.tipo         = vl.tipo
                    AND     cd.sequencia    = vl.sequencia
                    AND     cd.exercicio    = vl.exercicio
                    AND     cd.tipo_valor   = vl.tipo_valor
                    AND     cd.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = ''D''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio = ' || quote_literal(stExercicio) || '
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio
                    AND     hc.exercicio    = la.exercicio
                    AND     hc.cod_historico = la.cod_historico                    
                    AND     t_be.cod_plano = pa.cod_plano
                    AND     t_be.exercicio = pa.exercicio
                    ORDER BY pc.cod_estrutural
                  ) as tabela
                 WHERE
                ' || stFiltro ;
    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_credito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.tipo_valor
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.cod_lote
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.sequencia
                        ,vl.oid as oid_temp
                        ,sc.cod_sistema
                        ,pc.escrituracao
                        ,pc.indicador_superavit
                    FROM
                         contabilidade.plano_conta          as pc
                        ,contabilidade.plano_analitica      as pa
                        ,contabilidade.conta_credito        as cc
                        ,contabilidade.valor_lancamento     as vl
                        ,contabilidade.lancamento           as la
                        ,contabilidade.lote                 as lo
                        ,contabilidade.sistema_contabil     as sc
                        ,contabilidade.historico_contabil   as hc
                        ,tcemg.balancete_extmmaa              AS t_be
                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cc.cod_plano
                    AND     pa.exercicio    = cc.exercicio
                    AND     cc.cod_lote     = vl.cod_lote
                    AND     cc.tipo         = vl.tipo
                    AND     cc.sequencia    = vl.sequencia
                    AND     cc.exercicio    = vl.exercicio
                    AND     cc.tipo_valor   = vl.tipo_valor
                    AND     cc.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = ''C''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio = ' || quote_literal(stExercicio) || '
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio
                    AND     hc.exercicio    = la.exercicio
                    AND     hc.cod_historico = la.cod_historico
                    AND     t_be.cod_plano = pa.cod_plano
                    AND     t_be.exercicio = pa.exercicio
                    ORDER BY pc.cod_estrutural
                  ) as tabela
                 WHERE
                ' || stFiltro ;
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_debito              ON tmp_debito           (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_credito             ON tmp_credito          (cod_estrutural varchar_pattern_ops, oid_temp);


    CREATE TEMPORARY TABLE tmp_totaliza_debito AS
        SELECT *
        FROM  tmp_debito
        WHERE dt_lote BETWEEN to_date( stDtInicial::varchar , 'dd/mm/yyyy' ) AND   to_date( stDtFinal::varchar , 'dd/mm/yyyy' )
        AND   tipo <> 'I';

    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
        SELECT *
        FROM  tmp_credito
        WHERE dt_lote BETWEEN to_date( stDtInicial::varchar , 'dd/mm/yyyy' ) AND   to_date( stDtFinal::varchar , 'dd/mm/yyyy' )
        AND   tipo <> 'I';

    CREATE UNIQUE INDEX unq_totaliza_credito    ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito     ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, oid_temp);

    IF substr(stDtInicial,1,5) =  '01/01' THEN
        stSqlComplemento := ' dt_lote = to_date( ' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') ';
        stSqlComplemento := stSqlComplemento || ' AND tipo = ''I'' ';
    ELSE
        stSqlComplemento := 'dt_lote BETWEEN to_date( ''01/01/''||substr(to_char(to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') - 1,''dd/mm/yyyy'') ,7) ,''dd/mm/yyyy'') AND to_date( ' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'')-1';
    END IF;
    stSql := 'CREATE TEMPORARY TABLE tmp_totaliza AS
        SELECT * FROM tmp_debito
        WHERE
             ' || stSqlComplemento || '
       UNION
        SELECT * FROM tmp_credito
        WHERE
             ' || stSqlComplemento || '
    ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_totaliza            ON tmp_totaliza         (cod_estrutural varchar_pattern_ops, oid_temp);

    --Verifica estilo de relatório (Analítico ou Sintético)
    IF chEstilo = 'S' THEN
        stSql := ' SELECT
                         pc.cod_estrutural
                        ,publico.fn_nivel(pc.cod_estrutural) as nivel
                        ,pc.nom_conta
                        ,sc.cod_sistema
                        ,pc.indicador_superavit
                        ,0.00 as vl_saldo_anterior
                        ,0.00 as vl_saldo_debitos
                        ,0.00 as vl_saldo_creditos
                        ,0.00 as vl_saldo_atual
                    FROM
                         contabilidade.plano_conta       as pc
                        ,contabilidade.sistema_contabil  as sc
                    WHERE   pc.exercicio = ' || quote_literal(stExercicio) || '
                      AND   pc.exercicio   = sc.exercicio
                      AND   pc.cod_sistema = sc.cod_sistema

                      AND   NOT EXISTS ( SELECT 1
                                           FROM contabilidade.plano_analitica c_pa
                                          WHERE c_pa.cod_conta = pc.cod_conta
                                            AND c_pa.exercicio = pc.exercicio
                                            AND c_pa.exercicio = ' || quote_literal(stExercicio) || '
                                       )
                    ORDER BY cod_estrutural ';
    ELSE
            stSql := '
                SELECT cod_estrutural
                     , tipo_registro
                     , cod_orgao
                     , cod_unidade
                     , tipo_lancamento
                     , sub_tipo
                     , cod_plano AS cod_ext 
                     , cod_recurso
                     , SUM(0.00) as vl_saldo_anterior
                     , SUM(0.00) as vl_saldo_debitos
                     , SUM(0.00) as vl_saldo_creditos
                     , SUM(0.00) as vl_saldo_atual
                     , nat_saldo_anterior_fonte
                     , nat_saldo_atual_fonte
                  FROM ( SELECT pc.cod_estrutural
                              , 20 AS tipo_registro
                              , LPAD(configuracao_entidade.valor::VARCHAR,2,''0'')::VARCHAR AS cod_orgao
                              , LPAD((LPAD(configuracao_entidade.valor::VARCHAR,2, ''0'')||LPAD(configuracao_entidade.cod_entidade::VARCHAR,2, ''0'')), 5, ''0'') AS cod_unidade 
                              , LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'') as tipo_lancamento
                              , LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'') AS sub_tipo
                              , CASE  WHEN (t_be.tipo_lancamento = 1)
                                          THEN CASE WHEN t_be.sub_tipo_lancamento = 1
                                          THEN LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 2
                                          THEN LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 3
                                          THEN LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 4
                                          THEN LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          ELSE ''''
                                     END
                                        WHEN (t_be.tipo_lancamento = 4)
                                        THEN CASE WHEN t_be.sub_tipo_lancamento = 1
                                          THEN LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 2
                                          THEN LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          ELSE ''''
                                     END
                                ELSE ''''
                                END AS desdobra_sub_tipo                                                            
                              , CASE  WHEN (t_be.tipo_lancamento = 1)
                                          THEN CASE WHEN t_be.sub_tipo_lancamento = 1
                                          THEN LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'')||LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 2
                                          THEN LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'')||LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 3
                                          THEN LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'')||LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 4
                                          THEN LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'')||LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          ELSE pa.cod_plano::VARCHAR
                                     END
                                        WHEN (t_be.tipo_lancamento = 4)
                                        THEN CASE WHEN t_be.sub_tipo_lancamento = 1
                                          THEN LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'')||LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 2
                                          THEN LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'')||LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          ELSE pa.cod_plano::VARCHAR
                                     END
                                ELSE pa.cod_plano::VARCHAR
                                END AS cod_plano
                              , COALESCE(c_pr.cod_recurso,''100'') as cod_recurso
                              , 0.00 as vl_saldo_anterior
                              , 0.00 as vl_saldo_debitos
                              , 0.00 as vl_saldo_creditos
                              , 0.00 as vl_saldo_atual
                              , (SELECT plano_analitica.natureza_saldo 
                                   FROM contabilidade.plano_analitica
                                  WHERE plano_analitica.exercicio = (' || quote_literal( stExercicio ) ||'::INTEGER - 1)::VARCHAR
                                    AND plano_analitica.cod_plano = pa.cod_plano
                              ) AS nat_saldo_anterior_fonte
                              , (SELECT plano_analitica.natureza_saldo 
                                   FROM contabilidade.plano_analitica
                                  WHERE plano_analitica.exercicio = '|| quote_literal( stExercicio ) ||'
                                    AND plano_analitica.cod_plano = pa.cod_plano
                              ) AS nat_saldo_atual_fonte
                
                           FROM contabilidade.plano_analitica as pa
                
                           LEFT JOIN tesouraria.transferencia 
                             ON pa.cod_plano = transferencia.cod_plano_credito
                            AND pa.exercicio = transferencia.exercicio
                            AND transferencia.'||stFiltro||'
                
                           JOIN tcemg.balancete_extmmaa AS t_be
                             ON t_be.cod_plano = pa.cod_plano
                            AND t_be.exercicio = pa.exercicio
                            
                           JOIN contabilidade.plano_conta     as pc
                             ON pa.cod_conta = pc.cod_conta
                            and pa.exercicio = pc.exercicio
                            
                           LEFT JOIN (SELECT lote.exercicio
                                      , conta_credito.cod_plano
                                      , lote.tipo
                                      , lote.cod_entidade
                                   FROM contabilidade.lote
                                   JOIN contabilidade.valor_lancamento
                                     ON valor_lancamento.exercicio = lote.exercicio
                                    AND valor_lancamento.cod_entidade = lote.cod_entidade
                                    AND valor_lancamento.tipo = lote.tipo
                                    AND valor_lancamento.cod_lote = lote.cod_lote
                                    AND valor_lancamento.tipo_valor = ''C''
                
                                   JOIN contabilidade.conta_credito
                                     ON conta_credito.exercicio = valor_lancamento.exercicio
                                    AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                                    AND conta_credito.tipo = valor_lancamento.tipo
                                    AND conta_credito.cod_lote = valor_lancamento.cod_lote
                                    AND conta_credito.sequencia = valor_lancamento.sequencia
                                  
                                  WHERE lote.exercicio = '''||stExercicio||'''
                                    AND lote.'||stFiltro||'
                                    AND valor_lancamento.'||stFiltro||'
                                    AND lote.dt_lote BETWEEN TO_DATE('''||stDtInicial||''', ''dd/mm/yyyy'') and TO_DATE('''||stDtFinal||''', ''dd/mm/yyyy'')
                               GROUP BY 1,2,3,4
                
                                ) as valor
                             ON valor.exercicio    = pa.exercicio
                            AND valor.cod_plano    = pa.cod_plano
                
                           LEFT JOIN contabilidade.plano_recurso AS c_pr
                             ON c_pr.cod_plano = pa.cod_plano
                            AND c_pr.exercicio = pa.exercicio
                
                           JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.'||stFiltro||'
                            AND configuracao_entidade.exercicio = '''||stExercicio||'''
                            AND configuracao_entidade.cod_modulo = 55
                            AND configuracao_entidade.parametro = ''tcemg_codigo_orgao_entidade_sicom''
                
                          WHERE t_be.exercicio   = '''||stExercicio||'''
                          
                       GROUP BY cod_estrutural, pa.cod_plano, cod_recurso, cod_orgao, cod_unidade, tipo_lancamento, sub_tipo, desdobra_sub_tipo , t_be.sub_tipo_lancamento, nat_saldo_anterior_fonte, nat_saldo_atual_fonte
                       
                       UNION
                       
                         SELECT pc.cod_estrutural
                              , 20 AS tipo_registro
                              , LPAD(configuracao_entidade.valor::VARCHAR,2,''0'')::VARCHAR AS cod_orgao
                              , LPAD((LPAD(configuracao_entidade.valor::VARCHAR,2, ''0'')||LPAD(configuracao_entidade.cod_entidade::VARCHAR,2, ''0'')), 5, ''0'') AS cod_unidade 
                              , LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'') as tipo_lancamento
                              , LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'') AS sub_tipo
                              , CASE  WHEN (t_be.tipo_lancamento = 1)
                                          THEN CASE WHEN t_be.sub_tipo_lancamento = 1
                                          THEN LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 2
                                          THEN LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 3
                                          THEN LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 4
                                          THEN LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          ELSE ''''
                                     END
                                        WHEN (t_be.tipo_lancamento = 4)
                                        THEN CASE WHEN t_be.sub_tipo_lancamento = 1
                                          THEN LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 2
                                          THEN LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          ELSE ''''
                                     END
                                ELSE ''''
                                END AS desdobra_sub_tipo                                                            
                              , CASE  WHEN (t_be.tipo_lancamento = 1)
                                          THEN CASE WHEN t_be.sub_tipo_lancamento = 1
                                          THEN LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'')||LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 2
                                          THEN LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'')||LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 3
                                          THEN LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'')||LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 4
                                          THEN LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'')||LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          ELSE pa.cod_plano::VARCHAR
                                     END
                                        WHEN (t_be.tipo_lancamento = 4)
                                        THEN CASE WHEN t_be.sub_tipo_lancamento = 1
                                          THEN LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'')||LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          WHEN t_be.sub_tipo_lancamento = 2
                                          THEN LPAD(t_be.tipo_lancamento::VARCHAR,2,''0'')||LPAD(t_be.sub_tipo_lancamento::VARCHAR,4,''0'')
                                          ELSE pa.cod_plano::VARCHAR
                                     END
                                ELSE pa.cod_plano::VARCHAR
                                END AS cod_plano
                              , COALESCE(c_pr.cod_recurso,''100'') as cod_recurso                              
                              , 0.00 as vl_saldo_anterior
                              , 0.00 as vl_saldo_debitos
                              , 0.00 as vl_saldo_creditos
                              , 0.00 as vl_saldo_atual
                              , (SELECT plano_analitica.natureza_saldo 
                                   FROM contabilidade.plano_analitica
                                  WHERE plano_analitica.exercicio = (' || quote_literal( stExercicio ) ||'::INTEGER - 1)::VARCHAR
                                    AND plano_analitica.cod_plano = pa.cod_plano
                              ) AS nat_saldo_anterior_fonte
                              , (SELECT plano_analitica.natureza_saldo 
                                   FROM contabilidade.plano_analitica
                                  WHERE plano_analitica.exercicio = '|| quote_literal( stExercicio ) ||'
                                    AND plano_analitica.cod_plano = pa.cod_plano
                              ) AS nat_saldo_atual_fonte
                
                           FROM contabilidade.plano_analitica as pa
                
                           LEFT JOIN  tesouraria.transferencia 
                             ON pa.cod_plano = transferencia.cod_plano_debito
                            AND pa.exercicio = transferencia.exercicio
                            AND transferencia.'||stFiltro||'
                
                           JOIN tcemg.balancete_extmmaa AS t_be
                             ON t_be.cod_plano = pa.cod_plano
                            AND t_be.exercicio = pa.exercicio
                            
                           JOIN contabilidade.plano_conta     as pc
                             ON pa.cod_conta = pc.cod_conta
                            and pa.exercicio = pc.exercicio
                            
                           LEFT JOIN (SELECT lote.exercicio
                                      , conta_debito.cod_plano
                                      , lote.tipo
                                      , lote.cod_entidade
                                   FROM contabilidade.lote
                                   JOIN contabilidade.valor_lancamento
                                     ON valor_lancamento.exercicio = lote.exercicio
                                    AND valor_lancamento.cod_entidade = lote.cod_entidade
                                    AND valor_lancamento.tipo = lote.tipo
                                    AND valor_lancamento.cod_lote = lote.cod_lote
                                    AND valor_lancamento.tipo_valor = ''D''
                
                                   JOIN contabilidade.conta_debito
                                     ON conta_debito.exercicio = valor_lancamento.exercicio
                                    AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                                    AND conta_debito.tipo = valor_lancamento.tipo
                                    AND conta_debito.cod_lote = valor_lancamento.cod_lote
                                    AND conta_debito.sequencia = valor_lancamento.sequencia
                
                                  WHERE lote.exercicio = '''||stExercicio||'''
                                    AND lote.'||stFiltro||'
                                    AND valor_lancamento.'||stFiltro||'
                                    AND lote.dt_lote BETWEEN TO_DATE('''||stDtInicial||''', ''dd/mm/yyyy'') and TO_DATE('''||stDtFinal||''', ''dd/mm/yyyy'')
                               GROUP BY 1,2,3,4
                
                                ) as valor
                             ON valor.exercicio    = pa.exercicio
                            AND valor.cod_plano    = pa.cod_plano
                
                           LEFT JOIN contabilidade.plano_recurso AS c_pr
                             ON c_pr.cod_plano = pa.cod_plano
                            AND c_pr.exercicio = pa.exercicio
                
                           JOIN administracao.configuracao_entidade
                             ON configuracao_entidade.'||stFiltro||'
                            AND configuracao_entidade.exercicio = '''||stExercicio||'''
                            AND configuracao_entidade.cod_modulo = 55
                            AND configuracao_entidade.parametro = ''tcemg_codigo_orgao_entidade_sicom''
                
                          WHERE t_be.exercicio   = '''||stExercicio||'''

                       GROUP BY cod_estrutural, pa.cod_plano, cod_recurso, cod_orgao, cod_unidade, tipo_lancamento, sub_tipo, desdobra_sub_tipo , t_be.sub_tipo_lancamento, nat_saldo_anterior_fonte, nat_saldo_atual_fonte
                       
                ) AS registros
          
          GROUP BY tipo_registro, cod_orgao, cod_ext , cod_recurso, cod_estrutural, cod_unidade, tipo_lancamento, sub_tipo, nat_saldo_anterior_fonte, nat_saldo_atual_fonte
          ORDER BY cod_ext
            ';
    END IF;
    FOR reRegistro IN EXECUTE stSql
    LOOP
        arRetorno := contabilidade.fn_totaliza_balancete_verificacao( publico.fn_mascarareduzida(reRegistro.cod_estrutural) , stDtInicial, stDtFinal);
        reRegistro.vl_saldo_anterior := arRetorno[1];
        reRegistro.vl_saldo_debitos  := arRetorno[2];
        reRegistro.vl_saldo_creditos := arRetorno[3];
        reRegistro.vl_saldo_atual    := arRetorno[4];

        IF reRegistro.vl_saldo_anterior <> 0.00
          THEN
             IF (substr(reRegistro.cod_estrutural,1,1) = '2')
                  THEN reRegistro.vl_saldo_anterior := (reRegistro.vl_saldo_anterior * -1);
                       reRegistro.nat_saldo_anterior_fonte := 'C';
                  ELSE reRegistro.nat_saldo_anterior_fonte := 'D';
              END IF;
        END IF;

        IF reRegistro.vl_saldo_atual <> 0.00
          THEN
            IF (substr(reRegistro.cod_estrutural,1,1) = '2')
              THEN reRegistro.vl_saldo_atual := (reRegistro.vl_saldo_atual * -1);
                   reRegistro.nat_saldo_atual_fonte := 'C';
              ELSE reRegistro.nat_saldo_atual_fonte := 'D';
            END IF;
        END IF;

        RETURN NEXT reRegistro;
        
    END LOOP;

    DROP INDEX unq_totaliza;
    DROP INDEX unq_totaliza_debito;
    DROP INDEX unq_totaliza_credito;
    DROP INDEX unq_debito;
    DROP INDEX unq_credito;

    DROP TABLE tmp_totaliza;
    DROP TABLE tmp_debito;
    DROP TABLE tmp_credito;
    DROP TABLE tmp_totaliza_debito;
    DROP TABLE tmp_totaliza_credito;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
