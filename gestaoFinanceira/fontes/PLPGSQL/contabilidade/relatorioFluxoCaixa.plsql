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
/* Script de função PLPGSQL
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*/


CREATE OR REPLACE FUNCTION contabilidade.relatorioFluxoCaixa ( VARCHAR,VARCHAR,VARCHAR,VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio             ALIAS FOR $1;
    dtInicial               ALIAS FOR $2;
    dtFinal                 ALIAS FOR $3;
    stCodEntidade           ALIAS FOR $4;
    stSql                   VARCHAR := '';
    stExercicioAnterior     VARCHAR := ''; 
    dtInicialAnterior       VARCHAR := '';
    dtFinalAnterior         VARCHAR := '';
    reRegistro              RECORD;
    arDescricao             VARCHAR[];
    arDescricaoAux          VARCHAR[];
    i                       INTEGER;
    valoresAux              NUMERIC;
    valoresAnteriorAux      NUMERIC;
BEGIN


stExercicioAnterior     := (to_number(stExercicio,'9999')-1)::varchar;
dtInicialAnterior       := to_char(to_date(dtInicial::text,'dd/mm/yyyy')- interval '1 year','dd/mm/yyyy');
dtFinalAnterior         := to_char(to_date(dtFinal::text,'dd/mm/yyyy')- interval '1 year','dd/mm/yyyy');

--Criando tabela para armazerar as receitas referente a cada cod_estrutural
    stSql := ' CREATE TEMPORARY TABLE fluxo_caixa_receita AS
            SELECT  descricao 
                    ,sum(arrecadado_periodo) as valor
                    ,sum(arrecadado_periodo_anterior) as valor_anterior
            FROM(
                    SELECT
                    CASE
                        WHEN    cod_estrutural      = ''1.1.0.0.00.00.00.00.00''
                                OR cod_estrutural   = ''7.1.0.0.00.00.00.00.00''
                        THEN ''receita_tributaria'' 

                        WHEN    cod_estrutural      = ''1.2.0.0.00.00.00.00.00''
                                OR cod_estrutural   = ''7.2.0.0.00.00.00.00.00''
                        THEN ''receita_contribuicoes''

                        WHEN    cod_estrutural      = ''1.9.0.0.00.00.00.00.00''
                        THEN ''outras_receitas_derivadas''

                        WHEN    cod_estrutural      = ''1.3.1.0.00.00.00.00.00''
                                OR cod_estrutural   = ''1.3.2.0.00.00.00.00.00''
                                OR cod_estrutural   = ''1.3.3.0.00.00.00.00.00''
                                OR cod_estrutural   = ''1.3.4.0.00.00.00.00.00''
                                OR cod_estrutural   = ''1.3.5.0.00.00.00.00.00''
                                OR cod_estrutural   = ''1.3.6.0.00.00.00.00.00''
                                OR cod_estrutural   = ''1.3.9.0.00.00.00.00.00''
                        THEN ''receita_patrimonial''
                                
                        WHEN    cod_estrutural      = ''1.4.0.0.00.00.00.00.00''
                        THEN ''receita_agropecuaria''
                                
                        WHEN    cod_estrutural      = ''1.5.0.0.10.00.00.00.00''
                                OR cod_estrutural   = ''1.5.2.0.00.00.00.00.00''
                                OR cod_estrutural   = ''1.5.3.0.00.00.00.00.00''
                                OR cod_estrutural   = ''1.5.9.0.00.00.00.00.00''
                        THEN ''receita_industrial''

                        WHEN    cod_estrutural      = ''1.6.0.0.00.00.00.00.00''
                        THEN ''receita_servicos''

                        WHEN    cod_estrutural      = ''1.3.2.5.00.00.00.00.00''
                                OR cod_estrutural   = ''1.3.2.6.00.00.00.00.00''
                                OR cod_estrutural   = ''1.3.2.7.00.00.00.00.00''
                                OR cod_estrutural   = ''1.3.2.8.00.00.00.00.00''
                        THEN ''remuneracao_disponibilidades''
                                
                        WHEN    cod_estrutural      = ''1.7.2.1.00.00.00.00.00''
                                OR cod_estrutural   = ''1.7.6.1.00.00.00.00.00''
                                OR cod_estrutural   = ''2.4.2.1.00.00.00.00.00''
                                OR cod_estrutural   = ''2.4.7.1.00.00.00.00.00''
                        THEN ''transferencia_uniao''
                        
                        WHEN    cod_estrutural      = ''1.7.2.2.00.00.00.00.00''
                                OR cod_estrutural   = ''1.7.6.2.00.00.00.00.00''
                                OR cod_estrutural   = ''2.4.2.2.00.00.00.00.00''
                                OR cod_estrutural   = ''2.4.7.2.00.00.00.00.00''
                        THEN ''transferencia_estados_df''

                        WHEN    cod_estrutural      = ''1.7.2.3.00.00.00.00.00''
                                OR cod_estrutural   = ''1.7.6.3.00.00.00.00.00''
                                OR cod_estrutural   = ''2.4.2.3.00.00.00.00.00''
                                OR cod_estrutural   = ''2.4.7.3.00.00.00.00.00''
                        THEN ''transferencia_municipios''

                        WHEN    cod_estrutural      = ''1.7.2.4.00.00.00.00.00''
                                OR cod_estrutural   = ''1.7.3.0.00.00.00.00.00''
                                OR cod_estrutural   = ''1.7.4.0.00.00.00.00.00''
                                OR cod_estrutural   = ''1.7.5.0.00.00.00.00.00''
                                OR cod_estrutural   = ''1.7.7.0.00.00.00.00.00''
                                OR cod_estrutural   = ''1.7.6.4.00.00.00.00.00''
                                OR cod_estrutural   = ''2.4.3.0.00.00.00.00.00''
                                OR cod_estrutural   = ''2.4.4.0.00.00.00.00.00''
                                OR cod_estrutural   = ''2.4.5.0.00.00.00.00.00''
                                OR cod_estrutural   = ''2.4.6.0.00.00.00.00.00''
                                OR cod_estrutural   = ''2.4.7.4.00.00.00.00.00''
                                OR cod_estrutural   = ''2.4.8.0.00.00.00.00.00''
                        THEN ''outras_transferencias''
                        
                        WHEN    cod_estrutural      = ''2.2.0.0.00.00.00.00.00''
                                OR cod_estrutural   = ''8.2.0.0.00.00.00.00.00''
                        THEN ''alienacao_bens''

                        WHEN    cod_estrutural      = ''2.3.0.0.00.00.00.00.00''
                                OR cod_estrutural   = ''2.5.0.0.00.00.00.00.00''
                                OR cod_estrutural   = ''8.3.0.0.00.00.00.00.00''
                                OR cod_estrutural   = ''8.5.0.0.00.00.00.00.00''
                        THEN ''amortizacao_emprestimos_financiamentos_concedidos''

                        WHEN    cod_estrutural      = ''2.1.0.0.00.00.00.00.00''
                                OR cod_estrutural   = ''8.1.0.0.00.00.00.00.00''
                        THEN ''operacao_credito''
                    
                    ELSE ''''    
                    END as descricao
                        ,retorno.arrecadado_periodo
                        ,exercicio_anterior.arrecadado_periodo_anterior
                    FROM orcamento.fn_balancete_receita('|| quote_literal(stExercicio) ||'
                                                        ,''''
                                                        ,'|| quote_literal(dtInicial) ||'
                                                        ,'|| quote_literal(dtFinal) ||'
                                                        ,'|| quote_literal(stCodEntidade) ||'
                                                        ,'''','''','''','''','''','''','''')
                    as retorno(                      
                            cod_estrutural      varchar,                                           
                            receita             integer,                                           
                            recurso             varchar,                                           
                            descricao           varchar,                                           
                            valor_previsto      numeric,                                           
                            arrecadado_periodo  numeric,                                           
                            arrecadado_ano      numeric,                                           
                            diferenca           numeric                                           
                            )
                LEFT JOIN orcamento.fn_balancete_receita('|| quote_literal(stExercicioAnterior) ||'
                                                        ,''''
                                                        ,'|| quote_literal(dtInicialAnterior) ||'
                                                        ,'|| quote_literal(dtFinalAnterior) ||'
                                                        ,'|| quote_literal(stCodEntidade) ||'
                                                        ,'''','''','''','''','''','''','''')
                    as exercicio_anterior(                      
                            cod_estrutural_anterior      varchar,                                           
                            receita_anterior             integer,                                           
                            recurso_anterior             varchar,                                           
                            descricao_anterior           varchar,                                           
                            valor_previsto_anterior      numeric,                                           
                            arrecadado_periodo_anterior  numeric,                                           
                            arrecadado_ano_anterior      numeric,                                           
                            diferenca_anterior           numeric                                           
                            )
                    ON(retorno.cod_estrutural = exercicio_anterior.cod_estrutural_anterior)
                        
                
            
            ) as tbl
            GROUP BY descricao
            ';
        
    EXECUTE stSql;


--Criando tabela para armazenar despesas referente ao cod_estrutural
    stSql := ' CREATE TEMPORARY TABLE fluxo_caixa_despesa AS
    SELECT descricao
	,sum(valor) as valor
        ,sum(valor_anterior) as valor_anterior
    FROM(
            SELECT 
                CASE
                    when descricao = ''pessoal_outras_despesas'' and cod_funcao = 1
                        THEN ''legislativa''
                    when descricao = ''pessoal_outras_despesas'' and cod_funcao = 2
                        THEN ''judiciaria''
                    when descricao = ''pessoal_outras_despesas'' and cod_funcao = 9
                        THEN ''previdencia_social''
                    when descricao = ''pessoal_outras_despesas'' and cod_funcao = 4
                        THEN ''administracao''
                    when descricao = ''pessoal_outras_despesas'' and cod_funcao = 5
                        THEN ''defesa_nacional''
                    when descricao = ''pessoal_outras_despesas'' and cod_funcao = 6
                        THEN ''seguranca_publica''
                    when descricao = ''pessoal_outras_despesas'' and cod_funcao = 7
                        THEN ''relacoes_exteriores''
                    when descricao = ''pessoal_outras_despesas'' and cod_funcao = 8
                        THEN ''assistencia_social''
                    when descricao = ''pessoal_outras_despesas'' and cod_funcao = 10
                        THEN ''saude''
                    when descricao = ''pessoal_outras_despesas'' and cod_funcao = 11
                        THEN ''trabalho''
                    when descricao = ''pessoal_outras_despesas'' and cod_funcao = 12
                        THEN ''educacao''
                    when descricao = ''pessoal_outras_despesas'' and cod_funcao NOT IN(1,2,4,5,6,7,8,9,10,11,12)
                        THEN ''etc''
                ELSE descricao
                END as descricao
                ,valor
                ,valor_anterior
            FROM(
                    SELECT  descricao
                            ,cod_funcao
                            ,SUM(valor) AS valor
                            ,SUM(valor_anterior) AS valor_anterior
                    FROM(
                            SELECT 
                                    CASE
                                        WHEN    classificacao       LIKE ''3.1.9.0%''
                                                OR classificacao    LIKE ''3.3.9.0%''
                                                OR classificacao    LIKE ''3.1.9.1%''
                                                OR classificacao    LIKE ''3.3.9.1%''
                                                OR classificacao    LIKE ''3.1.9.3%''
                                                OR classificacao    LIKE ''3.3.9.3%''
                                                OR classificacao    LIKE ''3.1.9.4%''
                                                OR classificacao    LIKE ''3.3.9.4%''
                                                OR classificacao    LIKE ''3.1.9.5%''
                                                OR classificacao    LIKE ''3.3.9.5%''
                                                OR classificacao    LIKE ''3.1.9.6%''
                                                OR classificacao    LIKE ''3.3.9.6%''
                                        THEN ''pessoal_outras_despesas''
                                        WHEN    classificacao       LIKE ''3.2.9.0.21%''
                                                OR classificacao    LIKE ''3.2.9.0.22%''
                                                OR classificacao    LIKE ''3.2.9.0.23%''
                                                OR classificacao    LIKE ''3.2.9.0.24%''
                                                OR classificacao    LIKE ''3.2.9.0.25%''
                                                OR classificacao    LIKE ''4.6.9.0.73%''
                                                OR classificacao    LIKE ''4.6.9.0.74%''
                                                OR classificacao    LIKE ''4.6.9.0.75%''
                                        THEN ''juros_correcao_divida_interna''
                                        WHEN    classificacao       LIKE ''3.2.9.0.21.03%''
                                        THEN ''subtrai_juros_correcao_divida_interna''
                                        WHEN    classificacao       LIKE ''3.2.9.0.21.03%''
                                                OR classificacao    LIKE ''3.2.9.0.92.04%''
                                        THEN ''juros_correcao_divida_externa''
                                        WHEN    classificacao       LIKE ''3.2.9.0.22%''
                                                OR classificacao    LIKE ''3.2.9.0.24%''
                                                OR classificacao    LIKE ''3.2.9.0.92%''
                                                OR classificacao    LIKE ''3.2.9.0.93%''
                                        THEN ''outros_encargos''
                                        WHEN    classificacao       LIKE ''3.2.9.0.92.04%''
                                        THEN ''subtrai_outros_encargos''
                                        WHEN    classificacao       LIKE ''3.3.3.2%''
                                                OR classificacao    LIKE ''3.3.2.2%''
                                                OR classificacao    LIKE ''4.4.2.0%''
                                        THEN ''despesa_transferencia_uniao''
                                        WHEN    classificacao       LIKE ''3.1.3.0%''
                                                OR classificacao    LIKE ''3.3.3.0%''
                                                OR classificacao    LIKE ''3.3.3.1%''
                                                OR classificacao    LIKE ''3.3.3.2%''
                                                OR classificacao    LIKE ''4.4.3.0%''
                                                OR classificacao    LIKE ''4.4.3.1%''
                                                OR classificacao    LIKE ''4.5.3.0%''
                                        THEN ''despesa_transferencia_estado_df''
                                        WHEN    classificacao       LIKE ''3.3.4.0%''
                                                OR classificacao    LIKE ''3.3.4.1%''
                                                OR classificacao    LIKE ''3.3.4.2%''
                                                OR classificacao    LIKE ''4.4.4.0%''
                                                OR classificacao    LIKE ''4.4.4.1%''
                                                OR classificacao    LIKE ''4.5.4.0%''
                                        THEN ''despesa_transferencia_municipios''
                                        WHEN    classificacao       LIKE ''3.1.8.0%''
                                                OR classificacao    LIKE ''3.3.8.0%''
                                                OR classificacao    LIKE ''3.5.0.0%''
                                                OR classificacao    LIKE ''3.6.0.0%''
                                                OR classificacao    LIKE ''3.7.0.0%''
                                                OR classificacao    LIKE ''3.1.7.1%''
                                                OR classificacao    LIKE ''4.4.5.0%''
                                                OR classificacao    LIKE ''4.4.7.0%''
                                                OR classificacao    LIKE ''4.5.7.2%''
                                                OR classificacao    LIKE ''4.4.8.0%''
                                                OR classificacao    LIKE ''4.5.8.0%''
                                                OR classificacao    LIKE ''4.5.5.0%''
                                                OR classificacao    LIKE ''4.4.6.0%''
                                        THEN ''despesa_outras_transferencias''
                                        WHEN    classificacao       LIKE ''4.4.2.2.51%''
                                                OR classificacao    LIKE ''4.4.2.2.52%''
                                                OR classificacao    LIKE ''4.4.3.2.51%''
                                                OR classificacao    LIKE ''4.4.3.2.52%''
                                                OR classificacao    LIKE ''4.4.4.2.51%''
                                                OR classificacao    LIKE ''4.4.4.2.52%''
                                                OR classificacao    LIKE ''4.4.5.0.51%''
                                                OR classificacao    LIKE ''4.4.5.0.52%''
                                                OR classificacao    LIKE ''4.4.8.0.51%''
                                                OR classificacao    LIKE ''4.4.8.0.52%''
                                                OR classificacao    LIKE ''4.4.9.0.51%''
                                                OR classificacao    LIKE ''4.4.9.0.52%''
                                                OR classificacao    LIKE ''4.4.9.0.61%''
                                                OR classificacao    LIKE ''4.4.9.1.51%''
                                                OR classificacao    LIKE ''4.4.9.1.52%''
                                                OR classificacao    LIKE ''4.4.9.3.51%''
                                                OR classificacao    LIKE ''4.4.9.3.52%''
                                                OR classificacao    LIKE ''4.4.9.4.51%''
                                                OR classificacao    LIKE ''4.4.9.4.52%''
                                                OR classificacao    LIKE ''4.5.3.2.61%''
                                                OR classificacao    LIKE ''4.5.3.2.64%''
                                                OR classificacao    LIKE ''4.5.3.2.65%''
                                                OR classificacao    LIKE ''4.5.4.2.64%''
                                                OR classificacao    LIKE ''4.5.9.0.61%''
                                                OR classificacao    LIKE ''4.5.9.0.63%''
                                                OR classificacao    LIKE ''4.5.9.0.64%''
                                                OR classificacao    LIKE ''4.5.9.0.65%''
                                                OR classificacao    LIKE ''4.5.9.1.61%''
                                        THEN ''aquisicao_ativo_nao_circulante''
                                        WHEN    classificacao       LIKE ''4.5.5.0.66%''
                                                OR classificacao    LIKE ''4.5.9.0.66%''
                                                OR classificacao    LIKE ''4.5.9.1.66%''
                                        THEN ''concessao_emprestimos_financiamentos''
                                        WHEN    classificacao       LIKE ''4.4.2.0.41%''
                                                OR classificacao    LIKE ''4.4.2.0.42%''
                                                OR classificacao    LIKE ''4.4.4.0.92%''
                                                OR classificacao    LIKE ''4.4.2.2.92%''
                                                OR classificacao    LIKE ''4.4.2.2.93%''
                                                OR classificacao    LIKE ''4.4.3.0.41%''
                                                OR classificacao    LIKE ''4.4.3.0.42%''
                                                OR classificacao    LIKE ''4.4.3.1.41%''
                                                OR classificacao    LIKE ''4.4.3.1.42%''
                                                OR classificacao    LIKE ''4.4.3.1.92%''
                                                OR classificacao    LIKE ''4.4.3.2.20%''
                                                OR classificacao    LIKE ''4.4.3.2.92%''
                                                OR classificacao    LIKE ''4.4.3.2.93%''
                                                OR classificacao    LIKE ''4.4.4.0.41%''
                                                OR classificacao    LIKE ''4.4.4.0.42%''
                                                OR classificacao    LIKE ''4.4.4.1.41%''
                                                OR classificacao    LIKE ''4.4.4.1.42%''
                                                OR classificacao    LIKE ''4.4.4.1.92%''
                                                OR classificacao    LIKE ''4.4.4.2.14%''
                                                OR classificacao    LIKE ''4.4.4.2.14%''
                                                OR classificacao    LIKE ''4.4.4.2.92%''
                                                OR classificacao    LIKE ''4.4.5.0.14%''
                                                OR classificacao    LIKE ''4.4.5.0.30%''
                                                OR classificacao    LIKE ''4.4.5.0.36%''
                                                OR classificacao    LIKE ''4.4.5.0.39%''
                                                OR classificacao    LIKE ''4.4.5.0.41%''
                                                OR classificacao    LIKE ''4.4.5.0.42%''
                                                OR classificacao    LIKE ''4.4.5.0.47%''
                                        THEN ''outros_desembolsos''

                                        WHEN    classificacao       LIKE ''4.6%''
                                        THEN ''amortizacao_refinanciamento_divida''

                                        WHEN    cod_funcao = 28 and cod_subfuncao = 843
                                        THEN    (CASE WHEN classificacao LIKE ''4.6.9.0.73%''
                                            OR classificacao LIKE ''4.6.9.0.74%''
                                            OR classificacao LIKE ''4.6.9.0.75%''
                                                THEN ''subtrai_amortizacao_refinanciamento_divida'' else ''outras_contas'' END)

                                    ELSE ''''
                                    END AS descricao
                                    ,cod_funcao
                                    ,pago_per as valor
                                    ,0 as valor_anterior
                                FROM orcamento.fn_balancete_despesa('|| quote_literal(stExercicio) ||'
                                                                    ,'' AND od.cod_entidade IN  ('|| stCodEntidade ||')''
                                                                    ,'|| quote_literal(dtInicial) ||'
                                                                    ,'|| quote_literal(dtFinal) ||'
                                                                    ,'''','''','''','''','''' ,'''','''', '''' )
                                as retorno( 
                                    exercicio       char(4),                                                                                
                                    cod_despesa     integer,                                                                                
                                    cod_entidade    integer,                                                                                
                                    cod_programa    integer,                                                                                
                                    cod_conta       integer,                                                                                
                                    num_pao         integer,                                                                                
                                    num_orgao       integer,                                                                                
                                    num_unidade     integer,                                                                                
                                    cod_recurso     integer,                                                                                
                                    cod_funcao      integer,                                                                                
                                    cod_subfuncao   integer,                                                                                
                                    tipo_conta      varchar,                                                                                
                                    vl_original     numeric,                                                                                
                                    dt_criacao      date,                                                                                   
                                    classificacao   varchar,                                                                                
                                    descricao       varchar,                                                                                
                                    num_recurso     varchar,                                                                                
                                    nom_recurso     varchar,                                                                                
                                    nom_orgao       varchar,                                                                                
                                    nom_unidade     varchar,                                                                                
                                    nom_funcao      varchar,                                                                                
                                    nom_subfuncao   varchar,                                                                                
                                    nom_programa    varchar,                                                                                
                                    nom_pao         varchar,                                                                                
                                    empenhado_ano   numeric,                                                                                
                                    empenhado_per   numeric,                                                                                 
                                    anulado_ano     numeric,                                                                                
                                    anulado_per     numeric,                                                                                
                                    pago_ano        numeric,                                                                                
                                    pago_per        numeric,                                                                                 
                                    liquidado_ano   numeric,                                                                                
                                    liquidado_per   numeric,                                                                                
                                    saldo_inicial   numeric,                                                                                
                                    suplementacoes  numeric,                                                                                
                                    reducoes        numeric,                                                                                
                                    total_creditos  numeric,                                                                                
                                    credito_suplementar  numeric,                                                                            
                                    credito_especial  numeric,                                                                            
                                    credito_extraordinario  numeric,                                                                            
                                    num_programa    varchar,
                                    num_acao        varchar
                                    )       
                            
                            UNION ALL

                            SELECT 
                                    CASE
                                        WHEN    classificacao       LIKE ''3.1.9.0%''
                                                OR classificacao    LIKE ''3.3.9.0%''
                                                OR classificacao    LIKE ''3.1.9.1%''
                                                OR classificacao    LIKE ''3.3.9.1%''
                                                OR classificacao    LIKE ''3.1.9.3%''
                                                OR classificacao    LIKE ''3.3.9.3%''
                                                OR classificacao    LIKE ''3.1.9.4%''
                                                OR classificacao    LIKE ''3.3.9.4%''
                                                OR classificacao    LIKE ''3.1.9.5%''
                                                OR classificacao    LIKE ''3.3.9.5%''
                                                OR classificacao    LIKE ''3.1.9.6%''
                                                OR classificacao    LIKE ''3.3.9.6%''
                                        THEN ''pessoal_outras_despesas''
                                        WHEN    classificacao       LIKE ''3.2.9.0.21%''
                                                OR classificacao    LIKE ''3.2.9.0.22%''
                                                OR classificacao    LIKE ''3.2.9.0.23%''
                                                OR classificacao    LIKE ''3.2.9.0.24%''
                                                OR classificacao    LIKE ''3.2.9.0.25%''
                                                OR classificacao    LIKE ''4.6.9.0.73%''
                                                OR classificacao    LIKE ''4.6.9.0.74%''
                                                OR classificacao    LIKE ''4.6.9.0.75%''
                                        THEN ''juros_correcao_divida_interna''
                                        WHEN    classificacao       LIKE ''3.2.9.0.21.03%''
                                        THEN ''subtrai_juros_correcao_divida_interna''
                                        WHEN    classificacao       LIKE ''3.2.9.0.21.03%''
                                                OR classificacao    LIKE ''3.2.9.0.92.04%''
                                        THEN ''juros_correcao_divida_externa''
                                        WHEN    classificacao       LIKE ''3.2.9.0.22%''
                                                OR classificacao    LIKE ''3.2.9.0.24%''
                                                OR classificacao    LIKE ''3.2.9.0.92%''
                                                OR classificacao    LIKE ''3.2.9.0.93%''
                                        THEN ''outros_encargos''
                                        WHEN    classificacao       LIKE ''3.2.9.0.92.04%''
                                        THEN ''subtrai_outros_encargos''
                                        WHEN    classificacao       LIKE ''3.3.3.2%''
                                                OR classificacao    LIKE ''3.3.2.2%''
                                                OR classificacao    LIKE ''4.4.2.0%''
                                        THEN ''despesa_transferencia_uniao''
                                        WHEN    classificacao       LIKE ''3.1.3.0%''
                                                OR classificacao    LIKE ''3.3.3.0%''
                                                OR classificacao    LIKE ''3.3.3.1%''
                                                OR classificacao    LIKE ''3.3.3.2%''
                                                OR classificacao    LIKE ''4.4.3.0%''
                                                OR classificacao    LIKE ''4.4.3.1%''
                                                OR classificacao    LIKE ''4.5.3.0%''
                                        THEN ''despesa_transferencia_estado_df''
                                        WHEN    classificacao       LIKE ''3.3.4.0%''
                                                OR classificacao    LIKE ''3.3.4.1%''
                                                OR classificacao    LIKE ''3.3.4.2%''
                                                OR classificacao    LIKE ''4.4.4.0%''
                                                OR classificacao    LIKE ''4.4.4.1%''
                                                OR classificacao    LIKE ''4.5.4.0%''
                                        THEN ''despesa_transferencia_municipios''
                                        WHEN    classificacao       LIKE ''3.1.8.0%''
                                                OR classificacao    LIKE ''3.3.8.0%''
                                                OR classificacao    LIKE ''3.5.0.0%''
                                                OR classificacao    LIKE ''3.6.0.0%''
                                                OR classificacao    LIKE ''3.7.0.0%''
                                                OR classificacao    LIKE ''3.1.7.1%''
                                                OR classificacao    LIKE ''4.4.5.0%''
                                                OR classificacao    LIKE ''4.4.7.0%''
                                                OR classificacao    LIKE ''4.5.7.2%''
                                                OR classificacao    LIKE ''4.4.8.0%''
                                                OR classificacao    LIKE ''4.5.8.0%''
                                                OR classificacao    LIKE ''4.5.5.0%''
                                                OR classificacao    LIKE ''4.4.6.0%''
                                        THEN ''despesa_outras_transferencias''
                                        WHEN    classificacao       LIKE ''4.4.2.2.51%''
                                                OR classificacao    LIKE ''4.4.2.2.52%''
                                                OR classificacao    LIKE ''4.4.3.2.51%''
                                                OR classificacao    LIKE ''4.4.3.2.52%''
                                                OR classificacao    LIKE ''4.4.4.2.51%''
                                                OR classificacao    LIKE ''4.4.4.2.52%''
                                                OR classificacao    LIKE ''4.4.5.0.51%''
                                                OR classificacao    LIKE ''4.4.5.0.52%''
                                                OR classificacao    LIKE ''4.4.8.0.51%''
                                                OR classificacao    LIKE ''4.4.8.0.52%''
                                                OR classificacao    LIKE ''4.4.9.0.51%''
                                                OR classificacao    LIKE ''4.4.9.0.52%''
                                                OR classificacao    LIKE ''4.4.9.0.61%''
                                                OR classificacao    LIKE ''4.4.9.1.51%''
                                                OR classificacao    LIKE ''4.4.9.1.52%''
                                                OR classificacao    LIKE ''4.4.9.3.51%''
                                                OR classificacao    LIKE ''4.4.9.3.52%''
                                                OR classificacao    LIKE ''4.4.9.4.51%''
                                                OR classificacao    LIKE ''4.4.9.4.52%''
                                                OR classificacao    LIKE ''4.5.3.2.61%''
                                                OR classificacao    LIKE ''4.5.3.2.64%''
                                                OR classificacao    LIKE ''4.5.3.2.65%''
                                                OR classificacao    LIKE ''4.5.4.2.64%''
                                                OR classificacao    LIKE ''4.5.9.0.61%''
                                                OR classificacao    LIKE ''4.5.9.0.63%''
                                                OR classificacao    LIKE ''4.5.9.0.64%''
                                                OR classificacao    LIKE ''4.5.9.0.65%''
                                                OR classificacao    LIKE ''4.5.9.1.61%''
                                        THEN ''aquisicao_ativo_nao_circulante''
                                        WHEN    classificacao       LIKE ''4.5.5.0.66%''
                                                OR classificacao    LIKE ''4.5.9.0.66%''
                                                OR classificacao    LIKE ''4.5.9.1.66%''
                                        THEN ''concessao_emprestimos_financiamentos''
                                        WHEN    classificacao       LIKE ''4.4.2.0.41%''
                                                OR classificacao    LIKE ''4.4.2.0.42%''
                                                OR classificacao    LIKE ''4.4.4.0.92%''
                                                OR classificacao    LIKE ''4.4.2.2.92%''
                                                OR classificacao    LIKE ''4.4.2.2.93%''
                                                OR classificacao    LIKE ''4.4.3.0.41%''
                                                OR classificacao    LIKE ''4.4.3.0.42%''
                                                OR classificacao    LIKE ''4.4.3.1.41%''
                                                OR classificacao    LIKE ''4.4.3.1.42%''
                                                OR classificacao    LIKE ''4.4.3.1.92%''
                                                OR classificacao    LIKE ''4.4.3.2.20%''
                                                OR classificacao    LIKE ''4.4.3.2.92%''
                                                OR classificacao    LIKE ''4.4.3.2.93%''
                                                OR classificacao    LIKE ''4.4.4.0.41%''
                                                OR classificacao    LIKE ''4.4.4.0.42%''
                                                OR classificacao    LIKE ''4.4.4.1.41%''
                                                OR classificacao    LIKE ''4.4.4.1.42%''
                                                OR classificacao    LIKE ''4.4.4.1.92%''
                                                OR classificacao    LIKE ''4.4.4.2.14%''
                                                OR classificacao    LIKE ''4.4.4.2.14%''
                                                OR classificacao    LIKE ''4.4.4.2.92%''
                                                OR classificacao    LIKE ''4.4.5.0.14%''
                                                OR classificacao    LIKE ''4.4.5.0.30%''
                                                OR classificacao    LIKE ''4.4.5.0.36%''
                                                OR classificacao    LIKE ''4.4.5.0.39%''
                                                OR classificacao    LIKE ''4.4.5.0.41%''
                                                OR classificacao    LIKE ''4.4.5.0.42%''
                                                OR classificacao    LIKE ''4.4.5.0.47%''
                                        THEN ''outros_desembolsos''

                                        WHEN    classificacao       LIKE ''4.6%''
                                        THEN ''amortizacao_refinanciamento_divida''

                                        WHEN    cod_funcao = 28 and cod_subfuncao = 843
                                        THEN    (CASE WHEN classificacao LIKE ''4.6.9.0.73%''
                                            OR classificacao LIKE ''4.6.9.0.74%''
                                            OR classificacao LIKE ''4.6.9.0.75%''
                                                THEN ''subtrai_amortizacao_refinanciamento_divida'' else ''outras_contas'' END)

                                    ELSE ''''
                                    END AS descricao
                                    ,cod_funcao
                                    ,0 as valor
                                    ,pago_per_anterior as valor_anterior
                                FROM orcamento.fn_balancete_despesa('|| quote_literal(stExercicioAnterior) ||'
                                                                    ,'' AND od.cod_entidade IN  ('|| stCodEntidade ||')''
                                                                    ,'|| quote_literal(dtInicialAnterior) ||'
                                                                    ,'|| quote_literal(dtFinalAnterior) ||'
                                                                    ,'''','''','''','''','''' ,'''','''', '''' )
                                as retorno( 
                                    exercicio       char(4),                                                                                
                                    cod_despesa     integer,                                                                                
                                    cod_entidade    integer,                                                                                
                                    cod_programa    integer,                                                                                
                                    cod_conta       integer,                                                                                
                                    num_pao         integer,                                                                                
                                    num_orgao       integer,                                                                                
                                    num_unidade     integer,                                                                                
                                    cod_recurso     integer,                                                                                
                                    cod_funcao      integer,                                                                                
                                    cod_subfuncao   integer,                                                                                
                                    tipo_conta      varchar,                                                                                
                                    vl_original     numeric,                                                                                
                                    dt_criacao      date,                                                                                   
                                    classificacao   varchar,                                                                                
                                    descricao       varchar,                                                                                
                                    num_recurso     varchar,                                                                                
                                    nom_recurso     varchar,                                                                                
                                    nom_orgao       varchar,                                                                                
                                    nom_unidade     varchar,                                                                                
                                    nom_funcao      varchar,                                                                                
                                    nom_subfuncao   varchar,                                                                                
                                    nom_programa    varchar,                                                                                
                                    nom_pao         varchar,                                                                                
                                    empenhado_ano   numeric,                                                                                
                                    empenhado_per   numeric,                                                                                 
                                    anulado_ano     numeric,                                                                                
                                    anulado_per     numeric,                                                                                
                                    pago_ano        numeric,                                                                                
                                    pago_per_anterior numeric,                                                                                 
                                    liquidado_ano   numeric,                                                                                
                                    liquidado_per   numeric,                                                                                
                                    saldo_inicial   numeric,                                                                                
                                    suplementacoes  numeric,                                                                                
                                    reducoes        numeric,                                                                                
                                    total_creditos  numeric,                                                                                
                                    credito_suplementar  numeric,                                                                            
                                    credito_especial  numeric,                                                                            
                                    credito_extraordinario  numeric,                                                                            
                                    num_programa    varchar,
                                    num_acao        varchar
                                    )       

                        )as tabela_valores
                        GROUP BY descricao , cod_funcao 

                ) as resultado
                GROUP BY descricao , cod_funcao , valor, valor_anterior

    ) as tabela_relatorio
    GROUP BY descricao
    ORDER BY descricao
    ';
        
    EXECUTE stSql;

--Criando tabela para armazenar saldos referente ao cod_estrutural
    stSql := ' CREATE TEMPORARY TABLE fluxo_caixa_saldo AS
            SELECT 
                    CASE
                        WHEN 	cod_estrutural like ''1.1.1%''
                                OR cod_estrutural like ''1.1.4%''
                        THEN ''saldo_caixa''
                    END as descricao
                    , sum(vl_saldo_anterior) as saldo_inicial
                    , sum(vl_saldo_atual) as saldo_final
                    , sum(vl_saldo_anterior_anterior) as saldo_inicial_anterior
                    , sum(vl_saldo_atual_anterior) as saldo_final_anterior
                    FROM                                                                                        
                    contabilidade.fn_rl_balancete_verificacao('|| quote_literal(stExercicio) ||'
                                                                ,''cod_entidade IN  ('|| stCodEntidade ||') ''
                                                                ,'|| quote_literal(dtInicial) ||'
                                                                ,'|| quote_literal(dtFinal) ||'
                                                                ,''A''::char)
                        as retorno
                        ( cod_estrutural varchar                                                    
                                    ,nivel integer                                                               
                                    ,nom_conta varchar                                                           
                                    ,cod_sistema integer                                                         
                                    ,indicador_superavit char(12)                                                    
                                    ,vl_saldo_anterior numeric                                                   
                                    ,vl_saldo_debitos  numeric                                                   
                                    ,vl_saldo_creditos numeric                                                   
                                    ,vl_saldo_atual    numeric                                                   
                                    )
                    LEFT JOIN contabilidade.fn_rl_balancete_verificacao('|| quote_literal(stExercicioAnterior) ||'
                                                                ,''cod_entidade IN  ('|| stCodEntidade ||') ''
                                                                ,'|| quote_literal(dtInicialAnterior) ||'
                                                                ,'|| quote_literal(dtFinalAnterior) ||'
                                                                ,''A''::char)
                        as retorno_anterior
                        ( cod_estrutural_anterior              varchar                                                    
                                    ,nivel_anterior                      integer                                                               
                                    ,nom_conta_anterior                  varchar                                                           
                                    ,cod_sistema_anterior                integer                                                         
                                    ,indicador_superavit_anterior        char(12)                                                    
                                    ,vl_saldo_anterior_anterior          numeric                                                   
                                    ,vl_saldo_debitos_anterior           numeric                                                   
                                    ,vl_saldo_creditos_anterior          numeric                                                   
                                    ,vl_saldo_atual_anterior             numeric                                                   
                                    )
                            ON(retorno.cod_estrutural = retorno_anterior.cod_estrutural_anterior)        
                            
                GROUP BY descricao
            ';
        
    EXECUTE stSql;

--Criando tabela para juntar todos os resultados
    stSql :=' CREATE TEMPORARY TABLE resultado_fluxo_caixa AS
                SELECT  descricao
                        ,ABS(valor) as valor
                        ,ABS(valor_anterior) as valor_anterior
                        ,0 as saldo_final
                        ,0 as saldo_inicial_anterior
                        ,0 as saldo_final_anterior
                    FROM fluxo_caixa_receita
                    WHERE descricao <> ''''
            UNION
                SELECT  descricao
                        ,ABS(valor) as valor
                        ,ABS(valor_anterior) as valor_anterior
                        ,0 as saldo_final
                        ,0 as saldo_inicial_anterior
                        ,0 as saldo_final_anterior
                    FROM fluxo_caixa_despesa
                    WHERE descricao <> ''''
            UNION
                SELECT  descricao
                        ,saldo_inicial as valor
                        ,0 as valor_anterior
                        ,saldo_final
                        ,saldo_inicial_anterior
                        ,saldo_final_anterior
                    FROM fluxo_caixa_saldo
                    WHERE descricao <> ''''
            ORDER BY descricao

            ';

    EXECUTE stSql;

--CRIANDO TABELA PARA RESULTADO DO RELATORIO 
    stSql := 'CREATE TEMPORARY TABLE fluxo_valores_descricao
                (
                    ordem               INTEGER
                    ,descricao          VARCHAR
                    ,valor              NUMERIC
                    ,valor_anterior     NUMERIC
                )
        ';
    EXECUTE stSql;
    
    
--CRIANDO DESCRICOES    
    arDescricao[0] := 'FLUXOS DE CAIXA DAS ATIVIDADES DAS OPERAÇÕES';
    arDescricao[1] := 'INGRESSOS';
    arDescricao[2] := 'RECEITAS DERIVADAS';
    arDescricao[3] := 'Receita Tributária';
    arDescricao[4] := 'Receita de Contribuições';
    arDescricao[5] := 'Outras Receitas Derivadas';
    arDescricao[6] := 'RECEITAS ORIGINÁRIAS';
    arDescricao[7] := 'Receita Patrimonial';
    arDescricao[8] := 'Receita Agropecuária';
    arDescricao[9] := 'Receita Industrial';
    arDescricao[10] := 'Receita de Serviços';
    arDescricao[11] := 'Outras Receitas Originárias';
    arDescricao[12] := 'Remuneração das Disponibilidades';
    arDescricao[13] := 'TRANSFERÊNCIAS';
    arDescricao[14] := 'Intergovernamentais';
    arDescricao[15] := 'da União';
    arDescricao[16] := 'de Estados e Distrito Federal';
    arDescricao[17] := 'de Municípios';
    arDescricao[18] := 'Intragovernamentais';
    arDescricao[19] := 'Outras Transferências';
    arDescricao[20] := 'DESEMBOLSOS';
    arDescricao[21] := 'PESSOAL E OUTRAS DESPESAS CORRENTES POR FUNÇÃO';
    arDescricao[22] := 'Legislativa';
    arDescricao[23] := 'Judiciária';
    arDescricao[24] := 'Previdência Social';
    arDescricao[25] := 'Administração';
    arDescricao[26] := 'Defesa Nacional';
    arDescricao[27] := 'Segurança Pública';
    arDescricao[28] := 'Relações Exteriores';
    arDescricao[29] := 'Assistência Social';
    arDescricao[30] := 'Saúde';
    arDescricao[31] := 'Trabalho';
    arDescricao[32] := 'Educação';
    arDescricao[33] := '(...)';
    arDescricao[34] := 'JUROS E ENCARGOS DA DÍVIDA';
    arDescricao[35] := 'Juros e Correção Monetária da Dívida Interna';
    arDescricao[36] := 'Juros e Correção Monetária da Dívida Externa';
    arDescricao[37] := 'Outros Encargos da Dívida';
    arDescricao[38] := 'TRANSFERÊNCIAS';
    arDescricao[39] := 'Intergovernamentais';
    arDescricao[40] := 'da União';
    arDescricao[41] := 'a Estados e Distrito Federal';
    arDescricao[42] := 'a Municípios';
    arDescricao[43] := 'Intragovernamentais';
    arDescricao[44] := 'Outras Transferências';
    arDescricao[45] := 'FLUXO DE CAIXA LÍQUIDO DAS ATIVIDADES DAS OPERAÇÕES';
    arDescricao[46] := 'FLUXOS DE CAIXA DAS ATIVIDADES DE INVESTIMENTO';
    arDescricao[47] := 'INGRESSOS';
    arDescricao[48] := 'ALIENAÇÃO DE BENS';
    arDescricao[49] := 'AMORTIZAÇÃO DE EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS';
    arDescricao[50] := 'DESEMBOLSOS';
    arDescricao[51] := 'AQUISIÇÃO DE ATIVO NÃO CIRCULANTE';
    arDescricao[52] := 'CONCESSÃO DE EMPRÉSTIMOS E FINANCIAMENTOS';
    arDescricao[53] := 'OUTROS DESEMBOLSOS DE INVESTIMENTOS';
    arDescricao[54] := 'FLUXO DE CAIXA LÍQUIDO DAS ATIVIDADES DE INVESTIMENTO';
    arDescricao[55] := 'FLUXOS DE CAIXA DAS ATIVIDADES DE FINANCIAMENTO';
    arDescricao[56] := 'INGRESSOS';
    arDescricao[57] := 'OPERAÇÕES DE CRÉDITO';
    arDescricao[58] := 'DESEMBOLSOS';
    arDescricao[59] := 'AMORTIZAÇÃO/REFINANCIAMENTO DA DÍVIDA';
    arDescricao[60] := 'FLUXO DE CAIXA LÍQUIDO DAS ATIVIDADES DE FINANCIAMENTO';
    arDescricao[61] := 'APURAÇÃO DO FLUXO DE CAIXA DO PERÍODO';
    arDescricao[62] := 'GERAÇÃO LÍQUIDA DE CAIXA E EQUIVALENTE DE CAIXA';
    arDescricao[63] := 'CAIXA E EQUIVALENTE DE CAIXA INICIAL';
    arDescricao[64] := 'CAIXA E EQUIVALENTE DE CAIXA FINAL';
    

       
--CRIANDO RELACOES entres as descricoes a serem exibidas com as do banco de dados   
    arDescricaoAux[0] := '';                                         --FLUXOS DE CAIXA DAS ATIVIDADES DAS OPERAÇÕES
    arDescricaoAux[1] := '';                                         --INGRESSOS
    arDescricaoAux[2] := '';                                         --RECEITAS DERIVADAS
    arDescricaoAux[3] := 'receita_tributaria';                       --Receita Tributária
    arDescricaoAux[4] := 'receita_contribuicoes';                    --Receita de Contribuições
    arDescricaoAux[5] := 'outras_receitas_derivadas';                --Outras Receitas Derivadas
    arDescricaoAux[6] := '';                                         --RECEITAS ORIGINÁRIAS
    arDescricaoAux[7] := 'receita_patrimonial';                      --Receita Patrimonial
    arDescricaoAux[8] := 'receita_agropecuaria';                     --Receita Agropecuária
    arDescricaoAux[9] := 'receita_industrial';                       --Receita Industrial
    arDescricaoAux[10] := 'receita_servicos';                        --Receita de Serviços
    arDescricaoAux[11] := 'outras_receitas_derivadas';             --Outras Receitas Originárias
    arDescricaoAux[12] := 'remuneracao_disponibilidades';            --Remuneração das Disponibilidades
    arDescricaoAux[13] := '';                                        --TRANSFERÊNCIAS
    arDescricaoAux[14] := '';                                        --Intergovernamentais
    arDescricaoAux[15] := 'transferencia_uniao';                     --da União
    arDescricaoAux[16] := 'transferencia_estados_df';                --de Estados e Distrito Federal
    arDescricaoAux[17] := 'transferencia_municipios';                --de Municípios
    arDescricaoAux[18] := '';                                        --Intragovernamentais
    arDescricaoAux[19] := 'outras_transferencias';                   --Outras Transferências
    arDescricaoAux[20] := '';                                        --DESEMBOLSOS
    arDescricaoAux[21] := '';                                        --PESSOAL E OUTRAS DESPESAS CORRENTES POR FUNÇÃO
    arDescricaoAux[22] := 'legislativa';                             --Legislativa
    arDescricaoAux[23] := 'judiciaria';                              --Judiciária
    arDescricaoAux[24] := 'previdencia_social';                      --Previdência Social
    arDescricaoAux[25] := 'administracao';                           --Administração
    arDescricaoAux[26] := 'defesa_nacional';                         --Defesa Nacional
    arDescricaoAux[27] := 'seguranca_publica';                       --Segurança Pública
    arDescricaoAux[28] := 'relacoes_exteriores';                     --Relações Exteriores
    arDescricaoAux[29] := 'assistencia_social';                      --Assistência Social
    arDescricaoAux[30] := 'saude';                                   --Saúde
    arDescricaoAux[31] := 'trabalho';                                --Trabalho
    arDescricaoAux[32] := 'educacao';                                --Educação
    arDescricaoAux[33] := 'etc';                                     --(...)
    arDescricaoAux[34] := '';                                        --JUROS E ENCARGOS DA DÍVIDA
    arDescricaoAux[35] := 'juros_correcao_divida_interna';           --Juros e Correção Monetária da Dívida Interna
    arDescricaoAux[36] := 'juros_correcao_divida_externa';           --Juros e Correção Monetária da Dívida Externa
    arDescricaoAux[37] := 'outros_encargos';                         --Outros Encargos da Dívida
    arDescricaoAux[38] := '';                                        --TRANSFERÊNCIAS
    arDescricaoAux[39] := '';                                        --Intergovernamentais
    arDescricaoAux[40] := 'despesa_transferencia_uniao';             --da União
    arDescricaoAux[41] := 'despesa_transferencia_estado_df';         --a Estados e Distrito Federal
    arDescricaoAux[42] := 'despesa_transferencia_municipios';        --a Municípios
    arDescricaoAux[43] := '';                                        --Intragovernamentais
    arDescricaoAux[44] := 'despesa_outras_transferencias';           --Outras Transferências
    arDescricaoAux[45] := '';                                        --FLUXO DE CAIXA LÍQUIDO DAS ATIVIDADES DAS OPERAÇÕES
    arDescricaoAux[46] := '';                                        --FLUXOS DE CAIXA DAS ATIVIDADES DE INVESTIMENTO
    arDescricaoAux[47] := '';                                        --INGRESSOS
    arDescricaoAux[48] := 'alienacao_bens';                          --ALIENAÇÃO DE BENS
    arDescricaoAux[49] := 'amortizacao_emprestimos_financiamentos_concedidos';--AMORTIZAÇÃO DE EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS
    arDescricaoAux[50] := '';                                        --DESEMBOLSOS
    arDescricaoAux[51] := 'aquisicao_ativo_nao_circulante';          --AQUISIÇÃO DE ATIVO NÃO CIRCULANTE
    arDescricaoAux[52] := 'concessao_emprestimos_financiamentos';    --CONCESSÃO DE EMPRÉSTIMOS E FINANCIAMENTOS
    arDescricaoAux[53] := 'outros_desembolsos';                      --OUTROS DESEMBOLSOS DE INVESTIMENTOS
    arDescricaoAux[54] := '';                                        --FLUXO DE CAIXA LÍQUIDO DAS ATIVIDADES DE INVESTIMENTO
    arDescricaoAux[55] := '';                                        --FLUXOS DE CAIXA DAS ATIVIDADES DE FINANCIAMENTO
    arDescricaoAux[56] := '';                                        --INGRESSOS
    arDescricaoAux[57] := 'operacao_credito';                        --OPERAÇÕES DE CRÉDITO
    arDescricaoAux[58] := '';                                        --DESEMBOLSOS
    arDescricaoAux[59] := 'amortizacao_refinanciamento_divida';      --AMORTIZAÇÃO/REFINANCIAMENTO DA DÍVIDA
    arDescricaoAux[60] := '';                                        --FLUXO DE CAIXA LÍQUIDO DAS ATIVIDADES DE FINANCIAMENTO
    arDescricaoAux[61] := '';                                        --APURAÇÃO DO FLUXO DE CAIXA DO PERÍODO
    arDescricaoAux[62] := '';                                        --GERAÇÃO LÍQUIDA DE CAIXA E EQUIVALENTE DE CAIXA
    
    
--FOR para insert dos valores na descricões certas  
    FOR i IN 0..62 LOOP
        INSERT INTO fluxo_valores_descricao VALUES( i
                                                    ,arDescricao[i]
                                                    ,COALESCE((SELECT valor FROM resultado_fluxo_caixa WHERE descricao = arDescricaoAux[i]),0.00)
                                                    ,COALESCE((SELECT valor_anterior FROM resultado_fluxo_caixa WHERE descricao = arDescricaoAux[i]),0.00)
                                                    );                                       
    END LOOP;
    --INSERT CAIXA INICIAL
    INSERT INTO fluxo_valores_descricao VALUES( 63
                                                ,'CAIXA E EQUIVALENTE DE CAIXA INICIAL'
                                                ,COALESCE((SELECT valor FROM resultado_fluxo_caixa WHERE descricao = 'saldo_caixa'),0.00)
                                                ,COALESCE((SELECT saldo_inicial_anterior FROM resultado_fluxo_caixa WHERE descricao = 'saldo_caixa'),0.00)
                                                );
    --INSERT CAIXA FINAL
    INSERT INTO fluxo_valores_descricao VALUES( 64
                                                ,'CAIXA E EQUIVALENTE DE CAIXA INICIAL'
                                                ,COALESCE((SELECT saldo_final FROM resultado_fluxo_caixa WHERE descricao = 'saldo_caixa'),0.00)
                                                ,COALESCE((SELECT saldo_final_anterior FROM resultado_fluxo_caixa WHERE descricao = 'saldo_caixa'),0.00)
                                                );

--UPDATES PARA CALCULO DE ALGUNS VALORES
    
    --Juros e Correção Monetária da Dívida Interna
    valoresAux :=       
                        COALESCE((SELECT valor from resultado_fluxo_caixa where descricao = 'juros_correcao_divida_interna'),0.00)
                        -
                        COALESCE((SELECT valor from resultado_fluxo_caixa where descricao = 'subtrai_juros_correcao_divida_interna'),0.00) as valor;
    valoresAnteriorAux := 
                        COALESCE((SELECT valor_anterior from resultado_fluxo_caixa where descricao = 'juros_correcao_divida_interna'),0.00)
                        -
                        COALESCE((SELECT valor_anterior from resultado_fluxo_caixa where descricao = 'subtrai_juros_correcao_divida_interna'),0.00) as valor;
    stSql := ' UPDATE fluxo_valores_descricao
                SET     valor = '|| valoresAux ||'
                        ,valor_anterior = '|| valoresAnteriorAux ||'
                WHERE ordem = 35
            ';
    EXECUTE stSql;

    --Outros Encargos da Dívida 
    valoresAux :=       
                        COALESCE((SELECT valor from resultado_fluxo_caixa where descricao = 'outros_encargos'),0.00)
                        -
                        COALESCE((SELECT valor from resultado_fluxo_caixa where descricao = 'subtrai_outros_encargos'),0.00) as valor;
    valoresAnteriorAux := 
                        COALESCE((SELECT valor_anterior from resultado_fluxo_caixa where descricao = 'outros_encargos'),0.00)
                        -
                        COALESCE((SELECT valor_anterior from resultado_fluxo_caixa where descricao = 'subtrai_outros_encargos'),0.00) as valor;
    stSql := ' UPDATE fluxo_valores_descricao
                SET     valor = '|| valoresAux ||'
                        ,valor_anterior = '|| valoresAnteriorAux ||'
                WHERE ordem = 37
            ';
    EXECUTE stSql;
    
    --AMORTIZAÇÃO/REFINANCIAMENTO DA DÍVIDA
    valoresAux :=       
                        COALESCE((SELECT valor from resultado_fluxo_caixa where descricao = 'amortizacao_refinanciamento_divida'),0.00)
                        -
                        COALESCE((SELECT valor from resultado_fluxo_caixa where descricao = 'subtrai_amortizacao_refinanciamento_divida'),0.00) as valor;
    valoresAnteriorAux := 
                        COALESCE((SELECT valor_anterior from resultado_fluxo_caixa where descricao = 'amortizacao_refinanciamento_divida'),0.00)
                        -
                        COALESCE((SELECT valor_anterior from resultado_fluxo_caixa where descricao = 'subtrai_amortizacao_refinanciamento_divida'),0.00) as valor;
    stSql := ' UPDATE fluxo_valores_descricao
                SET     valor = '|| valoresAux ||'
                        ,valor_anterior = '|| valoresAnteriorAux ||'
                WHERE ordem = 59
            ';
    EXECUTE stSql;
    
--UPDATES para agregar os valores na tabela
    --RECEITAS DERIVADAS
            UPDATE fluxo_valores_descricao
            SET	    valor = (select sum(valor) as valor from fluxo_valores_descricao where ordem IN (3,4,5))
                    ,valor_anterior = (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem IN (3,4,5))
            where ordem = 2;
    --RECEITAS ORIGINARIAS
            UPDATE fluxo_valores_descricao
            SET	    valor = (select sum(valor) as valor from fluxo_valores_descricao where ordem IN (7,8,9,10,11,12))
                    ,valor_anterior = (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem IN (7,8,9,10,11,12))
            where ordem = 6;
    --TRANSFERENCIAS do campo RECEITAS
            UPDATE fluxo_valores_descricao
            SET	    valor = (select sum(valor) as valor from fluxo_valores_descricao where ordem IN (15,16,17,19))
                    ,valor_anterior = (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem IN (15,16,17,19))
            where ordem = 13;
    --PESSOAL E OUTRAS DESPESAS
            UPDATE fluxo_valores_descricao
            SET	    valor = (select sum(valor) as valor from fluxo_valores_descricao where ordem IN (22,23,24,25,26,27,28,29,30,31,32,33))
                    ,valor_anterior = (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem IN (22,23,24,25,26,27,28,29,30,31,32,33))
            where ordem = 21;
    --JUROS E ENCARGOS DA DIVIDA
            UPDATE fluxo_valores_descricao
            SET	    valor = (select sum(valor) as valor from fluxo_valores_descricao where ordem IN (35,36,37))
                    ,valor_anterior = (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem IN (35,36,37))
            where ordem = 34;
    --TRANSFERENCIAS do campo DESEMBOLSOS
            UPDATE fluxo_valores_descricao
            SET	    valor = (select sum(valor) as valor from fluxo_valores_descricao where ordem IN (40,41,42,43,44))
                    ,valor_anterior = (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem IN (40,41,42,43,44))
            where ordem = 38;
    --INGRESSOS do campo FLUXOS DE CAIXA DAS ATIVIDADES DE INVESTIMENTO
            UPDATE fluxo_valores_descricao
            SET	    valor = (select sum(valor) as valor from fluxo_valores_descricao where ordem IN (48,49))
                    ,valor_anterior = (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem IN (48,49))
            where ordem = 47;
    --DESEMBOLSOS do campo FLUXOS DE CAIXA DAS ATIVIDADES DE INVESTIMENTO
            UPDATE fluxo_valores_descricao
            SET	    valor = (select sum(valor) as valor from fluxo_valores_descricao where ordem IN (51,52,53))
                    ,valor_anterior = (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem IN (51,52,53))
            where ordem = 50;
    --INGRESSOS do campo FLUXOS DE CAIXA DAS ATIVIDADES DE FINANCIAMENTO 
            UPDATE fluxo_valores_descricao
            SET	    valor = (select sum(valor) as valor from fluxo_valores_descricao where ordem IN (57))
                    ,valor_anterior = (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem IN (57))
            where ordem = 56;
    --DESEMBOLSOS do campo FLUXOS DE CAIXA DAS ATIVIDADES DE FINANCIAMENTO 
            UPDATE fluxo_valores_descricao
            SET	    valor = (select sum(valor) as valor from fluxo_valores_descricao where ordem IN (59))
                    ,valor_anterior = (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem IN (59))
            where ordem = 58;
    --INGRESSOS do campo FLUXOS DE CAIXA DAS ATIVIDADES DAS OPERAÇÕES  
            UPDATE fluxo_valores_descricao
            SET	    valor = (select sum(valor) as valor from fluxo_valores_descricao where ordem IN (2,6,13))
                    ,valor_anterior = (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem IN (2,6,13))
            where ordem = 1;
    --DESEMBOLSOS do campo FLUXOS DE CAIXA DAS ATIVIDADES DAS OPERAÇÕES  
            UPDATE fluxo_valores_descricao
            SET	    valor = (SELECT (select sum(valor) as valor from fluxo_valores_descricao where ordem IN (21,34,38)) )
                    ,valor_anterior = (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem IN (21,34,38))
            where ordem = 20;
    --FLUXO DE CAIXA LÍQUIDO DAS ATIVIDADES DAS OPERAÇÕES
            UPDATE fluxo_valores_descricao
            SET	valor = (
                            SELECT ((select sum(valor) as valor from fluxo_valores_descricao where ordem = 1)
                                    -
                                    (select sum(valor) as valor from fluxo_valores_descricao where ordem = 20)
                                    )
                    )
                    ,valor_anterior = (
                            SELECT ((select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem = 1)
                                    -
                                    (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem = 20)
                            )
                    )
            where ordem = 45;
    --FLUXOS DE CAIXA DAS ATIVIDADES DE INVESTIMENTO
            UPDATE fluxo_valores_descricao
            SET	valor = (
                            SELECT ((select sum(valor) as valor from fluxo_valores_descricao where ordem = 47)
                                    -
                                    (select sum(valor) as valor from fluxo_valores_descricao where ordem = 50)
                                    )
                    )
                    ,valor_anterior = (
                            SELECT ((select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem = 47)
                                    -
                                    (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem = 50)
                            )
                    )
            where ordem = 54;
    --FLUXO DE CAIXA LÍQUIDO DAS ATIVIDADES DE FINANCIAMENTO
            UPDATE fluxo_valores_descricao
            SET	valor = (
                            SELECT ((select sum(valor) as valor from fluxo_valores_descricao where ordem = 56)
                                    -
                                    (select sum(valor) as valor from fluxo_valores_descricao where ordem = 58)
                                    )
                    )
                    ,valor_anterior = (
                            SELECT ((select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem = 56)
                                    -
                                    (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem = 58)
                            )
                    )
            where ordem = 60;
    --GERAÇÃO LÍQUIDA DE CAIXA E EQUIVALENTE DE CAIXA
            UPDATE fluxo_valores_descricao
            SET	    valor = (SELECT (select sum(valor) as valor from fluxo_valores_descricao where ordem IN (45,54,60)) )
                    ,valor_anterior = (select sum(valor_anterior) as valor from fluxo_valores_descricao where ordem IN (45,54,60))
            where ordem = 62;
    
    
    --INSERCAO dos niveis
            ALTER TABLE fluxo_valores_descricao
            ADD COLUMN nivel integer;
            
            UPDATE fluxo_valores_descricao
            SET     nivel = 1
            WHERE ordem IN (0,46,55,61);
            
            UPDATE fluxo_valores_descricao
            SET     nivel = 2
            WHERE ordem IN (1,2,6,13,20,21,34,38,45,47,50,53,56,58,62,63,64);
        
            UPDATE fluxo_valores_descricao
            SET     nivel = 3
            WHERE nivel IS NULL;
            
            UPDATE fluxo_valores_descricao
            SET     nivel = 4
            WHERE ordem IN (62,63,64);
    
    stSql :='SELECT * FROM fluxo_valores_descricao ORDER by ordem';
    
FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN NEXT reRegistro;
END LOOP;

DROP TABLE fluxo_valores_descricao;
DROP TABLE resultado_fluxo_caixa;
DROP TABLE fluxo_caixa_receita;
DROP TABLE fluxo_caixa_despesa;
DROP TABLE fluxo_caixa_saldo;

END;
$$ LANGUAGE 'PLPGSQL';

