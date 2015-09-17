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
 * Casos de uso: uc-02.02.11
 */

/*

*/

CREATE OR REPLACE FUNCTION contabilidade.balanco_patrimonial_ativo (varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$

DECLARE
    stExercicio         ALIAS FOR $1;
    stDtInicial         ALIAS FOR $2;
    stDtFinal           ALIAS FOR $3;
    stCodEntidades      ALIAS FOR $4;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];

BEGIN
   CREATE TEMPORARY TABLE tmp_ativo_financeiro (
         cod_estrutural  varchar
       , valores         numeric[] 
    );
    CREATE TEMPORARY TABLE tmp_ativo_permanente (
         cod_estrutural  varchar
       , valores         numeric[] 
    );

    stSql := 'CREATE TEMPORARY TABLE tmp_debito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.tipo_valor
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.cod_lote
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.sequencia
                        ,vl.oid as oid_temp
                    FROM
                         contabilidade.plano_conta       as pc
                        ,contabilidade.plano_analitica   as pa
                        ,contabilidade.conta_debito      as cd
                        ,contabilidade.valor_lancamento  as vl
                        ,contabilidade.lancamento        as la
                        ,contabilidade.lote              as lo
                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cd.cod_plano
                    AND     pa.exercicio    = cd.exercicio
                    AND     cd.cod_lote     = vl.cod_lote
                    AND     cd.tipo         = vl.tipo
                    AND     cd.sequencia    = vl.sequencia
                    AND     cd.exercicio    = vl.exercicio
                    AND     cd.tipo_valor   = vl.tipo_valor
                    AND     cd.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = ''D''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     lo.cod_entidade IN (' || stCodEntidades || ')
                    AND     pa.exercicio    = ' || quote_literal(stExercicio) || '
                    ORDER BY pc.cod_estrutural
                  ) as tabela
                ';
    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_credito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.tipo_valor
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.cod_lote
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.sequencia
                        ,vl.oid as oid_temp
                    FROM
                         contabilidade.plano_conta       as pc
                        ,contabilidade.plano_analitica   as pa
                        ,contabilidade.conta_credito     as cc
                        ,contabilidade.valor_lancamento  as vl
                        ,contabilidade.lancamento        as la
                        ,contabilidade.lote              as lo
                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cc.cod_plano
                    AND     pa.exercicio    = cc.exercicio
                    AND     cc.cod_lote     = vl.cod_lote
                    AND     cc.tipo         = vl.tipo
                    AND     cc.sequencia    = vl.sequencia
                    AND     cc.exercicio    = vl.exercicio
                    AND     cc.tipo_valor   = vl.tipo_valor
                    AND     cc.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = ''C''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     lo.cod_entidade IN (' || stCodEntidades || ')
                    AND     pa.exercicio = ' || quote_literal(stExercicio) || '
                    ORDER BY pc.cod_estrutural
                  ) as tabela
                ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_debito              ON tmp_debito           (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_credito             ON tmp_credito          (cod_estrutural varchar_pattern_ops, oid_temp);

    CREATE TEMPORARY TABLE tmp_totaliza_debito AS
        SELECT *
        FROM  tmp_debito
        WHERE dt_lote BETWEEN to_date( stDtInicial , 'dd/mm/yyyy' ) AND   to_date( stDtFinal , 'dd/mm/yyyy' )
        AND   tipo <> 'I';

    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
        SELECT *
        FROM  tmp_credito
        WHERE dt_lote BETWEEN to_date( stDtInicial , 'dd/mm/yyyy' ) AND   to_date( stDtFinal , 'dd/mm/yyyy' )
        AND   tipo <> 'I';

    CREATE UNIQUE INDEX unq_totaliza_credito ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito  ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, oid_temp);

    IF substr(stDtInicial,1,5) = '01/01' THEN
        stSqlComplemento := ' dt_lote = to_date( ' || quote_literal(stDtInicial) || ',' || quote_literal('dd/mm/yyyy') || ') ';
        stSqlComplemento := stSqlComplemento || ' AND tipo = '||quote_literal('I')||' ';
    ELSE
        stSqlComplemento := ' dt_lote <= to_date( ' || quote_literal(stDtInicial) || ',' || quote_literal('dd/mm/yyyy') || ')-1 ';
    END IF;

    stSql := 'CREATE TEMPORARY TABLE tmp_totaliza AS
        SELECT * FROM tmp_debito
        WHERE
             ' || stSqlComplemento || '
       UNION
        SELECT * FROM tmp_credito
        WHERE
             ' || stSqlComplemento || '
    ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_totaliza            ON tmp_totaliza         (cod_estrutural varchar_pattern_ops, oid_temp);
    
    --ATIVO FINANCEIRO
    stSql := ' Select *
                 from contabilidade.plano_conta
                where exercicio = ' || quote_literal(stExercicio) || '
                  and cod_estrutural like ''1%''
                  and escrituracao = ''analitica''
                  and indicador_superavit = ''financeiro''
             ';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        INSERT INTO tmp_ativo_financeiro VALUES ( reRegistro.cod_estrutural
                                                , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida(reRegistro.cod_estrutural) )
                                              );
        
    END LOOP;
    
    --ATIVO PERMANENTE
    stSql := ' Select *
                 from contabilidade.plano_conta
                where exercicio = ' || quote_literal(stExercicio) || '
                  and cod_estrutural like ''1%''
                  and escrituracao = ''analitica''
                  and indicador_superavit = ''permanente''
             ';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        INSERT INTO tmp_ativo_permanente VALUES ( reRegistro.cod_estrutural
                                                , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida(reRegistro.cod_estrutural) )
                                              );
        
    END LOOP;
    
    stSql := 'CREATE TEMPORARY TABLE tmp_soma_ativo_financeiro AS
                SELECT SUM(valores[1]) as vl_saldo_anterior
                     , SUM(valores[2]) as vl_saldo_debitos
                     , SUM(valores[3]) as vl_saldo_creditos
                     , SUM(valores[4]) as vl_saldo_atual
                  FROM tmp_ativo_financeiro
             ';
    EXECUTE stSql;
    
     stSql := 'CREATE TEMPORARY TABLE tmp_soma_ativo_permanente AS
                SELECT SUM(valores[1]) as vl_saldo_anterior
                     , SUM(valores[2]) as vl_saldo_debitos
                     , SUM(valores[3]) as vl_saldo_creditos
                     , SUM(valores[4]) as vl_saldo_atual
                  FROM tmp_ativo_permanente
             ';
    EXECUTE stSql;
    

    stSql := ' CREATE TEMPORARY TABLE tmp_balanco_patrimonial_ativo AS
        SELECT CAST(cod_estrutural AS VARCHAR) as cod_estrutural
             , nivel
             , CAST(nom_conta AS VARCHAR) as nom_conta
             , SUM(valores[1] * multiplicador) as vl_saldo_anterior
             , SUM(valores[2] * multiplicador) as vl_saldo_debitos
             , SUM(valores[3] * multiplicador) as vl_saldo_creditos
             , SUM(valores[4] * multiplicador) as vl_saldo_atual
          FROM (
                --CONTA ATIVO CIRCULANTE
                SELECT '||quote_literal('1.1.0.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.0.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.0.0.0.00.00')||') as nivel
                     , ''ATIVO CIRCULANTE'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA ATIVO CIRCULANTE
                SELECT '||quote_literal('1.1.0.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.1.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.0.0.0.00.00')||') as nivel
                     , ''ATIVO CIRCULANTE'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA ATIVO CIRCULANTE
                SELECT '||quote_literal('1.1.0.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.0.0.0.00.00')||') as nivel
                     , ''ATIVO CIRCULANTE'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA ATIVO CIRCULANTE
                SELECT '||quote_literal('1.1.0.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.5.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.0.0.0.00.00')||') as nivel
                     , ''ATIVO CIRCULANTE'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA ATIVO CIRCULANTE
                SELECT '||quote_literal('1.1.0.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.9.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.0.0.0.00.00')||') as nivel
                     , ''ATIVO CIRCULANTE'' as nom_conta
                     , -1 as  multiplicador


            UNION ALL

                --DEDUZ DA CONTA ATIVO CIRCULANTE
                SELECT '||quote_literal('1.1.0.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.9.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.0.0.0.00.00')||') as nivel
                     , ''ATIVO CIRCULANTE'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA CAIXA E EQUIVALENTE
                SELECT '||quote_literal('1.1.1.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.1.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.1.0.0.00.00')||') as nivel
                     , ''Caixa e Equivalentes de Caixa'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA CAIXA E EQUIVALENTE
                SELECT '||quote_literal('1.1.1.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.1.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.1.0.0.00.00')||') as nivel
                     , ''Caixa e Equivalentes de Caixa'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA CRÉDITOS A CURTO PRAZO
                SELECT '||quote_literal('1.1.2.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.0.0.00.00')||') as nivel
                     , ''Créditos a Curto Prazo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA CRÉDITOS A CURTO PRAZO
                SELECT '||quote_literal('1.1.2.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.0.0.00.00')||') as nivel
                     , ''Créditos a Curto Prazo'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA CRÉDITOS A CURTO PRAZO
                SELECT '||quote_literal('1.1.2.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.5.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.0.0.00.00')||') as nivel
                     , ''Créditos a Curto Prazo'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA CRÉDITOS A CURTO PRAZO
                SELECT '||quote_literal('1.1.2.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.9.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.0.0.00.00')||') as nivel
                     , ''Créditos a Curto Prazo'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA CRÉDITOS TRIBUTÁRIOS A RECEBER
                SELECT '||quote_literal('1.1.2.1.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.1.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.1.0.00.00')||') as nivel
                     , ''Créditos Tributários a Receber'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA CRÉDITOS TRIBUTÁRIOS A RECEBER
                SELECT '||quote_literal('1.1.2.1.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.1.0.00.00')||') as nivel
                     , ''Créditos Tributários a Receber'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA CLIENTES
                SELECT '||quote_literal('1.1.2.2.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.2.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.2.0.00.00')||') as nivel
                     , ''Clientes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA CRÉDITOS DE TRANSFERÊNCIAS A RECEBER
                SELECT '||quote_literal('1.1.2.3.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.3.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.3.0.00.00')||') as nivel
                     , ''Créditos de Transferências a Receber'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS
                SELECT '||quote_literal('1.1.2.4.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.4.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.4.0.00.00')||') as nivel
                     , ''Empréstimos e Financiamentos Concedidos'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA DÍVIDA ATIVA TRIBUTÁRIA
                SELECT '||quote_literal('1.1.2.5.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.5.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.5.0.00.00')||') as nivel
                     , ''Dívida Ativa Tributária'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA DÍVIDA ATIVA TRIBUTÁRIA
                SELECT '||quote_literal('1.1.2.5.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.5.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.5.0.00.00')||') as nivel
                     , ''Dívida Ativa Tributária'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA DÍVIDA ATIVA NÃO TRIBUTÁRIA
                SELECT '||quote_literal('1.1.2.6.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.6.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.6.0.00.00')||') as nivel
                     , ''Dívida Ativa não Tributária'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA AJUSTE DE PERDAS DE CRÉDITOS A CURTO PRAZO
                SELECT '||quote_literal('1.1.2.9.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.9.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.9.0.00.00')||') as nivel
                     , ''(-) Ajuste de Perdas de Créditos a Curto Prazo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA AJUSTE DE PERDAS DE CRÉDITOS A CURTO PRAZO
                SELECT '||quote_literal('1.1.2.9.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.2.9.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.2.9.0.00.00')||') as nivel
                     , ''(-) Ajuste de Perdas de Créditos a Curto Prazo'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA DEMAIS CRÉDITOS E VALORES A CURTO PRAZO
                SELECT '||quote_literal('1.1.3.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.3.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.3.0.0.00.00')||') as nivel
                     , ''Demais Créditos e Valores a Curto Prazo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA INVESTIMENTOS E APLICAÇÕES TEMPORÁRIAS A CURTO PRAZO
                SELECT '||quote_literal('1.1.4.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.4.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.4.0.0.00.00')||') as nivel
                     , ''Investimentos e Aplicações Temporárias a Curto Prazo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA ESTOQUES
                SELECT '||quote_literal('1.1.5.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.5.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.5.0.0.00.00')||') as nivel
                     , ''Estoques'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA VPD PAGAS ANTECIPADAMENTE
                SELECT '||quote_literal('1.1.9.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.1.9.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.1.9.0.0.00.00')||') as nivel
                     , ''VPD Pagas Antecipadamente'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA ATIVO NÃO-CIRCULANTE
                SELECT '||quote_literal('1.2.0.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.0.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.0.0.0.00.00')||') as nivel
                     , ''ATIVO NÃO-CIRCULANTE'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA ATIVO NÃO-CIRCULANTE
                SELECT '||quote_literal('1.2.0.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.0.0.0.00.00')||') as nivel
                     , ''ATIVO NÃO-CIRCULANTE'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA ATIVO NÃO-CIRCULANTE
                SELECT '||quote_literal('1.2.0.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.0.0.0.00.00')||') as nivel
                     , ''ATIVO NÃO-CIRCULANTE'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA ATIVO NÃO-CIRCULANTE
                SELECT '||quote_literal('1.2.0.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.9.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.0.0.0.00.00')||') as nivel
                     , ''ATIVO NÃO-CIRCULANTE'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA ATIVO REALIZÁVEL A LONGO PRAZO
                SELECT '||quote_literal('1.2.1.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.0.0.00.00')||') as nivel
                     , ''Ativo Realizável a Longo Prazo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA ATIVO REALIZÁVEL A LONGO PRAZO
                SELECT '||quote_literal('1.2.1.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.0.0.00.00')||') as nivel
                     , ''Ativo Realizável a Longo Prazo'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA CRÉDITOS A LONGO PRAZO
                SELECT '||quote_literal('1.2.1.1.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.00.00')||') as nivel
                     , ''Créditos a Longo Prazo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA CRÉDITOS A LONGO PRAZO
                SELECT '||quote_literal('1.2.1.1.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.00.00')||') as nivel
                     , ''Créditos a Longo Prazo'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA CRÉDITOS TRIBUTÁRIOS A RECEBER
                SELECT '||quote_literal('1.2.1.1.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.1.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.01.00')||') as nivel
                     , ''Créditos Tributários a Receber'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA CRÉDITOS TRIBUTÁRIOS A RECEBER
                SELECT '||quote_literal('1.2.1.1.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.3.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.01.00')||') as nivel
                     , ''Créditos Tributários a Receber'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA CRÉDITOS TRIBUTÁRIOS A RECEBER
                SELECT '||quote_literal('1.2.1.1.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.4.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.01.00')||') as nivel
                     , ''Créditos Tributários a Receber'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA CRÉDITOS TRIBUTÁRIOS A RECEBER
                SELECT '||quote_literal('1.2.1.1.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.5.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.01.00')||') as nivel
                     , ''Créditos Tributários a Receber'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA CLIENTES
                SELECT '||quote_literal('1.2.1.1.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.1.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.02.00')||') as nivel
                     , ''Clientes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA CLIENTES
                SELECT '||quote_literal('1.2.1.1.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.3.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.02.00')||') as nivel
                     , ''Clientes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA CLIENTES
                SELECT '||quote_literal('1.2.1.1.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.4.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.02.00')||') as nivel
                     , ''Clientes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA CLIENTES
                SELECT '||quote_literal('1.2.1.1.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.5.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.02.00')||') as nivel
                     , ''Clientes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS
                SELECT '||quote_literal('1.2.1.1.0.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.1.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.03.00')||') as nivel
                     , ''Empréstimos e Financiamentos Concedidos'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS
                SELECT '||quote_literal('1.2.1.1.0.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.3.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.03.00')||') as nivel
                     , ''Empréstimos e Financiamentos Concedidos'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS
                SELECT '||quote_literal('1.2.1.1.0.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.4.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.03.00')||') as nivel
                     , ''Empréstimos e Financiamentos Concedidos'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS
                SELECT '||quote_literal('1.2.1.1.0.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.5.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.03.00')||') as nivel
                     , ''Empréstimos e Financiamentos Concedidos'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA DÍVIDA ATIVA TRIBUTÁRIA
                SELECT '||quote_literal('1.2.1.1.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.1.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.04.00')||') as nivel
                     , ''Dívida Ativa Tributária'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA DÍVIDA ATIVA TRIBUTÁRIA
                SELECT '||quote_literal('1.2.1.1.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.3.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.04.00')||') as nivel
                     , ''Dívida Ativa Tributária'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA DÍVIDA ATIVA TRIBUTÁRIA
                SELECT '||quote_literal('1.2.1.1.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.4.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.04.00')||') as nivel
                     , ''Dívida Ativa Tributária'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA DÍVIDA ATIVA TRIBUTÁRIA
                SELECT '||quote_literal('1.2.1.1.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.5.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.04.00')||') as nivel
                     , ''Dívida Ativa Tributária'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA DÍVIDA ATIVA NÂO TRIBUTÁRIA
                SELECT '||quote_literal('1.2.1.1.0.05.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.1.05.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.05.00')||') as nivel
                     , ''Dívida Ativa não Tributária'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA DÍVIDA ATIVA NÂO TRIBUTÁRIA
                SELECT '||quote_literal('1.2.1.1.0.05.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.3.05.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.05.00')||') as nivel
                     , ''Dívida Ativa não Tributária'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA DÍVIDA ATIVA NÂO TRIBUTÁRIA
                SELECT '||quote_literal('1.2.1.1.0.05.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.4.05.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.05.00')||') as nivel
                     , ''Dívida Ativa não Tributária'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA DÍVIDA ATIVA NÂO TRIBUTÁRIA
                SELECT '||quote_literal('1.2.1.1.0.05.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.5.05.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.05.00')||') as nivel
                     , ''Dívida Ativa não Tributária'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA AJUSTE DE PERDAS DE CRÉDITOS A LONGO PRAZO
                SELECT '||quote_literal('1.2.1.1.0.99.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.1.99.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.99.00')||') as nivel
                     , ''Ajuste de Perdas de Créditos a Longo Prazo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA AJUSTE DE PERDAS DE CRÉDITOS A LONGO PRAZO
                SELECT '||quote_literal('1.2.1.1.0.99.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.3.99.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.99.00')||') as nivel
                     , ''Ajuste de Perdas de Créditos a Longo Prazo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA AJUSTE DE PERDAS DE CRÉDITOS A LONGO PRAZO
                SELECT '||quote_literal('1.2.1.1.0.99.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.4.99.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.99.00')||') as nivel
                     , ''Ajuste de Perdas de Créditos a Longo Prazo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA AJUSTE DE PERDAS DE CRÉDITOS A LONGO PRAZO
                SELECT '||quote_literal('1.2.1.1.0.99.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.1.5.99.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.1.0.99.00')||') as nivel
                     , ''Ajuste de Perdas de Créditos a Longo Prazo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA DEMAIS CRÉDITOS E VALORES A LONGO PRAZO
                SELECT '||quote_literal('1.2.1.2.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.2.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.2.0.00.00')||') as nivel
                     , ''Demais Créditos e Valores a Longo Prazo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA INVESTIMENTOS  E APLICAÇÕES TEMPORÁRIAS A LONGO PRAZO
                SELECT '||quote_literal('1.2.1.3.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.3.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.3.0.00.00')||') as nivel
                     , ''Investimentos e Aplicações Temporárias a Longo Prazo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA ESTOQUES
                SELECT '||quote_literal('1.2.1.4.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.4.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.4.0.00.00')||') as nivel
                     , ''Estoques'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA VDP PAGAS ANTECIPADAMENTE
                SELECT '||quote_literal('1.2.1.9.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.1.9.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.1.9.0.00.00')||') as nivel
                     , ''VDP Pagas Antecipadamente'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA INVESTIMENTOS
                SELECT '||quote_literal('1.2.2.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.0.0.00.00')||') as nivel
                     , ''Investimentos'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA INVESTIMENTOS
                SELECT '||quote_literal('1.2.2.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.0.0.00.00')||') as nivel
                     , ''Investimentos'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA INVESTIMENTOS
                SELECT '||quote_literal('1.2.2.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.9.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.0.0.00.00')||') as nivel
                     , ''Investimentos'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA PARTICIPAÇÕES PERMANENTES
                SELECT '||quote_literal('1.2.2.1.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.1.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.1.0.00.00')||') as nivel
                     , ''Participações Permanentes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA PARTICIPAÇÕES PERMANENTES
                SELECT '||quote_literal('1.2.2.1.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.1.0.00.00')||') as nivel
                     , ''Participações Permanentes'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE EQUIVALÊNCIA PATRIMONIAL
                SELECT '||quote_literal('1.2.2.1.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.1.1.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.1.0.01.00')||') as nivel
                     , ''Participações Avaliadas pelo Método de Equivalência Patrimonial'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE EQUIVALÊNCIA PATRIMONIAL
                SELECT '||quote_literal('1.2.2.1.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.1.3.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.1.0.01.00')||') as nivel
                     , ''Participações Avaliadas pelo Método de Equivalência Patrimonial'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE EQUIVALÊNCIA PATRIMONIAL
                SELECT '||quote_literal('1.2.2.1.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.1.4.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.1.0.01.00')||') as nivel
                     , ''Participações Avaliadas pelo Método de Equivalência Patrimonial'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA PARTICIPAÇÕES AVALIADAS PELO MÉTODO DE EQUIVALÊNCIA PATRIMONIAL
                SELECT '||quote_literal('1.2.2.1.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.1.5.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.1.0.01.00')||') as nivel
                     , ''Participações Avaliadas pelo Método de Equivalência Patrimonial'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA PADRÕES AVALIADAS PELO MÉTODO DE CUSTO 
                SELECT '||quote_literal('1.2.2.1.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.1.1.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.1.0.02.00')||') as nivel
                     , ''Padrões Avaliadas pelo Método de Custo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA PADRÕES AVALIADAS PELO MÉTODO DE CUSTO 
                SELECT '||quote_literal('1.2.2.1.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.1.3.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.1.0.02.00')||') as nivel
                     , ''Padrões Avaliadas pelo Método de Custo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA PADRÕES AVALIADAS PELO MÉTODO DE CUSTO 
                SELECT '||quote_literal('1.2.2.1.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.1.4.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.1.0.02.00')||') as nivel
                     , ''Padrões Avaliadas pelo Método de Custo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA PADRÕES AVALIADAS PELO MÉTODO DE CUSTO 
                SELECT '||quote_literal('1.2.2.1.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.1.5.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.1.0.02.00')||') as nivel
                     , ''Padrões Avaliadas pelo Método de Custo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE PARTICIPAÇÕES PERMANENTES
                SELECT '||quote_literal('1.2.2.9.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.9.1.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.9.0.01.00')||') as nivel
                     , ''(-) Redução ao Valor Recuperável de Participações Permanentes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE PARTICIPAÇÕES PERMANENTES
                SELECT '||quote_literal('1.2.2.9.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.9.3.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.9.0.01.00')||') as nivel
                     , ''(-) Redução ao Valor Recuperável de Participações Permanentes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE PARTICIPAÇÕES PERMANENTES
                SELECT '||quote_literal('1.2.2.9.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.9.4.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.9.0.01.00')||') as nivel
                     , ''(-) Redução ao Valor Recuperável de Participações Permanentes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE PARTICIPAÇÕES PERMANENTES
                SELECT '||quote_literal('1.2.2.9.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.9.5.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.9.0.01.00')||') as nivel
                     , ''(-) Redução ao Valor Recuperável de Participações Permanentes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA PROPRIEDADES PARA INVESTIMENTOS
                SELECT '||quote_literal('1.2.2.2.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.2.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.2.0.00.00')||') as nivel
                     , ''Propriedades para Investimentos'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA DEPRECIAÇÃO ACUMULADA DE PROPRIEDADES PARA INVESTIMENTOS
                SELECT '||quote_literal('1.2.2.8.1.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.8.1.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.8.1.01.00')||') as nivel
                     , ''Depreciação Acumulada de Propriedades para Investimentos'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE PROPRIEDADES DE INVESTIMENTOS
                SELECT '||quote_literal('1.2.2.9.1.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.9.1.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.9.1.02.00')||') as nivel
                     , ''Redução ao Valor Recuperável de Propriedades de Investimentos'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA INVESTIMENTOS DO RPPS DE LONGO PRAZO
                SELECT '||quote_literal('1.2.2.3.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.3.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.3.0.00.00')||') as nivel
                     , ''Investimentos do RPPS de Longo Prazo'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE INVESTIMENTOS DO RPPS
                SELECT '||quote_literal('1.2.2.9.1.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.9.1.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.9.1.03.00')||') as nivel
                     , ''(-) Redução ao Valor Recuperável de Investimentos do RPPS'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA DEMAIS INVESTIMENTOS PERMANENTES
                SELECT '||quote_literal('1.2.2.7.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.7.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.7.0.00.00')||') as nivel
                     , ''Demais Investimentos Permanentes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE DEMAIS INVESTIMENTOS PERMANENTES
                SELECT '||quote_literal('1.2.2.9.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.9.1.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.9.0.04.00')||') as nivel
                     , ''(-) Redução ao Valor Recuperável de Demais Investimentos Permanentes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE DEMAIS INVESTIMENTOS PERMANENTES
                SELECT '||quote_literal('1.2.2.9.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.9.3.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.9.0.04.00')||') as nivel
                     , ''(-) Redução ao Valor Recuperável de Demais Investimentos Permanentes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE DEMAIS INVESTIMENTOS PERMANENTES
                SELECT '||quote_literal('1.2.2.9.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.9.4.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.9.0.04.00')||') as nivel
                     , ''(-) Redução ao Valor Recuperável de Demais Investimentos Permanentes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE DEMAIS INVESTIMENTOS PERMANENTES
                SELECT '||quote_literal('1.2.2.9.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.2.9.5.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.2.9.0.04.00')||') as nivel
                     , ''(-) Redução ao Valor Recuperável de Demais Investimentos Permanentes'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA IMOBILIZADO
                SELECT '||quote_literal('1.2.3.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.3.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.3.0.0.00.00')||') as nivel
                     , ''Imobilizado'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA BENS MÓVEIS
                SELECT '||quote_literal('1.2.3.1.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.3.1.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.3.1.0.00.00')||') as nivel
                     , ''Bens Móveis'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA (-)DEPRECIAÇÃO/AMORTIZAÇÃO/EXAUSTÃO ACUMULADA DE BENS MÓVEIS
                SELECT '||quote_literal('1.2.3.8.1.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.3.8.1.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.3.8.1.01.00')||') as nivel
                     , ''(-) Depreciação/Amortização/Exaustão Acumulada de Bens Móveis'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA (-)DEPRECIAÇÃO/AMORTIZAÇÃO/EXAUSTÃO ACUMULADA DE BENS MÓVEIS
                SELECT '||quote_literal('1.2.3.8.1.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.3.8.1.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.3.8.1.01.00')||') as nivel
                     , ''(-) Depreciação/Amortização/Exaustão Acumulada de Bens Móveis'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA (-)DEPRECIAÇÃO/AMORTIZAÇÃO/EXAUSTÃO ACUMULADA DE BENS MÓVEIS
                SELECT '||quote_literal('1.2.3.8.1.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.3.8.1.05.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.3.8.1.01.00')||') as nivel
                     , ''(-) Depreciação/Amortização/Exaustão Acumulada de Bens Móveis'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE BENS MÓVEIS
                SELECT '||quote_literal('1.2.3.9.1.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.3.9.1.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.3.9.1.01.00')||') as nivel
                     , ''Redução ao Valor Recuperável de Bens Móveis'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA BENS IMÓVEIS    
                SELECT '||quote_literal('1.2.3.2.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.3.2.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.3.2.0.00.00')||') as nivel
                     , ''Bens Imóveis'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA (-)DEPRECIAÇÃO/AMORTIZAÇÃO/EXAUSTÃO ACUMULADA DE BENS IMÓVEIS
                SELECT '||quote_literal('1.2.3.8.1.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.3.8.1.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.3.8.1.02.00')||') as nivel
                     , ''(-) Depreciação/Amortização/Exaustão Acumulada de Bens Imóveis'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA (-)DEPRECIAÇÃO/AMORTIZAÇÃO/EXAUSTÃO ACUMULADA DE BENS IMÓVEIS
                SELECT '||quote_literal('1.2.3.8.1.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.3.8.1.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.3.8.1.02.00')||') as nivel
                     , ''(-) Depreciação/Amortização/Exaustão Acumulada de Bens Imóveis'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA (-)DEPRECIAÇÃO/AMORTIZAÇÃO/EXAUSTÃO ACUMULADA DE BENS IMÓVEIS
                SELECT '||quote_literal('1.2.3.8.1.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.3.8.1.06.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.3.8.1.02.00')||') as nivel
                     , ''(-) Depreciação/Amortização/Exaustão Acumulada de Bens Imóveis'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE BENS IMÓVEIS
                SELECT '||quote_literal('1.2.3.9.1.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.3.9.1.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.3.9.1.02.00')||') as nivel
                     , ''Redução ao Valor Recuperável de Bens Imóveis'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA INTANGÍVEL
                SELECT '||quote_literal('1.2.4.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.4.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.4.0.0.00.00')||') as nivel
                     , ''Intangível'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA SOFTWARES
                SELECT '||quote_literal('1.2.4.1.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.4.1.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.4.1.0.00.00')||') as nivel
                     , ''Softwares'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA AMORTIZAÇÃO ACUMULADA DE SOFTWARES
                SELECT '||quote_literal('1.2.4.8.1.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.4.8.1.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.4.8.1.01.00')||') as nivel
                     , ''(-) Amortização Acumulada de Softwares'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE SOFTWARES
                SELECT '||quote_literal('1.2.4.9.1.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.4.9.1.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.4.9.1.01.00')||') as nivel
                     , ''(-) Redução ao Valor Recuperável de Softwares'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA MARCAS, DIREITOS E PATENTES INDUSTRIAIS
                SELECT '||quote_literal('1.2.4.2.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.4.2.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.4.2.0.00.00')||') as nivel
                     , ''Marcas, Direitos e Patentes Industriais'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA AMORTIZAÇÃO ACUMULADA DE MARCAS, DIREITOS E PATENTES INDUSTRIAIS
                SELECT '||quote_literal('1.2.4.8.1.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.4.8.1.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.4.8.1.02.00')||') as nivel
                     , ''(-) Amortização Acumulada de Marcas, Direitos e Patentes Industriais'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE MARCAS, DIREITOS E PATENTES INDUSTRIAIS
                SELECT '||quote_literal('1.2.4.9.1.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.4.9.1.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.4.9.1.02.00')||') as nivel
                     , ''(-) Redução ao Valor Recuperável de Marcas, Direitos e Patentes Industriais'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA DIREITO DE USO DE IMÓVEIS
                SELECT '||quote_literal('1.2.4.3.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.4.3.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.4.3.0.00.00')||') as nivel
                     , ''Direito de Uso de Imóveis'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA AMORTIZAÇÃO ACUMULADA DE DIREITOS DE USO DE IMÓVEIS
                SELECT '||quote_literal('1.2.4.8.1.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.4.8.1.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.4.8.1.03.00')||') as nivel
                     , ''(-) Amortização Acumulada de Direitos de Uso de Imóveis'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA REDUÇÃO AO VALOR RECUPERÁVEL DE DIREITOS DE USO DE IMÓVEIS
                SELECT '||quote_literal('1.2.4.9.1.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('1.2.4.9.1.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('1.2.4.9.1.03.00')||') as nivel
                     , ''(-) Redução ao Valor Recuperável de Direitos de Uso de Imóveis'' as nom_conta
                     , 1 as  multiplicador
             ) as tabela
      GROUP BY cod_estrutural
             , nivel
             , nom_conta
    ';
    EXECUTE stSql;
    
    INSERT INTO tmp_balanco_patrimonial_ativo  SELECT '1.0.0.0.0.00.00' 
                                                    , 2 
                                                    , 'Ativo Financeiro' 
                                                    , vl_saldo_anterior
                                                    , vl_saldo_debitos
                                                    , vl_saldo_creditos
                                                    , vl_saldo_atual
                                                 FROM tmp_soma_ativo_financeiro ;

    INSERT INTO tmp_balanco_patrimonial_ativo  SELECT '1.0.0.0.0.00.01' 
                                                    , 2 
                                                    , 'Ativo Permanente' 
                                                    , vl_saldo_anterior
                                                    , vl_saldo_debitos
                                                    , vl_saldo_creditos
                                                    , vl_saldo_atual
                                                 FROM tmp_soma_ativo_permanente ;
    
    stSql := ' SELECT * FROM tmp_balanco_patrimonial_ativo ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP INDEX unq_totaliza;
    DROP INDEX unq_totaliza_debito;
    DROP INDEX unq_totaliza_credito;
    DROP INDEX unq_debito;
    DROP INDEX unq_credito;
    
    DROP TABLE tmp_totaliza;
    DROP TABLE tmp_debito;
    DROP TABLE tmp_credito;
    DROP TABLE tmp_totaliza_debito;
    DROP TABLE tmp_totaliza_credito;
    DROP TABLE tmp_ativo_financeiro;
    DROP TABLE tmp_ativo_permanente;
    DROP TABLE tmp_soma_ativo_financeiro;
    DROP TABLE tmp_soma_ativo_permanente;
    DROP TABLE tmp_balanco_patrimonial_ativo;
    

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
