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


CREATE OR REPLACE FUNCTION tcemg.fn_despesa_prev(varchar, varchar ,varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    dtInicial               ALIAS FOR $3;
    dtFinal                 ALIAS FOR $4;

    dtInicioAno             VARCHAR   := '';
    dtFimAno                VARCHAR   := '';
    stSql                   VARCHAR   := '';
    stSql1                  VARCHAR   := '';
    stMascClassReceita      VARCHAR   := '';
    stMascRecurso           VARCHAR   := '';
    stCodEstrutural         VARCHAR   := '';
    reRegistro              RECORD;

    arDatas varchar[] ;

BEGIN
        
        dtInicioAno := '01/01/' || stExercicio;
        
        stSql := '
        CREATE TEMPORARY TABLE tmp_despesa_lib AS (
            SELECT
                cd.exercicio,
                cd.cod_estrutural, 
                d.cod_entidade,
                d.num_orgao,
                d.num_unidade, 
                d.cod_despesa,
                d.cod_funcao,
                d.cod_subfuncao, 
                d.cod_recurso, 
                SUM(COALESCE(d.vl_original,0.00)) AS vl_original,
                COALESCE((SUM(COALESCE(sups.vl_suplementado,0.00)) - SUM(COALESCE(supr.vl_reduzido,0.00))), 0.00) AS vl_credito_adicional 
            FROM
                orcamento.conta_despesa cd
                INNER JOIN
                orcamento.despesa d ON
                    d.exercicio = cd.exercicio AND
                    d.cod_conta = cd.cod_conta
            
            --Suplementacoes
        
            LEFT JOIN (
                SELECT
                    sups.exercicio, 
                    sups.cod_despesa, 
                    SUM(sups.valor) AS vl_suplementado 
                FROM
                    orcamento.suplementacao sup
                    INNER JOIN 
                    orcamento.suplementacao_suplementada sups ON
                        sup.exercicio = sups.exercicio AND
                        sup.cod_suplementacao = sups.cod_suplementacao 
                WHERE 
                    sup.exercicio = ''' || stExercicio || '''  AND
                    sup.dt_suplementacao BETWEEN TO_DATE(''' || dtInicioAno || ''', ''dd/mm/yyyy'') AND
                                             TO_DATE(''' || dtFinal || ''', ''dd/mm/yyyy'') 
                    
                GROUP BY
                    sups.exercicio, 
                    sups.cod_despesa
            ) sups ON
                sups.exercicio = d.exercicio AND 
                sups.cod_despesa = d.cod_despesa 
            
            LEFT JOIN (
                SELECT
                    supr.exercicio, 
                    supr.cod_despesa, 
                    SUM(supr.valor) as vl_reduzido 
                FROM 
                    orcamento.suplementacao sup
                    INNER JOIN
                    orcamento.suplementacao_reducao supr ON
                        sup.exercicio = supr.exercicio AND 
                        sup.cod_suplementacao = supr.cod_suplementacao 
                WHERE 
                    sup.exercicio = ''' || stExercicio || '''  AND
                    sup.dt_suplementacao BETWEEN TO_DATE(''' || dtInicioAno || ''', ''dd/mm/yyyy'') AND
                                             TO_DATE(''' || dtFinal || ''', ''dd/mm/yyyy'') 
                GROUP BY 
                    supr.exercicio, 
                    supr.cod_despesa
            ) AS supr ON
              supr.exercicio = d.exercicio AND 
              supr.cod_despesa = d.cod_despesa 
            
            WHERE 
                cd.exercicio = ''' || stExercicio || ''' AND 
                d.cod_entidade IN (' || stCodEntidades || ') 
            
            GROUP BY 
                cd.exercicio, 
                cd.cod_estrutural, 
                d.cod_entidade, 
                d.num_orgao, 
                d.num_unidade, 
                d.cod_despesa, 
                d.cod_funcao, 
                d.cod_subfuncao, 
                d.cod_recurso 
            )
            ';
        EXECUTE stSql;
        
        
        stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS (
            SELECT
                    cast(1 as integer) AS grupo
                  , cast(1 as integer) as nivel
                  , conta_despesa.descricao as nom_conta
                  ,	conta_despesa.cod_estrutural
                  , sum(tmp_despesa_lib.vl_original) as vl_original
                  , (coalesce(sum(tmp_despesa_lib.vl_original),0.00)) + (coalesce(sum(tmp_despesa_lib.vl_credito_adicional),0.00))  as vl_suplementacoes
                  , case when( empenho.cod_empenho is not null ) then 
                        (SELECT * 
                           FROM  empenho.fn_empenho_empenhado(   ''' || stExercicio || '''
                                                             , empenho.cod_empenho
                                                             , ''' ||  stCodEntidades ||'''
                                                             , '''||dtInicial||'''
                                                             ,   '''||dtFinal||'''))                        
                    else
                         ''0.00''   
                    end as vl_empenhado
                  , funcao.cod_funcao
            FROM orcamento.despesa
            LEFT JOIN 	orcamento.conta_despesa
                    ON	conta_despesa.cod_conta = despesa.cod_conta
                    AND	conta_despesa.exercicio = despesa.exercicio
            LEFT JOIN orcamento.funcao
                   ON   funcao.exercicio = despesa.exercicio 
                   AND  funcao.cod_funcao = despesa.cod_funcao
            
            LEFT JOIN tmp_despesa_lib
                   ON tmp_despesa_lib.exercicio    = despesa.exercicio 
                  AND tmp_despesa_lib.cod_despesa  = despesa.cod_despesa 
            LEFT JOIN empenho.pre_empenho_despesa
                   ON pre_empenho_despesa.exercicio   = despesa.exercicio
                  AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa
            LEFT JOIN empenho.pre_empenho
                   ON pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                  AND pre_empenho.exercicio       = pre_empenho_despesa.exercicio 
            LEFT JOIN empenho.empenho
                   ON empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                  AND empenho.exercicio       = pre_empenho.exercicio
            where despesa.exercicio = ''' || stExercicio || '''
                and despesa.cod_entidade IN (' || stCodEntidades || ') 
                --and despesa.cod_funcao IN (4,9)
                and (conta_despesa.cod_estrutural ilike ''3.%''
                     or conta_despesa.cod_estrutural ilike ''4.%''
                     or conta_despesa.cod_estrutural ilike ''9.%''
                     or conta_despesa.cod_estrutural ilike ''7.%'')
                --and conta_despesa.cod_estrutural not ilike ''%.9.1.%''
            group by 	funcao.descricao, conta_despesa.cod_estrutural, conta_despesa.descricao,  empenho.cod_empenho, funcao.cod_funcao
            order by 	funcao.descricao, conta_despesa.cod_estrutural    
        ) '; 

        EXECUTE stSql;



    SELECT cod_estrutural INTO stCodEstrutural
      FROM tmp_despesa
     WHERE cod_estrutural = '3.3.9.0.01.00.00.00.00';

    IF stCodEstrutural IS NULL THEN
        INSERT INTO tmp_despesa VALUES (2, 3, 'Aposentadorias', '3.3.9.0.01.00.00.00.00');
    ELSE 
        UPDATE tmp_despesa SET nom_conta = 'Aposentadorias' WHERE cod_estrutural = '3.3.9.0.01.00.00.00.00';
    END IF;

    SELECT cod_estrutural INTO stCodEstrutural
      FROM tmp_despesa
     WHERE cod_estrutural = '3.3.9.0.03.00.00.00.00';

    IF stCodEstrutural IS NULL THEN
        INSERT INTO tmp_despesa VALUES (2, 3, 'Pensões', '3.3.9.0.03.00.00.00.00');
    ELSE
        UPDATE tmp_despesa SET nom_conta = 'Pensões' WHERE cod_estrutural = '3.3.9.0.03.00.00.00.00';
    END IF;

    SELECT cod_estrutural INTO stCodEstrutural
      FROM tmp_despesa
     WHERE cod_estrutural = '3.3.9.0.05.00.00.00.00';

    IF stCodEstrutural IS NULL THEN
        INSERT INTO tmp_despesa VALUES (2, 3, 'Outros Benefícios Previdenciários', '3.3.9.0.05.00.00.00.00');
    ELSE
        UPDATE tmp_despesa SET nom_conta = 'Outros Benefícios Previdenciários' WHERE cod_estrutural = '3.3.9.0.05.00.00.00.00';
    END IF;

    SELECT cod_estrutural INTO stCodEstrutural
      FROM tmp_despesa
     WHERE cod_estrutural = '3.3.9.0.09.00.00.00.00';

    IF stCodEstrutural IS NULL THEN
        INSERT INTO tmp_despesa VALUES (2, 3, 'Outros Benefícios Previdenciários', '3.3.9.0.09.00.00.00.00');
    ELSE
        UPDATE tmp_despesa SET nom_conta = 'Outros Benefícios Previdenciários'
                             , cod_estrutural = '3.3.9.0.05.00.00.00.00' WHERE cod_estrutural = '3.3.9.0.09.00.00.00.00';
    END IF;

 
    INSERT INTO tmp_despesa VALUES (3, 3, 'Pessoal Militar', 'militar1');
    INSERT INTO tmp_despesa VALUES (3, 4, 'Reformas', 'militar2');
    INSERT INTO tmp_despesa VALUES (3, 4, 'Pensões', 'militar3');
    INSERT INTO tmp_despesa VALUES (3, 4, 'Outros Benefícios Previdenciários', 'militar4');

    IF stCodEstrutural IS NULL THEN
        INSERT INTO tmp_despesa VALUES (4, 5, 'Reserva de Contingência', '9.9.9.9.99.00.00.00.00');
    ELSE
        UPDATE tmp_despesa SET nom_conta = 'Reserva de Contingência'
                             , cod_estrutural = '9.9.9.9.99.00.00.00.00' WHERE cod_estrutural = '9.%';
    END IF;
    
    stSql := '

    CREATE TEMPORARY TABLE tmp_retorno AS (
        SELECT 1 AS grupo
            ,  sum(coalesce(vl_original, 0.00)) as vl_original
            ,  sum(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  sum(coalesce(vl_empenhado, 0.00)) as vl_empenhado
          FROM tmp_despesa
         WHERE cod_funcao = 4

        UNION ALL

        SELECT 2 AS grupo
            ,  sum(coalesce(vl_original, 0.00)) as vl_original
            ,  sum(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  sum(coalesce(vl_empenhado, 0.00)) as vl_empenhado
          FROM tmp_despesa
         WHERE cod_funcao = 9

        UNION ALL
    
        SELECT 3 AS grupo
            ,  sum(coalesce(vl_original, 0.00)) as vl_original
            ,  sum(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  sum(coalesce(vl_empenhado, 0.00)) as vl_empenhado
          FROM tmp_despesa
         WHERE cod_funcao = 9
           AND (cod_estrutural LIKE ''3.1.9.0.01%''
            OR cod_estrutural LIKE ''3.1.9.0.03%'')

        UNION ALL

        SELECT 4 AS grupo
            ,  sum(coalesce(vl_original, 0.00)) as vl_original
            ,  sum(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  sum(coalesce(vl_empenhado, 0.00)) as vl_empenhado
          FROM tmp_despesa
         WHERE cod_estrutural LIKE ''3.3%''

        UNION ALL

        SELECT 5 AS grupo
            ,  sum(coalesce(vl_original, 0.00)) as vl_original
            ,  sum(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  sum(coalesce(vl_empenhado, 0.00)) as vl_empenhado
          FROM tmp_despesa
         WHERE cod_estrutural LIKE ''4.4%''

        UNION ALL

        SELECT 6 AS grupo
            ,  cast(0.00 as numeric(14,2)) as vl_original
            ,  cast(0.00 as numeric(14,2)) as vl_suplementacoes
            ,  cast(0.00 as numeric(14,2)) as vl_empenhado

        UNION ALL
    
        SELECT 7 AS grupo
            ,  coalesce(vl_original, 0.00) as vl_original
            ,  coalesce(vl_suplementacoes, 0.00) as vl_suplementacoes
            ,  coalesce(vl_empenhado, 0.00) as vl_empenhado
          FROM tmp_despesa
         WHERE cod_estrutural LIKE ''3.1.9.1.13%''

        UNION ALL

        SELECT 8 AS grupo
            ,  cast(0.00 as numeric(14,2)) as vl_original
            ,  cast(0.00 as numeric(14,2)) as vl_suplementacoes
            ,  cast(0.00 as numeric(14,2)) as vl_empenhado

        UNION ALL

        SELECT 9 AS grupo
            ,  cast(0.00 as numeric(14,2)) as vl_original
            ,  cast(0.00 as numeric(14,2)) as vl_suplementacoes
            ,  cast(0.00 as numeric(14,2)) as vl_empenhado

        UNION ALL
    
        SELECT 10 AS grupo
            ,  coalesce(vl_original, 0.00) as vl_original
            ,  coalesce(vl_suplementacoes, 0.00) as vl_suplementacoes
            ,  coalesce(vl_empenhado, 0.00) as vl_empenhado
          FROM tmp_despesa
         WHERE cod_estrutural LIKE ''3%''
           AND cod_funcao = 4

        UNION ALL
    
        SELECT 11 AS grupo
            ,  SUM(coalesce(vl_original, 0.00)) as vl_original
            ,  SUM(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  SUM(coalesce(vl_empenhado, 0.00)) as vl_empenhado
          FROM tmp_despesa
         WHERE cod_estrutural LIKE ''4%''
           AND cod_funcao = 4

        UNION ALL

        SELECT 12 AS grupo
            ,  SUM(coalesce(vl_original, 0.00)) as vl_original
            ,  SUM(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  SUM(coalesce(vl_empenhado, 0.00)) as vl_empenhado
          FROM tmp_despesa
         WHERE cod_estrutural LIKE ''3.3.9.0.05%''
           AND cod_funcao = 9

        UNION ALL

        SELECT 13 AS grupo
            ,  SUM(coalesce(vl_original, 0.00)) as vl_original
            ,  SUM(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  SUM(coalesce(vl_empenhado, 0.00)) as vl_empenhado
          FROM tmp_despesa
         WHERE cod_estrutural LIKE ''3.3.2.0.01.01%''
           AND cod_funcao = 9

        UNION ALL

        SELECT 14 AS grupo
            ,  SUM(coalesce(vl_original, 0.00)) as vl_original
            ,  SUM(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  SUM(coalesce(vl_empenhado, 0.00)) as vl_empenhado
          FROM tmp_despesa
         WHERE (cod_estrutural NOT LIKE ''3.1.9.0.01%''
            OR cod_estrutural NOT LIKE ''3.1.9.0.03%''
            OR cod_estrutural NOT LIKE ''3.3.9.0.05%''
            OR cod_estrutural NOT LIKE ''3.1.9.1.13%''
            OR cod_estrutural NOT LIKE ''3.3.2.0.01.01%'')
           AND cod_funcao = 9
    )';
   
    EXECUTE stSql;
   
    /*CREATE TEMP SEQUENCE seq;
 
    stSql := '
    CREATE TABLE tmp_retorno_index AS(
    SELECT nextval(''seq'') AS cont 
         , vl_original
         , vl_suplementacoes
         , vl_empenhado
      FROM tmp_retorno 
    )';    

    EXECUTE stSql;
    */

/*
    stSql := '

    SELECT
        ( SELECT sum(coalesce(vl_original,0.00)) as vl_original
            FROM tmp_retorno_index
           WHERE cont = 1 ) as despPrevSocInatPens
      , ( SELECT sum(coalesce(vl_original,0.00)) as vl_original
            FROM tmp_retorno_index
           WHERE cont = 19 ) as despReservaContingencia
      , ( SELECT sum(coalesce(vl_original,0.00)) as vl_original
            FROM tmp_retorno_index
           WHERE cont = 18 ) as despOutrasReservas
      , cast(01 as integer) as  codTipo
      , ( SELECT sum(coalesce(vl_original,0.00)) as vl_original
            FROM tmp_retorno_index
           WHERE cont = 17 ) as despesasPrevIntra
      , ( SELECT sum(coalesce(vl_original,0.00))
            FROM tmp_retorno_index
           WHERE cont = 3 ) as despCorrentes
      , ( SELECT sum(coalesce(vl_original,0.00)) as vl_original
            FROM tmp_retorno_index
           WHERE cont = 4 ) as despCapital
      , ( SELECT sum(coalesce(vl_original,0.00)) as vl_original
            FROM tmp_retorno_index
           WHERE cont = 9
              OR cont = 13 ) as outrosBeneficios
      , ( SELECT sum(coalesce(vl_original,0.00)) as vl_original
            FROM tmp_retorno_index
           WHERE cont = 15 ) as contPrevidenciaria
      , ( SELECT sum(coalesce(vl_original,0.00)) as vl_original
            FROM tmp_retorno_index
           WHERE cont = 16 ) as outrasDespesas
    
    UNION ALL

    SELECT
        ( SELECT sum(coalesce(vl_suplementacoes,0.00)) as vl_suplementacoes
            FROM tmp_retorno_index
           WHERE cont = 7
              OR cont = 8
              OR cont = 11
              OR cont = 12 ) as despPrevSocInatPens
      , ( SELECT sum(coalesce(vl_suplementacoes,0.00)) as vl_suplementacoes
            FROM tmp_retorno_index
           WHERE cont = 19 ) as despReservaContingencia
      , ( SELECT sum(coalesce(vl_suplementacoes,0.00)) as vl_suplementacoes
            FROM tmp_retorno_index
           WHERE cont = 18 ) as despOutrasReservas
      , cast(02 as integer) as codTipo
      , ( SELECT sum(coalesce(vl_suplementacoes,0.00)) as vl_suplementacoes
            FROM tmp_retorno_index
           WHERE cont = 17 ) as despesasPrevIntra
      , ( SELECT sum(coalesce(vl_suplementacoes,0.00)) as vl_suplementacoes
            FROM tmp_retorno_index
           WHERE cont = 3 ) as despCorrentes
      , ( SELECT sum(coalesce(vl_suplementacoes,0.00)) as vl_suplementacoes
            FROM tmp_retorno_index
           WHERE cont = 4 ) as despCapital
      , ( SELECT sum(coalesce(vl_suplementacoes,0.00)) as vl_suplementacoes
            FROM tmp_retorno_index
           WHERE cont = 9
              OR cont = 13 ) as outrosBeneficios
      , ( SELECT sum(coalesce(vl_suplementacoes,0.00)) as vl_suplementacoes
            FROM tmp_retorno_index
           WHERE cont = 15 ) as contPrevidenciaria
      , ( SELECT sum(coalesce(vl_suplementacoes,0.00)) as vl_suplementacoes
            FROM tmp_retorno_index
           WHERE cont = 16 ) as outrasDespesas

    UNION ALL

    SELECT
        ( SELECT sum(coalesce(vl_empenhado,0.00)) as vl_empenhado
            FROM tmp_retorno_index
           WHERE cont = 7
              OR cont = 8
              OR cont = 11
              OR cont = 12 ) as despPrevSocInatPens
      , ( SELECT sum(coalesce(vl_empenhado,0.00)) as vl_empenhado
            FROM tmp_retorno_index
           WHERE cont = 19 ) as despReservaContingencia
      , ( SELECT sum(coalesce(vl_empenhado,0.00)) as vl_empenhado
            FROM tmp_retorno_index
           WHERE cont = 18 ) as despOutrasReservas
      , cast(03 as integer) as codTipo
      , ( SELECT sum(coalesce(vl_empenhado,0.00)) as vl_empenhado
            FROM tmp_retorno_index
           WHERE cont = 17 ) as despesasPrevIntra
      , ( SELECT sum(coalesce(vl_empenhado,0.00)) as vl_empenhado
            FROM tmp_retorno_index
           WHERE cont = 3 ) as despCorrentes
      , ( SELECT sum(coalesce(vl_empenhado,0.00)) as vl_empenhado
            FROM tmp_retorno_index
           WHERE cont = 4 ) as despCapital
      , ( SELECT sum(coalesce(vl_empenhado,0.00)) as vl_empenhado
            FROM tmp_retorno_index
           WHERE cont = 9
              OR cont = 13 ) as outrosBeneficios
      , ( SELECT sum(coalesce(vl_empenhado,0.00)) as vl_empenhado
            FROM tmp_retorno_index
           WHERE cont = 15 ) as contPrevidenciaria
      , ( SELECT sum(coalesce(vl_empenhado,0.00)) as vl_empenhado
            FROM tmp_retorno_index
           WHERE cont = 16 ) as outrasDespesas

    ';
*/

    stSql := '
                SELECT
                        ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 1 ) AS despAdmGeral
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 2 ) AS despPrevSoci
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 3 ) AS despPrevSocInatPens
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 4 ) AS outrasDespCorrentes
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 5 ) AS despInvestimentos
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 6 ) AS despInversoesFinanceiras
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 7 ) AS despesasPrevIntra
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 8 ) AS despReserva
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 9 ) AS despOutrasReservas
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 10 ) AS despCorrentes
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 11 ) AS despCapital
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 12 ) AS outrosBeneficios
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 13 ) AS contPrevidenciaria
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_original
                            FROM tmp_despesa
                           WHERE grupo = 14 ) AS outrasDespesas
                      , CAST(01 AS INTEGER) as codTipo

                UNION ALL

                SELECT
                        ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 1 ) AS despAdmGeral
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 2 ) AS despPrevSoci
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 3 ) AS despPrevSocInatPens
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 4 ) AS outrasDespCorrentes
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 5 ) AS despInvestimentos
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 6 ) AS despInversoesFinanceiras
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 7 ) AS despesasPrevIntra
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 8 ) AS despReserva
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 9 ) AS despOutrasReservas
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 10 ) AS despCorrentes
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 11 ) AS despCapital
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 12 ) AS outrosBeneficios
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 13 ) AS contPrevidenciaria
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_suplementacoes
                            FROM tmp_despesa
                           WHERE grupo = 14 ) AS outrasDespesas
                      , CAST(02 AS INTEGER) as codTipo

                UNION ALL

                SELECT
                        ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 1 ) AS despAdmGeral
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 2 ) AS despPrevSoci
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 3 ) AS despPrevSocInatPens
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 4 ) AS outrasDespCorrentes
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 5 ) AS despInvestimentos
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 6 ) AS despInversoesFinanceiras
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 7 ) AS despesasPrevIntra
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 8 ) AS despReserva
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 9 ) AS despOutrasReservas
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 10 ) AS despCorrentes
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 11 ) AS despCapital
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 12 ) AS outrosBeneficios
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 13 ) AS contPrevidenciaria
                      , ( SELECT SUM(COALESCE(vl_empenhado,0.00)) as vl_empenhado
                            FROM tmp_despesa
                           WHERE grupo = 14 ) AS outrasDespesas
                      , CAST(03 AS INTEGER) as codTipo
    ';


    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    --DROP TABLE tmp_retorno_index;

    RETURN;
END;
$$ language 'plpgsql';
