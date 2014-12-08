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
    * Arquivo de mapeamento para a função que busca os dados inscritos de restos a pagar
    * Data de Criação   : 20/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_insc_restos_a_pagar(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio              ALIAS FOR $1;
    stCodEntidade            ALIAS FOR $2;
    inMes                    ALIAS FOR $3;
    stDtInicial              VARCHAR := '';
    stDtFinal                VARCHAR := '';
    stSql                    VARCHAR := '';
    inCodEntidadeRPPS        VARCHAR;
    inCodEntidadeLegislativo VARCHAR;
    inCodEntidade            VARCHAR;
    reRegistro               RECORD;

BEGIN

    stDtInicial := '01/01/' || stExercicio;
    stDtFinal := TO_CHAR(last_day(TO_DATE(stExercicio || '-' || inMes || '-' || '01','yyyy-mm-dd')),'dd/mm/yyyy');

    --recupera a entidade RPPS do sistema
    SELECT valor
      INTO inCodEntidadeRPPS
      FROM administracao.configuracao
     WHERE configuracao.exercicio  = stExercicio
       AND configuracao.cod_modulo = 8
       AND configuracao.parametro  = 'cod_entidade_rpps';

    --retira a entidade rpps das entidades selecionadas
    SELECT SUBSTR(REPLACE(stCodEntidade || ',', ',' || inCodEntidadeRPPS  || ',',','),1,LENGTH( REPLACE(stCodEntidade || ',', ',' || inCodEntidadeRPPS  || ',',','))-1)
      INTO inCodEntidade;

    --seleciona a entidade do legislativo
    SELECT cod_entidade
      INTO inCodEntidadeLegislativo
      FROM orcamento.entidade
INNER JOIN sw_cgm
        ON entidade.numcgm = sw_cgm.numcgm
     WHERE entidade.exercicio = stExercicio
       AND (    nom_cgm ILIKE '%camara%'
             OR nom_cgm ILIKE '%câmara%');

    IF((SELECT POSITION(inCodEntidadeLegislativo IN stCodEntidade)) = 0)
    THEN
        inCodEntidadeLegislativo := 0;
    END IF; 

    --cria a tabela temporaria para retornar os dados
    CREATE TEMPORARY TABLE tmp_retorno(
        mes                          INTEGER,
        vl_processado                NUMERIC(14,2),
        vl_nao_processado            NUMERIC(14,2),
        vl_despesa_nao_inscrita      NUMERIC(14,2),
        vl_vinculado                 NUMERIC(14,2),
        vl_nao_vinculado             NUMERIC(14,2),
        vl_rpps_processado           NUMERIC(14,2),
        vl_rpps_nao_processado       NUMERIC(14,2),
        vl_rpps_despesa_nao_inscrita NUMERIC(14,2),
        vl_rpps_vinculado            NUMERIC(14,2),
        vl_rpps_nao_vinculado        NUMERIC(14,2),
        vl_processado_legislativo    NUMERIC(14,2)

    );

    --Cria uma table temporaria para guardar os dados dos restos
    CREATE TEMPORARY TABLE tmp_restos AS
        SELECT lpad(tb.cod_recurso,4,'0') as cod_recurso
             , CASE WHEN( recurso.tipo IS NULL)
                    THEN 'L'
                    ELSE recurso.tipo
               END AS tipo_recurso
             , sum(liquidados_nao_pagos) as liquidados_nao_pagos
             , sum(empenhados_nao_liquidados) as empenhados_nao_liquidados
          FROM stn.fn_rgf_anexo6_recurso(stExercicio,inCodEntidade,stDtFinal) AS tb
               (  cod_recurso integer
                , tipo varchar
                , total_processados_exercicios_anteriores numeric
                , total_processados_exercicio_anterior numeric
                , total_nao_processados_exercicios_anteriores numeric
                , total_nao_processados_exercicio_anterior numeric
                , liquidados_nao_pagos numeric
                , empenhados_nao_liquidados numeric
               )
     LEFT JOIN orcamento.recurso(stExercicio) AS recurso
            ON recurso.cod_recurso = tb.cod_recurso
           AND recurso.exercicio = stExercicio
      GROUP BY tb.cod_recurso
             , recurso.tipo
      ORDER BY tb.cod_recurso
             , recurso.tipo;

    --Cria uma table temporaria para guardar os dados dos restos rpps
    CREATE TEMPORARY TABLE tmp_restos_rpps AS
        SELECT lpad(tb.cod_recurso,4,'0') as cod_recurso
             , CASE WHEN( recurso.tipo IS NULL)
                    THEN 'L'
                    ELSE recurso.tipo
               END AS tipo_recurso
             , sum(liquidados_nao_pagos) as liquidados_nao_pagos
             , sum(empenhados_nao_liquidados) as empenhados_nao_liquidados
          FROM stn.fn_rgf_anexo6_recurso(stExercicio,inCodEntidadeRPPS,stDtFinal) AS tb
               (  cod_recurso integer
                , tipo varchar
                , total_processados_exercicios_anteriores numeric
                , total_processados_exercicio_anterior numeric
                , total_nao_processados_exercicios_anteriores numeric
                , total_nao_processados_exercicio_anterior numeric
                , liquidados_nao_pagos numeric
                , empenhados_nao_liquidados numeric
               )
     LEFT JOIN orcamento.recurso(stExercicio) AS recurso
            ON recurso.cod_recurso = tb.cod_recurso
           AND recurso.exercicio = stExercicio
      GROUP BY tb.cod_recurso
             , recurso.tipo
      ORDER BY tb.cod_recurso
             , recurso.tipo;

    --Cria uma table temporaria para guardar os dados dos restos do legislativo
    CREATE TEMPORARY TABLE tmp_restos_legislativo AS
        SELECT lpad(tb.cod_recurso,4,'0') as cod_recurso
             , CASE WHEN( recurso.tipo IS NULL)
                    THEN 'L'
                    ELSE recurso.tipo
               END AS tipo_recurso
             , sum(liquidados_nao_pagos) as liquidados_nao_pagos
             , sum(empenhados_nao_liquidados) as empenhados_nao_liquidados
          FROM stn.fn_rgf_anexo6_recurso(stExercicio,inCodEntidadeLegislativo,stDtFinal) AS tb
               (  cod_recurso integer
                , tipo varchar
                , total_processados_exercicios_anteriores numeric
                , total_processados_exercicio_anterior numeric
                , total_nao_processados_exercicios_anteriores numeric
                , total_nao_processados_exercicio_anterior numeric
                , liquidados_nao_pagos numeric
                , empenhados_nao_liquidados numeric
               )
     LEFT JOIN orcamento.recurso(stExercicio) AS recurso
            ON recurso.cod_recurso = tb.cod_recurso
           AND recurso.exercicio = stExercicio
      GROUP BY tb.cod_recurso
             , recurso.tipo
      ORDER BY tb.cod_recurso
             , recurso.tipo;

    --insere os valores na tabela temporaria
    INSERT INTO tmp_retorno VALUES (  inMes
                                    , CAST(COALESCE(( SELECT SUM(liquidados_nao_pagos)
                                                        FROM tmp_restos),0) AS NUMERIC)
                                    , CAST(COALESCE(( SELECT SUM(empenhados_nao_liquidados)
                                                        FROM tmp_restos),0) AS NUMERIC)
                                    , CAST(0 AS NUMERIC)
                                    , CAST(COALESCE(( SELECT SUM(COALESCE(liquidados_nao_pagos,0))
                                                             +
                                                             SUM(COALESCE(empenhados_nao_liquidados,0))
                                                        FROM tmp_restos
                                                       WHERE tipo_recurso <> 'L'),0) AS NUMERIC)
                                    , CAST(COALESCE(( SELECT SUM(COALESCE(liquidados_nao_pagos,0))
                                                             +
                                                             SUM(COALESCE(empenhados_nao_liquidados,0))
                                                        FROM tmp_restos
                                                       WHERE tipo_recurso = 'L'),0) AS NUMERIC)
                                    , CAST(COALESCE(( SELECT SUM(liquidados_nao_pagos)
                                                        FROM tmp_restos_rpps),0) AS NUMERIC)
                                    , CAST(COALESCE((SELECT SUM(empenhados_nao_liquidados)
                                                       FROM tmp_restos_rpps),0) AS NUMERIC)
                                    , CAST(0 AS NUMERIC)
                                    , CAST(COALESCE(( SELECT SUM(COALESCE(liquidados_nao_pagos,0))
                                                             +
                                                             SUM(COALESCE(empenhados_nao_liquidados,0))
                                                        FROM Tmp_restos_rpps
                                                       WHERE Tipo_recurso <> 'L'),0) AS NUMERIC)
                                    , CAST(COALESCE(( SELECT SUM(COALESCE(liquidados_nao_pagos,0))
                                                             +
                                                             SUM(COALESCE(empenhados_nao_liquidados,0))
                                                        FROM tmp_restos_rpps
                                                       WHERE tipo_recurso = 'L'),0) AS NUMERIC)
                                    , CAST(COALESCE(( SELECT SUM(liquidados_nao_pagos)
                                                   FROM tmp_restos_legislativo),0) AS NUMERIC));


  stSql := '
      SELECT *
        FROM tmp_retorno
  ';

  FOR reRegistro IN EXECUTE stSql
  LOOP
      RETURN NEXT reRegistro;
  END LOOP;

  DROP TABLE tmp_retorno;
  DROP TABLE tmp_restos_rpps;
  DROP TABLE tmp_restos_legislativo;
  DROP TABLE tmp_restos;

  RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  
