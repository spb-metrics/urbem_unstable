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
*
* $Id: RelatorioResumoExecucaoRestosPagar.plsql 65265 2016-05-06 18:40:44Z michel $
*/

/**
 * Recebe como paramentro exercicio, entidade, data inicial, data final, exercicio empenho, credor, orgao, unidade
**/

CREATE OR REPLACE FUNCTION empenho.fn_relatorio_resumo_execucao_restos_pagar(varchar,varchar,varchar,varchar,varchar,varchar,integer,integer) RETURNS SETOF RECORD AS $$
DECLARE
  
  stExercicio         ALIAS FOR $1;
  stCodEntidade       ALIAS FOR $2;
  dtInicial           ALIAS FOR $3;
  dtFinal             ALIAS FOR $4;
  stExercicioEmpenho  ALIAS FOR $5;
  stCgmCredor         ALIAS FOR $6;
  inOrgao             ALIAS FOR $7;
  inUnidade           ALIAS FOR $8;

  stExercicioAnterior   VARCHAR := '';
  stSql                 VARCHAR := '';
  stSqlExercicios       VARCHAR := '';
  reRecord              RECORD;
  reRegistro            RECORD;
  
  stExercicioMin        INTEGER;
  stExercicioMax        INTEGER;
  stFiltro              VARCHAR := '';

