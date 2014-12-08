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
    reRegistro          RECORD;


BEGIN

  CREATE TEMPORARY TABLE tmp_arquivo (
          mes                         INTEGER
        , despPesEncSoc               NUMERIC(14,2)
        , despJurEncDivInt            NUMERIC(14,2)
        , despJurEncDivExt            NUMERIC(14,2)
        , despOutDespCor              NUMERIC(14,2)
        , mesanula                    INTEGER      
        , despPesEncSocanula          NUMERIC(14,2)
        , codTipoini                  INTEGER
        , despJurEncDivIntanula       NUMERIC(14,2)
        , despJurEncDivExtanula       NUMERIC(14,2)
        , despOutDespCoranula         NUMERIC(14,2)
        , codTipoanula                INTEGER
        , mesatual                    INTEGER
        , despPesEncSocatual          NUMERIC(14,2)
        , despJurEncDivIntatual       NUMERIC(14,2)
        , despJurEncDivExtatual       NUMERIC(14,2)
        , despOutDespCoratual         NUMERIC(14,2)
        , codTipoatual                INTEGER
        , mesliqui                    INTEGER
        , despPesEncSocliqui          NUMERIC(14,2)
        , despJurEncDivIntliqui       NUMERIC(14,2)
        , despJurEncDivExtliqui       NUMERIC(14,2)
        , despOutDespCorliqui         NUMERIC(14,2)
        , codTipoliqui                INTEGER
        , mesatualizada               INTEGER
        , despPesEncSocatualizada     NUMERIC(14,2)
        , despJurEncDivIntatualizada  NUMERIC(14,2)
        , despJurEncDivExtatualizada  NUMERIC(14,2)
        , despOutDespCoratualizada    NUMERIC(14,2)
        , codTipoatualizada           INTEGER
        , mesemp                      INTEGER
        , despPesEncSocemp            NUMERIC(14,2)
        , despJurEncDivIntemp         NUMERIC(14,2)
        , despJurEncDivExtemp         NUMERIC(14,2)
        , despOutDespCoremp           NUMERIC(14,2)
        , codTipoemp                  INTEGER
      );

    stSql := '
    INSERT INTO tmp_arquivo(mes,despPesEncSoc) VALUES( ' || inMes || ',
    (SELECT COALESCE(total_despPesEncSoc, 0.00) AS despPesEncSoc
      FROM (
         SELECT CAST(SUM(vl_despPesEncSoc) AS NUMERIC) AS total_despPesEncSoc
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despPesEncSoc
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.47.00.03%'')
                        
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS total_despPesEncSoc,

               ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivInt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.2.9.0.92.01.00%'' 
                         OR cod_estrutural LIKE ''3.2.9.0.92.02.00%''
                         )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despJurEncDivInt,

      ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivExt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.2.9.0.92.04.00.00%'' )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despJurEncDivExt,
      
 ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivExt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.34.00.00.00%'' )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despOutDespCor

       GROUP BY mes
          )as retorno));


--DESPESA ANULADA


UPDATE  tmp_arquivo SET mesanula =  ' || inMes || ',despPesEncSocanula = 
    (SELECT COALESCE(total_despPesEncSoc, 0.00) AS despPesEncSocanula
      FROM (
         SELECT CAST(SUM(vl_despPesEncSoc) AS NUMERIC) AS total_despPesEncSoc
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despPesEncSoc
                       , EXTRACT(month from nota_liquidacao.dt_liquidacao) AS mesanula
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.47.00.03%'')
                        
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS total_despPesEncSoc,
    
 ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivInt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.2.9.0.92.01.00%'' 
                         OR cod_estrutural LIKE ''3.2.9.0.92.02.00%''
                         )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despJurEncDivIntanula,


      ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivExt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.2.9.0.92.04.00.00%'' )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despJurEncDivExtanula,
      
 ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivExt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.34.00.00.00%'' )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despOutDespCoranula    

       GROUP BY mesanula
          )  AS retorno);

UPDATE  tmp_arquivo SET mesatual =  ' || inMes || ',despPesEncSocatual = 
    (SELECT COALESCE(total_despPesEncSoc, 0.00) AS despPesEncSocatual
      FROM (
         SELECT CAST(SUM(vl_despPesEncSoc) AS NUMERIC) AS total_despPesEncSoc
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despPesEncSoc
                       , EXTRACT(month from nota_liquidacao.dt_liquidacao) AS mesatual
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.47.00.03%'')
                        
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS total_despPesEncSoc,
    
 ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivInt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.2.9.0.92.01.00%'' 
                         OR cod_estrutural LIKE ''3.2.9.0.92.02.00%''
                         )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despJurEncDivIntatual,


      ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivExt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.2.9.0.92.04.00.00%'' )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despJurEncDivExtatual,
      
 ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivExt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.34.00.00.00%'' )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despOutDespCoratual  

       GROUP BY mesatual
          )  AS retorno);

