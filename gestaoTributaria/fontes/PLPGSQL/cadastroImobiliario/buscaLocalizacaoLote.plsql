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
* $Id: buscaLocalizacaoLote.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.03
*               uc-05.01.09
*/

/*
$Log$
Revision 1.2  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_busca_localizacao_lote( INTEGER ) RETURNS VARCHAR AS '

DECLARE
    inCodLote   ALIAS FOR $1;
    stRetorno   VARCHAR;
BEGIN
        SELECT loc.codigo_composto 
          INTO stRetorno
          FROM imobiliario.lote l 
    INNER JOIN imobiliario.lote_localizacao ll 
            ON ll.cod_lote = l.cod_lote 
    INNER JOIN imobiliario.localizacao loc 
            ON loc.cod_localizacao = ll.cod_localizacao
         WHERE l.cod_lote = inCodLote;

    RETURN stRetorno;
END;
'language 'plpgsql';
