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
-- $Revision: 26236 $
-- $Name$
-- $Autor: Diego $
-- Date: 2005/12/14 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Recupera o valor do registro de evento do contrato
-- no referido periodo de movimentacao.
--
--/
--


CREATE OR REPLACE FUNCTION pega0ValorRegistroEventoDoContratoNoPeriodo(INTEGER,INTEGER,INTEGER) RETURNS numeric as $$

DECLARE
    inCodContrato              ALIAS FOR $1;
    inCodEvento                ALIAS FOR $2;
    inCodPeriodoMovimento      ALIAS FOR $3;
    inControle                 INTEGER;
    stSql                      VARCHAR := '';
    stDesdobramento            VARCHAR := '';
    reRegistro                 RECORD;
    nuVlr                      NUMERIC := 0.00;
    crCursor                   REFCURSOR;
    stTipoFolha                VARCHAR := '';
    inCodConfiguracao          INTEGER;
    stEntidade              VARCHAR;
 BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    inControle := recuperarBufferInteiro('inControle');

    IF inControle > 1 THEN
        stSql:= '
              SELECT  valor 
                FROM  folhapagamento'||stEntidade||'.registro_evento_fixos
               WHERE  cod_periodo_movimentacao   = '||inCodPeriodoMovimento||'
                 AND  cod_evento                 = '||inCodEvento||'
                 AND  proporcional               = FALSE ';
--         EXECUTE stSql;
-- 
--         OPEN crCursor FOR EXECUTE stSql;
--         FETCH crCursor INTO nuVlr;
--         CLOSE crCursor;
    ELSE
        stTipoFolha := recuperarBufferTexto('stTipoFolha');
        IF stTipoFolha = 'S' THEN
            stSql := ' SELECT valor
                         FROM folhapagamento'||stEntidade||'.registro_evento_ordenado 
                        WHERE cod_periodo_movimentacao   = '||inCodPeriodoMovimento||'
                          AND cod_evento                 = '||inCodEvento;
        END IF;
        IF stTipoFolha = 'F' THEN
            stDesdobramento := recuperarBufferTexto('stDesdobramento');
            stSql := ' SELECT  valor
                                 FROM  folhapagamento'||stEntidade||'.registro_evento_ferias_ordenado
                                WHERE  cod_periodo_movimentacao   = '||inCodPeriodoMovimento||'
                                  AND  desdobramento              = '|| quote_literal(stDesdobramento) ||'
                                  AND  cod_evento                 = '||inCodEvento;
        END IF;
        IF stTipoFolha = 'D' THEN
            stDesdobramento := recuperarBufferTexto('stDesdobramento');
            stSql :=' SELECT  valor
                                 FROM  folhapagamento'||stEntidade||'.registro_evento_decimo_ordenado
                                WHERE  cod_periodo_movimentacao   = '||inCodPeriodoMovimento||'
                                  AND  desdobramento              = '|| quote_literal(stDesdobramento) ||'
                                  AND  cod_evento                 = '||inCodEvento;
        END IF;
        IF stTipoFolha = 'R' THEN
            stDesdobramento := recuperarBufferTexto('stDesdobramento');
            stSql :=' SELECT  valor
                                 FROM  folhapagamento'||stEntidade||'.registro_evento_rescisao_ordenado
                                WHERE  cod_periodo_movimentacao   = '||inCodPeriodoMovimento||'
                                  AND  desdobramento              = '|| quote_literal(stDesdobramento) ||'
                                  AND  cod_evento                 = '||inCodEvento;
        END IF;
        IF stTipoFolha = 'C' THEN
            inCodConfiguracao := recuperarBufferInteiro('inCodConfiguracao');
            stSql := 'SELECT valor
                                 FROM folhapagamento'||stEntidade||'.registro_evento_ordenado
                                WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimento||'
                                  AND cod_evento               = '||inCodEvento||'
                                  AND cod_configuracao         = '||inCodConfiguracao  ;
        END IF;
    END IF;
    nuVlr := selectIntoNumeric(stSql);
    RETURN nuVlr;
END;
$$ LANGUAGE 'plpgsql';
