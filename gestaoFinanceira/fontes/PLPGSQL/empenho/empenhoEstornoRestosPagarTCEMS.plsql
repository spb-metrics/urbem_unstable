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
CREATE OR REPLACE FUNCTION public.empenhoestornorestosapagarTCEMS(character varying, numeric, character varying, integer, character varying, integer, integer, character varying, character varying) returns integer as $$
DECLARE
    STEXERCICIO ALIAS   FOR $1;
    VALOR ALIAS         FOR $2;
    COMPLEMENTO ALIAS   FOR $3;
    CODLOTE ALIAS       FOR $4;
    TIPOLOTE ALIAS      FOR $5;
    CODENTIDADE ALIAS   FOR $6;
    CODPREEMPENHO ALIAS FOR $7;
    EXERCRP ALIAS       FOR $8;
    RESTOS ALIAS        FOR $9;
    
    SEQUENCIA           INTEGER;
    SEQUENCIAAUX        INTEGER;
    
    CODPLANODEB         INTEGER;
    CODPLANOCRED        INTEGER;

    inCodNota            INTEGER;
    inCodDespesa         INTEGER;
    stExercicioLiquidacao VARCHAR;
    SQLCONTAFIXA         VARCHAR;
    REREGISTROSCONTAFIXA RECORD;
