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
-- /**
--     * PLpgsql recuperaValoresAcumuladosCalculoComplementar
--     * Data de Criação: 24/06/2008
--     
--     
--     * @author Analista: Dagiane Vieira  
--     * @author Desenvolvedor: Diego Lemos de Souza
--     
--     * @ignore
--        
--     * Casos de uso: uc-04.05.65
--     
--     $Id:$    
-- */


CREATE OR REPLACE FUNCTION recuperaValoresAcumuladosCalculoComplementar(integer,integer,integer,varchar,varchar) RETURNS SETOF colunasValoresAcumuladosComplementar AS $$
DECLARE
    inCodContrato                       ALIAS FOR $1;
    inCodPeriodoMovimentacao            ALIAS FOR $2;
    inNumCGM                            ALIAS FOR $3;
    stNatureza                          ALIAS FOR $4;  
    stEntidade                          ALIAS FOR $5;    
    inCodPrevidencia                    INTEGER;
    inCodConfiguracao                   INTEGER;
    inIndex                             INTEGER;
    rwValoresAcumulados                 colunasValoresAcumuladosComplementar%ROWTYPE;
    stFolhas                            VARCHAR:='';
    stSql                               VARCHAR;
    stSqlSalario                        VARCHAR;
    stSqlComplementar                   VARCHAR;
    stSqlRescisao                       VARCHAR;        
    stSqlFerias                         VARCHAR;        
    stSqlDecimo                         VARCHAR;        
    stSqlAux                            VARCHAR;
    arDesdobramentosRescisao            varchar[]:='{"S","A","V","P","D"}';
    reEventos                           RECORD;
    reContratos                         RECORD;
    reEvento                            RECORD;
    nuValorEventoSalario             NUMERIC:=0;
    nuValorEventoFerias                 NUMERIC:=0;
    nuValorEventoDecimo                 NUMERIC:=0;
    nuValorEvento                 NUMERIC:=0;
    nuAux                               NUMERIC:=0;
    crCursor                            REFCURSOR;
