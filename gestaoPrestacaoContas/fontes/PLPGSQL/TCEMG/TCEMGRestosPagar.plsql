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
 * Script de função PLPGSQL - Relatório STN - RREO - Anexo 9
 *
 * URBEM Soluções de Gestão Pública Ltda
 * www.urbem.cnm.org.br
 *
 * Casos de uso: uc-06.01.10
 * 
 * $Id: $
 */

/**
 * Recebe como paramentro exercicio, entidade, periodo
 */

CREATE OR REPLACE FUNCTION tcemg.fn_restos_pagar(varchar,varchar,integer) RETURNS SETOF RECORD AS $$
DECLARE
  
  stExercicio         ALIAS FOR $1;
  stCodEntidade       ALIAS FOR $2;
  inMes               ALIAS FOR $3;

  dtInicial           VARCHAR := '';
  dtFinal             VARCHAR := '';
  stExercicioAnterior VARCHAR := '';
  stExercicioAnteriores VARCHAR := '';
  stSql               VARCHAR := '';  
  reRegistro          RECORD;

BEGIN
   
  dtInicial := '01/01/' || stExercicio;
  stExercicioAnterior := trim(to_char((to_number(stExercicio,'9999')-1),'9999'));
  stExercicioAnteriores := trim(to_char((to_number(stExercicioAnterior,'9999')-1),'9999'));
  dtFinal := TO_CHAR(last_day(TO_DATE(stExercicio || '-' || inMes || '-' || '01','yyyy-mm-dd')),'dd/mm/yyyy');
  
  -- cria a tabela temporaria para o valor processado no exercicios anteriores
  stSql := '
    CREATE TEMPORARY TABLE tmp_processados_exercicios_anteriores AS

      SELECT liquidado.cod_empenho
           , liquidado.cod_entidade
           , liquidado.exercicio
           , sw_cgm.nom_cgm AS nom_entidade
           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN ''ÓRGÃO NÃO INFORMADO''
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.nom_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.nom_orgao
             END AS nom_orgao
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN 99
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.num_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.num_orgao
             END AS num_orgao      

           , ( SUM(COALESCE(liquidado.vl_liquidado,0.00)) - SUM(COALESCE(pago.vl_pago,0.00)) ) AS vl_total
        FROM (  SELECT pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_empenho
                     , empenho.cod_entidade
                     , ( SUM(liquidado.vl_total) ) AS vl_liquidado
                  FROM empenho.nota_liquidacao
            
            INNER JOIN empenho.empenho
                    ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                   AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                   AND empenho.cod_empenho = nota_liquidacao.cod_empenho
            
            INNER JOIN empenho.pre_empenho
                    ON pre_empenho.exercicio = empenho.exercicio
                   AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
            
             LEFT JOIN (  SELECT nota_liquidacao_item.exercicio
                               , nota_liquidacao_item.cod_entidade
                               , nota_liquidacao_item.cod_nota
                               , ( SUM(COALESCE(nota_liquidacao_item.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) ) AS vl_total
                            FROM empenho.nota_liquidacao_item
                       LEFT JOIN (  SELECT exercicio
                                         , cod_nota
                                         , num_item
                                         , exercicio_item
                                         , cod_pre_empenho
                                         , cod_entidade
                                         , SUM(COALESCE(vl_anulado,0.00)) AS vl_anulado
                                      FROM empenho.nota_liquidacao_item_anulado
                                     WHERE TO_DATE(timestamp::TEXT,''yyyy-mm-dd'') <= TO_DATE(''31/12/'||stExercicioAnteriores||''',''dd/mm/yyyy'') 
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
                        GROUP BY nota_liquidacao_item.exercicio
                               , nota_liquidacao_item.cod_entidade
                               , nota_liquidacao_item.cod_nota
            
                       ) AS liquidado
                    ON liquidado.exercicio = nota_liquidacao.exercicio
                   AND liquidado.cod_entidade = nota_liquidacao.cod_entidade
                   AND liquidado.cod_nota = nota_liquidacao.cod_nota
            
                 WHERE empenho.exercicio < '''||stExercicioAnterior||'''
                   AND nota_liquidacao.dt_liquidacao <= TO_DATE(''31/12/'||stExercicioAnteriores||''',''dd/mm/yyyy'') 
                   AND empenho.cod_entidade IN ('||stCodEntidade||')

              GROUP BY pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
             ) AS liquidado
     LEFT JOIN (  SELECT ( SUM(liquidacao_paga.vl_total) ) AS vl_pago
                       , pre_empenho.exercicio
                       , pre_empenho.cod_pre_empenho
                       , empenho.cod_empenho
                       , empenho.cod_entidade
              
                    FROM empenho.nota_liquidacao
              
              INNER JOIN empenho.empenho
                      ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho = nota_liquidacao.cod_empenho
            
              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
              
              INNER JOIN (  SELECT nota_liquidacao_paga.exercicio
                                 , nota_liquidacao_paga.cod_entidade
                                 , nota_liquidacao_paga.cod_nota
                                 , ( SUM(COALESCE(nota_liquidacao_paga.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) ) AS vl_total
              
                              FROM (  SELECT nota_liquidacao_paga.exercicio
                                            , nota_liquidacao_paga.cod_entidade
                                           , nota_liquidacao_paga.cod_nota
                                           , SUM(nota_liquidacao_paga.vl_pago) AS vl_total
                                        FROM empenho.nota_liquidacao_paga
                                       WHERE TO_DATE(nota_liquidacao_paga.timestamp::TEXT,''yyyy-mm-dd'') <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                                    GROUP BY nota_liquidacao_paga.exercicio
                                           , nota_liquidacao_paga.cod_entidade
                                           , nota_liquidacao_paga.cod_nota
                                   ) AS nota_liquidacao_paga
              
                         LEFT JOIN (  SELECT nota_liquidacao_paga_anulada.exercicio
                                           , nota_liquidacao_paga_anulada.cod_entidade
                                           , nota_liquidacao_paga_anulada.cod_nota
                                           , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                                        FROM empenho.nota_liquidacao_paga_anulada
                                       WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::TEXT,''yyyy-mm-dd'') < TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                                    GROUP BY nota_liquidacao_paga_anulada.exercicio
                                           , nota_liquidacao_paga_anulada.cod_entidade
                                           , nota_liquidacao_paga_anulada.cod_nota
                                   ) AS nota_liquidacao_paga_anulada
                                ON nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio
                               AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                               AND nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
                          GROUP BY nota_liquidacao_paga.exercicio
                                 , nota_liquidacao_paga.cod_entidade
                                 , nota_liquidacao_paga.cod_nota
              
                         ) AS liquidacao_paga
                      ON liquidacao_paga.exercicio = nota_liquidacao.exercicio
                     AND liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                     AND liquidacao_paga.cod_nota = nota_liquidacao.cod_nota
              
                 WHERE empenho.exercicio < '''||stExercicioAnterior||'''
                   AND nota_liquidacao.dt_liquidacao <= TO_DATE(''31/12/'||stExercicioAnteriores||''',''dd/mm/yyyy'')
                   AND empenho.cod_entidade IN ('||stCodEntidade||')
    
              GROUP BY pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_empenho
                     , empenho.cod_entidade

               ) AS pago
            ON pago.exercicio = liquidado.exercicio
           AND pago.cod_pre_empenho = liquidado.cod_pre_empenho
           AND pago.cod_entidade = liquidado.cod_entidade
           AND pago.cod_empenho = liquidado.cod_empenho

-- inner para achar a entidade a que ele pertence
    INNER JOIN orcamento.entidade
            ON entidade.exercicio = liquidado.exercicio
           AND entidade.cod_entidade = liquidado.cod_entidade
    
    INNER JOIN sw_cgm
            ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
     LEFT JOIN empenho.pre_empenho_despesa
            ON pre_empenho_despesa.exercicio = liquidado.exercicio
           AND pre_empenho_despesa.cod_pre_empenho = liquidado.cod_pre_empenho
      
     LEFT JOIN orcamento.despesa
            ON despesa.exercicio = pre_empenho_despesa.exercicio
           AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
           
     LEFT JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN orcamento.orgao
            ON orgao.exercicio = '''||stExercicio||'''
           AND orgao.num_orgao = despesa.num_orgao

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = liquidado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = liquidado.cod_pre_empenho

     LEFT JOIN orcamento.orgao AS orgao_implantado
            ON orgao_implantado.exercicio = '''||stExercicio||'''
           AND orgao_implantado.num_orgao = restos_pre_empenho.num_orgao

      GROUP BY liquidado.cod_empenho
             , liquidado.cod_entidade
             , liquidado.exercicio
             , sw_cgm.nom_cgm
             , restos_pre_empenho.cod_estrutural
             , conta_despesa.cod_estrutural
             , orgao.num_orgao
             , orgao.nom_orgao
             , orgao_implantado.nom_orgao
             , orgao_implantado.num_orgao
             , despesa.dt_criacao

        HAVING ( SUM(COALESCE(liquidado.vl_liquidado,0.00)) - SUM(COALESCE(pago.vl_pago,0.00)) ) > 0 

  ';
  
  EXECUTE stSql;

  -- cria a tabela temporaria para o valor processado no exercicio anterior
  stSql := '
    CREATE TEMPORARY TABLE tmp_processados_exercicio_anterior AS

      SELECT liquidado.cod_empenho
           , liquidado.cod_entidade
           , liquidado.exercicio
           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , sw_cgm.nom_cgm AS nom_entidade
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN ''ÓRGÃO NÃO INFORMADO''
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.nom_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.nom_orgao
             END AS nom_orgao
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN 99
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.num_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.num_orgao
             END AS num_orgao  
           , ( SUM(COALESCE(liquidado.vl_liquidado,0.00)) - SUM(COALESCE(pago.vl_pago,0.00)) ) AS vl_total
        FROM (  SELECT pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
                     , ( SUM(liquidado.vl_total) ) AS vl_liquidado
                  FROM empenho.nota_liquidacao
            
            INNER JOIN empenho.empenho
                    ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                   AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                   AND empenho.cod_empenho = nota_liquidacao.cod_empenho
            
            INNER JOIN empenho.pre_empenho
                    ON pre_empenho.exercicio = empenho.exercicio
                   AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
            
             LEFT JOIN (  SELECT nota_liquidacao_item.exercicio
                               , nota_liquidacao_item.cod_entidade
                               , nota_liquidacao_item.cod_nota
                               , ( SUM(COALESCE(nota_liquidacao_item.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) ) AS vl_total
                            FROM empenho.nota_liquidacao_item
                       LEFT JOIN (  SELECT exercicio
                                         , cod_nota
                                         , num_item
                                         , exercicio_item
                                         , cod_pre_empenho
                                         , cod_entidade
                                         , SUM(COALESCE(vl_anulado,0.00)) AS vl_anulado
                                      FROM empenho.nota_liquidacao_item_anulado
                                     WHERE TO_DATE(timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
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
                        GROUP BY nota_liquidacao_item.exercicio
                               , nota_liquidacao_item.cod_entidade
                               , nota_liquidacao_item.cod_nota
            
                       ) AS liquidado
                    ON liquidado.exercicio = nota_liquidacao.exercicio
                   AND liquidado.cod_entidade = nota_liquidacao.cod_entidade
                   AND liquidado.cod_nota = nota_liquidacao.cod_nota
            
                 WHERE empenho.exercicio <= '''||stExercicioAnterior||'''
                   AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                   AND empenho.cod_entidade IN ('||stCodEntidade||')

              GROUP BY pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
             ) AS liquidado
     LEFT JOIN (  SELECT ( SUM(liquidacao_paga.vl_total) ) AS vl_pago
                       , pre_empenho.exercicio
                       , pre_empenho.cod_pre_empenho
                       , empenho.cod_entidade
                       , empenho.cod_empenho 
                    FROM empenho.nota_liquidacao
              
              INNER JOIN empenho.empenho
                      ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho = nota_liquidacao.cod_empenho
            
              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
              
              INNER JOIN (  SELECT nota_liquidacao_paga.exercicio
                                 , nota_liquidacao_paga.cod_entidade
                                 , nota_liquidacao_paga.cod_nota
                                 , ( SUM(COALESCE(nota_liquidacao_paga.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) ) AS vl_total
              
                              FROM (  SELECT nota_liquidacao_paga.exercicio
                                            , nota_liquidacao_paga.cod_entidade
                                           , nota_liquidacao_paga.cod_nota
                                           , SUM(nota_liquidacao_paga.vl_pago) AS vl_total
                                        FROM empenho.nota_liquidacao_paga
                                       WHERE TO_DATE(nota_liquidacao_paga.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                                    GROUP BY nota_liquidacao_paga.exercicio
                                           , nota_liquidacao_paga.cod_entidade
                                           , nota_liquidacao_paga.cod_nota
                                   ) AS nota_liquidacao_paga
              
                         LEFT JOIN (  SELECT nota_liquidacao_paga_anulada.exercicio
                                           , nota_liquidacao_paga_anulada.cod_entidade
                                           , nota_liquidacao_paga_anulada.cod_nota
                                           , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                                        FROM empenho.nota_liquidacao_paga_anulada
                                       WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                                    GROUP BY nota_liquidacao_paga_anulada.exercicio
                                           , nota_liquidacao_paga_anulada.cod_entidade
                                           , nota_liquidacao_paga_anulada.cod_nota
                                   ) AS nota_liquidacao_paga_anulada
                                ON nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio
                               AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                               AND nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
                          GROUP BY nota_liquidacao_paga.exercicio
                                 , nota_liquidacao_paga.cod_entidade
                                 , nota_liquidacao_paga.cod_nota
              
                         ) AS liquidacao_paga
                      ON liquidacao_paga.exercicio = nota_liquidacao.exercicio
                     AND liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                     AND liquidacao_paga.cod_nota = nota_liquidacao.cod_nota
              
                 WHERE empenho.exercicio <= '''||stExercicioAnterior||'''
                   AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                   AND empenho.cod_entidade IN ('||stCodEntidade||')
    
              GROUP BY pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
               ) AS pago
            ON pago.exercicio = liquidado.exercicio
           AND pago.cod_pre_empenho = liquidado.cod_pre_empenho
           AND pago.cod_entidade = liquidado.cod_entidade
           AND pago.cod_empenho = liquidado.cod_empenho

-- inner para achar a entidade a que ele pertence
    INNER JOIN orcamento.entidade
            ON entidade.exercicio = liquidado.exercicio
           AND entidade.cod_entidade = liquidado.cod_entidade
    
    INNER JOIN sw_cgm
            ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
     LEFT JOIN empenho.pre_empenho_despesa
            ON pre_empenho_despesa.exercicio = liquidado.exercicio
           AND pre_empenho_despesa.cod_pre_empenho = liquidado.cod_pre_empenho
      
     LEFT JOIN orcamento.despesa
            ON despesa.exercicio = pre_empenho_despesa.exercicio
           AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
           
     LEFT JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN orcamento.orgao
            ON orgao.exercicio = '''||stExercicio||'''
           AND orgao.num_orgao = despesa.num_orgao

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = liquidado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = liquidado.cod_pre_empenho

     LEFT JOIN orcamento.orgao AS orgao_implantado
            ON orgao_implantado.exercicio = '''||stExercicio||'''
           AND orgao_implantado.num_orgao = restos_pre_empenho.num_orgao

      GROUP BY liquidado.cod_empenho
             , liquidado.cod_entidade
             , liquidado.exercicio
             , sw_cgm.nom_cgm
             , restos_pre_empenho.cod_estrutural
             , conta_despesa.cod_estrutural
             , orgao.num_orgao
             , orgao.nom_orgao
             , orgao_implantado.nom_orgao
             , orgao_implantado.num_orgao
             , despesa.dt_criacao
        HAVING ( SUM(COALESCE(liquidado.vl_liquidado,0.00)) - SUM(COALESCE(pago.vl_pago,0.00)) ) > 0
  ';

  EXECUTE stSql;

  -- cria a tabela temporaria para o valor cancelado processado
  stSql := '
    CREATE TEMPORARY TABLE tmp_processados_cancelado AS
      SELECT COALESCE(empenho_anulado_item.vl_anulado,0.00) AS vl_total
           , empenho.cod_empenho
           , empenho.cod_entidade
           , empenho.exercicio
           , sw_cgm.nom_cgm AS nom_entidade
           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN ''ÓRGÃO NÃO INFORMADO''
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.nom_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.nom_orgao
             END AS nom_orgao
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN 99
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.num_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.num_orgao
             END AS num_orgao
        FROM empenho.empenho 

  INNER JOIN empenho.pre_empenho
          ON pre_empenho.exercicio = empenho.exercicio
         AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
         
  INNER JOIN empenho.item_pre_empenho
          ON pre_empenho.exercicio = item_pre_empenho.exercicio
         AND pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
  
  INNER JOIN (  SELECT empenho_anulado_item.exercicio
                     , empenho_anulado_item.cod_pre_empenho
                     , SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) AS vl_anulado
                  FROM empenho.empenho_anulado_item
                 WHERE TO_DATE(empenho_anulado_item.timestamp::TEXT,''yyyy-mm-dd'') <= TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
              GROUP BY empenho_anulado_item.exercicio
                     , empenho_anulado_item.cod_pre_empenho
             ) AS empenho_anulado_item 
          ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
         AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
  
  INNER JOIN (  SELECT nota_liquidacao.cod_empenho
                     , nota_liquidacao.exercicio_empenho
                     , nota_liquidacao.cod_entidade
                  FROM empenho.nota_liquidacao
                 WHERE nota_liquidacao.dt_liquidacao <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
              GROUP BY nota_liquidacao.cod_empenho
                     , nota_liquidacao.exercicio_empenho
                     , nota_liquidacao.cod_entidade
            ) AS liquidacao
          ON liquidacao.cod_empenho = empenho.cod_empenho
         AND liquidacao.exercicio_empenho = empenho.exercicio
         AND liquidacao.cod_entidade = empenho.cod_entidade

-- inner para achar a entidade a que ele pertence
  INNER JOIN orcamento.entidade
          ON entidade.exercicio = empenho.exercicio
         AND entidade.cod_entidade = empenho.cod_entidade
  
  INNER JOIN sw_cgm
          ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
   LEFT JOIN empenho.pre_empenho_despesa
          ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
         AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
    
   LEFT JOIN orcamento.despesa
          ON despesa.exercicio = pre_empenho_despesa.exercicio
         AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
         
   LEFT JOIN orcamento.conta_despesa
          ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
         AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

   LEFT JOIN orcamento.orgao
          ON orgao.exercicio = '''||stExercicio||'''
         AND orgao.num_orgao = despesa.num_orgao

   LEFT JOIN empenho.restos_pre_empenho
          ON restos_pre_empenho.exercicio = pre_empenho.exercicio
         AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

   LEFT JOIN orcamento.orgao AS orgao_implantado
          ON orgao_implantado.exercicio = '''||stExercicio||'''
         AND orgao_implantado.num_orgao = restos_pre_empenho.num_orgao

       WHERE empenho.exercicio <= '''||stExercicioAnterior||'''
         AND empenho.dt_empenho <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
         AND empenho.cod_entidade IN ('||stCodEntidade||')

    GROUP BY empenho.cod_empenho
           , empenho.cod_entidade
           , empenho.exercicio
           , sw_cgm.nom_cgm
           , restos_pre_empenho.cod_estrutural
           , conta_despesa.cod_estrutural
           , orgao.num_orgao
           , orgao.nom_orgao
           , orgao_implantado.nom_orgao
           , orgao_implantado.num_orgao
           , despesa.dt_criacao
           , empenho_anulado_item.vl_anulado
      HAVING ( COALESCE(empenho_anulado_item.vl_anulado,0.00) ) > 0
  ';

  EXECUTE stSql;

  -- cria a table temporaria para o valor processado pago
  stSql := '
    CREATE TEMPORARY TABLE tmp_processados_pago AS
      SELECT ( SUM(liquidacao_paga.vl_total) ) AS vl_total
           , empenho.cod_entidade
           , empenho.cod_empenho
           , empenho.exercicio
           , sw_cgm.nom_cgm AS nom_entidade
           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN ''ÓRGÃO NÃO INFORMADO''
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.nom_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.nom_orgao
             END AS nom_orgao
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN 99
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.num_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.num_orgao
             END AS num_orgao
        FROM empenho.nota_liquidacao
  
  INNER JOIN empenho.empenho
          ON empenho.exercicio = nota_liquidacao.exercicio_empenho
         AND empenho.cod_entidade = nota_liquidacao.cod_entidade
         AND empenho.cod_empenho = nota_liquidacao.cod_empenho
  
  INNER JOIN empenho.pre_empenho
          ON pre_empenho.exercicio = empenho.exercicio
         AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
  
  INNER JOIN (  SELECT nota_liquidacao_paga.exercicio
                     , nota_liquidacao_paga.cod_entidade
                     , nota_liquidacao_paga.cod_nota
                     , ( SUM(COALESCE(nota_liquidacao_paga.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) ) AS vl_total
  
                  FROM (  SELECT nota_liquidacao_paga.exercicio
                               , nota_liquidacao_paga.cod_entidade
                               , nota_liquidacao_paga.cod_nota
                               , SUM(nota_liquidacao_paga.vl_pago) AS vl_total
                            FROM empenho.nota_liquidacao_paga
                           WHERE TO_DATE(nota_liquidacao_paga.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                        GROUP BY nota_liquidacao_paga.exercicio
                               , nota_liquidacao_paga.cod_entidade
                               , nota_liquidacao_paga.cod_nota
                       ) AS nota_liquidacao_paga
  
             LEFT JOIN (  SELECT nota_liquidacao_paga_anulada.exercicio
                               , nota_liquidacao_paga_anulada.cod_entidade
                               , nota_liquidacao_paga_anulada.cod_nota
                               , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                            FROM empenho.nota_liquidacao_paga_anulada
                           WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                        GROUP BY nota_liquidacao_paga_anulada.exercicio
                               , nota_liquidacao_paga_anulada.cod_entidade
                               , nota_liquidacao_paga_anulada.cod_nota
                       ) AS nota_liquidacao_paga_anulada
                    ON nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio
                   AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                   AND nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
              GROUP BY nota_liquidacao_paga.exercicio
                     , nota_liquidacao_paga.cod_entidade
                     , nota_liquidacao_paga.cod_nota
  
             ) AS liquidacao_paga
          ON liquidacao_paga.exercicio = nota_liquidacao.exercicio
         AND liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
         AND liquidacao_paga.cod_nota = nota_liquidacao.cod_nota

-- inner para achar a entidade a que ele pertence
  INNER JOIN orcamento.entidade
          ON entidade.exercicio = empenho.exercicio
         AND entidade.cod_entidade = empenho.cod_entidade
  
  INNER JOIN sw_cgm
          ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
   LEFT JOIN empenho.pre_empenho_despesa
          ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
         AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
    
   LEFT JOIN orcamento.despesa
          ON despesa.exercicio = pre_empenho_despesa.exercicio
         AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
         
   LEFT JOIN orcamento.conta_despesa
          ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
         AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

   LEFT JOIN orcamento.orgao
          ON orgao.exercicio = '''||stExercicio||'''
         AND orgao.num_orgao = despesa.num_orgao

   LEFT JOIN empenho.restos_pre_empenho
          ON restos_pre_empenho.exercicio = pre_empenho.exercicio
         AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

   LEFT JOIN orcamento.orgao AS orgao_implantado
          ON orgao_implantado.exercicio = '''||stExercicio||'''
         AND orgao_implantado.num_orgao = restos_pre_empenho.num_orgao

       WHERE empenho.exercicio <= '''||stExercicioAnterior||'''
         AND empenho.dt_empenho <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
         AND nota_liquidacao.dt_liquidacao <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
         AND empenho.cod_entidade IN ('||stCodEntidade||')

    GROUP BY empenho.cod_empenho
           , empenho.cod_entidade
           , empenho.exercicio
           , sw_cgm.nom_cgm
           , restos_pre_empenho.cod_estrutural
           , conta_despesa.cod_estrutural
           , orgao.num_orgao
           , orgao.nom_orgao
           , orgao_implantado.nom_orgao
           , orgao_implantado.num_orgao
           , despesa.dt_criacao
      HAVING ( SUM(liquidacao_paga.vl_total) ) > 0
  ';

  EXECUTE stSql;

  -- cria a tabela temporaria para o valor nao processado em exercicios anteriores
  StSql := '
    CREATE TEMPORARY TABLE tmp_nao_processados_exercicios_anteriores AS

      SELECT empenhado.cod_empenho
           , empenhado.cod_entidade
           , empenhado.exercicio
           , sw_cgm.nom_cgm AS nom_entidade
           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN ''ÓRGÃO NÃO INFORMADO''
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.nom_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.nom_orgao
             END AS nom_orgao
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN 99
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.num_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.num_orgao
             END AS num_orgao
           , (SUM(COALESCE(empenhado.vl_empenhado,0.00)) - SUM(COALESCE(liquidado.vl_liquidado,0.00))) AS vl_total
        FROM (  SELECT (  SUM(COALESCE(item_pre_empenho.vl_total,0.00))
                          -
                          COALESCE(empenho_anulado_item.vl_anulado,0.00) ) AS vl_empenhado
                     , pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
                  FROM empenho.empenho
            
            INNER JOIN empenho.pre_empenho
                    ON pre_empenho.exercicio = empenho.exercicio
                   AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
            
            INNER JOIN empenho.item_pre_empenho
                    ON item_pre_empenho.exercicio = pre_empenho.exercicio
                   AND item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
            
             LEFT JOIN (  SELECT empenho_anulado_item.exercicio
                               , empenho_anulado_item.cod_pre_empenho
                               , SUM(empenho_anulado_item.vl_anulado) AS vl_anulado
                            FROM empenho.empenho_anulado_item
                           WHERE TO_DATE(empenho_anulado_item.timestamp::TEXT,''yyyy-mm-dd'') <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'') 
                        GROUP BY empenho_anulado_item.exercicio
                               , empenho_anulado_item.cod_pre_empenho
                       ) AS empenho_anulado_item
                    ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                   AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
            
                 WHERE empenho.exercicio <= '''||stExercicioAnterior||'''
                   AND empenho.dt_empenho <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                   AND empenho.cod_entidade IN ('||stCodEntidade||')
              GROUP BY pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
                     , empenho_anulado_item.vl_anulado
             ) AS empenhado 

     LEFT JOIN (  SELECT ( SUM(COALESCE(liquidado.vl_total,0.00)) ) AS vl_liquidado
                       , pre_empenho.exercicio
                       , pre_empenho.cod_pre_empenho
                       , empenho.cod_entidade
                       , empenho.cod_empenho
                    FROM empenho.nota_liquidacao
              
              INNER JOIN empenho.empenho
                      ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho = nota_liquidacao.cod_empenho
              
              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
              
               LEFT JOIN (  SELECT nota_liquidacao_item.exercicio
                                 , nota_liquidacao_item.cod_entidade
                                 , nota_liquidacao_item.cod_nota
                                 , ( SUM(COALESCE(nota_liquidacao_item.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) ) AS vl_total
                              FROM empenho.nota_liquidacao_item
                         LEFT JOIN (  SELECT exercicio
                                           , cod_nota
                                           , num_item
                                           , exercicio_item
                                           , cod_pre_empenho
                                           , cod_entidade
                                           , SUM(COALESCE(vl_anulado,0.00)) AS vl_anulado
                                        FROM empenho.nota_liquidacao_item_anulado
                                       WHERE TO_DATE(timestamp::TEXT,''yyyy-mm-dd'') <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
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
                          GROUP BY nota_liquidacao_item.exercicio
                                 , nota_liquidacao_item.cod_entidade
                                 , nota_liquidacao_item.cod_nota
              
                       ) AS liquidado
                      ON liquidado.exercicio = nota_liquidacao.exercicio
                     AND liquidado.cod_entidade = nota_liquidacao.cod_entidade
                     AND liquidado.cod_nota = nota_liquidacao.cod_nota

                   WHERE empenho.exercicio <= '''||stExercicioAnterior||'''
                     AND empenho.dt_empenho <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')

                     AND nota_liquidacao.dt_liquidacao < TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                     AND empenho.cod_entidade IN ('||stCodEntidade||')
                GROUP BY pre_empenho.exercicio
                       , pre_empenho.cod_pre_empenho
                       , empenho.cod_entidade
                       , empenho.cod_empenho

               ) AS liquidado 
            ON liquidado.exercicio = empenhado.exercicio
           AND liquidado.cod_pre_empenho = empenhado.cod_pre_empenho
           AND liquidado.cod_entidade = empenhado.cod_entidade
           AND liquidado.cod_empenho = empenhado.cod_empenho

-- inner para achar a entidade a que ele pertence
    INNER JOIN orcamento.entidade
            ON entidade.exercicio = empenhado.exercicio
           AND entidade.cod_entidade = empenhado.cod_entidade
    
    INNER JOIN sw_cgm
            ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
     LEFT JOIN empenho.pre_empenho_despesa
            ON pre_empenho_despesa.exercicio = empenhado.exercicio
           AND pre_empenho_despesa.cod_pre_empenho = empenhado.cod_pre_empenho
      
     LEFT JOIN orcamento.despesa
            ON despesa.exercicio = pre_empenho_despesa.exercicio
           AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
           
     LEFT JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN orcamento.orgao
            ON orgao.exercicio = '''||stExercicio||'''
           AND orgao.num_orgao = despesa.num_orgao

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = empenhado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = empenhado.cod_pre_empenho

     LEFT JOIN orcamento.orgao AS orgao_implantado
            ON orgao_implantado.exercicio = '''||stExercicio||'''
           AND orgao_implantado.num_orgao = restos_pre_empenho.num_orgao

      GROUP BY empenhado.cod_empenho
             , empenhado.cod_entidade
             , empenhado.exercicio
             , sw_cgm.nom_cgm
             , restos_pre_empenho.cod_estrutural
             , conta_despesa.cod_estrutural
             , orgao.num_orgao
             , orgao.nom_orgao
             , orgao_implantado.nom_orgao
             , orgao_implantado.num_orgao
             , despesa.dt_criacao
        HAVING (SUM(COALESCE(empenhado.vl_empenhado,0.00)) - SUM(COALESCE(liquidado.vl_liquidado,0.00)) ) > 0
  ';
  
  EXECUTE stSql;
  
  -- cria a tabela temporaria para o valor nao processado no exercicio anterior
  StSql := '
    CREATE TEMPORARY TABLE tmp_nao_processados_exercicio_anterior AS

      SELECT empenhado.cod_empenho
           , empenhado.cod_entidade
           , empenhado.exercicio
           , sw_cgm.nom_cgm AS nom_entidade
           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN ''ÓRGÃO NÃO INFORMADO''
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.nom_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.nom_orgao
             END AS nom_orgao
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN 99
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.num_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.num_orgao
             END AS num_orgao
           , (SUM(COALESCE(empenhado.vl_empenhado,0.00)) - SUM(COALESCE(liquidado.vl_liquidado,0.00))) AS vl_total
        FROM (  SELECT (  SUM(COALESCE(item_pre_empenho.vl_total,0.00))
                          -
                          COALESCE(empenho_anulado_item.vl_anulado,0.00) ) AS vl_empenhado
                     , pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
            
                  FROM empenho.empenho
            
            INNER JOIN empenho.pre_empenho
                    ON pre_empenho.exercicio = empenho.exercicio
                   AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
            
            INNER JOIN empenho.item_pre_empenho
                    ON item_pre_empenho.exercicio = pre_empenho.exercicio
                   AND item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
            
             LEFT JOIN (  SELECT empenho_anulado_item.exercicio
                               , empenho_anulado_item.cod_pre_empenho
                               , SUM(empenho_anulado_item.vl_anulado) AS vl_anulado
                            FROM empenho.empenho_anulado_item
                           WHERE TO_DATE(empenho_anulado_item.timestamp::TEXT,''yyyy-mm-dd'') <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                        GROUP BY empenho_anulado_item.exercicio
                               , empenho_anulado_item.cod_pre_empenho
                       ) AS empenho_anulado_item
                    ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                   AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
            
                 WHERE empenho.exercicio <= '''|| stExercicioAnterior ||'''
                   AND empenho.dt_empenho <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                   AND empenho.cod_entidade IN ('|| stCodEntidade ||')
              GROUP BY pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
                     , empenho_anulado_item.vl_anulado
             ) AS empenhado 

     LEFT JOIN (  SELECT ( SUM(liquidado.vl_total) ) AS vl_liquidado
                       , pre_empenho.exercicio
                       , pre_empenho.cod_pre_empenho
                       , empenho.cod_entidade
                       , empenho.cod_empenho
              
                    FROM empenho.nota_liquidacao
              
              INNER JOIN empenho.empenho
                      ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho = nota_liquidacao.cod_empenho
              
              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
              
               LEFT JOIN (  SELECT nota_liquidacao_item.exercicio
                                 , nota_liquidacao_item.cod_entidade
                                 , nota_liquidacao_item.cod_nota
                                 , ( SUM(COALESCE(nota_liquidacao_item.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) ) AS vl_total
                              FROM empenho.nota_liquidacao_item
                         LEFT JOIN (  SELECT exercicio
                                           , cod_nota
                                           , num_item
                                           , exercicio_item
                                           , cod_pre_empenho
                                           , cod_entidade
                                           , SUM(COALESCE(vl_anulado,0.00)) AS vl_anulado
                                        FROM empenho.nota_liquidacao_item_anulado
                                       WHERE TO_DATE(timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
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
                          GROUP BY nota_liquidacao_item.exercicio
                                 , nota_liquidacao_item.cod_entidade
                                 , nota_liquidacao_item.cod_nota
              
                       ) AS liquidado
                      ON liquidado.exercicio = nota_liquidacao.exercicio
                     AND liquidado.cod_entidade = nota_liquidacao.cod_entidade
                     AND liquidado.cod_nota = nota_liquidacao.cod_nota
                   WHERE empenho.exercicio <= '''||stExercicioAnterior||'''
                     AND empenho.dt_empenho <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                     AND nota_liquidacao.dt_liquidacao <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                     AND empenho.cod_entidade IN ('||stCodEntidade||')
                GROUP BY pre_empenho.exercicio
                       , pre_empenho.cod_pre_empenho
                       , empenho.cod_entidade
                       , empenho.cod_empenho

               ) AS liquidado 
            ON liquidado.exercicio = empenhado.exercicio
           AND liquidado.cod_pre_empenho = empenhado.cod_pre_empenho
           AND liquidado.cod_entidade = empenhado.cod_entidade

-- inner para achar a entidade a que ele pertence
    INNER JOIN orcamento.entidade
            ON entidade.exercicio = empenhado.exercicio
           AND entidade.cod_entidade = empenhado.cod_entidade
    
    INNER JOIN sw_cgm
            ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
     LEFT JOIN empenho.pre_empenho_despesa
            ON pre_empenho_despesa.exercicio = empenhado.exercicio
           AND pre_empenho_despesa.cod_pre_empenho = empenhado.cod_pre_empenho
      
     LEFT JOIN orcamento.despesa
            ON despesa.exercicio = pre_empenho_despesa.exercicio
           AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
           
     LEFT JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN orcamento.orgao
            ON orgao.exercicio = '''||stExercicio||'''
           AND orgao.num_orgao = despesa.num_orgao

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = empenhado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = empenhado.cod_pre_empenho

     LEFT JOIN orcamento.orgao AS orgao_implantado
            ON orgao_implantado.exercicio = '''||stExercicio||'''
           AND orgao_implantado.num_orgao = restos_pre_empenho.num_orgao

      GROUP BY empenhado.cod_empenho
             , empenhado.cod_entidade
             , empenhado.exercicio
             , sw_cgm.nom_cgm
             , restos_pre_empenho.cod_estrutural
             , conta_despesa.cod_estrutural
             , orgao.num_orgao
             , orgao.nom_orgao
             , orgao_implantado.nom_orgao
             , orgao_implantado.num_orgao
             , despesa.dt_criacao
        HAVING (SUM(COALESCE(empenhado.vl_empenhado,0.00)) - SUM(COALESCE(liquidado.vl_liquidado,0.00)) ) > 0
  ';
  
  EXECUTE stSql;

  --cria a tabela temporaria para o valor nao processado cancelado
  stSql := '
    CREATE TEMPORARY TABLE tmp_nao_processados_cancelado AS
      SELECT ( SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) ) AS vl_total
           , empenho.cod_empenho
           , empenho.cod_entidade
           , empenho.exercicio
           , sw_cgm.nom_cgm AS nom_entidade
           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN ''ÓRGÃO NÃO INFORMADO''
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.nom_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.nom_orgao
             END AS nom_orgao
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN 99
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.num_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.num_orgao
             END AS num_orgao
        FROM empenho.empenho 
  
  INNER JOIN empenho.pre_empenho
          ON pre_empenho.exercicio = empenho.exercicio
         AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
         
   LEFT JOIN (  SELECT nota_liquidacao.cod_empenho
                     , nota_liquidacao.exercicio_empenho
                     , nota_liquidacao.cod_entidade
                  FROM empenho.nota_liquidacao
                 WHERE nota_liquidacao.dt_liquidacao <= TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
              GROUP BY nota_liquidacao.cod_empenho
                     , nota_liquidacao.exercicio_empenho
                     , nota_liquidacao.cod_entidade
            ) AS liquidacao
          ON liquidacao.cod_empenho = empenho.cod_empenho
         AND liquidacao.exercicio_empenho = empenho.exercicio
         AND liquidacao.cod_entidade = empenho.cod_entidade            
  
  INNER JOIN (  SELECT empenho_anulado_item.exercicio
                     , empenho_anulado_item.cod_pre_empenho
                     , SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) AS vl_anulado
                  FROM empenho.empenho_anulado_item
                 WHERE TO_DATE(empenho_anulado_item.timestamp::TEXT,''yyyy-mm-dd'') <= TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
              GROUP BY empenho_anulado_item.exercicio
                     , empenho_anulado_item.cod_pre_empenho
             ) AS empenho_anulado_item 
          ON empenho_anulado_item.exercicio = pre_empenho.exercicio
         AND empenho_anulado_item.cod_pre_empenho = pre_empenho.cod_pre_empenho
         AND liquidacao.cod_empenho IS NULL
  
-- inner para achar a entidade a que ele pertence
  INNER JOIN orcamento.entidade
          ON entidade.exercicio = empenho.exercicio
         AND entidade.cod_entidade = empenho.cod_entidade
  
  INNER JOIN sw_cgm
          ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
   LEFT JOIN empenho.pre_empenho_despesa
          ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
         AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
    
   LEFT JOIN orcamento.despesa
          ON despesa.exercicio = pre_empenho_despesa.exercicio
         AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
         
   LEFT JOIN orcamento.conta_despesa
          ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
         AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

   LEFT JOIN orcamento.orgao 
          ON orgao.exercicio = '''||stExercicio||'''
         AND orgao.num_orgao = despesa.num_orgao

   LEFT JOIN empenho.restos_pre_empenho
          ON restos_pre_empenho.exercicio = pre_empenho.exercicio
         AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

   LEFT JOIN orcamento.orgao AS orgao_implantado
          ON orgao_implantado.exercicio = '''||stExercicio||'''
         AND orgao_implantado.num_orgao = restos_pre_empenho.num_orgao

       WHERE empenho.exercicio <= '''||stExercicioAnterior||'''
         AND empenho.dt_empenho <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
         AND empenho.cod_entidade IN ('||stCodEntidade||')

    GROUP BY empenho.cod_empenho
           , empenho.cod_entidade
           , empenho.exercicio
           , sw_cgm.nom_cgm
           , restos_pre_empenho.cod_estrutural
           , conta_despesa.cod_estrutural
           , orgao.num_orgao
           , orgao.nom_orgao
           , orgao_implantado.nom_orgao
           , orgao_implantado.num_orgao
           , despesa.dt_criacao
      HAVING ( SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) ) > 0
  ';

  EXECUTE stSql;

  --cria a tabela temporaria para o valor nao processado cancelado
  stSql := '
    CREATE TEMPORARY TABLE tmp_nao_processados_pago AS
      SELECT ( SUM(liquidacao_paga.vl_total) ) AS vl_total
           , empenho.cod_empenho
           , empenho.cod_entidade
           , empenho.exercicio
           , sw_cgm.nom_cgm AS nom_entidade
           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN ''ÓRGÃO NÃO INFORMADO''
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.nom_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.nom_orgao
             END AS nom_orgao
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN 99
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.num_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.num_orgao
             END AS num_orgao
        FROM empenho.nota_liquidacao
        
  INNER JOIN empenho.empenho
          ON empenho.exercicio = nota_liquidacao.exercicio_empenho
         AND empenho.cod_entidade = nota_liquidacao.cod_entidade
         AND empenho.cod_empenho = nota_liquidacao.cod_empenho
  
  INNER JOIN empenho.pre_empenho
          ON pre_empenho.exercicio = empenho.exercicio
         AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
  
  INNER JOIN (  SELECT nota_liquidacao_paga.exercicio
                     , nota_liquidacao_paga.cod_entidade
                     , nota_liquidacao_paga.cod_nota
                     , ( SUM(COALESCE(nota_liquidacao_paga.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) ) AS vl_total
  
                  FROM (  SELECT nota_liquidacao_paga.exercicio
                               , nota_liquidacao_paga.cod_entidade
                               , nota_liquidacao_paga.cod_nota
                               , SUM(nota_liquidacao_paga.vl_pago) AS vl_total
                            FROM empenho.nota_liquidacao_paga
                           WHERE TO_DATE(nota_liquidacao_paga.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                        GROUP BY nota_liquidacao_paga.exercicio
                               , nota_liquidacao_paga.cod_entidade
                               , nota_liquidacao_paga.cod_nota
                       ) AS nota_liquidacao_paga
  
             LEFT JOIN (  SELECT nota_liquidacao_paga_anulada.exercicio
                               , nota_liquidacao_paga_anulada.cod_entidade
                               , nota_liquidacao_paga_anulada.cod_nota
                               , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                            FROM empenho.nota_liquidacao_paga_anulada
                           WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                        GROUP BY nota_liquidacao_paga_anulada.exercicio
                               , nota_liquidacao_paga_anulada.cod_entidade
                               , nota_liquidacao_paga_anulada.cod_nota
                       ) AS nota_liquidacao_paga_anulada
                    ON nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio
                   AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                   AND nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
              GROUP BY nota_liquidacao_paga.exercicio
                     , nota_liquidacao_paga.cod_entidade
                     , nota_liquidacao_paga.cod_nota
  
             ) AS liquidacao_paga
          ON liquidacao_paga.exercicio = nota_liquidacao.exercicio
         AND liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
         AND liquidacao_paga.cod_nota = nota_liquidacao.cod_nota

-- inner para achar a entidade a que ele pertence
  INNER JOIN orcamento.entidade
          ON entidade.exercicio = empenho.exercicio
         AND entidade.cod_entidade = empenho.cod_entidade
  
  INNER JOIN sw_cgm
          ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
   LEFT JOIN empenho.pre_empenho_despesa
          ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
         AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
    
   LEFT JOIN orcamento.despesa
          ON despesa.exercicio = pre_empenho_despesa.exercicio
         AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
         
   LEFT JOIN orcamento.conta_despesa
          ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
         AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

   LEFT JOIN orcamento.orgao 
          ON orgao.exercicio = '''||stExercicio||'''
         AND orgao.num_orgao = despesa.num_orgao

   LEFT JOIN empenho.restos_pre_empenho
          ON restos_pre_empenho.exercicio = pre_empenho.exercicio
         AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

   LEFT JOIN orcamento.orgao AS orgao_implantado
          ON orgao_implantado.exercicio = '''||stExercicio||'''
         AND orgao_implantado.num_orgao = restos_pre_empenho.num_orgao

       WHERE empenho.exercicio <= '''||stExercicioAnterior||'''
         AND empenho.dt_empenho <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
         AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
         AND empenho.cod_entidade IN ('||stCodEntidade||')

    GROUP BY empenho.cod_empenho
           , empenho.cod_entidade
           , empenho.exercicio
           , sw_cgm.nom_cgm
           , restos_pre_empenho.cod_estrutural
           , conta_despesa.cod_estrutural
           , orgao.num_orgao
           , orgao.nom_orgao
           , orgao_implantado.nom_orgao
           , orgao_implantado.num_orgao
           , despesa.dt_criacao
      HAVING ( SUM(liquidacao_paga.vl_total) ) > 0 
  ';
 
  EXECUTE stSql; 


--consulta para retornar todas os orgaos para nao intra-orcamentarias
--  stSql := '
--    CREATE TEMPORARY TABLE tmp_orgao AS 
--      SELECT nom_orgao
--           , num_orgao
--           , nom_entidade
--           , cod_estrutural
--        FROM tmp_processados_exercicios_anteriores
--       UNION ALL
--      SELECT nom_orgao
--           , num_orgao
--           , nom_entidade
--           , cod_estrutural
--        FROM tmp_processados_exercicio_anterior
--       UNION ALL
--      SELECT nom_orgao
--           , num_orgao
--           , nom_entidade
--           , cod_estrutural
--        FROM tmp_processados_cancelado
--       UNION ALL
--      SELECT nom_orgao
--           , num_orgao
--           , nom_entidade
--           , cod_estrutural
--        FROM tmp_processados_pago
--       UNION ALL
--      SELECT nom_orgao
--           , num_orgao
--           , nom_entidade
--           , cod_estrutural
--        FROM tmp_nao_processados_exercicios_anteriores
--       UNION ALL
--      SELECT nom_orgao
--           , num_orgao
--           , nom_entidade
--           , cod_estrutural
--        FROM tmp_nao_processados_exercicio_anterior
--       UNION ALL
--      SELECT nom_orgao
--           , num_orgao
--           , nom_entidade
--           , cod_estrutural
--        FROM tmp_nao_processados_cancelado
--       UNION ALL
--      SELECT nom_orgao
--           , num_orgao
--           , nom_entidade
--           , cod_estrutural
--        FROM tmp_nao_processados_pago
--    ORDER BY num_orgao
--  ';
--
--  EXECUTE stSql;

  stSql := '
            SELECT cod_empenho, cod_entidade, exercicio, SUM(valor_processado_exercicios_anteriores) AS valor_processado_exercicios_anteriores,
                   SUM(valor_processado_exercicio_anterior) AS valor_processado_exercicio_anterior, SUM(valor_processado_cancelado) AS valor_processado_cancelado,
                   SUM(valor_processado_pago) AS valor_processado_pago, SUM(valor_nao_processado_exercicios_anteriores) AS valor_nao_processado_exercicios_anteriores,
                   SUM(valor_nao_processado_exercicio_anterior) AS valor_nao_processado_exercicio_anterior, SUM(valor_nao_processado_cancelado) AS valor_nao_processado_cancelado,
                   SUM(valor_nao_processado_pago) AS valor_nao_processado_pago
            FROM
                (
                SELECT
                        empenho.cod_empenho
                      , empenho.cod_entidade
                      , empenho.exercicio
                      , SUM(tmp_processados_exercicios_anteriores.vl_total) AS valor_processado_exercicios_anteriores
                      , 0.00 AS valor_processado_exercicio_anterior
                      , 0.00 AS valor_processado_cancelado
                      , 0.00 AS valor_processado_pago
                      , 0.00 AS valor_nao_processado_exercicios_anteriores
                      , 0.00 AS valor_nao_processado_exercicio_anterior
                      , 0.00 AS valor_nao_processado_cancelado
                      , 0.00 AS valor_nao_processado_pago
                      
                FROM empenho.empenho
                      
                JOIN tmp_processados_exercicios_anteriores
                  ON tmp_processados_exercicios_anteriores.cod_empenho = empenho.cod_empenho
                 AND tmp_processados_exercicios_anteriores.cod_entidade = empenho.cod_entidade
                 AND tmp_processados_exercicios_anteriores.exercicio = empenho.exercicio
                 
                WHERE empenho.exercicio <= ''' || stExercicio || ''' AND empenho.cod_entidade IN (' || stCodEntidade || ')
                 
                GROUP BY empenho.cod_empenho, empenho.cod_entidade, empenho.exercicio
                 
                UNION
                
                SELECT
                        empenho.cod_empenho
                      , empenho.cod_entidade
                      , empenho.exercicio
                      , 0.00 AS valor_processado_exercicios_anteriores
                      , SUM(tmp_processados_exercicio_anterior.vl_total) AS valor_processado_exercicio_anterior
                      , 0.00 AS valor_processado_cancelado
                      , 0.00 AS valor_processado_pago
                      , 0.00 AS valor_nao_processado_exercicios_anteriores
                      , 0.00 AS valor_nao_processado_exercicio_anterior
                      , 0.00 AS valor_nao_processado_cancelado
                      , 0.00 AS valor_nao_processado_pago
                      
                FROM empenho.empenho
                
                JOIN tmp_processados_exercicio_anterior
                  ON tmp_processados_exercicio_anterior.cod_empenho = empenho.cod_empenho
                 AND tmp_processados_exercicio_anterior.cod_entidade = empenho.cod_entidade
                 AND tmp_processados_exercicio_anterior.exercicio = empenho.exercicio
                 
                WHERE empenho.exercicio <= ''' || stExercicio || ''' AND empenho.cod_entidade IN (' || stCodEntidade || ')
                 
                GROUP BY empenho.cod_empenho, empenho.cod_entidade, empenho.exercicio
                 
                UNION
                
                SELECT
                        empenho.cod_empenho
                      , empenho.cod_entidade
                      , empenho.exercicio
                      , 0.00 AS valor_processado_exercicios_anteriores
                      , 0.00 AS valor_processado_exercicio_anterior
                      , SUM(tmp_processados_cancelado.vl_total) AS valor_processado_cancelado
                      , 0.00 AS valor_processado_pago
                      , 0.00 AS valor_nao_processado_exercicios_anteriores
                      , 0.00 AS valor_nao_processado_exercicio_anterior
                      , 0.00 AS valor_nao_processado_cancelado
                      , 0.00 AS valor_nao_processado_pago
                      
                FROM empenho.empenho
                 
                JOIN tmp_processados_cancelado
                  ON tmp_processados_cancelado.cod_empenho = empenho.cod_empenho
                 AND tmp_processados_cancelado.cod_entidade = empenho.cod_entidade
                 AND tmp_processados_cancelado.exercicio = empenho.exercicio
                 
                WHERE empenho.exercicio <= ''' || stExercicio || ''' AND empenho.cod_entidade IN (' || stCodEntidade || ')
                 
                GROUP BY empenho.cod_empenho, empenho.cod_entidade, empenho.exercicio
                 
                UNION
                
                SELECT
                        empenho.cod_empenho
                      , empenho.cod_entidade
                      , empenho.exercicio
                      , 0.00 AS valor_processado_exercicios_anteriores
                      , 0.00 AS valor_processado_exercicio_anterior
                      , 0.00 AS valor_processado_cancelado
                      , SUM(tmp_processados_pago.vl_total) AS valor_processado_pago
                      , 0.00 AS valor_nao_processado_exercicios_anteriores
                      , 0.00 AS valor_nao_processado_exercicio_anterior
                      , 0.00 AS valor_nao_processado_cancelado
                      , 0.00 AS valor_nao_processado_pago
                      
                FROM empenho.empenho
                 
                JOIN tmp_processados_pago
                  ON tmp_processados_pago.cod_empenho = empenho.cod_empenho
                 AND tmp_processados_pago.cod_entidade = empenho.cod_entidade
                 AND tmp_processados_pago.exercicio = empenho.exercicio
                 
                WHERE empenho.exercicio <= ''' || stExercicio || ''' AND empenho.cod_entidade IN (' || stCodEntidade || ')
                 
                GROUP BY empenho.cod_empenho, empenho.cod_entidade, empenho.exercicio
                 
                 UNION
                
                SELECT
                        empenho.cod_empenho
                      , empenho.cod_entidade
                      , empenho.exercicio
                      , 0.00 AS valor_processado_exercicios_anteriores
                      , 0.00 AS valor_processado_exercicio_anterior
                      , 0.00 AS valor_processado_cancelado
                      , 0.00 AS valor_processado_pago
                      , SUM(tmp_nao_processados_exercicios_anteriores.vl_total) AS valor_nao_processado_exercicios_anteriores
                      , 0.00 AS valor_nao_processado_exercicio_anterior
                      , 0.00 AS valor_nao_processado_cancelado
                      , 0.00 AS valor_nao_processado_pago
                      
                FROM empenho.empenho
                 
                JOIN tmp_nao_processados_exercicios_anteriores
                  ON tmp_nao_processados_exercicios_anteriores.cod_empenho = empenho.cod_empenho
                 AND tmp_nao_processados_exercicios_anteriores.cod_entidade = empenho.cod_entidade
                 AND tmp_nao_processados_exercicios_anteriores.exercicio = empenho.exercicio
                 
                WHERE empenho.exercicio <= ''' || stExercicio || ''' AND empenho.cod_entidade IN (' || stCodEntidade || ')
                 
                GROUP BY empenho.cod_empenho, empenho.cod_entidade, empenho.exercicio
                 
                UNION
                
                SELECT
                        empenho.cod_empenho
                      , empenho.cod_entidade
                      , empenho.exercicio
                      , 0.00 AS valor_processado_exercicios_anteriores
                      , 0.00 AS valor_processado_exercicio_anterior
                      , 0.00 AS valor_processado_cancelado
                      , 0.00 AS valor_processado_pago
                      , 0.00 AS valor_nao_processado_exercicios_anteriores
                      , SUM(tmp_nao_processados_exercicio_anterior.vl_total) AS valor_nao_processado_exercicio_anterior
                      , 0.00 AS valor_nao_processado_cancelado
                      , 0.00 AS valor_nao_processado_pago
                      
                FROM empenho.empenho
                 
                JOIN tmp_nao_processados_exercicio_anterior
                  ON tmp_nao_processados_exercicio_anterior.cod_empenho = empenho.cod_empenho
                 AND tmp_nao_processados_exercicio_anterior.cod_entidade = empenho.cod_entidade
                 AND tmp_nao_processados_exercicio_anterior.exercicio = empenho.exercicio
                 
                WHERE empenho.exercicio <= ''' || stExercicio || ''' AND empenho.cod_entidade IN (' || stCodEntidade || ')
                 
                GROUP BY empenho.cod_empenho, empenho.cod_entidade, empenho.exercicio
                 
                UNION
                
                SELECT
                        empenho.cod_empenho
                      , empenho.cod_entidade
                      , empenho.exercicio
                      , 0.00 AS valor_processado_exercicios_anteriores
                      , 0.00 AS valor_processado_exercicio_anterior
                      , 0.00 AS valor_processado_cancelado
                      , 0.00 AS valor_processado_pago
                      , 0.00 AS valor_nao_processado_exercicios_anteriores
                      , 0.00 AS valor_nao_processado_exercicio_anterior
                      , SUM(tmp_nao_processados_cancelado.vl_total) AS valor_nao_processado_cancelado
                      , 0.00 AS valor_nao_processado_pago
                      
                FROM empenho.empenho
                 
                JOIN tmp_nao_processados_cancelado
                  ON tmp_nao_processados_cancelado.cod_empenho = empenho.cod_empenho
                 AND tmp_nao_processados_cancelado.cod_entidade = empenho.cod_entidade
                 AND tmp_nao_processados_cancelado.exercicio = empenho.exercicio
                 
                WHERE empenho.exercicio <= ''' || stExercicio || ''' AND empenho.cod_entidade IN (' || stCodEntidade || ')
                 
                GROUP BY empenho.cod_empenho, empenho.cod_entidade, empenho.exercicio
                 
                UNION
                
                SELECT
                        empenho.cod_empenho
                      , empenho.cod_entidade
                      , empenho.exercicio
                      , 0.00 AS valor_processado_exercicios_anteriores
                      , 0.00 AS valor_processado_exercicio_anterior
                      , 0.00 AS valor_processado_cancelado
                      , 0.00 AS valor_processado_pago
                      , 0.00 AS valor_nao_processado_exercicios_anteriores
                      , 0.00 AS valor_nao_processado_exercicio_anterior
                      , 0.00 AS valor_nao_processado_cancelado
                      , SUM(tmp_nao_processados_pago.vl_total) AS valor_nao_processado_pago
                      
                FROM empenho.empenho
                 
                JOIN tmp_nao_processados_pago
                  ON tmp_nao_processados_pago.cod_empenho = empenho.cod_empenho
                 AND tmp_nao_processados_pago.cod_entidade = empenho.cod_entidade
                 AND tmp_nao_processados_pago.exercicio = empenho.exercicio
                 
                WHERE empenho.exercicio <= ''' || stExercicio || ''' AND empenho.cod_entidade IN (' || stCodEntidade || ')
                 
                GROUP BY empenho.cod_empenho, empenho.cod_entidade, empenho.exercicio
                
            ) AS record
                 
        GROUP BY cod_empenho, cod_entidade, exercicio
        
        ORDER BY cod_empenho
  ';

  FOR reRegistro IN EXECUTE stSql
  LOOP
      RETURN next reRegistro;
  END LOOP;

  DROP TABLE tmp_processados_exercicios_anteriores;
  DROP TABLE tmp_processados_exercicio_anterior;
  DROP TABLE tmp_processados_cancelado;
  DROP TABLE tmp_processados_pago;
  DROP TABLE tmp_nao_processados_exercicios_anteriores;
  DROP TABLE tmp_nao_processados_exercicio_anterior;
  DROP TABLE tmp_nao_processados_cancelado;
  DROP TABLE tmp_nao_processados_pago;

END;

$$ language 'plpgsql';
