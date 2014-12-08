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
    * Arquivo de mapeamento para a função que busca os dados exclusao de receita
    * Data de Criação   : 27/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_exclusao_receita(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    inMes               ALIAS FOR $3;
    stDtInicial         VARCHAR := '';
    stDtFinal           VARCHAR := '';
    stSql               VARCHAR := '';
    reRegistro          RECORD;

BEGIN

    stDtInicial := '01/01/' || stExercicio;
    stDtFinal := TO_CHAR(last_day(TO_DATE(stExercicio || '-' || inMes || '-' || '01','yyyy-mm-dd')),'dd/mm/yyyy');

    CREATE TEMPORARY TABLE tmp_retorno(
        mes                        INTEGER,
        contr_serv                 NUMERIC(14,2),
        compens_reg_prev           NUMERIC(14,2),
        fundacoes_transf_corrente   NUMERIC(14,2),
        autarquias_transf_corrente NUMERIC(14,2),
        empestdep_transf_corrente  NUMERIC(14,2),
        demaisent_transf_corrente  NUMERIC(14,2),
        fundacoes_transf_capital   NUMERIC(14,2),
        autarquias_transf_capital  NUMERIC(14,2),
        empestdep_transf_capital   NUMERIC(14,2),
        demaisent_transf_capital   NUMERIC(14,2),
        out_duplic                 NUMERIC(14,2),
        contr_patronal             NUMERIC(14,2)
    );

    INSERT INTO tmp_retorno VALUES (  inMes
                                    , stn.pl_saldo_contas (  stExercicio
                                                           , stDtInicial
                                                           , stDtFinal
                                                           , ' plano_conta.cod_estrutural LIKE ''1.2.1.0.29%'' '
                                                           , stCodEntidade
                                                           , 'false'
                                                          )
                                    , stn.pl_saldo_contas (  stExercicio
                                                           , stDtInicial
                                                           , stDtFinal
                                                           , ' plano_conta.cod_estrutural LIKE ''1.2.1.0.99.00.10%'' '
                                                           , stCodEntidade
                                                           , 'false'
                                                          )
                                    , 0
                                    , 0
                                    , 0
                                    , 0
                                    , 0
                                    , 0
                                    , 0
                                    , 0
                                    , 0
                                    , 0
                                   );

    stSql := 'SELECT * FROM tmp_retorno';                                                 

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_retorno;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  