BEGIN   
    --Consulta para busca de valores de salário
    stSqlSalario := 'SELECT sum(evento_calculado.valor) as valor
                       FROM folhapagamento'|| stEntidade ||'.evento_calculado
                          , folhapagamento'|| stEntidade ||'.registro_evento_periodo
                          , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                          , pessoal'|| stEntidade ||'.servidor
                          , folhapagamento'|| stEntidade ||'.evento
                      WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                        AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                        AND evento_calculado.cod_evento = evento.cod_evento
                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                        AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                        AND servidor.numcgm = '|| inNumCGM ||'
                        AND exists (SELECT 1
                                       FROM folhapagamento'|| stEntidade ||'.folha_situacao
                                          , (SELECT cod_periodo_movimentacao
                                                  , max(timestamp) as timestamp
                                               FROM folhapagamento'|| stEntidade ||'.folha_situacao
                                             GROUP BY cod_periodo_movimentacao) as max_folha_situacao
                                      WHERE folha_situacao.cod_periodo_movimentacao = max_folha_situacao.cod_periodo_movimentacao
                                        AND folha_situacao.timestamp = max_folha_situacao.timestamp
                                        AND folha_situacao.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao
                                        AND folha_situacao.situacao = ''f'')';
                        
    --Consulta para busca de valores de complementar
    stSqlComplementar := 'SELECT sum(evento_complementar_calculado.valor) as valor
                       FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                          , folhapagamento'|| stEntidade ||'.registro_evento_complementar
                          , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                          , pessoal'|| stEntidade ||'.servidor
                          , folhapagamento'|| stEntidade ||'.evento
                      WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                        AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                        AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                        AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                        AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                        AND evento_complementar_calculado.cod_evento = evento.cod_evento
                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                        AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                        AND servidor.numcgm = '|| inNumCGM;
                  
    --Consulta para busca de valores de rescisão
    stSqlRescisao := 'SELECT sum(evento_rescisao_calculado.valor) as valor
                       FROM folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                          , folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                          , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                          , pessoal'|| stEntidade ||'.servidor
                          , folhapagamento'|| stEntidade ||'.evento
                      WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                        AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                        AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                        AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                        AND registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato
                        AND evento_rescisao_calculado.cod_evento = evento.cod_evento
                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                        AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                        AND servidor.numcgm = '|| inNumCGM;       

                    
    --Consulta para busca de valores de férias
    stSqlFerias := 'SELECT sum(evento_ferias_calculado.valor) as valor
                       FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                          , folhapagamento'|| stEntidade ||'.registro_evento_ferias
                          , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                          , pessoal'|| stEntidade ||'.servidor
                          , folhapagamento'|| stEntidade ||'.evento
                      WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                        AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                        AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                        AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                        AND registro_evento_ferias.cod_contrato = servidor_contrato_servidor.cod_contrato
                        AND evento_ferias_calculado.cod_evento = evento.cod_evento
                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                        AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                        AND servidor.numcgm = '|| inNumCGM;       
                        
    --Consulta para busca de valores de décimo
    stSqlDecimo := 'SELECT sum(evento_decimo_calculado.valor) as valor
                       FROM folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                          , folhapagamento'|| stEntidade ||'.registro_evento_decimo
                          , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                          , pessoal'|| stEntidade ||'.servidor
                          , folhapagamento'|| stEntidade ||'.evento
                      WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                        AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                        AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                        AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                        AND registro_evento_decimo.cod_contrato = servidor_contrato_servidor.cod_contrato
                        AND evento_decimo_calculado.cod_evento = evento.cod_evento
                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                        AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                        AND servidor.numcgm = '|| inNumCGM;                              
                  
                                         
    --###################################
    --Busca de valores acumulados de IRRF    
    --###################################
    stSql := 'SELECT tabela_irrf_evento.*
                FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                   , (SELECT cod_tabela                                                 
                           , max(timestamp) as timestamp                                
                        FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento                          
                      GROUP BY cod_tabela) as max_tabela_irrf_evento                    
               WHERE tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela  
                 AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp';                                  
    FOR reEventos IN EXECUTE stSql LOOP
        nuValorEventoSalario := 0;
        nuValorEventoFerias := 0;
        nuValorEventoDecimo := 0;
       
        --Folha Salario
        stSqlAux := stSqlSalario ||' AND evento_calculado.cod_evento = '|| reEventos.cod_evento;
        stSqlAux := stSqlAux     ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';
        nuAux := selectIntoNumeric(stSqlAux);
        IF nuAux is not null THEN
            nuValorEventoSalario := nuValorEventoSalario + nuAux;
        END IF;       
       
        --Folha Complementar
        FOR inCodConfiguracao IN 1 .. 3 LOOP
            stSqlAux := stSqlComplementar ||' AND evento_complementar_calculado.cod_evento = '|| reEventos.cod_evento;
            stSqlAux := stSqlAux          ||' AND evento_complementar_calculado.cod_configuracao = '|| inCodConfiguracao;
            stSqlAux := stSqlAux          ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';
            nuAux := selectIntoNumeric(stSqlAux);
            IF nuAux is not null THEN
                IF inCodConfiguracao = 1 THEN
                    nuValorEventoSalario := nuValorEventoSalario + nuAux;
                END IF;
                IF inCodConfiguracao = 2 THEN
                    nuValorEventoFerias := nuValorEventoFerias + nuAux;
                END IF;
                IF inCodConfiguracao = 3 THEN
                    nuValorEventoDecimo := nuValorEventoDecimo + nuAux;
                END IF;                                
            END IF;
        END LOOP;
       
        --Folha Férias
        stSqlAux := stSqlFerias ||' AND evento_ferias_calculado.cod_evento = '|| reEventos.cod_evento;
        stSqlAux := stSqlAux    ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';     
        nuAux := selectIntoNumeric(stSqlAux);
        IF nuAux is not null THEN
            nuValorEventoFerias := nuValorEventoFerias + nuAux;
        END IF;
        
        --Folha Décimo
        stSqlAux := stSqlDecimo ||' AND evento_decimo_calculado.cod_evento = '|| reEventos.cod_evento;
        stSqlAux := stSqlAux    ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';     
        nuAux := selectIntoNumeric(stSqlAux);
        IF nuAux is not null THEN
            nuValorEventoDecimo := nuValorEventoDecimo + nuAux;
        END IF;        
        
        --Folha Rescisão
        FOR inIndex IN 1 .. 5 LOOP  
            stSqlAux := stSqlRescisao ||' AND evento_rescisao_calculado.cod_evento = '|| reEventos.cod_evento;
            stSqlAux := stSqlAux      ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';     
            stSqlAux := stSqlAux      ||' AND evento_rescisao_calculado.desdobramento = '|| quote_literal(arDesdobramentosRescisao[inIndex]) ||' ';
            IF stNatureza = 'D' THEN
                stSqlAux := stSqlAux     ||' AND registro_evento_rescisao.cod_contrato != '|| inCodContrato;    
            END IF;                 
            nuAux := selectIntoNumeric(stSqlAux);
            IF nuAux is not null THEN
                IF arDesdobramentosRescisao[inIndex] = 'S' THEN                
                    nuValorEventoSalario := nuValorEventoSalario + nuAux;
                END IF;
                IF arDesdobramentosRescisao[inIndex] = 'V' or arDesdobramentosRescisao[inIndex] = 'P' THEN                
                    nuValorEventoFerias := nuValorEventoFerias + nuAux;
                END IF;
                IF arDesdobramentosRescisao[inIndex] = 'D' THEN                
                    nuValorEventoDecimo := nuValorEventoDecimo + nuAux;
                END IF;                                
            END IF;        
        END LOOP;
        
        stSqlAux := 'SELECT * FROM folhapagamento'|| stEntidade ||'.evento WHERE cod_evento = '|| reEventos.cod_evento;
        OPEN crCursor FOR EXECUTE stSqlAux;
            FETCH crCursor INTO reEvento;
        CLOSE crCursor;           

        IF nuValorEventoSalario != 0 THEN
            rwValoresAcumulados.codigo      := reEvento.codigo;
            rwValoresAcumulados.descricao   := trim(reEvento.descricao ||' - Saldo Salário');
            rwValoresAcumulados.valor       := nuValorEventoSalario;
            rwValoresAcumulados.folhas      := stFolhas;
            RETURN NEXT rwValoresAcumulados;  
        END IF;
        IF nuValorEventoFerias != 0 THEN
            rwValoresAcumulados.codigo      := reEvento.codigo;
            rwValoresAcumulados.descricao   := trim(reEvento.descricao ||' - Férias Vencidas/Férias Proporcionais');
            rwValoresAcumulados.valor       := nuValorEventoFerias;
            rwValoresAcumulados.folhas      := stFolhas;
            RETURN NEXT rwValoresAcumulados;  
        END IF;
        IF nuValorEventoDecimo != 0 THEN
            rwValoresAcumulados.codigo      := reEvento.codigo;
            rwValoresAcumulados.descricao   := trim(reEvento.descricao ||' - 13º Salário');
            rwValoresAcumulados.valor       := nuValorEventoDecimo;
            rwValoresAcumulados.folhas      := stFolhas;
            RETURN NEXT rwValoresAcumulados;  
        END IF;                
    END LOOP;    
    
    --##########################################
    --Busca de valores acumulados de Previdencia    
    --##########################################    
    stSql := 'SELECT contrato_servidor_previdencia.cod_previdencia
                FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                   , (SELECT cod_contrato
                           , cod_previdencia
                           , max(timestamp) as timestamp
                        FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                      GROUP BY cod_contrato
                             , cod_previdencia) as max_contrato_servidor_previdencia
                   , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                   , (SELECT cod_previdencia
                           , max(timestamp) as timestamp
                        FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                      GROUP BY cod_previdencia) as max_previdencia_previdencia     
               WHERE contrato_servidor_previdencia.bo_excluido is false
                 AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                 AND contrato_servidor_previdencia.cod_previdencia = max_contrato_servidor_previdencia.cod_previdencia
                 AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                 AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                 AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                 AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                 AND previdencia_previdencia.tipo_previdencia = ''o''
                 AND contrato_servidor_previdencia.cod_contrato = '|| inCodContrato;
    inCodPrevidencia :=  selectIntoInteger(stSql);
        
    IF inCodPrevidencia is not null THEN
        stSql := 'SELECT previdencia_evento.*                                                
                    FROM folhapagamento'|| stEntidade ||'.previdencia_evento
                        , (SELECT cod_previdencia                           
                                , max(timestamp) as timestamp               
                             FROM folhapagamento'|| stEntidade ||'.previdencia_evento         
                           GROUP BY cod_previdencia) as max_previdencia_evento 
                    WHERE previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia 
                      AND previdencia_evento.timestamp       = max_previdencia_evento.timestamp    
                      AND previdencia_evento.cod_previdencia = '|| inCodPrevidencia;
        FOR reEventos IN EXECUTE stSql LOOP
            nuValorEventoSalario := 0;
            nuValorEventoDecimo := 0;
             
            --Folha Salário
            stSqlAux := stSqlSalario ||' AND evento_calculado.cod_evento = '|| reEventos.cod_evento;
            stSqlAux := stSqlAux     ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';
            nuAux := selectIntoNumeric(stSqlAux);
            IF nuAux is not null THEN
                nuValorEventoSalario := nuValorEventoSalario + nuAux;
            END IF;      
          
            --Folha Complementar
            FOR inCodConfiguracao IN 1 .. 3 LOOP        
                stSqlAux := stSqlComplementar ||' AND evento_complementar_calculado.cod_evento = '|| reEventos.cod_evento;
                stSqlAux := stSqlAux          ||' AND evento_complementar_calculado.cod_configuracao = '|| inCodConfiguracao;            
                stSqlAux := stSqlAux          ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';
                nuAux := selectIntoNumeric(stSqlAux);
                IF nuAux is not null THEN
                    IF inCodConfiguracao = 1 THEN
                        nuValorEventoSalario := nuValorEventoSalario + nuAux;
                    END IF;
                    IF inCodConfiguracao = 3 THEN
                        nuValorEventoDecimo := nuValorEventoDecimo + nuAux;
                    END IF;                                
                END IF;
            END LOOP;
                                           
            --Folha Férias
            stSqlAux := stSqlFerias ||' AND evento_ferias_calculado.cod_evento = '|| reEventos.cod_evento;
            stSqlAux := stSqlAux    ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';
            nuAux := selectIntoNumeric(stSqlAux);
            IF nuAux is not null THEN
                nuValorEventoSalario := nuValorEventoSalario + nuAux;
            END IF;        
                  
            --Folha Décimo
            stSqlAux := stSqlDecimo ||' AND evento_decimo_calculado.cod_evento = '|| reEventos.cod_evento;
            stSqlAux := stSqlAux    ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';     
            nuAux := selectIntoNumeric(stSqlAux);
            IF nuAux is not null THEN
                nuValorEventoDecimo := nuValorEventoDecimo + nuAux;
            END IF;        
            
            --Folha Rescisão
            FOR inIndex IN 1 .. 5 LOOP  
                stSqlAux := stSqlRescisao ||' AND evento_rescisao_calculado.cod_evento = '|| reEventos.cod_evento;
                stSqlAux := stSqlAux      ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';
                stSqlAux := stSqlAux      ||' AND evento_rescisao_calculado.desdobramento = '|| quote_literal(arDesdobramentosRescisao[inIndex]) ||' ';
                IF stNatureza = 'D' THEN
                    stSqlAux := stSqlAux     ||' AND registro_evento_rescisao.cod_contrato != '|| inCodContrato;    
                END IF;                 
                nuAux := selectIntoNumeric(stSqlAux);
                IF nuAux is not null THEN
                    IF arDesdobramentosRescisao[inIndex] = 'S' or arDesdobramentosRescisao[inIndex] = 'A' THEN  
                        nuValorEventoSalario := nuValorEventoSalario + nuAux;
                    END IF;
                    IF arDesdobramentosRescisao[inIndex] = 'D' THEN                
                        nuValorEventoDecimo := nuValorEventoDecimo + nuAux;
                    END IF;                                
                END IF;        
            END LOOP;               
              
            stSqlAux := 'SELECT * FROM folhapagamento'|| stEntidade ||'.evento WHERE cod_evento = '|| reEventos.cod_evento;
            OPEN crCursor FOR EXECUTE stSqlAux;
                FETCH crCursor INTO reEvento;
            CLOSE crCursor;           

            IF nuValorEventoSalario != 0 THEN
                rwValoresAcumulados.codigo      := reEvento.codigo;
                rwValoresAcumulados.descricao   := trim(reEvento.descricao ||' - Saldo Salário');
                rwValoresAcumulados.valor       := nuValorEventoSalario;
                rwValoresAcumulados.folhas      := stFolhas;
                RETURN NEXT rwValoresAcumulados;  
            END IF;    
            IF nuValorEventoDecimo != 0 THEN
                rwValoresAcumulados.codigo      := reEvento.codigo;
                rwValoresAcumulados.descricao   := trim(reEvento.descricao ||' - 13º Salário');
                rwValoresAcumulados.valor       := nuValorEventoDecimo;
                rwValoresAcumulados.folhas      := stFolhas;
                RETURN NEXT rwValoresAcumulados;  
            END IF;                
        END LOOP;
    END IF;
end
$$ language 'plpgsql';