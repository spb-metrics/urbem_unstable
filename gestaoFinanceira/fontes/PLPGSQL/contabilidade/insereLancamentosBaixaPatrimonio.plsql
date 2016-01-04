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
    * PL Lançamento Contábil de baixa de bens
    * Data de Criação: 01/06/2008

    * @author Analista:       Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Arthur Cruz
    
    * @package URBEM
    * @subpackage 

    $Id: $
*/
CREATE OR REPLACE FUNCTION contabilidade.fn_insere_lancamentos_baixa_patrimonio(VARCHAR, VARCHAR, INTEGER, DATE, INTEGER, VARCHAR, BOOLEAN) RETURNS VOID AS $$
DECLARE
    PstExercicio                ALIAS FOR $1;
    PstCodBem                   ALIAS FOR $2;
    PinTipoBaixa                ALIAS FOR $3;
    PdtDataBaixa                ALIAS FOR $4;
    PinCodHistorico             ALIAS FOR $5;
    PstTipo                     ALIAS FOR $6;
    PboEstorno                  ALIAS FOR $7;
    
    arCodBem                    INTEGER[];
    inIndice                    INTEGER := 0;
    inCodLote                   INTEGER := 0;
    inCodPlanoTipoBaixa         INTEGER := 0;
    inCodPlanoBem               INTEGER := 0;
    inCodPlanoDeb               INTEGER := 0;
    inCodPlanoCred              INTEGER := 0;
    inSequencia                 INTEGER := 0;
    inCodLancBaixa              INTEGER := 0;
    PinCodHistoricoDepreciacao  INTEGER := 0;
    stCodEstruturalBaixa        VARCHAR := '';
    stNomeLote                  VARCHAR := '';
    stComplemento               VARCHAR := '';
    stSql                       VARCHAR := '';
    stFiltro                    VARCHAR := '';
    reCodPlano                  RECORD;
    reBaixaBem                  RECORD;
BEGIN
   
    -- Através do tipo de baixa, seta o codigo_estrutural para buscar o cod_plano, que será usado no Débito, ou crédito quando estorno.
    IF PinTipoBaixa = 1 THEN
        -- Doação de bens Imóveis 
        stCodEstruturalBaixa := '3.5.1.2.2.02.02.00.00.00';
    
    ELSEIF PinTipoBaixa = 2 THEN
        -- Doação de bens Móveis
        stCodEstruturalBaixa := '3.5.1.2.2.02.04.00.00.00';
    
    ELSEIF PinTipoBaixa = 3 THEN
        -- Transferência de bens Imóveis
        stCodEstruturalBaixa := '3.5.1.2.2.02.01.00.00.00';
    
    ELSEIF PinTipoBaixa = 4 THEN
        -- Transferência de bens Móveis
        stCodEstruturalBaixa := '3.5.1.2.2.02.03.00.00.00';
    
    ELSEIF PinTipoBaixa = 5 THEN
        -- Perda Involuntária de bens Imóveis
        stCodEstruturalBaixa := '3.6.3.1.1.02.00.00.00.00';
    
    ELSEIF PinTipoBaixa = 6 THEN
        -- Perda Involuntária de bens Móveis
        stCodEstruturalBaixa := '3.6.3.1.1.01.00.00.00.00';
    END IF;
    
    -- Verifica a partir do estrutural do tipo de baixa, se está cadastrada no sistema...
     SELECT INTO
            inCodPlanoTipoBaixa
            cod_plano
      FROM contabilidade.plano_conta 
