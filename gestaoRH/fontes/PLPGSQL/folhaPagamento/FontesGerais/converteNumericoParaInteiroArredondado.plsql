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

-- script de funcao PLSQL
 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br

-- $Revision: 23095 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2005/12/22 10:50:00 $

-- Caso de uso: uc-04.05.48

-- Objetivo: Funcao de uso generico para mudanca de tipo numerico para inteiro



CREATE OR REPLACE FUNCTION converteNumericoParaInteiroArredondado(numeric) RETURNS integer as '

DECLARE
    nuValor                 ALIAS FOR $1;
    inValor                 INTEGER := 0;
BEGIN

    inValor := round( COALESCE(nuValor,0),0)::integer ;

    RETURN inValor;
END;
' LANGUAGE 'plpgsql';

