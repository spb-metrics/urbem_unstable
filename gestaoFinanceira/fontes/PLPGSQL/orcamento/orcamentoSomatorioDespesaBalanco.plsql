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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.01.09
*/

/*
$Log$
Revision 1.9  2006/07/05 20:38:04  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_orcamento_somatorio_despesa_balanco(varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stFiltro                ALIAS FOR $2;
    stCodEntidade           ALIAS FOR $3;
    stNumOrgao              ALIAS FOR $4;
    stNumUnidade            ALIAS FOR $5;
    stDataInicial           ALIAS FOR $6;
    stDataFinal             ALIAS FOR $7;
    inCodDemDespesa         ALIAS FOR $8;
    stSql                   VARCHAR   := '';
    stMascara               VARCHAR   := '';
    reRegistro              RECORD;

BEGIN

SELECT
    valor
INTO
    stMascara
FROM
    administracao.configuracao
WHERE
    cod_modulo  = 8                         AND
    parametro   = 'masc_class_despesa'    AND
    exercicio   = stExercicio;

IF ( inCodDemDespesa::integer = 1 ) THEN
    stSql := 'CREATE TEMPORARY TABLE tmp_empenhado AS (
        SELECT
            e.dt_empenho                as dataConsulta,
            coalesce(ipe.vl_total,0.00) as valor,
            cd.cod_estrutural           as cod_estrutural,
            d.num_orgao                 as num_orgao,
            d.num_unidade               as num_unidade
        FROM
            orcamento.despesa           as d,
            orcamento.conta_despesa     as cd,
            empenho.pre_empenho_despesa as ped,
            empenho.empenho             as e,
            empenho.pre_empenho         as pe,
            empenho.item_pre_empenho    as ipe
        WHERE
                cd.cod_conta               = ped.cod_conta
            AND cd.exercicio               = ped.exercicio

            And d.cod_despesa              = ped.cod_despesa
            AND d.exercicio                = ped.exercicio

            And pe.exercicio               = ped.exercicio
            And pe.cod_pre_empenho         = ped.cod_pre_empenho

            And e.cod_entidade             IN (' || stCodEntidade || ')
            And e.exercicio                = ''' || stExercicio || '''

            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho

            AND pe.exercicio               = ipe.exercicio
            AND pe.cod_pre_empenho         = ipe.cod_pre_empenho';

            if (stNumOrgao is not null and stNumOrgao <> '') then
                stSql := stSql || ' AND d.num_orgao = ' || stNumOrgao || '';
            end if;

            if (stNumUnidade is not null and stNumUnidade <> '') then
                stSql := stSql || ' AND d.num_unidade = ' || stNumUnidade || '';
            end if;


        stSql := stSql || '
      )';

      EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_anulado AS (
        SELECT
            to_date(to_char(EEAI.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta, EEAI.vl_anulado as valor, OCD.cod_estrutural as cod_estrutural, OD.num_orgao, OD.num_unidade
        FROM
            orcamento.despesa           as OD,
            orcamento.conta_despesa     as OCD,
            empenho.pre_empenho_despesa as EPED,
            empenho.pre_empenho         as EPE,
            empenho.item_pre_empenho    as EIPE,
            empenho.empenho_anulado_item as EEAI
        WHERE
                OCD.cod_conta            = EPED.cod_conta
            AND OCD.exercicio            = EPED.exercicio
            And EPED.exercicio           = EPE.exercicio
            And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
            And EPE.exercicio            = EIPE.exercicio
            And EPE.cod_pre_empenho      = EIPE.cod_pre_empenho
            And EIPE.exercicio           = EEAI.exercicio
            And EIPE.cod_pre_empenho     = EEAI.cod_pre_empenho
            And EIPE.num_item            = EEAI.num_item
            And EEAI.exercicio           ='''|| stExercicio ||'''
            And EEAI.cod_entidade        IN ('||stCodEntidade||')
            And OD.cod_despesa           = EPED.cod_despesa
            AND OD.exercicio             = EPED.exercicio';

        if (stNumOrgao is not null and stNumOrgao <> '') then
            stSql := stSql || ' AND OD.num_orgao = ' || stNumOrgao || '';
        end if;

        if (stNumUnidade is not null and stNumUnidade <> '') then
            stSql := stSql || ' AND OD.num_unidade = ' || stNumUnidade || '';
        end if;

        stSql := stSql || ')';
      EXECUTE stSql;
END IF;

IF ( inCodDemDespesa::integer = 3 ) THEN
    stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
        SELECT
            to_date(to_char(ENLP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLP.vl_pago as valor,
            OCD.cod_estrutural as cod_estrutural,
            OD.num_orgao as num_orgao,
            OD.num_unidade as num_unidade
        FROM
            orcamento.despesa               as OD,
            orcamento.conta_despesa         as OCD,
            empenho.pre_empenho_despesa     as EPED,
            empenho.empenho                 as EE,
            empenho.pre_empenho             as EPE,
            empenho.nota_liquidacao         as ENL,
            empenho.nota_liquidacao_paga    as ENLP

        WHERE
                OCD.cod_conta            = EPED.cod_conta
            AND OCD.exercicio            = EPED.exercicio

            AND OD.cod_despesa           = EPED.cod_despesa
            AND OD.exercicio             = EPED.exercicio

            And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
            And EPED.exercicio           = EPE.exercicio

            And EPE.exercicio            = EE.exercicio
            And EPE.cod_pre_empenho      = EE.cod_pre_empenho

            And EE.exercicio                ='''|| stExercicio ||'''
            And EE.cod_entidade          IN ('||stCodEntidade ||')

            And EE.cod_empenho           = ENL.cod_empenho
            And EE.exercicio             = ENL.exercicio_empenho
            And EE.cod_entidade          = ENL.cod_entidade

            And ENL.cod_nota             = ENLP.cod_nota
            And ENL.cod_entidade         = ENLP.cod_entidade
            And ENL.exercicio            = ENLP.exercicio ';

        if (stNumOrgao is not null and stNumOrgao <> '') then
            stSql := stSql || ' AND OD.num_orgao = ' || stNumOrgao || '';
        end if;

        if (stNumUnidade is not null and stNumUnidade <> '') then
            stSql := stSql || ' AND OD.num_unidade = ' || stNumUnidade || '';
        end if;

        stSql := stSql || ')';
        EXECUTE stSql;

  stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
      SELECT
          to_date(to_char(ENLPA.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
          ENLPA.vl_anulado as valor,
          OCD.cod_estrutural as cod_estrutural,
          OD.num_orgao as num_orgao,
          OD.num_unidade as num_unidade
      FROM
          orcamento.despesa                    as OD,
          orcamento.conta_despesa              as OCD,
          empenho.pre_empenho_despesa          as EPED,
          empenho.empenho                      as EE,
          empenho.pre_empenho                  as EPE,
          empenho.nota_liquidacao              as ENL,
          empenho.nota_liquidacao_paga         as ENLP,
          empenho.nota_liquidacao_paga_anulada as ENLPA
      WHERE
              OCD.cod_conta            = EPED.cod_conta
          AND OCD.exercicio            = EPED.exercicio
          And OD.cod_despesa           = EPED.cod_despesa
          AND OD.exercicio             = EPED.exercicio';

          stSql := stSql || '
          And EPED.exercicio           = EPE.exercicio
          And EPED.cod_pre_empenho     = EPE.cod_pre_empenho

          And EPE.exercicio            = EE.exercicio
          And EPE.cod_pre_empenho      = EE.cod_pre_empenho

          And EE.cod_entidade          IN ('||stCodEntidade ||')
          And EE.exercicio             = '''|| stExercicio ||'''

          And EE.cod_empenho           = ENL.cod_empenho
          And EE.exercicio             = ENL.exercicio_empenho
          And EE.cod_entidade          = ENL.cod_entidade

          And ENL.exercicio            = ENLP.exercicio
          And ENL.cod_nota             = ENLP.cod_nota
          And ENL.cod_entidade         = ENLP.cod_entidade

          And ENLP.cod_entidade        = ENLPA.cod_entidade
          And ENLP.cod_nota            = ENLPA.cod_nota
          And ENLP.exercicio           = ENLPA.exercicio
          And ENLP.timestamp           = ENLPA.timestamp ';

        if (stNumOrgao is not null and stNumOrgao <> '') then
            stSql := stSql || ' AND OD.num_orgao = ' || stNumOrgao || '';
        end if;

        if (stNumUnidade is not null and stNumUnidade <> '') then
            stSql := stSql || ' AND OD.num_unidade = ' || stNumUnidade || '';
        end if;


      stSql := stSql || ')';
      EXECUTE stSql;
END IF;

IF ( inCodDemDespesa::integer = 2 ) THEN
    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado AS (
        SELECT
            nl.dt_liquidacao as dataConsulta,
            nli.vl_total as valor,
            cd.cod_estrutural as cod_estrutural,
            d.num_orgao as num_orgao,
            d.num_unidade as num_unidade
        FROM
            orcamento.despesa             as d,
            orcamento.conta_despesa       as cd,
            empenho.pre_empenho_despesa   as ped,
            empenho.pre_empenho           as pe,
            empenho.empenho               as e,
            empenho.nota_liquidacao_item  as nli,
            empenho.nota_liquidacao       as nl
        WHERE
                cd.cod_conta               = ped.cod_conta
            AND cd.exercicio               = ped.exercicio

            And d.cod_despesa              = ped.cod_despesa
            AND d.exercicio                = ped.exercicio

            And pe.exercicio               = ped.exercicio
            And pe.cod_pre_empenho         = ped.cod_pre_empenho

            And e.cod_entidade             IN (' || stCodEntidade || ')
            And e.exercicio                = ''' || stExercicio || '''

            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho

            AND e.exercicio = nl.exercicio_empenho
            AND e.cod_entidade = nl.cod_entidade
            AND e.cod_empenho = nl.cod_empenho

            AND nl.exercicio = nli.exercicio
            AND nl.cod_nota = nli.cod_nota
            AND nl.cod_entidade = nli.cod_entidade';

        if (stNumOrgao is not null and stNumOrgao <> '') then
            stSql := stSql || ' AND OD.num_orgao = ' || stNumOrgao || '';
        end if;

        if (stNumUnidade is not null and stNumUnidade <> '') then
            stSql := stSql || ' AND OD.num_unidade = ' || stNumUnidade || '';
        end if;


      stSql := stSql || ')';



      EXECUTE stSql;

  stSql := 'CREATE TEMPORARY TABLE tmp_liquidado_estornado AS (
      SELECT
          to_date(to_char(ENLIA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta, ENLIA.vl_anulado as valor, OCD.cod_estrutural as cod_estrutural, OD.num_orgao, OD.num_unidade
      from orcamento.despesa                    as OD,
           orcamento.conta_despesa              as OCD,
           empenho.pre_empenho_despesa          as EPED,
           empenho.pre_empenho                  as EPE,
           empenho.empenho                      as EE,
           empenho.nota_liquidacao              as ENL,
           empenho.nota_liquidacao_item         as ENLI,
           empenho.nota_liquidacao_item_anulado as ENLIA

      Where OCD.cod_conta               = EPED.cod_conta
        AND OCD.exercicio               = EPED.exercicio
        And EPE.cod_pre_empenho         = EE.cod_pre_empenho
        And EPE.exercicio               = EE.exercicio

        And EE.exercicio                = ENL.exercicio_empenho
        And EE.cod_entidade             = ENL.cod_entidade
        And EE.cod_empenho              = ENL.cod_empenho
        And EE.cod_entidade             IN ('||stCodEntidade ||')
        And EE.exercicio                = '''|| stExercicio || '''

        And ENL.exercicio               = ENLI.exercicio
        And ENL.cod_nota                = ENLI.cod_nota
        And ENL.cod_entidade            = ENLI.cod_entidade';

        stSql := stSql || '
        And ENLI.exercicio           = ENLIA.exercicio
        And ENLI.cod_pre_empenho     = ENLIA.cod_pre_empenho
        And ENLI.num_item            = ENLIA.num_item
        And ENLI.cod_entidade        = ENLIA.cod_entidade
        And ENLI.exercicio_item      = ENLIA.exercicio_item
        And ENLI.cod_nota            = ENLIA.cod_nota
        And OD.cod_despesa           = EPED.cod_despesa
        AND OD.exercicio             = EPED.exercicio
        And OD.cod_entidade          IN ('||stCodEntidade||')
        And EPED.exercicio           = EPE.exercicio
        And EPED.cod_pre_empenho     = EPE.cod_pre_empenho ';

        if (stNumOrgao is not null and stNumOrgao <> '') then
            stSql := stSql || ' AND OD.num_orgao = ' || stNumOrgao || '';
        end if;

        if (stNumUnidade is not null and stNumUnidade <> '') then
            stSql := stSql || ' AND OD.num_unidade = ' || stNumUnidade || '';
        end if;

      stSql := stSql || ')';
      EXECUTE stSql;
END IF;

    stSql := '
    SELECT
        d.cod_conta,
        publico.fn_nivel( orcamento.fn_consulta_class_despesa(d.cod_conta, d.exercicio, ''' || stMascara || ''')) as nivel,
        d.descricao,
        d.cod_estrutural as classificacao,
        publico.fn_mascarareduzida(d.cod_estrutural) as classificacao_reduzida';

        IF ( inCodDemDespesa::integer = 1 ) THEN
            stSql := stSql || ',(coalesce(orcamento.fn_consolidado_empenhado(''' || stDataInicial || ''', ''' || stDataFinal || ''', publico.fn_mascarareduzida(d.cod_estrutural),0,0),0.00) - coalesce(orcamento.fn_consolidado_anulado(''' || stDataInicial || ''', ''' || stDataFinal || ''', publico.fn_mascarareduzida(d.cod_estrutural),0,0),0.00)) as valor ';
        ELSE
            IF ( inCodDemDespesa::integer = 3 ) THEN
                stSql := stSql || ',(coalesce(orcamento.fn_consolidado_pago(''' || stDataInicial || ''', ''' || stDataFinal || ''', publico.fn_mascarareduzida(d.cod_estrutural),0,0),0.00) - coalesce(orcamento.fn_consolidado_estornado(''' || stDataInicial || ''', ''' || stDataFinal || ''', publico.fn_mascarareduzida(d.cod_estrutural),0,0),0.00)) as valor ';
            ELSE
                IF ( inCodDemDespesa::integer = 2 )  THEN
                    stSql := stSql || ',(coalesce(orcamento.fn_consolidado_liquidado(''' || stDataInicial || ''', ''' || stDataFinal || ''', publico.fn_mascarareduzida(d.cod_estrutural),0,0),0.00) - coalesce(orcamento.fn_consolidado_liquidado_estornado(''' || stDataInicial || ''', ''' || stDataFinal || ''', publico.fn_mascarareduzida(d.cod_estrutural),0,0),0.00)) as valor ';
                ELSE
                    stSql := stSql || ', 0.00 as valor';
                END IF;
            END IF;
        END IF;

    stSql := stSql || '
    FROM
        orcamento.conta_despesa as d
    WHERE d.exercicio = '''||stExercicio||'''
    GROUP BY
        d.exercicio,
        d.cod_conta,
        d.cod_estrutural,
        d.descricao
    ORDER BY
        d.exercicio,
        d.cod_conta,
        d.cod_estrutural,
        d.descricao
    ';



    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    IF ( inCodDemDespesa::integer = 1 ) THEN
        DROP TABLE tmp_empenhado;
        DROP TABLE tmp_anulado;
    END IF;
    IF ( inCodDemDespesa::integer = 3 ) THEN
        DROP TABLE tmp_pago;
        DROP TABLE tmp_estornado;
    END IF;
    if ( inCodDemDespesa::integer = 2 ) THEN
        DROP TABLE tmp_liquidado;
        DROP TABLE tmp_liquidado_estornado;
    END IF;

    RETURN;
END;
$$ language 'plpgsql';
