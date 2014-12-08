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
    * Retorna a lista de despesas
    * Data de Criação: 10/06/2009 
 
 
    * @author Analista:      Tonismar Bernardo <tonismar.bernardo@cnm.org.br> 
    * @author Desenvolvedor: Henrique Boaventura <henrique.boaventura@cnm.org.br> 
 
    * @package      URBEM 
    * @subpackage   LDO 
 
    * $Id: $ 
*/ 
 
CREATE OR REPLACE FUNCTION ldo.fn_despesa_configuracao(INTEGER,CHAR(4)) RETURNS SETOF RECORD AS $$ 
DECLARE
    inCodPPA            ALIAS FOR $1;
    stExercicio         ALIAS FOR $2;
    
    stExercicioInicial  VARCHAR;
    stCodReduzido       VARCHAR;
    stFiltro            VARCHAR := '';
    stSql               VARCHAR;
    stSqlAux            VARCHAR;

    reRecord            RECORD;

    flValor_1           NUMERIC(14,2);
    flValor_2           NUMERIC(14,2);
    flValor_3           NUMERIC(14,2);
    flValor_4           NUMERIC(14,2);

    boOrcamento_1       NUMERIC(1) := 0;
    boOrcamento_2       NUMERIC(1) := 0;
    boOrcamento_3       NUMERIC(1) := 0;
    boOrcamento_4       NUMERIC(1) := 0;

