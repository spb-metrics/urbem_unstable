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
    * Nota:
    * O parametro de entrada inOpcao (integer)
    * se refere ao tipo de despesa desejada.
    * Opções válidas para inOpcao:
    
    1) 1 = Despesas realizadas com recursos do Fundeb
    2) 2 = MDE - Despesas Realizadas com Recursos de 
           Ações Típicas de Manutenção do Ensino
    3) 3 = Outras Despesas MDE
   
*/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo8_despesas(stExercicio VARCHAR, stEntidades VARCHAR, inOpcao INTEGER, stDtIni VARCHAR, stDtFim VARCHAR) RETURNS SETOF RECORD AS $$

DECLARE 

    stDtIniExercicio VARCHAR := ''; 
    stOperacao       CHARACTER(1);
    stSQL            VARCHAR := '';
    stSQLaux         VARCHAR := '';
    stSQLRestos      VARCHAR := '';
    reReg            RECORD;

BEGIN 
    -- Definicao de Datas conforme Bimestre selecionado
    stDtIniExercicio := '01/01/' || stExercicio;

    --Validando restos de acordo com o bimestre
    IF ( stDtIni = '01/11/'||stExercicio||'' AND stDtFim = '31/12/'||stExercicio||'') THEN
        stSQLRestos := ' COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                                , '|| quote_literal(stExercicio)      ||'
                                                                                , '|| quote_literal(stEntidades)      ||'
                                                                                , '|| quote_literal(stDtIniExercicio) ||'
                                                                                , '|| quote_literal(stDtFim)          ||'
                                                                                , false )
                        ), 0.00) AS vl_restos_inscritos';
    ELSE
        stSQLRestos := ' COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                                , '|| quote_literal(stExercicio) ||'
                                                                                , '|| quote_literal(stEntidades) ||'
                                                                                , '|| quote_literal(stDtIni)     ||'
                                                                                , '|| quote_literal(stDtFim)     ||'
                                                                                , false )
                        ), 0.00) AS vl_restos_inscritos';
    END IF;

    -- --------------------------------------------
    -- Inicio das Tabelas Temporarias
    -- --------------------------------------------

    stSQL := '
    
    CREATE TEMPORARY TABLE tmp_despesa_liberada AS (
    SELECT
        cd.exercicio,
        cd.cod_estrutural, 
        d.cod_entidade,
        d.num_orgao,
        d.num_unidade, 
        d.cod_despesa,
        d.cod_recurso, 
        SUM(COALESCE(d.vl_original,0.00)) AS vl_original, 
        COALESCE((SUM(COALESCE(sups.vl_suplementado,0.00)) - SUM(COALESCE(supr.vl_reduzido,0.00))), 0.00) AS vl_credito_adicional 
    FROM
        orcamento.conta_despesa cd
        INNER JOIN
        orcamento.despesa d ON
            d.exercicio = cd.exercicio AND
            d.cod_conta = cd.cod_conta
        
        LEFT JOIN 
        
        (SELECT
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
            sup.exercicio = ''' || stExercicio || ''' AND
            sup.dt_suplementacao BETWEEN TO_DATE(''' || stDtIniExercicio || ''', ''dd/mm/yyyy'') AND
                                         TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') 
        GROUP BY
            sups.exercicio, 
            sups.cod_despesa
        ) sups ON
            sups.exercicio = d.exercicio AND 
            sups.cod_despesa = d.cod_despesa 
        
        LEFT JOIN
        
        (SELECT
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
            sup.exercicio = ''' || stExercicio || ''' AND 
            sup.dt_suplementacao BETWEEN TO_DATE(''' || stDtIniExercicio || ''', ''dd/mm/yyyy'') AND 
                                         TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') 
        GROUP BY 
            supr.exercicio, 
            supr.cod_despesa
        ) AS supr ON
            supr.exercicio = d.exercicio AND 
            supr.cod_despesa = d.cod_despesa 
    WHERE 
        cd.exercicio = ''' || stExercicio || ''' AND 
        d.cod_entidade IN (' || stEntidades || ') 
    GROUP BY 
        cd.exercicio,
        cd.cod_estrutural, 
        d.cod_entidade,
        d.num_orgao,
        d.num_unidade, 
        d.cod_despesa,
        d.cod_recurso 
    )
    ';

    EXECUTE stSQL;


    /*
    Despesas Liquidadas do primeiro dia do exercicio
    até o último dia do bimestre selecionado
    */
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_despesa_liquidada_total AS (
    SELECT 
        pedcd.exercicio, 
        pedcd.cod_despesa, 
        pedcd.cod_subfuncao, 
        COALESCE(SUM(nli.vl_total), 0.00) AS vl_liquidado 
    FROM 
        empenho.pre_empenho pe
        
        LEFT JOIN
        
        (SELECT 
            ped.exercicio, 
            ped.cod_pre_empenho, 
            d.cod_despesa, 
            d.cod_subfuncao 
        FROM 
            empenho.pre_empenho_despesa ped
            INNER JOIN 
            orcamento.despesa d ON 
                ped.exercicio   = d.exercicio AND
                ped.cod_despesa = d.cod_despesa
        WHERE 
            ped.exercicio = ''' || stExercicio || ''' 
        ) AS pedcd ON 
            pe.exercicio = pedcd.exercicio AND 
            pe.cod_pre_empenho = pedcd.cod_pre_empenho
            
        INNER JOIN
        
        empenho.empenho e ON 
            e.exercicio = pe.exercicio AND 
            e.cod_pre_empenho = pe.cod_pre_empenho
            
        INNER JOIN
        
        empenho.nota_liquidacao nl ON 
            nl.exercicio_empenho = e.exercicio AND 
            nl.cod_entidade = e.cod_entidade AND 
            nl.cod_empenho = e.cod_empenho
            
        INNER JOIN
        
        empenho.nota_liquidacao_item nli ON 
            nli.exercicio = nl.exercicio AND 
            nli.cod_entidade = nl.cod_entidade AND 
            nli.cod_nota = nl.cod_nota 
            
    WHERE 
        e.exercicio = ''' || stExercicio || ''' AND 
        e.cod_entidade IN (' || stEntidades || ') AND 
        nl.dt_liquidacao BETWEEN to_date(''' || stDtIniExercicio || ''', ''dd/mm/yyyy'') AND 
                                 to_date(''' || stDtFim || ''', ''dd/mm/yyyy'') 
    GROUP BY
        pedcd.exercicio, 
        pedcd.cod_despesa, 
        pedcd.cod_subfuncao 
    )';
    
    EXECUTE stSQL;
    

    
    -- /* Despesas Liquidadas durante o bimestre */
    
    -- stSQL := '
    -- CREATE TEMPORARY TABLE tmp_despesa_liquidada_bimestre AS (
    -- SELECT 
    --     pedcd.exercicio, 
    --     pedcd.cod_despesa, 
    --     pedcd.cod_subfuncao, 
    --     COALESCE(SUM(nli.vl_total), 0.00) AS vl_liquidado 
    -- FROM 
    --     empenho.pre_empenho pe
        
    --     LEFT JOIN
        
    --     (SELECT 
    --         ped.exercicio, 
    --         ped.cod_pre_empenho, 
    --         d.cod_despesa, 
    --         d.cod_subfuncao 
    --     FROM 
    --         empenho.pre_empenho_despesa ped
    --         INNER JOIN 
    --         orcamento.despesa d ON 
    --             ped.exercicio   = d.exercicio AND
    --             ped.cod_despesa = d.cod_despesa
    --     WHERE 
    --         ped.exercicio = ''' || stExercicio || ''' 
    --     ) AS pedcd ON 
    --         pe.exercicio = pedcd.exercicio AND 
    --         pe.cod_pre_empenho = pedcd.cod_pre_empenho
            
    --     INNER JOIN
        
    --     empenho.empenho e ON 
    --         e.exercicio = pe.exercicio AND 
    --         e.cod_pre_empenho = pe.cod_pre_empenho
            
    --     INNER JOIN
        
    --     empenho.nota_liquidacao nl ON 
    --         nl.exercicio_empenho = e.exercicio AND 
    --         nl.cod_entidade = e.cod_entidade AND 
    --         nl.cod_empenho = e.cod_empenho
            
    --     INNER JOIN
        
    --     empenho.nota_liquidacao_item nli ON 
    --         nli.exercicio = nl.exercicio AND 
    --         nli.cod_entidade = nl.cod_entidade AND 
    --         nli.cod_nota = nl.cod_nota 

    -- WHERE 
    --     e.exercicio = ''' || stExercicio || ''' AND 
    --     e.cod_entidade IN (' || stEntidades || ') AND 
    --     nl.dt_liquidacao BETWEEN to_date(''' || stDtIni || ''', ''dd/mm/yyyy'') AND 
    --                              to_date(''' || stDtFim || ''', ''dd/mm/yyyy'') 
    -- GROUP BY
    --     pedcd.exercicio, 
    --     pedcd.cod_despesa, 
    --     pedcd.cod_subfuncao 
    -- )';
    
    -- EXECUTE stSQL;
    
    
    /*
    Despesas Estornadas do primeiro dia do exercicio
    até o último dia do bimestre selecionado
    */
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_despesa_estornada_total AS (
    SELECT 
        pedcd.exercicio, 
        pedcd.cod_despesa, 
        pedcd.cod_subfuncao, 
        COALESCE(SUM(nlia.vl_anulado), 0.00) AS vl_estornado 
    FROM 
        empenho.pre_empenho pe
        
        LEFT JOIN
        
        (SELECT 
            ped.exercicio, 
            ped.cod_pre_empenho, 
            d.cod_despesa, 
            d.cod_subfuncao 
        FROM 
            empenho.pre_empenho_despesa ped
            INNER JOIN 
            orcamento.despesa d ON 
                ped.exercicio   = d.exercicio AND
                ped.cod_despesa = d.cod_despesa
        WHERE 
            ped.exercicio = ''' || stExercicio || ''' 
        ) AS pedcd ON 
            pe.exercicio = pedcd.exercicio AND 
            pe.cod_pre_empenho = pedcd.cod_pre_empenho
            
        INNER JOIN
        
        empenho.empenho e ON 
            e.exercicio = pe.exercicio AND 
            e.cod_pre_empenho = pe.cod_pre_empenho
            
        INNER JOIN
        
        empenho.nota_liquidacao nl ON 
            nl.exercicio_empenho = e.exercicio AND 
            nl.cod_entidade = e.cod_entidade AND 
            nl.cod_empenho = e.cod_empenho
            
        INNER JOIN
        
        empenho.nota_liquidacao_item nli ON 
            nli.exercicio = nl.exercicio AND 
            nli.cod_entidade = nl.cod_entidade AND 
            nli.cod_nota = nl.cod_nota 
        
        INNER JOIN 
        
        (SELECT
            exercicio,
            cod_entidade,
            cod_nota,
            num_item,
            cod_pre_empenho,
            exercicio_item,
            timestamp, 
            COALESCE(SUM(vl_anulado), 0.00) AS vl_anulado 
        FROM         
            empenho.nota_liquidacao_item_anulado 
        WHERE 
            exercicio = ''' || stExercicio || ''' AND 
            cod_entidade IN (' || stEntidades || ') 
        GROUP BY 
            exercicio,
            cod_entidade,
            cod_nota,
            num_item,
            cod_pre_empenho,
            exercicio_item,
            timestamp 
        ) AS nlia ON 
            nli.exercicio = nlia.exercicio AND 
            nli.cod_nota = nlia.cod_nota AND 
            nli.cod_entidade = nlia.cod_entidade AND 
            nli.num_item = nlia.num_item AND 
            nli.cod_pre_empenho = nlia.cod_pre_empenho AND 
            nli.exercicio_item = nlia.exercicio_item 
            
    WHERE 
        e.exercicio = ''' || stExercicio || ''' AND 
        e.cod_entidade IN (' || stEntidades || ') AND
        TO_DATE(TO_CHAR(nlia.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'') BETWEEN TO_DATE(''' || stDtIniExercicio || ''', ''dd/mm/yyyy'') 
                                                                        AND TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') 
    GROUP BY
        pedcd.exercicio, 
        pedcd.cod_despesa, 
        pedcd.cod_subfuncao 
    )';
    
    EXECUTE stSQL;
    
    
    -- /*
    -- Despesas Estornadas durante o Bimestre 
    -- */
    
    -- stSQL := '
    -- CREATE TEMPORARY TABLE tmp_despesa_estornada_bimestre AS (
    -- SELECT 
    --     pedcd.exercicio, 
    --     pedcd.cod_despesa, 
    --     pedcd.cod_subfuncao, 
    --     COALESCE(SUM(nlia.vl_anulado), 0.00) AS vl_estornado 
    -- FROM 
    --     empenho.pre_empenho pe
        
    --     LEFT JOIN
        
    --     (SELECT 
    --         ped.exercicio, 
    --         ped.cod_pre_empenho, 
    --         d.cod_despesa, 
    --         d.cod_subfuncao 
    --     FROM 
    --         empenho.pre_empenho_despesa ped
    --         INNER JOIN 
    --         orcamento.despesa d ON 
    --             ped.exercicio   = d.exercicio AND
    --             ped.cod_despesa = d.cod_despesa
    --     WHERE 
    --         ped.exercicio = ''' || stExercicio || ''' 
    --     ) AS pedcd ON 
    --         pe.exercicio = pedcd.exercicio AND 
    --         pe.cod_pre_empenho = pedcd.cod_pre_empenho
            
    --     INNER JOIN
        
    --     empenho.empenho e ON 
    --         e.exercicio = pe.exercicio AND 
    --         e.cod_pre_empenho = pe.cod_pre_empenho
            
    --     INNER JOIN
        
    --     empenho.nota_liquidacao nl ON 
    --         nl.exercicio_empenho = e.exercicio AND 
    --         nl.cod_entidade = e.cod_entidade AND 
    --         nl.cod_empenho = e.cod_empenho
            
    --     INNER JOIN
        
    --     empenho.nota_liquidacao_item nli ON 
    --         nli.exercicio = nl.exercicio AND 
    --         nli.cod_entidade = nl.cod_entidade AND 
    --         nli.cod_nota = nl.cod_nota 
        
    --     INNER JOIN 
        
    --     (SELECT
    --         exercicio,
    --         cod_entidade,
    --         cod_nota,
    --         num_item,
    --         cod_pre_empenho,
    --         exercicio_item,
    --         timestamp, 
    --         COALESCE(SUM(vl_anulado), 0.00) AS vl_anulado
    --     FROM         
    --         empenho.nota_liquidacao_item_anulado 
    --     WHERE 
    --         exercicio = ''' || stExercicio || ''' AND 
    --         cod_entidade IN (' || stEntidades || ') 
    --     GROUP BY 
    --         exercicio,
    --         cod_entidade,
    --         cod_nota,
    --         num_item,
    --         cod_pre_empenho,
    --         exercicio_item,
    --         timestamp 
    --     ) AS nlia ON 
    --         nli.exercicio = nlia.exercicio AND 
    --         nli.cod_nota = nlia.cod_nota AND 
    --         nli.cod_entidade = nlia.cod_entidade AND 
    --         nli.num_item = nlia.num_item AND 
    --         nli.cod_pre_empenho = nlia.cod_pre_empenho AND 
    --         nli.exercicio_item = nlia.exercicio_item 
            
    -- WHERE 
    --     e.exercicio = ''' || stExercicio || ''' AND 
    --     e.cod_entidade IN (' || stEntidades || ') AND
    --     TO_DATE(TO_CHAR(nlia.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'') BETWEEN TO_DATE(''' || stDtIni || ''', ''dd/mm/yyyy'') 
    --                                                                     AND TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') 
    -- GROUP BY
    --     pedcd.exercicio, 
    --     pedcd.cod_despesa, 
    --     pedcd.cod_subfuncao 
    -- )';
    
    -- EXECUTE stSQL;

    

    -- --------------------------------------------
    -- Fim das Tabelas Temporarias
    -- --------------------------------------------

    -- --------------------------------------------
    -- FUNDEB
    -- Despesas do Fundeb
    -- --------------------------------------------
        
    IF (inOpcao = 1) THEN
        
        stSQL := '
        CREATE TEMPORARY TABLE tmp_rreo_retorno AS (
        SELECT * FROM (
            SELECT
                CAST(13 AS INTEGER) AS grupo, 
                CAST(0 AS INTEGER) AS nivel, 
                CAST(''Pagamento dos Profissionais do Magistério'' AS VARCHAR) AS descricao, 
                CAST(0.00 AS NUMERIC(14,2)) AS dot_ini, 
                CAST(0.00 AS NUMERIC(14,2)) AS dot_atu,                 
                CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
                CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
                CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,                
                CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
                CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
        ) tb1 
            
        UNION ALL 
            
        SELECT * FROM (
            SELECT
                CAST(13 AS INTEGER) AS grupo, 
                CAST(1 AS INTEGER) AS nivel, 
                CAST(''Com Ensino Infantil'' AS VARCHAR) AS descricao,
                COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini,
                (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu, 
                (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
                CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
                CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,                
                COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                        , ' || quote_literal(stExercicio) || '
                                                                        , ' || quote_literal(stEntidades) || '
                                                                        , ' || quote_literal(stDtIniExercicio) || '
                                                                        , ' || quote_literal(stDtFim)     || '
                                                                        , false )
                )), 0.00) AS vl_empenhado_ate_bimestre,
                '|| stSQLRestos ||'
            FROM 
                orcamento.subfuncao s 
                LEFT JOIN (
                SELECT
                    d.exercicio,
                    d.cod_despesa,
                    d.cod_subfuncao, 
                    cd.cod_estrutural 
                FROM 
                    stn.vinculo_recurso vr
                    INNER JOIN
                    orcamento.despesa d ON
                        d.exercicio = vr.exercicio AND 
                        d.cod_entidade = vr.cod_entidade AND 
                        d.cod_recurso = vr.cod_recurso AND 
                        d.num_orgao = vr.num_orgao AND 
                        d.num_unidade = vr.num_unidade 
                    INNER JOIN
                    orcamento.conta_despesa cd ON
                        cd.exercicio = d.exercicio AND
                        cd.cod_conta = d.cod_conta
                WHERE 
                    d.exercicio = ''' || stExercicio || ''' AND                     
                    vr.cod_vinculo = ' || inOpcao || ' AND
                    SUBSTRING(cd.cod_estrutural, 1, 3) = ''3.1'' AND
                    vr.cod_tipo = 1
                ) AS cdd ON 
                    cdd.exercicio = s.exercicio AND 
                    cdd.cod_subfuncao = s.cod_subfuncao
                
                -- suplementacoes e reduções de despesa
                
                LEFT JOIN 
                tmp_despesa_liberada tdl ON 
                    tdl.exercicio   = cdd.exercicio AND 
                    tdl.cod_despesa = cdd.cod_despesa 
                    
                -- liquidacao total no exercicio 
            
                LEFT JOIN
                
                tmp_despesa_liquidada_total tt ON 
                    tt.exercicio = cdd.exercicio and 
                    tt.cod_despesa = cdd.cod_despesa
                    
                LEFT JOIN
                
                tmp_despesa_estornada_total test_tot ON
                    test_tot.exercicio = cdd.exercicio AND
                    test_tot.cod_despesa = cdd.cod_despesa 
            
                    
            WHERE 
                -- educação infantil 
                s.cod_subfuncao IN (365)                 
            
            GROUP BY 
                s.cod_subfuncao, s.descricao ,cdd.cod_estrutural
            ORDER BY 
                s.descricao
            ) AS tb2b
            
        UNION ALL 
        
        SELECT * FROM (
            SELECT
                CAST(13 AS INTEGER) AS grupo, 
                CAST(2 AS INTEGER) AS nivel,                 
                CAST(''Com Ensino Fundamental'' AS VARCHAR) AS descricao, 
                COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini,
                (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,                 
                (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
                CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
                CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,
                COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                        , ' || quote_literal(stExercicio) || '
                                                                        , ' || quote_literal(stEntidades) || '
                                                                        , ' || quote_literal(stDtIniExercicio) || '
                                                                        , ' || quote_literal(stDtFim)     || '
                                                                        , false )
                )), 0.00) AS vl_empenhado_ate_bimestre,
                '|| stSQLRestos ||'
            FROM 
                orcamento.subfuncao s 
                LEFT JOIN (
                SELECT
                    d.exercicio,
                    d.cod_despesa,
                    d.cod_subfuncao, 
                    cd.cod_estrutural 
                FROM 
                    stn.vinculo_recurso vr
                    INNER JOIN
                    orcamento.despesa d ON
                        d.exercicio = vr.exercicio AND 
                        d.cod_entidade = vr.cod_entidade AND 
                        d.cod_recurso = vr.cod_recurso AND 
                        d.num_orgao = vr.num_orgao AND 
                        d.num_unidade = vr.num_unidade 
                    INNER JOIN
                    orcamento.conta_despesa cd ON
                        cd.exercicio = d.exercicio AND
                        cd.cod_conta = d.cod_conta
                WHERE 
                    d.exercicio = ''' || stExercicio || ''' AND                     
                    vr.cod_vinculo = ' || inOpcao || ' AND
                    SUBSTRING(cd.cod_estrutural, 1, 3) = ''3.1'' AND
                    vr.cod_tipo = 1
                ) AS cdd ON 
                    cdd.exercicio = s.exercicio AND 
                    cdd.cod_subfuncao = s.cod_subfuncao
                
                -- suplementacoes e reduções de despesa
                
                LEFT JOIN 
                tmp_despesa_liberada tdl ON 
                    tdl.exercicio   = cdd.exercicio AND 
                    tdl.cod_despesa = cdd.cod_despesa 
                    
                -- liquidacao total no exercicio 
            
                LEFT JOIN
                
                tmp_despesa_liquidada_total tt ON 
                    tt.exercicio = cdd.exercicio and 
                    tt.cod_despesa = cdd.cod_despesa
                    
                LEFT JOIN
                
                tmp_despesa_estornada_total test_tot ON
                    test_tot.exercicio = cdd.exercicio AND
                    test_tot.cod_despesa = cdd.cod_despesa 
            
                    
            WHERE 
                -- ensino fundamental , educação de jovens e adultos, educação especial
                s.cod_subfuncao IN (361, 366, 367)                 
            
            GROUP BY 
                cdd.cod_estrutural
            --ORDER BY 
            --    s.descricao
            ) AS tb2             
            
        UNION ALL
        
        SELECT * FROM (
            SELECT
                CAST(14 AS INTEGER) AS grupo, 
                CAST(0 AS INTEGER) AS nivel, 
                CAST(''Outras Despesas'' AS VARCHAR) AS descricao, 
                CAST(0.00 AS NUMERIC(14,2)) AS dot_ini, 
                CAST(0.00 AS NUMERIC(14,2)) AS dot_atu,                 
                CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
                CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
                CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,                
                CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
                CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
        ) tb3 
            
        UNION ALL 
        
        SELECT * FROM (
            SELECT
                CAST(14 AS INTEGER) AS grupo, 
                CAST(1 AS INTEGER) AS nivel, 
                --initcap(TRIM(s.descricao)) AS descricao, 
                CAST(''Com Ensino Infantil'' AS VARCHAR) AS descricao,
                COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini, 
                (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,                 
                (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
                CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
                CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,                
                COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                        , ' || quote_literal(stExercicio) || '
                                                                        , ' || quote_literal(stEntidades) || '
                                                                        , ' || quote_literal(stDtIniExercicio) || '
                                                                        , ' || quote_literal(stDtFim)     || '
                                                                        , false )
                )), 0.00) AS vl_empenhado_ate_bimestre,
                '|| stSQLRestos ||'
            FROM 
                orcamento.subfuncao s 
                LEFT JOIN (
                SELECT
                    d.exercicio,
                    d.cod_despesa,
                    d.cod_subfuncao, 
                    cd.cod_estrutural 
                FROM 
                    stn.vinculo_recurso vr
                    INNER JOIN
                    orcamento.despesa d ON
                        d.exercicio = vr.exercicio AND 
                        d.cod_entidade = vr.cod_entidade AND 
                        d.cod_recurso = vr.cod_recurso AND 
                        d.num_orgao = vr.num_orgao AND 
                        d.num_unidade = vr.num_unidade 
                    INNER JOIN
                    orcamento.conta_despesa cd ON
                        cd.exercicio = d.exercicio AND
                        cd.cod_conta = d.cod_conta
                WHERE
                    d.exercicio = ''' || stExercicio || ''' AND 
                    vr.cod_vinculo = ' || inOpcao || ' AND
                    --SUBSTRING(cd.cod_estrutural, 1, 3) <> ''3.1'' AND
                    vr.cod_tipo = 2
                ) AS cdd ON 
                    cdd.exercicio = s.exercicio AND 
                    cdd.cod_subfuncao = s.cod_subfuncao
                
                -- suplementacoes e reduções de despesa
                
                LEFT JOIN 
                tmp_despesa_liberada tdl ON 
                    tdl.exercicio   = cdd.exercicio AND 
                    tdl.cod_despesa = cdd.cod_despesa 
                    
                -- liquidacao total no exercicio 
            
                LEFT JOIN
                
                tmp_despesa_liquidada_total tt ON 
                    tt.exercicio = cdd.exercicio and 
                    tt.cod_despesa = cdd.cod_despesa
                    
                LEFT JOIN
                
                tmp_despesa_estornada_total test_tot ON
                    test_tot.exercicio = cdd.exercicio AND
                    test_tot.cod_despesa = cdd.cod_despesa 
                    
            WHERE 
                -- educação infantil 
                s.cod_subfuncao IN (365) 
                
            GROUP BY 
                s.cod_subfuncao, s.descricao , cdd.cod_estrutural
            ORDER BY 
                s.descricao
            ) AS tb4
            
        UNION ALL 
        
        SELECT * FROM (
            SELECT
                CAST(14 AS INTEGER) AS grupo, 
                CAST(2 AS INTEGER) AS nivel, 
                CAST(''Com Ensino Fundamental'' AS VARCHAR) AS descricao, 
                COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini, 
                (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,                 
                (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
                CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
                CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,                
                COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                        , ' || quote_literal(stExercicio) || '
                                                                        , ' || quote_literal(stEntidades) || '
                                                                        , ' || quote_literal(stDtIniExercicio) || '
                                                                        , ' || quote_literal(stDtFim)     || '
                                                                        , false )
                )), 0.00) AS vl_empenhado_ate_bimestre,
                '|| stSQLRestos ||'
            FROM 
                orcamento.subfuncao s 
                LEFT JOIN (
                SELECT
                    d.exercicio,
                    d.cod_despesa,
                    d.cod_subfuncao, 
                    cd.cod_estrutural 
                FROM 
                    stn.vinculo_recurso vr
                    INNER JOIN
                    orcamento.despesa d ON
                        d.exercicio = vr.exercicio AND 
                        d.cod_entidade = vr.cod_entidade AND 
                        d.cod_recurso = vr.cod_recurso AND 
                        d.num_orgao = vr.num_orgao AND 
                        d.num_unidade = vr.num_unidade 
                    INNER JOIN
                    orcamento.conta_despesa cd ON
                        cd.exercicio = d.exercicio AND
                        cd.cod_conta = d.cod_conta
                WHERE
                    d.exercicio = ''' || stExercicio || ''' AND 
                    vr.cod_vinculo = ' || inOpcao || ' AND
                    --SUBSTRING(cd.cod_estrutural, 1, 3) <> ''3.1'' AND
                    vr.cod_tipo = 2
                ) AS cdd ON 
                    cdd.exercicio = s.exercicio AND 
                    cdd.cod_subfuncao = s.cod_subfuncao
                
                -- suplementacoes e reduções de despesa
                
                LEFT JOIN 
                tmp_despesa_liberada tdl ON 
                    tdl.exercicio   = cdd.exercicio AND 
                    tdl.cod_despesa = cdd.cod_despesa 
                    
                -- liquidacao total no exercicio 
            
                LEFT JOIN
                
                tmp_despesa_liquidada_total tt ON 
                    tt.exercicio = cdd.exercicio and 
                    tt.cod_despesa = cdd.cod_despesa
                    
                LEFT JOIN
                
                tmp_despesa_estornada_total test_tot ON
                    test_tot.exercicio = cdd.exercicio AND
                    test_tot.cod_despesa = cdd.cod_despesa 
          
                    
            WHERE 
                -- educação infantil, educação de jovens e adultos, educação especial
                s.cod_subfuncao IN (361, 366, 367) 
                
            GROUP BY 
                cdd.cod_estrutural
            --ORDER BY 
            --    s.descricao
            ) AS tb4b 
            
        )';


    -- --------------------------------------------
    -- MDE
    -- Despesas com Ações Típicas de Manutenção
    -- e Desenvolvimento do Ensino
    -- --------------------------------------------
    
    
    ELSEIF (inOpcao = 2) THEN 
        
        stSQL := '
        CREATE TEMPORARY TABLE tmp_rreo_retorno AS (
        
        -- Educacao Infantil (cod_subfuncao = 365)
        
        SELECT * FROM (
        SELECT 
            CAST(23 AS INTEGER) AS grupo, 
            CAST(0 AS INTEGER) AS nivel, 
            --s.cod_subfuncao, 
            CAST(''EDUCAÇÃO INFANTIL'' AS VARCHAR) AS descricao, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_ini, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_atu,             
            CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
            CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
        ) AS tbl
        
        UNION ALL 
        
        SELECT * FROM (
        
        SELECT 
            CAST(23 AS INTEGER) AS grupo, 
            CAST(1 AS INTEGER) AS nivel, 
            CAST(''Despesas Custeadas com Recursos do FUNDEB'' AS VARCHAR) AS descricao, 
            COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini, 
            (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,             
            (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                    , ' || quote_literal(stExercicio) || '
                                                                    , ' || quote_literal(stEntidades) || '
                                                                    , ' || quote_literal(stDtIniExercicio) || '
                                                                    , ' || quote_literal(stDtFim)     || '
                                                                    , false )
            )), 0.00) AS vl_empenhado_ate_bimestre,
            '|| stSQLRestos ||'

        FROM 
            orcamento.subfuncao s 
            LEFT JOIN 
            (SELECT 
                d.exercicio, 
                d.cod_despesa, 
                d.cod_subfuncao, 
                d.cod_funcao, 
                d.vl_original, 
                cd.cod_estrutural 
            FROM 
                stn.vinculo_recurso vr 
                INNER JOIN 
                orcamento.despesa d ON 
                    d.exercicio = vr.exercicio AND 
                    d.cod_entidade = vr.cod_entidade AND 
                    d.cod_recurso = vr.cod_recurso AND 
                    d.num_orgao = vr.num_orgao AND 
                    d.num_unidade = vr.num_unidade 
                INNER JOIN 
                orcamento.conta_despesa cd ON 
                    cd.exercicio = d.exercicio AND 
                    cd.cod_conta = d.cod_conta 
            WHERE 
                d.exercicio = ''' || stExercicio || ''' AND 
                vr.cod_vinculo = 1 AND
                d.cod_funcao = 12 AND-- apenas Função Educação (12)
                vr.cod_tipo = 2
            ) AS cdd ON 
                cdd.exercicio = s.exercicio AND 
                cdd.cod_subfuncao = s.cod_subfuncao 
            
            -- suplementacoes e reduções de despesa
            
            LEFT JOIN 
            tmp_despesa_liberada tdl ON 
                tdl.exercicio   = cdd.exercicio AND 
                tdl.cod_despesa = cdd.cod_despesa 
                
            -- liquidacao total no exercicio 
            
            LEFT JOIN 
            
            tmp_despesa_liquidada_total tt ON 
                tt.exercicio = cdd.exercicio AND 
                tt.cod_despesa = cdd.cod_despesa
                
            LEFT JOIN
            
            tmp_despesa_estornada_total test_tot ON
                test_tot.exercicio = cdd.exercicio AND
                test_tot.cod_despesa = cdd.cod_despesa 
           
        
        WHERE
            -- Educacao Infantil
            s.cod_subfuncao = 365 
            
        GROUP BY 
            s.cod_subfuncao, s.descricao, cdd.cod_estrutural
            
        ORDER BY 
            grupo, nivel, s.descricao
        
        ) AS tbl2
        
        UNION ALL
        
        SELECT * FROM (
        
        SELECT 
            CAST(23 AS INTEGER) AS grupo, 
            CAST(2 AS INTEGER) AS nivel, 
            CAST(''Despesas Custeadas com Outros Recursos de Impostos'' AS VARCHAR) AS descricao, 
            COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini, 
            (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,             
            (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                    , ' || quote_literal(stExercicio) || '
                                                                    , ' || quote_literal(stEntidades) || '
                                                                    , ' || quote_literal(stDtIniExercicio) || '
                                                                    , ' || quote_literal(stDtFim)     || '
                                                                    , false )
            )), 0.00) AS vl_empenhado_ate_bimestre,
            '|| stSQLRestos ||'
        FROM 
            orcamento.subfuncao s
            
            -- Vinculo com Recursos configurados no STN
            
            LEFT JOIN
            
            (SELECT 
                d.exercicio, 
                d.cod_despesa, 
                d.cod_subfuncao, 
                d.cod_funcao, 
                cd.cod_estrutural 
            FROM 
                orcamento.despesa d 
                INNER JOIN 
                orcamento.conta_despesa cd ON 
                    cd.exercicio = d.exercicio AND 
                    cd.cod_conta = d.cod_conta 
            WHERE 
                d.exercicio = ''' || stExercicio || ''' AND 
                -- apenas Função Educação (12) 
                d.cod_funcao = 12 AND 
                -- Exclui Salario Educacao, Operacoes de Credito e Outros Recursos MDE
                d.cod_recurso NOT IN    (   SELECT cod_recurso 
                                                            FROM stn.vinculo_recurso 
                                                          WHERE exercicio = ''' || stExercicio || ''' 
                                                               AND cod_vinculo NOT IN ( SELECT cod_vinculo 
                                                                                                          FROM stn.vinculo_recurso 
                                                                                                        WHERE exercicio = ''' || stExercicio || ''' 
                                                                                                             AND cod_vinculo<> 1) 
                                                               AND cod_tipo NOT IN (SELECT cod_vinculo 
                                                                                                    FROM stn.vinculo_recurso 
                                                                                                  WHERE exercicio = ''' || stExercicio || '''  
                                                                                                      AND cod_tipo <>2)  

                                                        ) -- Exclui MDE
            ) AS cdd ON 
                cdd.exercicio = s.exercicio AND 
                cdd.cod_subfuncao = s.cod_subfuncao 
            
            -- suplementacoes e reduções de despesa
            
            LEFT JOIN 
            tmp_despesa_liberada tdl ON 
                tdl.exercicio   = cdd.exercicio AND 
                tdl.cod_despesa = cdd.cod_despesa  
                
            -- liquidacao total no exercicio 
            
            LEFT JOIN
            
            tmp_despesa_liquidada_total tt ON 
                tt.exercicio = cdd.exercicio AND 
                tt.cod_despesa = cdd.cod_despesa
                
            LEFT JOIN
            
            tmp_despesa_estornada_total test_tot ON
                test_tot.exercicio = cdd.exercicio AND
                test_tot.cod_despesa = cdd.cod_despesa 
       
                
        WHERE
            -- Educacao Infantil
            s.cod_subfuncao = 365 
            
        GROUP BY 
            s.cod_subfuncao, s.descricao, cdd.cod_estrutural
            
        ORDER BY 
            grupo, nivel, s.descricao
        
        ) AS tbl3
        
        UNION ALL 
        
        SELECT * FROM (
        SELECT 
            CAST(24 AS INTEGER) AS grupo, 
            CAST(0 AS INTEGER) AS nivel, 
            --s.cod_subfuncao, 
            CAST(''ENSINO FUNDAMENTAL'' AS VARCHAR) AS descricao, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_ini, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_atu,             
            CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
            CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
        ) AS tbl4
        
        UNION ALL 
        
        SELECT * FROM (
        
        SELECT 
            CAST(24 AS INTEGER) AS grupo, 
            CAST(1 AS INTEGER) AS nivel, 
            CAST(''Despesas Custeadas com Recursos do FUNDEB'' AS VARCHAR) AS descricao, 
            COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini, 
            (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,             
            (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                    , ' || quote_literal(stExercicio) || '
                                                                    , ' || quote_literal(stEntidades) || '
                                                                    , ' || quote_literal(stDtIniExercicio) || '
                                                                    , ' || quote_literal(stDtFim)     || '
                                                                    , false )
            )), 0.00) AS vl_empenhado_ate_bimestre,
            '|| stSQLRestos ||'
        FROM 
            orcamento.subfuncao s
            LEFT JOIN 
            (SELECT 
                d.exercicio, 
                d.cod_despesa,
                d.cod_funcao, 
                d.cod_subfuncao, 
                cd.cod_estrutural 
            FROM 
                stn.vinculo_recurso vr 
                INNER JOIN 
                orcamento.despesa d ON 
                    d.exercicio = vr.exercicio AND 
                    d.cod_entidade = vr.cod_entidade AND 
                    d.cod_recurso = vr.cod_recurso AND 
                    d.num_orgao = vr.num_orgao AND 
                    d.num_unidade = vr.num_unidade 
                INNER JOIN 
                orcamento.conta_despesa cd ON 
                    cd.exercicio = d.exercicio AND 
                    cd.cod_conta = d.cod_conta 
            WHERE 
                d.exercicio = ''' || stExercicio || ''' AND 
                vr.cod_vinculo = 1 AND
                d.cod_funcao = 12 AND-- apenas Função Educação (12) 
        vr.cod_tipo = 2
            ) AS cdd ON 
                cdd.exercicio = s.exercicio AND 
                cdd.cod_subfuncao = s.cod_subfuncao 
           
            -- suplementacoes e reduções de despesa
            
            LEFT JOIN 
            tmp_despesa_liberada tdl ON 
                tdl.exercicio   = cdd.exercicio AND 
                tdl.cod_despesa = cdd.cod_despesa 
                
            -- liquidacao total no exercicio 
            
            LEFT JOIN
            
            tmp_despesa_liquidada_total tt ON 
                tt.exercicio = cdd.exercicio AND 
                tt.cod_despesa = cdd.cod_despesa
                
            LEFT JOIN
            
            tmp_despesa_estornada_total test_tot ON
                test_tot.exercicio = cdd.exercicio AND
                test_tot.cod_despesa = cdd.cod_despesa 

                
        WHERE
            -- Ensino Fundamental, Educação de Jovens e Adultos, Educação Especial
            s.cod_subfuncao IN ( 361, 366, 367)
            
        GROUP BY 
            s.descricao, cdd.cod_estrutural
        
        --ORDER BY 
        --    grupo, nivel, s.descricao
        
        ) AS tbl5
        
        UNION ALL
        
        SELECT * FROM (
        
        SELECT 
            CAST(24 AS INTEGER) AS grupo, 
            CAST(2 AS INTEGER) AS nivel, 
            CAST(''Despesas Custeadas com Outros Recursos de Impostos'' AS VARCHAR) AS descricao, 
            COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini, 
            (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,             
            (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                    , ' || quote_literal(stExercicio) || '
                                                                    , ' || quote_literal(stEntidades) || '
                                                                    , ' || quote_literal(stDtIniExercicio) || '
                                                                    , ' || quote_literal(stDtFim)     || '
                                                                    , false )
            )), 0.00) AS vl_empenhado_ate_bimestre,
            '|| stSQLRestos ||'
        FROM 
            orcamento.subfuncao s

            -- Vinculo com Recursos configurados no STN
            
            LEFT JOIN 
            (SELECT 
                d.exercicio, 
                d.cod_despesa, 
                d.cod_subfuncao, 
                d.cod_funcao, 
                cd.cod_estrutural 
            FROM 
                orcamento.despesa d 
                INNER JOIN 
                orcamento.conta_despesa cd ON 
                    cd.exercicio = d.exercicio AND 
                    cd.cod_conta = d.cod_conta 
            WHERE
                d.exercicio = ''' || stExercicio || ''' AND 
                -- apenas Função Educação (12) 
                d.cod_funcao = 12 AND 
                -- Exclui Salario Educacao, Operacoes de Credito e Outros Recursos MDE
                d.cod_recurso NOT IN    (SELECT
                                           cod_recurso
                                        FROM
                                            stn.vinculo_recurso
                                        WHERE
                                            exercicio = ''' || stExercicio || ''' AND
                                            cod_vinculo = 1 and cod_tipo = 2) -- Exclui MDE
                AND d.cod_entidade IN (SELECT
                                           cod_entidade
                                        FROM
                                            stn.vinculo_recurso
                                        WHERE
                                            exercicio = ''' || stExercicio || ''' AND
                                            cod_vinculo = 2) -- Exclui MDE
            ) AS cdd ON 
                cdd.exercicio = s.exercicio AND 
                cdd.cod_subfuncao = s.cod_subfuncao 
            
            -- suplementacoes e reduções de despesa
            
            LEFT JOIN 
            tmp_despesa_liberada tdl ON 
                tdl.exercicio   = cdd.exercicio AND 
                tdl.cod_despesa = cdd.cod_despesa 
                
            -- liquidacao total no exercicio 
            
            LEFT JOIN
            
            tmp_despesa_liquidada_total tt ON 
                tt.exercicio = cdd.exercicio AND 
                tt.cod_despesa = cdd.cod_despesa
                
            LEFT JOIN
            
            tmp_despesa_estornada_total test_tot ON
                test_tot.exercicio = cdd.exercicio AND
                test_tot.cod_despesa = cdd.cod_despesa 
            
                
        WHERE
            -- Ensino Fundamental, Educação de Jovens e Adultos, Educação Especial
            s.cod_subfuncao IN ( 361, 366, 367 )
            
        GROUP BY 
            s.descricao, cdd.cod_estrutural
            
        --ORDER BY 
        --    grupo, nivel, s.descricao 
        ) AS tbl6
        
        UNION ALL 
        
        SELECT * FROM (
        SELECT 
            CAST(25 AS INTEGER) AS grupo, 
            CAST(0 AS INTEGER) AS nivel, 
            CAST(''ENSINO MÉDIO'' AS VARCHAR) AS descricao, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_ini, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_atu,             
            CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
            CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
        ) AS tbl7
        
        UNION ALL
        
        -- Despesas com Ensino Medio
        
        SELECT * FROM (
        
        SELECT 
            CAST(25 AS INTEGER) AS grupo, 
            CAST(1 AS INTEGER) AS nivel, 
            CAST(''Despesas com Ensino Médio'' AS VARCHAR) AS descricao, 
            COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini, 
            (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,             
            (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                    , ' || quote_literal(stExercicio) || '
                                                                    , ' || quote_literal(stEntidades) || '
                                                                    , ' || quote_literal(stDtIniExercicio) || '
                                                                    , ' || quote_literal(stDtFim)     || '
                                                                    , false )
            )), 0.00) AS vl_empenhado_ate_bimestre,
            '|| stSQLRestos ||'
        FROM 
            orcamento.subfuncao s

            -- Vinculo com Recursos configurados no STN
            
            LEFT JOIN 
            (SELECT 
                d.exercicio, 
                d.cod_despesa, 
                d.cod_subfuncao, 
                d.cod_funcao, 
                cd.cod_estrutural 
            FROM 
                orcamento.despesa d 
                INNER JOIN 
                orcamento.conta_despesa cd ON 
                    cd.exercicio = d.exercicio AND 
                    cd.cod_conta = d.cod_conta 
            WHERE 
                d.exercicio = ''' || stExercicio || ''' AND 
                -- apenas Função Educação (12) 
                d.cod_funcao = 12 AND 
                -- Exclui Salario Educacao, Operacoes de Credito e Outros Recursos MDE
                d.cod_recurso NOT IN    (SELECT
                                           cod_recurso
                                        FROM
                                            stn.vinculo_recurso
                                        WHERE
                                            exercicio = ''' || stExercicio || ''' AND
                                            cod_vinculo <> 2) -- Exclui MDE
            ) AS cdd ON 
                cdd.exercicio = s.exercicio AND 
                cdd.cod_subfuncao = s.cod_subfuncao 
            
            -- suplementacoes e reduções de despesa
            
            LEFT JOIN 
            tmp_despesa_liberada tdl ON 
                tdl.exercicio   = cdd.exercicio AND 
                tdl.cod_despesa = cdd.cod_despesa 
                
            -- liquidacao total no exercicio 
            
            LEFT JOIN
            
            tmp_despesa_liquidada_total tt ON 
                tt.exercicio = cdd.exercicio AND 
                tt.cod_despesa = cdd.cod_despesa
                
            LEFT JOIN
            
            tmp_despesa_estornada_total test_tot ON
                test_tot.exercicio = cdd.exercicio AND
                test_tot.cod_despesa = cdd.cod_despesa 
                         
            
        WHERE
            -- Ensino Medio (pode ser somado a Educacao de Jovens e Adultos)
            (
            s.cod_subfuncao = 362 -- OR    -- Medio 
            --s.cod_subfuncao = 366       -- Jovens e Adultos
            )
            
        GROUP BY 
            s.cod_subfuncao, s.descricao, cdd.cod_estrutural
            
        ORDER BY 
            grupo, nivel, s.descricao 
        ) AS tbl8
        
        UNION ALL 
        
        SELECT * FROM (
        SELECT 
            CAST(26 AS INTEGER) AS grupo, 
            CAST(0 AS INTEGER) AS nivel, 
            CAST(''ENSINO SUPERIOR'' AS VARCHAR) AS descricao, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_ini, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_atu,             
            CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
            CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
        ) AS tbl9 
        
        UNION ALL
        
        -- Despesas com Ensino Superior
        
        SELECT * FROM (
        
        SELECT 
            CAST(26 AS INTEGER) AS grupo, 
            CAST(1 AS INTEGER) AS nivel, 
            CAST(''Despesas com Ensino Superior'' AS VARCHAR) AS descricao, 
            COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini, 
            (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,             
            (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                    , ' || quote_literal(stExercicio) || '
                                                                    , ' || quote_literal(stEntidades) || '
                                                                    , ' || quote_literal(stDtIniExercicio) || '
                                                                    , ' || quote_literal(stDtFim)     || '
                                                                    , false )
            )), 0.00) AS vl_empenhado_ate_bimestre,
            '|| stSQLRestos ||'
        FROM 
            orcamento.subfuncao s

            -- Vinculo com Recursos configurados no STN
            
            LEFT JOIN 
            (SELECT 
                d.exercicio, 
                d.cod_despesa, 
                d.cod_subfuncao, 
                d.cod_funcao, 
                cd.cod_estrutural 
            FROM 
                orcamento.despesa d 
                INNER JOIN 
                orcamento.conta_despesa cd ON 
                    cd.exercicio = d.exercicio AND 
                    cd.cod_conta = d.cod_conta 
            WHERE 
                d.exercicio = ''' || stExercicio || ''' AND 
                -- apenas Função Educação (12) 
                d.cod_funcao = 12 
            ) AS cdd ON 
                cdd.exercicio = s.exercicio AND 
                cdd.cod_subfuncao = s.cod_subfuncao 
            
            -- suplementacoes e reduções de despesa
            
            LEFT JOIN 
            tmp_despesa_liberada tdl ON 
                tdl.exercicio   = cdd.exercicio AND 
                tdl.cod_despesa = cdd.cod_despesa 
                
            -- liquidacao total no exercicio 
            
            LEFT JOIN
            
            tmp_despesa_liquidada_total tt ON 
                tt.exercicio = cdd.exercicio AND 
                tt.cod_despesa = cdd.cod_despesa
                
            LEFT JOIN
            
            tmp_despesa_estornada_total test_tot ON
                test_tot.exercicio = cdd.exercicio AND
                test_tot.cod_despesa = cdd.cod_despesa 
                
        WHERE
            -- Ensino Medio 
            s.cod_subfuncao = 364 
            
        GROUP BY 
            s.cod_subfuncao, s.descricao, cdd.cod_estrutural
            
        ORDER BY 
            grupo, nivel, s.descricao 
        ) AS tbl10
        
        UNION ALL 
        
        SELECT * FROM (
        SELECT 
            CAST(27 AS INTEGER) AS grupo, 
            CAST(0 AS INTEGER) AS nivel, 
            CAST(''ENSINO PROFISSIONAL NÃO INTEGRADO AO ENSINO REGULAR'' AS VARCHAR) AS descricao, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_ini, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_atu,             
            CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
            CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
        ) AS tbl11 
        
        UNION ALL
        
        -- Despesas com Ensino Superior
        
        SELECT * FROM (
        
        SELECT 
            CAST(27 AS INTEGER) AS grupo, 
            CAST(1 AS INTEGER) AS nivel, 
            CAST(''Despesas com Ensino Profissional'' AS VARCHAR) AS descricao, 
            COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini, 
            (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,             
            (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                    , ' || quote_literal(stExercicio) || '
                                                                    , ' || quote_literal(stEntidades) || '
                                                                    , ' || quote_literal(stDtIniExercicio) || '
                                                                    , ' || quote_literal(stDtFim)     || '
                                                                    , false )
            )), 0.00) AS vl_empenhado_ate_bimestre,
            '|| stSQLRestos ||'
        FROM 
            orcamento.subfuncao s

            -- Vinculo com Recursos configurados no STN
            
            LEFT JOIN 
            (SELECT 
                d.exercicio, 
                d.cod_despesa, 
                d.cod_subfuncao, 
                d.cod_funcao, 
                cd.cod_estrutural 
            FROM 
                orcamento.despesa d 
                INNER JOIN 
                orcamento.conta_despesa cd ON 
                    cd.exercicio = d.exercicio AND 
                    cd.cod_conta = d.cod_conta 
            WHERE 
                d.exercicio = ''' || stExercicio || ''' AND 
                -- apenas Função Educação (12) 
                d.cod_funcao = 12 
            ) AS cdd ON 
                cdd.exercicio = s.exercicio AND 
                cdd.cod_subfuncao = s.cod_subfuncao 
            
            -- suplementacoes e reduções de despesa
            
            LEFT JOIN 
            tmp_despesa_liberada tdl ON 
                tdl.exercicio   = cdd.exercicio AND 
                tdl.cod_despesa = cdd.cod_despesa 
                
            -- liquidacao total no exercicio 
            
            LEFT JOIN
            
            tmp_despesa_liquidada_total tt ON 
                tt.exercicio = cdd.exercicio AND 
                tt.cod_despesa = cdd.cod_despesa
                
            LEFT JOIN
            
            tmp_despesa_estornada_total test_tot ON
                test_tot.exercicio = cdd.exercicio AND
                test_tot.cod_despesa = cdd.cod_despesa 
                            
        WHERE
            -- Ensino Profissional 
            s.cod_subfuncao = 363 
            
        GROUP BY 
            s.cod_subfuncao, s.descricao, cdd.cod_estrutural
            
        ORDER BY 
            grupo, nivel, s.descricao 
        ) AS tbl12
        
        UNION ALL 
        
        SELECT * FROM (
        SELECT 
            CAST(28 AS INTEGER) AS grupo, 
            CAST(0 AS INTEGER) AS nivel, 
            CAST(''OUTRAS'' AS VARCHAR) AS descricao, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_ini, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_atu,             
            CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
            CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
        ) AS tbl13 
        
        UNION ALL
        
        -- Outras Despesas - Exclui as subfunções 361, 362, 363, 364, 365, 366
        
        SELECT * FROM (
        
        SELECT 
            CAST(28 AS INTEGER) AS grupo, 
            CAST(1 AS INTEGER) AS nivel, 
            CAST(''Outras Despesas'' AS VARCHAR) AS descricao, 
            COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini, 
            (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,             
            (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                    , ' || quote_literal(stExercicio) || '
                                                                    , ' || quote_literal(stEntidades) || '
                                                                    , ' || quote_literal(stDtIniExercicio) || '
                                                                    , ' || quote_literal(stDtFim)     || '
                                                                    , false )
            )), 0.00) AS vl_empenhado_ate_bimestre,
            '|| stSQLRestos ||'
        FROM 
            orcamento.subfuncao s

            -- Vinculo com Recursos configurados no STN
            
            LEFT JOIN 
            (SELECT 
                d.exercicio, 
                d.cod_despesa, 
                d.cod_subfuncao, 
                d.cod_funcao, 
                cd.cod_estrutural 
            FROM 
                orcamento.despesa d 
                INNER JOIN 
                orcamento.conta_despesa cd ON 
                    cd.exercicio = d.exercicio AND 
                    cd.cod_conta = d.cod_conta 
            WHERE 
                d.exercicio = ''' || stExercicio || ''' AND 
                -- apenas Função Educação (12) 
                d.cod_funcao = 12 
            ) AS cdd ON 
                cdd.exercicio = s.exercicio AND 
                cdd.cod_subfuncao = s.cod_subfuncao 
            
            -- suplementacoes e reduções de despesa
            
            LEFT JOIN 
            tmp_despesa_liberada tdl ON 
                tdl.exercicio   = cdd.exercicio AND 
                tdl.cod_despesa = cdd.cod_despesa 
                
            -- liquidacao total no exercicio 
            
            LEFT JOIN
            
            tmp_despesa_liquidada_total tt ON 
                tt.exercicio = cdd.exercicio AND 
                tt.cod_despesa = cdd.cod_despesa
                
            LEFT JOIN
            
            tmp_despesa_estornada_total test_tot ON
                test_tot.exercicio = cdd.exercicio AND
                test_tot.cod_despesa = cdd.cod_despesa 
                
        WHERE
            -- Outras Despesas - Exclui Subfunções: 361, 362, 363, 364, 365, 366.
            -- Retirada também a subfunção 367
            s.cod_subfuncao NOT IN ( 361, 362, 363, 364, 365, 366, 367 ) 
        GROUP BY 
            grupo , cdd.cod_estrutural
        ) AS tbl14 

    )';
    
    -- --------------------------------------------
    -- OUTRAS DESPESAS
    -- Outras Despesas com Recursos 
    -- Destinados a MDE
    -- --------------------------------------------    
    
    ELSEIF (inOpcao = 3) THEN

        stSQL := '        
        CREATE TEMPORARY TABLE tmp_rreo_retorno AS (        
        SELECT * FROM (
        SELECT
            CAST(40 AS INTEGER) AS grupo,  
            CAST(0 AS INTEGER) AS nivel,
            CAST(''DESPESAS CUSTEADAS COM A APLICAÇÃO FINANCEIRA DE OUTROS RECURSOS DE IMPOSTOS VINCULADOS AO ENSINO'' AS VARCHAR) AS descricao,
            CAST(0.00 AS NUMERIC(14,2)) AS dot_ini, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_atu,             
            CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
            CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
        ) AS tbl1

        UNION ALL

        SELECT * FROM (
        SELECT 
            CAST(41 AS INTEGER) AS grupo, 
            CAST(0 AS INTEGER) AS nivel, 
            CAST(''DESPESAS CUSTEADAS COM A CONTRIBUIÇÃO SOCIAL DO SALÁRIO-EDUCAÇÃO'' AS VARCHAR) AS descricao, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_ini, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_atu,
            CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
            CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
        ) AS tbl1 
        
        UNION ALL
        
        -- Contribuição Social do Salário Educação com Recursos do MDE
        
        SELECT * FROM (
        
        SELECT 
            CAST(41 AS INTEGER) AS grupo, 
            CAST(1 AS INTEGER) AS nivel, 
            CAST(''Contribuição Social do Salário Educação'' AS VARCHAR) AS descricao, 
            COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini, 
            (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,             
            (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                    , ' || quote_literal(stExercicio) || '
                                                                    , ' || quote_literal(stEntidades) || '
                                                                    , ' || quote_literal(stDtIniExercicio) || '
                                                                    , ' || quote_literal(stDtFim)     || '
                                                                    , false )
            )), 0.00) AS vl_empenhado_ate_bimestre,
            '|| stSQLRestos ||' 
        FROM 
            orcamento.subfuncao s
            
            -- Vinculo com Recursos Configurados no STN
            
            JOIN 
            (SELECT 
                d.exercicio, 
                d.cod_despesa,
                d.cod_funcao, 
                d.cod_subfuncao, 
                d.cod_conta,
                cd.cod_estrutural 
            FROM 
                stn.vinculo_recurso vr
                    INNER JOIN
                    orcamento.despesa d ON
                        d.exercicio = vr.exercicio AND 
                        d.cod_entidade = vr.cod_entidade AND 
                        d.cod_recurso = vr.cod_recurso AND 
                        d.num_orgao = vr.num_orgao AND 
                        d.num_unidade = vr.num_unidade 
                    INNER JOIN
                    orcamento.conta_despesa cd ON
                        cd.exercicio = d.exercicio AND
                        cd.cod_conta = d.cod_conta
            WHERE
                d.exercicio = ''' || stExercicio || ''' AND 
                d.cod_entidade IN (' || stEntidades || ') AND 
                -- Educacao 
                d.cod_funcao = 12 AND
                -- Salario Educacao em stn.vinculo_stn_recurso
                vr.cod_vinculo = 3 
            ) AS cdd ON 
                cdd.exercicio = s.exercicio AND 
                cdd.cod_subfuncao = s.cod_subfuncao
            
            -- suplementacoes e reduções de despesa
            
            LEFT JOIN 
            tmp_despesa_liberada tdl ON 
                tdl.exercicio   = cdd.exercicio AND 
                tdl.cod_despesa = cdd.cod_despesa 
                
            -- liquidacao total no exercicio 
            
            LEFT JOIN
            
            tmp_despesa_liquidada_total tt ON 
                tt.exercicio = cdd.exercicio AND 
                tt.cod_despesa = cdd.cod_despesa
                
            LEFT JOIN
            
            tmp_despesa_estornada_total test_tot ON
                test_tot.exercicio = cdd.exercicio AND
                test_tot.cod_despesa = cdd.cod_despesa 

        GROUP BY 
            grupo, cdd.cod_estrutural ) AS tbl2
        
        UNION ALL
        
        -- 33. RECURSOS DE OPERAÇÕES DE CRÉDITO
        
        SELECT * FROM (
        SELECT 
            CAST(42 AS INTEGER) AS grupo, 
            CAST(0 AS INTEGER) AS nivel, 
            CAST(''DESPESAS CUSTEADAS COM OPERAÇÕES DE CRÉDITO'' AS VARCHAR) AS descricao, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_ini, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_atu,
            CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
            CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
        ) AS tbl3 
        
        UNION ALL 
        
        SELECT * FROM (
        
        SELECT 
            CAST(42 AS INTEGER) AS grupo, 
            CAST(1 AS INTEGER) AS nivel, 
            CAST(''Recursos de Operações de Crédito'' AS VARCHAR) AS descricao, 
            COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini, 
            (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,             
            (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                    , ' || quote_literal(stExercicio) || '
                                                                    , ' || quote_literal(stEntidades) || '
                                                                    , ' || quote_literal(stDtIniExercicio) || '
                                                                    , ' || quote_literal(stDtFim)     || '
                                                                    , false )
            )), 0.00) AS vl_empenhado_ate_bimestre,
            '|| stSQLRestos ||' 
        FROM 
            orcamento.subfuncao s
            
            -- Vinculo com Recursos Configurados no STN
            
            JOIN 
            (SELECT 
                d.exercicio, 
                d.cod_despesa,
                d.cod_funcao, 
                d.cod_subfuncao, 
                d.cod_conta,
                cd.cod_estrutural 
            FROM 
                stn.vinculo_recurso vr
                    INNER JOIN
                    orcamento.despesa d ON
                        d.exercicio = vr.exercicio AND 
                        d.cod_entidade = vr.cod_entidade AND 
                        d.cod_recurso = vr.cod_recurso AND 
                        d.num_orgao = vr.num_orgao AND 
                        d.num_unidade = vr.num_unidade 
                    INNER JOIN
                    orcamento.conta_despesa cd ON
                        cd.exercicio = d.exercicio AND
                        cd.cod_conta = d.cod_conta
            WHERE
                d.exercicio = ''' || stExercicio || ''' AND 
                d.cod_entidade IN (' || stEntidades || ') AND 
                -- Educacao 
                d.cod_funcao = 12 AND
                -- Operacoes de Credito em stn.vinculo_stn_recurso
                vr.cod_vinculo = 4 
            ) AS cdd ON 
                cdd.exercicio = s.exercicio AND 
                cdd.cod_subfuncao = s.cod_subfuncao
            
            -- suplementacoes e reduções de despesa
            
            LEFT JOIN 
            tmp_despesa_liberada tdl ON 
                tdl.exercicio   = cdd.exercicio AND 
                tdl.cod_despesa = cdd.cod_despesa 
                
            -- liquidacao total no exercicio 
            
            LEFT JOIN
            
            tmp_despesa_liquidada_total tt ON 
                tt.exercicio = cdd.exercicio AND 
                tt.cod_despesa = cdd.cod_despesa
                
            LEFT JOIN
            
            tmp_despesa_estornada_total test_tot ON
                test_tot.exercicio = cdd.exercicio AND
                test_tot.cod_despesa = cdd.cod_despesa 
            
        GROUP BY
            grupo, cdd.cod_estrutural ) AS tbl4 
        
        UNION ALL 
        
        -- 34. OUTROS RECURSOS DESTINADOS À EDUCAÇÃO
        
        SELECT * FROM (
        SELECT 
            CAST(43 AS INTEGER) AS grupo, 
            CAST(0 AS INTEGER) AS nivel, 
            CAST(''DESPESAS CUSTEADAS COM OUTRAS RECEITAS PARA O FINANCIAMENTO DE ENSINO'' AS VARCHAR) AS descricao, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_ini, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_atu,
            CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
            CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
        ) AS tbl5 
        
        UNION ALL 
        
        SELECT * FROM (
        
        SELECT 
            CAST(43 AS INTEGER) AS grupo, 
            CAST(1 AS INTEGER) AS nivel, 
            CAST(''Outros Recursos Destinados a Educação'' AS VARCHAR) AS descricao, 
            COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini, 
            (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu,             
            (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado,
            CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado,            
            COALESCE(SUM((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                    , ' || quote_literal(stExercicio) || '
                                                                    , ' || quote_literal(stEntidades) || '
                                                                    , ' || quote_literal(stDtIniExercicio) || '
                                                                    , ' || quote_literal(stDtFim)     || '
                                                                    , false )
            )), 0.00) AS vl_empenhado_ate_bimestre,
            '|| stSQLRestos ||' 
        FROM 
            orcamento.subfuncao s
            
            -- Vinculo com Recursos Configurados no STN
            
            JOIN 
            (SELECT 
                d.exercicio, 
                d.cod_despesa,
                d.cod_funcao, 
                d.cod_subfuncao, 
                d.cod_conta,
                cd.cod_estrutural 
            FROM 
                stn.vinculo_recurso vr
                    INNER JOIN
                    orcamento.despesa d ON
                        d.exercicio = vr.exercicio AND 
                        d.cod_entidade = vr.cod_entidade AND 
                        d.cod_recurso = vr.cod_recurso AND 
                        d.num_orgao = vr.num_orgao AND 
                        d.num_unidade = vr.num_unidade 
                    INNER JOIN
                    orcamento.conta_despesa cd ON
                        cd.exercicio = d.exercicio AND
                        cd.cod_conta = d.cod_conta
            WHERE
                d.exercicio = ''' || stExercicio || ''' AND 
                d.cod_entidade IN (' || stEntidades || ') AND 
                -- Educacao 
                d.cod_funcao = 12 AND
                -- Outros Recursos destinados a MDE em stn.vinculo_stn_recurso
                vr.cod_vinculo = 5 
            ) AS cdd ON 
                cdd.exercicio = s.exercicio AND 
                cdd.cod_subfuncao = s.cod_subfuncao
            
            -- suplementacoes e reduções de despesa
            
            LEFT JOIN 
            tmp_despesa_liberada tdl ON 
                tdl.exercicio   = cdd.exercicio AND 
                tdl.cod_despesa = cdd.cod_despesa 
                
            -- liquidacao total no exercicio 
            
            LEFT JOIN
            
            tmp_despesa_liquidada_total tt ON 
                tt.exercicio = cdd.exercicio AND 
                tt.cod_despesa = cdd.cod_despesa
                
            LEFT JOIN
            
            tmp_despesa_estornada_total test_tot ON
                test_tot.exercicio = cdd.exercicio AND
                test_tot.cod_despesa = cdd.cod_despesa 
                                    
        GROUP BY
            grupo, cdd.cod_estrutural ) AS tbl6 
        )';
    
    END IF;

    EXECUTE stSQL;
    
    -- Calcular totais do nivel pai
    
    stSQL := 'SELECT DISTINCT grupo FROM tmp_rreo_retorno ';
    
    FOR reReg IN EXECUTE stSQL
    LOOP
        
        stSQLaux := '
        UPDATE tmp_rreo_retorno SET
            dot_ini                     = (SELECT COALESCE(SUM(dot_ini), 0.00) FROM tmp_rreo_retorno                    WHERE grupo = ' || reReg.grupo || ' AND nivel > 0),
            dot_atu                     = (SELECT COALESCE(SUM(dot_atu), 0.00) FROM tmp_rreo_retorno                    WHERE grupo = ' || reReg.grupo || ' AND nivel > 0),
            liq_tot                     = (SELECT COALESCE(SUM(liq_tot), 0.00) FROM tmp_rreo_retorno                    WHERE grupo = ' || reReg.grupo || ' AND nivel > 0),
            vl_empenhado_ate_bimestre   = (SELECT COALESCE(SUM(vl_empenhado_ate_bimestre), 0.00) FROM tmp_rreo_retorno  WHERE grupo = ' || reReg.grupo || ' AND nivel > 0),
            vl_restos_inscritos         = (SELECT COALESCE(SUM(vl_restos_inscritos), 0.00) FROM tmp_rreo_retorno        WHERE grupo = ' || reReg.grupo || ' AND nivel > 0),
            pct_liquidado               = (SELECT COALESCE(SUM((liq_tot / dot_atu) * 100), 0.00) FROM tmp_rreo_retorno  WHERE grupo = ' || reReg.grupo || ' AND nivel > 0 and liq_tot > 0 AND dot_atu > 0),
            pct_empenhado               = (SELECT COALESCE(SUM((vl_empenhado_ate_bimestre / dot_atu) * 100), 0.00) FROM tmp_rreo_retorno WHERE grupo = ' || reReg.grupo || ' AND nivel > 0 AND dot_atu > 0 AND vl_empenhado_ate_bimestre > 0)
            
        WHERE
            grupo = ' || reReg.grupo || ' AND nivel = 0 ';
            
        EXECUTE stSQLaux;
        
    END LOOP;
    
    
    -- Calcular porcentagens
    
    -- stSQL := '  UPDATE tmp_rreo_retorno SET 
    --             pct_liquidado = CAST(((liq_tot / dot_atu) * 100) AS NUMERIC(14,2))                
    --             WHERE liq_tot > 0 AND dot_atu > 0;

    --             UPDATE tmp_rreo_retorno SET                 
    --             pct_empenhado = CAST(((vl_empenhado_ate_bimestre / dot_atu) * 100) AS NUMERIC(14,2)) 
    --             WHERE dot_atu > 0 AND vl_empenhado_ate_bimestre > 0;
    --         ';

    -- EXECUTE stSQL;
    
    -- Seleção de Retorno
    
    -- --------------------------------------
    -- Select de Retorno
    -- --------------------------------------
    
    stSQL := '  SELECT  CAST(grupo AS INTEGER) AS grupo, 
                        CAST(nivel AS INTEGER) AS nivel, 
                        CAST(descricao AS VARCHAR) AS descricao, 
                        CAST(dot_ini AS NUMERIC(14,2)) AS dot_ini, 
                        CAST(dot_atu AS NUMERIC(14,2)) AS dot_atu,                         
                        CAST(liq_tot AS NUMERIC(14,2)) AS liq_tot, 
                        CAST(pct_liquidado AS NUMERIC(14,2)) AS pct_liquidado,
                        CAST(pct_empenhado AS NUMERIC(14,2)) AS pct_empenhado,                        
                        CAST(vl_empenhado_ate_bimestre AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
                        CAST(vl_restos_inscritos AS NUMERIC(14,2)) AS vl_restos_inscritos
                FROM tmp_rreo_retorno 
                ORDER BY grupo, nivel, descricao';
    
    FOR reReg IN EXECUTE stSQL
    LOOP    
        RETURN NEXT reReg;  
    END LOOP;

    DROP TABLE tmp_despesa_liberada;
    DROP TABLE tmp_despesa_liquidada_total;
    DROP TABLE tmp_despesa_estornada_total;
    DROP TABLE tmp_rreo_retorno;
    
    RETURN;

END;
$$ LANGUAGE 'plpgsql';
