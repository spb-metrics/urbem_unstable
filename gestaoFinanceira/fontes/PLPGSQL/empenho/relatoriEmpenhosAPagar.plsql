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
/*
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: relatoriEmpenhosAPagar.plsql 62328 2015-04-24 13:35:46Z jean $
*
* Casos de uso: uc-02.03.07
*/

CREATE OR REPLACE FUNCTION empenho.fn_relatorio_empenhos_a_pagar(varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stFiltro                        ALIAS FOR $1;
    stCodEntidades                  ALIAS FOR $2;
    stExercicio                     ALIAS FOR $3;
    stDataInicial                   ALIAS FOR $4;
    stDataFinal                     ALIAS FOR $5;
    stDataSituacao                  ALIAS FOR $6;
    inCodEmpenhoInicial             ALIAS FOR $7;
    inCodEmpenhoFinal               ALIAS FOR $8;
    inCGM                           ALIAS FOR $9;
    inNumOrgao                      ALIAS FOR $10;
    inOrdenacao                     ALIAS FOR $11;
    stCodRecurso                    ALIAS FOR $12;
    stDestinacaoRecurso             ALIAS FOR $13;

    stSql               VARCHAR   := '';
    reRegistro          RECORD;

BEGIN
    stSql := 'CREATE TEMPORARY TABLE tmp_empenhado AS (
        SELECT
            e.cod_entidade      as entidade,
            e.cod_empenho       as empenho,
            e.exercicio         as exercicio,
            sum(ipe.vl_total)   as valor
        FROM
            empenho.empenho             as e,
            empenho.item_pre_empenho    as ipe,
            empenho.pre_empenho         as pe
    
    LEFT JOIN empenho.pre_empenho_despesa as ped
           ON pe.exercicio        = ped.exercicio
          AND pe.cod_pre_empenho  = ped.cod_pre_empenho
            
    LEFT JOIN orcamento.despesa as ode
           ON ped.exercicio       = ode.exercicio
          AND ped.cod_despesa     = ode.cod_despesa
            
        WHERE
            e.cod_entidade      IN (' || quote_literal(stCodEntidades) || ') AND
            e.exercicio         =   ' || quote_literal(stExercicio) || '::varchar AND ';

            if (stDataInicial is not null and stDataInicial <> '') then
               stSql := stSql || ' e.dt_empenho >= to_date(''' || stDataInicial || ''', ''dd/mm/yyyy'') AND';
            end if;

            if (stDataFinal is not null and stDataFinal <> '') then
               stSql := stSql || ' e.dt_empenho <= to_date(''' || stDataFinal || ''',''dd/mm/yyyy'') AND';
            end if;

            if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <> '') then
               stSql := stSql || ' e.cod_empenho >= ''' || inCodEmpenhoInicial || ''' AND ';
            end if;

            if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <> '') then
               stSql := stSql || ' e.cod_empenho <= ''' || inCodEmpenhoFinal || ''' AND ';
            end if;

            if (inNumOrgao is not null and inNumOrgao <> '') then
               stSql := stSql || ' ode.num_orgao = ''' || inNumOrgao || ''' AND ';
            end if;


        stSql := stSql || '
            --Ligação EMPENHO : PRE_EMPENHO
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho AND

            --Ligação PRE_EMPENHO : ITEM_PRE_EMPENHO
            pe.exercicio        = ipe.exercicio AND
            pe.cod_pre_empenho  = ipe.cod_pre_empenho
        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )';

        EXECUTE stSql;

   stSql := 'CREATE TEMPORARY TABLE tmp_anulado AS (
        SELECT
            e.cod_entidade      as entidade,
            e.cod_empenho       as empenho,
            e.exercicio         as exercicio,
            sum(eai.vl_anulado) as valor
        FROM
            empenho.empenho                 as e,
            empenho.empenho_anulado         as ea,
            empenho.empenho_anulado_item    as eai,
            empenho.pre_empenho             as pe
            
  LEFT JOIN empenho.pre_empenho_despesa as ped
         ON pe.exercicio        = ped.exercicio
        AND pe.cod_pre_empenho  = ped.cod_pre_empenho
            
  LEFT JOIN orcamento.despesa as ode
         ON ped.exercicio       = ode.exercicio
        AND ped.cod_despesa     = ode.cod_despesa

        WHERE
            e.cod_entidade      IN (' || stCodEntidades || ') AND
            e.exercicio         =   ' || quote_literal( stExercicio ) || '::varchar AND ';

        if (stDataInicial is not null and stDataInicial <> '' ) then
            stSql := stSql || ' e.dt_empenho >= to_date(' || quote_literal( stDataInicial ) || ', ''dd/mm/yyyy'') AND ';
        end if;

        if (stDataFinal is not null and stDataFinal <> '') then
            stSql := stSql || ' e.dt_empenho <= to_date(' || quote_literal( stDataFinal ) || ', ''dd/mm/yyyy'') AND ';
        end if;

        if (stDataSituacao is not null and stDataSituacao <> '') then
           stSql := stSql || ' to_date( to_char( ea.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'' ) <= to_date ('|| quote_literal( stDataSituacao ) || ',''dd/mm/yyyy'') AND ';
        end if;

        if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <> '') then
           stSql := stSql || ' e.cod_empenho >= ' || inCodEmpenhoInicial || ' AND ';
        end if;

        if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <> '') then
           stSql := stSql || ' e.cod_empenho <= ' || inCodEmpenhoFinal || ' AND ';
        end if;

        if (inNumOrgao is not null and inNumOrgao <> '') then
            stSql := stSql || ' ode.num_orgao = ' || inNumOrgao || ' AND ';
        end if;

        stSql := stSql || '
            --Ligação EMPENHO : PRE_EMPENHO
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho AND

            --Ligação EMPENHO : EMPENHO ANULADO
            e.exercicio        = ea.exercicio AND
            e.cod_entidade     = ea.cod_entidade AND
            e.cod_empenho      = ea.cod_empenho AND

            --Ligação EMPENHO ANULADO : EMPENHO ANULADO ITEM
            ea.exercicio        = eai.exercicio AND
            ea.timestamp        = eai.timestamp AND
            ea.cod_entidade     = eai.cod_entidade AND
            ea.cod_empenho      = eai.cod_empenho
        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )';
        EXECUTE stSql;

   stSql := 'CREATE TEMPORARY TABLE tmp_liquidado AS (
        SELECT
            e.cod_entidade      as entidade,
            e.cod_empenho       as empenho,
            e.exercicio         as exercicio,
            sum(nli.vl_total)   as valor
        FROM
            empenho.empenho                 as e,
            empenho.nota_liquidacao         as nl,
            empenho.nota_liquidacao_item    as nli,
            empenho.pre_empenho             as pe
  
  LEFT JOIN empenho.pre_empenho_despesa as ped
         ON pe.exercicio        = ped.exercicio
        AND pe.cod_pre_empenho  = ped.cod_pre_empenho
        
  LEFT JOIN orcamento.despesa as ode 
         ON ped.exercicio       = ode.exercicio
        AND ped.cod_despesa     = ode.cod_despesa
        
        WHERE
            e.cod_entidade      IN (' || quote_literal( stCodEntidades ) || ') AND
            e.exercicio         =   ' || quote_literal( stExercicio    ) || '::varchar AND ';

        if (stDataInicial is not null and stDataInicial <> '') then
          stSql := stSql || ' e.dt_empenho >= to_date(' || quote_literal( stDataInicial ) || ',''dd/mm/yyyy'') AND ';
        end if;

        if (stDataFinal is not null and stDataFinal <> '') then
          stSql := stSql || ' e.dt_empenho <= to_date(' || quote_literal( stDataFinal )|| ', ''dd/mm/yyyy'') AND ';
        end if;

        if (stDataSituacao is not null and stDataSituacao <> '') then
           stSql := stSql || ' nl.dt_liquidacao <= to_date(' || quote_literal( stDataSituacao ) || ',''dd/mm/yyyy'') AND ';
        end if;

        if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <> '') then
           stSql := stSql || ' e.cod_empenho >= ' || inCodEmpenhoInicial || ' AND ';
        end if;

        if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <> '') then
           stSql := stSql || ' e.cod_empenho <= ' || quote_literal( inCodEmpenhoFinal ) || ' AND ';
        end if;

        if (inNumOrgao is not null and inNumOrgao <> '') then
            stSql := stSql || ' ode.num_orgao = ' || inNumOrgao || ' AND ';
        end if;

        stSql := stSql || '
            --Ligação EMPENHO : PRE_EMPENHO
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho AND

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            e.exercicio         = nl.exercicio_empenho AND
            e.cod_entidade      = nl.cod_entidade AND
            e.cod_empenho       = nl.cod_empenho AND

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
            nl.exercicio        = nli.exercicio AND
            nl.cod_nota         = nli.cod_nota AND
            nl.cod_entidade     = nli.cod_entidade
        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado_anulado AS (
        SELECT
            e.cod_entidade       as entidade,
            e.cod_empenho        as empenho,
            e.exercicio          as exercicio,
            sum(nlia.vl_anulado) as valor
        FROM
            empenho.empenho                 as e,
            empenho.nota_liquidacao         as nl,
            empenho.nota_liquidacao_item    as nli,
            empenho.nota_liquidacao_item_anulado nlia,
            empenho.pre_empenho             as pe
            
  LEFT JOIN empenho.pre_empenho_despesa as ped
         ON pe.exercicio        = ped.exercicio
        AND pe.cod_pre_empenho  = ped.cod_pre_empenho
        
  LEFT JOIN orcamento.despesa as ode
         ON ped.exercicio       = ode.exercicio
        AND ped.cod_despesa     = ode.cod_despesa
            
        WHERE
            e.cod_entidade      IN (' || quote_literal( stCodEntidades ) || ') AND
            e.exercicio         =   ' || quote_literal( stExercicio    ) || '::varchar AND ';

       if (stDataInicial is not null and stDataInicial <> '') then
          stSql := stSql || ' e.dt_empenho >= to_date(' || quote_literal( stDataInicial ) || ',''dd/mm/yyyy'') AND ';
       end if;

       if (stDataFinal is not null and stDataFinal<>'''') then
          stSql := stSql || ' e.dt_empenho <= to_date(' || quote_literal( stDataFinal ) || ',''dd/mm/yyyy'') AND ';
       end if;

        if (stDataSituacao is not null and stDataSituacao <> '') then
           stSql := stSql || ' to_date(to_char(nlia.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date(' || quote_literal( stDataSituacao ) || ',''dd/mm/yyyy'') AND';
        end if;

        if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <> '') then
           stSql := stSql || ' e.cod_empenho >= ' || quote_literal( inCodEmpenhoInicial ) || ' AND ';
        end if;

        if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <> '') then
           stSql := stSql || ' e.cod_empenho <= ' || inCodEmpenhoFinal || ' AND ';
        end if;

        if (inNumOrgao is not null and inNumOrgao <> '') then
            stSql := stSql || ' ode.num_orgao = ' || inNumOrgao || ' AND ';
        end if;

        stSql := stSql || '
            --Ligação EMPENHO : PRE_EMPENHO
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho AND

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            e.exercicio         = nl.exercicio_empenho AND
            e.cod_entidade      = nl.cod_entidade AND
            e.cod_empenho       = nl.cod_empenho AND

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
            nl.exercicio        = nli.exercicio AND
            nl.cod_nota         = nli.cod_nota AND
            nl.cod_entidade     = nli.cod_entidade AND

            --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
            nli.exercicio       = nlia.exercicio AND
            nli.cod_nota        = nlia.cod_nota AND
            nli.cod_entidade    = nlia.cod_entidade AND
            nli.num_item        = nlia.num_item AND
            nli.cod_pre_empenho = nlia.cod_pre_empenho AND
            nli.exercicio_item  = nlia.exercicio_item
        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
        SELECT
            e.cod_entidade      as entidade,
            e.cod_empenho       as empenho,
            e.exercicio         as exercicio,
            sum(nlp.vl_pago)    as valor
        FROM
            empenho.empenho                 as e,
            empenho.nota_liquidacao         as nl,
            empenho.nota_liquidacao_paga    as nlp,
            empenho.pre_empenho             as pe
            
  LEFT JOIN empenho.pre_empenho_despesa as ped
         ON pe.exercicio        = ped.exercicio
        AND pe.cod_pre_empenho  = ped.cod_pre_empenho
        
  LEFT JOIN orcamento.despesa as ode
         ON ped.exercicio       = ode.exercicio
        AND ped.cod_despesa     = ode.cod_despesa
        
        WHERE
            e.cod_entidade      IN (' || quote_literal( stCodEntidades ) || ') AND
            e.exercicio         =   ' || quote_literal( stExercicio    ) || '::varchar AND ';

       if (stDataInicial is not null and stDataInicial <> '') then
          stSql := stSql || ' e.dt_empenho >= to_date(' || quote_literal( stDataInicial ) || ',''dd/mm/yyyy'') AND ';
       end if;

       if (stDataFinal is not null and stDataFinal <> '') then
          stSql := stSql || ' e.dt_empenho <= to_date(' || quote_literal( stDataFinal ) || ',''dd/mm/yyyy'') AND';
       end if;

        if (stDataSituacao is not null and stDataSituacao <> '') then
           stSql := stSql || ' to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date('|| quote_literal( stDataSituacao ) || ',''dd/mm/yyyy'') AND ';
        end if;

        if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <> '') then
           stSql := stSql || ' e.cod_empenho >= ' || inCodEmpenhoInicial || ' AND ';
        end if;
        if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <> '') then
           stSql := stSql || ' e.cod_empenho <= ' || inCodEmpenhoFinal || ' AND ';
        end if;

        if (inNumOrgao is not null and inNumOrgao <> '' ) then
            stSql := stSql || ' ode.num_orgao = ' || inNumOrgao || ' AND ';
        end if;

        stSql := stSql || '
             --Ligação EMPENHO : PRE_EMPENHO
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho AND

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            e.exercicio         = nl.exercicio_empenho AND
            e.cod_entidade      = nl.cod_entidade AND
            e.cod_empenho       = nl.cod_empenho AND

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
            nl.exercicio        = nlp.exercicio AND
            nl.cod_nota         = nlp.cod_nota AND
            nl.cod_entidade     = nlp.cod_entidade
        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
        SELECT
            e.cod_entidade          as entidade,
            e.cod_empenho           as empenho,
            e.exercicio             as exercicio,
            sum(nlpa.vl_anulado)    as valor
        FROM
            empenho.empenho                         as e,
            empenho.nota_liquidacao                 as nl,
            empenho.nota_liquidacao_paga            as nlp,
            empenho.nota_liquidacao_paga_anulada    as nlpa,
            empenho.pre_empenho             as pe
            
  LEFT JOIN empenho.pre_empenho_despesa as ped
         ON pe.exercicio        = ped.exercicio
        AND pe.cod_pre_empenho  = ped.cod_pre_empenho
            
  LEFT JOIN orcamento.despesa as ode
         ON ped.exercicio       = ode.exercicio
        AND ped.cod_despesa     = ode.cod_despesa

        WHERE
            e.cod_entidade      IN (' || quote_literal( stCodEntidades ) || ') AND
            e.exercicio         =   ' || stExercicio || '::varchar AND ';

       if (stDataInicial is not null and stDataInicial <> '' ) then
          stSql := stSql || ' e.dt_empenho >= to_date(' || quote_literal( stDataInicial ) || ',''dd/mm/yyyy'') AND ';
       end if;

       if (stDataFinal is not null and stDataFinal <> '') then
          stSql := stSql || ' e.dt_empenho <= to_date(' || quote_literal( stDataFinal ) || ',''dd/mm/yyyy'') AND ';
       end if;

        if (stDataSituacao is not null and stDataSituacao <> '') then
           stSql := stSql || ' to_date(to_char(nlpa.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date(' || quote_literal( stDataSituacao ) || ',''dd/mm/yyyy'') AND ';
        end if;

        if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <> '') then
           stSql := stSql || ' e.cod_empenho >= ' || inCodEmpenhoInicial || ' AND ';
        end if;

        if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <> '') then
           stSql := stSql || ' e.cod_empenho <= ' || inCodEmpenhoFinal || ' AND ' ;
        end if;

        if (inNumOrgao is not null and inNumOrgao <> '' ) then
            stSql := stSql || ' ode.num_orgao = ' || inNumOrgao || ' AND ';
        end if;

        stSql := stSql || '
            --Ligação EMPENHO : PRE_EMPENHO
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho AND

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            e.exercicio             = nl.exercicio_empenho AND
            e.cod_entidade          = nl.cod_entidade AND
            e.cod_empenho           = nl.cod_empenho AND

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
            nl.exercicio            = nlp.exercicio AND
            nl.cod_nota             = nlp.cod_nota AND
            nl.cod_entidade         = nlp.cod_entidade AND

            --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
            nlp.exercicio           = nlpa.exercicio AND
            nlp.cod_nota            = nlpa.cod_nota AND
            nlp.cod_entidade        = nlpa.cod_entidade AND
            nlp.timestamp           = nlpa.timestamp
        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )';
        EXECUTE stSql;

