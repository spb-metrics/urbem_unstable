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
    * Arquivo de mapeamento para a função que busca os dados do recurso de alienacao do ativo
    * Data de Criação   : 29/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_recurso_alienacao_ativo(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    inMes               ALIAS FOR $3;
    arCodEntidade       VARCHAR[];
    stNomEntidade       VARCHAR := '';
    stDtInicial         VARCHAR := '';
    stDtFinal           VARCHAR := '';
    stFiltro            VARCHAR := '';
    stSql               VARCHAR := '';
    stCodRecursos       VARCHAR := '';
    reRegistro          RECORD;
    i                   INTEGER;

BEGIN

    stDtInicial := '01/01/' || stExercicio;
    stDtFinal := TO_CHAR(last_day(TO_DATE(stExercicio || '-' || inMes || '-' || '01','yyyy-mm-dd')),'dd/mm/yyyy');
    arCodEntidade := string_to_array(stCodEntidade,',');

    CREATE TEMPORARY TABLE tmp_retorno(
        mes                 INTEGER,
        saldo_anterior      NUMERIC(14,2),
        rec_realizada       NUMERIC(14,2),
        desp_emp            NUMERIC(14,2),
        desp_liq            NUMERIC(14,2),
        desp_paga           NUMERIC(14,2),
        cod_vinc            INTEGER,
        cod_entidade        INTEGER
    ); 

    ---------------------------------
    -- Recupera o nome das entidades
    ---------------------------------
    stSql := '
        CREATE TEMPORARY TABLE tmp_entidade AS (
            SELECT cod_entidade
                 , nom_cgm AS nom_entidade
              FROM orcamento.entidade
        INNER JOIN sw_cgm
                ON entidade.numcgm = sw_cgm.numcgm
             WHERE entidade.exercicio = ''' || stExercicio || '''
               AND entidade.cod_entidade IN (' || stCodEntidade || ')
        )      
    ';

    EXECUTE stSql;

    ----------------------------------------
    -- Recupera os recursos para a consulta
    ----------------------------------------
    SELECT ARRAY_TO_STRING(ARRAY( SELECT cod_recurso
                                    FROM stn.recurso_rreo_anexo_14
                                   WHERE exercicio = stExercicio
                                ),',' )
      INTO stCodRecursos;

    -----------------------------
    -- Recupera o saldo anterior
    -----------------------------

    stSql := '
        CREATE TEMPORARY TABLE tmp_saldo_anterior AS
            SELECT receita.cod_entidade
                 , SUM(receita.vl_original) AS valor
              FROM orcamento.receita
             WHERE receita.cod_recurso IN (' || stCodRecursos || ')
               AND receita.cod_entidade IN (' || stCodEntidade || ')
               AND receita.exercicio = ''' || stExercicio || '''
          GROUP BY receita.cod_entidade
    ';

    EXECUTE stSql;

    --------------------------------
    -- Recupera a receita realizada
    --------------------------------
    stSql := '
        CREATE TEMPORARY TABLE tmp_receita AS
            SELECT receita.cod_entidade
                 , SUM(valor_lancamento.vl_lancamento) as valor
              FROM orcamento.receita
        INNER JOIN orcamento.conta_receita
                ON receita.exercicio = conta_receita.exercicio
               AND receita.cod_conta = conta_receita.cod_conta
        INNER JOIN contabilidade.lancamento_receita       
                ON receita.exercicio   = lancamento_receita.exercicio
               AND receita.cod_receita = lancamento_receita.cod_receita
               AND lancamento_receita.estorno = true
               AND lancamento_receita.tipo = ''A''
        INNER JOIN contabilidade.lancamento
                ON lancamento_receita.exercicio    = lancamento.exercicio
               AND lancamento_receita.cod_entidade = lancamento.cod_entidade
               AND lancamento_receita.cod_lote     = lancamento.cod_lote
               AND lancamento_receita.sequencia    = lancamento.sequencia
               AND lancamento_receita.tipo         = lancamento.tipo 
        INNER JOIN contabilidade.valor_lancamento
                ON lancamento.exercicio        = valor_lancamento.exercicio
               AND lancamento.sequencia        = valor_lancamento.sequencia
               AND lancamento.cod_entidade     = valor_lancamento.cod_entidade
               AND lancamento.cod_lote         = valor_lancamento.cod_lote
               AND lancamento.tipo             = valor_lancamento.tipo
               AND valor_lancamento.tipo_valor = ''D''           
        INNER JOIN contabilidade.lote
                ON valor_lancamento.cod_lote     = lote.cod_lote
               AND valor_lancamento.cod_entidade = lote.cod_entidade
               AND valor_lancamento.exercicio    = lote.exercicio
               AND valor_lancamento.tipo         = lote.tipo
             WHERE receita.exercicio    = ''' || stExercicio || '''
               AND receita.cod_entidade IN (' || stCodEntidade || ')
               AND lote.dt_lote  BETWEEN to_char(to_date(''' || stDtInicial || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')
                                     AND to_char(to_date(''' || stDtFinal || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')
               AND receita.cod_recurso IN (' || stCodRecursos || ')
          GROUP BY receita.cod_entidade
    
             UNION
             
            SELECT receita.cod_entidade
                 , SUM(valor_lancamento.vl_lancamento) as valor
              FROM orcamento.receita
        INNER JOIN orcamento.conta_receita
                ON receita.exercicio = conta_receita.exercicio
               AND receita.cod_conta = conta_receita.cod_conta
        INNER JOIN contabilidade.lancamento_receita       
                ON receita.exercicio   = lancamento_receita.exercicio
               AND receita.cod_receita = lancamento_receita.cod_receita
               AND lancamento_receita.estorno = false
               AND lancamento_receita.tipo = ''A''
        INNER JOIN contabilidade.lancamento
                ON lancamento_receita.exercicio    = lancamento.exercicio
               AND lancamento_receita.cod_entidade = lancamento.cod_entidade
               AND lancamento_receita.cod_lote     = lancamento.cod_lote
               AND lancamento_receita.sequencia    = lancamento.sequencia
               AND lancamento_receita.tipo         = lancamento.tipo 
        INNER JOIN contabilidade.valor_lancamento
                ON lancamento.exercicio        = valor_lancamento.exercicio
               AND lancamento.sequencia        = valor_lancamento.sequencia
               AND lancamento.cod_entidade     = valor_lancamento.cod_entidade
               AND lancamento.cod_lote         = valor_lancamento.cod_lote
               AND lancamento.tipo             = valor_lancamento.tipo
               AND valor_lancamento.tipo_valor = ''C''           
        INNER JOIN contabilidade.lote
                ON valor_lancamento.cod_lote     = lote.cod_lote
               AND valor_lancamento.cod_entidade = lote.cod_entidade
               AND valor_lancamento.exercicio    = lote.exercicio
               AND valor_lancamento.tipo         = lote.tipo     
             WHERE receita.exercicio    = ''' || stExercicio || '''
               AND receita.cod_entidade IN (' || stCodEntidade || ')
               AND lote.dt_lote  BETWEEN to_char(to_date(''' || stDtInicial || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')
                                     AND to_char(to_date(''' || stDtFinal || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')

               AND receita.cod_recurso IN (' || stCodRecursos || ')
          GROUP BY receita.cod_entidade';

    EXECUTE stSql;

    --------------------------------
    -- Recupera a despesa empenhada
    --------------------------------
    stSql := 'CREATE TABLE tmp_empenhado AS (
               SELECT item_pre_empenho.vl_total as vl_total
                    , despesa.cod_entidade as cod_entidade
                 FROM orcamento.despesa
                 JOIN orcamento.recurso(''' || stExercicio || ''') as ORU
                   ON ( ORU.cod_recurso = despesa.cod_recurso
                  AND ORU.exercicio   = despesa.exercicio )
                    , orcamento.conta_despesa
                    , empenho.pre_empenho_despesa
                    , empenho.empenho
                    , empenho.pre_empenho
                    , empenho.item_pre_empenho
                WHERE conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                  AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                  AND despesa.exercicio       = pre_empenho_despesa.exercicio
                  AND despesa.cod_despesa     = pre_empenho_despesa.cod_despesa

                  AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                  AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                  AND pre_empenho.exercicio       = empenho.exercicio
                  AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                  AND pre_empenho.exercicio       = item_pre_empenho.exercicio
                  AND pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                  AND empenho.dt_empenho BETWEEN to_char(to_date(''' || stDtInicial || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')
                                             AND to_char(to_date(''' || stDtFinal || '''  , ''dd/mm/yyyy''), ''yyyy-mm-dd'')
                  AND pre_empenho.exercicio  = ''' || stExercicio  ||''' 
                  AND despesa.cod_entidade  IN (' || stCodEntidade || ')
                  AND despesa.cod_recurso   IN (' || stCodRecursos || ') ' ;

              stSql := stSql || ')';


              EXECUTE stSql;


       stSql := 'CREATE TABLE tmp_anulado as (
               SELECT empenho_anulado_item.vl_anulado as vl_anulado
                    , despesa.cod_entidade as cod_entidade
                 FROM orcamento.despesa
                 JOIN orcamento.recurso(''' || stExercicio || ''') as ORU
                   ON ( ORU.cod_recurso = despesa.cod_recurso
                  AND ORU.exercicio   = despesa.exercicio )
                    , orcamento.conta_despesa
                    , empenho.pre_empenho_despesa
                    , empenho.pre_empenho
                    , empenho.item_pre_empenho
                    , empenho.empenho_anulado_item
                WHERE conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                  AND conta_despesa.exercicio = pre_empenho_despesa.exercicio

                  AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                  AND despesa.exercicio   = pre_empenho_despesa.exercicio
  
                  AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                  AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
  
                  AND pre_empenho.exercicio       = item_pre_empenho.exercicio
                  AND pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
  
                  AND item_pre_empenho.exercicio       = empenho_anulado_item.exercicio
                  AND item_pre_empenho.cod_pre_empenho = empenho_anulado_item.cod_pre_empenho
                  AND item_pre_empenho.num_item        = empenho_anulado_item.num_item
                  AND empenho_anulado_item.timestamp BETWEEN to_char(to_date(''' || stDtInicial || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')
                                                         AND to_char(to_date(''' || stDtFinal || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')
  
                  AND empenho_anulado_item.exercicio   = ''' || stExercicio  || ''' 
                  AND despesa.cod_entidade            IN (' || stCodEntidade || ')
                  AND despesa.cod_recurso             IN (' || stCodRecursos || ') ' ;

              stSql := stSql || ')';

              EXECUTE stSql;

    --------------------------------
    -- Recupera a despesa liquidada
    --------------------------------
   stSql := 'CREATE TABLE  tmp_nota_liquidacao AS(
                 SELECT nota_liquidacao_item.vl_total as vl_total
                      , despesa.cod_entidade as cod_entidade
                   FROM orcamento.despesa 
                   JOIN orcamento.recurso(''' || stExercicio || ''') as oru
                     ON ( oru.cod_recurso = despesa.cod_recurso
                    AND oru.exercicio   = despesa.exercicio )
                      , orcamento.conta_despesa 
                      , empenho.pre_empenho_despesa 
                      , empenho.pre_empenho
                      , empenho.empenho 
                      , empenho.nota_liquidacao_item
                      , empenho.nota_liquidacao 
                  WHERE conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                    AND conta_despesa.exercicio = pre_empenho_despesa.exercicio

                    AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                    AND despesa.exercicio   = pre_empenho_despesa.exercicio

                    AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    AND pre_empenho.exercicio       = empenho.exercicio

                    AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                    AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                    AND empenho.cod_empenho  = nota_liquidacao.cod_empenho

                    AND nota_liquidacao.exercicio    = nota_liquidacao_item.exercicio
                    AND nota_liquidacao.cod_nota     = nota_liquidacao_item.cod_nota
                    AND nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade

                    AND pre_empenho.exercicio               = pre_empenho_despesa.exercicio
                    AND pre_empenho.cod_pre_empenho         = pre_empenho_despesa.cod_pre_empenho
                    AND nota_liquidacao.dt_liquidacao BETWEEN to_char(to_date(''' || stDtInicial || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')
                                                          AND to_char(to_date(''' || stDtFinal || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')

                    AND empenho.exercicio         = '''|| stExercicio || '''
                    AND nota_liquidacao.exercicio = '''|| stExercicio || ''' 
                    AND despesa.cod_entidade     IN (' || stCodEntidade || ')
                    AND despesa.cod_recurso      IN (' || stCodRecursos || ') ' ;

              stSql := stSql || ')';


              EXECUTE stSql;


   stSql := 'CREATE TABLE  tmp_nota_liquidacao_anulada AS (
                 SELECT nota_liquidacao_item_anulado.vl_anulado as vl_anulado
                      , despesa.cod_entidade as cod_entidade
                   FROM orcamento.despesa 
                   JOIN orcamento.recurso(''' || stExercicio || ''') as ORU
                     ON ( ORU.cod_recurso = despesa.cod_recurso
                    AND   ORU.exercicio   = despesa.exercicio )
                      , orcamento.conta_despesa
                      , empenho.pre_empenho_despesa 
                      , empenho.pre_empenho 
                      , empenho.empenho 
                      , empenho.nota_liquidacao 
                      , empenho.nota_liquidacao_item 
                      , empenho.nota_liquidacao_item_anulado 
                  WHERE
                       conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                   AND conta_despesa.exercicio = pre_empenho_despesa.exercicio

                   AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                   AND despesa.exercicio   = pre_empenho_despesa.exercicio

                   AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                   AND pre_empenho.exercicio       = empenho.exercicio

                   AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                   AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                   AND empenho.cod_empenho  = nota_liquidacao.cod_empenho

                   AND nota_liquidacao.exercicio    = nota_liquidacao_item.exercicio
                   AND nota_liquidacao.cod_nota     = nota_liquidacao_item.cod_nota
                   AND nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade

                   AND nota_liquidacao_item.exercicio       = nota_liquidacao_item_anulado.exercicio
                   AND nota_liquidacao_item.cod_pre_empenho = nota_liquidacao_item_anulado.cod_pre_empenho
                   AND nota_liquidacao_item.num_item        = nota_liquidacao_item_anulado.num_item
                   AND nota_liquidacao_item.cod_entidade    = nota_liquidacao_item_anulado.cod_entidade
                   AND nota_liquidacao_item.exercicio_item  = nota_liquidacao_item_anulado.exercicio_item
                   AND nota_liquidacao_item.cod_nota        = nota_liquidacao_item_anulado.cod_nota

                   AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                   AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                   AND nota_liquidacao_item_anulado.timestamp BETWEEN to_char(to_date(''' || stDtInicial || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')
                                                                  AND to_char(to_date(''' || stDtFinal || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')

                   AND empenho.exercicio        = '''|| stExercicio || '''
                   AND nota_liquidacao_item_anulado.exercicio = '''|| stExercicio || ''' 
                   AND despesa.cod_entidade    IN (' || stCodEntidade || ')
                   AND despesa.cod_recurso     IN (' || stCodRecursos || ') ' ;

              stSql := stSql || ')';


              EXECUTE stSql;

    --------------------------------
    -- Recupera a despesa paga
    --------------------------------
   stSql := 'CREATE TABLE  tmp_nota_liquidacao_paga AS (
               SELECT nota_liquidacao_paga.vl_pago as vl_pago
                    , despesa.cod_entidade as cod_entidade
                 FROM orcamento.despesa 
                 JOIN orcamento.recurso(''' || stExercicio || ''') as ORU
                   ON ( ORU.cod_recurso = despesa.cod_recurso
                  AND   ORU.exercicio   = despesa.exercicio )
                    , orcamento.conta_despesa 
                    , empenho.pre_empenho_despesa 
                    , empenho.empenho 
                    , empenho.pre_empenho 
                    , empenho.nota_liquidacao 
                    , empenho.nota_liquidacao_paga
                WHERE conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                  AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
 
                  AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                  AND despesa.exercicio   = pre_empenho_despesa.exercicio
 
                  AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                  AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
 
                  AND pre_empenho.exercicio       = empenho.exercicio
                  AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
 
                  AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                  AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                  AND empenho.cod_entidade = nota_liquidacao.cod_entidade
 
                  AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
                  AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                  AND nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                  AND nota_liquidacao_paga.timestamp BETWEEN to_char(to_date(''' || stDtInicial || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')
                                                         AND to_char(to_date(''' || stDtFinal || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')
 
                  AND empenho.exercicio              = '''|| stExercicio || '''
                  AND nota_liquidacao_paga.exercicio = '''|| stExercicio || ''' 
                  AND despesa.cod_entidade          IN (' || stCodEntidade || ')
                  AND despesa.cod_recurso           IN (' || stCodRecursos || ') ' ;

              stSql := stSql || ')';


              EXECUTE stSql;


   stSql := 'CREATE TABLE tmp_nota_liquidacao_paga_anulada  AS(
              SELECT nota_liquidacao_paga_anulada.vl_anulado as vl_anulado
                   , despesa.cod_entidade as cod_entidade
                FROM orcamento.despesa 
                JOIN orcamento.recurso(''' || stExercicio || ''') as ORU
                  ON ( ORU.cod_recurso = despesa.cod_recurso
                 AND   ORU.exercicio   = despesa.exercicio )
                   , orcamento.conta_despesa 
                   , empenho.pre_empenho_despesa 
                   , empenho.empenho
                   , empenho.pre_empenho 
                   , empenho.nota_liquidacao 
                   , empenho.nota_liquidacao_paga
                   , empenho.nota_liquidacao_paga_anulada 
               WHERE conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                 AND conta_despesa.exercicio = pre_empenho_despesa.exercicio

                 AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                 AND despesa.exercicio   = pre_empenho_despesa.exercicio

                 AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                 AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                 AND pre_empenho.exercicio       = empenho.exercicio
                 AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                 AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                 AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                 AND empenho.cod_entidade = nota_liquidacao.cod_entidade

                 AND nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                 AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
                 AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade

                 AND nota_liquidacao_paga.cod_entidade = nota_liquidacao_paga_anulada.cod_entidade
                 AND nota_liquidacao_paga.cod_nota     = nota_liquidacao_paga_anulada.cod_nota
                 AND nota_liquidacao_paga.exercicio    = nota_liquidacao_paga_anulada.exercicio
                 AND nota_liquidacao_paga.timestamp    = nota_liquidacao_paga_anulada.timestamp
                 AND nota_liquidacao_paga_anulada.timestamp_anulada BETWEEN to_char(to_date(''' || stDtInicial || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')
                                                                        AND to_char(to_date(''' || stDtFinal || ''', ''dd/mm/yyyy''), ''yyyy-mm-dd'')

                 AND empenho.exercicio              = '''|| stExercicio || '''
                 AND nota_liquidacao_paga.exercicio = '''|| stExercicio || ''' 
                 AND despesa.cod_entidade          IN (' || stCodEntidade || ')
                 AND despesa.cod_recurso           IN (' || stCodRecursos || ') ' ;

              stSql := stSql || ')';


              EXECUTE stSql;


    ---------------------------------------
    -- Faz as consultas para cada entidade 
    ---------------------------------------
    FOR i IN 1..ARRAY_UPPER(arCodEntidade,1) LOOP
        
        SELECT nom_entidade 
          INTO stNomEntidade
          FROM tmp_entidade
         WHERE cod_entidade = arCodEntidade[i];    

        INSERT INTO tmp_retorno VALUES (  12
                                        , ( SELECT COALESCE(SUM(valor),0.00)
                                              FROM tmp_saldo_anterior
                                             WHERE cod_entidade = arCodEntidade[i] )
                                        , ( SELECT COALESCE(SUM(valor),0.00)*(-1)
                                              FROM tmp_receita
                                             WHERE cod_entidade = arCodEntidade[i] )
                                        , ( ( SELECT COALESCE(SUM(vl_total), 0.00) 
                                                FROM tmp_empenhado
                                               WHERE cod_entidade = arCodEntidade[i] )
                                          - ( SELECT COALESCE(SUM(vl_anulado), 0.00)
                                                FROM tmp_anulado
                                               WHERE cod_entidade = arCodEntidade[i] ) )
                                        , ( ( SELECT COALESCE(SUM(vl_total), 0.00) 
                                                FROM tmp_nota_liquidacao
                                               WHERE cod_entidade = arCodEntidade[i] )
                                          - ( SELECT COALESCE(SUM(vl_anulado), 0.00)
                                                FROM tmp_nota_liquidacao_anulada
                                               WHERE cod_entidade = arCodEntidade[i] ) )
                                        , ( ( SELECT COALESCE(SUM(vl_pago), 0.00)
                                                FROM tmp_nota_liquidacao_paga
                                               WHERE cod_entidade = arCodEntidade[i] )
                                          - ( SELECT COALESCE(SUM(vl_anulado), 0.00)
                                                FROM tmp_nota_liquidacao_paga_anulada
                                               WHERE cod_entidade = arCodEntidade[i] ) )
                                        , CASE WHEN (stNomEntidade ILIKE '%prefeitura%')
                                               THEN 1
                                               WHEN (stNomEntidade ILIKE '%camara%' OR stNomEntidade ILIKE '%câmara%')
                                               THEN 3
                                               ELSE 2
                                          END
                                        , 0
                                       );           

    END LOOP;


    stSql := 'SELECT * FROM tmp_retorno';                                                 

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_nota_liquidacao;
    DROP TABLE tmp_nota_liquidacao_anulada;
    DROP TABLE tmp_nota_liquidacao_paga;
    DROP TABLE tmp_nota_liquidacao_paga_anulada;
    DROP TABLE tmp_entidade;
    DROP TABLE tmp_receita;
    DROP TABLE tmp_retorno;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  
