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
    $Id: empenhoEmpenhadoPagoEstornado.plsql 59612 2014-09-02 12:00:51Z gelson $

* $Revision: 27052 $
* $Name:  $
* $Author: cako $
* $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $
*
* Casos de uso: uc-02.03.06
*/
CREATE OR REPLACE FUNCTION empenho.fn_empenho_empenhado_pago_estornado(varchar,varchar,varchar,varchar,varchar,varchar, varchar, varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio                 ALIAS FOR $1;
    stFiltro                    ALIAS FOR $2;
    stDtInicial                 ALIAS FOR $3;
    stDtFinal                   ALIAS FOR $4;
    stCodEntidades              ALIAS FOR $5;
    stCodOrgao                  ALIAS FOR $6;
    stCodUnidade                ALIAS FOR $7;
    stCodPao                    ALIAS FOR $8;
    stCodRecurso                ALIAS FOR $9;
    stCodElementoDispensa       ALIAS FOR $10;
    stDestinacaoRecurso         ALIAS FOR $11;
    inCodDetalhamento           ALIAS FOR $12;
    stCodElementoDispensaMasc   ALIAS FOR $13;
    stSituacao                  ALIAS FOR $14;
    stCodHistorico              ALIAS FOR $15;
    stOrdenacao                 ALIAS FOR $16;
    inCodFuncao                 ALIAS FOR $17;
    inCodSubFuncao              ALIAS FOR $18;
    inCodPrograma               ALIAS FOR $19;
    inCodPlano                  ALIAS FOR $20;
    inCodDotacao                ALIAS FOR $21;
    stSql               VARCHAR   := '';
    reRegistro          RECORD;

BEGIN
        stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
            SELECT
                p.cod_entidade as cod_entidade,
                p.cod_nota as cod_nota,
                p.exercicio_liquidacao as exercicio_liquidacao,
                p.timestamp as timestamp,
                pa.cod_plano as cod_plano,
                pc.nom_conta as nom_conta
            FROM
                contabilidade.pagamento p,
                contabilidade.lancamento_empenho le,
                contabilidade.conta_credito cc,
                contabilidade.plano_analitica pa,
                contabilidade.plano_conta pc,
                empenho.nota_liquidacao_conta_pagadora nlcp
            WHERE
                --Ligação PAGAMENTO : LANCAMENTO EMPENHO
                    p.cod_entidade      IN (' || stCodEntidades || ')
                AND p.exercicio     = ''' || stExercicio || '''
                AND p.cod_lote = le.cod_lote
                AND p.tipo = le.tipo
                AND p.sequencia = le.sequencia
                AND p.exercicio = le.exercicio
                AND p.cod_entidade = le.cod_entidade
                AND le.estorno = false

                AND to_date(to_char(p.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') 
                    BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') 
                        AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')

                --Ligação LANCAMENTO EMPENHO : CONTA_CREDITO
                AND le.cod_lote = cc.cod_lote
                AND le.tipo = cc.tipo
                AND le.exercicio = cc.exercicio
                AND le.cod_entidade = cc.cod_entidade
                AND le.sequencia = cc.sequencia
                
                AND nlcp.timestamp = p.timestamp 
                AND nlcp.exercicio_liquidacao = p.exercicio_liquidacao 
                AND nlcp.cod_entidade = p.cod_entidade
                AND nlcp.cod_nota = p.cod_nota

                --Ligação CONTA_CREDITO : PLANO ANALITICA
                AND nlcp.cod_plano = pa.cod_plano
                AND nlcp.exercicio = pa.exercicio';

                if ( inCodPlano is not null and TRIM(inCodPlano)<>'') then
                    stSql := stSql || ' and nlcp.cod_plano = ' || inCodPlano || ' ';
                end if;
                
                stSql := stSql || '
               --Ligação PLANO ANALITICA : PLANO CONTA
                AND pa.cod_conta = pc.cod_conta
                AND pa.exercicio = pc.exercicio
        );

        CREATE INDEX idx_tmp_pago ON tmp_pago (cod_entidade, cod_nota, exercicio_liquidacao, timestamp);

        ';
        
        EXECUTE stSql;
        
        stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
                   select nota_liquidacao_paga_anulada.cod_entidade
                        , nota_liquidacao_paga_anulada.cod_nota
                        , nota_liquidacao_paga_anulada.exercicio as exercicio_liquidacao
                        , nota_liquidacao_paga_anulada.timestamp
                        , sum (nota_liquidacao_paga_anulada.vl_anulado) as vl_anulado
                        , plano_analitica.cod_plano as cod_plano
                        , plano_conta.nom_conta as nom_conta
                    from empenho.nota_liquidacao_paga_anulada
                    join contabilidade.pagamento_estorno
                      on pagamento_estorno.exercicio_liquidacao = nota_liquidacao_paga_anulada.exercicio
                     and pagamento_estorno.cod_entidade 	    = nota_liquidacao_paga_anulada.cod_entidade
                     and pagamento_estorno.cod_nota 	    = nota_liquidacao_paga_anulada.cod_nota
                     and pagamento_estorno.timestamp 	    = nota_liquidacao_paga_anulada.timestamp
                     and pagamento_estorno.timestamp_anulada    = nota_liquidacao_paga_anulada.timestamp_anulada
                    join contabilidade.pagamento
                      on pagamento.exercicio    = pagamento_estorno.exercicio
                     and pagamento.cod_entidade = pagamento_estorno.cod_entidade
                     and pagamento.sequencia    = pagamento_estorno.sequencia
                     and pagamento.tipo         = pagamento_estorno.tipo
                     and pagamento.cod_lote     = pagamento_estorno.cod_lote
                    join contabilidade.lancamento_empenho
                      on lancamento_empenho.exercicio    = pagamento.exercicio
                     and lancamento_empenho.cod_lote     = pagamento.cod_lote
                     and lancamento_empenho.tipo 	     = pagamento.tipo
                     and lancamento_empenho.sequencia    = pagamento.sequencia
                     and lancamento_empenho.cod_entidade = pagamento.cod_entidade
                    join contabilidade.conta_credito
                      on lancamento_empenho.cod_lote     = conta_credito.cod_lote
                     and lancamento_empenho.tipo 	     = conta_credito.tipo
                     and lancamento_empenho.exercicio    = conta_credito.exercicio
                     and lancamento_empenho.cod_entidade = conta_credito.cod_entidade
                     and lancamento_empenho.sequencia    = conta_credito.sequencia
                    
                     
                    JOIN empenho.nota_liquidacao_conta_pagadora AS nlcp
                      on (nlcp.timestamp = pagamento.timestamp 
                     AND  nlcp.exercicio_liquidacao = pagamento.exercicio_liquidacao 
                     AND  nlcp.cod_entidade = pagamento.cod_entidade
                     AND  nlcp.cod_nota = pagamento.cod_nota)
                     
                    join contabilidade.plano_analitica
                      on nlcp.cod_plano = plano_analitica.cod_plano
                     and nlcp.exercicio = plano_analitica.exercicio
                     
                    join contabilidade.plano_conta
                      on plano_conta.cod_conta = plano_analitica.cod_conta
                     and plano_conta.exercicio = plano_analitica.exercicio
                   where to_date(to_char(nota_liquidacao_paga_anulada.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') 
                         BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') 
                             AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')
                ';

        if ( inCodPlano is not null and TRIM(inCodPlano)<>'') then

                stSql := stSql || ' 
                        AND exists (  select 1
                          from contabilidade.pagamento
                          join contabilidade.lancamento_empenho
                            on ( pagamento.exercicio    = lancamento_empenho.exercicio
                           and   pagamento.cod_lote     = lancamento_empenho.cod_lote
                           and   pagamento.tipo         = lancamento_empenho.tipo
                           and   pagamento.sequencia    = lancamento_empenho.sequencia
                           and   pagamento.cod_entidade = lancamento_empenho.cod_entidade )
                          join contabilidade.conta_debito
                            on ( lancamento_empenho.cod_lote     = conta_debito.cod_lote
                           AND   lancamento_empenho.tipo         = conta_debito.tipo
                           AND   lancamento_empenho.exercicio    = conta_debito.exercicio
                           AND   lancamento_empenho.cod_entidade = conta_debito.cod_entidade
                           AND   lancamento_empenho.sequencia    = conta_debito.sequencia )
                          join contabilidade.plano_analitica
                            on ( conta_debito.cod_plano = plano_analitica.cod_plano
                           AND   conta_debito.exercicio = plano_analitica.exercicio )
                        where
                              plano_analitica.cod_plano = ' || inCodPlano || '
                          and pagamento.exercicio_liquidacao = nota_liquidacao_paga_anulada.exercicio
                          and pagamento.cod_entidade         = nota_liquidacao_paga_anulada.cod_entidade
                          and pagamento.cod_nota             = nota_liquidacao_paga_anulada.cod_nota
                          and pagamento.timestamp            = nota_liquidacao_paga_anulada.timestamp ) 
                     ';
        end if ;
        
        stSql := stSql || ' group by nota_liquidacao_paga_anulada.cod_entidade
                                    , nota_liquidacao_paga_anulada.cod_nota
                                    , nota_liquidacao_paga_anulada.exercicio
                                    , nota_liquidacao_paga_anulada.timestamp
                                    , plano_analitica.cod_plano
                                    , plano_conta.nom_conta     )';

        EXECUTE stSql;

        stSql := '
            SELECT entidade
                 , descricao_categoria
                 , nom_tipo
                 , empenho
                 , exercicio
                 , cgm
                 , razao_social
                 , cod_nota
                 , stData
                 , ordem
                 , conta
                 , coalesce(nome_conta,''NÃO INFORMADO'')
                 , valor
                 , vl_anulado
                 , ( valor - vl_anulado ) as vl_liquido
                 , descricao
                 , CAST(substr(recurso, 0, 35) as VARCHAR) as recurso
                 , despesa 
            FROM(

            SELECT e.cod_entidade as entidade
                 , e.cod_empenho as empenho
                 , e.exercicio as exercicio
                 , pe.cgm_beneficiario as cgm
                 , cgm.nom_cgm as razao_social
                 , cast(pe.descricao as varchar ) as descricao
                 , categoria_empenho.descricao as descricao_categoria
                 , tipo_empenho.nom_tipo';

            stSql := stSql || ', to_char(nlp.timestamp,''dd/mm/yyyy'') as stData 
                    , nlp.cod_nota as cod_nota
                    , sum(nlp.vl_pago) as valor
                    , pl.cod_ordem as ordem
                    , tmp.cod_plano as conta
                    , tmp.nom_conta as nome_conta
                    , sum(coalesce(tmp_estornado.vl_anulado,0.00)) as vl_anulado ';


            stSql := stSql || ', ped_d_cd.nom_recurso as recurso, ped_d_cd.cod_estrutural as despesa ';

            stSql := stSql || '
            FROM
                empenho.empenho     as e 
               ,empenho.categoria_empenho
               ,empenho.tipo_empenho
               ,empenho.historico   as h ';

                    stSql := stSql || '
                    , empenho.nota_liquidacao nl
                    , empenho.nota_liquidacao_paga nlp
                        JOIN ( SELECT cod_entidade, cod_nota, exercicio_liquidacao, timestamp, cod_plano, nom_conta FROM tmp_pago
                               UNION
                               SELECT cod_entidade, cod_nota, exercicio_liquidacao, timestamp, cod_plano, nom_conta FROM tmp_estornado
                              ) as tmp ON (
                              --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO
                             nlp.cod_entidade = tmp.cod_entidade
                         AND nlp.cod_nota = tmp.cod_nota
                         AND nlp.exercicio = tmp.exercicio_liquidacao
                         AND nlp.timestamp = tmp.timestamp
                        )
                        LEFT JOIN tmp_estornado as tmp_estornado ON (
                            --Ligação PAGAMENTO ESTORNADO : PAGAMENTO
                                tmp_estornado.cod_entidade         = tmp.cod_entidade
                            AND tmp_estornado.cod_nota             = tmp.cod_nota
                            AND tmp_estornado.exercicio_liquidacao = tmp.exercicio_liquidacao
                            AND tmp_estornado.timestamp            = tmp.timestamp
                        )
                    , empenho.pagamento_liquidacao_nota_liquidacao_paga plnlp
                    , empenho.pagamento_liquidacao pl
                    , empenho.nota_liquidacao_conta_pagadora nlcp
                    ';
             stSql := stSql || '
              , sw_cgm              as cgm
              , empenho.pre_empenho as pe
                LEFT OUTER JOIN (
                    SELECT
                        ped.exercicio, 
                        ped.cod_pre_empenho, 
                        d.num_pao, 
                        d.num_orgao,
                        d.num_unidade, 
                        d.cod_recurso,
                        d.cod_despesa,
                        rec.nom_recurso,  
                        rec.cod_detalhamento,
                        rec.masc_recurso_red,
                        cd.cod_estrutural,
                        ppa.acao.num_acao,
                        programa.num_programa
                    FROM
                        empenho.pre_empenho_despesa as ped, 
                        orcamento.despesa           as d
                        JOIN orcamento.recurso(''' || stExercicio || ''') as rec
                        ON ( rec.cod_recurso = d.cod_recurso
                            AND rec.exercicio = d.exercicio )
                        JOIN orcamento.programa_ppa_programa
                          ON programa_ppa_programa.cod_programa = d.cod_programa
                         AND programa_ppa_programa.exercicio   = d.exercicio
                        JOIN ppa.programa
                          ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                        JOIN orcamento.pao_ppa_acao
                          ON pao_ppa_acao.num_pao = d.num_pao
                         AND pao_ppa_acao.exercicio = d.exercicio
                        JOIN ppa.acao 
                          ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                        ,orcamento.conta_despesa     as cd
                    WHERE
                        ped.exercicio      = ''' || stExercicio || '''   AND
                        ped.cod_despesa    = d.cod_despesa and 
                        ped.exercicio      = d.exercicio   and ';
                    
                    if ( inCodFuncao is not null and TRIM(inCodFuncao)<>'') then
                        stSql := stSql || ' d.cod_funcao = ' || inCodFuncao || ' and ';
                    end if;

                    if ( inCodSubFuncao is not null and TRIM(inCodSubFuncao)<>'') then
                        stSql := stSql || ' d.cod_subfuncao = ' || inCodSubFuncao || ' and ';
                    end if;

                    if ( inCodPrograma is not null and TRIM(inCodPrograma)<>'') then
                        stSql := stSql || ' programa.num_programa = ' || inCodPrograma || ' and ';
                    end if;
                

               stSql := stSql || '
                        ped.cod_conta      = cd.cod_conta  and 
                        ped.exercicio      = cd.exercicio
                ) as ped_d_cd ON pe.exercicio = ped_d_cd.exercicio AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho

            WHERE
                    e.exercicio         = ''' || stExercicio || '''
                AND e.exercicio         = pe.exercicio
                AND e.cod_pre_empenho   = pe.cod_pre_empenho
                AND e.cod_entidade      IN (' || stCodEntidades || ')
                AND pe.cgm_beneficiario = cgm.numcgm 
                AND h.cod_historico     = pe.cod_historico    
                AND h.exercicio         = pe.exercicio   
                AND categoria_empenho.cod_categoria = e.cod_categoria
                AND tipo_empenho.cod_tipo = pe.cod_tipo';

                if (stCodHistorico is not null and TRIM(stCodHistorico)<>'') then
                    stSql := stSql || ' and h.cod_historico = ' || stCodHistorico || ' ';
                end if;

                stSql := stSql || '

                   --Ligação EMPENHO : NOTA LIQUIDAÇÃO
                    AND e.exercicio = nl.exercicio_empenho
                    AND e.cod_entidade = nl.cod_entidade
                    AND e.cod_empenho = nl.cod_empenho

                    --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
                    AND nl.exercicio = nlp.exercicio
                    AND nl.cod_nota = nlp.cod_nota
                    AND nl.cod_entidade = nlp.cod_entidade

                    --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA
                    AND nlp.cod_entidade = plnlp.cod_entidade
                    AND nlp.cod_nota = plnlp.cod_nota
                    AND nlp.exercicio = plnlp.exercicio_liquidacao
                    AND nlp.timestamp = plnlp.timestamp

                    --Ligação PAGAMENTO LIQUIDACAO : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA
                    AND pl.cod_ordem = plnlp.cod_ordem
                    AND pl.exercicio = plnlp.exercicio
                    AND pl.cod_entidade = plnlp.cod_entidade
                    AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                    AND pl.cod_nota = plnlp.cod_nota

                    AND nlp.cod_entidade = nlcp.cod_entidade
                    AND nlp.cod_nota     = nlcp.cod_nota
                    AND nlp.exercicio    = nlcp.exercicio_liquidacao
                    AND nlp.timestamp    = nlcp.timestamp

                ';

                if (stCodOrgao is not null and stCodOrgao<>'') then
                    stSql := stSql || ' AND ped_d_cd.num_orgao = '|| stCodOrgao ||' ';
                end if;

                if (stCodUnidade is not null and stCodUnidade<>'') then
                    stSql := stSql || ' AND ped_d_cd.num_unidade = '|| stCodUnidade ||' ';
                end if;

                if (stCodPao is not null and stCodPao<>'') then
                    stSql := stSql || ' AND ped_d_cd.num_acao ='|| stCodPao ||' ';
                end if;

                IF (inCodPrograma IS NOT NULL AND inCodPrograma <> '') THEN
                    stSql := stSql || ' AND ped_d_cd.num_programa = '|| inCodPrograma ||' ';
                END IF;

                if (stCodRecurso is not null and stCodRecurso<>'') then
                    stSql := stSql || ' AND ped_d_cd.cod_recurso = '|| stCodRecurso ||' ';
                end if;

                if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') then
                    stSql := stSql || ' AND ped_d_cd.masc_recurso_red like '''|| stDestinacaoRecurso || '%' ||''' ';
                end if;
                
                if (inCodDotacao is not null and inCodDotacao <>'') then
                    stSql := stSql || ' AND ped_d_cd.cod_despesa = ' || inCodDotacao || ' ';
                end if;
                
                if (inCodDetalhamento is not null and inCodDetalhamento <> '') then
                        stSql := stSql || ' AND ped_d_cd.cod_detalhamento = '|| inCodDetalhamento ||' ';
                end if;

                if (stCodElementoDispensa is not null and stCodElementoDispensa<>'') then
                    stSql := stSql || ' AND ped_d_cd.cod_estrutural like publico.fn_mascarareduzida(''' || stCodElementoDispensaMasc || ''')|| ''%'' ';
                end if;

                if ( inCodPlano is not null and TRIM(inCodPlano)<>'') then
                    stSql := stSql || ' AND nlcp.cod_plano = ' || inCodPlano || ' ';
                end if;


                IF stFiltro != '' THEN
                    stSql := stSql || ' AND ' || stFiltro || ' ';
                END IF;

            stSql := stSql || ' GROUP BY ';

            stSql := stSql || 'to_char(nlp.timestamp,''dd/mm/yyyy''), nlp.cod_nota,  pl.cod_ordem, tmp.cod_plano, tmp.nom_conta,';

            stSql := stSql || ' e.cod_entidade, e.cod_empenho , e.exercicio , pe.cgm_beneficiario, cgm.nom_cgm,pe.descricao , ped_d_cd.cod_estrutural , ped_d_cd.nom_recurso , categoria_empenho.descricao, tipo_empenho.nom_tipo ORDER BY ';

            stSql := stSql || 'to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy''),';

            stSql := stSql || 'e.cod_entidade , e.cod_empenho , e.exercicio, ';

            stSql := stSql || 'nlp.cod_nota, pl.cod_ordem, tmp.cod_plano, tmp.nom_conta,';

            stSql := stSql || 'pe.cgm_beneficiario, cgm.nom_cgm) as tbl where valor <> ''0.00'' ';

            if (stOrdenacao = 'data' ) then
                stSql := stSql || ' ORDER BY to_date(stData,''dd/mm/yyyy''), entidade, empenho, exercicio, cgm, razao_social, cod_nota, ordem, conta, nome_conta';
            end if;

            if (stOrdenacao = 'credor' ) then
                stSql := stSql || ' ORDER BY to_date(stData,''dd/mm/yyyy''), razao_social, entidade, empenho, exercicio, cgm, cod_nota, ordem, conta, nome_conta';
            end if;

            if (stOrdenacao = 'credor_data' ) then
                stSql := stSql || ' ORDER BY razao_social, to_date(stData,''dd/mm/yyyy''), entidade, empenho, exercicio, cgm, cod_nota, ordem, conta, nome_conta';
            end if;

    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_pago;
    DROP TABLE tmp_estornado;
    
    RETURN;
END;
$$ language 'plpgsql';
