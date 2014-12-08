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
* URBEM Soluções de Gestão Pública Ltda
*
* $Id: $
*
* Caso de uso: uc-05.03.05
*/

/*
$Log$

*/

CREATE OR REPLACE FUNCTION arrecadacao.deleteCalculosSimulado( inCodGrupo INTEGER, stExercicio VARCHAR ) RETURNS VARCHAR AS $$

DECLARE

    stSql               VARCHAR;
    reRecord            RECORD;
    stDelete            VARCHAR;
    iCount              INTEGER := 0;
    stRetorno           VARCHAR;
 
BEGIN

    stSql := '
        SELECT DISTINCT 
            AIC.cod_calculo
        FROM 
            arrecadacao.calculo AS AIC

        INNER JOIN 
            arrecadacao.calculo_grupo_credito AS ACGC
        ON 
            ACGC.cod_calculo = AIC.cod_calculo
            AND ACGC.cod_grupo = ' || inCodGrupo  || '
            AND ACGC.ano_exercicio = ''' || stExercicio || '''

        LEFT JOIN 
            arrecadacao.lancamento_calculo AS ALC
        ON 
            ALC.cod_calculo = AIC.cod_calculo

        WHERE 
            ALC.cod_calculo IS NULL
            AND AIC.simulado = true
    ';

    FOR reRecord IN EXECUTE stSql LOOP

        stDelete := deleteCalculo( reRecord.cod_calculo );
        iCount   := iCount + 1;

    END LOOP;

    stRetorno := 'total: ' || iCount || ' calculos deletados! ';

RETURN stRetorno;
END;

$$ LANGUAGE 'plpgsql';
