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
*
    $Id: listaArrecadacaoTCE.plsql 64692 2016-03-22 13:36:45Z michel $
*
* Casos de uso: uc-02.04.07,uc-02.04.19,uc-02.04.04,uc-02.04.10
*/

CREATE OR REPLACE FUNCTION tesouraria.fn_listar_arrecadacao_tce(varchar,varchar) RETURNS BOOLEAN AS '
DECLARE
    stFiltroCarne       ALIAS FOR $1;
    stFiltroReceita     ALIAS FOR $2;

    nuVlMulta           NUMERIC;
    nuVlJuros           NUMERIC;
    nuVlMultaTotal      NUMERIC;
    nuVlJurosTotal      NUMERIC;

    stSql               VARCHAR   := '''';
    stSqlFuncao         VARCHAR   := '''';
    reRegistro          RECORD;
    crCursor            REFCURSOR;

BEGIN

    stSql := ''
        DROP TABLE IF EXISTS tmp_deducao;

        CREATE TEMPORARY TABLE tmp_deducao AS (
            SELECT TB.cod_boletim
                  ,TO_CHAR( TB.dt_boletim, ''''dd/mm/yyyy'''' ) AS dt_boletim
                  ,TO_CHAR( TA.timestamp_arrecadacao, ''''HH24:mi:ss'''' ) as hora
                  ,TA.cod_entidade
                  ,TA.cod_arrecadacao
                  ,ARD.cod_receita_dedutora
                  ,ARD.cod_receita
                  ,0 as cod_calculo
                  ,TA.exercicio
                  ,CAST ('''''''' as VARCHAR ) AS numeracao
                  ,TA.timestamp_arrecadacao AS timestamp
                  ,ARD.vl_deducao AS valor    
                  ,CAST( 0.00 AS NUMERIC ) as vl_desconto
                  ,CAST( 0.00 AS NUMERIC ) as vl_multa
                  ,CAST( 0.00 AS NUMERIC ) as vl_juros
                  ,0 AS conta_debito
                  ,TA.cod_plano AS conta_credito
                  ,0 as porcentagem_valor
                  ,TA.cgm_usuario
                  ,OCR.descricao AS conta_receita
            FROM tesouraria.boletim             AS TB
                ,tesouraria.arrecadacao         AS TA
                ,tesouraria.arrecadacao_receita AS TAR
                ,tesouraria.arrecadacao_receita_dedutora AS ARD
                ,orcamento.receita              AS ORE
                ,orcamento.conta_receita        AS OCR
              -- Join com arrecadacao
            WHERE TB.exercicio   = TA.exercicio
              AND TB.cod_boletim = TA.cod_boletim
              AND TB.cod_entidade= TA.cod_entidade
              -- Join com arrecadacao_receita 
              AND TA.exercicio              = TAR.exercicio
              AND TA.cod_arrecadacao        = TAR.cod_arrecadacao
              AND TA.timestamp_arrecadacao  = TAR.timestamp_arrecadacao
              AND TA.devolucao              = false
              -- Join com arrecadacao_receita_dedutora
              AND TAR.cod_arrecadacao       = ARD.cod_arrecadacao
              AND TAR.cod_receita           = ARD.cod_receita
              AND TAR.exercicio             = ARD.exercicio
              AND TAR.timestamp_arrecadacao = ARD.timestamp_arrecadacao
              -- Join com orcamento.receita 
              AND ARD.exercicio             = ORE.exercicio
              AND ARD.cod_receita_dedutora  = ORE.cod_receita
              -- Join com orcamento.conta_receita
              AND ORE.exercicio             = OCR.exercicio
              AND ORE.cod_conta             = OCR.cod_conta

              '' || stFiltroReceita || ''
            ORDER BY exercicio 
                    ,cod_entidade
                    ,cod_receita
            );'';

    EXECUTE stSql;

    stSql := ''
        DROP TABLE IF EXISTS tmp_deducao_estornada;

        CREATE TEMPORARY TABLE tmp_deducao_estornada AS (
            SELECT TB.cod_boletim
                  ,TO_CHAR( TB.dt_boletim, ''''dd/mm/yyyy'''' ) AS dt_boletim
                  ,TO_CHAR( TA.timestamp_arrecadacao, ''''HH24:mi:ss'''' ) as hora
                  ,TA.cod_entidade
                  ,TA.cod_arrecadacao
                  ,ARD.cod_receita_dedutora AS cod_receita
                  ,0 as cod_calculo
                  ,TA.exercicio
                  ,CAST ('''''''' as VARCHAR ) AS numeracao
                  ,TA.timestamp_arrecadacao AS timestamp
                  ,TAE.timestamp_estornada
                  ,ARDE.vl_estornado AS valor
                  ,CAST( 0.00 AS NUMERIC ) as vl_desconto
                  ,CAST( 0.00 AS NUMERIC ) as vl_multa
                  ,CAST( 0.00 AS NUMERIC ) as vl_juros
                  ,0 AS conta_debito
                  ,TA.cod_plano AS conta_credito
                  ,0 as porcentagem_valor
                  ,TA.cgm_usuario
                  ,OCR.descricao AS conta_receita
            FROM tesouraria.boletim               AS TB
                ,tesouraria.arrecadacao           AS TA
                 INNER JOIN tesouraria.arrecadacao_estornada AS TAE ON (
                        TAE.exercicio              = TA.exercicio
                    AND TAE.cod_arrecadacao        = TA.cod_arrecadacao
                    AND TAE.timestamp_arrecadacao  = TA.timestamp_arrecadacao
                    AND TA.devolucao               = false
                 )
                ,tesouraria.arrecadacao_receita   AS TAR
                ,tesouraria.arrecadacao_receita_dedutora AS ARD
                ,tesouraria.arrecadacao_receita_dedutora_estornada AS ARDE
                ,orcamento.receita                AS ORE
                ,orcamento.conta_receita          AS OCR
              -- Join com arrecadacao_estornada
            WHERE TB.exercicio               = TAE.exercicio
              AND TB.cod_boletim             = TAE.cod_boletim
              AND TB.cod_entidade            = TAE.cod_entidade
              -- Join com arrecadacao_receita
              AND TAE.exercicio              = TAR.exercicio
              AND TAE.cod_arrecadacao        = TAR.cod_arrecadacao
              AND TAE.timestamp_arrecadacao  = TAR.timestamp_arrecadacao
              -- Join com arrecadacao_receita_dedutora
              AND TAR.cod_arrecadacao        = ARD.cod_arrecadacao
              AND TAR.cod_receita            = ARD.cod_receita
              AND TAR.exercicio              = ARD.exercicio
              AND TAR.timestamp_arrecadacao  = ARD.timestamp_arrecadacao

              AND ARD.cod_arrecadacao        = ARDE.cod_arrecadacao
              AND ARD.cod_receita            = ARDE.cod_receita
              AND ARD.exercicio              = ARDE.exercicio
              AND ARD.timestamp_arrecadacao  = ARDE.timestamp_arrecadacao
              AND ARD.cod_receita_dedutora   = ARDE.cod_receita_dedutora

              AND ARDE.cod_arrecadacao       = TAE.cod_arrecadacao
              AND ARDE.exercicio             = TAE.exercicio
              AND ARDE.timestamp_arrecadacao = TAE.timestamp_arrecadacao
              AND ARDE.timestamp_estornada   = TAE.timestamp_estornada

              -- Join com orcamento.receita 
              AND ARD.exercicio             = ORE.exercicio
              AND ARD.cod_receita_dedutora  = ORE.cod_receita
              -- Join com orcamento.conta_receita
              AND ORE.exercicio             = OCR.exercicio
              AND ORE.cod_conta             = OCR.cod_conta

              '' || stFiltroReceita || ''

            ORDER BY exercicio 
                    ,cod_entidade
                    ,cod_receita);'';
    EXECUTE stSql;

    stSql := ''
        DROP TABLE IF EXISTS tmp_arrecadacao;
    
        CREATE TEMPORARY TABLE tmp_arrecadacao AS
            SELECT TB.cod_boletim
                  ,TO_CHAR( TB.dt_boletim, ''''dd/mm/yyyy'''' ) AS dt_boletim
                  ,TO_CHAR( TA.timestamp_arrecadacao, ''''HH24:mi:ss'''' ) as hora
                  ,TA.cod_entidade
                  ,TA.cod_arrecadacao
                  ,ORE.cod_receita 
                  ,ACA.cod_calculo
                  ,TA.exercicio
                  ,TAC.numeracao
                  ,TA.timestamp_arrecadacao
                  ,CASE WHEN AP.nr_parcela = 0 
                    THEN ACA.valor
                    ELSE CAST( ACA.valor / AL.total_parcelas AS NUMERIC(14,2) )
                   END AS valor
                  ,coalesce( ( ( APD.valor * tbl.porcentagem_valor ) / 100 ), 0.00 ) as vl_desconto
                  ,CAST( 0.00 AS NUMERIC ) AS vl_multa
                  ,CAST( 0.00 AS NUMERIC ) AS vl_juros
                  ,TA.cod_plano AS conta_debito
                  ,0 AS conta_credito
                  ,tbl.porcentagem_valor
                  ,TA.cgm_usuario
                  ,''''A'''' AS tipo_arrecadacao
                  ,OCR.descricao AS conta_receita
                  ,NULL::INTEGER AS cod_historico
            FROM tesouraria.boletim     AS TB
                ,tesouraria.arrecadacao AS TA
                ,tesouraria.arrecadacao_carne AS TAC
                ,arrecadacao.carne      AS AC
                ,arrecadacao.parcela    AS AP
              -- Join parcela_desconto
                LEFT JOIN arrecadacao.parcela_desconto AS APD
                ON( AP.cod_parcela = APD.cod_parcela )
                ,arrecadacao.lancamento                AS AL
                ,arrecadacao.lancamento_calculo        AS ALC
                ,arrecadacao.calculo                   AS ACA
                ,orcamento.conta_receita               AS OCR
                ,orcamento.receita                     AS ORE
              -- Join para trazer porcentagem que o calculo possui do valor da parcela
                ,( SELECT AC.cod_calculo
                         ,AP.cod_parcela
                         ,CASE WHEN AP.nr_parcela = 0
                           THEN CAST( ( ( AC.valor * 100 ) / AL.valor ) AS NUMERIC(10,2) )
                           ELSE CAST( ( ( ( AC.valor / AL.total_parcelas ) * 100 ) / AL.valor ) AS NUMERIC(10,2) )
                          END AS porcentagem_valor
                   FROM arrecadacao.parcela             AS AP
                       ,arrecadacao.lancamento          AS AL
                       ,arrecadacao.lancamento_calculo  AS ALA
                       ,arrecadacao.calculo             AS AC
                   WHERE AP.cod_lancamento = AL.cod_lancamento
                     AND AL.cod_lancamento = ALA.cod_lancamento
                     AND ALA.cod_calculo   = AC.cod_calculo
                ) AS tbl
              -- Join com arrecadacao
            WHERE TB.exercicio            = TA.exercicio
              AND TB.cod_boletim          = TA.cod_boletim
              AND TB.cod_entidade         = TA.cod_entidade
              -- Join com arrecadacao_carne
              AND TA.exercicio            = TAC.exercicio
              AND TA.cod_arrecadacao      = TAC.cod_arrecadacao
              AND TA.timestamp_arrecadacao = TAC.timestamp_arrecadacao
              AND TA.devolucao             = false
              -- Join com carne
              AND TAC.exercicio            = AC.exercicio
              AND TAC.numeracao            = AC.numeracao
              -- Join com parcela
              AND AC.cod_parcela          = AP.cod_parcela
              -- Join com lancamento
              AND AP.cod_lancamento       = AL.cod_lancamento
              -- Join com lancamento_calculo
              AND AL.cod_lancamento       = ALC.cod_lancamento
              -- Join com calculo
              AND ALC.cod_calculo         = ACA.cod_calculo
              -- Join com orcamento.receita
              AND OCR.exercicio           = ORE.exercicio
              AND OCR.cod_conta           = ORE.cod_conta
              -- join com tbl
              AND ACA.cod_calculo         = tbl.cod_calculo
              AND AP.cod_parcela          = tbl.cod_parcela
              -- Filtros
              '' || stFiltroCarne || ''

            UNION ALL

            SELECT TB.cod_boletim
                  ,TO_CHAR( TB.dt_boletim, ''''dd/mm/yyyy'''' ) AS dt_boletim
                  ,TO_CHAR( TA.timestamp_arrecadacao, ''''HH24:mi:ss'''' ) as hora
                  ,TA.cod_entidade
                  ,TA.cod_arrecadacao
                  ,TAR.cod_receita
                  ,0 as cod_calculo
                  ,TA.exercicio
                  ,'''''''' as numeracao
                  ,TA.timestamp_arrecadacao
                  ,TAR.vl_arrecadacao AS valor    
                  ,CAST( 0.00 AS NUMERIC ) as vl_desconto
                  ,CAST( 0.00 AS NUMERIC ) as vl_multa
                  ,CAST( 0.00 AS NUMERIC ) as vl_juros
                  ,TA.cod_plano AS conta_debito
                  ,0 AS conta_credito
                  ,0 as porcentagem_valor
                  ,TA.cgm_usuario
                  ,''''A'''' AS tipo_arrecadacao
                  ,OCR.descricao AS conta_receita
                  ,NULL::INTEGER AS cod_historico
            FROM tesouraria.boletim             AS TB 
                ,tesouraria.arrecadacao         AS TA
                ,tesouraria.arrecadacao_receita AS TAR
                ,orcamento.receita              AS ORE
                ,orcamento.conta_receita        AS OCR
              -- Join com arrecadacao
            WHERE TB.exercicio   = TA.exercicio
              AND TB.cod_boletim = TA.cod_boletim
              AND TB.cod_entidade= TA.cod_entidade
              -- Join com arrecadacao_receita
              AND TA.exercicio             = TAR.exercicio
              AND TA.cod_arrecadacao       = TAR.cod_arrecadacao
              AND TA.timestamp_arrecadacao = TAR.timestamp_arrecadacao
              AND TA.devolucao             = false
              -- Join com orcamento.receita
              AND TAR.exercicio            = ORE.exercicio
              AND TAR.cod_receita          = ORE.cod_receita
              -- Join com orcamento.conta_receita
              AND ORE.exercicio            = OCR.exercicio
              AND ORE.cod_conta            = OCR.cod_conta

              '' || stFiltroReceita || ''

            UNION ALL

            SELECT TD.cod_boletim
                  ,TD.dt_boletim
                  ,TD.hora
                  ,TD.cod_entidade
                  ,TD.cod_arrecadacao
                  ,TD.cod_receita
                  ,0 as cod_calculo
                  ,TD.exercicio
                  ,'''''''' as numeracao
                  ,TD.timestamp
                  ,TD.valor AS valor
                  ,CAST( 0.00 AS NUMERIC ) as vl_desconto
                  ,CAST( 0.00 AS NUMERIC ) as vl_multa
                  ,CAST( 0.00 AS NUMERIC ) as vl_juros
                  ,TD.conta_credito
                  ,TD.conta_debito 
                  ,0 as porcentagem_valor
                  ,TD.cgm_usuario
                  ,''''D'''' AS tipo_arrecadacao
                  ,'''''''' AS conta_receita
                  ,926 AS cod_historico
            FROM tmp_deducao_estornada            AS TD
                ,tesouraria.arrecadacao_estornada AS TAE
                ,tesouraria.arrecadacao           AS TA
                ,tesouraria.boletim               AS TB
            WHERE TD.exercicio             = TAE.exercicio
              AND TD.cod_arrecadacao       = TAE.cod_arrecadacao
              AND TD.timestamp             = TAE.timestamp_arrecadacao

              AND TAE.exercicio            = TA.exercicio
              AND TAE.cod_arrecadacao      = TA.cod_arrecadacao
              AND TAE.timestamp_arrecadacao= TA.timestamp_arrecadacao

              AND TA.exercicio             = TB.exercicio
              AND TA.cod_entidade          = TB.cod_entidade
              AND TA.cod_boletim           = TB.cod_boletim
              AND TA.devolucao             = false

            ORDER BY exercicio 
                    ,cod_entidade
                    ,numeracao
                    ,cod_receita
    '';
    EXECUTE stSql;

    stSql := ''
        DROP TABLE IF EXISTS tmp_estorno_arrecadacao;

        CREATE TEMPORARY TABLE tmp_estorno_arrecadacao AS
            SELECT TB.cod_boletim
                  ,TO_CHAR( TB.dt_boletim, ''''dd/mm/yyyy'''' ) AS dt_boletim
                  ,TO_CHAR( TA.timestamp_arrecadacao, ''''HH24:mi:ss'''' ) as hora
                  ,TA.cod_entidade
                  ,TA.cod_arrecadacao
                  ,TAR.cod_receita
                  ,0 as cod_calculo
                  ,TA.exercicio
                  ,'''''''' as numeracao
                  ,TA.timestamp_arrecadacao
                  ,TAE.timestamp_estornada
                  ,TAER.vl_estornado AS valor
                  ,CAST( 0.00 AS NUMERIC ) as vl_desconto
                  ,CAST( 0.00 AS NUMERIC ) as vl_multa
                  ,CAST( 0.00 AS NUMERIC ) as vl_juros
                  ,TA.cod_plano AS conta_debito
                  ,0 AS conta_credito
                  ,0 as porcentagem_valor
                  ,TA.cgm_usuario
                  ,CAST( ''''A'''' AS VARCHAR) as tipo_arrecadacao
                  ,OCR.descricao AS conta_receita
            FROM tesouraria.boletim               AS TB
                ,tesouraria.arrecadacao           AS TA
                 INNER JOIN tesouraria.arrecadacao_estornada as TAE ON (
                        TAE.exercicio             = TA.exercicio
                    AND TAE.cod_arrecadacao       = TA.cod_arrecadacao
                    AND TAE.timestamp_arrecadacao = TA.timestamp_arrecadacao
                    AND TA.devolucao              = false
                 )
                 INNER JOIN tesouraria.arrecadacao_estornada_receita as TAER ON (
                        TAER.exercicio             = TAE.exercicio
                    AND TAER.cod_arrecadacao       = TAE.cod_arrecadacao
                    AND TAER.timestamp_arrecadacao = TAE.timestamp_arrecadacao
                    AND TAER.timestamp_estornada   = TAE.timestamp_estornada
                 )
                ,tesouraria.arrecadacao_receita   AS TAR
                ,orcamento.receita                AS ORE
                ,orcamento.conta_receita          AS OCR
              -- Join com arrecadacao_estornada
            WHERE TB.exercicio   = TAE.exercicio
              AND TB.cod_boletim = TAE.cod_boletim
              AND TB.cod_entidade= TAE.cod_entidade
              -- Join com arrecadacao_receita
              AND TAE.exercicio             = TAR.exercicio
              AND TAE.cod_arrecadacao       = TAR.cod_arrecadacao
              AND TAE.timestamp_arrecadacao = TAR.timestamp_arrecadacao

              AND TAR.exercicio             = TAER.exercicio
              AND TAR.cod_arrecadacao       = TAER.cod_arrecadacao
              AND TAR.timestamp_arrecadacao = TAER.timestamp_arrecadacao
              AND TAR.cod_receita           = TAER.cod_receita

              -- Join com orcamento.receita
              AND TAR.exercicio            = ORE.exercicio
              AND TAR.cod_receita          = ORE.cod_receita
              -- Join com orcamento.conta_receita
              AND ORE.exercicio            = OCR.exercicio
              AND ORE.cod_conta            = OCR.cod_conta

              '' || stFiltroReceita || ''

            ORDER BY exercicio 
                    ,cod_entidade
                    ,numeracao
                    ,cod_receita
    '';

    EXECUTE stSql;

    stSql := ''
                SELECT AC.cod_calculo
                      ,AC.exercicio
                      ,AF.nom_funcao
                      ,TMPA.numeracao
                      ,TMPA.porcentagem_valor
                      ,LOWER( MTA.nom_tipo ) AS tipo_acrescimo
                FROM arrecadacao.calculo            AS AC
                    ,monetario.credito              AS MC
                    ,monetario.credito_acrescimo    AS MCA
                    ,monetario.acrescimo            AS MA
                    INNER JOIN( SELECT cod_acrescimo
                                      ,cod_funcao
                                      ,cod_modulo
                                      ,cod_biblioteca
                                      ,MAX( timestamp )    AS timestamp
                                FROM  monetario.formula_acrescimo
                                WHERE timestamp::date <= TO_DATE( now()::text, ''''yyyy-mm-dd'''' )
                                GROUP BY cod_acrescimo
                                        ,cod_funcao
                                        ,cod_modulo
                                        ,cod_biblioteca
                                ORDER BY cod_acrescimo
                                        ,cod_funcao
                                        ,cod_modulo
                                        ,cod_biblioteca
                    ) AS MFA ON ( MA.cod_acrescimo = MFA.cod_acrescimo )
                    ,monetario.tipo_acrescimo       AS MTA
                    ,administracao.funcao           AS AF
                    ,tmp_arrecadacao                AS TMPA
                WHERE AC.cod_credito     = MC.cod_credito
                  AND AC.cod_natureza    = MC.cod_natureza
                  AND AC.cod_genero      = MC.cod_genero
                  AND AC.cod_especie     = MC.cod_especie
                  AND MC.cod_especie     = MCA.cod_especie
                  AND MC.cod_genero      = MCA.cod_genero
                  AND MC.cod_natureza    = MCA.cod_natureza
                  AND MC.cod_credito     = MCA.cod_credito
                  -- Join com acrescimo
                  AND MCA.cod_acrescimo  = MA.cod_acrescimo
                  -- Join com tipo_acrescimo
                  AND MA.cod_tipo        = MTA.cod_tipo
                  -- Tipo = multa
                  AND (   LOWER( MTA.nom_tipo ) = ''''juros''''
                       OR LOWER( MTA.nom_tipo ) = ''''multa'''' )
                  -- Join com funcao
                  AND MFA.cod_modulo     = AF.cod_modulo
                  AND MFA.cod_biblioteca = AF.cod_biblioteca
                  AND MFA.cod_funcao     = AF.cod_funcao
                  -- Join com tmp_arrecadacao
                  AND AC.cod_calculo    = TMPA.cod_calculo
    '';

FOR reRegistro IN EXECUTE stSql
LOOP
    IF reRegistro.nom_funcao IS NOT NULL THEN
        stSqlFuncao := ''SELECT ''||reRegistro.nom_funcao||''( '''''' || reRegistro.numeracao || '''''' , TO_CHAR( now(), ''''dd/mm/yyyy'''' ), '''''' || reRegistro.exercicio || '''''' ) '';

        OPEN crCursor FOR EXECUTE stSqlFuncao;
            IF( reRegistro.tipo_acrescimo = ''juros'' ) THEN
                FETCH crCursor INTO nuVlJuros;
                UPDATE tmp_arrecadacao SET vl_juros = ( nuVlJuros * reRegistro.porcentagem_valor ) / 100 WHERE cod_calculo = reRegistro.cod_calculo AND numeracao = reRegistro.numeracao;
            END IF;
            IF( reRegistro.tipo_acrescimo = ''multa'' ) THEN
                FETCH crCursor INTO nuVlMulta;
                UPDATE tmp_arrecadacao SET vl_multa = ( nuVlMulta * reRegistro.porcentagem_valor ) / 100 WHERE cod_calculo = reRegistro.cod_calculo AND numeracao = reRegistro.numeracao;
            END IF;
        CLOSE crCursor;
    END IF;
END LOOP;

    stSql := ''
    DROP TABLE IF EXISTS tmp_arrecadacao_estornada;

    CREATE TEMPORARY TABLE tmp_arrecadacao_estornada AS(
         SELECT tbl.cod_boletim
               ,tbl.dt_boletim
               ,TO_CHAR( TAE.timestamp_arrecadacao, ''''HH24:mi:ss'''' ) as hora
               ,tbl.cod_entidade
               ,tbl.cod_arrecadacao
               ,tbl.cod_receita
               ,tbl.cod_calculo
               ,TAE.exercicio
               ,tbl.numeracao
               ,tbl.timestamp_arrecadacao
               ,TAE.timestamp_estornada AS timestamp
               ,tbl.valor
               ,tbl.vl_desconto
               ,tbl.vl_multa
               ,tbl.vl_juros
               ,tbl.conta_debito  AS conta_credito
               ,tbl.conta_credito AS conta_debito 
               ,tbl.porcentagem_valor
               ,TAE.cgm_usuario
               ,tbl.tipo_arrecadacao
               ,tbl.conta_receita
               ,NULL::INTEGER AS cod_historico
         FROM tmp_estorno_arrecadacao          AS tbl
             ,tesouraria.arrecadacao_estornada AS TAE
         WHERE tbl.exercicio             = TAE.exercicio
           AND tbl.cod_arrecadacao       = TAE.cod_arrecadacao
           AND tbl.timestamp_arrecadacao = TAE.timestamp_arrecadacao
           AND tbl.timestamp_estornada   = TAE.timestamp_estornada

         UNION ALL

         SELECT TD.cod_boletim
               ,TD.dt_boletim
               ,TD.hora
               ,TD.cod_entidade
               ,TD.cod_arrecadacao
               ,TD.cod_receita_dedutora as cod_receita
               ,TD.cod_calculo
               ,TD.exercicio
               ,TD.numeracao
               ,TA.timestamp_arrecadacao
               ,TD.timestamp
               ,TD.valor
               ,TD.vl_desconto
               ,TD.vl_multa
               ,TD.vl_juros
               ,TD.conta_credito
               ,TD.conta_debito
               ,TD.porcentagem_valor
               ,TD.cgm_usuario
               ,''''D'''' AS tipo_arrecadacao
               ,TA.conta_receita
               ,925 AS cod_historico
         FROM tmp_arrecadacao AS TA
             ,tmp_deducao     AS TD
         WHERE TA.exercicio             = TD.exercicio
           AND TA.cod_entidade          = TD.cod_entidade
           AND TA.cod_boletim           = TD.cod_boletim 
           AND TA.cod_arrecadacao       = TD.cod_arrecadacao
           AND TA.timestamp_arrecadacao = TD.timestamp
           AND TA.cod_receita           = TD.cod_receita

    UNION ALL
    /* Devolução de Receitas -- Devem aparecer como Estorno de dedução para que os lançamentos sejam efetuados na contabilidade
                             -- de forma inversa e também demonstrados como estorno nos relatórios.
                             -- Uma devolução é desfeita realizando uma arrecadação na Receita Dedutora via Orçamentaria - Arrecadação.
    */

            SELECT TB.cod_boletim
                  ,TO_CHAR( TB.dt_boletim, ''''dd/mm/yyyy'''' ) AS dt_boletim
                  ,TO_CHAR( TA.timestamp_arrecadacao, ''''HH24:mi:ss'''' ) as hora
                  ,TA.cod_entidade
                  ,TA.cod_arrecadacao
                  ,TAR.cod_receita
                  ,0 as cod_calculo
                  ,TA.exercicio
                  ,'''''''' as numeracao
                  ,TA.timestamp_arrecadacao
                  ,TA.timestamp_arrecadacao as timestamp_estornada
                  ,TAR.vl_arrecadacao AS valor    
                  ,CAST( 0.00 AS NUMERIC ) as vl_desconto
                  ,CAST( 0.00 AS NUMERIC ) as vl_multa
                  ,CAST( 0.00 AS NUMERIC ) as vl_juros
                  ,TA.cod_plano AS conta_debito
                  ,0 AS conta_credito
                  ,0 as porcentagem_valor
                  ,TA.cgm_usuario
                  ,CAST( ''''D'''' AS VARCHAR) as tipo_arrecadacao
                  ,OCR.descricao AS conta_receita
                  ,NULL::INTEGER AS cod_historico
            FROM tesouraria.boletim               AS TB 
                 JOIN tesouraria.arrecadacao AS TA 
                 ON (   TB.exercicio   = TA.exercicio
                    AND TB.cod_boletim = TA.cod_boletim
                    AND TB.cod_entidade= TA.cod_entidade
                 )
                 JOIN tesouraria.arrecadacao_receita AS TAR
                 ON (   ta.cod_arrecadacao       = tar.cod_arrecadacao  
                    AND ta.exercicio             = tar.exercicio
                    AND ta.timestamp_arrecadacao = tar.timestamp_arrecadacao
                 )
                 JOIN orcamento.receita AS ORE
                 ON (   TAR.exercicio    = ORE.exercicio
                    AND TAR.cod_receita  = ORE.cod_receita                    
                 )
                 JOIN orcamento.conta_receita AS OCR
                 ON (   ORE.exercicio = OCR.exercicio
                    AND ORE.cod_conta = OCR.cod_conta
                 )
            WHERE ta.devolucao = true

              '' || stFiltroReceita || ''

    ORDER BY exercicio 
            ,cod_entidade
            ,numeracao
            ,cod_receita
    );'';
    EXECUTE stSql;

RETURN true;

END;

'language 'plpgsql';
