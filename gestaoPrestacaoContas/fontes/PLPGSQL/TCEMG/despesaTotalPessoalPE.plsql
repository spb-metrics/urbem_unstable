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
    * Data de Criação   : 20/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_despesa_total_pessoal_pe(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    inMes               ALIAS FOR $3;
    stSql               VARCHAR := '';
    reRegistro          RECORD;


BEGIN


 CREATE TEMPORARY TABLE tmp_arquivo (
          mes                    INTEGER
        , vencVantagens          NUMERIC(14,2)
        , inativos               NUMERIC(14,2)
        , pensionistas           NUMERIC(14,2)
        , salarioFamilia         NUMERIC(14,2)
        , subsPrefeito           NUMERIC(14,2)
        , subsVice               NUMERIC(14,2)
        , subsSecret             NUMERIC(14,2)   
        , obrigPatronais         NUMERIC(14,2)
        , repassePatronal        NUMERIC(14,2)
        , sentJudPessoal         NUMERIC(14,2)
        , indenDemissao          NUMERIC(14,2)
        , incDemVolunt           NUMERIC(14,2)
        , sentJudAnt             NUMERIC(14,2)
        , inatPensFontCustProp   NUMERIC(14,2) 
        , outrasDespesasPessoal  NUMERIC(14,2)
        , despAnteriores         NUMERIC(14,2)
        , exclusaoDespAnteriores NUMERIC(14,2)
        , nadaDeclararPessoal    VARCHAR

 );




 stSql := ' 
     INSERT INTO tmp_arquivo(mes,vencVantagens,inativos,pensionistas,salarioFamilia,subsPrefeito,subsSecret,subsVice,obrigPatronais,repassePatronal,sentJudPessoal,indenDemissao, incDemVolunt,sentJudAnt,inatPensFontCustProp,outrasDespesasPessoal,despAnteriores,exclusaoDespAnteriores,nadaDeclararPessoal ) VALUES( ' || inMes || ',
    (SELECT COALESCE(total_vencVantagens, 0.00) AS vencVantagens
      FROM (
         SELECT CAST(SUM(vl_vencVantagens) AS NUMERIC) AS total_vencVantagens
         
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_vencVantagens
                       , EXTRACT(month from nota_liquidacao.dt_liquidacao) AS mes
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
  ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
 AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = '|| quote_literal(stExercicio) ||'
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural like ''3.1.9.0%''
                       AND   cod_estrutural not like ''3.1.9.0.01%''
                       AND   cod_estrutural not like ''3.1.9.0.03%''
                       AND   cod_estrutural not like ''3.1.9.0.09%''
                       AND   cod_estrutural != ''3.1.9.0.11.74.00.00.00''
                       AND   cod_estrutural != ''3.1.9.0.04.15.00.00.00''
                       AND   cod_estrutural != ''3.1.9.0.13.00.00.00.00''
                       AND   cod_estrutural != ''3.1.9.0.07.03.00.00.00''
                       AND   cod_estrutural not like ''3.1.9.0.91%''
                       AND   cod_estrutural != ''3.1.9.0.94.01.01.00.00''
                       AND   cod_estrutural != ''3.1.9.0.94.01.02.00.00''
                       AND   cod_estrutural not like ''3.1.9.0.92%''
                       AND   cod_estrutural != ''3.1.9.0.16.04.00.00.00''
                       AND   cod_estrutural not like ''3.1.9.0.34%'')
  
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS total_vencVantagens

  GROUP BY mes
          ) AS retorno),
  
 (SELECT COALESCE(total_inativos, 0.00) AS inativos
      FROM (
         SELECT CAST(SUM(vl_inativos) AS NUMERIC) AS total_inativos
         
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_inativos
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
  ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
 AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = '|| quote_literal(stExercicio) ||'
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.11%'')
  
                GROUP BY nota_liquidacao.dt_liquidacao
           )AS total_inativos)as retorno ),


 (SELECT COALESCE(total_pensionistas, 0.00) AS pensionistas
      FROM (
         SELECT CAST(SUM(vl_pensionistas) AS NUMERIC) AS total_pensionistas
         
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_pensionistas
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
  ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
 AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = '|| quote_literal(stExercicio) ||'
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.03.00.00%'')
  
                GROUP BY nota_liquidacao.dt_liquidacao
           )AS total_pensionistas)as retorno ),

(SELECT COALESCE(total_salarioFamilia, 0.00) AS salarioFamilia
      FROM (
         SELECT CAST(SUM(vl_salarioFamilia) AS NUMERIC) AS total_salarioFamilia
         
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_salarioFamilia
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
  ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
 AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = '|| quote_literal(stExercicio) ||'
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.09%'')
  
                GROUP BY nota_liquidacao.dt_liquidacao
           )AS total_salarioFamilia)as retorno ),

(SELECT COALESCE(total_subsPrefeito, 0.00) AS subsPrefeito
      FROM (
         SELECT CAST(SUM(vl_subsPrefeito) AS NUMERIC) AS total_subsPrefeito
         
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_subsPrefeito
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
  ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
 AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = '|| quote_literal(stExercicio) ||'
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.11%'')
  
                GROUP BY nota_liquidacao.dt_liquidacao
           )AS total_subsPrefeito)as retorno ),

          (SELECT 0.00 as subsSecret),
          (SELECT 0.00 as subsVice),
          
