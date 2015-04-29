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
/**
    * Arquivo de mapeamento para a função que busca os dados de despesas pessoais.
    * Data de Criação   : 23/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/
CREATE OR REPLACE FUNCTION tcemg.fn_despesa_corrente(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    inMes               ALIAS FOR $3;
    stSql               VARCHAR := '';
    arDatas             VARCHAR[];
    reRegistro          RECORD;


BEGIN

  arDatas   := publico.mes(stExercicio,inMes);

  stSql := 'CREATE TEMPORARY TABLE tmp_elem_despesa AS (
                SELECT *
                  FROM orcamento.fn_consolidado_elem_despesa( ' || quote_literal(stExercicio) || ' ,
                                                              '''',
                                                              '|| quote_literal(arDatas[0]) ||',
                                                              '|| quote_literal(arDatas[1]) ||',
                                                              '|| quote_literal(stCodEntidade) ||',
                                                              '''','''','''','''','''','''', 0, 0
                                                            )
                    AS retorno(
                                classificacao   varchar,        
                                cod_reduzido    varchar,        
                                descricao       varchar,        
                                num_orgao       integer,        
                                nom_orgao       varchar,        
                                num_unidade     integer,        
                                nom_unidade     varchar,        
                                saldo_inicial   numeric,        
                                suplementacoes  numeric,        
                                reducoes        numeric,        
                                empenhado_mes   numeric,        
                                empenhado_ano   numeric,        
                                anulado_mes     numeric,        
                                anulado_ano     numeric,        
                                pago_mes        numeric,        
                                pago_ano        numeric,        
                                liquidado_mes   numeric,        
                                liquidado_ano   numeric,        
                                tipo_conta      varchar,        
                                nivel           integer         
                              )                                                                                                       
              ORDER BY classificacao
            )
          ';
  
  EXECUTE stSql;

  stSql := '
              SELECT 
                      ' || inMes || ' AS mes,
                      ''01'' AS cod_tipo,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(saldo_inicial,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.1%''),0.00)::VARCHAR,''.'','''') AS despPesEncSoc,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(saldo_inicial,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2%'' AND (classificacao NOT ILIKE ''3.2.9.0.21.03.00.00.00'' AND classificacao NOT ILIKE ''3.2.9.0.92.04.00.00.00'')),0.00)::VARCHAR,''.'','''') AS despJurEncDivInt,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(saldo_inicial,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2.9.0.21.03.00.00.00'' OR classificacao ILIKE ''3.2.9.0.92.04.00.00.00''),0.00)::VARCHAR,''.'','''') AS despJurEncDivExt,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(saldo_inicial,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.3%''),0.00)::VARCHAR,''.'','''') AS despOutDespCor

               UNION

              SELECT 
                      ' || inMes || ' AS mes,
                      ''02'' AS cod_tipo,
                      REPLACE(COALESCE((SELECT (SUM(COALESCE(saldo_inicial,0.00)) + SUM(COALESCE(suplementacoes,0.00)) - SUM(COALESCE(reducoes,0.00))) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.1%''),0.00)::VARCHAR, ''.'', '''') AS despPesEncSoc,
                      REPLACE(COALESCE((SELECT (SUM(COALESCE(saldo_inicial,0.00)) + SUM(COALESCE(suplementacoes,0.00)) - SUM(COALESCE(reducoes,0.00))) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2%'' AND (classificacao NOT ILIKE ''3.2.9.0.21.03.00.00.00'' AND classificacao NOT ILIKE ''3.2.9.0.92.04.00.00.00'')),0.00)::VARCHAR,''.'','''') AS despJurEncDivInt,
                      REPLACE(COALESCE((SELECT (SUM(COALESCE(saldo_inicial,0.00)) + SUM(COALESCE(suplementacoes,0.00)) - SUM(COALESCE(reducoes,0.00))) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2.9.0.21.03.00.00.00'' OR classificacao ILIKE ''3.2.9.0.92.04.00.00.00''),0.00)::VARCHAR,''.'','''') AS despJurEncDivExt,
                      REPLACE(COALESCE((SELECT (SUM(COALESCE(saldo_inicial,0.00)) + SUM(COALESCE(suplementacoes,0.00)) - SUM(COALESCE(reducoes,0.00))) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.3%''),0.00)::VARCHAR,''.'','''') AS despOutDespCor

               UNION

              SELECT 
                      ' || inMes || ' AS mes,
                      ''03'' AS cod_tipo,
                      ''000'' AS despPesEncSoc,
                      ''000'' AS despJurEncDivInt,
                      ''000'' AS despJurEncDivExt,
                      ''000'' AS despOutDespCor

               UNION

              SELECT 
                      ' || inMes || ' AS mes,
                      ''04'' AS cod_tipo,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(empenhado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.1%''),0.00)::VARCHAR,''.'','''') AS despPesEncSoc,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(empenhado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2%'' AND (classificacao NOT ILIKE ''3.2.9.0.21.03.00.00.00'' AND classificacao NOT ILIKE ''3.2.9.0.92.04.00.00.00'')),0.00)::VARCHAR,''.'','''') AS despJurEncDivInt,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(empenhado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2.9.0.21.03.00.00.00'' OR classificacao ILIKE ''3.2.9.0.92.04.00.00.00''),0.00)::VARCHAR,''.'','''') AS despJurEncDivExt,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(empenhado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.3%''),0.00)::VARCHAR,''.'','''') AS despOutDespCor

               UNION

              SELECT 
                      ' || inMes || ' AS mes,
                      ''05'' AS cod_tipo,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(liquidado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.1%''),0.00)::VARCHAR,''.'','''') AS despPesEncSoc,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(liquidado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2%'' AND (classificacao NOT ILIKE ''3.2.9.0.21.03.00.00.00'' AND classificacao NOT ILIKE ''3.2.9.0.92.04.00.00.00'')),0.00)::VARCHAR,''.'','''') AS despJurEncDivInt,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(liquidado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2.9.0.21.03.00.00.00'' OR classificacao ILIKE ''3.2.9.0.92.04.00.00.00''),0.00)::VARCHAR,''.'','''') AS despJurEncDivExt,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(liquidado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.3%''),0.00)::VARCHAR,''.'','''') AS despOutDespCor

               UNION

              SELECT 
                      ' || inMes || ' AS mes,
                      ''06'' AS cod_tipo,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(anulado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.1%''),0.00)::VARCHAR,''.'','''') AS despPesEncSoc,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(anulado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2%'' AND (classificacao NOT ILIKE ''3.2.9.0.21.03.00.00.00'' AND classificacao NOT ILIKE ''3.2.9.0.92.04.00.00.00'')),0.00)::VARCHAR,''.'','''') AS despJurEncDivInt,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(anulado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2.9.0.21.03.00.00.00'' OR classificacao ILIKE ''3.2.9.0.92.04.00.00.00''),0.00)::VARCHAR,''.'','''') AS despJurEncDivExt,
                      REPLACE(COALESCE((SELECT SUM(COALESCE(anulado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.3%''),0.00)::VARCHAR,''.'','''') AS despOutDespCor
          ';
          
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_elem_despesa;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';

