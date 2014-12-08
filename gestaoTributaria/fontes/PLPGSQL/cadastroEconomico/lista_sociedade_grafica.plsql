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
* $Id: lista_sociedade_grafica.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.02.10
*               uc-05.03.11
*/

/*
$Log$
Revision 1.2  2007/01/23 11:01:32  fabio
correção da tag de caso de uso

Revision 1.1  2007/01/16 10:08:31  dibueno
*** empty log message ***

Revision 1.2  2006/09/15 10:19:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION economico.fn_lista_sociedade_grafica(INTEGER) RETURNS VARCHAR AS '
DECLARE
    inInscricaoEconomica    ALIAS FOR $1;
    reRecord                RECORD;
    stValor                 VARCHAR := '''';
    stSql                   VARCHAR ;
    inCount                 INTEGER;
BEGIN
    stSql := ''
        SELECT
            cgm.nom_cgm as valor
        FROM
            economico.sociedade as es
            INNER JOIN sw_cgm as cgm
            ON cgm.numcgm = es.numcgm
        WHERE
            es.inscricao_economica = ''||inInscricaoEconomica||''
        ORDER BY
            es.quota_socio DESC
    '';

    inCount := 1;
    FOR reRecord IN EXECUTE stSql LOOP
        stValor := stValor||'', ''|| reRecord.valor;
        inCount := inCount +1;
    END LOOP;

    stValor := SUBSTR( stValor, 2, LENGTH(stValor) );

    RETURN stValor;
END;

'language 'plpgsql';
