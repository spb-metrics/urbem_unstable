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
    * Arquivo de mapeamento para a função que busca os dados dos serviços de terceiros
    * Data de Criação   : 19/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_comparativo_pl(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    inMes               ALIAS FOR $3;
    stSql               VARCHAR := '';
    stDtFinal           VARCHAR := '';
    stDtFinalDate       VARCHAR;
    stDia               VARCHAR := '';
    nuValor             NUMERIC(14,2);
    nuValorRCL          NUMERIC(14,2);
    reRegistro          RECORD;

BEGIN

    stDia := '01/'||inMes||'/'||stExercicio;
    SELECT last_day (TO_DATE(stDia, 'dd/mm/yyyy')) INTO stDtFinalDate;
    stDtFinal := TO_CHAR(TO_DATE(stDtFinalDate, 'yyyy-mm-dd'), 'dd/mm/yyyy');

    CREATE TEMPORARY TABLE tmp_arquivo (
          valor       NUMERIC(14,2)
        , mes         INTEGER
    );

    SELECT COALESCE(SUM(CAST((total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6
                   + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12) AS NUMERIC(14,2))), 0.00) INTO nuValor
    FROM stn.pl_total_subcontas (stDtFinal) AS retorno (  ordem          integer
                                                        , cod_conta      varchar
                                                        , nom_conta      varchar
                                                        , cod_estrutural varchar
                                                        , mes_1          numeric
                                                        , mes_2          numeric
                                                        , mes_3          numeric
                                                        , mes_4          numeric
                                                        , mes_5          numeric
                                                        , mes_6          numeric
                                                        , mes_7          numeric
                                                        , mes_8          numeric
                                                        , mes_9          numeric
                                                        , mes_10         numeric
                                                        , mes_11         numeric
                                                        , mes_12         numeric
                                                        , total_mes_1    numeric
                                                        , total_mes_2    numeric
                                                        , total_mes_3    numeric
                                                        , total_mes_4    numeric
                                                        , total_mes_5    numeric
                                                        , total_mes_6    numeric
                                                        , total_mes_7    numeric
                                                        , total_mes_8    numeric
                                                        , total_mes_9    numeric
                                                        , total_mes_10   numeric
                                                        , total_mes_11   numeric
                                                        , total_mes_12   numeric)
    WHERE ordem = 1;

    --
    -- Acrescenta o valor da rcl vinculada ao periodo
    --
    SELECT stn.fn_calcula_rcl_vinculada(stExercicio,stDtFinal,stCodEntidade)
      INTO nuValorRCL;

    nuValor := nuValor + nuValorRCL;

    INSERT INTO tmp_arquivo VALUES (nuValor, inMes);

    stSql := ' SELECT mes, COALESCE(valor, 0.00) AS valor FROM tmp_arquivo; ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_arquivo;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  
