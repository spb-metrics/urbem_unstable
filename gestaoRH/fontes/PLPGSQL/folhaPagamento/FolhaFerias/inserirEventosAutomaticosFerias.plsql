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
--    $Revision: 23133 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-07 12:40:10 -0300 (Qui, 07 Jun 2007) $
--
--    * Casos de uso: uc-04.05.09
--*/

CREATE OR REPLACE FUNCTION inserirEventosAutomaticosFerias(INTEGER) RETURNS BOOLEAN as '

DECLARE
    inCodTipo                   ALIAS FOR $1;
    inCodContrato               INTEGER;
    inCodPeriodoMovimentacao    INTEGER;
    inCodEvento                 INTEGER;
    dtVigencia                  VARCHAR := '''';
    stNatureza                  VARCHAR := '''';
    boRetorno                   BOOLEAN;
    stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
BEGIN
    inCodContrato              := recuperarBufferInteiro(''inCodContrato'');
    inCodPeriodoMovimentacao   := recuperarBufferInteiro(''inCodPeriodoMovimentacao'');
    dtVigencia                 := recuperarBufferTexto(''dtVigenciaIrrf'');
    inCodEvento := selectIntoInteger('' SELECT cod_evento
                               FROM folhapagamento''||stEntidade||''.tabela_irrf_evento
                                  , folhapagamento''||stEntidade||''.tabela_irrf
                                  , (SELECT max(timestamp) as timestamp
                                          , cod_tabela
                                       FROM folhapagamento''||stEntidade||''.tabela_irrf
                                      WHERE tabela_irrf.vigencia = ''''''||dtVigencia||''''''
                                   GROUP BY cod_tabela) as max_tabela_irrf
                              WHERE tabela_irrf_evento.cod_tipo = ''||inCodTipo||''
                                AND tabela_irrf_evento.cod_tabela = tabela_irrf.cod_tabela
                                AND tabela_irrf_evento.timestamp  = tabela_irrf.timestamp
                                AND tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp'');
    stNatureza                 := pega0NaturezaEvento(inCodEvento);
    boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,''F'');
    IF NOT(stNatureza = ''D'' OR inCodTipo = 5 OR inCodTipo = 4 ) THEN
        IF inCodTipo != 2 THEN
            boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,''A'');
        END IF;
        boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,''D'');
    END IF;
    return boRetorno;
END;
'LANGUAGE 'plpgsql';
