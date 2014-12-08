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
--    * Data de Criação: 18/10/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23402 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-20 16:57:16 -0300 (Qua, 20 Jun 2007) $
--
--    * Casos de uso: uc-04.05.18
--*/

CREATE OR REPLACE FUNCTION deletarLogErroCalculoRescisao(INTEGER,INTEGER) RETURNS BOOLEAN as '

DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    --stDesdobramento             ALIAS FOR $3;
    stSql                       VARCHAR := '''';
    Registro                    RECORD;
    stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
BEGIN
    stSql := ''SELECT log_erro_calculo_rescisao.*
                 FROM folhapagamento''||stEntidade||''.log_erro_calculo_rescisao
                    , folhapagamento''||stEntidade||''.ultimo_registro_evento_rescisao
                    , folhapagamento''||stEntidade||''.registro_evento_rescisao
                WHERE log_erro_calculo_rescisao.cod_registro = ultimo_registro_evento_rescisao.cod_registro
                  AND log_erro_calculo_rescisao.cod_evento = ultimo_registro_evento_rescisao.cod_evento
                  AND log_erro_calculo_rescisao.timestamp = ultimo_registro_evento_rescisao.timestamp
                  AND log_erro_calculo_rescisao.desdobramento = ultimo_registro_evento_rescisao.desdobramento
                  AND ultimo_registro_evento_rescisao.cod_registro = registro_evento_rescisao.cod_registro
                  AND ultimo_registro_evento_rescisao.cod_evento = registro_evento_rescisao.cod_evento
                  AND ultimo_registro_evento_rescisao.timestamp = registro_evento_rescisao.timestamp
                  AND ultimo_registro_evento_rescisao.desdobramento = registro_evento_rescisao.desdobramento
                  AND registro_evento_rescisao.cod_contrato = ''||inCodContrato||'' 
                  AND registro_evento_rescisao.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||'' 
                '';
                  --AND registro_evento_rescisao.desdobramento = ''''''||stDesdobramento||'''''' 
                --'';
    FOR Registro IN EXECUTE stSql
    LOOP
        stSql := ''DELETE FROM folhapagamento''||stEntidade||''.log_erro_calculo_rescisao WHERE cod_registro = ''||Registro.cod_registro||''
                                                      AND cod_evento   = ''||Registro.cod_evento||''
                                                      AND timestamp    = ''''''||Registro.timestamp||''''''
                                                      AND desdobramento = ''''''||Registro.desdobramento||'''''' '';
        EXECUTE stSql;
    END LOOP;
    RETURN true;
END;
'LANGUAGE 'plpgsql';
