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
/* recuperarContratoServidor
 * 
 * Data de Criação : 08/07/2009


 * @author Analista : Dagiane Vieira
 * @author Desenvolvedor : Alex A V Cardoso
 
 * @package URBEM
 * @subpackage 

 */

CREATE OR REPLACE FUNCTION recuperarEventosCalculadosIntervalo(INTEGER,INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR) RETURNS SETOF colunasEventosCalculadosIntervalo AS $$
DECLARE
    inCodConfiguracao                ALIAS FOR $1;
    inCodPeriodoMovimentacaoInicial  ALIAS FOR $2;
    inCodPeriodoMovimentacaoFinal    ALIAS FOR $3;
    inCodContrato                    ALIAS FOR $4;
    inCodComplementar                ALIAS FOR $5;
    stEntidade                       ALIAS FOR $6;
    stOrdem                          ALIAS FOR $7;
    rwEventosCalculados              colunasEventosCalculadosIntervalo%ROWTYPE;
    stSql                            VARCHAR;
    stSqlComplementar                VARCHAR;
    stSqlSalario                     VARCHAR;
    stSqlFerias                      VARCHAR;
    stSqlDecimo                      VARCHAR;
    stSqlRescisao                    VARCHAR;
    stOrdemAux                       VARCHAR;
    reRegistro                       RECORD;
    arDesdobramentos                 VARCHAR[];
    inIndex                          INTEGER;