BEGIN

  stExercicioAnterior := trim(to_char((to_number(stExercicio,'9999')-1),'9999'));

  CREATE TEMPORARY TABLE tmp_restos_tcemg
                       ( cod_empenho                INTEGER,
                         cod_entidade               INTEGER,
                         exercicio                  VARCHAR,
                         credor                     VARCHAR,
                         emissao                    TEXT,
                         vencimento                 TEXT,
                         empenhado                  NUMERIC(14,2),
                         aliquidar                  NUMERIC(14,2),
                         liquidadoapagar            NUMERIC(14,2),
                         anulado                    NUMERIC(14,2),
                         liquidado                  NUMERIC(14,2),
                         pagamento                  NUMERIC(14,2),
                         empenhado_saldo            NUMERIC(14,2),
                         aliquidar_saldo            NUMERIC(14,2),
                         liquidadoapagar_saldo      NUMERIC(14,2)
                       );
                       
  IF stExercicioEmpenho IS NOT NULL AND stExercicioEmpenho <> '' THEN
    stFiltro := ' AND exercicio = '''||stExercicioEmpenho||''' ';
  END IF;

  stSqlExercicios := ' SELECT min(exercicio) AS stExercicioMin
                         FROM empenho.empenho
                        WHERE exercicio < '''||stExercicio||'''
                         '||stFiltro||'
                       ; ';

  stFiltro := '';

  IF inOrgao IS NOT NULL AND inOrgao > 0 THEN
    stFiltro := stFiltro||' AND despesa.num_orgao = '||inOrgao;
  END IF;

  IF inUnidade IS NOT NULL AND inUnidade > 0 THEN
    stFiltro := stFiltro||' AND despesa.num_unidade = '||inUnidade;
  END IF;

  FOR reRecord IN EXECUTE stSqlExercicios LOOP
    stExercicioMin := reRecord.stExercicioMin::INTEGER;
    stExercicioMax := stExercicioAnterior::INTEGER;
    
    IF stExercicioEmpenho IS NOT NULL AND stExercicioEmpenho <> '' THEN
        stExercicioMax := stExercicioEmpenho::INTEGER;
    END IF;

    FOR stExercicioAnteriores IN (stExercicioMin)..(stExercicioMax) LOOP
              stSql := ' SELECT retorno.exercicio
                              , retorno.cod_empenho
                              , retorno.cod_entidade
                              , retorno.credor
                              , retorno.emissao
                              , TO_CHAR(empenho.dt_vencimento,''dd/mm/yyyy'') AS vencimento
                              , sum(saldoempenhado)  AS empenhado
                              , sum(aliquidar)       AS aliquidar
                              , sum(liquidadoapagar) AS liquidadoapagar
                              , sum(COALESCE(empenho_anulado.vl_anulado, 0.00))                         AS anulado
                              , sum(COALESCE(liquidado.vl_total, 0.00))                                 AS liquidado
                              , sum(COALESCE(liquidacao_paga.vl_total, 0.00))                           AS pagamento
                              , sum(saldoempenhado) - sum(COALESCE(empenho_anulado.vl_anulado, 0.00))   AS empenhado_saldo
                              , ( sum(aliquidar) - sum(COALESCE(liquidado.vl_total, 0.00)) - sum(COALESCE(empenho_anulado.vl_anulado, 0.00)) )     AS aliquidar_saldo
                              , ( sum(liquidadoapagar) + sum(COALESCE(liquidado.vl_total, 0.00)) ) - sum(COALESCE(liquidacao_paga.vl_total, 0.00)) AS liquidadoapagar_saldo

                           from empenho.fn_situacao_empenho('''||stCodEntidade||'''
                                                           ,'''||stExercicioAnteriores||'''
                                                           ,''01/01/'||stExercicioAnteriores||'''
                                                           , TO_CHAR(TO_DATE('''||dtInicial||''',''dd/mm/yyyy'')-1, ''dd/mm/yyyy'')
                                                           ,''01/01/'||stExercicioAnteriores||'''
                                                           , TO_CHAR(TO_DATE('''||dtInicial||''',''dd/mm/yyyy'')-1, ''dd/mm/yyyy'')
                                                           ,''01/01/'||stExercicioAnteriores||'''
                                                           , TO_CHAR(TO_DATE('''||dtInicial||''',''dd/mm/yyyy'')-1, ''dd/mm/yyyy'')
                                                           ,''01/01/'||stExercicioAnteriores||'''
                                                           , TO_CHAR(TO_DATE('''||dtInicial||''',''dd/mm/yyyy'')-1, ''dd/mm/yyyy'')
                                                           ,''01/01/'||stExercicioAnteriores||'''
                                                           , TO_CHAR(TO_DATE('''||dtInicial||''',''dd/mm/yyyy'')-1, ''dd/mm/yyyy'')
                                                           ,''01/01/'||stExercicioAnteriores||'''
                                                           , TO_CHAR(TO_DATE('''||dtInicial||''',''dd/mm/yyyy'')-1, ''dd/mm/yyyy'')
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,'''||stCgmCredor||'''
                                                           ,''''
                                                           ,''''
                                                           ) as retorno(cod_empenho         integer,
                                                                        cod_entidade        integer,
                                                                        exercicio           char(4),
                                                                        emissao             text,
                                                                        credor              varchar,
                                                                        empenhado           numeric,
                                                                        anulado             numeric,
                                                                        saldoempenhado      numeric,
                                                                        liquidado           numeric,
                                                                        pago                numeric,
                                                                        aliquidar           numeric,
                                                                        empenhadoapagar     numeric,
                                                                        liquidadoapagar     numeric,
                                                                        cod_recurso         integer
                                                                        )

                     INNER JOIN empenho.empenho
                             ON empenho.exercicio    = retorno.exercicio
                            AND empenho.cod_empenho  = retorno.cod_empenho
                            AND empenho.cod_entidade = retorno.cod_entidade

                     INNER JOIN empenho.pre_empenho
                             ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                            AND pre_empenho.exercicio       = empenho.exercicio

                     INNER JOIN (  SELECT ped.exercicio
                                        , ped.cod_pre_empenho
                                        , d.num_orgao
                                        , d.num_unidade
                                        , d.cod_recurso
                                        , REPLACE(cd.cod_estrutural,''.'', '''') AS cod_estrutural
                                        , d.cod_funcao
                                        , d.cod_subfuncao
                                     FROM empenho.pre_empenho_despesa as ped
                               INNER JOIN orcamento.despesa as d
                                       ON ped.cod_despesa = d.cod_despesa 
                                      AND ped.exercicio = d.exercicio 
                               INNER JOIN orcamento.recurso as r
                                       ON r.cod_recurso = d.cod_recurso
                                      AND r.exercicio = d.exercicio
                               INNER JOIN orcamento.conta_despesa as cd
                                       ON ped.cod_conta = cd.cod_conta 
                                      AND ped.exercicio = cd.exercicio
                                    UNION
                                   SELECT restos_pre_empenho.exercicio
                                        , restos_pre_empenho.cod_pre_empenho
                                        , restos_pre_empenho.num_orgao
                                        , restos_pre_empenho.num_unidade
                                        , restos_pre_empenho.recurso AS cod_recurso
                                        , restos_pre_empenho.cod_estrutural
                                        , restos_pre_empenho.cod_funcao
                                        , restos_pre_empenho.cod_subfuncao
                                     FROM empenho.restos_pre_empenho
                                ) AS despesa
                             ON despesa.exercicio       = pre_empenho.exercicio 
                            AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                      LEFT JOIN (  SELECT exercicio_item
                                        , cod_pre_empenho
                                        , cod_entidade
                                        , SUM(COALESCE(vl_anulado,0.00)) AS vl_anulado
                                     FROM empenho.nota_liquidacao_item_anulado
                                    WHERE TO_DATE(timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE('''||dtInicial||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                                      AND exercicio < '''||stExercicio||'''
                                 GROUP BY exercicio_item
                                        , cod_pre_empenho
                                        , cod_entidade
                                ) AS nota_liquidacao_item_anulado_processado                              
                             ON nota_liquidacao_item_anulado_processado.exercicio_item  = empenho.exercicio
                            AND nota_liquidacao_item_anulado_processado.cod_pre_empenho = empenho.cod_pre_empenho
                            AND nota_liquidacao_item_anulado_processado.cod_entidade    = empenho.cod_entidade

                      LEFT JOIN (  SELECT nota_liquidacao_item.exercicio_item
                                        , nota_liquidacao_item.cod_pre_empenho
                                        , nota_liquidacao_item.cod_entidade
                                        , ( SUM(COALESCE(nota_liquidacao_item.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) ) AS vl_total
                                     FROM empenho.nota_liquidacao_item
                               INNER JOIN empenho.nota_liquidacao
                                       ON nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                                      AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                                      AND nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota                               
                                LEFT JOIN (  SELECT exercicio
                                                  , cod_nota
                                                  , num_item
                                                  , exercicio_item
                                                  , cod_pre_empenho
                                                  , cod_entidade
                                                  , SUM(COALESCE(vl_anulado,0.00)) AS vl_anulado
                                               FROM empenho.nota_liquidacao_item_anulado
                                           GROUP BY exercicio
                                                  , cod_nota
                                                  , num_item
                                                  , exercicio_item
                                                  , cod_pre_empenho
                                                  , cod_entidade
                                          ) AS nota_liquidacao_item_anulado
                                       ON nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                                      AND nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                                      AND nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                                      AND nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                                      AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                                      AND nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                                    WHERE nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('''||dtInicial||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                                 GROUP BY nota_liquidacao_item.exercicio_item
                                        , nota_liquidacao_item.cod_pre_empenho
                                        , nota_liquidacao_item.cod_entidade
                                ) AS liquidado
                             ON liquidado.exercicio_item  = empenho.exercicio
                            AND liquidado.cod_pre_empenho = empenho.cod_pre_empenho
                            AND liquidado.cod_entidade    = empenho.cod_entidade

                      LEFT JOIN (  SELECT empenho_anulado_item.exercicio
                                        , empenho_anulado_item.cod_pre_empenho
                                        , empenho_anulado_item.cod_entidade
                                        , SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) AS vl_anulado
                                     FROM empenho.empenho_anulado_item
                                    WHERE TO_DATE(empenho_anulado_item.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE('''||dtInicial||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                                 GROUP BY empenho_anulado_item.exercicio
                                        , empenho_anulado_item.cod_pre_empenho
                                        , empenho_anulado_item.cod_entidade
                                ) AS empenho_anulado
                             ON empenho_anulado.exercicio       = empenho.exercicio
                            AND empenho_anulado.cod_pre_empenho = empenho.cod_pre_empenho
                            AND empenho_anulado.cod_entidade    = empenho.cod_entidade

                      LEFT JOIN (  SELECT nota_liquidacao.exercicio_empenho
                                        , nota_liquidacao.cod_empenho
                                        , nota_liquidacao.cod_entidade
                                        , ( SUM(COALESCE(nota_liquidacao_paga.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) ) AS vl_total
                                     FROM (  SELECT nota_liquidacao_paga.exercicio
                                                  , nota_liquidacao_paga.cod_entidade
                                                  , nota_liquidacao_paga.cod_nota
                                                  , SUM(nota_liquidacao_paga.vl_pago) AS vl_total
                                               FROM empenho.nota_liquidacao_paga
                                              WHERE TO_DATE(nota_liquidacao_paga.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE('''||dtInicial||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                                           GROUP BY nota_liquidacao_paga.exercicio
                                                  , nota_liquidacao_paga.cod_entidade
                                                  , nota_liquidacao_paga.cod_nota
                                          ) AS nota_liquidacao_paga
                                LEFT JOIN (  SELECT nota_liquidacao_paga_anulada.exercicio
                                                  , nota_liquidacao_paga_anulada.cod_entidade
                                                  , nota_liquidacao_paga_anulada.cod_nota
                                                  , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                                               FROM empenho.nota_liquidacao_paga_anulada
                                              WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE('''||dtInicial||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                                           GROUP BY nota_liquidacao_paga_anulada.exercicio
                                                  , nota_liquidacao_paga_anulada.cod_entidade
                                                  , nota_liquidacao_paga_anulada.cod_nota
                                          ) AS nota_liquidacao_paga_anulada
                                       ON nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                                      AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                                      AND nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                               INNER JOIN empenho.nota_liquidacao
                                       ON nota_liquidacao_paga.exercicio = nota_liquidacao.exercicio
                                      AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                                      AND nota_liquidacao_paga.cod_nota = nota_liquidacao.cod_nota    
                                    WHERE nota_liquidacao.exercicio_empenho < '''||stExercicio||'''
                                 GROUP BY nota_liquidacao.exercicio_empenho
                                        , nota_liquidacao.cod_empenho
                                        , nota_liquidacao.cod_entidade
                                ) AS liquidacao_paga
                             ON liquidacao_paga.exercicio_empenho  = empenho.exercicio
                            AND liquidacao_paga.cod_empenho        = empenho.cod_empenho
                            AND liquidacao_paga.cod_entidade       = empenho.cod_entidade

                          WHERE ( aliquidar > 0 OR liquidadoapagar > 0 )
                            '||stFiltro||'

                       GROUP BY retorno.exercicio
                              , retorno.cod_empenho
                              , retorno.cod_entidade
                              , retorno.credor
                              , retorno.emissao
                              , empenho.dt_vencimento;
              ';

              FOR reRegistro IN EXECUTE stSQL LOOP
                   INSERT
                     INTO tmp_restos_tcemg
                   VALUES ( reRegistro.cod_empenho
                          , reRegistro.cod_entidade
                          , reRegistro.exercicio
                          , reRegistro.credor
                          , reRegistro.emissao
                          , reRegistro.vencimento
                          , reRegistro.empenhado
                          , reRegistro.aliquidar
                          , reRegistro.liquidadoapagar
                          , reRegistro.anulado
                          , reRegistro.liquidado
                          , reRegistro.pagamento
                          , reRegistro.empenhado_saldo
                          , reRegistro.aliquidar_saldo
                          , reRegistro.liquidadoapagar_saldo
                          );
              END LOOP;
    END LOOP;
  END LOOP;

  stSql := '
            SELECT *
            FROM tmp_restos_tcemg
  ';

  FOR reRegistro IN EXECUTE stSql
  LOOP
      RETURN next reRegistro;
  END LOOP;

  DROP TABLE tmp_restos_tcemg;

END;

$$ language 'plpgsql';
