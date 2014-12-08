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
* $Id: fn_fracionamento_lote.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: UC-5.3.5
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.10  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_fracionamento_lote(INTEGER)  RETURNS NUMERIC AS $$
DECLARE
    inIM                        ALIAS FOR $1;
    stSql                       VARCHAR := '';
    inLoteImovel                INTEGER;
    nuSomaEdificadasLote        NUMERIC(20,4);
    nuSomaAreasImovel           NUMERIC(20,4);
    nuResultado                 NUMERIC(20,4);
    arRetorno                   VARCHAR;
    boLog                       BOOLEAN;
BEGIN
    inLoteImovel            := arrecadacao.fn_busca_lote_imovel(inIM);
    nuSomaEdificadasLote    := arrecadacao.fn_area_lote(inLoteImovel);
    nuSomaAreasImovel       := imobiliario.fn_calcula_area_imovel(inIM);

    nuResultado             := coalesce(nuSomaAreasImovel,1) / coalesce(nuSomaEdificadasLote,1);

    IF nuResultado IS NULL THEN
        boLog := arrecadacao.salva_log('arrecadacao.fn_fracionamento_lote','Erro:'||nuResultado::varchar);
    ELSE
        boLog := arrecadacao.salva_log('arrecadacao.fn_fracionamento_lote',nuResultado::varchar);
    END IF;

    RETURN nuResultado;
END;
$$ LANGUAGE 'plpgsql';
