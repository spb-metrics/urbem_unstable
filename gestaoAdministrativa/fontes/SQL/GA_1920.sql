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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: GA_1920.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.92.0
*/


----------------
-- Ticket #13633
----------------

DELETE
  FROM administracao.permissao
 WHERE cod_acao
    IN (
            SELECT cod_acao
              FROM administracao.acao
             WHERE cod_funcionalidade
                IN (59, 58)
       );

DELETE
  FROM administracao.auditoria
 WHERE cod_acao
    IN (
            SELECT cod_acao
              FROM administracao.acao
             WHERE cod_funcionalidade
                IN (59, 58)
       );

DELETE
  FROM administracao.acao
 WHERE cod_funcionalidade
    IN (58, 59);

DELETE
  FROM administracao.funcionalidade
 WHERE cod_funcionalidade
    IN (58, 59);


----------------
-- Ticket #11146
----------------

ALTER TABLE sw_atributo_protocolo ADD COLUMN obrigatorio BOOLEAN NOT NULL DEFAULT FALSE;


----------------
-- Ticket #12852
----------------

CREATE OR REPLACE FUNCTION manutencao () RETURNS VOID AS $$
DECLARE

    crUnidade       REFCURSOR;
    reUnidade       RECORD;
    stSql           VARCHAR := '    SELECT *
                                      FROM administracao.unidade_medida
                                     WHERE nom_unidade IN ( \'Ampola\'        , \'Anastubete\'    , \'Balde\'         , \'Barra\'         , \'Bisnaga\'       
                                                          , \'Blister\'       , \'Bloco\'         , \'Bobina\'        , \'Bombona\'       , \'Cápsula\'       
                                                          , \'Carga\'         , \'Cartela\'       , \'Cento\'         , \'cm/coluna\'     , \'Coleção\'       
                                                          , \'Comprimido\'    , \'Conjunto\'      , \'Diária\'        , \'Diskus\'        , \'Dragea\'        
                                                          , \'Embalagem\'     , \'Envelope\'      , \'Estojo\'        , \'Flaconete\'     , \'Folha\'         
                                                          , \'Frasco\'        , \'Frasco-Ampola\' , \'Galão\'         , \'Garrafa\'       , \'Grosa\'         
                                                          , \'Hora/Mês\'      , \'Jogo\'          , \'Kit\'           , \'Maço\'          , \'Metro Linear\'  
                                                          , \'Minutos/Mês\'   , \'Pote\'          , \'Rolo\'          , \'Saca\'          , \'Sache\'         
                                                          , \'Saco\'          , \'Seringa\'       , \'Serviço\'       , \'Tablete\'       , \'Tambor\'        
                                                          , \'Tubo\'          , \'Vidro\'
                                                          );
                               ';

BEGIN



    OPEN crUnidade FOR EXECUTE stSql;
    LOOP
        FETCH crUnidade INTO reUnidade;
        EXIT WHEN NOT FOUND;

        UPDATE almoxarifado.catalogo_item
           SET cod_unidade  = 0
             , cod_grandeza = 0
         WHERE cod_unidade  = reUnidade.cod_unidade
           AND cod_grandeza = reUnidade.cod_grandeza;

        UPDATE empenho.item_pre_empenho
           SET cod_unidade  = 0
             , cod_grandeza = 0
         WHERE cod_unidade  = reUnidade.cod_unidade
           AND cod_grandeza = reUnidade.cod_grandeza;

        UPDATE imobiliario.area_lote
           SET cod_unidade  = 0
             , cod_grandeza = 0
         WHERE cod_unidade  = reUnidade.cod_unidade
           AND cod_grandeza = reUnidade.cod_grandeza;

        UPDATE public.sw_item_pre_empenho
           SET cod_unidade  = 0
             , cod_grandeza = 0
         WHERE cod_unidade  = reUnidade.cod_unidade
           AND cod_grandeza = reUnidade.cod_grandeza;


        DELETE
          FROM administracao.unidade_medida
         WHERE cod_unidade  = reUnidade.cod_unidade
           AND cod_grandeza = reUnidade.cod_grandeza;

    END LOOP;

    CLOSE crUnidade;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();





