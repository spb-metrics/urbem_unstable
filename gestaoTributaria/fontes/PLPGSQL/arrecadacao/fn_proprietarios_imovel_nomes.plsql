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
* $Id: fn_proprietarios_imovel_nomes.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-5.3.5
*/

/*
$Log$
Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/


CREATE OR REPLACE FUNCTION arrecadacao.fn_proprietarios_imovel_nomes( INTEGER ) RETURNS VARCHAR AS '
DECLARE
    inInscricaoMunicipal ALIAS FOR $1;
    inCgmProp            INTEGER;
    reRecord            RECORD;
    stSql               VARCHAR;
    stRetorno           VARCHAR;
BEGIN
    stSql := ''SELECT b.numcgm ,b.nom_cgm FROM imobiliario.proprietario a, sw_cgm b where b.numcgm = a.numcgm and inscricao_municipal = ''||inInscricaoMunicipal;
    stRetorno := '''';
    FOR reRecord IN EXECUTE stSql LOOP
        stRetorno := stRetorno||reRecord.nom_cgm||'','';
    END LOOP;

    return substring(stRetorno from 1 for ( length( stRetorno ) - 1 ) );
END;
'language 'plpgsql';
