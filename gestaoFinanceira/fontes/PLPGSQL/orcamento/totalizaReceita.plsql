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
* Casos de uso: uc-02.01.09
*/

/*
$Log$
Revision 1.6  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_totaliza_receita(varchar) RETURNS numeric(14,2) AS '
DECLARE
    stMascaraReduzida   ALIAS FOR $1;
    stSql               VARCHAR   := '''';
    nuSoma              NUMERIC   := 0;
    crCursor            REFCURSOR;

BEGIN
--    stSql := ''SELECT
--                sum( vl_original ) as soma
--                FROM    orcamento.receita
--                WHERE   orcamento.fn_consulta_class_receita(cod_conta, exercicio::character varying, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro::text = ''''masc_class_receita''''::text AND administracao.configuracao.exercicio = exercicio))::character varying) like '''''' || stMascaraReduzida || ''%''''
--                AND     exercicio = '' || stExercicio ||'' ''|| stFiltro ;
--    OPEN crCursor FOR EXECUTE stSql;
--    FETCH crCursor INTO nuSoma;
    stSql := ''SELECT
                sum( vl_original ) as soma
                FROM    tmp_receita
                WHERE   classificacao like '''''' || stMascaraReduzida || ''%''''
                '';
    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;


    RETURN nuSoma;
END;
'language 'plpgsql';
