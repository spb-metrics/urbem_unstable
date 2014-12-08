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
--/**
--    * Função PLSQL
--    * Data de Criação: 03/06/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23095 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $
--
--    * Casos de uso: uc-04.05.10
--*/

CREATE OR REPLACE FUNCTION insertRegistroEventoAutomaticoComplementar(INTEGER,INTEGER,INTEGER,INTEGER,INTEGER) RETURNS BOOLEAN as '

DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inCodEvento                 ALIAS FOR $3;
    inCodComplementar           ALIAS FOR $4;
    inCodConfiguracao           ALIAS FOR $5;
    inCodRegistro               INTEGER;
    inContador                  INTEGER;
    stTimestamp                 TIMESTAMP;
    stSql                       VARCHAR := '''';
    stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
BEGIN
    inContador := selectIntoInteger(''SELECT COUNT(ultimo_registro_evento_complementar.*) AS contador
                              FROM folhapagamento''||stEntidade||''.ultimo_registro_evento_complementar
                                 , folhapagamento''||stEntidade||''.registro_evento_complementar
                             WHERE ultimo_registro_evento_complementar.cod_registro = registro_evento_complementar.cod_registro
                               AND ultimo_registro_evento_complementar.cod_evento   = registro_evento_complementar.cod_evento
                               AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao
                               AND ultimo_registro_evento_complementar.timestamp = registro_evento_complementar.timestamp
                               AND registro_evento_complementar.cod_contrato = ''||inCodContrato||''
                               AND registro_evento_complementar.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||''
                               AND registro_evento_complementar.cod_configuracao = ''||inCodConfiguracao||''
                               AND registro_evento_complementar.cod_complementar = ''||inCodComplementar||''
                               AND ultimo_registro_evento_complementar.cod_evento = ''||inCodEvento);
    IF inContador = 0 THEN
        inCodRegistro := selectIntoInteger('' SELECT max(cod_registro)+1 as cod_registro
                                     FROM folhapagamento''||stEntidade||''.registro_evento_complementar'');
        stTimestamp = now();
        stSql := ''INSERT INTO folhapagamento''||stEntidade||''.registro_evento_complementar (cod_registro,timestamp,cod_evento,cod_configuracao,cod_contrato,cod_complementar,cod_periodo_movimentacao)
                        VALUES (''||inCodRegistro||'',TO_TIMESTAMP(''''''||stTimestamp||'''''',''''yyyy-mm-dd hh24:mi:ss.us''''),''||inCodEvento||'',''||inCodConfiguracao||'',''||inCodContrato||'',''||inCodComplementar||'',''||inCodPeriodoMovimentacao||'')'';        
        EXECUTE stSql;
        stSql := ''INSERT INTO folhapagamento''||stEntidade||''.ultimo_registro_evento_complementar (timestamp,cod_registro,cod_evento,cod_configuracao)
                        VALUES (TO_TIMESTAMP(''''''||stTimestamp||'''''',''''yyyy-mm-dd hh24:mi:ss.us''''),''||inCodRegistro||'',''||inCodEvento||'',''||inCodConfiguracao||'')'';
        EXECUTE stSql;
    END IF;
    RETURN true;
END;
'LANGUAGE 'plpgsql';