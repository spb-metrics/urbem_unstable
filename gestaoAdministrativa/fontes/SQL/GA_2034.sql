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
*
* Script de DDL e DML
*
* Versao 2.03.4
*
* Fabio Bertoldi - 20141210
*
*/

----------------
-- Ticket #21932
----------------

/*
* Script de Virada de Ano: virada_para_2015.sql
*/

   -- Exclusao de possiveis funcoes de manutencao.
   CREATE OR REPLACE FUNCTION public.manutencaokiller() RETURNS VOID AS $$
   DECLARE
      reRegistro         RECORD;
      varFuncao          VARCHAR;
   BEGIN
       FOR reRegistro IN SELECT 'DROP FUNCTION '         ||
                                pg_namespace.nspname     ||
                                '.'                      ||
                                pg_proc.proname          ||
                                '( '                     ||
                                Btrim(pg_catalog.oidvectortypes(pg_proc.proargtypes)) ||
                                ' ) '  as comando
                           FROM pg_catalog.pg_proc LEFT JOIN pg_catalog.pg_namespace  ON pg_namespace.oid = pg_proc.pronamespace
                          WHERE pg_proc.prorettype    != 'pg_catalog.cstring'::pg_catalog.regtype
                            AND pg_namespace.nspname  != 'pg_catalog'
                            AND ( pg_proc.proname      = 'manutencao' OR pg_proc.proname ILIKE 'temp%' OR pg_proc.proname ILIKE 'tmp%' )
                            ORDER BY 1
       LOOP
          varFuncao := reRegistro.comando;
          EXECUTE varFuncao;
       END LOOP;

       RETURN;
   END;
   $$ LANGUAGE 'plpgsql';

   SELECT        public.manutencaokiller();
   DROP FUNCTION public.manutencaokiller();

   --
   -- Procedimento 01
   --
   CREATE TEMP TABLE tmp_exer_permis
                  AS SELECT ano_exercicio
                FROM administracao.permissao
            GROUP BY ano_exercicio
            ORDER BY 1
                   ;

   DELETE
     FROM administracao.auditoria_detalhe
    WHERE numcgm = 0
      AND cod_acao IN ( SELECT acao.cod_acao
                          FROM administracao.modulo
                             , administracao.funcionalidade
                             , administracao.acao
                         WHERE modulo.cod_modulo                 = funcionalidade.cod_modulo
                           AND funcionalidade.cod_funcionalidade = acao.cod_funcionalidade
                      )
        ;

   DELETE
     FROM administracao.auditoria
    WHERE numcgm = 0
      AND cod_acao IN ( SELECT acao.cod_acao
                          FROM administracao.modulo
                             , administracao.funcionalidade
                             , administracao.acao
                         WHERE modulo.cod_modulo                 = funcionalidade.cod_modulo
                           AND funcionalidade.cod_funcionalidade = acao.cod_funcionalidade
                      )
        ;

   DELETE
     FROM administracao.permissao
    WHERE numcgm = 0
      AND cod_acao IN ( SELECT acao.cod_acao
                          FROM administracao.modulo
                             , administracao.funcionalidade
                             , administracao.acao
                         WHERE modulo.cod_modulo                 = funcionalidade.cod_modulo
                           AND funcionalidade.cod_funcionalidade = acao.cod_funcionalidade
                      )
        ;

   CREATE OR REPLACE FUNCTION public.manutencao() RETURNS VOID AS $$
   DECLARE
      recRecno RECORD;
   BEGIN
      FOR recRecno
       IN   SELECT ano_exercicio
              FROM tmp_exer_permis
          GROUP BY ano_exercicio
          ORDER BY 1
      LOOP
         INSERT
           INTO administracao.permissao
              ( numcgm
              , cod_acao
              , ano_exercicio
              )
         SELECT 0
              , acao.cod_acao
              , recRecno.ano_exercicio
           FROM administracao.modulo
              , administracao.funcionalidade
              , administracao.acao
          WHERE (
                     (     modulo.cod_modulo = 2
                        OR modulo.cod_modulo = 4
                     )
                  OR (     modulo.cod_modulo = 30
                       AND cod_acao IN (1124,1335,1334,1127,1126,1125,1128)
                     )
                )
            AND modulo.cod_modulo                 = funcionalidade.cod_modulo
            AND funcionalidade.cod_funcionalidade = acao.cod_funcionalidade
            AND 0 = (
                      SELECT COALESCE(Count(1),0)
                        FROM administracao.permissao
                       WHERE permissao.ano_exercicio = recRecno.ano_exercicio
                         AND permissao.cod_acao      = acao.cod_acao
                         AND permissao.numcgm        = 0
                    )
              ;

      END LOOP;

      RETURN;
   END;
   $$ LANGUAGE 'plpgsql'
   ;

   SELECT         public.manutencao();
   DROP  FUNCTION public.manutencao();

   INSERT
     INTO administracao.permissao
        ( numcgm
        , cod_acao
        , ano_exercicio
        )
   SELECT numcgm
        , cod_acao
        , '2015'
     FROM administracao.permissao AS perm
    WHERE perm.ano_exercicio = '2014'
      AND 0 = (
                SELECT COALESCE(Count(1),0)
                  FROM administracao.permissao
                 WHERE permissao.ano_exercicio = '2015'
                   AND permissao.cod_acao      = perm.cod_acao
                   AND permissao.numcgm        = perm.numcgm
              )
        ;

   DELETE
     FROM Administracao.permissao
    WHERE numcgm != 0
      AND ano_exercicio = '2014'
        ;


   -- Procedimento 02
   INSERT
     INTO administracao.configuracao
        ( exercicio
        , cod_modulo
        , parametro
        , valor
        )
   SELECT '2015'
        , cod_modulo
        , parametro
        , valor
     FROM administracao.configuracao AS proximo
    WHERE exercicio='2014'
      AND NOT EXISTS (
                       SELECT 1
                         FROM administracao.configuracao
                        WHERE exercicio  = '2015'
                          and cod_modulo = proximo.cod_modulo
                          and parametro  = proximo.parametro
                     )
        ;

   -- No caso da prefeitura ter inserido uma nova entidade apos a criacao do orcamento 2014.
   INSERT
     INTO orcamento.entidade 
        ( exercicio
        , cod_entidade
        , numcgm
        , cod_responsavel
        , cod_resp_tecnico
        , cod_profissao
        , sequencia
        )
   SELECT '2015'
        , cod_entidade
        , numcgm
        , cod_responsavel
        , cod_resp_tecnico
        , cod_profissao
        , sequencia
     FROM orcamento.entidade AS proximo
    WHERE exercicio = '2014'
      AND NOT EXISTS (
                       SELECT 1
                         FROM orcamento.entidade
                        WHERE exercicio    = '2015'
                          AND cod_entidade = proximo.cod_entidade
                     )
        ;

   -- No caso da prefeitura ter inserido uma nova entidade apos a criacao do orcamento 2014.
   INSERT
     INTO orcamento.usuario_entidade
        ( exercicio
        , numcgm
        , cod_entidade
        )
   SELECT '2015'
        , numcgm
        , cod_entidade
     FROM orcamento.usuario_entidade as proximo
    WHERE exercicio = '2014'
      AND NOT EXISTS (
                       SELECT 1
                         FROM orcamento.usuario_entidade
                        WHERE exercicio    = '2015'
                          AND cod_entidade = proximo.cod_entidade
                     )
        ;


   INSERT
     INTO administracao.configuracao_entidade
        ( exercicio
        , cod_entidade
        , cod_modulo
        , parametro
        , valor
        )
   SELECT '2015'
        , cod_entidade
        , cod_modulo
        , parametro
        , valor
     FROM administracao.configuracao_entidade as proximo
    WHERE exercicio = '2014'
      AND NOT EXISTS (
                       SELECT 1
                         FROM administracao.configuracao_entidade
                        WHERE exercicio    = '2015'
                          AND cod_entidade = proximo.cod_entidade
                          AND cod_modulo   = proximo.cod_modulo
                          AND parametro    = proximo.parametro
                    )
        ;

   INSERT
     INTO administracao.entidade_rh
        ( exercicio
        , cod_entidade
        , schema_cod
        )
   SELECT '2015'
        , cod_entidade
        , schema_cod
     FROM administracao.entidade_rh as proximo
    WHERE exercicio = '2014'
      AND NOT EXISTS (
                       SELECT 1
                         FROM administracao.entidade_rh
                        WHERE exercicio    = '2015'
                          AND cod_entidade = proximo.cod_entidade
                     )
        ;

    -- ASSINATURA
    INSERT
      INTO administracao.assinatura
         ( exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , cargo
         )
    SELECT '2015' AS exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , cargo
      FROM administracao.assinatura AS proximo
     WHERE exercicio = '2014'
       AND NOT EXISTS ( SELECT 1
                          FROM administracao.assinatura
                         WHERE exercicio    = '2015'
                           AND cod_entidade = proximo.cod_entidade
                           AND numcgm       = proximo.numcgm
                           AND timestamp    = proximo.timestamp
                           AND cargo        = proximo.cargo
                      )
         ;

    INSERT
      INTO administracao.assinatura_crc
         ( exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , insc_crc
         )
    SELECT '2015' AS exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , insc_crc
      FROM administracao.assinatura_crc AS proximo
     WHERE exercicio = '2014'
       AND NOT EXISTS ( SELECT 1
                          FROM administracao.assinatura_crc
                         WHERE exercicio    = '2015'
                           AND cod_entidade = proximo.cod_entidade
                           AND numcgm       = proximo.numcgm
                           AND timestamp    = proximo.timestamp
                           AND insc_crc     = proximo.insc_crc
                      )
         ;

    INSERT
      INTO administracao.assinatura_modulo
         ( exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , cod_modulo
         )
    SELECT '2015' AS exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , cod_modulo
      FROM administracao.assinatura_modulo AS proximo
     WHERE exercicio = '2014'
       AND NOT EXISTS ( SELECT 1
                          FROM administracao.assinatura_modulo
                         WHERE exercicio    = '2015'
                           AND cod_entidade = proximo.cod_entidade
                           AND numcgm       = proximo.numcgm
                           AND timestamp    = proximo.timestamp
                           AND cod_modulo   = proximo.cod_modulo
                      )
         ;



   -- Procedimento 03
   UPDATE administracao.configuracao
      SET valor = '2015'
    WHERE exercicio = '2015'
      AND parametro = 'ano_exercicio'
        ;

   DELETE
     FROM administracao.configuracao
    WHERE exercicio = '2014'
     AND parametro  = 'diretorio'
       ;


