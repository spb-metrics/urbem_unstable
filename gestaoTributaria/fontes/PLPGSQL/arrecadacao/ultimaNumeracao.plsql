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
* www.urbem.cnm.org.br
*
* $Id: ultimaNumeracao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.4  2006/11/23 11:23:37  dibueno
Alterações no procedimento de casas da numeracao

Revision 1.3  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION ultimaNumeracao(int, int) RETURNS VARCHAR AS '
DECLARE
    stTmp varchar;
    sxTmp varchar;
    scTmp varchar;
    inNumCasas  ALIAS FOR $2;
BEGIN
    SELECT
        substring( max(numeracao) from (inNumCasas+1) for char_length( max(numeracao)))
    INTO
        stTmp
    FROM
        arrecadacao.carne_limite
    WHERE
        cod_convenio = $1;

    return stTmp;
END;
' LANGUAGE 'plpgsql';
