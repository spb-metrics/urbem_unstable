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
* $Id: fn_localizacao_distrito_imovel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: UC-5.3.5
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.8  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_localizacao_distrito_imovel(INTEGER)  RETURNS INTEGER AS $$
DECLARE
    inIM                        ALIAS FOR $1;
    arRetorno                   VARCHAR;
    stTmpRes                    VARCHAR;
    inResultado                 INTEGER := 0;
    boLog                       BOOLEAN;


BEGIN
    SELECT
        coalesce(l.cod_localizacao,0)
    INTO
        inResultado
    FROM
        imobiliario.localizacao l
    INNER JOIN imobiliario.lote_localizacao ll      ON l.cod_localizacao            = ll.cod_localizacao
    INNER JOIN imobiliario.lote lote                ON ll.cod_lote                  = lote.cod_lote
    INNER JOIN imobiliario.vw_max_imovel_lote ilote ON lote.cod_lote                = ilote.cod_lote
    INNER JOIN imobiliario.imovel i                 ON ilote.inscricao_municipal    = i.inscricao_municipal
    WHERE
        i.inscricao_municipal = inIM;

    /* caso FOUND true, retorna resultado com erro setado para falso  */
    IF FOUND THEN
        boLog   := arrecadacao.salva_log('arrecadacao.fn_localizacao_distrito_imovel',inResultado::varchar);
    ELSE
        boLog   := arrecadacao.salva_log('arrecadacao.fn_localizacao_distrito_imovel','Erro:'||inResultado::varchar);
    END IF;

    RETURN inResultado;
END;
$$ LANGUAGE plpgsql;