/*
* Script de Virada de Ano: virada_para_2009.sql
*/

   -- Exclusão de possiveis funçoes de manutencao.
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
                          WHERE pg_proc.prorettype     <> 'pg_catalog.cstring'::pg_catalog.regtype
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

   Select public.manutencaokiller(); Drop Function public.manutencaokiller();

   --
   -- Procedimento 01
   --
   CREATE TEMP TABLE tmp_exer_permis AS SELECT ano_exercicio  FROM administracao.permissao  GROUP BY ano_exercicio ORDER BY 1;

   Delete From administracao.auditoria
     Where numcgm = 0
       And cod_acao In ( Select acao.cod_acao
                           From administracao.modulo
                              , administracao.funcionalidade
                              , administracao.acao
                          Where modulo.cod_modulo                 = funcionalidade.cod_modulo
                            And funcionalidade.cod_funcionalidade = acao.cod_funcionalidade)
   ;

   Delete From administracao.permissao
     Where numcgm = 0
       And cod_acao In ( Select acao.cod_acao
                           From administracao.modulo
                              , administracao.funcionalidade
                              , administracao.acao
                          Where modulo.cod_modulo                 = funcionalidade.cod_modulo
                            And funcionalidade.cod_funcionalidade = acao.cod_funcionalidade)
   ;

   CREATE OR REPLACE FUNCTION public.manutencao() RETURNS VOID AS $$
   DECLARE
      recRecno RECORD;
   BEGIN
      FOR recRecno
       IN SELECT ano_exercicio
               FROM tmp_exer_permis
           GROUP BY ano_exercicio
           ORDER BY 1
      LOOP
         Insert Into administracao.permissao ( numcgm, cod_acao, ano_exercicio )
              Select 0, acao.cod_acao, recRecno.ano_exercicio
                From administracao.modulo
                   , administracao.funcionalidade
                   , administracao.acao
               Where ((modulo.cod_modulo = 2  or modulo.cod_modulo = 4) or (modulo.cod_modulo = 30 and cod_acao IN (1124,1335,1334,1127,1126,1125,1128)))
                 And modulo.cod_modulo                 = funcionalidade.cod_modulo
                 And funcionalidade.cod_funcionalidade = acao.cod_funcionalidade
                 And 0 = ( Select COALESCE(Count(1),0)
                             From administracao.permissao
                            Where permissao.ano_exercicio = recRecno.ano_exercicio
                              and permissao.cod_acao      = acao.cod_acao
                              and permissao.numcgm        = 0 );

      END LOOP;

      RETURN;
   END;
   $$ LANGUAGE 'plpgsql'
   ;

   Select public.manutencao();
   DROP  FUNCTION  public.manutencao();

   Insert Into administracao.permissao ( numcgm, cod_acao, ano_exercicio )
        Select numcgm, cod_acao, '2009'
          From administracao.permissao  as perm
         Where perm.ano_exercicio = '2008'
          and  0 = ( Select COALESCE(Count(1),0)
                       From administracao.permissao
                      Where permissao.ano_exercicio = '2009'
                        and permissao.cod_acao      = perm.cod_acao
                        and permissao.numcgm        = perm.numcgm )
   ;

   Delete From administracao.permissao Where numcgm != 0 AND ano_exercicio = '2008';


   -- Procedimento 02
   Insert Into administracao.configuracao ( exercicio
                                          , cod_modulo
                                          , parametro
                                          , valor)
                                    SELECT '2009'
                                         , cod_modulo
                                         , parametro
                                         , valor
                                    FROM administracao.configuracao as proximo
                                   WHERE exercicio='2008'
                                     AND NOT EXISTS ( SELECT 1
                                                        FROM administracao.configuracao
                                                       WHERE exercicio  ='2009'
                                                         and cod_modulo = proximo.cod_modulo
                                                         and parametro  = proximo.parametro);

   -- No caso da pref.  ter sido inserido uma nova entidade após a criação do orçamento 2009.
   Insert Into orcamento.entidade ( exercicio, cod_entidade, numcgm, cod_responsavel, cod_resp_tecnico, cod_profissao, sequencia )
                                    SELECT '2009', cod_entidade, numcgm, cod_responsavel, cod_resp_tecnico, cod_profissao, sequencia
                                      FROM orcamento.entidade as proximo
                                     WHERE exercicio='2008'
                                      AND NOT EXISTS ( SELECT 1
                                                       FROM orcamento.entidade
                                                      WHERE exercicio      = '2009'
                                                        and cod_entidade      = proximo.cod_entidade);

   -- No caso da pref.  ter sido inserido uma nova entidade após a criação do orçamento 2009.
   Insert Into orcamento.usuario_entidade ( exercicio, numcgm, cod_entidade )
                                    SELECT '2009', numcgm, cod_entidade
                                      FROM orcamento.usuario_entidade as proximo
                                     WHERE exercicio='2008'
                                      AND NOT EXISTS ( SELECT 1
                                                       FROM orcamento.usuario_entidade
                                                      WHERE exercicio      = '2009'
                                                        and cod_entidade      = proximo.cod_entidade);


   Insert Into administracao.configuracao_entidade ( exercicio
                                                   , cod_entidade
                                                   , cod_modulo
                                                   , parametro
                                                   , valor)
                                    SELECT '2009'
                                          , cod_entidade
                                          , cod_modulo
                                          , parametro
                                          , valor
                                      FROM administracao.configuracao_entidade as proximo
                                     WHERE exercicio='2008'
                                      AND NOT EXISTS ( SELECT 1
                                                       FROM administracao.configuracao_entidade
                                                      WHERE exercicio      = '2009'
                                                        and cod_entidade   = proximo.cod_entidade
                                                        and cod_modulo     = proximo.cod_modulo
                                                        and parametro      = proximo.parametro);

   Insert Into administracao.entidade_rh ( exercicio, cod_entidade, schema_cod )
                                    SELECT '2009', cod_entidade, schema_cod
                                      FROM administracao.entidade_rh as proximo
                                     WHERE exercicio='2008'
                                      AND NOT EXISTS ( SELECT 1
                                                       FROM administracao.entidade_rh
                                                      WHERE exercicio      = '2009'
                                                        and cod_entidade      = proximo.cod_entidade);


   -- Procedimento 03
   update administracao.configuracao
      set valor='2009'
   where exercicio='2009'
      and parametro='ano_exercicio';

   delete from administracao.configuracao
         where exercicio='2008'
         and parametro='diretorio'
   ;

