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
-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 28638 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2005/11/28 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Recebe o codigo do contrato e timestamp e retorna a funcao registrada para o contrato
--
-- Esta funcao devera ser revista. Como identificar se o timestamp esta dentro do periodo ainda nao fechado.
-- sera necessario o registro da data de fechamento de periodo.
--
--
--


CREATE OR REPLACE FUNCTION pega0FuncaoDoContratoNaData(integer,varchar) RETURNS integer as '

DECLARE
    inCodContratoParametro  ALIAS FOR $1;
    stTimestamp             ALIAS FOR $2;
    dtTimestamp             DATE;
    inCodFuncao             INTEGER := 0;
    inCodContrato           INTEGER;
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN
    inCodContrato := recuperaContratoServidorPensionista(inCodContratoParametro);

    dtTimestamp = to_date( stTimestamp, ''yyyy-mm-dd'' );


    inCodFuncao := selectIntoInteger(''
         SELECT cod_cargo as cod_funcao 
           FROM pessoal''||stEntidade||''.contrato_servidor_funcao
          WHERE cod_contrato = ''||inCodContrato||''
            AND vigencia <= ''''''||dtTimestamp||''''''
       ORDER BY timestamp desc 
          LIMIT 1''
               ); 
    IF inCodFuncao IS NULL THEN
        inCodFuncao := 0;
    END IF;

    RETURN inCodFuncao;
END;
' LANGUAGE 'plpgsql';