BEGIN

    IF STEXERCICIO::INTEGER < 2014 THEN
        IF RESTOS = '0' THEN
        -- não processado
                SELECT INTO CODPLANODEB c_pa1.cod_plano
                  FROM contabilidade.plano_conta as c_pc1
            INNER JOIN contabilidade.plano_analitica as c_pa1
                    ON c_pa1.cod_conta = c_pc1.cod_conta
                   AND c_pa1.exercicio = c_pc1.exercicio
                 WHERE c_pc1.cod_estrutural ILIKE '6.3.1.1%'
                   AND c_pc1.exercicio = STEXERCICIO;    
            
                SELECT INTO CODPLANOCRED c_pa2.cod_plano
                  FROM contabilidade.plano_conta as c_pc2
            INNER JOIN contabilidade.plano_analitica as c_pa2
                    ON c_pa2.cod_conta = c_pc2.cod_conta
                   AND c_pa2.exercicio = c_pc2.exercicio
                 WHERE c_pc2.cod_estrutural ILIKE '5.3.1.1%'
                   AND c_pc2.exercicio = STEXERCICIO;    
                   
                SEQUENCIA := FAZERLANCAMENTO('631100000000000', '531100000000000', 918, STEXERCICIO, VALOR, COMPLEMENTO, CODLOTE, TIPOLOTE, CODENTIDADE, CODPLANODEB, CODPLANOCRED);
                
        ELSE
        -- processado   
                SELECT INTO CODPLANODEB c_pa1.cod_plano
                  FROM contabilidade.plano_conta AS c_pc1
            INNER JOIN contabilidade.plano_analitica as c_pa1
                    ON c_pa1.cod_conta = c_pc1.cod_conta
                   AND c_pa1.exercicio = c_pc1.exercicio
                 WHERE c_pc1.cod_estrutural ILIKE '6.3.2.1%'
                   AND c_pc1.exercicio = STEXERCICIO;
            
                SELECT INTO CODPLANOCRED c_pa2.cod_plano
                  FROM contabilidade.plano_conta as c_pc2
            INNER JOIN contabilidade.plano_analitica as c_pa2
                    ON c_pa2.cod_conta = c_pc2.cod_conta
                   AND c_pa2.exercicio = c_pc2.exercicio
                 WHERE c_pc2.cod_estrutural ILIKE '5.3.2.1%'
                   AND c_pc2.exercicio = STEXERCICIO;
                   
                  
                SEQUENCIA := FAZERLANCAMENTO('632100000000000', '532100000000000', 918, STEXERCICIO, VALOR, COMPLEMENTO, CODLOTE, TIPOLOTE, CODENTIDADE, CODPLANODEB, CODPLANOCRED);
        END IF;
    ELSE
        IF RESTOS = '0' THEN
        -- não processado

            SELECT nota_liquidacao.cod_nota
              INTO inCodNota
              FROM empenho.nota_liquidacao 
              JOIN empenho.empenho 
                ON empenho.cod_empenho = nota_liquidacao.cod_empenho 
               AND empenho.exercicio   = nota_liquidacao.exercicio_empenho 
             WHERE empenho.cod_pre_empenho = CODPREEMPENHO
               AND empenho.exercicio = EXERCRP::VARCHAR;

            SELECT nota_liquidacao.exercicio
              INTO stExercicioLiquidacao
              FROM empenho.nota_liquidacao 
              JOIN empenho.empenho 
                ON empenho.cod_empenho = nota_liquidacao.cod_empenho 
               AND empenho.exercicio   = nota_liquidacao.exercicio_empenho 
             WHERE empenho.cod_pre_empenho = CODPREEMPENHO
               AND empenho.exercicio = EXERCRP::VARCHAR;

            IF inCodNota IS NOT NULL THEN
            SQLCONTAFIXA := '
                SELECT REPLACE(plano_analitica_debito.cod_plano::VARCHAR, ''.'', '''')::integer AS conta_debito
                     , (SELECT plano_analitica.cod_plano 
                          FROM contabilidade.plano_conta 
                          JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_conta = plano_conta.cod_conta
                           AND plano_analitica.exercicio = plano_conta.exercicio
                         WHERE plano_conta.exercicio = '''||STEXERCICIO||''' 
                           AND REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''4640100%'', ''.'', '''')
                           ) AS conta_credito
                     , configuracao_lancamento_debito.cod_conta_despesa
                     , REPLACE(plano_conta_debito.cod_estrutural, ''.'', '''') as estrutural_debito
                     , (SELECT plano_conta.cod_estrutural
                          FROM contabilidade.plano_conta 
                          JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_conta = plano_conta.cod_conta
                           AND plano_analitica.exercicio = plano_conta.exercicio
                         WHERE plano_conta.exercicio = '''||STEXERCICIO||''' 
                           AND REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''4640100%'', ''.'', '''')
                           ) AS estrutural_credito
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
                   AND configuracao_lancamento_credito.exercicio         = '''||STEXERCICIO||'''
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
                   AND configuracao_lancamento_credito.exercicio = '''||STEXERCICIO||'''
                   AND configuracao_lancamento_credito.tipo = ''liquidacao''
                   AND nota_liquidacao.cod_nota = ' || inCodNota || '
                   AND nota_liquidacao.exercicio = '''||stExercicioLiquidacao||'''
        ';

                FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
                LOOP
                        SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , 918 , STEXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
                END LOOP;
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
                          WHERE REPLACE(plano_conta.cod_estrutural, ''.'',  '''') LIKE ''6311%''
                       ) AS debito
            INNER JOIN (
                         SELECT plano_conta.cod_estrutural
                              , plano_analitica.cod_plano
                              , plano_conta.exercicio
                           FROM contabilidade.plano_conta
                     INNER JOIN contabilidade.plano_analitica
                             ON plano_conta.cod_conta = plano_analitica.cod_conta
                            AND plano_conta.exercicio = plano_analitica.exercicio
                          WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''63191%''
                       ) AS credito
                     ON debito.exercicio = credito.exercicio
                  WHERE debito.exercicio = '''||STEXERCICIO||'''
            ';

            FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
            LOOP
                    SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , 918 , STEXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
            END LOOP;
              
            SQLCONTAFIXA := '
                SELECT tabela_debito.plano_debito
                     , tabela_debito.estrutural_debito
                     , tabela_credito.plano_credito
                     , tabela_credito.estrutural_credito
                  FROM empenho.pre_empenho
                  JOIN empenho.pre_empenho_despesa
                    ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                   AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                  JOIN orcamento.despesa
                    ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                   AND despesa.exercicio   = pre_empenho_despesa.exercicio
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
                          WHERE plano_conta.cod_estrutural LIKE ''8.2.1.1.2%''
                            AND plano_conta.exercicio = '''||EXERCRP||'''
                     ) AS tabela_debito
                    ON tabela_debito.cod_recurso = recurso.cod_recurso
                   AND tabela_debito.exercicio   = recurso.exercicio
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
                          WHERE plano_conta.cod_estrutural LIKE ''8.2.1.1.1%''
                            AND plano_conta.exercicio = '''||EXERCRP||'''
                     ) AS tabela_credito
                    ON tabela_credito.cod_recurso = recurso.cod_recurso
                   AND tabela_credito.exercicio   = recurso.exercicio
                 WHERE pre_empenho.cod_pre_empenho = '||CODPREEMPENHO||'
                   AND pre_empenho.exercicio = '''||EXERCRP||'''
            ';
            
            FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
            LOOP
                SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , 918 , STEXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
            END LOOP;

        ELSE
        -- processado   

            inCodDespesa := selectIntoInteger(' SELECT despesa.cod_despesa
                                          FROM empenho.empenho
                                    INNER JOIN empenho.pre_empenho
                                            ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                                           AND pre_empenho.exercicio       = empenho.exercicio
                                    INNER JOIN empenho.pre_empenho_despesa
                                            ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                           AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                                    INNER JOIN orcamento.despesa
                                            ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                                           AND despesa.exercicio   = pre_empenho_despesa.exercicio
                                         WHERE empenho.cod_pre_empenho = ' || CODPREEMPENHO || '
                                           AND empenho.exercicio = ''' || EXERCRP || '''
                                           ');

            SQLCONTAFIXA := '
                SELECT REPLACE(plano_analitica_debito.cod_plano::VARCHAR, ''.'', '''')::integer AS conta_debito
                     , (SELECT plano_analitica.cod_plano 
                          FROM contabilidade.plano_conta 
                          JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_conta = plano_conta.cod_conta
                           AND plano_analitica.exercicio = plano_conta.exercicio
                         WHERE plano_conta.exercicio = '''||STEXERCICIO||''' 
                           AND REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''4640100%'', ''.'', '''')
                           ) AS conta_credito
                     , configuracao_lancamento_debito.cod_conta_despesa
                     , REPLACE(plano_conta_debito.cod_estrutural, ''.'', '''') as estrutural_debito
                     , (SELECT plano_conta.cod_estrutural
                          FROM contabilidade.plano_conta 
                          JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_conta = plano_conta.cod_conta
                           AND plano_analitica.exercicio = plano_conta.exercicio
                         WHERE plano_conta.exercicio = '''||STEXERCICIO||''' 
                           AND REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''4640100%'', ''.'', '''')
                           ) AS estrutural_credito
                  FROM empenho.empenho
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
                   AND configuracao_lancamento_credito.exercicio         = '''||STEXERCICIO||'''
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
                   AND configuracao_lancamento_credito.exercicio = '''||STEXERCICIO||'''
                   AND configuracao_lancamento_credito.tipo = ''liquidacao''
                   AND despesa.cod_despesa = '||inCodDespesa||'
                   AND despesa.exercicio = '''||STEXERCICIO||'''
        ';

            FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
            LOOP
                    SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , 918 , STEXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
            END LOOP;

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
                          WHERE REPLACE(plano_conta.cod_estrutural, ''.'',  '''') LIKE ''6321%''
                       ) AS debito
            INNER JOIN (
                         SELECT plano_conta.cod_estrutural
                              , plano_analitica.cod_plano
                              , plano_conta.exercicio
                           FROM contabilidade.plano_conta
                     INNER JOIN contabilidade.plano_analitica
                             ON plano_conta.cod_conta = plano_analitica.cod_conta
                            AND plano_conta.exercicio = plano_analitica.exercicio
                          WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''63299%''
                       ) AS credito
                     ON debito.exercicio = credito.exercicio
                  WHERE debito.exercicio = '''||STEXERCICIO||'''
            ';

            FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
            LOOP
                    SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , 918 , STEXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
            END LOOP;
              
            SQLCONTAFIXA := '
                SELECT tabela_debito.plano_debito
                     , tabela_debito.estrutural_debito
                     , tabela_credito.plano_credito
                     , tabela_credito.estrutural_credito
                  FROM empenho.pre_empenho
                  JOIN empenho.pre_empenho_despesa
                    ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                   AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                  JOIN orcamento.despesa
                    ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                   AND despesa.exercicio   = pre_empenho_despesa.exercicio
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
                            AND plano_conta.exercicio = '''||EXERCRP||'''
                     ) AS tabela_debito
                    ON tabela_debito.cod_recurso = recurso.cod_recurso
                   AND tabela_debito.exercicio   = recurso.exercicio
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
                          WHERE plano_conta.cod_estrutural LIKE ''8.2.1.1.1%''
                            AND plano_conta.exercicio = '''||EXERCRP||'''
                     ) AS tabela_credito
                    ON tabela_credito.cod_recurso = recurso.cod_recurso
                   AND tabela_credito.exercicio   = recurso.exercicio
                 WHERE pre_empenho.cod_pre_empenho = '||CODPREEMPENHO||'
                   AND pre_empenho.exercicio = '''||EXERCRP||'''
            ';
            
            FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
            LOOP
                SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , 918 , STEXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
            END LOOP;
        END IF;
    END IF;
    
    RETURN SEQUENCIA;

END;

$$ language 'plpgsql';