stSql := '
SELECT * FROM (
    SELECT
        cod_entidade,
        cod_empenho,
        exercicio,
        dt_emissao,
        cgm,
        credor,
        (sum(empenhado) - sum(anulado))             as empenhado,
        (sum(liquidado) - sum(estornoliquidado))    as liquidado,
        (sum(pago) - sum(estornopago))              as pago,
        (sum(empenhado) - sum(anulado)) - (sum(pago) - sum(estornopago)) as apagar,
        (sum(liquidado) - sum(estornoliquidado)) - (sum(pago) - sum(estornopago)) as apagarliquidado,
        cod_recurso,
        nom_recurso,
        masc_recurso_red
    FROM (
        SELECT
            e.cod_entidade          as cod_entidade,
            e.cod_empenho           as cod_empenho,
            e.exercicio             as exercicio,
            to_char(e.dt_empenho, ''dd/mm/yyyy'') as dt_emissao,
            pe.cgm_beneficiario     as cgm,
            cgm.nom_cgm             as credor,
            recurso.cod_recurso,
            recurso.nom_recurso,
            recurso.masc_recurso_red,
            coalesce(empenho.fn_somatorio_razao_credor_empenhado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as empenhado,
            coalesce(empenho.fn_somatorio_razao_credor_anulado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as anulado,
            coalesce(empenho.fn_somatorio_razao_credor_liquidado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as liquidado,
            coalesce(empenho.fn_somatorio_razao_credor_liquidado_anulado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as estornoliquidado,
            coalesce(empenho.fn_somatorio_razao_credor_pago(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as pago,
            coalesce(empenho.fn_somatorio_razao_credor_estornado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as estornopago
        
        FROM empenho.empenho as e
            
  INNER JOIN empenho.pre_empenho_despesa
          ON pre_empenho_despesa.cod_pre_empenho = e.cod_pre_empenho
         AND pre_empenho_despesa.exercicio       = e.exercicio
                   
  INNER JOIN orcamento.despesa
          ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
         AND despesa.exercicio   = pre_empenho_despesa.exercicio
         
  INNER JOIN orcamento.recurso(' || quote_literal( stExercicio ) || ') as recurso
          ON recurso.cod_recurso = despesa.cod_recurso
         AND recurso.exercicio   = despesa.exercicio
         
  INNER JOIN empenho.pre_empenho as pe
          ON e.exercicio         = pe.exercicio
	 AND e.cod_pre_empenho   = pe.cod_pre_empenho

  INNER JOIN sw_cgm as cgm
	  ON pe.cgm_beneficiario = cgm.numcgm
            
   LEFT JOIN empenho.pre_empenho_despesa as ped
          ON pe.exercicio        = ped.exercicio
         AND pe.cod_pre_empenho  = ped.cod_pre_empenho
       
   LEFT JOIN orcamento.despesa as ode
          ON ped.exercicio     = ode.exercicio
         AND  ped.cod_despesa  = ode.cod_despesa

        WHERE   e.cod_entidade          IN (' || quote_literal( stCodEntidades ) || ')
                AND e.exercicio         =   ' || quote_literal( stExercicio ) || '::varchar ';

                if (stDataInicial is not null and stDataInicial<>'') then
                   stSql := stSql || ' AND e.dt_empenho >= to_date(' || quote_literal( stDataInicial ) || ',''dd/mm/yyyy'') ';
                end if;

                if (stDataFinal is not null and stDataFinal <> '') then
                   stSql := stSql || ' AND e.dt_empenho <= to_date(' || quote_literal( stDataFinal ) || ', ''dd/mm/yyyy'') ';
                end if;

                if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <> '') then
                   stSql := stSql || 'AND e.cod_empenho >= ' || inCodEmpenhoInicial || ' ';
                end if;

                if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <> '') then
                   stSql := stSql || 'AND e.cod_empenho <= ' || inCodEmpenhoFinal || ' ';
                end if;

                if (inNumOrgao is not null and inNumOrgao <> '') then
                    stSql := stSql || ' AND ode.num_orgao = ' || inNumOrgao || ' ';
                end if;

                if (inCGM is not null and inCGM <> '' ) then
                    stSql := stSql || ' AND pe.cgm_beneficiario     = ' || inCGM || ' ';
                end if;
                
                if (stCodRecurso is not null and stCodRecurso <> '') then
                    stSql := stSql || ' AND recurso.cod_recurso = '|| quote_literal( stCodRecurso ) || ' ';
                end if;
                
                if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') then
                    stSql := stSql || ' AND recurso.masc_recurso_red like '|| stDestinacaoRecurso || '%' || ' ';
                end if;
                
           stSql := stSql || '
            GROUP BY
                e.cod_entidade,
                e.cod_empenho,
                e.exercicio,
                e.dt_empenho,
                pe.cgm_beneficiario,
                cgm.nom_cgm,
                recurso.cod_recurso,
                recurso.nom_recurso,
                recurso.masc_recurso_red
            ORDER BY
                e.cod_entidade,
                e.cod_empenho,
                e.exercicio,
                e.dt_empenho,
                cgm.nom_cgm,
                recurso.cod_recurso,
                recurso.nom_recurso,
                recurso.masc_recurso_red
        ) as tbl
        GROUP BY
            cod_entidade,
            cod_empenho,
            exercicio,
            dt_emissao,
            cgm,
            credor,
            cod_recurso,
            nom_recurso,
            masc_recurso_red
        ) as tmp
        
        WHERE apagar > 0 ';

        stSql := stSql || '
        ORDER BY ';

        if (inOrdenacao is not null and inOrdenacao <> '') then
            if(inOrdenacao = 1) then
                stSql := stSql || '
                    cod_empenho,
                    exercicio,
                    cod_entidade,
                    dt_emissao,
                    credor
                ';
            end if;
            if(inOrdenacao = 2) then
                stSql := stSql || '
                    credor,
                    cod_empenho,
                    exercicio,
                    cod_entidade,
                    dt_emissao
                ';
            end if;
        else
            stSql := stSql || '
                cod_empenho,
                exercicio,
                cod_entidade,
                dt_emissao,
                credor
            ';
        end if;

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_liquidado;
    DROP TABLE tmp_liquidado_anulado;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_estornado;

    RETURN;

END;

$$ language 'plpgsql';