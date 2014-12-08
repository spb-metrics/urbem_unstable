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
    stSql                   VARCHAR   := '';
    stSql1                  VARCHAR   := '';
    reRegistro              RECORD;
    dtInicial               VARCHAR := ''; 
    dtFinal                 VARCHAR := ''; 
    dtFinalDate             VARCHAR := ''; 

BEGIN
        
    dtInicial := '01/'||inMes||'/'||stExercicio;
    SELECT last_day (TO_DATE(dtInicial, 'dd/mm/yyyy')) INTO dtFinalDate;
    dtFinal := TO_CHAR(TO_DATE(dtFinalDate, 'yyyy-mm-dd'), 'dd/mm/yyyy');

    CREATE TEMPORARY TABLE tmp_retorno (
          mes                       INTEGER
        , contrib_pat               NUMERIC(14,2)
        , contrib_serv_ativo        NUMERIC(14,2)
        , contrib_serv_inat_pens    NUMERIC(14,2)
        , rec_patrimoniais          NUMERIC(14,2)
        , alienacao_bens            NUMERIC(14,2)
        , outras_rec_cap            NUMERIC(14,2)
        , comp_prev                 NUMERIC(14,2)
        , outras_rec                NUMERIC(14,2)
        , cod_tipo                  VARCHAR
        , contrib_pat_anterior      NUMERIC(14,2)
        , repasses_prev             NUMERIC(14,2)
        , receitas_prev_intra       NUMERIC(14,2)
    );    

    stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
        SELECT
              conta_receita.cod_estrutural as cod_estrutural
            , lote.dt_lote       as data
            , valor_lancamento.vl_lancamento   as valor
            , valor_lancamento.oid             as primeira
        FROM
            contabilidade.valor_lancamento      ,
            orcamento.conta_receita             ,
            orcamento.receita                   ,
            contabilidade.lancamento_receita    ,
            contabilidade.lancamento            ,
            contabilidade.lote                  
        WHERE

                receita.exercicio       IN (''' || stExercicio || ''')';
            if ( stCodEntidades != '' ) then
               stSql := stSql || ' AND receita.cod_entidade    IN (' || stCodEntidades || ') ';
            end if;

        stSql := stSql || '

            AND conta_receita.cod_conta       = receita.cod_conta
            AND conta_receita.exercicio       = receita.exercicio

            -- join lancamento receita
            AND lancamento_receita.cod_receita      = receita.cod_receita
            AND lancamento_receita.exercicio        = receita.exercicio
            AND lancamento_receita.estorno          = true
            -- tipo de lancamento receita deve ser = A , de arrecadação
            AND lancamento_receita.tipo             = ''A''

            -- join nas tabelas lancamento_receita e lancamento
            AND lancamento.cod_lote        = lancamento_receita.cod_lote
            AND lancamento.sequencia       = lancamento_receita.sequencia
            AND lancamento.exercicio       = lancamento_receita.exercicio
            AND lancamento.cod_entidade    = lancamento_receita.cod_entidade
            AND lancamento.tipo            = lancamento_receita.tipo

            -- join nas tabelas lancamento e valor_lancamento
            AND valor_lancamento.exercicio        = lancamento.exercicio
            AND valor_lancamento.sequencia        = lancamento.sequencia
            AND valor_lancamento.cod_entidade     = lancamento.cod_entidade
            AND valor_lancamento.cod_lote         = lancamento.cod_lote
            AND valor_lancamento.tipo             = lancamento.tipo
            -- na tabela valor lancamento  tipo_valor deve ser credito
            AND valor_lancamento.tipo_valor       = ''D''

            AND lote.cod_lote       = lancamento.cod_lote
            AND lote.cod_entidade   = lancamento.cod_entidade
            AND lote.exercicio      = lancamento.exercicio
            AND lote.tipo           = lancamento.tipo

        UNION

        SELECT
              conta_receita.cod_estrutural as cod_estrutural
            , lote.dt_lote       as data
            , valor_lancamento.vl_lancamento   as valor
            , valor_lancamento.oid             as segunda
        FROM
            contabilidade.valor_lancamento      ,
            orcamento.conta_receita             ,
            orcamento.receita                   ,
            contabilidade.lancamento_receita    ,
            contabilidade.lancamento            ,
            contabilidade.lote                  

        WHERE
            receita.exercicio       IN (''' || stExercicio || ''')';

            if ( stCodEntidades != '' ) then
               stSql := stSql || ' AND receita.cod_entidade    IN (' || stCodEntidades || ') ';
            end if;
        stSql := stSql || '

            AND conta_receita.cod_conta       = receita.cod_conta
            AND conta_receita.exercicio       = receita.exercicio


            -- join lancamento receita
            AND lancamento_receita.cod_receita      = receita.cod_receita
            AND lancamento_receita.exercicio        = receita.exercicio
            AND lancamento_receita.estorno          = false
            -- tipo de lancamento receita deve ser = A , de arrecadação
            AND lancamento_receita.tipo             = ''A''

            -- join nas tabelas lancamento_receita e lancamento
            AND lancamento.cod_lote        = lancamento_receita.cod_lote
            AND lancamento.sequencia       = lancamento_receita.sequencia
            AND lancamento.exercicio       = lancamento_receita.exercicio
            AND lancamento.cod_entidade    = lancamento_receita.cod_entidade
            AND lancamento.tipo            = lancamento_receita.tipo

            -- join nas tabelas lancamento e valor_lancamento
            AND valor_lancamento.exercicio        = lancamento.exercicio
            AND valor_lancamento.sequencia        = lancamento.sequencia
            AND valor_lancamento.cod_entidade     = lancamento.cod_entidade
            AND valor_lancamento.cod_lote         = lancamento.cod_lote
            AND valor_lancamento.tipo             = lancamento.tipo
            -- na tabela valor lancamento  tipo_valor deve ser credito
            AND valor_lancamento.tipo_valor       = ''C''

            -- Data Inicial e Data Final, antes iguala codigo do lote
            AND lote.cod_lote       = lancamento.cod_lote
            AND lote.cod_entidade   = lancamento.cod_entidade
            AND lote.exercicio      = lancamento.exercicio
            AND lote.tipo           = lancamento.tipo ) '; 

    EXECUTE stSql;
    
    stSql1 := '
    CREATE TEMPORARY TABLE tmp_receitas AS (
    SELECT
            cast(1 as integer) as grupo,
            cod_estrutural,
            cast(1 as integer) as nivel,
            nom_conta,
            coalesce(no_bimestre,0.00)*-1 as no_bimestre,
            coalesce(previsao_inicial,0.00)*-1 as previsao_inicial,
            coalesce(previsao_inicial,0.00)*-1 as previsao_atualizada
    FROM(
        SELECT
            plano_conta.cod_estrutural,
            plano_conta.nom_conta,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicial || '''
                                                     ,''' || dtFinal || '''
            ) as no_bimestre,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                    ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                    , ''' || stCodEntidades || '''
            ) as previsao_inicial
        FROM
            contabilidade.plano_conta,
            orcamento.conta_receita
            
        WHERE plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  
            AND plano_conta.exercicio        = conta_receita.exercicio
            AND (
                (plano_conta.cod_estrutural ILIKE ''4.1.2%''
                 AND publico.fn_nivel(plano_conta.cod_estrutural) = 3)
                OR (plano_conta.cod_estrutural ILIKE ''4.1.2.1.0.01%''
                    AND publico.fn_nivel(plano_conta.cod_estrutural) = 8)
                OR (plano_conta.cod_estrutural ILIKE ''4.1.2.1.0.29%''
                    AND publico.fn_nivel(plano_conta.cod_estrutural) = 7)
                OR (plano_conta.cod_estrutural like ''4.1.2.1.0.99%''
                    AND publico.fn_nivel(plano_conta.cod_estrutural) = 8)
                    
                OR (plano_conta.cod_estrutural ILIKE ''4.1.3%''
                    AND publico.fn_nivel(plano_conta.cod_estrutural) <= 4)
                    
                OR (plano_conta.cod_estrutural ILIKE ''4.1.6%''
                    AND publico.fn_nivel(plano_conta.cod_estrutural) = 3)
                
                OR (plano_conta.cod_estrutural ILIKE ''4.1.9%'')
                
                OR ((plano_conta.cod_estrutural ILIKE ''4.1.1%''
                    OR plano_conta.cod_estrutural ILIKE ''4.1.4%''
                    OR plano_conta.cod_estrutural ILIKE ''4.1.5%''
                    OR plano_conta.cod_estrutural ILIKE ''4.1.7%'')
                     AND publico.fn_nivel(plano_conta.cod_estrutural) = 3)
                     
                OR (plano_conta.cod_estrutural ILIKE ''4.2%''
                 AND publico.fn_nivel(plano_conta.cod_estrutural) = 3)
                 
                OR ((plano_conta.cod_estrutural ILIKE ''4.7%''
                    OR plano_conta.cod_estrutural ILIKE ''4.8%'')
                    AND publico.fn_nivel(plano_conta.cod_estrutural) = 2)
                    
                OR (plano_conta.cod_estrutural ILIKE ''4.%''
                    AND publico.fn_nivel(plano_conta.cod_estrutural) = 2)
                    
                )
             AND plano_conta.exercicio = ' || stExercicio || '
        ) as tbl
        
    UNION ALL 
    
    SELECT
            cast(1 as integer) as grupo,
            cod_estrutural,
            cast(1 as integer) as nivel,
            ''(-) DEDUÇÕES DA RECEITA'' AS nom_conta,
            coalesce(no_bimestre,0.00)*-1 as no_bimestre,
            coalesce(previsao_inicial,0.00)*-1 as previsao_inicial,
            coalesce(previsao_inicial,0.00)*-1 as previsao_atualizada
    FROM(
        SELECT
            plano_conta.cod_estrutural,
            plano_conta.nom_conta,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicial || '''
                                                     ,''' || dtFinal || '''
            ) as no_bimestre,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                    ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                    , ''' || stCodEntidades || '''
            ) as previsao_inicial
        FROM
            contabilidade.plano_conta,
            orcamento.conta_receita
            
        WHERE
          CASE WHEN plano_conta.exercicio <= ''2007'' THEN
                plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  
                AND plano_conta.cod_estrutural   like ''4.9%'' 
          ELSE
            plano_conta.cod_estrutural   = conta_receita.cod_estrutural  
            AND plano_conta.cod_estrutural   like ''9.%'' 
          END
          
          AND plano_conta.exercicio = conta_receita.exercicio 
          AND publico.fn_nivel(plano_conta.cod_estrutural) = 2 
          AND plano_conta.exercicio = ''' || stExercicio || '''
        
        ) as tbl
        
        ORDER BY cod_estrutural
    )
    ';
    EXECUTE stSql1;
    
    UPDATE tmp_receitas SET nom_conta = 'Receita Patrimonial'              WHERE cod_estrutural = '4.1.3.0.0.00.00.00.00.00';
    UPDATE tmp_receitas SET nom_conta = 'Receitas Imobiliárias'            WHERE cod_estrutural = '4.1.3.1.0.00.00.00.00.00';
    UPDATE tmp_receitas SET nom_conta = 'Receitas de Valores Imobiliários' WHERE cod_estrutural = '4.1.3.2.0.00.00.00.00.00';
    
    UPDATE tmp_receitas SET nom_conta = 'Receitas de Serviços'      WHERE cod_estrutural = '4.1.6.0.0.00.00.00.00.00';
    UPDATE tmp_receitas SET nom_conta = 'Outras Receitas Correntes' WHERE cod_estrutural = '4.1.9.0.0.00.00.00.00.00';
    
    UPDATE tmp_receitas SET nom_conta = 'Compensação Previdenciária do RGPS para o RPPS' WHERE cod_estrutural = '4.1.9.2.2.10.00.00.00.00';
    
    UPDATE tmp_receitas SET nom_conta = 'Alienação de Bens'      WHERE cod_estrutural = '4.2.2.0.0.00.00.00.00.00';
    UPDATE tmp_receitas SET nom_conta = 'Amortização de Empréstimos' WHERE cod_estrutural = '4.2.3.0.0.00.00.00.00.00';
    
    INSERT INTO tmp_retorno VALUES (  
                                      inMes
                                    -- Contribuição Patronal
                                    , (SELECT SUM(COALESCE(previsao_inicial, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.2.1.0.29.01.00.00.00')
                                    -- Contribuição do Servidor Ativo
                                    , (SELECT SUM(COALESCE(previsao_inicial, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.2.1.0.01.01.01.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.01.01.03.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.29.07.00.00.00')
                                    -- Contribuição do Servidor inativo e pensionista
                                    , (SELECT SUM(COALESCE(previsao_inicial, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.2.1.0.01.01.02.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.01.01.04.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.01.01.05.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.29.09.00.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.29.11.00.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.99.00.11.00.00')
                                    -- Receitas Patrimoniais
                                    , (SELECT SUM(COALESCE(previsao_inicial, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.3.0.0.00.00.00.00.00')
                                    -- Alienação de Bens
                                    , (SELECT SUM(COALESCE(previsao_inicial, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.2.2.0.0.00.00.00.00.00')
                                    -- Outras Receitas de Capital
                                    , (SELECT SUM(COALESCE(previsao_inicial, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.2.1.0.0.00.00.00.00.00'
                                                                                 OR cod_estrutural = '4.2.3.0.0.00.00.00.00.00'
                                                                                 OR cod_estrutural = '4.2.4.0.0.00.00.00.00.00'
                                                                                 OR cod_estrutural = '4.2.5.0.0.00.00.00.00.00')
                                    -- Compensações Previdenciárias
                                    , (SELECT SUM(COALESCE(previsao_inicial, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.2.1.0.99.00.10.00.00')
                                    -- Outras Receitas
                                    , (SELECT SUM(COALESCE(previsao_inicial, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural NOT LIKE '4.2%'
                                                                                AND cod_estrutural NOT LIKE '4.1.3%'
                                                                                AND cod_estrutural NOT LIKE '4.1.2.1.0%')

                                    -- Tipo de Receita
                                    , '01'
                                    -- Contribuição Patronal Anterior
                                    , 0.00
                                    -- Repasses Previdenciários
                                    , 0.00
                                    -- Receitas Previdenciárias intra-orçamentárias
                                    , ((SELECT SUM(COALESCE(previsao_inicial, 0.00)) FROM tmp_receitas  
                                                                               WHERE (cod_estrutural NOT ILIKE '4.9%'
                                                                                  OR  cod_estrutural NOT ILIKE '9.%')
                                                                                 AND publico.fn_nivel(cod_estrutural) = 3)
                                    - (SELECT SUM(COALESCE(previsao_inicial, 0.00)) FROM tmp_receitas  
                                                                              WHERE (cod_estrutural ILIKE '4.9%'
                                                                                 OR  cod_estrutural ILIKE '9.%')
                                                                                AND publico.fn_nivel(cod_estrutural) = 2))
                                   );


     INSERT INTO tmp_retorno VALUES (  
                                      inMes
                                    -- Contribuição Patronal
                                    , (SELECT SUM(COALESCE(previsao_atualizada, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.2.1.0.29.01.00.00.00')
                                    -- Contribuição do Servidor Ativo
                                    , (SELECT SUM(COALESCE(previsao_atualizada, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.2.1.0.01.01.01.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.01.01.03.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.29.07.00.00.00')
                                    -- Contribuição do Servidor inativo e pensionista
                                    , (SELECT SUM(COALESCE(previsao_atualizada, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.2.1.0.01.01.02.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.01.01.04.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.01.01.05.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.29.09.00.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.29.11.00.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.99.00.11.00.00')
                                    -- Receitas Patrimoniais
                                    , (SELECT SUM(COALESCE(previsao_atualizada, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.3.0.0.00.00.00.00.00')
                                    -- Alienação de Bens
                                    , (SELECT SUM(COALESCE(previsao_atualizada, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.2.2.0.0.00.00.00.00.00')
                                    -- Outras Receitas de Capital
                                    , (SELECT SUM(COALESCE(previsao_atualizada, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.2.1.0.0.00.00.00.00.00'
                                                                                 OR cod_estrutural = '4.2.3.0.0.00.00.00.00.00'
                                                                                 OR cod_estrutural = '4.2.4.0.0.00.00.00.00.00'
                                                                                 OR cod_estrutural = '4.2.5.0.0.00.00.00.00.00')
                                    -- Compensações Previdenciárias
                                    , (SELECT SUM(COALESCE(previsao_atualizada, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.2.1.0.99.00.10.00.00')
                                    -- Outras Receitas
                                    , (SELECT SUM(COALESCE(previsao_atualizada, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural NOT LIKE '4.2%'
                                                                                AND cod_estrutural NOT LIKE '4.1.3%'
                                                                                AND cod_estrutural NOT LIKE '4.1.2.1.0%')

                                    -- Tipo de Receita
                                    , '02'
                                    -- Contribuição Patronal Anterior
                                    , 0.00
                                    -- Repasses Previdenciários
                                    , 0.00
                                    -- Receitas Previdenciárias intra-orçamentárias
                                    , ((SELECT SUM(COALESCE(previsao_atualizada, 0.00)) FROM tmp_receitas  
                                                                               WHERE (cod_estrutural NOT ILIKE '4.9%'
                                                                                  OR  cod_estrutural NOT ILIKE '9.%')
                                                                                 AND publico.fn_nivel(cod_estrutural) = 3)
                                    - (SELECT SUM(COALESCE(previsao_atualizada, 0.00)) FROM tmp_receitas  
                                                                              WHERE (cod_estrutural ILIKE '4.9%'
                                                                                 OR  cod_estrutural ILIKE '9.%')
                                                                                AND publico.fn_nivel(cod_estrutural) = 2))
                                   );
   

    INSERT INTO tmp_retorno VALUES (  
                                      inMes
                                    -- Contribuição Patronal
                                    , (SELECT SUM(COALESCE(no_bimestre, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.2.1.0.29.01.00.00.00')
                                    -- Contribuição do Servidor Ativo
                                    , (SELECT SUM(COALESCE(no_bimestre, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.2.1.0.01.01.01.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.01.01.03.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.29.07.00.00.00')
                                    -- Contribuição do Servidor inativo e pensionista
                                    , (SELECT SUM(COALESCE(no_bimestre, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.2.1.0.01.01.02.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.01.01.04.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.01.01.05.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.29.09.00.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.29.11.00.00.00'
                                                                                 OR cod_estrutural = '4.1.2.1.0.99.00.11.00.00')
                                    -- Receitas Patrimoniais
                                    , (SELECT SUM(COALESCE(no_bimestre, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.3.0.0.00.00.00.00.00')
                                    -- Alienação de Bens
                                    , (SELECT SUM(COALESCE(no_bimestre, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.2.2.0.0.00.00.00.00.00')
                                    -- Outras Receitas de Capital
                                    , (SELECT SUM(COALESCE(no_bimestre, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.2.1.0.0.00.00.00.00.00'
                                                                                 OR cod_estrutural = '4.2.3.0.0.00.00.00.00.00'
                                                                                 OR cod_estrutural = '4.2.4.0.0.00.00.00.00.00'
                                                                                 OR cod_estrutural = '4.2.5.0.0.00.00.00.00.00')
                                    -- Compensações Previdenciárias
                                    , (SELECT SUM(COALESCE(no_bimestre, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural = '4.1.2.1.0.99.00.10.00.00')
                                    -- Outras Receitas
                                    , (SELECT SUM(COALESCE(no_bimestre, 0.00)) FROM tmp_receitas 
                                                                              WHERE cod_estrutural NOT LIKE '4.2%'
                                                                                AND cod_estrutural NOT LIKE '4.1.3%'
                                                                                AND cod_estrutural NOT LIKE '4.1.2.1.0%')

                                    -- Tipo de Receita
                                    , '04'
                                    -- Contribuição Patronal Anterior
                                    , 0.00
                                    -- Repasses Previdenciários
                                    , 0.00
                                    -- Receitas Previdenciárias intra-orçamentárias
                                    , ((SELECT SUM(COALESCE(no_bimestre, 0.00)) FROM tmp_receitas  
                                                                               WHERE (cod_estrutural NOT ILIKE '4.9%'
                                                                                  OR  cod_estrutural NOT ILIKE '9.%')
                                                                                 AND publico.fn_nivel(cod_estrutural) = 3)
                                    - (SELECT SUM(COALESCE(no_bimestre, 0.00)) FROM tmp_receitas  
                                                                              WHERE (cod_estrutural ILIKE '4.9%'
                                                                                 OR  cod_estrutural ILIKE '9.%')
                                                                                AND publico.fn_nivel(cod_estrutural) = 2))
                                   );

    stSql := ' SELECT mes 
                    , contrib_pat           
                    , contrib_serv_ativo    
                    , contrib_serv_inat_pens
                    , rec_patrimoniais      
                    , alienacao_bens        
                    , outras_rec_cap        
                    , comp_prev             
                    , outras_rec            
                    , cod_tipo              
                    , contrib_pat_anterior  
                    , repasses_prev         
                    , receitas_prev_intra  
                 FROM tmp_retorno
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_valor;
    DROP TABLE tmp_receitas;
    DROP TABLE tmp_retorno;

    RETURN;
END;
$$ language 'plpgsql';
