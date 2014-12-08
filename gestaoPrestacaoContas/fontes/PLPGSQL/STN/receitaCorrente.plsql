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
  $Id:$
*/

CREATE OR REPLACE FUNCTION stn.receitaCorrente(varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stFiltro                ALIAS FOR $2;
    dtInicial               ALIAS FOR $3;
    dtFinal                 ALIAS FOR $4;
    stCodEntidades          ALIAS FOR $5;
    stCodEstruturalInicial  ALIAS FOR $6;
    stCodEstruturalFinal    ALIAS FOR $7;
    stCodReduzidoInicial    ALIAS FOR $8;
    stCodReduzidoFinal      ALIAS FOR $9;
    inCodRecurso            ALIAS FOR $10;
    stDestinacaoRecurso     ALIAS FOR $11;
    inCodDetalhamento       ALIAS FOR $12;
    dtInicioAno         VARCHAR   := '''';
    dtFimAno            VARCHAR   := '''';
    stSql               VARCHAR   := '''';
    stMascClassReceita  VARCHAR   := '''';
    stMascRecurso       VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN
        dtInicioAno := ''01/01/'' || stExercicio;

        stSql := ''CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as primeira
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote
            WHERE
                    ore.cod_entidade    IN ('' || stCodEntidades || '')
                AND ore.exercicio       = '' || stExercicio || ''

                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio

                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
                AND lr.estorno          = true
                -- tipo de lancamento receita deve ser = A , de arrecadação
                AND lr.tipo             = ''''A''''

                -- join nas tabelas lancamento_receita e lancamento
                AND lan.cod_lote        = lr.cod_lote
                AND lan.sequencia       = lr.sequencia
                AND lan.exercicio       = lr.exercicio
                AND lan.cod_entidade    = lr.cod_entidade
                AND lan.tipo            = lr.tipo

                -- join nas tabelas lancamento e valor_lancamento
                AND vl.exercicio        = lan.exercicio
                AND vl.sequencia        = lan.sequencia
                AND vl.cod_entidade     = lan.cod_entidade
                AND vl.cod_lote         = lan.cod_lote
                AND vl.tipo             = lan.tipo
                -- na tabela valor lancamento  tipo_valor deve ser credito
                AND vl.tipo_valor       = ''''D''''

                AND lote.cod_lote       = lan.cod_lote
                AND lote.cod_entidade   = lan.cod_entidade
                AND lote.exercicio      = lan.exercicio
                AND lote.tipo           = lan.tipo

            UNION

            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as segunda
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote

            WHERE
                    ore.cod_entidade    IN('' || stCodEntidades || '')
                AND ore.exercicio       = '' || stExercicio || ''
                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio

                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
                AND lr.estorno          = false
                -- tipo de lancamento receita deve ser = A , de arrecadação
                AND lr.tipo             = ''''A''''

                -- join nas tabelas lancamento_receita e lancamento
                AND lan.cod_lote        = lr.cod_lote
                AND lan.sequencia       = lr.sequencia
                AND lan.exercicio       = lr.exercicio
                AND lan.cod_entidade    = lr.cod_entidade
                AND lan.tipo            = lr.tipo

                -- join nas tabelas lancamento e valor_lancamento
                AND vl.exercicio        = lan.exercicio
                AND vl.sequencia        = lan.sequencia
                AND vl.cod_entidade     = lan.cod_entidade
                AND vl.cod_lote         = lan.cod_lote
                AND vl.tipo             = lan.tipo
                -- na tabela valor lancamento  tipo_valor deve ser credito
                AND vl.tipo_valor       = ''''C''''

                -- Data Inicial e Data Final, antes iguala codigo do lote
                AND lote.cod_lote       = lan.cod_lote
                AND lote.cod_entidade   = lan.cod_entidade
                AND lote.exercicio      = lan.exercicio
                AND lote.tipo           = lan.tipo )'';

        EXECUTE stSql;


        stSql := ''
            SELECT tbl.cod_estrutural
                 , tbl.receita
                 , tbl.recurso
                 , tbl.descricao
                 , coalesce(sum(tbl.valor_previsto),0.00)
                 , coalesce(sum(tbl.arrecadado_periodo),0.00)
                 , coalesce(sum(tbl.arrecadado_ano),0.00)
                 , coalesce(sum(tbl.valor_previsto),0.00) + coalesce(sum(tbl.arrecadado_ano),0.00) as diferenca
            FROM (
                SELECT
                    ocr.cod_estrutural as cod_estrutural,
                    r.cod_receita  as receita,
                    rec.masc_recurso_red as recurso,
                    ocr.descricao AS descricao,
                    orcamento.fn_receita_valor_previsto( '''''' || stExercicio || ''''''
                                                        ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                        , '''''' || stCodEntidades || ''''''
                    ) as valor_previsto,
                    orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                             ,'''''' || dtInicial || ''''''
                                                             ,'''''' || dtFinal || ''''''
                    ) as arrecadado_periodo,
                    orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                             ,'''''' || dtInicioAno || ''''''
                                                             ,'''''' || dtFinal || ''''''
                    ) as arrecadado_ano
                FROM
                    orcamento.conta_receita ocr
                        LEFT OUTER JOIN orcamento.receita as r ON
                            ocr.exercicio = r.exercicio AND
                            ocr.cod_conta = r.cod_conta AND
                            r.cod_entidade    IN ('' || stCodEntidades || '') AND
                            r.exercicio       = '' || stExercicio || ''
                        LEFT JOIN orcamento.recurso('''''' || stExercicio || '''''') as rec ON
                            rec.cod_recurso = r.cod_recurso AND
                            rec.exercicio   = r.exercicio
                WHERE
                    ocr.cod_conta = ocr.cod_conta
                AND ocr.exercicio =  '' || stExercicio || ''
                    '';

                    if (stCodEstruturalInicial is not null and stCodEstruturalInicial <> '''') then
                        stSql := stSql || '' AND ocr.cod_estrutural >= '''''' || stCodEstruturalInicial || '''''' '';
                    end if;

                    if (stCodEstruturalFinal is not null and stCodEstruturalFinal <> '''') then
                        stSql := stSql || '' AND ocr.cod_estrutural <= '''''' || stCodEstruturalFinal || '''''''';
                    end if;

                    if (stCodReduzidoInicial is not null and stCodReduzidoInicial <> '''') then
                        stSql := stSql || '' AND r.cod_receita >= '''''' || stCodReduzidoInicial || '''''' '';
                    end if;

                    if (stCodReduzidoFinal is not null and stCodReduzidoFinal <> '''') then
                        stSql := stSql || '' AND r.cod_receita <= '''''' || stCodReduzidoFinal || '''''''';
                    end if;

                    if (inCodRecurso is not null and inCodRecurso <> '''') then
                        stSql := stSql || '' AND r.cod_recurso = '''''' || inCodRecurso || '''''''';
                    end if;

                    if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '''') then
                            stSql := stSql || '' AND rec.masc_recurso_red like ''''''|| stDestinacaoRecurso||''%''||'''''' '';
                    end if;
                    
                    if (inCodDetalhamento is not null and inCodDetalhamento <> '''') then
                            stSql := stSql || '' AND rec.cod_detalhamento = ''|| inCodDetalhamento ||'' '';
                    end if;

                    stSql := stSql || '' '' || stFiltro || '' ORDER BY ocr.cod_estrutural) as tbl

                    WHERE    cod_estrutural = ''''1.2.0.0.00.00.00.00.00'''' --receita de contribuições
			  or cod_estrutural = ''''1.5.0.0.00.00.00.00.00'''' --receita industrial
			  or cod_estrutural = ''''1.4.0.0.00.00.00.00.00'''' --receita agropecuaria
			  or cod_estrutural = ''''1.6.0.0.00.00.00.00.00'''' --receita servicos
			  or cod_estrutural = ''''1.9.0.0.00.00.00.00.00'''' --outras receitas correntes
			  or cod_estrutural = ''''1.1.2.0.00.00.00.00.00'''' --taxas
			  or cod_estrutural = ''''1.3.4.0.00.00.00.00.00'''' --compensações financeiras
			  or cod_estrutural = ''''1.3.9.0.00.00.00.00.00'''' --outras receitas patrimoniais
			  or cod_estrutural = ''''1.1.3.0.00.00.00.00.00'''' --contribuições de melhoria
			  or cod_estrutural = ''''1.1.1.2.02.00.00.00.00'''' --IPTU
			  or cod_estrutural = ''''1.1.1.3.05.00.00.00.00'''' --issqn imposto sobre serviço qualqer natureza
			  or cod_estrutural = ''''1.1.1.2.08.00.00.00.00'''' --ITBI Imposto Sobre Transmissao inter-vivos De Bens Imoveis E De
			  or cod_estrutural = ''''1.7.2.1.01.02.00.00.00'''' --FPM
			  or cod_estrutural = ''''1.1.1.2.04.31.00.00.00'''' --IRRF
			  or cod_estrutural = ''''1.7.2.2.01.01.00.00.00'''' --cota ICMS
			  or cod_estrutural = ''''1.7.2.2.01.02.00.00.00'''' --cota IPVA
			  or cod_estrutural = ''''1.7.2.2.01.04.00.00.00'''' --cota IPI
			  or cod_estrutural = ''''1.7.2.4.01.00.00.00.00'''' --transferencias de rec do FUNDEB
			  or cod_estrutural = ''''2.4.7.0.00.00.00.00.00'''' --valor de convenios
			  or cod_estrutural = ''''1.7.6.1.99.00.00.00.00'''' --outras transferencias uniao
			  or (cod_estrutural ilike ''''9%'''' and receita is null)


                    GROUP BY tbl.cod_estrutural, tbl.receita, tbl.recurso, tbl.descricao

                        '';


    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_valor;

    RETURN;
END;
'language 'plpgsql';