BEGIN
    stExercicioInicial := TRIM(TO_CHAR((TO_NUMBER(stExercicio,'9999') - 4),'9999'));
    
    -----------------------------------------
    -- cria a tabela temporaria de retorno --
    -----------------------------------------
    CREATE TEMPORARY TABLE tmp_retorno (
        cod_tipo         INTEGER,
        exercicio        VARCHAR(4),
        cod_estrutural   VARCHAR,
        descricao        VARCHAR,
        tipo             CHAR(1),
        nivel            NUMERIC(1),
        rpps             NUMERIC(1),
        orcamento_1      NUMERIC(1),
        orcamento_2      NUMERIC(1),
        orcamento_3      NUMERIC(1),
        orcamento_4      NUMERIC(1),
        valor_1          NUMERIC(14,2),
        valor_2          NUMERIC(14,2),
        valor_3          NUMERIC(14,2),
        valor_4          NUMERIC(14,2)
    );
    
    
    --------------------------------------------------------
    -- recupera os tipos de despesas que vao no relatorio --
    --------------------------------------------------------
    stSql := 'SELECT *
                   , publico.fn_mascarareduzida(cod_estrutural) AS estrutural_reduzido 
                FROM ldo.tipo_receita_despesa
               WHERE tipo = ''D'' 
                 AND nivel = 1
            ORDER BY cod_tipo ';

    FOR reRecord IN EXECUTE stSql
    LOOP
        -- se o estrutural for 4.5.9.0.99.00.00.00.00 ele totaliza todas as contas do grupo 4.5 exceto 4.5.9.0.66.00.00.00.00 
        IF reRecord.cod_estrutural = '4.5.9.0.99.00.00.00.00' 
        THEN
            stCodReduzido := '4.5';
            stFiltro := 'AND cod_estrutural NOT LIKE ''4.5.9.0.66%'' ';
        ELSE
            stCodReduzido := reRecord.estrutural_reduzido;
            stFiltro := '';
        END IF;

        SELECT ldo.fn_despesa_liquidada(stCodReduzido,stExercicioInicial,reRecord.rpps,stFiltro)                               
          INTO flValor_1;
    
        SELECT ldo.fn_despesa_liquidada(stCodReduzido,TRIM(TO_CHAR((TO_NUMBER(stExercicioInicial,'9999') + 1),'9999')),reRecord.rpps,stFiltro)
          INTO flValor_2;

        SELECT ldo.fn_despesa_liquidada(stCodReduzido,TRIM(TO_CHAR((TO_NUMBER(stExercicioInicial,'9999') + 2),'9999')),reRecord.rpps,stFiltro)
          INTO flValor_3;

        SELECT ldo.fn_despesa_liquidada_estimativa(stCodReduzido,TRIM(TO_CHAR((TO_NUMBER(stExercicioInicial,'9999') + 3),'9999')),reRecord.rpps,stFiltro)
          INTO flValor_4;

        -------------------------------------------------------------------------------------
        -- insere na tabela de retorno o somatorio do valor dos estruturais para os 4 anos --
        -------------------------------------------------------------------------------------
        INSERT INTO tmp_retorno VALUES( reRecord.cod_tipo
                                       ,stExercicio
                                       ,reRecord.cod_estrutural
                                       ,reRecord.descricao
                                       ,reRecord.tipo
                                       ,reRecord.nivel
                                       ,CASE WHEN (reRecord.rpps IS TRUE)
                                             THEN 1
                                             ELSE 0
                                        END
                                       ,boOrcamento_1
                                       ,boOrcamento_2
                                       ,boOrcamento_3
                                       ,boOrcamento_4
                                       ,flValor_1
                                       ,flValor_2
                                       ,flValor_3
                                       ,flValor_4);


    END LOOP;
  
    ---------------------------------------------------
    -- verifica se existe movimentacao para cada ano --
    ---------------------------------------------------
    SELECT CASE WHEN ( SELECT SUM(valor_1)
                         FROM tmp_retorno ) > 0
                THEN 1
                ELSE 0
           END
      INTO boOrcamento_1;

    SELECT CASE WHEN ( SELECT SUM(valor_2)
                         FROM tmp_retorno ) > 0
                THEN 1
                ELSE 0
           END
      INTO boOrcamento_2;

    SELECT CASE WHEN ( SELECT SUM(COALESCE(valor_3,0))
                         FROM tmp_retorno ) > 0                     
                THEN 1
                ELSE 0
           END
      INTO boOrcamento_3;

    SELECT CASE WHEN ( SELECT SUM(COALESCE(valor_4,0))
                         FROM tmp_retorno ) > 0                     
                THEN 1
                ELSE 0
           END
      INTO boOrcamento_4;

    IF( boOrcamento_1 = 1 ) THEN
        UPDATE tmp_retorno SET orcamento_1 = 1;
    END IF;

    IF( boOrcamento_2 = 1 ) THEN
        UPDATE tmp_retorno SET orcamento_2 = 1;
    END IF;

    IF( boOrcamento_3 = 1 ) THEN
        UPDATE tmp_retorno SET orcamento_3 = 1;
    END IF;

    IF( boOrcamento_4 = 1 ) THEN
        UPDATE tmp_retorno SET orcamento_4 = 1;
    END IF;

    -------------------------------------------------
    -- verifica se existem valores ja configurados --    
    -------------------------------------------------
    stSql := ' SELECT vl_arrecadado_liquidado
                    , CASE WHEN tipo_receita_despesa.rpps IS TRUE
                           THEN 1
                           ELSE 0
                      END AS rpps
                    , tipo_receita_despesa.cod_estrutural
                    , configuracao_receita_despesa.exercicio
                 FROM ldo.configuracao_receita_despesa
           INNER JOIN ldo.tipo_receita_despesa  
                   ON configuracao_receita_despesa.cod_tipo = tipo_receita_despesa.cod_tipo
                  AND configuracao_receita_despesa.tipo     = tipo_receita_despesa.tipo
           INNER JOIN ppa.ppa
                   ON ppa.cod_ppa = configuracao_receita_despesa.cod_ppa
                WHERE configuracao_receita_despesa.cod_ppa = '|| inCodPPA ||'
                  AND configuracao_receita_despesa.tipo    = ''D'' ';

    FOR reRecord IN EXECUTE stSql
    LOOP
        IF(boOrcamento_1 = 0 AND reRecord.exercicio = stExercicioInicial) THEN

            UPDATE tmp_retorno
               SET valor_1 = COALESCE(reRecord.vl_arrecadado_liquidado,0)
             WHERE tmp_retorno.cod_estrutural = reRecord.cod_estrutural
               AND tmp_retorno.rpps           = reRecord.rpps;

        END IF;

        IF(boOrcamento_2 = 0 AND reRecord.exercicio = (TO_NUMBER(stExercicioInicial,'9999') + 1)::varchar ) THEN

            UPDATE tmp_retorno
               SET valor_2 = COALESCE(reRecord.vl_arrecadado_liquidado,0)
             WHERE tmp_retorno.cod_estrutural = reRecord.cod_estrutural
               AND tmp_retorno.rpps           = reRecord.rpps;

        END IF;

        IF(boOrcamento_3 = 0 AND reRecord.exercicio = (TO_NUMBER(stExercicioInicial,'9999') + 2)::varchar ) THEN

            UPDATE tmp_retorno
               SET valor_3 = COALESCE(reRecord.vl_arrecadado_liquidado,0)
             WHERE tmp_retorno.cod_estrutural = reRecord.cod_estrutural
               AND tmp_retorno.rpps           = reRecord.rpps;

        END IF;

        IF(boOrcamento_4 = 0 AND reRecord.exercicio = (TO_NUMBER(stExercicioInicial,'9999') + 3)::varchar ) THEN

            UPDATE tmp_retorno
               SET valor_4 = COALESCE(reRecord.vl_arrecadado_liquidado,0)
             WHERE tmp_retorno.cod_estrutural = reRecord.cod_estrutural
               AND tmp_retorno.rpps           = reRecord.rpps;

        END IF;

        
    END LOOP;


    ----------------------------------------------------------
    -- insere na tabela de retorno o somatorio dos niveis 0 --
    ----------------------------------------------------------
    stSql := ' SELECT publico.fn_mascarareduzida(cod_estrutural) AS estrutural_reduzido
                    , *
                 FROM ldo.tipo_receita_despesa
                WHERE nivel = 0
                  AND tipo = ''D''
             ORDER BY cod_tipo'; 
    
    FOR reRecord IN EXECUTE stSql
    LOOP
        SELECT SUM(COALESCE(valor_1,0)) 
          INTO flValor_1
          FROM tmp_retorno 
         WHERE cod_estrutural LIKE reRecord.estrutural_reduzido || '%' 
           AND nivel = 1;

        SELECT SUM(COALESCE(valor_2,0)) 
          INTO flValor_2
          FROM tmp_retorno 
         WHERE cod_estrutural LIKE reRecord.estrutural_reduzido || '%' 
           AND nivel = 1;

        SELECT SUM(COALESCE(valor_3,0)) 
          INTO flValor_3
          FROM tmp_retorno 
         WHERE cod_estrutural LIKE reRecord.estrutural_reduzido || '%' 
           AND nivel = 1;

        SELECT SUM(COALESCE(valor_4,0))
          INTO flValor_4
          FROM tmp_retorno 
         WHERE cod_estrutural LIKE reRecord.estrutural_reduzido || '%' 
           AND nivel = 1;
    
        INSERT INTO tmp_retorno ( SELECT reRecord.cod_tipo
                                       , stExercicio
                                       , reRecord.cod_estrutural
                                       , reRecord.descricao
                                       , reRecord.tipo
                                       , reRecord.nivel
                                       , CASE WHEN (reRecord.rpps IS TRUE)
                                             THEN 1
                                             ELSE 0
                                         END
                                       , boOrcamento_1
                                       , boOrcamento_2
                                       , boOrcamento_3
                                       , boOrcamento_4
                                       , flValor_1
                                       , flValor_2
                                       , flValor_3
                                       , flValor_4);
    END LOOP;

    ------------------------
    -- retorna os valores --
    ------------------------
 
    stSql := ' SELECT * 
                 FROM tmp_retorno
             ORDER BY cod_tipo';

    FOR reRecord IN EXECUTE stSql
    LOOP
        RETURN NEXT reRecord;
    END LOOP;
        
    DROP TABLE tmp_retorno;

END;

$$ LANGUAGE 'plpgsql';
 
