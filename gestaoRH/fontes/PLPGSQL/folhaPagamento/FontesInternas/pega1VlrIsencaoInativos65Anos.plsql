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
* script de funcao PLSQL
* 
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br

* $Revision: 23095 $
* $Name$
* $Autor: Marcia $
* Date: 2006/04/24 10:50:00 $
*
* Caso de uso: uc-04.05.13
*
* Objetivo: O codigo da tabela de irrf e por default = 1. Recebe a data de referencia 
* para a definicao do valor de isencao para inativos e pensionistas com 65 anos ou mais
* 
*/



CREATE OR REPLACE FUNCTION pega1VlrIsencaoInativos65Anos() RETURNS numeric as '

DECLARE
    stDataFinalCompetencia           VARCHAR;

    inCodTabela                      INTEGER := 1;
    nuVlrIsencao65Anos               NUMERIC := 0.00;
    dtTimestamp                      DATE;

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    stDataFinalCompetencia := recuperarBufferTexto( ''stDataFinalCompetencia'' );
    dtTimestamp = to_date( stDataFinalCompetencia, ''yyyy-mm-dd'' );


    nuVlrIsencao65Anos := selectIntoNumeric(''
        SELECT vl_limite_isencao
        FROM  folhapagamento''||stEntidade||''.tabela_irrf
        WHERE cod_tabela = ''||inCodTabela||''
          AND vigencia <= ''''''||dtTimestamp||''''''
        ORDER BY timestamp desc
        LIMIT 1 '') ;

    IF nuVlrIsencao65Anos is null THEN
       nuVlrIsencao65Anos := 0.00;
    END IF;


    RETURN nuVlrIsencao65Anos;
END;
' LANGUAGE 'plpgsql';