--    -- Procedimento 04
--    delete from administracao.configuracao
--          where exercicio='2006'
--          and parametro='ano_exercicio'
--          and valor='2006';
--
--    -- Procedimento 05
--    delete from administracao.configuracao
--          where exercicio='2006'
--          and parametro='diretorio';




-- VERIFICACAO DE UTILIZACAO OU NAO DOS MODULOS GF --
-----------------------------------------------------

CREATE OR REPLACE FUNCTION atualiza_gf() RETURNS VOID AS $$
DECLARE
    stAux       VARCHAR;
BEGIN

    PERFORM 1
       FROM orcamento.receita
      WHERE exercicio = '2008';

    IF FOUND THEN
            -- Procedimento 07
               Insert Into contabilidade.plano_analitica_credito ( exercicio
                                                               ,  cod_plano
                                                               ,  cod_especie
                                                               ,  cod_genero
                                                               ,  cod_natureza
                                                               ,  cod_credito)
                                                            Select '2009'
                                                               ,  cod_plano
                                                               ,  cod_especie
                                                               ,  cod_genero
                                                               ,  cod_natureza
                                                               ,  cod_credito
                                                            from contabilidade.plano_analitica_credito as proximo
                                                            where exercicio='2008'
                                                               and NOT EXISTS ( SELECT cod_plano
                                                                                    , cod_especie
                                                                                    , cod_genero
                                                                                    , cod_natureza
                                                                                    , cod_credito
                                                                                 FROM contabilidade.plano_analitica_credito
                                                                              WHERE exercicio    ='2009'
                                                                                 AND cod_plano    = proximo.cod_plano
                                                                                 AND cod_credito  = proximo.cod_credito
                                                                                 AND cod_genero   = proximo.cod_genero
                                                                                 AND cod_especie  = proximo.cod_especie
                                                                                 AND cod_natureza = proximo.cod_natureza )
               ;
            
            
            ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
               -- Procedimento 08
                  Insert Into tcers.credor ( exercicio
                                           , numcgm
                                           , tipo)
                                      Select '2009'
                                           , numcgm
                                           , tipo
                                      from tcers.credor as proximo
                                     where exercicio='2008'
                                       and NOT EXISTS ( select 1
                                                          from tcers.credor
                                                         where exercicio = '2009'
                                                           and numcgm   = proximo.numcgm)
                                                           ;
            
               -- Procedimento 09
                  Insert Into tcers.uniorcam ( numcgm
                                             , exercicio
                                             , num_unidade
                                             , num_orgao
                                             , identificador)
                                       Select  numcgm
                                             , '2009'
                                             , num_unidade
                                             , num_orgao
                                             , identificador
                                          from tcers.uniorcam as proximo
                                          where exercicio='2008'
                                            and NOT EXISTS (select 1
                                                              from tcers.uniorcam
                                                              where exercicio='2009'
                                                                and numcgm       = proximo.numcgm
                                                                and num_unidade  = proximo.num_unidade
                                                                and num_orgao    = proximo.num_orgao
                                                                );
            
            
               -- Procedimento 10
               Insert Into tcers.rd_extra ( cod_conta
                                          , exercicio
                                          , classificacao)
                                     Select cod_conta
                                          , '2009'
                                          , classificacao
                                     from tcers.rd_extra proximo
                                    where exercicio='2008'
                                      and not exists ( select 1
                                                         from tcers.rd_extra
                                                        where exercicio = '2009'
                                                          and cod_conta = proximo.cod_conta);
            
            
               -- Procedimento 11
                  Insert Into tcerj.conta_despesa  ( cod_conta
                                                   , cod_estrutural_tce
                                                   , lancamento
                                                   , exercicio)
                                              Select cod_conta
                                                   , cod_estrutural_tce
                                                   , lancamento
                                                   , '2009'
                                                from tcerj.conta_despesa as proximo
                                               where exercicio='2008'
                                                 and not exists ( select 1
                                                                    from tcerj.conta_despesa
                                                                   where exercicio = '2009'
                                                                     and cod_conta = proximo.cod_conta);
            
               -- Procedimento 12
                  Insert Into tcerj.conta_receita ( cod_conta
                                                  , cod_estrutural_tce
                                                  , lancamento
                                                  , exercicio)
                                             Select cod_conta
                                                  , cod_estrutural_tce
                                                  , lancamento
                                                  , '2009'
                                              from tcerj.conta_receita as proximo
                                             where exercicio='2008'
                                               and not exists ( select 1
                                                                  from tcerj.conta_receita
                                                                 where exercicio = '2009'
                                                                   and cod_conta = proximo.cod_conta);

                PERFORM 1
                   FROM contabilidade.tipo_transferencia
                  WHERE exercicio = '2009';

                IF NOT FOUND THEN

                        RAISE EXCEPTION 'É necessário gerar o exercício seguinte na elaboração do orçamento para a aplicação do pacote da virada!';

                ELSE
 
                        -- Procedimento 13
                        Insert Into tcerj.tipo_alteracao ( exercicio
                                                          , cod_tipo
                                                          , cod_tipo_alteracao
                                                          , tipo )
                                                    Select '2009'
                                                          , cod_tipo
                                                          , cod_tipo_alteracao
                                                          , tipo
                                                       from tcerj.tipo_alteracao as proximo
                                                       where exercicio='2008'
                                                         and not exists ( select 1
                                                                            from tcerj.tipo_alteracao
                                                                           where exercicio = '2009'
                                                                            and cod_tipo  = proximo.cod_tipo
                                                                            and tipo      = proximo.tipo)
                                                                          ;

               END IF;

            
               -- Procedimento 14
               Insert Into tcerj.recurso ( cod_recurso, exercicio, cod_fonte)
                                    Select cod_recurso, '2009'   , cod_fonte
                                      from tcerj.recurso as proximo
                                     where exercicio='2008'
                                       and not exists  ( select 1
                                                           from tcerj.recurso
                                                          where exercicio='2009'
                                                            and cod_recurso = proximo.cod_recurso);
            
               -- Procedimento 15
               Insert Into tcers.modelo_lrf ( exercicio, cod_modelo, nom_modelo, nom_modelo_orcamento)
                                    Select '2009'   , cod_modelo, nom_modelo, nom_modelo_orcamento
                                      from  tcers.modelo_lrf as proximo
                                     where exercicio='2008'
                                       and not exists  ( select 1
                                                           from tcers.modelo_lrf
                                                          where exercicio='2009'
                                                            and cod_modelo = proximo.cod_modelo);
               -- Procedimento 16
               Insert Into tcers.quadro_modelo_lrf ( exercicio, cod_modelo, cod_quadro, nom_quadro)
                                    Select '2009'   , cod_modelo, cod_quadro, nom_quadro
                                      from tcers.quadro_modelo_lrf as proximo
                                     where exercicio='2008'
                                       and not exists  ( select 1
                                                           from tcers.quadro_modelo_lrf
                                                          where exercicio='2009'
                                                            and cod_modelo = proximo.cod_modelo
                                                            and cod_quadro = proximo.cod_quadro);

    END IF;

