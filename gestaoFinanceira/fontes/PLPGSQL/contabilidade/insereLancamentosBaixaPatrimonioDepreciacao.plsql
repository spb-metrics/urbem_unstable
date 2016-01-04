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
CREATE OR REPLACE FUNCTION contabilidade.fn_insere_lancamentos_baixa_patrimonio_depreciacao(VARCHAR, INTEGER, INTEGER, DATE, INTEGER, VARCHAR, BOOLEAN) RETURNS VOID AS $$
DECLARE
    PstExercicio                ALIAS FOR $1;
    PinCodBem                   ALIAS FOR $2;
    pinTipoBaixa                ALIAS FOR $3;
    PdtDataBaixa                ALIAS FOR $4;
    PinCodHistorico             ALIAS FOR $5;
    PstTipo                     ALIAS FOR $6;
    PboEstorno                  ALIAS FOR $7;
    
    inCodLote                   INTEGER := 0;
    inCodPlanoDeb               INTEGER := 0;
    inCodPlanoCred              INTEGER := 0;
    inSequencia                 INTEGER := 0;
    inCodLancBaixa              INTEGER := 0;
    stNomeLote                  VARCHAR := '';
    stComplemento               VARCHAR := '';
    stSql                       VARCHAR := '';
    stFiltro                    VARCHAR := '';
    reBaixaDepreciacao          RECORD;
