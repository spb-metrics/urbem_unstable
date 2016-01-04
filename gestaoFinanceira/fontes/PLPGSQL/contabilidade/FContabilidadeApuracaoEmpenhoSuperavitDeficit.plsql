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
* $Revision: 64186 $
* $Name$
* $Author: franver $
* $Date: 2015-12-11 18:36:20 -0200 (Sex, 11 Dez 2015) $
*
* Casos de uso: uc-02.03.10
*/

/*
$Log$
Revision 1.7  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.apuracao_empenho_superavit_deficit(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stDtInicial             ALIAS FOR $2;
    stDtFinal               ALIAS FOR $3;
    stCodEntidades          ALIAS FOR $4;
    stSql               VARCHAR   := '';
    ponto               VARCHAR   := '.';
    stDtInicialEmissao  VARCHAR   := '';
    stDtFinalEmissao    VARCHAR   := '';
    stDtInicialAux      VARCHAR   := '';
    reRegistro          RECORD;
    reReg               RECORD;

BEGIN

    IF (LENGTH(stDtInicial) <> 10) THEN
        stSql := 'SELECT DISTINCT exercicio FROM empenho.empenho WHERE empenho.exercicio < ' || quote_literal(stExercicio) || ' ORDER BY exercicio ';
    ELSE
        stSql := 'SELECT DISTINCT exercicio FROM empenho.empenho WHERE empenho.exercicio = ' || quote_literal(stExercicio) || ' ORDER BY exercicio ';
    END IF;

    CREATE TEMPORARY TABLE tmp_empenhos (
        exercicio    CHAR(4),
        cod_empenho     TEXT,
        cod_entidade INTEGER,
        cod_recurso  INTEGER,
        valor        NUMERIC
    );

    FOR reReg IN EXECUTE stSql
    LOOP

        stDtInicialEmissao := '01/01/' || reReg.exercicio;
        stDtFinalEmissao := '31/12/' || reReg.exercicio;
        IF (LENGTH(stDtInicial) <> 10) THEN
            stDtInicialAux := stDtInicial || reReg.exercicio;
        ELSE
            stDtInicialAux := stDtInicial;
        END IF;

        stSql := '
         INSERT INTO tmp_empenhos
              SELECT exercicio
                   , cod_empenho
                   , cod_entidade
                   , recurso AS cod_recurso
                   , (valor_liquidado - valor_pago) AS valor
                FROM (
                      SELECT empenho.exercicio
                           , empenho.cod_empenho
                           , empenho.cod_entidade
                           , CASE WHEN restos_pre_empenho.recurso IS NOT NULL
                                  THEN restos_pre_empenho.recurso
                                  ELSE ped_d_cd.cod_recurso
                              END AS recurso
                           -- Valor pago
                           , (empenho.fn_empenho_pago( empenho.exercicio, empenho.cod_empenho, empenho.cod_entidade, '''||stDtInicialAux||''', '''||stDtFinal||''') - empenho.fn_empenho_estornado( empenho.exercicio,empenho.cod_empenho , empenho.cod_entidade, '''||stDtInicialAux||''' ,'''||stDtFinal||''')) as valor_pago
                           -- Valor Liquidado
                           , (empenho.fn_empenho_liquidado( empenho.exercicio, empenho.cod_empenho, empenho.cod_entidade, '''||stDtInicialAux||''', '''||stDtFinal||''') - empenho.fn_empenho_estorno_liquidacao( empenho.exercicio ,empenho.cod_empenho ,empenho.cod_entidade ,'''||stDtInicialAux||''' ,'''||stDtFinal||''')) as valor_liquidado
                        FROM empenho.empenho
                  INNER JOIN empenho.pre_empenho
                          ON pre_empenho.exercicio = empenho.exercicio
                         AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                   LEFT JOIN empenho.restos_pre_empenho
                          ON pre_empenho.exercicio = restos_pre_empenho.exercicio
                         AND pre_empenho.cod_pre_empenho  = restos_pre_empenho.cod_pre_empenho
                   LEFT JOIN (SELECT ped.exercicio
                                   , ped.cod_pre_empenho
                                   , d.cod_recurso
                                FROM empenho.pre_empenho_despesa AS ped
                          INNER JOIN orcamento.despesa AS d
                                  ON ped.cod_despesa = d.cod_despesa
                                 AND ped.exercicio   = d.exercicio
                          INNER JOIN orcamento.recurso('||quote_literal(reReg.exercicio)||') AS rec
                                  ON rec.exercicio = d.exercicio
                                 AND rec.cod_recurso = d.cod_recurso
                               WHERE ped.exercicio = '||quote_literal(reReg.exercicio)||'
                             ) AS ped_d_cd
                          ON pre_empenho.exercicio = ped_d_cd.exercicio
                         AND pre_empenho.cod_pre_empenho = ped_d_cd.cod_pre_empenho
                       WHERE empenho.exercicio = '||quote_literal(reReg.exercicio)||'
                         AND empenho.cod_entidade IN ('||stCodEntidades||')
                    ORDER BY empenho.exercicio
                           , empenho.cod_empenho
                           , empenho.cod_entidade
                     
                     ) as tbl
        ';
        
        EXECUTE stSql;
    END LOOP;
    
    stSql := '
         INSERT INTO tmp_empenhos
              SELECT exercicio
                   , cod_empenho::TEXT
                   , cod_entidade
                   , cod_recurso
                   , apagarliquidado AS valor
                FROM empenho.fn_relatorio_empenhos_a_pagar
                   ( ''''
                   , '''||stCodEntidades||'''
                   , '''||stExercicio||'''
                   , ''01/01/'||stExercicio||'''
                   , '''||stDtFinal||'''
                   , '''||stDtFinal||'''
                   , ''''
                   , ''''
                   , ''''
                   , ''''
                   , ''''
                   , ''''
                   , ''''
                   ) as retorno
                   ( cod_entidade         integer
                   , cod_empenho          integer
                   , exercicio            char(4)
                   , dt_emissao           text
                   , cgm                  integer
                   , credor               varchar
                   , empenhado            numeric
                   , liquidado            numeric
                   , pago                 numeric
                   , apagar               numeric
                   , apagarliquidado      numeric
                   , cod_recurso          integer
                   , nom_recurso          varchar
                   , masc_recurso_red     varchar
                   )
    ';

    EXECUTE stSql;

    stSql := '
      SELECT exercicio
           , cod_empenho
           , cod_entidade
           , cod_recurso
           , valor
        FROM tmp_empenhos;
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_empenhos;

    RETURN;
END;
$$ language 'plpgsql';
