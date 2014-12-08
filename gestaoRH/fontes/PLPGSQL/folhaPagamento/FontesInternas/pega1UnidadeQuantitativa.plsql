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
--*
-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23095 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2006/04/18  $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: a partir dos dados do buffer do evento e a data de 
-- referencia (data final da competencia 
-- retornando a unidade quantitativa cadatrada para o evento. 
-- Caso campo nulo retornara 0.
-- folha fechada ( processo de virada - tabela periodo_moviemntacao_situacao - utilizar a data do fechamento ou entao now() se nao estiver virada.
--???? 
--/
--


CREATE OR REPLACE FUNCTION pega1UnidadeQuantitativa() RETURNS Numeric as $$

DECLARE
    inCodEvento                 INTEGER;
    stSql                       VARCHAR;
    crCursor                    REFCURSOR;
    nuQtd                       NUMERIC := 0.00;
    stEntidade               VARCHAR;
 BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    inCodEvento := recuperarBufferInteiroPilha('inCodEvento');

     nuQtd := selectIntoNumeric (' SELECT COALESCE(unidade_quantitativa,0)
          FROM folhapagamento'||stEntidade||'.evento_evento
        	  WHERE cod_evento = '||inCodEvento||'
          ORDER BY timestamp desc 
          LIMIT 1 ') ;
     RETURN nuQtd  ;

END;

$$ LANGUAGE 'plpgsql';