INNER JOIN contabilidade.plano_analitica
        ON plano_analitica.exercicio  = plano_conta.exercicio 
       AND plano_analitica.cod_conta  = plano_conta.cod_conta 
     WHERE plano_conta.cod_estrutural = stCodEstruturalBaixa
       AND plano_analitica.exercicio  = PstExercicio;
       
    IF inCodPlanoTipoBaixa IS NULL THEN
        RAISE EXCEPTION 'Conta ( % ) não é analítica ou não está cadastrada no plano de contas.', stCodEstruturalBaixa;
    END IF;
    
    -- Transforma em um array os cod_bens passados por parametro.
    arCodBem = string_to_array(PstCodBem,',');

    FOR inIndice IN 1..array_upper(arCodBem, 1) LOOP

        -- Verifica quais bens possuem um cod_plano de baixa relacionado configurado no exercicio.
        stSql := '
            SELECT bem.cod_bem
                 , bem.cod_bem || '' - '' || TRIM(bem.descricao) AS descricao_bem
                 , bem.vl_bem
                 , valor_liquido_contabil.vl_atualizado AS vl_lancamento_contabil
                 , grupo_plano_analitica.cod_plano
                 , grupo_plano_analitica.cod_plano_doacao
                 , grupo_plano_analitica.cod_plano_perda_involuntaria
                 , grupo_plano_analitica.cod_plano_transferencia
                 , depreciacao.cod_depreciacao
                 , natureza.cod_tipo
                 , natureza.cod_natureza
                 , natureza.nom_natureza
                 , grupo.cod_grupo
                 , grupo.nom_grupo
                 , bem_comprado.cod_entidade
    
              FROM patrimonio.bem
              
        INNER JOIN patrimonio.bem_comprado
                ON bem_comprado.cod_bem = bem.cod_bem
    
        INNER JOIN patrimonio.especie
                ON especie.cod_natureza = bem.cod_natureza
               AND especie.cod_grupo    = bem.cod_grupo
               AND especie.cod_especie  = bem.cod_especie
    
        INNER JOIN patrimonio.grupo
                ON grupo.cod_natureza = especie.cod_natureza
               AND grupo.cod_grupo    = especie.cod_grupo
    
        INNER JOIN patrimonio.natureza
                ON natureza.cod_natureza = grupo.cod_natureza
    
         LEFT JOIN patrimonio.grupo_plano_analitica
                ON grupo_plano_analitica.cod_grupo    = grupo.cod_grupo
               AND grupo_plano_analitica.cod_natureza = grupo.cod_natureza
               AND grupo_plano_analitica.exercicio    = '|| quote_literal(PstExercicio) ||'
    
        INNER JOIN (
                    SELECT cod_bem
                         , vl_bem
                         , vl_atualizado
                         , vl_acumulado
                      FROM patrimonio.fn_depreciacao_acumulada('|| arCodBem[inIndice] ||')
                        AS retorno (  cod_bem            INTEGER
                                    , vl_acumulado       NUMERIC
                                    , vl_atualizado      NUMERIC
                                    , vl_bem             NUMERIC
                                    , min_competencia    VARCHAR
                                    , max_competencia    VARCHAR
                                   )
                   ) AS valor_liquido_contabil
                ON valor_liquido_contabil.cod_bem = bem.cod_bem
    
         LEFT JOIN patrimonio.depreciacao
                ON depreciacao.cod_bem = bem.cod_bem
    
             WHERE NOT EXISTS ( SELECT 1 
                                 FROM patrimonio.depreciacao_anulada
                                WHERE depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
                                  AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem
                                  AND depreciacao_anulada.timestamp       = depreciacao.timestamp )
               AND bem.cod_bem = ' || arCodBem[inIndice];

        -- Para cada bem irá fazer os lançamentos contabéis
        FOR reBaixaBem IN EXECUTE stSql
        LOOP

            -- Seta o valor do cod_plano do bem conforme o tipo de baixa informado.
            IF PinTipoBaixa = 1 OR PinTipoBaixa = 2 THEN
                inCodPlanoBem := reBaixaBem.cod_plano_doacao;
            ELSEIF PinTipoBaixa = 3 OR PinTipoBaixa = 4 THEN
                inCodPlanoBem := reBaixaBem.cod_plano_transferencia;
            ELSEIF PinTipoBaixa = 5 OR PinTipoBaixa = 6 THEN
                inCodPlanoBem := reBaixaBem.cod_plano_perda_involuntaria;
            END IF;
            
            -- Conforme parametro passado verifica se é ou não Estorno, caso seja, inverte as contas de lançamento.
            IF PboEstorno = FALSE THEN
                inCodPlanoDeb  := inCodPlanoTipoBaixa;
                inCodPlanoCred := inCodPlanoBem;
                stNomeLote     := 'Lançamento de Baixa Patrimonial do Bem: ' || reBaixaBem.cod_bem;
                stComplemento  := 'Baixa do Bem: ' || reBaixaBem.descricao_bem;
                PinCodHistoricoDepreciacao := 964;
            ELSE
                inCodPlanoDeb  := inCodPlanoBem;
                inCodPlanoCred := inCodPlanoTipoBaixa;
                stNomeLote     := 'Lançamento de Estorno de Baixa Patrimonial do Bem: ' || reBaixaBem.cod_bem;
                stComplemento  := 'Estorno de Baixa do Bem: ' || reBaixaBem.descricao_bem;
                PinCodHistoricoDepreciacao := 965;
            END IF;
    
            -- Caso o bem possua alguma depreciação, é necessário fazer o lançamento para apuração do valor liquido contábil.
            IF reBaixaBem.cod_depreciacao IS NOT NULL THEN
                PERFORM contabilidade.fn_insere_lancamentos_baixa_patrimonio_depreciacao(PstExercicio, arCodBem[inIndice], PinTipoBaixa, PdtDataBaixa, PinCodHistoricoDepreciacao, PstTipo, PboEstorno);
            END IF;
            
            -- Recupera o último cod_lote a ser inserido na tabela contabilidade.lancamento
            stFiltro  :=            'WHERE exercicio    = ' || quote_literal(PstExercicio); 
            stFiltro  := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
            stFiltro  := stFiltro || ' AND cod_entidade = ' || reBaixaBem.cod_entidade;
            inCodLote := publico.fn_proximo_cod('cod_lote','contabilidade.lote', stFiltro);
            
            INSERT INTO contabilidade.lote
                (cod_lote, exercicio, tipo, cod_entidade, nom_lote, dt_lote)
            VALUES
                (inCodLote, PstExercicio, PstTipo, reBaixaBem.cod_entidade, stNomeLote, PdtDataBaixa);
            
             -- Recupera a ultima sequencia de contabilidade.lancamento. Será uma para cada lancamento conforme o último lote inserido.
            stFiltro    :=       'WHERE exercicio         = ' || quote_literal(PstExercicio);
            stFiltro    := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
            stFiltro    := stFiltro || ' AND cod_entidade = ' || reBaixaBem.cod_entidade;
            stFiltro    := stFiltro || ' AND cod_lote     = ' || inCodLote;
            inSequencia := publico.fn_proximo_cod('sequencia','contabilidade.lancamento', stFiltro);
            
            INSERT INTO contabilidade.lancamento
                (sequencia, cod_lote, tipo, exercicio, cod_entidade, cod_historico, complemento)
            VALUES
                (inSequencia, inCodLote, PstTipo, PstExercicio, reBaixaBem.cod_entidade, PinCodHistorico, stComplemento);

            -- São inseridos 2 registros um a débito (valor positivo) e outro a crédito (valor negativo)
            IF inCodPlanoDeb IS NOT NULL AND inCodPlanoCred IS NOT NULL THEN
                --Insere dados de Crédito
                INSERT INTO contabilidade.valor_lancamento
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
                VALUES
                    (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBem.cod_entidade, 'C', (reBaixaBem.vl_lancamento_contabil * -1) );
                
                INSERT INTO contabilidade.conta_credito
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
                VALUES
                    (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBem.cod_entidade, 'C', inCodPlanoCred );
                
                --Insere dados de Débito
                INSERT INTO contabilidade.valor_lancamento
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
                VALUES
                    (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBem.cod_entidade,'D', (reBaixaBem.vl_lancamento_contabil) );
                    
                INSERT INTO contabilidade.conta_debito
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
                VALUES
                    (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBem.cod_entidade, 'D', inCodPlanoDeb );
            ELSE
                RAISE EXCEPTION 'Deve ser informada pelo menos uma conta de débito ou crédito, para o bem: %', reBaixaBem.cod_bem;
            END IF;
            
            -- Recupera o último id de lançamento de baixa de patrimonio
            inCodLancBaixa := publico.fn_proximo_cod('id','contabilidade.lancamento_baixa_patrimonio','');
            
            -- Relaciona a baixa com o bem.
            INSERT INTO contabilidade.lancamento_baixa_patrimonio
                (id, timestamp, exercicio, cod_entidade, tipo, cod_lote, sequencia, cod_bem, estorno )
            VALUES
                (inCodLancBaixa, ('now'::text)::timestamp(3), PstExercicio, reBaixaBem.cod_entidade, PstTipo, inCodLote, inSequencia, reBaixaBem.cod_bem, PboEstorno);
            
        END LOOP;
    END LOOP;

END;
$$ LANGUAGE 'plpgsql';