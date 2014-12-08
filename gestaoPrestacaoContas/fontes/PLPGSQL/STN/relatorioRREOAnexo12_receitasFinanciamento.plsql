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
    * Script de função PLPGSQL - Relatório STN - RREO - Anexo 16.
    * Data de Criação: 20/05/2008


    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-06.01.15

    $Id: $

*/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo12_receitas_financiamento (varchar, varchar ,varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio  ALIAS FOR $1;
    stDtInicial  ALIAS FOR $2;
    stDtFinal    ALIAS FOR $3;  
    stEntidades  ALIAS FOR $4;
    
    dtInicioAno     VARCHAR := '';
    stSql           VARCHAR := '';
    stSQLaux        VARCHAR := '';
    reRegistro      RECORD;
    reReg           RECORD;
    dtInicial       VARCHAR := ''; 
    dtFinal         VARCHAR := ''; 
    arDatas         VARCHAR[] ;

BEGIN
    dtInicioAno := '01/01/' || stExercicio;
    dtInicial   := stDtInicial;
    dtFinal     := stDtFinal;

    stSql := '
    CREATE TEMPORARY TABLE tmp_valor AS 
    SELECT
        ocr.cod_estrutural as cod_estrutural ,
        lote.dt_lote       as data ,
        vl.vl_lancamento   as valor ,
        vl.oid             as primeira
    FROM
        contabilidade.valor_lancamento      as vl   ,
        orcamento.conta_receita             as ocr  ,
        orcamento.receita                   as ore  ,
        contabilidade.lancamento_receita    as lr   ,
        contabilidade.lancamento            as lan  ,
        contabilidade.lote                  as lote
    WHERE
        ore.exercicio       = ''' || stExercicio || '''

        AND ore.cod_entidade    IN (' || stEntidades || ')

        AND ocr.cod_conta       = ore.cod_conta
        AND ocr.exercicio       = ore.exercicio

        -- join lancamento receita
        AND lr.cod_receita      = ore.cod_receita
        AND lr.exercicio        = ore.exercicio
        AND lr.estorno          = true
        -- tipo de lancamento receita deve ser = A , de arrecadação
        AND lr.tipo             = ''A''

        -- join nas tabelas lancamento_receita e lancamento
        AND lan.cod_lote        = lr.cod_lote
        AND lan.sequencia       = lr.sequencia
        AND lan.exercicio       = lr.exercicio
        AND lan.cod_entidade    = lr.cod_entidade
        AND lan.tipo            = lr.tipo

        -- join nas tabelas lancamento e valor_lancamento
        AND vl.exercicio        = lan.exercicio
        AND vl.sequencia        = lan.sequencia
        AND vl.cod_entidade     = lan.cod_entidade
        AND vl.cod_lote         = lan.cod_lote
        AND vl.tipo             = lan.tipo
        -- na tabela valor lancamento  tipo_valor deve ser credito
        AND vl.tipo_valor       = ''D''

        AND lote.cod_lote       = lan.cod_lote
        AND lote.cod_entidade   = lan.cod_entidade
        AND lote.exercicio      = lan.exercicio
        AND lote.tipo           = lan.tipo

    UNION ALL
    
    SELECT
        ocr.cod_estrutural as cod_estrutural ,
        lote.dt_lote       as data ,
        vl.vl_lancamento   as valor ,
        vl.oid             as segunda
    FROM
        contabilidade.valor_lancamento      as vl   ,
        orcamento.conta_receita             as ocr  ,
        orcamento.receita                   as ore  ,
        contabilidade.lancamento_receita    as lr   ,
        contabilidade.lancamento            as lan  ,
        contabilidade.lote                  as lote

    WHERE
        ore.exercicio       = ''' || stExercicio || '''
        AND ore.cod_entidade    IN (' || stEntidades || ')
        AND ocr.cod_conta       = ore.cod_conta
        AND ocr.exercicio       = ore.exercicio
        -- join lancamento receita
        AND lr.cod_receita      = ore.cod_receita
        AND lr.exercicio        = ore.exercicio
        AND lr.estorno          = false
        -- tipo de lancamento receita deve ser = A , de arrecadação
        AND lr.tipo             = ''A''

        -- join nas tabelas lancamento_receita e lancamento
        AND lan.cod_lote        = lr.cod_lote
        AND lan.sequencia       = lr.sequencia
        AND lan.exercicio       = lr.exercicio
        AND lan.cod_entidade    = lr.cod_entidade
        AND lan.tipo            = lr.tipo

        -- join nas tabelas lancamento e valor_lancamento
        AND vl.exercicio        = lan.exercicio
        AND vl.sequencia        = lan.sequencia
        AND vl.cod_entidade     = lan.cod_entidade
        AND vl.cod_lote         = lan.cod_lote
        AND vl.tipo             = lan.tipo
        -- na tabela valor lancamento  tipo_valor deve ser credito
        AND vl.tipo_valor       = ''C''

        -- Data Inicial e Data Final, antes iguala codigo do lote
        AND lote.cod_lote       = lan.cod_lote
        AND lote.cod_entidade   = lan.cod_entidade
        AND lote.exercicio      = lan.exercicio
        AND lote.tipo           = lan.tipo
    '; 

    EXECUTE stSql;

    -- -------------------------------------    
    -- Estrutura de Tabelas Temporarias
    -- -------------------------------------

    -- Tabela tmp_rreo_an12_receita

    CREATE TEMPORARY TABLE tmp_rreo_an12_receita (
        grupo INTEGER DEFAULT 0 ,
        subgrupo INTEGER DEFAULT 0 ,
        item INTEGER DEFAULT 0 ,
        descricao VARCHAR(150) DEFAULT NULL ,
        ini NUMERIC(14,2) DEFAULT 0.00 ,
        atu NUMERIC(14,2) DEFAULT 0.00 ,
        no_bi NUMERIC(14,2) DEFAULT 0.00 ,
        ate_bi NUMERIC(14,2) DEFAULT 0.00 ,
        pct NUMERIC(14,2) DEFAULT 0.00
    ) ;

    -- Tabela tmp_retorno
    -- Guarda os resultados para serem retornados na PL
    
    CREATE TEMPORARY TABLE tmp_retorno (
        grupo INTEGER DEFAULT 0 ,
        subgrupo INTEGER DEFAULT 0 ,
        item INTEGER DEFAULT 0,
        descricao VARCHAR(150) DEFAULT NULL ,
        previsao_inicial NUMERIC(14,2) DEFAULT 0.00 ,
        previsao_atualizada NUMERIC(14,2) DEFAULT 0.00 ,
        ate_periodo NUMERIC(14,2) DEFAULT 0.00 ,
        porc_periodo NUMERIC(14,2) DEFAULT 0.00 
    );

    -- -------------------------------------
    -- Fim Estrutura de Tabelas Temporarias
    -- ------------------------------------- 
    -- --------------------------------------------------------------
    -- Transferencias de recursos do sistema úncio de saúde - sus(II)
    -- --------------------------------------------------------------
    INSERT INTO tmp_rreo_an12_receita 
         SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
           FROM stn.fn_rreo_valor_conta('' || stExercicio || '', 'R', '1.7.2.1.33.00.00.00.00', '' || stEntidades || '', dtInicial ,   dtFinal, true, 2, 1, 0) ;

    INSERT INTO tmp_rreo_an12_receita 
         SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
           FROM stn.fn_rreo_valor_conta('' || stExercicio || '', 'R', '1.7.2.1.99.00.01.00.00', '' || stEntidades || '', dtInicial ,   dtFinal, true, 2, 1, 0) ;

    INSERT INTO tmp_rreo_an12_receita 
         SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
           FROM stn.fn_rreo_valor_conta('' || stExercicio || '', 'R', '1.7.2.2.33.00.00.00.00', '' || stEntidades || '', dtInicial ,   dtFinal, true, 2, 2, 0) ;

    INSERT INTO tmp_rreo_an12_receita 
         SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
           FROM stn.fn_rreo_valor_conta('' || stExercicio || '', 'R', '1.7.2.3.01.00.00.00.00', '' || stEntidades || '', dtInicial ,   dtFinal, true, 2, 3, 0) ;

--------------------------------------------------------------------------------------------------
--RECEITAS DE OPERAÇÕES DE CRÉDITO VINCULADAS À SAÚDE---
--------------------------------------------------------------------------------------------------
    INSERT INTO tmp_rreo_an12_receita 
         SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
           FROM stn.fn_rreo_valor_conta('' || stExercicio || '', 'R',   '2.1.1.4.02.00.00.00.00', '' || stEntidades || '', dtInicial ,   dtFinal, true, 3, 0, 0) ;

     INSERT INTO tmp_rreo_an12_receita 
         SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
           FROM stn.fn_rreo_valor_conta('' || stExercicio || '', 'R',   '2.1.2.3.02.00.00.00.00', '' || stEntidades || '', dtInicial ,   dtFinal, true, 3, 0, 0) ;

--------------------------------------------------------------------------------------------------
--TRANSFERÊNCIAS VOLUNTÁRIAS                    
--------------------------------------------------------------------------------------------------
      INSERT INTO tmp_rreo_an12_receita 
         SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
           FROM stn.fn_rreo_valor_conta('' || stExercicio || '', 'R',   '2.4.2.1.01.00.00.00.00', '' || stEntidades || '', dtInicial ,   dtFinal, true, 4, 0, 0) ;
      
      INSERT INTO tmp_rreo_an12_receita 
         SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
           FROM stn.fn_rreo_valor_conta('' || stExercicio || '', 'R',   '2.4.2.2.01.00.00.00.00', '' || stEntidades || '', dtInicial ,   dtFinal, true, 4, 0, 0) ;

      INSERT INTO tmp_rreo_an12_receita 
         SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
           FROM stn.fn_rreo_valor_conta('' || stExercicio || '', 'R',   '2.4.2.3.01.00.00.00.00', '' || stEntidades || '', dtInicial ,   dtFinal, true, 4, 0, 0) ;

      INSERT INTO tmp_rreo_an12_receita 
         SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
           FROM stn.fn_rreo_valor_conta('' || stExercicio || '', 'R',   '2.4.7.1.01.00.00.00.00', '' || stEntidades || '', dtInicial ,   dtFinal, true, 4, 0, 0) ;

       INSERT INTO tmp_rreo_an12_receita 
         SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
           FROM stn.fn_rreo_valor_conta('' || stExercicio || '', 'R',   '2.4.7.2.01.00.00.00.00', '' || stEntidades || '', dtInicial ,   dtFinal, true, 4, 0, 0) ;

      INSERT INTO tmp_rreo_an12_receita 
         SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
           FROM stn.fn_rreo_valor_conta('' || stExercicio || '', 'R',   ' 2.4.7.3.01.00.00.00.00', '' || stEntidades || '', dtInicial ,   dtFinal, true, 4, 0, 0) ;


    -- ------------------------------
    -- Outras receitas orcamentarias 
    -- ------------------------------
    stSql := '
        SELECT cod_estrutural
          FROM stn.vinculo_stn_recurso
    INNER JOIN stn.vinculo_recurso
            ON vinculo_recurso.cod_vinculo = vinculo_stn_recurso.cod_vinculo
    INNER JOIN orcamento.receita
            ON receita.cod_recurso = vinculo_recurso.cod_recurso
           AND receita.exercicio = vinculo_recurso.exercicio
           AND receita.cod_entidade = vinculo_recurso.cod_entidade
    INNER JOIN orcamento.conta_receita
            ON conta_receita.cod_conta = receita.cod_conta
           AND conta_receita.exercicio = receita.exercicio
         WHERE stn.vinculo_stn_recurso.cod_vinculo = 9
           AND stn.vinculo_recurso.exercicio = '''|| stExercicio ||''' 
    ';

    FOR reReg IN EXECUTE stSql
    LOOP
        INSERT INTO tmp_rreo_an12_receita
             SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
               FROM stn.fn_rreo_valor_conta('' || stExercicio || '', 'R', reReg.cod_estrutural, '' || stEntidades || '', dtInicial ,   dtFinal, true, 5, 0, 0) ;
    END LOOP;

    

    -- -------------------------------------
    -- Inserts com os valores de cada linha
    -- -------------------------------------

     
   


    INSERT INTO tmp_retorno
              SELECT CAST(2 AS INTEGER) AS grupo
                   , CAST(1 AS INTEGER) AS subgrupo
                   , CAST(0 AS INTEGER) AS item
                   , CAST('Provenientes da União' AS VARCHAR) AS descricao
                   , CAST(SUM(ini) AS NUMERIC) AS previsao_inicial
                   , CAST(SUM(atu) AS NUMERIC) AS previsao_atualizada
                   , CAST(SUM(ate_bi) AS NUMERIC) AS ate_periodo
                   , CASE WHEN SUM(atu) > 0
                          THEN  CAST(ROUND(SUM(ate_bi)/SUM(atu),2)*100 AS NUMERIC) 
                          ELSE 0 
                     END AS porc_periodo
                FROM tmp_rreo_an12_receita 
               WHERE grupo = 2
                 AND subgrupo = 1
                 AND item = 0;

    INSERT INTO tmp_retorno
              SELECT CAST(2 AS INTEGER) AS grupo
                   , CAST(2 AS INTEGER) AS subgrupo
                   , CAST(0 AS INTEGER) AS item
                   , CAST('Provenientes dos Estados' AS VARCHAR) AS descricao
                   , CAST(SUM(ini) AS NUMERIC) AS previsao_inicial
                   , CAST(SUM(atu) AS NUMERIC) AS previsao_atualizada
                   , CAST(SUM(ate_bi) AS NUMERIC) AS ate_periodo
                   , CASE WHEN SUM(atu) > 0
                          THEN  CAST(ROUND(SUM(ate_bi)/SUM(atu),2)*100 AS NUMERIC) 
                          ELSE 0 
                     END AS porc_periodo
                FROM tmp_rreo_an12_receita 
               WHERE grupo = 2
                 AND subgrupo = 2 
                 AND item = 0;

    INSERT INTO tmp_retorno
              SELECT CAST(2 AS INTEGER) AS grupo
                   , CAST(3 AS INTEGER) AS subgrupo
                   , CAST(0 AS INTEGER) AS item
                   , CAST('Provenientes de Outros Municípios' AS VARCHAR) AS descricao
                   , CAST(SUM(ini) AS NUMERIC) AS previsao_inicial
                   , CAST(SUM(atu) AS NUMERIC) AS previsao_atualizada
                   , CAST(SUM(ate_bi) AS NUMERIC) AS ate_periodo
                   , CASE WHEN SUM(atu) > 0
                          THEN  CAST(ROUND(SUM(ate_bi)/SUM(atu),2)*100 AS NUMERIC) 
                          ELSE 0 
                     END AS porc_periodo
                FROM tmp_rreo_an12_receita 
               WHERE grupo = 2
                 AND subgrupo = 3
                 AND item = 0;

    INSERT INTO tmp_retorno
              SELECT CAST(2 AS INTEGER) AS grupo
                   , CAST(4 AS INTEGER) AS subgrupo
                   , CAST(0 AS INTEGER) AS item
                   , CAST('Outras Receitas do SUS' AS VARCHAR) AS descricao
                   , CAST(0 AS NUMERIC) AS previsao_inicial
                   , CAST(0 AS NUMERIC) AS previsao_atualizada
                   , CAST(0 AS NUMERIC) AS ate_periodo
                   , CASE WHEN SUM(atu) > 0
                          THEN  CAST(ROUND(SUM(ate_bi)/SUM(atu),2)*100 AS NUMERIC) 
                          ELSE 0 
                     END AS porc_periodo
                FROM tmp_rreo_an12_receita 
               WHERE grupo = 2
                 AND subgrupo = 4
                 AND item = 0;

    -- Somatorio do grupo 2
    INSERT INTO tmp_retorno
              SELECT CAST(2 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS subgrupo
                   , CAST(0 AS INTEGER) AS item
                   , CAST('TRANSFERÊNCIA DE RECURSOS DO SISTEMA ÚNICO DE SAÚDE-SUS' AS VARCHAR) AS descricao
                   , CAST(SUM(previsao_inicial) AS NUMERIC) AS previsao_inicial
                   , CAST(SUM(previsao_atualizada) AS NUMERIC) AS previsao_atualizada
                   , CAST(SUM(ate_periodo) AS NUMERIC) AS ate_periodo
                   , CASE WHEN SUM(previsao_atualizada) > 0
                          THEN  CAST(ROUND(SUM(ate_periodo)/SUM(previsao_atualizada),2)*100 AS NUMERIC) 
                          ELSE 0 
                     END AS porc_periodo
                FROM tmp_retorno
               WHERE grupo = 2;


    INSERT INTO tmp_retorno
              SELECT CAST(3 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS subgrupo
                   , CAST(0 AS INTEGER) AS item
                   , CAST('RECEITAS DE OPERAÇÕES DE CRÉDITO VINCULADAS À SAÚDE ' AS VARCHAR) AS descricao
                   , CAST(SUM(ini) AS NUMERIC) AS previsao_inicial
                   , CAST(SUM(atu) AS NUMERIC) AS previsao_atualizada
                   , CAST(SUM(ate_bi) AS NUMERIC) AS ate_periodo
                   , CASE WHEN SUM(atu) > 0
                          THEN  CAST(ROUND(SUM(ate_bi)/SUM(atu),2)*100 AS NUMERIC) 
                          ELSE 0 
                     END AS porc_periodo
                FROM tmp_rreo_an12_receita 
               WHERE grupo = 3
                 AND subgrupo = 0
                 AND item = 0;

       INSERT INTO tmp_retorno
              SELECT CAST(4 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS subgrupo
                   , CAST(0 AS INTEGER) AS item
                   , CAST('TRANSFERÊNCIAS VOLUNTÁRIAS' AS VARCHAR) AS descricao
                   , 0 AS previsao_inicial
                   , 0 AS previsao_atualizada
                   , 0 AS ate_periodo
                   ,  CASE WHEN SUM(atu) > 0
                          THEN  CAST(ROUND(SUM(ate_bi)/SUM(atu),2)*100 AS NUMERIC) 
                          ELSE 0 
                     END AS porc_periodo
                FROM tmp_rreo_an12_receita 
               WHERE grupo = 4
                 AND subgrupo = 0
                 AND item = 0;


    INSERT INTO tmp_retorno
              SELECT CAST(5 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS subgrupo
                   , CAST(0 AS INTEGER) AS item
                   , CAST('OUTRAS RECEITAS PARA FINANCIAMENTO DA SAÚDE' AS VARCHAR) AS descricao
                   , CAST(SUM(ini) AS NUMERIC) AS previsao_inicial
                   , CAST(SUM(atu) AS NUMERIC) AS previsao_atualizada
                   , CAST(SUM(ate_bi) AS NUMERIC) AS ate_periodo
                   , CASE WHEN SUM(atu) > 0
                          THEN  CAST(ROUND(SUM(ate_bi)/SUM(atu),2)*100 AS NUMERIC) 
                          ELSE 0 
                     END AS porc_periodo
                FROM tmp_rreo_an12_receita 
               WHERE grupo = 5
                 AND subgrupo = 0
                 AND item = 0;




    stSql := 'SELECT grupo
                   , subgrupo
                   , item
                   , descricao
                   , COALESCE(previsao_inicial,0) AS previsao_inicial
                   , COALESCE(previsao_atualizada,0) AS previsao_atualizada
                   , COALESCE(ate_periodo,0) AS ate_periodo
                   , COALESCE(porc_periodo,0) AS porc_periodo
                FROM tmp_retorno 
            ORDER BY grupo
                   , subgrupo
                   , item
    ';

    FOR reReg IN EXECUTE stSql
    LOOP
        RETURN NEXT reReg;
    END LOOP;


    DROP TABLE tmp_rreo_an12_receita ;
    DROP TABLE tmp_valor ;
    DROP TABLE tmp_retorno ;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';
