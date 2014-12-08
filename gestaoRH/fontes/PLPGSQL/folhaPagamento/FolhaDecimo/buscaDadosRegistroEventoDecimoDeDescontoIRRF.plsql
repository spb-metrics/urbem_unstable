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
--    * Data de Criação: 16/11/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23101 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-06 10:17:40 -0300 (Qua, 06 Jun 2007) $
--
--    * Casos de uso: uc-04.05.11
--*/

CREATE OR REPLACE FUNCTION buscaDadosRegistroEventoDecimoDeDescontoIRRF(VARCHAR,INTEGER,INTEGER,INTEGER) RETURNS VARCHAR as '
DECLARE
    dtVigencia                  ALIAS FOR $1;
    inCodTipo                   ALIAS FOR $2;
    inCodContrato               ALIAS FOR $3;
    inCodPeriodoMovimentacao    ALIAS FOR $4;
    stSql                       VARCHAR;
    stTimestampRegistro         VARCHAR;
    stRetorno                   VARCHAR;
    stDesdobramento             VARCHAR;
    crCursor                    REFCURSOR;
    inCodEvento                 INTEGER;
    inCodRegistro               INTEGER;   
    stEntidade VARCHAR := recuperarBufferTexto(''stEntidade''); 
BEGIN
    stSql := ''SELECT evento_decimo_calculado.cod_evento
                    , evento_decimo_calculado.cod_registro
                    , evento_decimo_calculado.timestamp_registro
                    , evento_decimo_calculado.desdobramento
                 FROM folhapagamento''||stEntidade||''.tabela_irrf_evento
                    , folhapagamento''||stEntidade||''.tabela_irrf
                    , (   SELECT cod_tabela
                               , max(timestamp) as timestamp
                            FROM folhapagamento''||stEntidade||''.tabela_irrf
                           WHERE tabela_irrf.vigencia = ''''''||dtVigencia||''''''
                        GROUP BY cod_tabela) as max_tabela_irrf
                    , folhapagamento''||stEntidade||''.evento
                    , folhapagamento''||stEntidade||''.registro_evento_decimo
                    , folhapagamento''||stEntidade||''.ultimo_registro_evento_decimo
                    , folhapagamento''||stEntidade||''.evento_decimo_calculado
                WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                  AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                  AND tabela_irrf.cod_tabela = tabela_irrf_evento.cod_tabela
                  AND tabela_irrf.timestamp  = tabela_irrf_evento.timestamp
                  AND tabela_irrf_evento.cod_evento = evento.cod_evento
                  AND evento.cod_evento             = registro_evento_decimo.cod_evento
                  AND registro_evento_decimo.cod_evento    = ultimo_registro_evento_decimo.cod_evento
                  AND registro_evento_decimo.timestamp     = ultimo_registro_evento_decimo.timestamp
                  AND registro_evento_decimo.cod_registro  = ultimo_registro_evento_decimo.cod_registro
                  AND registro_evento_decimo.desdobramento = ultimo_registro_evento_decimo.desdobramento
                  AND registro_evento_decimo.cod_evento    = evento_decimo_calculado.cod_evento
                  AND registro_evento_decimo.timestamp     = evento_decimo_calculado.timestamp_registro
                  AND registro_evento_decimo.cod_registro  = evento_decimo_calculado.cod_registro
                  AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                  AND tabela_irrf_evento.cod_tipo = ''||inCodTipo||''
                  AND registro_evento_decimo.cod_contrato = ''||inCodContrato||''
                  AND registro_evento_decimo.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||'' '';
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO inCodEvento,inCodRegistro,stTimestampRegistro,stDesdobramento;
    CLOSE crCursor;
    stRetorno := inCodEvento||''#''||inCodRegistro||''#''||stTimestampRegistro||''#''||stDesdobramento;  
    RETURN stRetorno; 
END;
'LANGUAGE 'plpgsql';
