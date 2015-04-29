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
    * Arquivo de mapeamento para a função que busca os dados da disponibilidade financeira 
    * Data de Criação   : 19/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_disp_financeiras(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    inMes               ALIAS FOR $3;
    stDtInicial         VARCHAR := '';
    stDtFinal           VARCHAR := '';
    stSql               VARCHAR := '';
    reRegistro          RECORD;

BEGIN

    stDtInicial := '01/01/' || stExercicio;
    stDtFinal := TO_CHAR(last_day(TO_DATE(stExercicio || '-' || inMes || '-' || '01','yyyy-mm-dd')),'dd/mm/yyyy');

    --Pega os valores "normais"
    CREATE TEMPORARY TABLE tmp_retorno AS
        SELECT inMes AS mes
             , caixa
             , conta_movimento
             , contas_vinculadas
             , aplicacoes_financeiras
             , (depositos 
                + 
                rpp_exercicio
                +
                rpp_exercicios_anteriores
                +
                outras_obrigacoes_financeiras) AS compromissado
             , 0.00 AS caixa_rpps
             , 0.00 AS conta_movimento_rpps
             , 0.00 AS contas_vinculadas_rpps
             , 0.00 AS aplicacoes_financeiras_rpps
             , 0.00 AS compromissado_rpps
          FROM tcemg.fn_rgf_anexo5_geral_disp_finan ( stExercicio , stDtInicial, stDtFinal, stCodEntidade, 'false') as
          (     caixa                                 NUMERIC
              , conta_movimento                       NUMERIC
              , contas_vinculadas                     NUMERIC
              , aplicacoes_financeiras                NUMERIC
              , outras_disponibilidades_financeiras   NUMERIC
              , depositos                             NUMERIC
              , rpp_exercicio                         NUMERIC
              , rpp_exercicios_anteriores             NUMERIC
              , outras_obrigacoes_financeiras         NUMERIC
              , restos_nao_processados                NUMERIC
          );


    stSql := '
    SELECT caixa
         , conta_movimento
         , contas_vinculadas
         , aplicacoes_financeiras
         , outras_disponibilidades_financeiras
         , depositos
         , rpp_exercicio
         , rpp_exercicios_anteriores
         , outras_obrigacoes_financeiras
         , restos_nao_processados
      FROM tcemg.fn_rgf_anexo5_geral_disp_finan ( ''' || stExercicio || ''', ''' || stDtInicial || ''', ''' || stDtFinal || ''', ''' || stCodEntidade || ''', ''true'') as
      (     caixa                                 NUMERIC
          , conta_movimento                       NUMERIC
          , contas_vinculadas                     NUMERIC
          , aplicacoes_financeiras                NUMERIC
          , outras_disponibilidades_financeiras   NUMERIC
          , depositos                             NUMERIC
          , rpp_exercicio                         NUMERIC
          , rpp_exercicios_anteriores             NUMERIC
          , outras_obrigacoes_financeiras         NUMERIC
          , restos_nao_processados                NUMERIC
      )';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        UPDATE tmp_retorno SET caixa_rpps = reRegistro.caixa
             , conta_movimento_rpps = reRegistro.conta_movimento
             , contas_vinculadas_rpps = reRegistro.contas_vinculadas
             , aplicacoes_financeiras_rpps = reRegistro.aplicacoes_financeiras
             , compromissado_rpps = (reRegistro.depositos 
                                     + 
                                     reRegistro.rpp_exercicio
                                     +
                                     reRegistro.rpp_exercicios_anteriores
                                     +
                                     reRegistro.outras_obrigacoes_financeiras); 

    END LOOP;
    
    stSql := 'SELECT * FROM tmp_retorno';                                                 

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_retorno;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  