-- VERIFICACAO DE UTILIZACAO OU NAO DOS MODULOS GF --
-----------------------------------------------------

CREATE OR REPLACE FUNCTION atualiza_gf() RETURNS VOID AS $$
DECLARE
    stAux       VARCHAR;
BEGIN

    PERFORM 1
       FROM orcamento.receita
      WHERE exercicio = '2014';

    IF FOUND THEN

        PERFORM 1
           FROM orcamento.conta_receita
          WHERE exercicio = '2015'
              ;

        IF NOT FOUND THEN
            RAISE EXCEPTION 'É necessário gerar o exercício seguinte na elaboração do orçamento para a aplicação do pacote da virada.'; 
        END IF;


            INSERT
              INTO empenho.permissao_autorizacao
                 ( exercicio
                 , numcgm
                 , num_unidade
                 , num_orgao
                 )
            SELECT '2015' AS exercicio
                 , numcgm
                 , num_unidade
                 , num_orgao
              FROM empenho.permissao_autorizacao AS proximo
             WHERE exercicio = '2014'
               AND NOT EXISTS (
                                SELECT 1
                                  FROM empenho.permissao_autorizacao
                                 WHERE exercicio   = '2015'
                                   AND cod_numcgm  = proximo.numcgm
                                   AND num_unidade = proximo.num_unidade
                                   AND num_orgao   = proximo.num_orgao
                              )
                 ;


            INSERT
              INTO contabilidade.posicao_plano
                 ( exercicio
                 , cod_posicao
                 , mascara
                 )
            SELECT '2015' AS exercicio
                 , cod_posicao
                 , mascara
              FROM contabilidade.posicao_plano AS proximo
             WHERE exercicio = '2014'
               AND NOT EXISTS (
                                SELECT 1
                                  FROM contabilidade.posicao_plano
                                 WHERE exercicio   = '2015'
                                   AND cod_posicao = proximo.cod_posicao
                              )
                 ;

            INSERT
              INTO contabilidade.classificacao_contabil
                 ( exercicio
                 , cod_classificacao
                 , nom_classificacao
                 )
            SELECT '2015' AS exercicio
                 , cod_classificacao
                 , nom_classificacao
              FROM contabilidade.classificacao_contabil AS proximo
             WHERE exercicio = '2014'
               AND NOT EXISTS (
                                SELECT 1
                                  FROM contabilidade.classificacao_contabil
                                 WHERE exercicio   = '2015'
                                   AND cod_classificacao = proximo.cod_classificacao
                              )
                 ;

            INSERT
              INTO contabilidade.sistema_contabil
                 ( exercicio
                 , cod_sistema
                 , nom_sistema
                 , grupos
                 )
            SELECT '2015' AS exercicio
                 , cod_sistema
                 , nom_sistema
                 , grupos
              FROM contabilidade.sistema_contabil AS proximo
             WHERE exercicio = '2014'
               AND NOT EXISTS (
                                SELECT 1
                                  FROM contabilidade.sistema_contabil
                                 WHERE exercicio   = '2015'
                                   AND cod_sistema = proximo.cod_sistema
                              )
                 ;

            INSERT
              INTO contabilidade.plano_conta
                 ( exercicio
                 , cod_conta
                 , nom_conta
                 , cod_classificacao
                 , cod_sistema
                 , cod_estrutural
                 , escrituracao
                 , natureza_saldo
                 , indicador_superavit
                 , funcao
                 , atributo_tcepe
                 )
            SELECT '2015' AS exercicio
                 , cod_conta
                 , nom_conta
                 , cod_classificacao
                 , cod_sistema
                 , cod_estrutural
                 , escrituracao
                 , natureza_saldo
                 , indicador_superavit
                 , funcao
                 , atributo_tcepe
              FROM contabilidade.plano_conta AS proximo
             WHERE exercicio = '2014'
               AND NOT EXISTS (
                                SELECT 1
                                  FROM contabilidade.plano_conta
                                 WHERE exercicio   = '2015'
                                   AND cod_conta   = proximo.cod_conta
                              )
                 ;

            INSERT
              INTO contabilidade.plano_analitica
                 ( exercicio
                 , cod_plano
                 , cod_conta
                 , natureza_saldo
                 )
            SELECT '2015' AS exercicio
                 , cod_plano
                 , cod_conta
                 , natureza_saldo
              FROM contabilidade.plano_analitica AS proximo
             WHERE exercicio = '2014'
               AND NOT EXISTS (
                                SELECT 1
                                  FROM contabilidade.plano_analitica
                                 WHERE exercicio   = '2015'
                                   AND cod_plano   = proximo.cod_plano
                              )
                 ;

            INSERT
              INTO contabilidade.plano_banco
                 ( exercicio
                 , cod_plano
                 , conta_corrente
                 , cod_entidade
                 , cod_banco
                 , cod_agencia
                 , cod_conta_corrente
                 )
            SELECT '2015' AS exercicio
                 , cod_plano
                 , conta_corrente
                 , cod_entidade
                 , cod_banco
                 , cod_agencia
                 , cod_conta_corrente
              FROM contabilidade.plano_banco AS proximo
             WHERE exercicio = '2014'
               AND NOT EXISTS (
                                SELECT 1
                                  FROM contabilidade.plano_banco
                                 WHERE exercicio   = '2015'
                                   AND cod_plano   = proximo.cod_plano
                              )
                 ;

            INSERT
              INTO contabilidade.plano_recurso
                 ( cod_plano
                 , exercicio
                 , cod_recurso
                 )
            SELECT proximo.cod_plano
                 , '2015' AS exercicio
                 , proximo.cod_recurso
              FROM contabilidade.plano_recurso AS proximo
              JOIN orcamento.recurso
                ON recurso.cod_recurso                                       = proximo.cod_recurso
               AND CAST((CAST(recurso.exercicio AS INTEGER) - 1) AS VARCHAR) = proximo.exercicio
             WHERE recurso.exercicio = '2015'
               AND NOT EXISTS (
                                SELECT 1
                                  FROM contabilidade.plano_recurso
                                 WHERE exercicio   = '2015'
                                   AND cod_plano   = proximo.cod_plano
                                   AND cod_recurso = proximo.cod_recurso
                              )
                 ;

            INSERT
              INTO contabilidade.classificacao_plano
                 ( exercicio
                 , cod_classificacao
                 , cod_conta
                 , cod_posicao
                 )
            SELECT '2015' AS exercicio
                 , cod_classificacao
                 , cod_conta
                 , cod_posicao
              FROM contabilidade.classificacao_plano AS proximo
             WHERE exercicio = '2014'
               AND NOT EXISTS (
                                SELECT 1
                                  FROM contabilidade.classificacao_plano
                                 WHERE exercicio   = '2015'
                                   AND cod_classificacao   = proximo.cod_classificacao
                                   AND cod_conta           = proximo.cod_conta
                                   AND cod_posicao         = proximo.cod_posicao
                              )
                 ;




                    INSERT
                      INTO contabilidade.configuracao_lancamento_debito
                         ( exercicio
                         , cod_conta
                         , cod_conta_despesa
                         , estorno
                         , tipo
                         , rpps
                         )
                    SELECT '2015' AS exercicio
                         , proximo.cod_conta
                         , proximo.cod_conta_despesa
                         , proximo.estorno
                         , proximo.tipo
                         , proximo.rpps
                      FROM contabilidade.configuracao_lancamento_debito AS proximo
                      JOIN orcamento.conta_despesa
                        ON conta_despesa.cod_conta                                         = proximo.cod_conta_despesa
                       AND CAST((CAST(conta_despesa.exercicio AS INTEGER) - 1) AS VARCHAR) = proximo.exercicio
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM contabilidade.configuracao_lancamento_debito
                                         WHERE exercicio = '2015'
                                           AND cod_conta         = proximo.cod_conta
                                           AND cod_conta_despesa = proximo.cod_conta_despesa
                                           AND estorno           = proximo.estorno
                                      )
                         ;

                    INSERT
                      INTO contabilidade.configuracao_lancamento_credito
                         ( exercicio
                         , cod_conta
                         , cod_conta_despesa
                         , estorno
                         , tipo
                         , rpps
                         )
                    SELECT '2015' AS exercicio
                         , proximo.cod_conta
                         , proximo.cod_conta_despesa
                         , proximo.estorno
                         , proximo.tipo
                         , proximo.rpps
                      FROM contabilidade.configuracao_lancamento_credito AS proximo
                      JOIN orcamento.conta_despesa
                        ON conta_despesa.cod_conta                                         = proximo.cod_conta_despesa
                       AND CAST((CAST(conta_despesa.exercicio AS INTEGER) - 1) AS VARCHAR) = proximo.exercicio
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM contabilidade.configuracao_lancamento_credito
                                         WHERE exercicio = '2015'
                                           AND cod_conta         = proximo.cod_conta
                                           AND cod_conta_despesa = proximo.cod_conta_despesa
                                           AND estorno           = proximo.estorno
                                      )
                         ;

                    INSERT
                      INTO contabilidade.configuracao_lancamento_receita
                         ( exercicio
                         , cod_conta
                         , cod_conta_receita
                         , estorno
                         )
                    SELECT '2015' AS exercicio
                         , proximo.cod_conta
                         , proximo.cod_conta_receita
                         , proximo.estorno
                      FROM contabilidade.configuracao_lancamento_receita AS proximo
                      JOIN orcamento.conta_receita
                        ON conta_receita.cod_conta                                         = proximo.cod_conta_receita
                       AND CAST((CAST(conta_receita.exercicio AS INTEGER) - 1) AS VARCHAR) = proximo.exercicio
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM contabilidade.configuracao_lancamento_receita
                                         WHERE exercicio = '2015'
                                           AND cod_conta         = proximo.cod_conta
                                           AND cod_conta_receita = proximo.cod_conta_receita
                                           AND estorno           = proximo.estorno
                                      )
                         ;


                -----------------------------------------------------------------------------------------
                -- TCE TO 

                    INSERT
                      INTO tceto.credor
                         ( exercicio
                         , numcgm
                         , tipo
                         )
                    SELECT '2015' AS exercicio
                         , numcgm
                         , tipo
                      FROM tceto.credor AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tceto.credor
                                         WHERE exercicio = '2015'
                                           AND numcgm  = proximo.numcgm
                                      )
                         ;

                    INSERT
                      INTO tceto.uniorcam
                         ( exercicio
                         , numcgm
                         , num_unidade
                         , num_orgao
                         , identificador
                         )
                    SELECT '2015' AS exercicio
                         , numcgm
                         , num_unidade
                         , num_orgao
                         , identificador
                      FROM tceto.uniorcam AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tceto.uniorcam
                                         WHERE exercicio = '2015'
                                           AND num_unidade = proximo.num_unidade
                                           AND num_orgao   = proximo.num_orgao
                                      )
                         ;

                -----------------------------------------------------------------------------------------
                -- TCE AL 

                    INSERT
                      INTO tceal.ocorrencia_funcional
                         ( exercicio
                         , cod_ocorrencia
                         , descricao
                         )
                    SELECT '2014' AS exercicio
                         , cod_ocorrencia
                         , descricao
                      FROM tceal.ocorrencia_funcional AS proximo
                     WHERE proximo.exercicio = '2013'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tceal.ocorrencia_funcional
                                         WHERE exercicio = '2014'
                                           AND cod_ocorrencia  = proximo.cod_ocorrencia
                                      )
                         ;
                    INSERT
                      INTO tceal.ocorrencia_funcional
                         ( exercicio
                         , cod_ocorrencia
                         , descricao
                         )
                    SELECT '2015' AS exercicio
                         , cod_ocorrencia
                         , descricao
                      FROM tceal.ocorrencia_funcional AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tceal.ocorrencia_funcional
                                         WHERE exercicio = '2015'
                                           AND cod_ocorrencia  = proximo.cod_ocorrencia
                                      )
                         ;

                    INSERT
                      INTO tceal.credor
                         ( exercicio
                         , tipo
                         , numcgm
                         )
                    SELECT '2015' AS exercicio
                         , tipo
                         , numcgm
                      FROM tceal.credor AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tceal.credor
                                         WHERE exercicio = '2015'
                                           AND numcgm = proximo.numcgm
                                      )
                         ;

                    INSERT
                      INTO tceal.de_para_tipo_cargo
                         ( exercicio
                         , cod_entidade
                         , cod_sub_divisao
                         , cod_tipo_cargo_tce
                         )
                    SELECT '2015' AS exercicio
                         , cod_entidade
                         , cod_sub_divisao
                         , cod_tipo_cargo_tce
                      FROM tceal.de_para_tipo_cargo AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tceal.de_para_tipo_cargo
                                         WHERE exercicio = '2015'
                                           AND cod_entidade    = proximo.cod_entidade
                                           AND cod_sub_divisao = proximo.cod_sub_divisao
                                      )
                         ;

                    INSERT
                      INTO tceal.ocorrencia_funcional_assentamento
                         ( exercicio
                         , cod_entidade
                         , cod_ocorrencia
                         , cod_assentamento
                         )
                    SELECT '2015' AS exercicio
                         , cod_entidade
                         , cod_ocorrencia
                         , cod_assentamento
                      FROM tceal.ocorrencia_funcional_assentamento AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tceal.ocorrencia_funcional_assentamento
                                         WHERE exercicio = '2015'
                                           AND cod_entidade     = proximo.cod_entidade
                                           AND cod_ocorrencia   = proximo.cod_ocorrencia
                                           AND cod_assentamento = proximo.cod_assentamento
                                      )
                         ;

                    INSERT
                      INTO tceal.uniorcam
                         ( exercicio
                         , numcgm
                         , num_unidade
                         , num_orgao
                         , identificador
                         )
                    SELECT '2015' AS exercicio
                         , numcgm
                         , num_unidade
                         , num_orgao
                         , identificador
                      FROM tceal.uniorcam AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tceal.uniorcam
                                         WHERE exercicio = '2015'
                                           AND num_unidade  = proximo.num_unidade
                                           AND num_orgao    = proximo.num_orgao
                                      )
                         ;

                -----------------------------------------------------------------------------------------
                -- TCE PE 

                    INSERT
                      INTO tcepe.modalidade_despesa
                         ( exercicio
                         , cod_modalidade
                         , modalidade
                         )
                    SELECT '2015' AS exercicio
                         , cod_modalidade
                         , modalidade
                      FROM tcepe.modalidade_despesa AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcepe.modalidade_despesa
                                         WHERE exercicio = '2015'
                                           AND cod_modalidade  = proximo.cod_modalidade
                                      )
                         ;

                    INSERT
                      INTO tcepe.agente_eletivo
                         ( exercicio
                         , cod_entidade
                         , cod_cargo
                         , cod_tipo_remuneracao
                         , cod_tipo_norma
                         , cod_norma
                         )
                    SELECT '2015' AS exercicio
                         , cod_entidade
                         , cod_cargo
                         , cod_tipo_remuneracao
                         , cod_tipo_norma
                         , cod_norma
                      FROM tcepe.agente_eletivo AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcepe.agente_eletivo
                                         WHERE exercicio = '2015'
                                           AND cod_entidade  = proximo.cod_entidade
                                           AND cod_cargo     = proximo.cod_cargo
                                      )
                         ;

                    INSERT
                      INTO tcepe.cgm_tipo_credor
                         ( exercicio
                         , cgm_credor
                         , cod_tipo_credor
                         )
                    SELECT '2015' AS exercicio
                         , cgm_credor
                         , cod_tipo_credor
                      FROM tcepe.cgm_tipo_credor AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcepe.cgm_tipo_credor
                                         WHERE exercicio = '2015'
                                           AND cgm_credor = proximo.cgm_credor
                                      )
                         ;

                    INSERT
                      INTO tcepe.codigo_fonte_recurso
                         ( exercicio
                         , cod_recurso
                         , cod_fonte
                         )
                    SELECT '2015' AS exercicio
                         , cod_recurso
                         , cod_fonte
                      FROM tcepe.codigo_fonte_recurso AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcepe.codigo_fonte_recurso
                                         WHERE exercicio = '2015'
                                           AND cod_recurso  = proximo.cod_recurso
                                      )
                         ;

                    INSERT
                      INTO tcepe.fonte_recurso_local
                         ( exercicio
                         , cod_fonte
                         , cod_entidade
                         , cod_local
                         )
                    SELECT '2015' AS exercicio
                         , cod_fonte
                         , cod_entidade
                         , cod_local
                      FROM tcepe.fonte_recurso_local AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcepe.fonte_recurso_local
                                         WHERE exercicio = '2015'
                                           AND cod_fonte    = proximo.cod_fonte
                                           AND cod_entidade = proximo.cod_entidade
                                           AND cod_local    = proximo.cod_local
                                      )
                         ;

                    INSERT
                      INTO tcepe.fonte_recurso_lotacao
                         ( exercicio
                         , cod_fonte
                         , cod_entidade
                         , cod_orgao
                         )
                    SELECT '2015' AS exercicio
                         , cod_fonte
                         , cod_entidade
                         , cod_orgao
                      FROM tcepe.fonte_recurso_lotacao AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcepe.fonte_recurso_lotacao
                                         WHERE exercicio = '2015'
                                           AND cod_fonte    = proximo.cod_fonte
                                           AND cod_entidade = proximo.cod_entidade
                                           AND cod_orgao    = proximo.cod_orgao
                                      )
                         ;

                    INSERT
                      INTO tcepe.modalidade_despesa
                         ( exercicio
                         , cod_modalidade
                         , modalidade
                         )
                    SELECT '2015' AS exercicio
                         , cod_modalidade
                         , modalidade
                      FROM tcepe.modalidade_despesa AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcepe.modalidade_despesa
                                         WHERE exercicio = '2015'
                                           AND cod_modalidade  = proximo.cod_modalidade
                                      )
                         ;


                -----------------------------------------------------------------------------------------
                -- TCE MG 

                    INSERT
                      INTO tcemg.conta_bancaria
                         ( exercicio
                         , cod_conta
                         , cod_entidade
                         , sequencia
                         , cod_tipo_aplicacao
                         , cod_ctb_anterior
                         )
                    SELECT '2015' AS exercicio
                         , cod_conta
                         , cod_entidade
                         , sequencia
                         , cod_tipo_aplicacao
                         , cod_ctb_anterior
                      FROM tcemg.conta_bancaria AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcemg.conta_bancaria
                                         WHERE exercicio = '2015'
                                           AND cod_conta = proximo.cod_conta
                                      )
                         ;

                    INSERT
                      INTO tcemg.uniorcam
                         ( exercicio
                         , num_unidade
                         , num_orgao
                         , identificador
                         , cgm_ordenador
                         , exercicio_atual
                         , num_orgao_atual
                         , num_unidade_atual
                         )
                    SELECT '2015' AS exercicio
                         , num_unidade
                         , num_orgao
                         , identificador
                         , cgm_ordenador
                         , exercicio_atual
                         , num_orgao_atual
                         , num_unidade_atual
                      FROM tcemg.uniorcam AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcemg.uniorcam
                                         WHERE exercicio = '2015'
                                           AND num_unidade = proximo.num_unidade
                                           AND num_orgao   = proximo.num_orgao
                                      )
                         ;


                -----------------------------------------------------------------------------------------
                -- TCE PB 

                    INSERT
                      INTO tcepb.tipo_obra
                         ( exercicio
                         , cod_tipo
                         , descricao
                         )
                    SELECT '2015' AS exercicio
                         , cod_tipo
                         , descricao
                      FROM tcepb.tipo_obra AS proximo
                     WHERE proximo.exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcepb.tipo_obra
                                         WHERE exercicio = '2015'
                                           AND cod_tipo  = proximo.cod_tipo
                                      )
                         ;

                    INSERT
                      INTO tcepb.tipo_situacao
                         ( exercicio
                         , cod_tipo
                         , descricao
                         )
                    SELECT '2015' AS exercicio
                         , cod_tipo
                         , descricao
                      FROM tcepb.tipo_situacao AS proximo
                     WHERE exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcepb.tipo_situacao
                                         WHERE exercicio = '2015'
                                           AND cod_tipo  = proximo.cod_tipo
                                      )
                         ;

                    INSERT
                      INTO tcepb.tipo_fonte_obras
                         ( exercicio
                         , cod_tipo
                         , descricao
                         )
                    SELECT '2015' AS exercicio
                         , cod_tipo
                         , descricao
                      FROM tcepb.tipo_fonte_obras AS proximo
                     WHERE exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcepb.tipo_fonte_obras
                                         WHERE exercicio = '2015'
                                           AND cod_tipo  = proximo.cod_tipo
                                      )
                         ;

                    INSERT
                      INTO tcepb.tipo_categoria_obra
                         ( exercicio
                         , cod_tipo
                         , descricao
                         )
                    SELECT '2015' AS exercicio
                         , cod_tipo
                         , descricao
                      FROM tcepb.tipo_categoria_obra AS proximo
                     WHERE exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcepb.tipo_categoria_obra
                                         WHERE exercicio = '2015'
                                           AND cod_tipo  = proximo.cod_tipo
                                      )
                         ;

                   INSERT
                     INTO tcepb.tipo_origem_recurso
                        ( exercicio
                        , cod_tipo
                        , descricao
                        )
                   SELECT '2015'
                        , cod_tipo
                        , descricao
                     FROM tcepb.tipo_origem_recurso AS proximo
                    WHERE exercicio='2014'
                      AND NOT EXISTS (
                                       SELECT 1
                                         FROM tcepb.tipo_origem_recurso
                                        WHERE exercicio  = '2015'
                                          and cod_tipo = proximo.cod_tipo
                                     )
                        ;

                    INSERT
                      INTO tcepb.recurso
                         ( exercicio
                         , cod_recurso
                         , cod_tipo
                         )
                    SELECT '2015' AS exercicio
                         , cod_recurso
                         , cod_tipo
                      FROM tcepb.recurso AS proximo
                     WHERE exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcepb.recurso
                                         WHERE exercicio = '2015'
                                           AND cod_recurso  = proximo.cod_recurso
                                      )
                         ;

                    INSERT
                      INTO tcepb.programa_objetivo_milenio
                         ( exercicio
                         , cod_programa
                         , cod_tipo_objetivo
                         )
                    SELECT '2015' AS exercicio
                         , cod_programa
                         , cod_tipo_objetivo
                      FROM tcepb.programa_objetivo_milenio AS proximo
                     WHERE exercicio = '2014'
                       AND NOT EXISTS (
                                        SELECT 1
                                          FROM tcepb.programa_objetivo_milenio
                                         WHERE exercicio = '2015'
                                           AND cod_programa = proximo.cod_programa
                                      )
                         ;


                -----------------------------------------------------------------------------------------
                -- TCE RS 

                INSERT
                  INTO tcers.credor
                     ( exercicio
                     , numcgm
                     , tipo
                     )
                SELECT '2015'
                     , numcgm
                     , tipo
                  FROM tcers.credor AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.credor
                                     WHERE exercicio = '2015'
                                       AND numcgm    = proximo.numcgm
                                  )
                     ;

                INSERT
                  INTO tcers.uniorcam
                     ( numcgm
                     , exercicio
                     , num_unidade
                     , num_orgao
                     , identificador
                     )
                SELECT numcgm
                     , '2015'
                     , num_unidade
                     , num_orgao
                     , identificador
                  FROM tcers.uniorcam AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.uniorcam
                                     WHERE exercicio   = '2015'
                                       AND numcgm      = proximo.numcgm
                                       AND num_unidade = proximo.num_unidade
                                       AND num_orgao   = proximo.num_orgao
                                  )
                     ;

                INSERT
                  INTO tcers.rd_extra
                     ( cod_conta
                     , exercicio
                     , classificacao
                     )
                SELECT cod_conta
                     , '2015'
                     , classificacao
                  FROM tcers.rd_extra AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (

                                    SELECT 1
                                      FROM tcers.rd_extra
                                     WHERE exercicio = '2015'
                                       AND cod_conta = proximo.cod_conta
                                  )
                     ;

                INSERT
                  INTO tcers.modelo_lrf
                     ( exercicio
                     , cod_modelo
                     , nom_modelo
                     , nom_modelo_orcamento
                     )
                SELECT '2015'
                     , cod_modelo
                     , nom_modelo
                     , nom_modelo_orcamento
                  FROM  tcers.modelo_lrf AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.modelo_lrf
                                     WHERE exercicio='2015'
                                       AND cod_modelo = proximo.cod_modelo
                                  )
                     ;

                INSERT
                  INTO tcers.quadro_modelo_lrf
                     ( exercicio
                     , cod_modelo
                     , cod_quadro
                     , nom_quadro
                     )
                SELECT '2015'
                     , cod_modelo
                     , cod_quadro
                     , nom_quadro
                  FROM tcers.quadro_modelo_lrf AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.quadro_modelo_lrf
                                     WHERE exercicio='2015'
                                       AND cod_modelo = proximo.cod_modelo
                                       AND cod_quadro = proximo.cod_quadro
                                  )
                     ;


                -----------------------------------------------------------------------------------------
                -- TCM GO

                INSERT
                  INTO tcmgo.orgao
                     ( exercicio
                     , num_orgao
                     , numcgm_orgao
                     , numcgm_contador
                     , cod_tipo
                     , crc_contador
                     , uf_crc_contador
                     )
                SELECT '2015'
                     , num_orgao
                     , numcgm_orgao
                     , numcgm_contador
                     , cod_tipo
                     , crc_contador
                     , uf_crc_contador
                  FROM tcmgo.orgao AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.orgao
                                     WHERE exercicio='2015'
                                       AND num_orgao = proximo.num_orgao
                                  )
                     ;

                INSERT
                  INTO tcmgo.orgao_controle_interno
                     ( exercicio
                     , num_orgao
                     , numcgm
                     )
                SELECT '2015'
                     , num_orgao
                     , numcgm
                  FROM tcmgo.orgao_controle_interno AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.orgao_controle_interno
                                     WHERE exercicio='2015'
                                       AND num_orgao = proximo.num_orgao
                                  )
                     ;

                INSERT
                  INTO tcmgo.elemento_de_para
                     ( exercicio
                     , cod_conta
                     , estrutural
                     )
                SELECT '2015'
                     , cod_conta
                     , estrutural
                  FROM tcmgo.elemento_de_para AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.elemento_de_para
                                     WHERE exercicio='2015'
                                       AND cod_conta = proximo.cod_conta
                                  )
                     ;

                INSERT
                  INTO tcmgo.orgao_representante
                     ( exercicio
                     , num_orgao
                     , numcgm
                     )
                SELECT '2015'
                     , num_orgao
                     , numcgm
                  FROM tcmgo.orgao_representante AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.orgao_representante
                                     WHERE exercicio='2015'
                                       AND num_orgao = proximo.num_orgao
                                  )
                     ;

                INSERT
                  INTO tcmgo.tipo_retencao
                     ( exercicio
                     , cod_tipo
                     , descricao
                     )
                SELECT '2015'
                     , cod_tipo
                     , descricao
                  FROM tcmgo.tipo_retencao AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.tipo_retencao
                                     WHERE exercicio = '2015'
                                       AND cod_tipo = proximo.cod_tipo
                                  )
                     ;

                INSERT
                  INTO tcmgo.orgao_plano_banco
                     ( exercicio
                     , cod_plano
                     , num_orgao
                     )
                SELECT '2015'
                     , cod_plano
                     , num_orgao
                  FROM tcmgo.orgao_plano_banco AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.orgao_plano_banco
                                     WHERE exercicio = '2015'
                                       AND cod_plano = proximo.cod_plano
                                       AND num_orgao = proximo.num_orgao
                                  )
                     ;


                -----------------------------------------------------------------------------------------
                -- TCE RN

                INSERT
                  INTO tcern.contrato_aditivo
                     ( exercicio
                     , num_convenio
                     , cod_entidade
                     , num_contrato_aditivo
                     , exercicio_aditivo
                     , cod_processo
                     , exercicio_processo
                     , bimestre
                     , cod_objeto
                     , valor_aditivo
                     , dt_inicio_vigencia
                     , dt_termino_vigencia
                     , dt_assinatura
                     , dt_publicacao
                     )
                SELECT '2015'
                     , num_convenio
                     , cod_entidade
                     , num_contrato_aditivo
                     , exercicio_aditivo
                     , cod_processo
                     , exercicio_processo
                     , bimestre
                     , cod_objeto
                     , valor_aditivo
                     , dt_inicio_vigencia
                     , dt_termino_vigencia
                     , dt_assinatura
                     , dt_publicacao
                  FROM tcern.contrato_aditivo AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.contrato_aditivo
                                     WHERE exercicio='2015'
                                       AND num_convenio         = proximo.num_convenio
                                       AND cod_entidade         = proximo.cod_entidade
                                       AND num_contrato_aditivo = proximo.num_contrato_aditivo
                                       AND exercicio_aditivo    = proximo.exercicio_aditivo
                                  )
                     ;

                INSERT
                  INTO tcern.obra
                     ( exercicio
                     , cod_entidade
                     , num_obra
                     , obra
                     , objetivo
                     , localizacao
                     , cod_cidade
                     , cod_recurso_1
                     , cod_recurso_2
                     , cod_recurso_3
                     , valor_recurso_1
                     , valor_recurso_2
                     , valor_recurso_3
                     , valor_orcamento_base
                     , projeto_existente
                     , observacao
                     , latitude
                     , longitude
                     , rdc
                     )
                SELECT '2015'
                     , cod_entidade
                     , num_obra
                     , obra
                     , objetivo
                     , localizacao
                     , cod_cidade
                     , cod_recurso_1
                     , cod_recurso_2
                     , cod_recurso_3
                     , valor_recurso_1
                     , valor_recurso_2
                     , valor_recurso_3
                     , valor_orcamento_base
                     , projeto_existente
                     , observacao
                     , latitude
                     , longitude
                     , rdc
                  FROM tcern.obra AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.obra
                                     WHERE exercicio='2015'
                                       AND cod_entidade = proximo.cod_entidade
                                       AND num_obra     = proximo.num_obra
                                  )
                     ;

                INSERT
                  INTO tcern.unidade_orcamentaria
                     ( exercicio
                     , id
                     , cod_institucional
                     , cgm_unidade_orcamentaria
                     , cod_norma
                     , id_unidade_gestora
                     , situacao
                     , num_unidade
                     , num_orgao
                     )
                SELECT '2015'
                     , id
                     , cod_institucional
                     , cgm_unidade_orcamentaria
                     , cod_norma
                     , id_unidade_gestora
                     , situacao
                     , num_unidade
                     , num_orgao
                  FROM tcern.unidade_orcamentaria AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.unidade_orcamentaria
                                     WHERE exercicio='2015'
                                       AND id = proximo.id
                                  )
                     ;


                INSERT
                  INTO tcern.convenio
                     ( exercicio
                     , cod_entidade
                     , num_convenio
                     , cod_processo
                     , exercicio_processo
                     , numcgm_recebedor
                     , cod_objeto
                     , cod_recurso_1
                     , cod_recurso_2
                     , cod_recurso_3
                     , valor_recurso_1
                     , valor_recurso_2
                     , valor_recurso_3
                     , dt_inicio_vigencia
                     , dt_termino_vigencia
                     , dt_assinatura
                     , dt_publicacao
                     )
                SELECT '2015'
                     , cod_entidade
                     , num_convenio
                     , cod_processo
                     , exercicio_processo
                     , numcgm_recebedor
                     , cod_objeto
                     , cod_recurso_1
                     , cod_recurso_2
                     , cod_recurso_3
                     , valor_recurso_1
                     , valor_recurso_2
                     , valor_recurso_3
                     , dt_inicio_vigencia
                     , dt_termino_vigencia
                     , dt_assinatura
                     , dt_publicacao
                  FROM tcern.convenio AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.convenio
                                     WHERE exercicio='2015'
                                       AND cod_entidade = proximo.cod_entidade
                                       AND num_convenio = proximo.num_convenio
                                  )
                     ;

                INSERT
                  INTO tcern.unidade_gestora
                     ( exercicio
                     , id 
                     , cod_institucional
                     , cgm_unidade
                     , personalidade
                     , administracao
                     , natureza
                     , cod_norma
                     , situacao
                     )
                SELECT '2015'
                     , id 
                     , cod_institucional
                     , cgm_unidade
                     , personalidade
                     , administracao
                     , natureza
                     , cod_norma
                     , situacao
                  FROM tcern.unidade_gestora AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.unidade_gestora
                                     WHERE exercicio='2015'
                                       AND id = proximo.id
                                  )
                     ;


                -----------------------------------------------------------------------------------------
                -- STN

                INSERT
                  INTO stn.vinculo_stn_receita
                     ( exercicio
                     , cod_receita
                     , cod_tipo
                     )
                SELECT '2015'
                     , cod_receita
                     , cod_tipo
                  FROM stn.vinculo_stn_receita AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM stn.vinculo_stn_receita
                                     WHERE exercicio='2015'
                                       AND cod_receita = proximo.cod_receita
                                       AND cod_tipo    = proximo.cod_tipo
                                  )
                     ;

                INSERT
                  INTO stn.vinculo_recurso
                     ( exercicio
                     , cod_entidade
                     , num_orgao
                     , num_unidade
                     , cod_recurso
                     , cod_vinculo
                     , cod_tipo
                     )
                SELECT '2015'
                     , cod_entidade
                     , num_orgao
                     , num_unidade
                     , cod_recurso
                     , cod_vinculo
                     , cod_tipo
                  FROM stn.vinculo_recurso AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM stn.vinculo_recurso
                                     WHERE exercicio='2015'
                                       AND cod_entidade = proximo.cod_entidade
                                       AND num_orgao    = proximo.num_orgao
                                       AND num_unidade  = proximo.num_unidade
                                       AND cod_recurso  = proximo.cod_recurso
                                       AND cod_vinculo  = proximo.cod_vinculo
                                       AND cod_tipo     = proximo.cod_tipo
                                  )
                     ;

                INSERT
                  INTO stn.riscos_fiscais
                     ( exercicio
                     , cod_risco
                     , cod_entidade
                     , descricao 
                     , valor
                     )
                SELECT '2015'
                     , cod_risco
                     , cod_entidade
                     , descricao
                     , valor
                  FROM stn.riscos_fiscais AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM stn.riscos_fiscais
                                     WHERE exercicio='2015'
                                       AND cod_risco    = proximo.cod_risco
                                       AND cod_entidade = proximo.cod_entidade
                                  )
                     ;

                INSERT
                  INTO stn.recurso_rreo_anexo_14
                     ( exercicio
                     , cod_recurso
                     )
                SELECT '2015'
                     , cod_recurso
                  FROM stn.recurso_rreo_anexo_14 AS proximo
                 WHERE exercicio = '2014'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM stn.recurso_rreo_anexo_14
                                     WHERE exercicio='2015'
                                       AND cod_recurso = proximo.cod_recurso
                                  )
                     ;



    ELSE

        INSERT
          INTO orcamento.conta_receita
             ( exercicio
             , cod_conta
             , cod_norma
             , descricao
             , cod_estrutural
             )
        SELECT '2015' AS exercicio
             , cod_conta
             , cod_norma
             , descricao
             , cod_estrutural
          FROM orcamento.conta_receita AS proximo
         WHERE exercicio = '2014'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.conta_receita
                             WHERE exercicio = '2015'
                               AND cod_conta = proximo.cod_conta
                          )
             ;
        INSERT
          INTO orcamento.posicao_receita
             ( exercicio
             , cod_posicao
             , mascara
             , cod_tipo
             )
        SELECT '2015' AS exercicio
             , cod_posicao
             , mascara
             , cod_tipo
          FROM orcamento.posicao_receita AS proximo
         WHERE exercicio = '2014'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.posicao_receita
                             WHERE exercicio   = '2015'
                               AND cod_tipo    = proximo.cod_tipo
                               AND cod_posicao = proximo.cod_posicao
                          )
             ;
        INSERT
          INTO orcamento.classificacao_receita
             ( exercicio
             , cod_posicao
             , cod_conta
             , cod_classificacao
             , cod_tipo
             )
        SELECT '2015' AS exercicio
             , cod_posicao
             , cod_conta
             , cod_classificacao
             , cod_tipo
          FROM orcamento.classificacao_receita AS proximo
         WHERE exercicio = '2014'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.classificacao_receita
                             WHERE exercicio   = '2015'
                               AND cod_tipo    = proximo.cod_tipo
                               AND cod_posicao = proximo.cod_posicao
                               AND cod_conta   = proximo.cod_conta
                          )
             ;

        INSERT
          INTO orcamento.conta_despesa
             ( exercicio
             , cod_conta
             , descricao
             , cod_estrutural
             )
        SELECT '2015' AS exercicio
             , cod_conta
             , descricao
             , cod_estrutural
          FROM orcamento.conta_despesa AS proximo
         WHERE exercicio = '2014'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.conta_despesa
                             WHERE exercicio = '2015'
                               AND cod_conta = proximo.cod_conta
                          )
             ;
        INSERT
          INTO orcamento.posicao_despesa
             ( exercicio
             , cod_posicao
             , mascara
             )
        SELECT '2015' AS exercicio
             , cod_posicao
             , mascara
          FROM orcamento.posicao_despesa AS proximo
         WHERE exercicio = '2014'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.posicao_despesa
                             WHERE exercicio   = '2015'
                               AND cod_posicao = proximo.cod_posicao
                          )
             ;
        INSERT
          INTO orcamento.classificacao_despesa
             ( exercicio
             , cod_conta
             , cod_posicao
             , cod_classificacao
             )
        SELECT '2015' AS exercicio
             , cod_conta
             , cod_posicao
             , cod_classificacao
          FROM orcamento.classificacao_despesa AS proximo
         WHERE exercicio = '2014'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.classificacao_despesa
                             WHERE exercicio   = '2015'
                               AND cod_conta   = proximo.cod_conta
                               AND cod_posicao = proximo.cod_posicao
                          )
             ;

        INSERT
          INTO contabilidade.historico_contabil
        SELECT cod_historico
             , '2015' as exercicio
             , nom_historico
             , complemento
             , historico_interno
          FROM contabilidade.historico_contabil AS proximo
         WHERE exercicio = '2014'
           AND NOT EXISTS (
                            SELECT 1
                              FROM contabilidade.historico_contabil
                             WHERE exercicio     = '2015'
                               AND cod_historico = proximo.cod_historico
                          )
             ;

        INSERT
          INTO contabilidade.posicao_plano
        SELECT '2015' as exercicio
             , cod_posicao,mascara
          FROM contabilidade.posicao_plano AS proximo
         WHERE exercicio = '2014'
           AND NOT EXISTS (
                            SELECT 1
                              FROM contabilidade.posicao_plano
                             WHERE exercicio   = '2015'
                               AND cod_posicao = proximo.cod_posicao
                          )
             ;
        
        UPDATE administracao.configuracao
            SET valor = '1'
          WHERE parametro = 'multiplos_boletim'
            AND cod_modulo = 30
              ;
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        atualiza_gf();
DROP FUNCTION atualiza_gf();


--
-- Procedimento 18
--
    DELETE
      FROM administracao.configuracao
     WHERE parametro = 'virada_GF'
       AND exercicio = '2015'
         ;


--------------------------------
-- Replicacao de ima.codigo_dirf
--------------------------------

    SELECT atualizarbanco('
                           INSERT
                             INTO ima.codigo_dirf
                                ( exercicio
                                , cod_dirf
                                , tipo
                                , descricao
                                )
                           SELECT ''2015'' AS exercicio
                                , cod_dirf
                                , tipo
                                , descricao
                             FROM ima.codigo_dirf AS proximo
                            WHERE exercicio = ''2014''
                              AND NOT EXISTS (
                                               SELECT 1
                                                 FROM ima.codigo_dirf
                                                WHERE exercicio = ''2015''
                                                  AND cod_dirf  = proximo.cod_dirf
                                                  AND tipo      = proximo.tipo
                          )
                                ;
                          ')
         ;


----------------
-- Ticket #22408
----------------

ALTER TABLE tceto.norma_detalhe ADD COLUMN cod_norma_alteracao INTEGER NOT NULL;

ALTER TABLE normas.norma_detalhe_al ALTER COLUMN descricao_alteracao TYPE VARCHAR(400);

