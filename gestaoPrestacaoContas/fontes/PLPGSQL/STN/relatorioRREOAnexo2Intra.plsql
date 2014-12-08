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
    * PL do RREOAnexo2 - Arquivo STN da GPC 
    * Data de Criação   : 01/06/2008


    * @author Analista      Alexandre Melo
    * @author Desenvolvedor Alexandre Melo
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

/*
    ESTA PL GERA O SOMATORIO REFERENTE A LINHA DE INTRA-ORCAMENTARIAS.
    ELA E BASICAMENTE UMA COPIA DA PL relatorioRREOAnexo2.plsql.
    Prog.: Alexandre Melo
*/

CREATE OR REPLACE FUNCTION stn.fn_anexo2_intra(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    	ALIAS FOR $1;
    dtInicial     	ALIAS FOR $2;
    dtFinal     	ALIAS FOR $3;
    stCodEntidades 	ALIAS FOR $4;

    dtIniExercicio  VARCHAR := '';

    reRegistro      record ;
    stSql           varchar := '';
    stNomFuncao     varchar := '';

BEGIN
    dtIniExercicio := '01/01/' || stExercicio;
 
    stSql := '
    CREATE TEMPORARY TABLE tmp_intra_orcamentarias AS
    SELECT
           d.cod_funcao
         , d.cod_subfuncao
         , f.descricao             as nom_funcao
         , sf.descricao            as nom_subfuncao
         , sum(d.vl_original)      as vl_original
         , (sum(coalesce(d.vl_original,0.00)) + (sum(coalesce(suplementado.vl_suplementado,0.00)) - sum(coalesce(reduzido.vl_reduzido,0.00)))) as vl_suplementacoes
         , sum(coalesce(empenhado_bimestre.vl_total,0.00)) - sum(coalesce(empenhado_anulado_bimestre.vl_total,0.00)) as vl_empenhado_bimestre
         , sum(coalesce(empenhado_ate_bimestre.vl_total,0.00))                 as vl_empenhado_ate_bimestre
         , sum(coalesce(liquidado_bimestre.vl_total,0.00)) - sum(coalesce(liquidado_anulado_bimestre.vl_total,0.00)) as vl_liquidado_bimestre
         , sum(coalesce(liquidado_ate_bimestre.vl_total,0.00))  as vl_liquidado_ate_bimestre
      FROM
           orcamento.despesa          as d
           -- EMPENHADO BIMESTRE
           LEFT JOIN( SELECT
                             sum(coalesce(ipe.vl_total, 0.00)) as vl_total
                           , ped.cod_despesa
                        FROM
                             empenho.pre_empenho_despesa  as ped
                           , empenho.pre_empenho          as pe
                           , empenho.item_pre_empenho     as ipe
                           , empenho.empenho  as e
                       WHERE
                             ped.exercicio = pe.exercicio
                         AND ped.cod_pre_empenho = pe.cod_pre_empenho
                         AND pe.cod_pre_empenho = ipe.cod_pre_empenho

                         AND pe.exercicio = ipe.exercicio
                         AND e.exercicio = pe.exercicio
                         AND e.cod_pre_empenho = pe.cod_pre_empenho
                         AND e.exercicio = ''' || stExercicio || '''
                         AND e.cod_entidade IN (' || stCodEntidades || ')
                         AND e.dt_empenho::date BETWEEN to_date('''||dtInicial||''', ''dd/mm/yyyy'')              -- DATA INCIAL DO BIMESTRE
                                              AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')              -- DATA FINAL DO BIMESTRE
                    GROUP BY ped.cod_despesa                                                   ) as empenhado_bimestre
                  ON( empenhado_bimestre.cod_despesa = d.cod_despesa )

           -- EMPENHADO ANULADO BIMESTRE
           LEFT JOIN( SELECT
                             sum(coalesce(item_empenho_anulado.vl_anulado,0.00))  as vl_total
                           , ped.cod_despesa
                        FROM
                             empenho.pre_empenho_despesa  as ped
                           , empenho.pre_empenho          as pe
                           , empenho.item_pre_empenho     as ipe
                             INNER JOIN( SELECT eai.vl_anulado
                                             , eai.exercicio
                                             , eai.cod_pre_empenho
                                             , eai.num_item
                                          FROM empenho.empenho_anulado_item as eai
                                         WHERE eai.timestamp::date BETWEEN to_date( '''||dtInicial||''', ''dd/mm/yyyy'')
                                                                     AND to_date( '''||dtFinal||''', ''dd/mm/yyyy'')  ) as item_empenho_anulado
                                    ON(     item_empenho_anulado.exercicio       = ipe.exercicio
                                        AND item_empenho_anulado.cod_pre_empenho = ipe.cod_pre_empenho
                                        AND item_empenho_anulado.num_item        = ipe.num_item )
                            , empenho.empenho  as e
                       WHERE
                             ped.exercicio = pe.exercicio
                         AND ped.cod_pre_empenho = pe.cod_pre_empenho
                         AND pe.cod_pre_empenho = ipe.cod_pre_empenho

                         AND pe.exercicio = ipe.exercicio
                         AND e.exercicio = pe.exercicio
                         AND e.cod_pre_empenho = pe.cod_pre_empenho
                         AND e.exercicio = ''' || stExercicio || '''
                         AND e.cod_entidade IN (' || stCodEntidades || ')
                         AND e.dt_empenho::date BETWEEN to_date('''||dtIniExercicio||''', ''dd/mm/yyyy'')              -- DATA INCIAL DO EXERCICIO
                                              AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')              -- DATA FINAL DO BIMESTRE
                    GROUP BY ped.cod_despesa                                                   ) as empenhado_anulado_bimestre
                  ON( empenhado_anulado_bimestre.cod_despesa = d.cod_despesa )

           -- TOTAL EMPENHADO ATÉ O BIMESTRE
           LEFT JOIN( SELECT  sum(coalesce(ipe.vl_total, 0.00)) - sum(coalesce(item_empenho_anulado.vl_anulado,0.00))  as vl_total
                           , ped.cod_despesa
                        FROM
                             empenho.pre_empenho_despesa  as ped
                           , empenho.pre_empenho          as pe
                           , empenho.item_pre_empenho     as ipe
                             LEFT JOIN( SELECT eai.vl_anulado
                                             , eai.exercicio
                                             , eai.cod_pre_empenho
                                             , eai.num_item
                                          FROM empenho.empenho_anulado_item as eai
                                         WHERE eai.timestamp::date BETWEEN to_date( '''||dtIniExercicio||''', ''dd/mm/yyyy'')
                                                                     AND to_date( '''||dtFinal||''', ''dd/mm/yyyy'')  ) as item_empenho_anulado
                                    ON(     item_empenho_anulado.exercicio       = ipe.exercicio
                                        AND item_empenho_anulado.cod_pre_empenho = ipe.cod_pre_empenho
                                        AND item_empenho_anulado.num_item        = ipe.num_item )
                           , empenho.empenho              as e
                       WHERE
                             ped.exercicio = pe.exercicio
                         AND ped.cod_pre_empenho = pe.cod_pre_empenho
                         AND pe.cod_pre_empenho = ipe.cod_pre_empenho
                         AND pe.exercicio = ipe.exercicio
                         AND e.exercicio = pe.exercicio
                         AND e.cod_pre_empenho = pe.cod_pre_empenho
                         AND e.exercicio = ''' || stExercicio || '''
                         AND e.cod_entidade IN (' || stCodEntidades || ')
                         AND e.dt_empenho::date BETWEEN to_date('''||dtIniExercicio||''', ''dd/mm/yyyy'')              -- DATA INCIAL DO EXERCICIO
                                              AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')              -- DATA FINAL DO BIMESTRE
                    GROUP BY ped.cod_despesa                                                     ) as empenhado_ate_bimestre
                  ON( empenhado_ate_bimestre.cod_despesa = d.cod_despesa )

        -- LIQUIDADO NO BIMESTRE
        LEFT JOIN(  SELECT
                           ped.cod_despesa
                         , sum(coalesce(nli.vl_total,0.00)) as vl_total
                      FROM
                           empenho.nota_liquidacao as nl
                         , empenho.nota_liquidacao_item as nli
                         , empenho.empenho as e
                         , empenho.pre_empenho as pe
                         , empenho.pre_empenho_despesa as ped
                     WHERE
                           nli.exercicio        = nl.exercicio
                       AND nli.cod_entidade     = nl.cod_entidade
                       AND nli.cod_nota         = nl.cod_nota
                       AND nl.exercicio_empenho = e.exercicio
                       AND nl.cod_entidade      = e.cod_entidade
                       AND nl.cod_empenho       = e.cod_empenho
                       AND e.exercicio          = pe.exercicio
                       AND e.cod_pre_empenho    = pe.cod_pre_empenho
                       AND pe.exercicio         = ped.exercicio
                       AND pe.cod_pre_empenho   = ped.cod_pre_empenho
                       AND e.exercicio = ''' || stExercicio || '''
                       AND e.cod_entidade IN (' || stCodEntidades || ')
                       AND nl.dt_liquidacao::date BETWEEN to_date( '''||dtInicial||''', ''dd/mm/yyyy'')
                                                AND to_date( '''||dtFinal||''', ''dd/mm/yyyy'')
                  GROUP BY ped.cod_despesa                                                                     ) as liquidado_bimestre
                 ON( liquidado_bimestre.cod_despesa = d.cod_despesa )

        -- LIQUIDADO ANULADO NO BIMESTRE
        LEFT JOIN(  SELECT
                           ped.cod_despesa
                         , sum(coalesce(item_anulado.vl_anulado,0.00)) as vl_total
                      FROM
                           empenho.nota_liquidacao as nl
                         , empenho.nota_liquidacao_item as nli
                           INNER JOIN( SELECT nlia.exercicio
                                           , nlia.cod_nota
                                           , nlia.num_item
                                           , nlia.exercicio_item
                                           , nlia.cod_pre_empenho
                                           , nlia.cod_entidade
                                           , nlia.vl_anulado
                                        FROM empenho.nota_liquidacao_item_anulado as nlia
                                       WHERE nlia.timestamp::date BETWEEN to_date( '''||dtInicial||''',  ''dd/mm/yyyy'')
                                                                 AND to_date( '''||dtFinal||''', ''dd/mm/yyyy'')     
                                                                                                                                    ) as item_anulado
                                  ON(     item_anulado.exercicio       = nli.exercicio
                                      AND item_anulado.cod_nota        = nli.cod_nota
                                      AND item_anulado.num_item        = nli.num_item
                                      AND item_anulado.exercicio_item  = nli.exercicio_item
                                      AND item_anulado.cod_pre_empenho = nli.cod_pre_empenho
                                      AND item_anulado.cod_entidade    = nli.cod_entidade     )
                         , empenho.empenho as e
                         , empenho.pre_empenho as pe
                         , empenho.pre_empenho_despesa as ped
                     WHERE
                           nli.exercicio        = nl.exercicio
                       AND nli.cod_entidade     = nl.cod_entidade
                       AND nli.cod_nota         = nl.cod_nota
                       AND nl.exercicio_empenho = e.exercicio
                       AND nl.cod_entidade      = e.cod_entidade
                       AND nl.cod_empenho       = e.cod_empenho
                       AND e.exercicio          = pe.exercicio
                       AND e.cod_pre_empenho    = pe.cod_pre_empenho
                       AND pe.exercicio         = ped.exercicio
                       AND pe.cod_pre_empenho   = ped.cod_pre_empenho
                       AND e.exercicio = ''' || stExercicio || '''
                       AND e.cod_entidade IN (' || stCodEntidades || ')
                       AND nl.dt_liquidacao::date BETWEEN to_date( '''||dtIniExercicio||''', ''dd/mm/yyyy'')
                                                AND to_date( '''||dtFinal||''', ''dd/mm/yyyy'')
                  GROUP BY ped.cod_despesa                                                                     ) as liquidado_anulado_bimestre
                 ON( liquidado_anulado_bimestre.cod_despesa = d.cod_despesa )


        -- LIQUIDADO ATE BIMESTRE
        LEFT JOIN(  SELECT
                           ped.cod_despesa
                         , sum(coalesce(nli.vl_total,0.00)) - sum(coalesce(item_anulado.vl_anulado,0.00)) as vl_total
                      FROM
                           empenho.nota_liquidacao as nl
                         , empenho.nota_liquidacao_item as nli
                           LEFT JOIN( SELECT nlia.exercicio
                                           , nlia.cod_nota
                                           , nlia.num_item
                                           , nlia.exercicio_item
                                           , nlia.cod_pre_empenho
                                           , nlia.cod_entidade
                                           , nlia.vl_anulado
                                        FROM empenho.nota_liquidacao_item_anulado as nlia
                                       WHERE nlia.timestamp::date BETWEEN to_date( '''||dtIniExercicio||''', ''dd/mm/yyyy'')
                                                                AND to_date( '''||dtFinal||''', ''dd/mm/yyyy'')  
                                                                                                                                      ) as item_anulado
                                  ON(     item_anulado.exercicio       = nli.exercicio 
                                      AND item_anulado.cod_nota        = nli.cod_nota
                                      AND item_anulado.num_item        = nli.num_item
                                      AND item_anulado.exercicio_item  = nli.exercicio_item
                                      AND item_anulado.cod_pre_empenho = nli.cod_pre_empenho
                                      AND item_anulado.cod_entidade    = nli.cod_entidade     )
                         , empenho.empenho as e
                         , empenho.pre_empenho as pe
                         , empenho.pre_empenho_despesa as ped
                     WHERE
                           nli.exercicio        = nl.exercicio
                       AND nli.cod_entidade     = nl.cod_entidade
                       AND nli.cod_nota         = nl.cod_nota               
                       AND nl.exercicio_empenho = e.exercicio
                       AND nl.cod_entidade      = e.cod_entidade
                       AND nl.cod_empenho       = e.cod_empenho
                       AND e.exercicio          = pe.exercicio
                       AND e.cod_pre_empenho    = pe.cod_pre_empenho
                       AND pe.exercicio         = ped.exercicio
                       AND pe.cod_pre_empenho   = ped.cod_pre_empenho
                       AND e.exercicio = ''' || stExercicio || '''
                       AND e.cod_entidade IN (' || stCodEntidades || ')
                       AND nl.dt_liquidacao::date BETWEEN to_date( '''||dtIniExercicio||''', ''dd/mm/yyyy'')
                                                AND to_date( '''||dtFinal||''', ''dd/mm/yyyy'')
                  GROUP BY ped.cod_despesa                                                                     ) as liquidado_ate_bimestre
                 ON( liquidado_ate_bimestre.cod_despesa = d.cod_despesa )



           LEFT JOIN ( SELECT ss.exercicio
                            , ss.cod_despesa
                            , sum(ss.valor)           as vl_suplementado
                         FROM orcamento.suplementacao                   as s
                            , orcamento.suplementacao_suplementada      as ss
                        WHERE s.exercicio         = ss.exercicio
                          AND s.cod_suplementacao = ss.cod_suplementacao
                          AND s.dt_suplementacao::date BETWEEN to_date( '''||dtIniExercicio||''', ''dd/mm/yyyy'')   
                                                     AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')  
                     GROUP BY ss.exercicio
                            , ss.cod_despesa
                     ORDER BY ss.cod_despesa ) as suplementado
                  ON (     suplementado.exercicio   = d.exercicio
                       AND suplementado.cod_despesa = d.cod_despesa )
           LEFT JOIN ( SELECT sr.exercicio
                            , sr.cod_despesa
                            , sum(sr.valor)           as vl_reduzido
                         FROM orcamento.suplementacao              as s
                            , orcamento.suplementacao_reducao      as sr
                        WHERE s.exercicio         = sr.exercicio
                          AND s.cod_suplementacao = sr.cod_suplementacao
                          AND s.exercicio         = ''' || stExercicio || '''
                          AND s.dt_suplementacao::date BETWEEN to_date( '''||dtIniExercicio||''', ''dd/mm/yyyy'')
                                                     AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')   
                     GROUP BY sr.exercicio
                            , sr.cod_despesa
                     ORDER BY sr.cod_despesa ) as reduzido
                  ON (     reduzido.exercicio   = d.exercicio
                       AND reduzido.cod_despesa = d.cod_despesa )
           LEFT JOIN orcamento.funcao as f
                  on (    f.exercicio  = d.exercicio
                      AND f.cod_funcao = d.cod_funcao  )
           LEFT JOIN orcamento.subfuncao as sf
                  on (    sf.exercicio = d.exercicio
                      AND sf.cod_subfuncao = d.cod_subfuncao )
         , orcamento.conta_despesa     as cd
     WHERE
           d.cod_conta   = cd.cod_conta AND d.exercicio   = cd.exercicio
       AND d.exercicio   =  ''' || stExercicio || '''
       AND d.cod_entidade IN (' || stCodEntidades || ')
       AND substring(cd.cod_estrutural, 5, 3) = ''9.1''
  GROUP BY d.cod_funcao
         , d.cod_subfuncao
         , f.descricao
         , sf.descricao
  ORDER BY f.descricao;
    ';

    EXECUTE stSql;

    stSql := '
              SELECT  
                     sum(vl_original)                         as vl_original                      
                   , sum(vl_suplementacoes)                   as vl_suplementacoes
                   , sum(vl_empenhado_bimestre)               as vl_empenhado_bimestre
                   , sum(vl_empenhado_ate_bimestre)           as vl_empenhado_ate_bimestre
                   , sum(vl_liquidado_bimestre)               as vl_liquidado_bimestre
                   , sum(vl_liquidado_ate_bimestre)           as vl_liquidado_ate_bimestre
                FROM
                     tmp_intra_orcamentarias;

        ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    RETURN;

END;

$$ language plpgsql;