UPDATE  tmp_arquivo SET mesliqui =  ' || inMes || ',despPesEncSocliqui = 
    (SELECT COALESCE(total_despPesEncSoc, 0.00) AS despPesEncSocliqui
      FROM (
         SELECT CAST(SUM(vl_despPesEncSoc) AS NUMERIC) AS total_despPesEncSoc
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despPesEncSoc
                       , EXTRACT(month from nota_liquidacao.dt_liquidacao) AS mesliqui
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.47.00.03%'')
                        
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS total_despPesEncSoc,
    
 ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivInt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.2.9.0.92.01.00%'' 
                         OR cod_estrutural LIKE ''3.2.9.0.92.02.00%''
                         )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despJurEncDivIntliqui,


      ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivExt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.2.9.0.92.04.00.00%'' )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despJurEncDivExtliqui,
      
 ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivExt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.34.00.00.00%'' )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despOutDespCorliqui  

       GROUP BY mesliqui
          )  AS retorno);

UPDATE  tmp_arquivo SET mesatualizada =  ' || inMes || ',despPesEncSocatualizada = 
    (SELECT COALESCE(total_despPesEncSoc, 0.00) AS despPesEncSocatualizada
      FROM (
         SELECT CAST(SUM(vl_despPesEncSoc) AS NUMERIC) AS total_despPesEncSoc
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despPesEncSoc
                       , EXTRACT(month from nota_liquidacao.dt_liquidacao) AS mesatualizada
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.47.00.03%'')
                        
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS total_despPesEncSoc,
    
 ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivInt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.2.9.0.92.01.00%'' 
                         OR cod_estrutural LIKE ''3.2.9.0.92.02.00%''
                         )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despJurEncDivIntatualizada,


      ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivExt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.2.9.0.92.04.00.00%'' )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despJurEncDivExtatualizada,
      
 ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivExt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.34.00.00.00%'' )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despOutDespCoratualizada    

       GROUP BY mesemp
          )  AS retorno);






UPDATE  tmp_arquivo SET mesemp =  ' || inMes || ',despPesEncSocemp = 
    (SELECT COALESCE(total_despPesEncSoc, 0.00) AS despPesEncSocemp
      FROM (
         SELECT CAST(SUM(vl_despPesEncSoc) AS NUMERIC) AS total_despPesEncSoc
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despPesEncSoc
                       , EXTRACT(month from nota_liquidacao.dt_liquidacao) AS mesemp
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.47.00.03%'')
                        
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS total_despPesEncSoc,
    
 ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivInt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.2.9.0.92.01.00%'' 
                         OR cod_estrutural LIKE ''3.2.9.0.92.02.00%''
                         )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despJurEncDivIntemp,


      ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivExt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.2.9.0.92.04.00.00%'' )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despJurEncDivExtemp,
      
 ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_despJurEncDivExt
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
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.1.9.0.34.00.00.00%'' )
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS despOutDespCoremp    

       GROUP BY mesemp
          )  AS retorno)
 
';
   

 EXECUTE stSql;

    stSql := ' SELECT mes, COALESCE(despPesEncSoc, 0.00),COALESCE(despJurEncDivInt, 0.00),COALESCE(despJurEncDivExt, 0.00),COALESCE(despOutDespCor, 0.00), mesanula, COALESCE(despPesEncSocanula, 0.00),COALESCE(despJurEncDivIntanula, 0.00),COALESCE(despJurEncDivExtanula, 0.00),COALESCE(despOutDespCoranula, 0.00), mesatual, COALESCE(despPesEncSocatual, 0.00),COALESCE(despJurEncDivIntatual, 0.00),COALESCE(despJurEncDivExtatual, 0.00),COALESCE(despOutDespCoratual, 0.00), mesliqui, COALESCE(despPesEncSocliqui, 0.00),COALESCE(despJurEncDivIntliqui, 0.00),COALESCE(despJurEncDivExtliqui, 0.00),COALESCE(despOutDespCorliqui, 0.00), mesatualizada, COALESCE(despPesEncSocatualizada, 0.00),COALESCE(despJurEncDivIntatualizada, 0.00),COALESCE(despJurEncDivExtatualizada, 0.00),COALESCE(despOutDespCoratualizada, 0.00), mesemp, COALESCE(despPesEncSocemp, 0.00),COALESCE(despJurEncDivIntemp, 0.00),COALESCE(despJurEncDivExtemp, 0.00),COALESCE(despOutDespCoremp, 0.00),COALESCE(codTipoini, 1),COALESCE(codTipoanula, 6),COALESCE(codTipoliqui, 5), COALESCE(codTipoemp, 4), COALESCE(codTipoatual, 3), COALESCE(codTipoatualizada, 2) FROM tmp_arquivo; ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_arquivo;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';

