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
* Casos de uso: uc-02.01.11
*/

/*
$Log$
Revision 1.5  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_despesa_unidade_balanco(varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS
$$
DECLARE
    stExercicio             ALIAS FOR $1;
    stFiltro                ALIAS FOR $2;
    stDataInicial           ALIAS FOR $3;
    stDataFinal             ALIAS FOR $4;
    stCodEntidades          ALIAS FOR $5;
    stSituacao              ALIAS FOR $6;
    inNumOrgao              ALIAS FOR $7;
    inNumUnidade            ALIAS FOR $8;
    stSql                   VARCHAR   := '';
    nuSoma                  NUMERIC(14,2);
    reRegistro              RECORD;
    reRegistro2             RECORD;

BEGIN
  IF ( stSituacao = 'empenhados' ) THEN
    stSql := 'CREATE TEMPORARY TABLE tmp_empenhado AS (
       SELECT
            e.dt_empenho as dataConsulta,
            coalesce(ipe.vl_total,0.00) as valor,
            cd.cod_estrutural as cod_estrutural,
            d.num_orgao as num_orgao,
            d.num_unidade as num_unidade
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

            And e.cod_entidade             IN (' || stCodEntidades || ')
            And e.exercicio                = ' || quote_literal(stExercicio) || '

            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho

            AND pe.exercicio               = ipe.exercicio
            AND pe.cod_pre_empenho         = ipe.cod_pre_empenho
            ';

            if(inNumOrgao is not null and inNumOrgao <> '') then
                stSql := stSql || ' AND d.num_orgao = ' || inNumOrgao ||' ';
            end if;
            if (inNumUnidade is not null and inNumUnidade <> '') then
                stSql := stSql || ' AND d.num_unidade = ' || inNumUnidade;
            end if;

          stSql := stSql || ')';

        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_anulado AS (
            SELECT to_date(to_char(EEAI.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta
                    , EEAI.vl_anulado as valor
                    , OCD.cod_estrutural as cod_estrutural
                    , OD.num_orgao
                    , OD.num_unidade
               from orcamento.despesa           as OD,
                    orcamento.conta_despesa     as OCD,
                    empenho.pre_empenho_despesa as EPED,
                    empenho.pre_empenho         as EPE,
                    empenho.item_pre_empenho    as EIPE,
                    empenho.empenho_anulado_item as EEAI

               Where
                     OCD.cod_conta            = EPED.cod_conta
                 AND OCD.exercicio            = EPED.exercicio
                 And EPED.exercicio           = EPE.exercicio
                 And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
                 And EPE.exercicio            = EIPE.exercicio
                 And EPE.cod_pre_empenho      = EIPE.cod_pre_empenho
                 And EIPE.exercicio           = EEAI.exercicio
                 And EIPE.cod_pre_empenho     = EEAI.cod_pre_empenho
                 And EIPE.num_item            = EEAI.num_item
                 And EEAI.exercicio           ='|| quote_literal(stExercicio) ||'
                 And EEAI.cod_entidade        IN ('|| stCodEntidades ||')
                 And OD.cod_despesa           = EPED.cod_despesa
                 AND OD.exercicio             = EPED.exercicio
                 ';
                
                if(inNumOrgao is not null and inNumOrgao <> '') then
                    stSql := stSql || ' AND OD.num_orgao = ' || inNumOrgao ||' ';
                end if;
                if (inNumUnidade is not null and inNumUnidade <> '') then
                    stSql := stSql || ' AND OD.num_unidade = ' || inNumUnidade;
                end if;

              stSql := stSql || ')';
              
       EXECUTE stSql;
  END IF;
  
  IF ( stSituacao = 'pagos' ) THEN
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

            And EE.exercicio                ='|| quote_literal(stExercicio) ||'
            And EE.cod_entidade          IN ('|| stCodEntidades ||')

            And EE.cod_empenho           = ENL.cod_empenho
            And EE.exercicio             = ENL.exercicio_empenho
            And EE.cod_entidade          = ENL.cod_entidade

            And ENL.cod_nota             = ENLP.cod_nota
            And ENL.cod_entidade         = ENLP.cod_entidade
            And ENL.exercicio            = ENLP.exercicio
            ';

            if(inNumOrgao is not null and inNumOrgao <> '') then
                stSql := stSql || ' AND OD.num_orgao = ' || inNumOrgao ||' ';
            end if;
            if (inNumUnidade is not null and inNumUnidade <> '') then
                stSql := stSql || ' AND OD.num_unidade = ' || inNumUnidade;
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
            AND OD.exercicio             = EPED.exercicio
            ';

            stSql := stSql || '
            And EPED.exercicio           = EPE.exercicio
            And EPED.cod_pre_empenho     = EPE.cod_pre_empenho

            And EPE.exercicio            = EE.exercicio
            And EPE.cod_pre_empenho      = EE.cod_pre_empenho

            And EE.cod_entidade          IN ('||stCodEntidades||')
            And EE.exercicio             = '|| quote_literal(stExercicio) ||'

            And EE.cod_empenho           = ENL.cod_empenho
            And EE.exercicio             = ENL.exercicio_empenho
            And EE.cod_entidade          = ENL.cod_entidade

            And ENL.exercicio            = ENLP.exercicio
            And ENL.cod_nota             = ENLP.cod_nota
            And ENL.cod_entidade         = ENLP.cod_entidade

            And ENLP.cod_entidade        = ENLPA.cod_entidade
            And ENLP.cod_nota            = ENLPA.cod_nota
            And ENLP.exercicio           = ENLPA.exercicio
            And ENLP.timestamp           = ENLPA.timestamp
            ';
            
            if(inNumOrgao is not null and inNumOrgao <> '') then
                stSql := stSql || ' AND OD.num_orgao = ' || inNumOrgao ||' ';
            end if;
            if (inNumUnidade is not null and inNumUnidade <> '') then
                stSql := stSql || ' AND OD.num_unidade = ' || inNumUnidade;
            end if;
        stSql := stSql || ')';

        EXECUTE stSql;
  END IF;
  
  IF ( stSituacao = 'liquidados' ) THEN
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

                    And e.cod_entidade             IN (' || stCodEntidades || ')
                    And e.exercicio                = ' || quote_literal(stExercicio) || '

                    AND e.exercicio                = pe.exercicio
                    AND e.cod_pre_empenho          = pe.cod_pre_empenho

                    AND e.exercicio = nl.exercicio_empenho
                    AND e.cod_entidade = nl.cod_entidade
                    AND e.cod_empenho = nl.cod_empenho

                    AND nl.exercicio = nli.exercicio
                    AND nl.cod_nota = nli.cod_nota
                    AND nl.cod_entidade = nli.cod_entidade
                    ';
                    
                    if(inNumOrgao is not null and inNumOrgao <> '') then
                        stSql := stSql || ' AND d.num_orgao = ' || inNumOrgao ||' ';
                    end if;
                    if (inNumUnidade is not null and inNumUnidade <> '') then
                        stSql := stSql || ' AND d.num_unidade = ' || inNumUnidade;
                    end if;

        stSql := stSql || ')';

        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado_estornado AS (
        SELECT
            to_date(to_char(ENLIA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta
            , ENLIA.vl_anulado as valor
            , OCD.cod_estrutural as cod_estrutural
            , OD.num_orgao
            , OD.num_unidade
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
          And EE.cod_entidade             IN ('||stCodEntidades||')
          And EE.exercicio                = '|| quote_literal(stExercicio) || '

          And ENL.exercicio               = ENLI.exercicio
          And ENL.cod_nota                = ENLI.cod_nota
          And ENL.cod_entidade            = ENLI.cod_entidade
          ';

          stSql := stSql || '
          And ENLI.exercicio           = ENLIA.exercicio
          And ENLI.cod_pre_empenho     = ENLIA.cod_pre_empenho
          And ENLI.num_item            = ENLIA.num_item
          And ENLI.cod_entidade        = ENLIA.cod_entidade
          And ENLI.exercicio_item      = ENLIA.exercicio_item
          And ENLI.cod_nota            = ENLIA.cod_nota
          And OD.cod_despesa           = EPED.cod_despesa
          AND OD.exercicio             = EPED.exercicio
          And OD.cod_entidade          IN ('||stCodEntidades||')
          And EPED.exercicio           = EPE.exercicio
          And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
          ';

         if(inNumOrgao is not null and inNumOrgao <> '') then
             stSql := stSql || ' AND OD.num_orgao = ' || inNumOrgao ||' ';
         end if;
         if (inNumUnidade is not null and inNumUnidade <> '') then
             stSql := stSql || 's AND OD.num_unidade = ' || inNumUnidade;
         end if;

        stSql := stSql || ')';

        EXECUTE stSql;
  END IF;


stSql:= 'CREATE TEMPORARY TABLE tmp_class_despesa as
            SELECT
                orcamento.fn_consulta_class_despesa(cod_conta
                                                    , exercicio
                                                    , ((    SELECT administracao.configuracao.valor
                                                    FROM administracao.configuracao
                                                    WHERE administracao.configuracao.cod_modulo = 8
                                                    AND administracao.configuracao.parametro = ''masc_class_despesa''
                                                    AND administracao.configuracao.exercicio ='|| quote_literal(stExercicio) ||'))
                ) as classificacao
                ,publico.fn_mascarareduzida( orcamento.fn_consulta_class_despesa(cod_conta
                                                                                , exercicio
                                                                                , ((    SELECT administracao.configuracao.valor
                                                                                FROM administracao.configuracao
                                                                                WHERE administracao.configuracao.cod_modulo = 8
                                                                                AND administracao.configuracao.parametro = ''masc_class_despesa''
                                                                                AND administracao.configuracao.exercicio ='|| quote_literal(stExercicio) ||'))
                                                                                )
                ) as classificacao_reduzida
                ,publico.fn_nivel( orcamento.fn_consulta_class_despesa(cod_conta
                                                                        , exercicio
                                                                        , ((    SELECT administracao.configuracao.valor
                                                                        FROM administracao.configuracao
                                                                        WHERE administracao.configuracao.cod_modulo = 8
                                                                        AND administracao.configuracao.parametro = ''masc_class_despesa''
                                                                        AND administracao.configuracao.exercicio ='|| quote_literal(stExercicio) ||'))
                                                                        )
                ) as nivel
                ,cod_conta
                ,exercicio
                ,descricao
            FROM    orcamento.conta_despesa
            WHERE   exercicio ='|| quote_literal(stExercicio) ||'
            ORDER BY classificacao
            ';
EXECUTE stSql;

CREATE TEMPORARY TABLE tmp_relatorio(
     classificacao          VARCHAR(100)
    ,classificacao_reduzida VARCHAR(100)
    ,nivel                  INTEGER
    ,cod_conta              INTEGER
    ,num_orgao              INTEGER
    ,num_unidade            INTEGER
    ,exercicio              VARCHAR(4)
    ,descricao              VARCHAR(100)
    ,vl_original            NUMERIC(14,2)
);

stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS
                SELECT
                     cod_conta
                     ,num_orgao
                     ,num_unidade
                     ,vl_original
                    ,orcamento.fn_consulta_class_despesa(cod_conta
                                                        , exercicio
                                                        , ((    SELECT administracao.configuracao.valor
                                                        FROM administracao.configuracao
                                                        WHERE administracao.configuracao.cod_modulo = 8
                                                        AND administracao.configuracao.parametro = ''masc_class_despesa''
                                                        AND administracao.configuracao.exercicio = ' || quote_literal(stExercicio) || '))
                    ) as classificacao
                FROM    orcamento.despesa
                WHERE   exercicio = ' || quote_literal(stExercicio) || '
                ';

                if(inNumOrgao is not null and inNumOrgao <> '') then
                  stSql := stSql || ' AND num_orgao = ' || inNumOrgao ||' ';
                end if;
                if (inNumUnidade is not null and inNumUnidade <> '') then
                    stSql := stSql || ' AND num_unidade = ' || inNumUnidade;
                end if;

                stSql := stSql || stFiltro ;

        EXECUTE stSql;


        FOR reRegistro IN
            EXECUTE 'SELECT   DISTINCT on (num_orgao,num_unidade) *
                        FROM     tmp_despesa
                        ORDER BY num_orgao'
        LOOP
            FOR reRegistro2 IN
                EXECUTE 'SELECT   *
                            FROM     tmp_class_despesa'
            LOOP
                nuSoma := orcamento.fn_totaliza_despesa_unidade(reRegistro2.classificacao_reduzida,reRegistro.num_orgao,reRegistro.num_unidade);
                IF nuSoma <> 0.00 THEN
                    nuSoma := coalesce(nuSoma,0);
                    INSERT INTO tmp_relatorio (num_orgao, num_unidade, cod_conta, classificacao, classificacao_reduzida, nivel, exercicio, descricao, vl_original)
                    VALUES (reRegistro.num_orgao, reRegistro.num_unidade, reRegistro2.cod_conta, reRegistro2.classificacao, reRegistro2.classificacao_reduzida, reRegistro2.nivel, reRegistro2.exercicio, reRegistro2.descricao, nuSoma);
                END IF;
            END LOOP;
        END LOOP;


stSql := ' SELECT   cod_conta
                ,nivel
                ,descricao
                ,classificacao
                ,classificacao_reduzida
                ,num_orgao
                ,num_unidade
        ';
        
        IF ( stSituacao = 'empenhados' ) THEN
           stSql := stSql || ',(coalesce(orcamento.fn_consolidado_empenhado(' || quote_literal(stDataInicial) || '
                                                                            , ' || quote_literal(stDataFinal) || '
                                                                            , publico.fn_mascarareduzida(classificacao),num_orgao,num_unidade),0.00)
                                                                            -
                                                                            coalesce(orcamento.fn_consolidado_anulado(' || quote_literal(stDataInicial) || '
                                                                                                                      , ' || quote_literal(stDataFinal) || '
                                                                                                                      , publico.fn_mascarareduzida(classificacao),num_orgao,num_unidade),0.00)
                                ) as valor ';
        END IF;
        IF ( stSituacao = 'pagos' ) THEN
            stSql := stSql || ',(coalesce(orcamento.fn_consolidado_pago(' || quote_literal(stDataInicial) || '
                                                                        , ' || quote_literal(stDataFinal) || '
                                                                        , publico.fn_mascarareduzida(classificacao),num_orgao,num_unidade),0.00)
                                                                        -
                                                                        coalesce(orcamento.fn_consolidado_estornado(' || quote_literal(stDataInicial) || '
                                                                                                                    , ' || quote_literal(stDataFinal) || '
                                                                                                                    , publico.fn_mascarareduzida(classificacao),num_orgao,num_unidade),0.00)
                                ) as valor ';
        END IF;
        IF ( stSituacao = 'liquidados' )  THEN
            stSql := stSql || ',(coalesce(orcamento.fn_consolidado_liquidado(' || quote_literal(stDataInicial) || '
                                                                            , ' || quote_literal(stDataFinal) || '
                                                                            , publico.fn_mascarareduzida(classificacao),num_orgao,num_unidade),0.00)
                                                                            -
                                                                            coalesce(orcamento.fn_consolidado_liquidado_estornado(' || quote_literal(stDataInicial) || '
                                                                                                                                 , ' || quote_literal(stDataFinal) || '
                                                                                                                                 , publico.fn_mascarareduzida(classificacao),num_orgao,num_unidade),0.00)
                                ) as valor ';
        END IF;
        stSql := stSql || ' FROM
                 tmp_relatorio
        ORDER BY num_orgao, num_unidade, classificacao ';


    FOR reRegistro IN
       EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    IF ( stSituacao = 'empenhados' ) THEN
        DROP TABLE tmp_empenhado;
        DROP TABLE tmp_anulado;
    END IF;
    IF ( stSituacao = 'pagos' ) THEN
        DROP TABLE tmp_pago;
        DROP TABLE tmp_estornado;
    END IF;
    if ( stSituacao = 'liquidados' ) THEN
        DROP TABLE tmp_liquidado;
        DROP TABLE tmp_liquidado_estornado;
    END IF;

    DROP TABLE tmp_class_despesa;
    DROP TABLE tmp_despesa;
    DROP TABLE tmp_relatorio;
    
    RETURN;
END;
$$ LANGUAGE 'plpgsql';
