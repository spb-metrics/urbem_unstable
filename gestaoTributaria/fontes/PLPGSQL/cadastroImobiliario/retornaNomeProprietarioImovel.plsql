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
* funcao imobiliario.retornaNomeProprietarioImovel(INTEGER, INTEGER)
* retorno: VARCHAR contendo o nome do proprietario do imovel, segundo o OFFSET passado
*
* Fabio Bertoldi - 20130328
*
*/

CREATE OR REPLACE FUNCTION imobiliario.retornaNomeProprietarioImovel( inImovel     INTEGER
                                                                   , inOffset     INTEGER
                                                                   ) RETURNS      VARCHAR AS $$
DECLARE
    inNomeCGM   VARCHAR;
BEGIN
       SELECT sw_cgm.nom_cgm
         INTO inNomeCGM
         FROM imobiliario.proprietario
         JOIN sw_cgm
           ON sw_cgm.numcgm = proprietario.numcgm
        WHERE proprietario.inscricao_municipal = inImovel
     ORDER BY proprietario.numcgm
       OFFSET inOffset
         ;

    RETURN inNomeCGM;
END;
$$ LANGUAGE 'plpgsql';