BEGIN

    IF stOrdem IS NULL OR trim(stOrdem) = '' OR trim(stOrdem) = 'null' THEN 
        stOrdemAux = ' evento.codigo ';
    ELSE
        stOrdemAux = stOrdem;
    END IF;

    IF inCodConfiguracao = 0 THEN
        arDesdobramentos  := '{'''',''F'',''D'',''A'',''I''}';
        stSqlComplementar := '
                      SELECT registro_evento_complementar.cod_periodo_movimentacao
                           , registro_evento_complementar.cod_contrato
                           , evento_complementar_calculado.*
                           , evento.*
                           , sequencia_calculo.sequencia
                           , sequencia_calculo.descricao as desc_sequencia
                        FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                  INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_complementar
                          ON registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                         AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                         AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                         AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                         AND registro_evento_complementar.cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal;
                         
        --                 
        -- tratamento cod_contrato = 0 utilizado para retornar os valores calculados para todos os contratos no periodo, na folha
        -- condicao cod_contrato <> 0 onde nao é tratada condicao diferente de nulo, pois como a pl é utilizada em joins, 
        -- quando cod_contrato FOR nulo, força erro de sql (utilizar somente em INNER JOINs com cod_contrato preenchido)
        --
        
        IF inCodContrato <> 0 THEN
            stSqlComplementar := stSqlComplementar  ||'
                         AND registro_evento_complementar.cod_contrato = '|| inCodContrato ||' ';
        END IF;
        
        stSqlComplementar := stSqlComplementar  ||'                 
                         AND registro_evento_complementar.cod_complementar = '|| inCodComplementar ||'
                  INNER JOIN folhapagamento'|| stEntidade ||'.evento
                          ON evento.cod_evento = evento_complementar_calculado.cod_evento
                  INNER JOIN folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
                          ON sequencia_calculo_evento.cod_evento = evento.cod_evento
                  INNER JOIN folhapagamento'|| stEntidade ||'.sequencia_calculo
                          ON sequencia_calculo.cod_sequencia = sequencia_calculo_evento.cod_sequencia';
    END IF;
    IF inCodConfiguracao = 1 THEN
        arDesdobramentos := '{'''',''F'',''D'',''A'',''I''}';
        stSqlSalario := '
                      SELECT registro_evento_periodo.cod_periodo_movimentacao
                           , registro_evento_periodo.cod_contrato
                           , evento_calculado.*
                           , evento.*
                           , sequencia_calculo.sequencia
                           , sequencia_calculo.descricao as desc_sequencia
                        FROM folhapagamento'|| stEntidade ||'.evento_calculado
                  INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_periodo
                          ON registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                         AND registro_evento_periodo.cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal;
                         
        IF inCodContrato <> 0 THEN
            stSqlSalario := stSqlSalario  ||'
                         AND registro_evento_periodo.cod_contrato = '|| inCodContrato ||' ';
        END IF;
                         
        stSqlSalario := stSqlSalario  ||'                 
                  INNER JOIN folhapagamento'|| stEntidade ||'.evento
                          ON evento.cod_evento = evento_calculado.cod_evento
                  INNER JOIN folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
                          ON sequencia_calculo_evento.cod_evento = evento.cod_evento
                  INNER JOIN folhapagamento'|| stEntidade ||'.sequencia_calculo
                          ON sequencia_calculo.cod_sequencia = sequencia_calculo_evento.cod_sequencia';
    END IF;
    IF inCodConfiguracao = 2 THEN
        arDesdobramentos := '{''F'',''D'',''A''}';
        stSqlFerias := '    
                      SELECT registro_evento_ferias.cod_periodo_movimentacao
                           , registro_evento_ferias.cod_contrato
                           , evento_ferias_calculado.*
                           , evento.*
                           , sequencia_calculo.sequencia
                           , sequencia_calculo.descricao as desc_sequencia
                        FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                  INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_ferias
                          ON registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                         AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                         AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                         AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                         AND registro_evento_ferias.cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal;
                         
        IF inCodContrato <> 0 THEN
            stSqlFerias := stSqlFerias  ||'
                         AND registro_evento_ferias.cod_contrato = '|| inCodContrato ||' ';
        END IF;
                         
        stSqlFerias := stSqlFerias  ||'                 
                  INNER JOIN folhapagamento'|| stEntidade ||'.evento
                          ON evento.cod_evento = evento_ferias_calculado.cod_evento
                  INNER JOIN folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
                          ON sequencia_calculo_evento.cod_evento = evento.cod_evento
                  INNER JOIN folhapagamento'|| stEntidade ||'.sequencia_calculo
                          ON sequencia_calculo.cod_sequencia = sequencia_calculo_evento.cod_sequencia';
    END IF;
    IF inCodConfiguracao = 3 THEN
        arDesdobramentos := '{''A'',''D'',''C''}';
        stSqlDecimo := '
                      SELECT registro_evento_decimo.cod_periodo_movimentacao
                           , registro_evento_decimo.cod_contrato
                           , evento_decimo_calculado.*
                           , evento.*
                           , sequencia_calculo.sequencia
                           , sequencia_calculo.descricao as desc_sequencia
                        FROM folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                  INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_decimo
                          ON registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                         AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                         AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                         AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                         AND registro_evento_decimo.cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal;
                         
        IF inCodContrato <> 0 THEN
            stSqlDecimo := stSqlDecimo  ||'
                         AND registro_evento_decimo.cod_contrato = '|| inCodContrato ||' ';
        END IF;
                         
        stSqlDecimo := stSqlDecimo  ||'                 
                  INNER JOIN folhapagamento'|| stEntidade ||'.evento
                          ON evento.cod_evento = evento_decimo_calculado.cod_evento
                  INNER JOIN folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
                          ON sequencia_calculo_evento.cod_evento = evento.cod_evento
                  INNER JOIN folhapagamento'|| stEntidade ||'.sequencia_calculo
                          ON sequencia_calculo.cod_sequencia = sequencia_calculo_evento.cod_sequencia';
    END IF;
    IF inCodConfiguracao = 4 THEN
        arDesdobramentos := '{''S'',''A'',''V'',''P'',''D''}';
        stSqlRescisao := '    
                      SELECT registro_evento_rescisao.cod_periodo_movimentacao
                           , registro_evento_rescisao.cod_contrato
                           , evento_rescisao_calculado.*
                           , evento.*
                           , sequencia_calculo.sequencia
                           , sequencia_calculo.descricao as desc_sequencia
                        FROM folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                  INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                          ON registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                         AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                         AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                         AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                         AND registro_evento_rescisao.cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal;
                         
        IF inCodContrato <> 0 THEN
            stSqlRescisao := stSqlRescisao  ||'
                         AND registro_evento_rescisao.cod_contrato = '|| inCodContrato ||' ';
        END IF;
                         
        stSqlRescisao := stSqlRescisao  ||'                 
                  INNER JOIN folhapagamento'|| stEntidade ||'.evento
                          ON evento.cod_evento = evento_rescisao_calculado.cod_evento
                  INNER JOIN folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
                          ON sequencia_calculo_evento.cod_evento = evento.cod_evento
                  INNER JOIN folhapagamento'|| stEntidade ||'.sequencia_calculo
                          ON sequencia_calculo.cod_sequencia = sequencia_calculo_evento.cod_sequencia';
    END IF;

    inIndex := 1;
    -- Recupera todos os eventos calculados, desdobramento por desdobramento
    -- Como na folha complementar e na salário o desdobramento não é obrigatório, 
    -- então quando FOR NULL ele tb precisa pegar os eventos sem desdobramento
    
    WHILE arDesdobramentos[inIndex] IS NOT NULL LOOP
        IF inCodConfiguracao = 0 THEN
            IF arDesdobramentos[inIndex] != '''''' THEN
                stSql := stSqlComplementar || ' WHERE evento_complementar_calculado.desdobramento = '|| arDesdobramentos[inIndex];
            ELSE
                stSql := stSqlComplementar || ' WHERE (trim(evento_complementar_calculado.desdobramento) = '''' OR evento_complementar_calculado.desdobramento IS NULL)';
            END IF;    
        END IF;
        IF inCodConfiguracao = 1 THEN
            IF arDesdobramentos[inIndex] != '''''' THEN
                stSql := stSqlSalario || ' WHERE evento_calculado.desdobramento = '|| arDesdobramentos[inIndex];
            ELSE
                stSql := stSqlSalario || ' WHERE (trim(evento_calculado.desdobramento) = '''' OR evento_calculado.desdobramento IS NULL)';
            END IF;
        END IF;
        IF inCodConfiguracao = 2 THEN
            stSql := stSqlFerias || ' WHERE evento_ferias_calculado.desdobramento = '|| arDesdobramentos[inIndex];
        END IF;
        IF inCodConfiguracao = 3 THEN
            stSql := stSqlDecimo || ' WHERE evento_decimo_calculado.desdobramento = '|| arDesdobramentos[inIndex];
        END IF;
        IF inCodConfiguracao = 4 THEN
            stSql := stSqlRescisao || ' WHERE evento_rescisao_calculado.desdobramento = '|| arDesdobramentos[inIndex];
        END IF;
        
        stSql := stSql ||' ORDER BY cod_periodo_movimentacao, '|| stOrdemAux;
        
        FOR reRegistro IN EXECUTE stSql LOOP
            rwEventosCalculados.cod_periodo_movimentacao    := reRegistro.cod_periodo_movimentacao;
            rwEventosCalculados.cod_contrato                := reRegistro.cod_contrato;
            rwEventosCalculados.cod_evento                  := reRegistro.cod_evento;
            rwEventosCalculados.codigo                      := reRegistro.codigo;
            rwEventosCalculados.descricao                   := reRegistro.descricao;        
            rwEventosCalculados.natureza                    := reRegistro.natureza;
            rwEventosCalculados.tipo                        := reRegistro.tipo;             
            rwEventosCalculados.fixado                      := reRegistro.fixado;           
            rwEventosCalculados.limite_calculo              := reRegistro.limite_calculo;   
            rwEventosCalculados.apresenta_parcela           := reRegistro.apresenta_parcela;
            rwEventosCalculados.evento_sistema              := reRegistro.evento_sistema;   
            rwEventosCalculados.sigla                       := reRegistro.sigla;            
            rwEventosCalculados.valor                       := reRegistro.valor;            
            rwEventosCalculados.quantidade                  := reRegistro.quantidade;            
            rwEventosCalculados.desdobramento               := reRegistro.desdobramento;            
            rwEventosCalculados.desdobramento_texto         := getDesdobramentoFolha(inCodConfiguracao,reRegistro.desdobramento,stEntidade);
            rwEventosCalculados.sequencia                   := reRegistro.sequencia;
            rwEventosCalculados.desc_sequencia              := reRegistro.desc_sequencia;            
            RETURN NEXT rwEventosCalculados;
        END LOOP;
        inIndex := inIndex + 1;
    END LOOP;
END
$$ LANGUAGE 'PLPGSQL';
