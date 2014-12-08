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
CREATE OR REPLACE FUNCTION EmpenhoAnulacaoLiquidacaoRestosAPagarTCEMS ( character varying, numeric, character varying, integer, character varying, integer, integer, character varying, character varying ) RETURNS INTEGER AS $$
DECLARE
    EXERCICIO ALIAS FOR $1;
    VALOR ALIAS FOR $2;
    COMPLEMENTO ALIAS FOR $3;
    CODLOTE ALIAS FOR $4;
    TIPOLOTE ALIAS FOR $5;
    CODENTIDADE ALIAS FOR $6;
    CODNOTA ALIAS FOR $7;
    EXERCRP ALIAS FOR $8;
    EXERCLIQUIDACAO ALIAS FOR $9;

    SEQUENCIA INTEGER;
    inCodDespesa INTEGER;
    boImplantado BOOLEAN;
    INCONTCONFIGURACAO INTEGER := 0;
    SQLCONTA VARCHAR := '';
    SQLCONTAFIXA VARCHAR := '';
    SQLCONFIGURACAO VARCHAR := '';
    RECONTA RECORD;
    REREGISTROSCONTAFIXA RECORD;
    REREGISTROSCONFIGURACAO RECORD;
BEGIN

    inCodDespesa := selectIntoInteger(' SELECT despesa.cod_despesa
                                          FROM empenho.nota_liquidacao
                                    INNER JOIN empenho.empenho
                                            ON empenho.cod_empenho  = nota_liquidacao.cod_empenho
                                           AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                           AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                    INNER JOIN empenho.pre_empenho
                                            ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                                           AND pre_empenho.exercicio       = empenho.exercicio
                                    INNER JOIN empenho.pre_empenho_despesa
                                            ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                           AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                                    INNER JOIN orcamento.despesa
                                            ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                                           AND despesa.exercicio   = pre_empenho_despesa.exercicio
                                         WHERE nota_liquidacao.cod_nota = ' || CODNOTA || '
                                           AND nota_liquidacao.exercicio = '''||EXERCLIQUIDACAO||'''
                                           ');

    boImplantado := selectIntoBoolean(' SELECT pre_empenho.implantado
                                          FROM empenho.nota_liquidacao
                                    INNER JOIN empenho.empenho
                                            ON empenho.cod_empenho  = nota_liquidacao.cod_empenho
                                           AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                           AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                    INNER JOIN empenho.pre_empenho
                                            ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                                           AND pre_empenho.exercicio       = empenho.exercicio
                                         WHERE nota_liquidacao.cod_nota = ' || CODNOTA || '
                                           AND nota_liquidacao.exercicio = '''||EXERCLIQUIDACAO||'''
                                           ');

    IF EXERCICIO::integer > 2013 THEN
        IF boImplantado = FALSE THEN
            SQLCONFIGURACAO := '
                    SELECT REPLACE(plano_analitica_credito.cod_plano::VARCHAR, ''.'', '''')::integer AS conta_credito
                         , REPLACE(plano_analitica_debito.cod_plano::VARCHAR, ''.'', '''')::integer AS conta_debito
                         , configuracao_lancamento_debito.cod_conta_despesa
                         , REPLACE(plano_conta_credito.cod_estrutural, ''.'', '''') as estrutural_credito
                         , REPLACE(plano_conta_debito.cod_estrutural, ''.'', '''') as estrutural_debito
                      FROM empenho.nota_liquidacao
                INNER JOIN empenho.empenho
                        ON empenho.cod_empenho  = nota_liquidacao.cod_empenho
                       AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                       AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                INNER JOIN empenho.pre_empenho
                        ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                       AND pre_empenho.exercicio       = empenho.exercicio
                INNER JOIN empenho.pre_empenho_despesa
                        ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                INNER JOIN orcamento.conta_despesa
                        ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                       AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                INNER JOIN contabilidade.configuracao_lancamento_credito
                        ON configuracao_lancamento_credito.cod_conta_despesa = conta_despesa.cod_conta
                       AND configuracao_lancamento_credito.exercicio         = '''||EXERCICIO||'''
                INNER JOIN contabilidade.configuracao_lancamento_debito
                        ON configuracao_lancamento_credito.exercicio = configuracao_lancamento_debito.exercicio
                       AND configuracao_lancamento_credito.cod_conta_despesa = configuracao_lancamento_debito.cod_conta_despesa
                       AND configuracao_lancamento_credito.tipo = configuracao_lancamento_debito.tipo
                       AND configuracao_lancamento_credito.estorno = configuracao_lancamento_debito.estorno
                INNER JOIN contabilidade.plano_conta plano_conta_credito
                        ON plano_conta_credito.cod_conta = configuracao_lancamento_credito.cod_conta
                       AND plano_conta_credito.exercicio = configuracao_lancamento_credito.exercicio
                INNER JOIN contabilidade.plano_analitica plano_analitica_credito
                        ON plano_conta_credito.cod_conta = plano_analitica_credito.cod_conta
                       AND plano_conta_credito.exercicio = plano_analitica_credito.exercicio
                INNER JOIN contabilidade.plano_conta plano_conta_debito
                        ON plano_conta_debito.cod_conta = configuracao_lancamento_debito.cod_conta
                       AND plano_conta_debito.exercicio = configuracao_lancamento_debito.exercicio
                INNER JOIN contabilidade.plano_analitica plano_analitica_debito
                        ON plano_conta_debito.cod_conta = plano_analitica_debito.cod_conta
                       AND plano_conta_debito.exercicio = plano_analitica_debito.exercicio
                     WHERE configuracao_lancamento_credito.estorno = ''true''
                       AND configuracao_lancamento_credito.exercicio = '''||EXERCICIO||'''
                       AND configuracao_lancamento_credito.tipo = ''liquidacao''
                       AND nota_liquidacao.cod_nota = ' || CODNOTA || '
                       AND nota_liquidacao.exercicio = '''||EXERCLIQUIDACAO||'''
            ';
        ELSE
            SQLCONFIGURACAO := '
                    SELECT REPLACE(plano_analitica_debito.cod_plano::VARCHAR, ''.'', '''')::integer AS conta_debito
                         , REPLACE(plano_analitica_credito.cod_plano::VARCHAR, ''.'', '''')::integer AS conta_credito
                         , configuracao_lancamento_debito.cod_conta_despesa
                         , REPLACE(plano_conta_debito.cod_estrutural, ''.'', '''') as estrutural_debito
                         , REPLACE(plano_conta_credito.cod_estrutural, ''.'', '''') as estrutural_credito
                      FROM empenho.nota_liquidacao
                INNER JOIN empenho.empenho
                        ON empenho.cod_empenho  = nota_liquidacao.cod_empenho
                       AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                       AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                INNER JOIN empenho.pre_empenho
                        ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                       AND pre_empenho.exercicio       = empenho.exercicio
                INNER JOIN empenho.restos_pre_empenho
                        ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       AND restos_pre_empenho.exercicio       = pre_empenho.exercicio
                INNER JOIN orcamento.conta_despesa
                        ON REPLACE(conta_despesa.cod_estrutural, ''.'', '''') = restos_pre_empenho.cod_estrutural
                       AND conta_despesa.exercicio = '''||EXERCICIO||'''
                INNER JOIN contabilidade.configuracao_lancamento_credito
                        ON configuracao_lancamento_credito.cod_conta_despesa = conta_despesa.cod_conta
                       AND configuracao_lancamento_credito.exercicio         = '''||EXERCICIO||'''
                INNER JOIN contabilidade.configuracao_lancamento_debito
                        ON configuracao_lancamento_credito.exercicio = configuracao_lancamento_debito.exercicio
                       AND configuracao_lancamento_credito.cod_conta_despesa = configuracao_lancamento_debito.cod_conta_despesa
                       AND configuracao_lancamento_credito.tipo = configuracao_lancamento_debito.tipo
                       AND configuracao_lancamento_credito.estorno = configuracao_lancamento_debito.estorno
                INNER JOIN contabilidade.plano_conta plano_conta_credito
                        ON plano_conta_credito.cod_conta = configuracao_lancamento_credito.cod_conta
                       AND plano_conta_credito.exercicio = configuracao_lancamento_credito.exercicio
                INNER JOIN contabilidade.plano_analitica plano_analitica_credito
                        ON plano_conta_credito.cod_conta = plano_analitica_credito.cod_conta
                       AND plano_conta_credito.exercicio = plano_analitica_credito.exercicio
                INNER JOIN contabilidade.plano_conta plano_conta_debito
                        ON plano_conta_debito.cod_conta = configuracao_lancamento_debito.cod_conta
                       AND plano_conta_debito.exercicio = configuracao_lancamento_debito.exercicio
                INNER JOIN contabilidade.plano_analitica plano_analitica_debito
                        ON plano_conta_debito.cod_conta = plano_analitica_debito.cod_conta
                       AND plano_conta_debito.exercicio = plano_analitica_debito.exercicio
                     WHERE configuracao_lancamento_credito.estorno = ''false''
                       AND configuracao_lancamento_credito.exercicio = '''||EXERCICIO||'''
                       AND configuracao_lancamento_credito.tipo = ''liquidacao''
                       AND nota_liquidacao.cod_nota = ' || CODNOTA || '
                       AND nota_liquidacao.exercicio = '''||EXERCLIQUIDACAO||'''
            ';
        END IF;
    
        FOR REREGISTROSCONFIGURACAO IN EXECUTE SQLCONFIGURACAO
        LOOP
            SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONFIGURACAO.estrutural_debito , REREGISTROSCONFIGURACAO.estrutural_credito , 919 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONFIGURACAO.conta_debito, REREGISTROSCONFIGURACAO.conta_credito );
            INCONTCONFIGURACAO := INCONTCONFIGURACAO + 1;
        END LOOP;

        IF (INCONTCONFIGURACAO = 0) THEN
            RAISE EXCEPTION 'Configuração dos Lançamentos de despesa não configurados para esta despesa!';
        END IF;
    END IF;

    SQLCONTAFIXA := '
            SELECT debito.cod_estrutural AS estrutural_debito
                 , credito.cod_estrutural AS estrutural_credito
                 , debito.cod_plano AS plano_debito
                 , credito.cod_plano AS plano_credito
                 , debito.exercicio
              FROM (
                     SELECT plano_conta.cod_estrutural
                          , plano_analitica.cod_plano
                          , plano_conta.exercicio
                       FROM contabilidade.plano_conta
                 INNER JOIN contabilidade.plano_analitica
                         ON plano_conta.cod_conta = plano_analitica.cod_conta
                        AND plano_conta.exercicio = plano_analitica.exercicio
                      WHERE REPLACE(plano_conta.cod_estrutural, ''.'',  '''') LIKE ''6313%''
                   ) AS debito
        INNER JOIN (
                     SELECT plano_conta.cod_estrutural
                          , plano_analitica.cod_plano
                          , plano_conta.exercicio
                       FROM contabilidade.plano_conta
                 INNER JOIN contabilidade.plano_analitica
                         ON plano_conta.cod_conta = plano_analitica.cod_conta
                        AND plano_conta.exercicio = plano_analitica.exercicio
                      WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''6311%''
                   ) AS credito
                 ON debito.exercicio = credito.exercicio
              WHERE debito.exercicio = '''||EXERCICIO||'''
    ';

    FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
    LOOP
            SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , 919 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
    END LOOP;

    IF EXERCICIO::integer > 2013 THEN
        IF boImplantado = FALSE THEN
            SQLCONTAFIXA := '
        SELECT tabela_debito.plano_debito
             , tabela_debito.estrutural_debito
             , tabela_credito.plano_credito
             , tabela_credito.estrutural_credito
          FROM orcamento.despesa
          JOIN orcamento.recurso
            ON recurso.cod_recurso = despesa.cod_recurso
           AND recurso.exercicio   = despesa.exercicio
          JOIN ( SELECT plano_recurso.cod_recurso
                      , plano_recurso.exercicio
                      , plano_analitica.cod_plano AS plano_debito
                      , plano_conta.cod_estrutural AS estrutural_debito
                   FROM contabilidade.plano_recurso
                   JOIN contabilidade.plano_analitica
                     ON plano_analitica.cod_plano = plano_recurso.cod_plano
                    AND plano_analitica.exercicio = plano_recurso.exercicio
                   JOIN contabilidade.plano_conta
                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                    AND plano_conta.exercicio = plano_analitica.exercicio
                  WHERE plano_conta.cod_estrutural LIKE ''8.2.1.1.3%''
                    AND plano_conta.exercicio = '''||EXERCICIO||'''
             ) AS tabela_debito
            ON tabela_debito.cod_recurso = recurso.cod_recurso
          JOIN ( SELECT plano_recurso.cod_recurso
                      , plano_recurso.exercicio
                      , plano_analitica.cod_plano AS plano_credito
                      , plano_conta.cod_estrutural AS estrutural_credito
                   FROM contabilidade.plano_recurso
                   JOIN contabilidade.plano_analitica
                     ON plano_analitica.cod_plano = plano_recurso.cod_plano
                    AND plano_analitica.exercicio = plano_recurso.exercicio
                   JOIN contabilidade.plano_conta
                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                    AND plano_conta.exercicio = plano_analitica.exercicio
                  WHERE plano_conta.cod_estrutural LIKE ''8.2.1.1.2%''
                    AND plano_conta.exercicio = '''||EXERCICIO||'''
             ) AS tabela_credito
            ON tabela_credito.cod_recurso = recurso.cod_recurso
         WHERE despesa.cod_despesa = '||inCodDespesa||'
           AND despesa.exercicio = '''||EXERCLIQUIDACAO||'''
            ';
        ELSE
            SQLCONTAFIXA := '
        SELECT tabela_debito.plano_debito
             , tabela_debito.estrutural_debito
             , tabela_credito.plano_credito
             , tabela_credito.estrutural_credito
          FROM empenho.nota_liquidacao
    INNER JOIN empenho.empenho
            ON empenho.cod_empenho  = nota_liquidacao.cod_empenho
           AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
           AND empenho.cod_entidade = nota_liquidacao.cod_entidade
    INNER JOIN empenho.pre_empenho
            ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
           AND pre_empenho.exercicio       = empenho.exercicio
    INNER JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
           AND restos_pre_empenho.exercicio       = pre_empenho.exercicio
          JOIN ( SELECT plano_recurso.cod_recurso
                      , plano_recurso.exercicio
                      , plano_analitica.cod_plano AS plano_debito
                      , plano_conta.cod_estrutural AS estrutural_debito
                   FROM contabilidade.plano_recurso
                   JOIN contabilidade.plano_analitica
                     ON plano_analitica.cod_plano = plano_recurso.cod_plano
                    AND plano_analitica.exercicio = plano_recurso.exercicio
                   JOIN contabilidade.plano_conta
                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                    AND plano_conta.exercicio = plano_analitica.exercicio
                  WHERE plano_conta.cod_estrutural LIKE ''8.2.1.1.3%''
                    AND plano_conta.exercicio = '''||EXERCICIO||'''
             ) AS tabela_debito
            ON tabela_debito.cod_recurso = restos_pre_empenho.recurso
          JOIN ( SELECT plano_recurso.cod_recurso
                      , plano_recurso.exercicio
                      , plano_analitica.cod_plano AS plano_credito
                      , plano_conta.cod_estrutural AS estrutural_credito
                   FROM contabilidade.plano_recurso
                   JOIN contabilidade.plano_analitica
                     ON plano_analitica.cod_plano = plano_recurso.cod_plano
                    AND plano_analitica.exercicio = plano_recurso.exercicio
                   JOIN contabilidade.plano_conta
                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                    AND plano_conta.exercicio = plano_analitica.exercicio
                  WHERE plano_conta.cod_estrutural LIKE ''8.2.1.1.2%''
                    AND plano_conta.exercicio = '''||EXERCICIO||'''
             ) AS tabela_credito
            ON tabela_credito.cod_recurso = restos_pre_empenho.recurso
         WHERE nota_liquidacao.cod_nota  = ' || CODNOTA || '
           AND nota_liquidacao.exercicio = '''||EXERCLIQUIDACAO||'''
            ';
        END IF;
    
        FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
        LOOP
            SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , 919 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
        END LOOP;
    END IF;
    -- O URBEM NAO UTILIZA CONTRATO PARA PODER LANCAR ESSAS CONTAS
    --SEQUENCIA := FAZERLANCAMENTO(  '812420000' , '812410000' , 919 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    --SEQUENCIA := EMPENHOANULACAOLIQUIDACAORESTOSAPAGAREXERCICIO(  EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , CODNOTA , EXERCRP , EXERCLIQUIDACAO  );
    RETURN SEQUENCIA;
END;
$$ LANGUAGE 'plpgsql';