(SELECT COALESCE(total_obrigPatronais, 0.00) AS obrigPatronais
      FROM (
         SELECT CAST(SUM(vl_obrigPatronais) AS NUMERIC) AS total_obrigPatronais
         
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_obrigPatronais
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
  ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
 AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = '|| quote_literal(stExercicio) ||'
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.04.15%'' OR cod_estrutural LIKE ''3.1.9.0.13.00%'')
  
                GROUP BY nota_liquidacao.dt_liquidacao
           )AS total_obrigPatronais)as retorno ),

 (SELECT COALESCE(total_repassePatronal, 0.00) AS repassePatronal
      FROM (
         SELECT CAST(SUM(vl_repassePatronal) AS NUMERIC) AS total_repassePatronal
         
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_repassePatronal
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
  ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
 AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = '|| quote_literal(stExercicio) ||'
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.07.03.00.00.00%'' OR cod_estrutural LIKE ''3.1.9.1.13%'')
  
                GROUP BY nota_liquidacao.dt_liquidacao
           )AS total_repassePatronal)as retorno ),

 
(SELECT COALESCE(total_sentJudPessoal, 0.00) AS sentJudPessoal
      FROM (
         SELECT CAST(SUM(vl_sentJudPessoal) AS NUMERIC) AS total_sentJudPessoal
         
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_sentJudPessoal
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
  ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
 AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = '|| quote_literal(stExercicio) ||'
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural  LIKE ''3.1.9.0.11%'')
  
                GROUP BY nota_liquidacao.dt_liquidacao
           )AS total_sentJudPessoal)as retorno ),

(SELECT COALESCE(total_indenDemissao, 0.00) AS indenDemissao
      FROM (
         SELECT CAST(SUM(vl_indenDemissao) AS NUMERIC) AS total_indenDemissao
         
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_indenDemissao
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
  ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
 AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = '|| quote_literal(stExercicio) ||'
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND (   cod_estrutural LIKE ''3.1.9.0.94.01.01.00.00%''  AND
                             cod_estrutural LIKE ''3.1.9.0.94.01.02.00.00%'')
  
                GROUP BY nota_liquidacao.dt_liquidacao
           )AS total_indenDemissao)as retorno ),

(SELECT COALESCE(total_incDemVolunt, 0.00) AS incDemVolunt
      FROM (
         SELECT CAST(SUM(vl_incDemVolunt) AS NUMERIC) AS total_incDemVolunt
         
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_incDemVolunt
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
  ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
 AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = '|| quote_literal(stExercicio) ||'
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND (  cod_estrutural LIKE ''3.1.9.0.11%'' )
  
                GROUP BY nota_liquidacao.dt_liquidacao
           )AS total_incDemVolunt)as retorno ),


(SELECT COALESCE(total_sentJudAnt, 0.00) AS sentJudAnt
      FROM (
         SELECT CAST(SUM(vl_sentJudAnt) AS NUMERIC) AS total_sentJudAnt
         
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_sentJudAnt
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
  ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
 AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = '|| quote_literal(stExercicio) ||'
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND (  cod_estrutural LIKE ''3.1.9.0.11%'' )
  
                GROUP BY nota_liquidacao.dt_liquidacao
           )AS total_sentJudAnt)as retorno ),

(SELECT COALESCE(total_inatPensFontCustProp, 0.00) AS inatPensFontCustProp
      FROM (
         SELECT CAST(SUM(vl_inatPensFontCustProp) AS NUMERIC) AS total_inatPensFontCustProp
         
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_inatPensFontCustProp
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
  ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
 AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = '|| quote_literal(stExercicio) ||'
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND (  cod_estrutural LIKE ''3.1.9.0.01%'' )
  
                GROUP BY nota_liquidacao.dt_liquidacao
           )AS total_inatPensFontCustProp)as retorno ),
           
           (SELECT 0.00 as outrasDespesasPessoal),
          
(SELECT COALESCE(total_despAnteriores, 0.00) AS despAnteriores
      FROM (
         SELECT CAST(SUM(vl_despAnteriores) AS NUMERIC) AS total_despAnteriores
         
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despAnteriores
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
  ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
 AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = '|| quote_literal(stExercicio) ||'
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND (  cod_estrutural  LIKE ''3.3.1.9.0.92%'' )
  
                GROUP BY nota_liquidacao.dt_liquidacao
           )AS total_despAnteriores)as retorno ),
  
           (SELECT 0.00 as exclusaoDespAnteriores),
            ''N'' 

)



';

    EXECUTE stSql;

    stSql := 'SELECT mes, COALESCE(vencVantagens, 0.00),COALESCE(inativos, 0.00),COALESCE(pensionistas,0.00),COALESCE(salarioFamilia,0.00),COALESCE(subsPrefeito,0.00),COALESCE(subsVice,0.00),COALESCE(subsSecret,0.00), COALESCE(obrigPatronais, 0.00),COALESCE(repassePatronal,0.00),COALESCE(sentJudPessoal,0.00),COALESCE(indenDemissao,0.00),COALESCE(incDemVolunt,0.00),COALESCE(sentJudAnt, 0.00),COALESCE(inatPensFontCustProp,0.00),COALESCE(outrasDespesasPessoal,0.00),COALESCE(despAnteriores,0.00),COALESCE(exclusaoDespAnteriores,0.00),nadaDeclararPessoal  FROM tmp_arquivo; ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_arquivo;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';
