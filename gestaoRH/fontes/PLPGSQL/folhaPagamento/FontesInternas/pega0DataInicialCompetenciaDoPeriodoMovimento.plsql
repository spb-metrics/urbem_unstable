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
--
--
-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23095 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2005/11/28 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: recebe o codigo do movimento corrente e retorna
-- varchar com a data do primeiro dia do mes/ano
--



CREATE OR REPLACE FUNCTION pega0DataInicialCompetenciaDoPeriodoMovimento(integer) RETURNS varchar as $$

DECLARE

    inCodPeriodoMovimentacao          ALIAS FOR $1;
    dtTimestamp                       timestamp;
    stTimestamp                       VARCHAR;
    stAnoMes                          VARCHAR;

stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
 BEGIN

   dtTimestamp := selectIntoVarchar('SELECT folhapagamento'||stEntidade||'.periodo_movimentacao.dt_final FROM folhapagamento'||stEntidade||'.periodo_movimentacao WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao);

   stTimestamp := substr(dtTimestamp::VARCHAR,1,10);
   stAnoMes := substr(dtTimestamp::VARCHAR,1,8);
   stTimestamp = stAnoMes||'01';


   RETURN stTimestamp;
END;
$$ LANGUAGE 'plpgsql';

