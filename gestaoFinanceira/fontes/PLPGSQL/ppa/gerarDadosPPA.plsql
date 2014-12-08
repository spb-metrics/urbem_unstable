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
    * PL que replica os dados do orçamento para os 4 exercícios do ppa
    * Data de Criação   : 18/08/2009


    * @author Analista      Tonismar Régis Bernardo 
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION ppa.fn_gerar_dados_ppa(VARCHAR, VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    stExercicioInicio           ALIAS FOR $1;
    stExercicioReplicar         ALIAS FOR $2;

    stProximoExercicio          VARCHAR;
    inExercicioExiste           INTEGER := 0;
    inCount                     INTEGER := 0;
    bolRetorno                  BOOLEAN := true;
    bolRecursoDestinacao        BOOLEAN := false;

    recRegistro                 RECORD;
BEGIN

    FOR inCount IN 0..3 LOOP
        stProximoExercicio := BTRIM(TO_CHAR(TO_NUMBER(stExercicioInicio,'9999') + inCount, '9999'));
        inExercicioExiste  := ppa.fn_verifica_exercicio_ppa(stProximoExercicio);

        IF (inExercicioExiste > 0) THEN
           bolRetorno := false;
        ELSE
            --
            -- TABELA ADMINISTRACAO_CONFIGURACAO
            --
            FOR recRegistro IN SELECT * FROM administracao.configuracao WHERE cod_modulo IN (8,9,10) AND exercicio = stExercicioReplicar
            LOOP
               INSERT INTO administracao.configuracao (cod_modulo, parametro, valor, exercicio) VALUES ( recRegistro.cod_modulo ,recRegistro.parametro, recRegistro.valor, stProximoExercicio);
            END LOOP;
            --
            -- TABELA ORCAMENTO.FUNCAO
            --
            FOR recRegistro IN SELECT * FROM orcamento.funcao WHERE exercicio = stExercicioReplicar
            LOOP
               INSERT INTO orcamento.funcao (exercicio, cod_funcao, descricao) VALUES (stProximoExercicio, recRegistro.cod_funcao ,recRegistro.descricao);
            END LOOP;

            --
            -- TABELA ORCAMENTO.SUBFUNCAO
            --
            FOR recRegistro IN SELECT * FROM orcamento.subfuncao WHERE  exercicio = stExercicioReplicar
            LOOP
               INSERT INTO orcamento.subfuncao (exercicio, cod_subfuncao, descricao) VALUES (stProximoExercicio, recRegistro.cod_subfuncao ,recRegistro.descricao);
            END LOOP;

            --
            -- TABELA ORCAMENTO.RECURSO
            --
            FOR recRegistro IN SELECT * FROM orcamento.recurso WHERE exercicio = stExercicioReplicar
            LOOP
               INSERT INTO orcamento.recurso (exercicio, cod_recurso, nom_recurso, cod_fonte) VALUES (stProximoExercicio, recRegistro.cod_recurso ,recRegistro.nom_recurso, recRegistro.cod_fonte);
            END LOOP;
            
            SELECT BTRIM(valor)::VARCHAR  INTO bolRecursoDestinacao
              FROM administracao.configuracao
             WHERE configuracao.exercicio  = stExercicioReplicar
               AND configuracao.cod_modulo = 8
               AND configuracao.parametro  = 'recurso_destinacao';

            IF bolRecursoDestinacao THEN
                --
                -- TABELA ORCAMENTO.DETALHAMENTO_DESTINACAO_RECURSO
                --
                FOR recRegistro IN SELECT * FROM orcamento.detalhamento_destinacao_recurso WHERE exercicio = stExercicioReplicar
                LOOP
                   INSERT INTO orcamento.detalhamento_destinacao_recurso (exercicio, cod_detalhamento, descricao)
                   VALUES (stProximoExercicio, recRegistro.cod_detalhamento, recRegistro.descricao);
                END LOOP;
                
                --
                -- TABELA ORCAMENTO.DESTINACAO_RECURSO
                --
                FOR recRegistro IN SELECT * FROM orcamento.destinacao_recurso WHERE exercicio = stExercicioReplicar
                LOOP
                   INSERT INTO orcamento.destinacao_recurso (exercicio, cod_destinacao, descricao)
                   VALUES (stProximoExercicio, recRegistro.cod_destinacao, recRegistro.descricao);
                END LOOP;
                  
                --
                -- TABELA ORCAMENTO.IDENTIFICADOR_USO
                --
                FOR recRegistro IN SELECT * FROM orcamento.identificador_uso WHERE exercicio = stExercicioReplicar
                LOOP
                   INSERT INTO orcamento.identificador_uso (exercicio, cod_uso, descricao)
                   VALUES (stProximoExercicio, recRegistro.cod_uso, recRegistro.descricao);
                END LOOP;
                  
                --
                -- TABELA ORCAMENTO.ESPECIFICACAO_DESTINACAO_RECURSO
                --
                FOR recRegistro IN SELECT * FROM orcamento.especificacao_destinacao_recurso WHERE exercicio = stExercicioReplicar
                LOOP
                   INSERT INTO orcamento.especificacao_destinacao_recurso (exercicio, cod_especificacao, cod_fonte, descricao)
                   VALUES (stProximoExercicio, recRegistro.cod_especificacao, recRegistro.cod_fonte, recRegistro.descricao);
                END LOOP;
                
                --
                -- TABELA ORCAMENTO.RECURSO_DESTINACAO
                --
                FOR recRegistro IN SELECT * FROM orcamento.recurso_destinacao WHERE exercicio = stExercicioReplicar
                LOOP
                   INSERT INTO orcamento.recurso_destinacao (exercicio, cod_recurso, cod_uso, cod_destinacao, cod_especificacao, cod_detalhamento)
                   VALUES (stProximoExercicio, recRegistro.cod_recurso, recRegistro.cod_uso, recRegistro.cod_destinacao, recRegistro.cod_especificacao, recRegistro.cod_detalhamento);
                END LOOP;

            ELSE
                --
                -- TABELA ORCAMENTO.RECURSO_DIRETO
                --
                FOR recRegistro IN SELECT * FROM orcamento.recurso_direto WHERE exercicio = stExercicioReplicar
                LOOP
                    INSERT INTO orcamento.recurso_direto (exercicio, cod_recurso, cod_fonte, nom_recurso, finalidade, tipo, codigo_tc)
                    VALUES (stProximoExercicio, recRegistro.cod_recurso, recRegistro.cod_fonte, recRegistro.nom_recurso, recRegistro.finalidade, recRegistro.tipo, recRegistro.codigo_tc);
                END LOOP;
              
            END IF;
            
            --
            -- TABELA ORCAMENTO.ORGAO
            --
            FOR recRegistro IN SELECT * FROM orcamento.orgao WHERE exercicio = stExercicioReplicar
            LOOP
                 INSERT INTO orcamento.orgao (exercicio, num_orgao, nom_orgao, usuario_responsavel) VALUES (stProximoExercicio, recRegistro.num_orgao ,recRegistro.nom_orgao, recRegistro.usuario_responsavel);
            END LOOP;

            --
            -- TABELA ORCAMENTO.UNIDADE
            --
            FOR recRegistro IN SELECT * FROM orcamento.unidade WHERE exercicio = stExercicioReplicar
            LOOP
                INSERT INTO orcamento.unidade (exercicio, num_unidade, num_orgao, nom_unidade, usuario_responsavel) VALUES (stProximoExercicio, recRegistro.num_unidade, recRegistro.num_orgao ,recRegistro.nom_unidade, recRegistro.usuario_responsavel);
            END LOOP;

            bolRetorno := true;
        END IF;
    END LOOP;

RETURN bolRetorno;

END;
$$ LANGUAGE 'plpgsql';