END;
$$ LANGUAGE 'plpgsql';
            

SELECT atualiza_gf();
DROP FUNCTION atualiza_gf();


   -- Procedimento 17
   --
   -- Inclusão Contábil.
   --
   CREATE OR REPLACE FUNCTION contabilidade.fn_sub_importa_conta_contabil_exer_ant( varExer            VARCHAR
                                                                                  , intCodContaExerAnt INTEGER
                                                                                  , varCodStrutural    VARCHAR
                                                                                  , intCassficacaoCont INTEGER
                                                                                  , intSistemaCont     INTEGER
                                                                                  , varDescr           VARCHAR) RETURNS VOID AS $$
   DECLARE
      intCodConta             INTEGER;
      varExerAnt              VARCHAR := BTRIM(TO_CHAR((TO_NUMBER(varExer, '9999') - 1), '9999'));
      intClass01              INTEGER;
      intClass02              INTEGER;
      intClass03              INTEGER;
      intClass04              INTEGER;
      intClass05              INTEGER;
      intClass06              INTEGER;
      intClass07              INTEGER;
      intClass08              INTEGER;
      intClass09              INTEGER;
      intClass10              INTEGER;
        stNaturezaSaldo         VARCHAR;
   BEGIN

      SELECT cod_classificacao INTO intClass01 FROM contabilidade.classificacao_plano WHERE exercicio = varExerAnt AND cod_conta = intCodContaExerAnt AND cod_posicao =  1;
      SELECT cod_classificacao INTO intClass02 FROM contabilidade.classificacao_plano WHERE exercicio = varExerAnt AND cod_conta = intCodContaExerAnt AND cod_posicao =  2;
      SELECT cod_classificacao INTO intClass03 FROM contabilidade.classificacao_plano WHERE exercicio = varExerAnt AND cod_conta = intCodContaExerAnt AND cod_posicao =  3;
      SELECT cod_classificacao INTO intClass04 FROM contabilidade.classificacao_plano WHERE exercicio = varExerAnt AND cod_conta = intCodContaExerAnt AND cod_posicao =  4;
      SELECT cod_classificacao INTO intClass05 FROM contabilidade.classificacao_plano WHERE exercicio = varExerAnt AND cod_conta = intCodContaExerAnt AND cod_posicao =  5;
      SELECT cod_classificacao INTO intClass06 FROM contabilidade.classificacao_plano WHERE exercicio = varExerAnt AND cod_conta = intCodContaExerAnt AND cod_posicao =  6;
      SELECT cod_classificacao INTO intClass07 FROM contabilidade.classificacao_plano WHERE exercicio = varExerAnt AND cod_conta = intCodContaExerAnt AND cod_posicao =  7;
      SELECT cod_classificacao INTO intClass08 FROM contabilidade.classificacao_plano WHERE exercicio = varExerAnt AND cod_conta = intCodContaExerAnt AND cod_posicao =  8;
      SELECT cod_classificacao INTO intClass09 FROM contabilidade.classificacao_plano WHERE exercicio = varExerAnt AND cod_conta = intCodContaExerAnt AND cod_posicao =  9;
      SELECT cod_classificacao INTO intClass10 FROM contabilidade.classificacao_plano WHERE exercicio = varExerAnt AND cod_conta = intCodContaExerAnt AND cod_posicao = 10;

      PERFORM 1  FROM contabilidade.plano_conta WHERE exercicio = varExer AND cod_estrutural = varCodStrutural;

      IF NOT FOUND THEN
         SELECT COALESCE(Max(cod_conta) + 1, 1) INTO intCodConta FROM contabilidade.plano_conta WHERE exercicio = varExer;

         INSERT
           INTO contabilidade.plano_conta (exercicio, cod_conta  , cod_estrutural, nom_conta       , cod_classificacao , cod_sistema)
                                   VALUES (varExer  , intCodConta, ''            , BTRIM(varDescr) , intCassficacaoCont, intSistemaCont );

         Insert Into contabilidade.classificacao_plano ( exercicio, cod_posicao, cod_classificacao , cod_conta) Values ( varExer,  1, intClass01, intCodConta);
         Insert Into contabilidade.classificacao_plano ( exercicio, cod_posicao, cod_classificacao , cod_conta) Values ( varExer,  2, intClass02, intCodConta);
         Insert Into contabilidade.classificacao_plano ( exercicio, cod_posicao, cod_classificacao , cod_conta) Values ( varExer,  3, intClass03, intCodConta);
         Insert Into contabilidade.classificacao_plano ( exercicio, cod_posicao, cod_classificacao , cod_conta) Values ( varExer,  4, intClass04, intCodConta);
         Insert Into contabilidade.classificacao_plano ( exercicio, cod_posicao, cod_classificacao , cod_conta) Values ( varExer,  5, intClass05, intCodConta);
         Insert Into contabilidade.classificacao_plano ( exercicio, cod_posicao, cod_classificacao , cod_conta) Values ( varExer,  6, intClass06, intCodConta);
         Insert Into contabilidade.classificacao_plano ( exercicio, cod_posicao, cod_classificacao , cod_conta) Values ( varExer,  7, intClass07, intCodConta);
         Insert Into contabilidade.classificacao_plano ( exercicio, cod_posicao, cod_classificacao , cod_conta) Values ( varExer,  8, intClass08, intCodConta);
         Insert Into contabilidade.classificacao_plano ( exercicio, cod_posicao, cod_classificacao , cod_conta) Values ( varExer,  9, intClass09, intCodConta);
         Insert Into contabilidade.classificacao_plano ( exercicio, cod_posicao, cod_classificacao , cod_conta) Values ( varExer, 10, intClass10, intCodConta);

         UPDATE contabilidade.plano_conta SET cod_estrutural = cod_estrutural WHERE exercicio = varExer AND cod_conta = intCodConta;

           IF substr(varCodStrutural,1,1) = 1 THEN stNaturezaSaldo := 'D';
        ELSIF substr(varCodStrutural,1,1) = 2 THEN stNaturezaSaldo := 'C';
        ELSIF substr(varCodStrutural,1,1) = 3 THEN stNaturezaSaldo := 'D';
        ELSIF substr(varCodStrutural,1,1) = 4 THEN stNaturezaSaldo := 'C';
        ELSIF substr(varCodStrutural,1,1) = 5 THEN stNaturezaSaldo := 'D';
        ELSIF substr(varCodStrutural,1,1) = 6 THEN stNaturezaSaldo := 'C';
        ELSIF substr(varCodStrutural,1,1) = 7 THEN stNaturezaSaldo := 'C';
        ELSIF substr(varCodStrutural,1,1) = 9 THEN stNaturezaSaldo := 'D';
        END IF;

         INSERT INTO contabilidade.plano_analitica ( exercicio
                                                   , cod_conta
                                                   , cod_plano 
                                                   , natureza_saldo )
                                            VALUES ( varExer
                                                   , intCodConta
                                                   , ( SELECT COALESCE((max(cod_plano)+1),1)
                                                         FROM contabilidade.plano_analitica
                                                        WHERE exercicio = varExer ) 
                                                   , stNaturezaSaldo
                                                   );
      END IF;

      RETURN;
   END;
   $$ LANGUAGE 'plpgsql';


   --
   -- Função principal.
   --
      CREATE OR REPLACE FUNCTION contabilidade.fn_importa_conta_contabil_exer_ant(varExer VARCHAR) RETURNS VOID AS $$
      DECLARE
         varExerAnt              VARCHAR := BTRIM(TO_CHAR((TO_NUMBER(varExer, '9999') - 1), '9999'));
         recAnalitica            RECORD;
      BEGIN
         FOR recAnalitica
          IN  SELECT plano_conta.cod_conta
                   , plano_conta.cod_estrutural
                   , plano_conta.cod_classificacao
                   , plano_conta.cod_sistema
                   , plano_conta.nom_conta
                FROM contabilidade.plano_conta
                   , contabilidade.plano_analitica
               WHERE plano_conta.exercicio = varExerAnt
                 AND plano_conta.exercicio = plano_analitica.exercicio
                 AND plano_conta.cod_conta = plano_analitica.cod_conta
         LOOP
            -- Inclusão de contas na contabilidade.
            PERFORM contabilidade.fn_sub_importa_conta_contabil_exer_ant( varExer
                                                                        , recAnalitica.cod_conta
                                                                        , recAnalitica.cod_estrutural
                                                                        , recAnalitica.cod_classificacao
                                                                        , recAnalitica.cod_sistema
                                                                        , recAnalitica.nom_conta);
         END LOOP;

         RETURN;
      END;
      $$ LANGUAGE 'plpgsql';


   CREATE OR REPLACE FUNCTION publico.fn_consulta_classificacao(VARCHAR,VARCHAR,VARCHAR) RETURNS VARCHAR AS '

   DECLARE
       reRecord                RECORD;
       stOut                   VARCHAR := '''';
       stSql                   VARCHAR := '''';
       stTabelaClassificacao   ALIAS FOR $1;
       stTabelaPosicao         ALIAS FOR $2;
       stFiltro                ALIAS FOR $3;

   BEGIN

       IF stTabelaClassificacao != ''orcamento.classificacao_receita''  THEN
          stSql := ''
              SELECT *
              FROM  (
                  SELECT
                       cla.*
                      ,pos.mascara
                  FROM
                       '' || stTabelaClassificacao || ''        as cla
                      ,'' || stTabelaPosicao || ''              as pos
                  WHERE   cla.exercicio   = pos.exercicio
                  AND     cla.cod_posicao = pos.cod_posicao
                  ORDER BY cla.cod_posicao
                    ) as tabela
              '' || stFiltro || ''
              '';
       ELSE
              stSql := ''
              SELECT *
              FROM  (
                  SELECT
                       cla.*
                      ,pos.mascara
                  FROM
                       '' || stTabelaClassificacao || ''        as cla
                      ,'' || stTabelaPosicao || ''              as pos
                  WHERE   cla.exercicio   = pos.exercicio
                  AND     cla.cod_posicao = pos.cod_posicao
                  AND     cla.cod_tipo    = pos.cod_tipo
                  ORDER BY cla.cod_posicao
                    ) as tabela
              '' || stFiltro || ''
              '';
       END IF;

       FOR reRecord IN EXECUTE stSql LOOP
           stOut := stOut||''.''||sw_fn_mascara_dinamica ( ( case when reRecord.mascara = '''' then ''0'' else reRecord.mascara end ) , cast( reRecord.cod_classificacao as VARCHAR) );
       END LOOP;

       stOut := SUBSTR(stOut,2,LENGTH(stOut));

       RETURN stOut;

   END;
   'LANGUAGE 'plpgsql';



CREATE OR REPLACE FUNCTION atualiza_gf() RETURNS VOID AS $$
DECLARE
    stAux       VARCHAR;
BEGIN

    PERFORM 1
       FROM orcamento.receita
      WHERE exercicio = '2008';

    IF FOUND THEN

            PERFORM contabilidade.fn_importa_conta_contabil_exer_ant('2009');

    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT atualiza_gf();
DROP FUNCTION atualiza_gf();



   --
   -- Procedimento 18
   --
   DELETE
     FROM administracao.configuracao
    WHERE parametro = 'virada_GF'
      AND exercicio = '2009';


