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
--     * PLpgsql recuperaValoresAcumuladosCalculoDecimo
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

CREATE OR REPLACE FUNCTION recuperaValoresAcumuladosCalculoDecimo(integer,integer,integer,varchar,varchar) RETURNS SETOF colunasValoresAcumuladosDecimo AS $$
DECLARE
    inCodContrato                       ALIAS FOR $1;
    inCodPeriodoMovimentacao            ALIAS FOR $2;
    inNumCGM                            ALIAS FOR $3;
    stNatureza                          ALIAS FOR $4;   
    stEntidade                          ALIAS FOR $5;     
    inCodPrevidencia                    INTEGER;
    rwValoresAcumulados                 colunasValoresAcumuladosDecimo%ROWTYPE;
    stFolhas                            VARCHAR:='';
    stSql                               VARCHAR;
    stSqlSalario                        VARCHAR;
    stSqlComplementar                   VARCHAR;
    stSqlRescisao                       VARCHAR;        
    stSqlFerias                         VARCHAR;        
    stSqlDecimo                         VARCHAR;        
    stSqlAux                            VARCHAR;
    reEventos                           RECORD;
    reContratos                         RECORD;
    reEvento                            RECORD;
    nuValorEvento                       NUMERIC:=0;
    nuAux                               NUMERIC:=0;
    crCursor                            REFCURSOR;
BEGIN   
                        
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
        nuValorEvento := 0;
             
        --Folha Decimo
        stSqlAux := stSqlDecimo ||' AND evento_decimo_calculado.cod_evento = '|| reEventos.cod_evento;
        stSqlAux := stSqlAux    ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';
        IF stNatureza = 'D' THEN
            stSqlAux := stSqlAux     ||' AND registro_evento_decimo.cod_contrato != '|| inCodContrato;    
        END IF;                 
        nuAux := selectIntoNumeric(stSqlAux);
        IF nuAux is not null THEN
            nuValorEvento := nuValorEvento + nuAux;
        END IF;
        
        stSqlAux := 'SELECT * FROM folhapagamento'|| stEntidade ||'.evento WHERE cod_evento = '|| reEventos.cod_evento;
        OPEN crCursor FOR EXECUTE stSqlAux;
            FETCH crCursor INTO reEvento;
        CLOSE crCursor;           

        IF nuValorEvento != 0 THEN
            rwValoresAcumulados.codigo      := reEvento.codigo;
            rwValoresAcumulados.descricao   := trim(reEvento.descricao);
            rwValoresAcumulados.valor       := nuValorEvento;
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
            nuValorEvento := 0;
                                                            
            --Folha Decimo
            stSqlAux := stSqlDecimo ||' AND evento_decimo_calculado.cod_evento = '|| reEventos.cod_evento;
            stSqlAux := stSqlAux    ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';
            IF stNatureza = 'D' THEN
                stSqlAux := stSqlAux     ||' AND registro_evento_decimo.cod_contrato != '|| inCodContrato;    
            END IF;                    
            nuAux := selectIntoNumeric(stSqlAux);
            IF nuAux is not null THEN
                nuValorEvento := nuValorEvento + nuAux;
            END IF;        
                  
              
            stSqlAux := 'SELECT * FROM folhapagamento'|| stEntidade ||'.evento WHERE cod_evento = '|| reEventos.cod_evento;
            OPEN crCursor FOR EXECUTE stSqlAux;
                FETCH crCursor INTO reEvento;
            CLOSE crCursor;           

            IF nuValorEvento != 0 THEN
                rwValoresAcumulados.codigo      := reEvento.codigo;
                rwValoresAcumulados.descricao   := trim(reEvento.descricao);
                rwValoresAcumulados.valor       := nuValorEvento;
                rwValoresAcumulados.folhas      := stFolhas;
                RETURN NEXT rwValoresAcumulados;  
            END IF;          
        END LOOP;
    END IF;
end
$$ language 'plpgsql';