BEGIN
    
    -- Recupera os cod_planos do bem, a serem usados para débito e crédito
    stSql := ' SELECT depreciacao.cod_bem
                    , bem.cod_bem || '' - '' || TRIM(bem.descricao) AS descricao_bem
                    , bem_comprado.cod_entidade
                    , valor_liquido_contabil.vl_atualizado AS vl_lancamento_contabil
	                , CASE WHEN bem_plano_analitica.cod_plano IS NOT NULL
                           THEN bem_plano_analitica.cod_plano
                           ELSE grupo_plano_bem.cod_plano
                      END AS cod_plano_bem
                    , CASE WHEN bem_plano_depreciacao.cod_plano IS NOT NULL
                          THEN bem_plano_depreciacao.cod_plano
                          ELSE grupo_plano_depreciacao.cod_plano
                      END AS cod_plano_depreciacao

                     FROM patrimonio.depreciacao 
      
               INNER JOIN patrimonio.bem
                       ON bem.cod_bem = depreciacao.cod_bem
               
               INNER JOIN patrimonio.bem_comprado
                       ON bem_comprado.cod_bem = bem.cod_bem
                       
               INNER JOIN (
                        SELECT cod_bem
                             , vl_bem
                             , vl_atualizado
                             , vl_acumulado
                          FROM patrimonio.fn_depreciacao_acumulada('|| PinCodBem ||')
                            AS retorno (  cod_bem            INTEGER
                                        , vl_acumulado       NUMERIC
                                        , vl_atualizado      NUMERIC
                                        , vl_bem             NUMERIC
                                        , min_competencia    VARCHAR
                                        , max_competencia    VARCHAR
                                       )
                        ) AS valor_liquido_contabil
                       ON valor_liquido_contabil.cod_bem = bem.cod_bem
               
                LEFT JOIN (
                        SELECT bem_plano_analitica.cod_bem
                             , bem_plano_analitica.cod_plano
                             , bem_plano_analitica.exercicio
                                                    
                          FROM patrimonio.bem_plano_analitica
               
                    INNER JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_plano = bem_plano_analitica.cod_plano
                           AND plano_analitica.exercicio = bem_plano_analitica.exercicio
                       
                         WHERE bem_plano_analitica.timestamp::timestamp = ( SELECT MAX(bem_plano.timestamp::timestamp) AS timestamp 
                                                                                FROM patrimonio.bem_plano_analitica AS bem_plano
                                                                               
                                                                               WHERE bem_plano_analitica.cod_bem   = bem_plano.cod_bem
                                                                                 AND bem_plano_analitica.exercicio = bem_plano.exercicio
                                                                                 AND bem_plano_analitica.exercicio   = '|| quote_literal(PstExercicio) ||'
                                                                            
                                                                            GROUP BY bem_plano.cod_bem
                                                                                   , bem_plano.exercicio )
                           AND bem_plano_analitica.exercicio   = '|| quote_literal(PstExercicio) ||'
                      ORDER BY timestamp DESC
                      
                     )AS bem_plano_analitica
                    ON bem_plano_analitica.cod_bem = depreciacao.cod_bem
               
             LEFT JOIN (
                         SELECT bem.cod_bem
                              , grupo_plano_analitica.cod_plano
                              , grupo_plano_analitica.exercicio
                      
                           FROM patrimonio.grupo_plano_analitica
                 
                     INNER JOIN patrimonio.grupo
                             ON grupo.cod_natureza = grupo_plano_analitica.cod_natureza
                            AND grupo.cod_grupo    = grupo_plano_analitica.cod_grupo
                     
                     INNER JOIN patrimonio.especie
                             ON especie.cod_grupo    = grupo.cod_grupo
                            AND especie.cod_natureza = grupo.cod_natureza
                     
                     INNER JOIN patrimonio.bem
                             ON bem.cod_especie  = especie.cod_especie
                            AND bem.cod_grupo    = especie.cod_grupo
                            AND bem.cod_natureza = especie.cod_natureza
                         
                          WHERE grupo_plano_analitica.exercicio = '|| quote_literal(PstExercicio) ||'
                           
                      ) AS grupo_plano_bem
                     ON grupo_plano_bem.cod_bem = depreciacao.cod_bem
               
              LEFT JOIN (
                        SELECT bem_plano_depreciacao.cod_bem
                             , bem_plano_depreciacao.cod_plano
                             , bem_plano_depreciacao.exercicio
                                                    
                          FROM patrimonio.bem_plano_depreciacao 
               
                    INNER JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_plano = bem_plano_depreciacao.cod_plano
                           AND plano_analitica.exercicio = bem_plano_depreciacao.exercicio
               
                         WHERE bem_plano_depreciacao.timestamp::timestamp = ( SELECT MAX(bem_plano.timestamp::timestamp) AS timestamp 
                                                                                FROM patrimonio.bem_plano_depreciacao AS bem_plano
                                                                               
                                                                               WHERE bem_plano_depreciacao.cod_bem   = bem_plano.cod_bem
                                                                                 AND bem_plano_depreciacao.exercicio = bem_plano.exercicio
                                                                                 AND bem_plano_depreciacao.exercicio = '|| quote_literal(PstExercicio) ||'
                                                                            
                                                                            GROUP BY bem_plano.cod_bem
                                                                                   , bem_plano.exercicio )
                           AND bem_plano_depreciacao.exercicio   = '|| quote_literal(PstExercicio) ||'
                      ORDER BY timestamp DESC
                      
                     )AS bem_plano_depreciacao
                      ON bem_plano_depreciacao.cod_bem = depreciacao.cod_bem
                   
               LEFT JOIN (
                           SELECT bem.cod_bem
                                , grupo_plano_depreciacao.cod_plano
                                , grupo_plano_depreciacao.exercicio
                      
                            FROM patrimonio.grupo_plano_depreciacao
                 
                      INNER JOIN patrimonio.grupo
                              ON grupo.cod_natureza = grupo_plano_depreciacao.cod_natureza
                             AND grupo.cod_grupo    = grupo_plano_depreciacao.cod_grupo
                      
                      INNER JOIN patrimonio.especie
                              ON especie.cod_grupo    = grupo.cod_grupo
                             AND especie.cod_natureza = grupo.cod_natureza
                      
                      INNER JOIN patrimonio.bem
                              ON bem.cod_especie  = especie.cod_especie
                             AND bem.cod_grupo    = especie.cod_grupo
                             AND bem.cod_natureza = especie.cod_natureza
                          
                           WHERE grupo_plano_depreciacao.exercicio = '|| quote_literal(PstExercicio) ||'
                           
                      ) AS grupo_plano_depreciacao
                     ON grupo_plano_depreciacao.cod_bem = depreciacao.cod_bem
               
                  WHERE NOT EXISTS ( SELECT 1 
                                      FROM patrimonio.depreciacao_anulada
                                     WHERE depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
                                       AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem
                                       AND depreciacao_anulada.timestamp       = depreciacao.timestamp
                                   )
                     AND bem.cod_bem = ' || PinCodBem;

    FOR reBaixaDepreciacao IN EXECUTE stSql
    LOOP
    
        -- Conforme parametro passado verifica se é ou não Estorno, caso seja, inverte as contas de lançamento.
        IF PboEstorno = FALSE THEN
            inCodPlanoDeb  := reBaixaDepreciacao.cod_plano_depreciacao;
            inCodPlanoCred := reBaixaDepreciacao.cod_plano_bem;
            stNomeLote     := 'Lançamento do valor líquido contábil de depreciação de baixa patrimonial do Bem: ' || reBaixaDepreciacao.descricao_bem;
            stComplemento  := 'Lançamento do valor líquido contábil de depreciação do Bem: ' || reBaixaDepreciacao.descricao_bem;
        ELSE
            inCodPlanoDeb  := reBaixaDepreciacao.cod_plano_bem;
            inCodPlanoCred := reBaixaDepreciacao.cod_plano_depreciacao;
            stNomeLote     := 'Lançamento de Estorno do valor líquido contábil de baixa patrimonial do Bem: ' || reBaixaDepreciacao.descricao_bem;
            stComplemento  := 'Estorno do valor líquido contábil de depreciação do Bem: ' || reBaixaDepreciacao.descricao_bem;
        END IF;
        
        -- Recupera o último cod_lote a ser inserido na tabela contabilidade.lancamento
        stFiltro  :=            'WHERE exercicio    = ' || quote_literal(PstExercicio);
        stFiltro  := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
        stFiltro  := stFiltro || ' AND cod_entidade = ' || reBaixaDepreciacao.cod_entidade;
        inCodLote := publico.fn_proximo_cod('cod_lote','contabilidade.lote', stFiltro);
        
        INSERT INTO contabilidade.lote
            (cod_lote, exercicio, tipo, cod_entidade, nom_lote, dt_lote)
        VALUES
            (inCodLote, PstExercicio, PstTipo, reBaixaDepreciacao.cod_entidade, stNomeLote, PdtDataBaixa);
        
         -- Recupera a ultima sequencia de contabilidade.lancamento. Será uma para cada lancamento conforme o último lote inserido.
        stFiltro    :=       'WHERE exercicio         = ' || quote_literal(PstExercicio);
        stFiltro    := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
        stFiltro    := stFiltro || ' AND cod_entidade = ' || reBaixaDepreciacao.cod_entidade;
        stFiltro    := stFiltro || ' AND cod_lote     = ' || inCodLote;
        inSequencia := publico.fn_proximo_cod('sequencia','contabilidade.lancamento', stFiltro);
        
        INSERT INTO contabilidade.lancamento
            (sequencia, cod_lote, tipo, exercicio, cod_entidade, cod_historico, complemento)
        VALUES
            (inSequencia, inCodLote, PstTipo, PstExercicio, reBaixaDepreciacao.cod_entidade, PinCodHistorico, stComplemento);
            
        -- São inseridos 2 registros um a débito (valor positivo) e outro a crédito (valor negativo)
        IF inCodPlanoDeb IS NOT NULL AND inCodPlanoCred IS NOT NULL THEN
            --Insere dados de Crédito
            INSERT INTO contabilidade.valor_lancamento
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaDepreciacao.cod_entidade, 'C', (reBaixaDepreciacao.vl_lancamento_contabil * -1) );
            
            INSERT INTO contabilidade.conta_credito
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaDepreciacao.cod_entidade, 'C', inCodPlanoCred );
            
            --Insere dados de Débito
            INSERT INTO contabilidade.valor_lancamento
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaDepreciacao.cod_entidade,'D', (reBaixaDepreciacao.vl_lancamento_contabil) );
                
            INSERT INTO contabilidade.conta_debito
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaDepreciacao.cod_entidade, 'D', inCodPlanoDeb );
        ELSE
            RAISE EXCEPTION 'Deve ser informada pelo menos uma conta de débito ou crédito, para o bem: %', reBaixaDepreciacao.cod_bem;
        END IF;
        
        -- Recupera o último id de lançamento de baixa de patrimonio deprecicao
        inCodLancBaixa := publico.fn_proximo_cod('id','contabilidade.lancamento_baixa_patrimonio_depreciacao','');
        
        -- Relaciona a baixa com o bem.
        INSERT INTO contabilidade.lancamento_baixa_patrimonio_depreciacao
            (id, timestamp, exercicio, cod_entidade, tipo, cod_lote, sequencia, cod_bem, estorno )
        VALUES
            (inCodLancBaixa, ('now'::text)::timestamp(3), PstExercicio, reBaixaDepreciacao.cod_entidade, PstTipo, inCodLote, inSequencia, reBaixaDepreciacao.cod_bem, PboEstorno);    
    END LOOP;
    
END;
$$ LANGUAGE 'plpgsql';