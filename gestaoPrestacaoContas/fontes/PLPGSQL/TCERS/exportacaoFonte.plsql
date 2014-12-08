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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $
*
* Casos de uso: uc-02.08.08
* Casos de uso: uc-02.08.08
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.9  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcerj.fn_exportacao_fonte(varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    stSql                   VARCHAR   := '''';
    reRegistro              RECORD;
BEGIN
stSql := ''
    SELECT
        tre.cod_fonte        as cod_fonte,
        ore.cod_recurso      as cod_recurso,
        ore.nom_recurso      as nom_recurso,
        ore.exercicio        as exercicio
    FROM
        orcamento.recurso ore,
        tcerj.recurso tre
    WHERE
        ore.exercicio   =   '''''' || stExercicio || ''''''
    AND ore.exercicio   = tre.exercicio
    AND ore.cod_recurso = tre.cod_recurso
    AND EXISTS (SELECT 
                    cod_despesa
                FROM
                    orcamento.despesa as de
                WHERE
                    de.exercicio    = '''''' || stExercicio || ''''''
                AND de.cod_entidade IN ('' || stCodEntidades || '') 
                AND de.exercicio    = ore.exercicio
                AND de.cod_recurso  = ore.cod_recurso
               )
    /*GROUP BY
        tre.cod_fonte,
        ore.cod_recurso,
        ore.nom_recurso,
        ore.exercicio*/
'';


FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    RETURN;
END;

'language 'plpgsql';

