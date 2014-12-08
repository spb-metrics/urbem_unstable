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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.01.06
*/

CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_detalhamento_unidade_funcao(varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;
    reRegistro2         RECORD;
    arDotacao           VARCHAR[];
    arMascDespesa       VARCHAR[];

    inNivel             INTEGER := 0;
    inPosicao           INTEGER;
    stOrgao             VARCHAR;
    stCampos            VARCHAR := '''';
    stExecute           VARCHAR := '''';
    nuTotUnidade        NUMERIC(14,2);
    nuFuncao            NUMERIC(14,2);


BEGIN
        SELECT INTO
                   arMascDespesa
                   string_to_array(administracao.configuracao.valor,''.'')
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = ''masc_despesa''
               AND administracao.configuracao.exercicio = stExercicio;

        stSql := ''CREATE TEMPORARY TABLE tmp_despesa AS
                SELECT
                     *
                FROM    orcamento.despesa
                WHERE   exercicio = '' || stExercicio || ''
                '' || stFiltro ;

        EXECUTE stSql;

        FOR reRegistro IN
            SELECT   distinct cod_funcao
            FROM     tmp_despesa
            ORDER BY cod_funcao
        LOOP
            stCampos := stCampos || '',f_'' || reRegistro.cod_funcao || '' numeric(14,2) '';
        END LOOP;

        stSql := ''CREATE TEMPORARY TABLE tmp_relatorio(
                     codigo         VARCHAR(20)
                    ,num_orgao      INTEGER
                    ,num_unidade    INTEGER
                    ,nom_unidade    VARCHAR(100)
                 ''|| stCampos ||''
                    ,vl_total       NUMERIC(14,2)
                ) '';

        --RAISE EXCEPTION ''%'',stSql;
        EXECUTE stSql;


        FOR reRegistro IN
            SELECT   DISTINCT  ou.num_orgao
                              ,ou.num_unidade
                              ,ou.nom_unidade
            FROM     orcamento.unidade  as ou
                    ,tmp_despesa        as td
            WHERE    ou.num_orgao     = td.num_orgao
            AND      ou.num_unidade   = td.num_unidade
            AND      ou.exercicio     = td.exercicio
            ORDER BY ou.num_orgao, ou.num_unidade
        LOOP
            INSERT INTO tmp_relatorio (num_orgao, num_unidade, nom_unidade) VALUES (reRegistro.num_orgao, reRegistro.num_unidade, reRegistro.nom_unidade);
        END LOOP;

    --Totaliza os resultados dinamicamente com update
    FOR reRegistro IN
        SELECT  *
        FROM    tmp_relatorio
        ORDER BY num_orgao, num_unidade
    LOOP
        nuTotUnidade := 0;
        FOR reRegistro2 IN
            SELECT   distinct cod_funcao
            FROM     tmp_despesa
            ORDER BY cod_funcao
        LOOP
            nuFuncao := coalesce(orcamento.fn_totaliza_unidade_funcao(reRegistro.num_orgao,reRegistro.num_unidade,reRegistro2.cod_funcao),0);
            nuTotUnidade := nuTotUnidade + nuFuncao;
            stExecute := ''UPDATE tmp_relatorio SET f_''||reRegistro2.cod_funcao||'' = ''||nuFuncao||'' WHERE num_orgao=''||reRegistro.num_orgao||'' AND num_unidade=''||reRegistro.num_unidade;
            EXECUTE stExecute;
        END LOOP;
        UPDATE tmp_relatorio SET vl_total = nuTotUnidade WHERE num_orgao = reRegistro.num_orgao AND num_unidade = reRegistro.num_unidade;
    END LOOP;

    --Lista os resultados
    FOR reRegistro IN
        SELECT  *
        FROM    tmp_relatorio
        ORDER BY num_orgao, num_unidade
    LOOP
        reRegistro.codigo := sw_fn_mascara_dinamica(arMascDespesa[1]||''.''||arMascDespesa[2] , reRegistro.num_orgao||''.''||reRegistro.num_unidade);
        RETURN next reRegistro;
    END LOOP;


    DROP TABLE tmp_relatorio;
    DROP TABLE tmp_despesa;

    RETURN;
END;
'language 'plpgsql';
