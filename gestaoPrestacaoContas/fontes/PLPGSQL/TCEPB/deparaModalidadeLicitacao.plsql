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
CREATE OR REPLACE FUNCTION tcepb.fn_depara_modalidade_licitacao(INTEGER) RETURNS INTEGER AS '
DECLARE
    inIndice            ALIAS FOR $1;
    inResult            INTEGER := 0;
BEGIN
    IF ( inIndice = 1 ) THEN 
        inResult := 3;
    ELSEIF ( inIndice = 2 ) THEN
        inResult := 2;
    ELSEIF ( inIndice = 3 ) THEN
        inResult := 1;
    ELSEIF ( inIndice = 5 ) THEN
        inResult := 4;
    ELSEIF ( inIndice = 8 ) THEN
        inResult := 6;
    ELSEIF ( inIndice = 9 ) THEN
        inResult := 8;
    ELSE
        inResult := null;
    END IF;

    return inResult;
END;


'LANGUAGE 'plpgsql';
