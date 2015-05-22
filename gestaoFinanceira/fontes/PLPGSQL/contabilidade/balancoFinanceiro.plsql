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
$Id: balancoFinanceiro.plsql 62477 2015-05-13 17:31:55Z michel $
*/


CREATE OR REPLACE FUNCTION contabilidade.relatorioBalancoFinanceiroRecurso ( VARCHAR,VARCHAR,VARCHAR,VARCHAR,CHAR ) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio             ALIAS FOR $1;
    dtInicial               ALIAS FOR $2;
    dtFinal                 ALIAS FOR $3;
    stCodEntidade           ALIAS FOR $4;
    stTipoDespesa           ALIAS FOR $5;
    stSql                   VARCHAR := '';
    stExercicioAnterior     VARCHAR := ''; 
    dtInicialAnterior       VARCHAR := '';
    dtFinalAnterior         VARCHAR := '';
    stDespesa               VARCHAR := '';
    reRegistro              RECORD;
    reRegistroAux           RECORD;
    arDescricao             VARCHAR[];
    arDescricaoDespesas     VARCHAR[];
    arDescricaoValores      VARCHAR[];
    arDescricaoDespesasValores   VARCHAR[];
    i                       INTEGER;
    totalI                  NUMERIC;
    totalII                 NUMERIC;
    totalIII                NUMERIC;
    totalIV                 NUMERIC;
    totalV                  NUMERIC;
    
BEGIN

stExercicioAnterior     := (to_number(stExercicio,'9999')-1)::varchar;
dtInicialAnterior       := to_char(to_date(dtInicial::text,'dd/mm/yyyy')- interval '1 year','dd/mm/yyyy');
dtFinalAnterior         := to_char(to_date(dtFinal::text,'dd/mm/yyyy')- interval '1 year','dd/mm/yyyy');

--Relacionando colunas das tabelas com o tipo de despesa selecionado no filtro
IF (stTipoDespesa = 'E') THEN
        stDespesa := '(empenhado_per - anulado_per) as valor';
        
        IF (stExercicio >= '2014') THEN
            stDespesa := stDespesa ||', empenhado_per_anterior as valor_anterior';
        END IF;
    
    END IF;
    
    IF (stTipoDespesa = 'L') THEN
        stDespesa := 'liquidado_per as valor';
        
        IF (stExercicio >= '2014') THEN
            stDespesa := stDespesa ||', liquidado_per_anterior as valor_anterior';
        END IF;
    END IF;
    
    IF (stTipoDespesa = 'P') THEN
        stDespesa := 'pago_per as valor';
        
        IF (stExercicio >= '2014') THEN
            stDespesa := stDespesa ||', pago_per_anterior as valor_anterior';
        END IF;
    END IF;

--Criando tabela para armazerar as receitas referente a cada cod_estrutural
    stSql := ' CREATE TEMPORARY TABLE fluxo_caixa_receita AS
            SELECT
                    descricao
                    ,SUM(arrecadado_periodo) as arrecadado_periodo
        ';
    IF(stExercicio >= '2014' )THEN
        stSql := stSql || ',SUM(arrecadado_periodo_anterior) as arrecadado_periodo_anterior';
    END IF;
    
    stSql := stSql || '
            FROM(
                SELECT
                    CASE
                        WHEN descricao = ''redutoras_receita_orcamentaria'' AND recurso like ''0001''
                        THEN ''deducoes_recurso_livre''
                        WHEN descricao = ''redutoras_receita_orcamentaria'' AND recurso NOT LIKE ''0001''
                        THEN ''deducoes_recurso_vinculado''
                ELSE descricao
                END as descricao
                ,SUM(arrecadado_periodo) as arrecadado_periodo
    ';
    
    IF(stExercicio >= '2014' )THEN
        stSql := stSql || ',SUM(arrecadado_periodo_anterior) as arrecadado_periodo_anterior';
    END IF;
    
    stSql := stSql ||'
                FROM(
                    SELECT 
                        CASE
                            WHEN recurso like ''0001''
                            THEN ''recurso_livre''
                            WHEN recurso NOT like ''0001'' AND cod_estrutural NOT like ''9%''
                            THEN ''recurso_vinculado''
                            WHEN cod_estrutural like ''9%''
                            THEN ''redutoras_receita_orcamentaria''
                            WHEN cod_estrutural like ''1.0.0.0%''
                            OR cod_estrutural like ''2.0.0.0%''
                            THEN ''receita_orcamentaria''
                    END as descricao
                    ,recurso
                    ,ABS(arrecadado_periodo) as arrecadado_periodo
            ';
    
    IF(stExercicio >= '2014' )THEN
        stSql := stSql || ',ABS(arrecadado_periodo_anterior) as arrecadado_periodo_anterior';
    END IF;
                    
                    
    stSql := stSql || '                
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
    ';
    IF(stExercicio >= '2014' )THEN
        stSql := stSql || '
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
                
            ';
    END IF;
    stSql := stSql || '
                ) as tbl
                WHERE descricao IS NOT NULL
		GROUP BY descricao,recurso
            ) resultado
            GROUP BY descricao
    ';
        
    EXECUTE stSql;

--Criando tabela para armazenar despesas referente a sua classificao para calculo futuro
    stSql := ' CREATE TEMPORARY TABLE tmp_calculo_despesas AS
            SELECT
                    classificacao            
                    ,num_recurso        
                    ,'|| stDespesa ||'
                    ,empenhado_ano
                    ,anulado_ano
                    ,liquidado_ano
                    ,pago_ano
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
        ';

    IF(stExercicio::integer >= 2014 )THEN
        stSql := stSql || '
                            LEFT JOIN orcamento.fn_balancete_despesa('|| quote_literal(stExercicioAnterior) ||'
                                                                    ,'' AND od.cod_entidade IN  ('|| stCodEntidade ||')''
                                                                    ,'|| quote_literal(dtInicialAnterior) ||'
                                                                    ,'|| quote_literal(dtFinalAnterior) ||'
                                                                    ,'''','''','''','''','''' ,'''','''', '''' )
                                as retorno_anterior( 
                                    exercicio_anterior       char(4),                                                                                
                                    cod_despesa_anterior     integer,                                                                                
                                    cod_entidade_anterior    integer,                                                                                
                                    cod_programa_anterior    integer,                                                                                
                                    cod_conta_anterior       integer,                                                                                
                                    num_pao_anterior         integer,                                                                                
                                    num_orgao_anterior       integer,                                                                                
                                    num_unidade_anterior     integer,                                                                                
                                    cod_recurso_anterior     integer,                                                                                
                                    cod_funcao_anterior      integer,                                                                                
                                    cod_subfuncao_anterior   integer,                                                                                
                                    tipo_conta_anterior      varchar,                                                                                
                                    vl_original_anterior     numeric,                                                                                
                                    dt_criacao_anterior      date,                                                                                   
                                    classificacao_anterior   varchar,                                                                                
                                    descricao_anterior       varchar,                                                                                
                                    num_recurso_anterior     varchar,                                                                                
                                    nom_recurso_anterior     varchar,                                                                                
                                    nom_orgao_anterior       varchar,                                                                                
                                    nom_unidade_anterior     varchar,                                                                                
                                    nom_funcao_anterior      varchar,                                                                                
                                    nom_subfuncao_anterior   varchar,                                                                                
                                    nom_programa_anterior    varchar,                                                                                
                                    nom_pao_anterior         varchar,                                                                                
                                    empenhado_ano_anterior   numeric,                                                                                
                                    empenhado_per_anterior   numeric,                                                                                 
                                    anulado_ano_anterior     numeric,                                                                                
                                    anulado_per_anterior     numeric,                                                                                
                                    pago_ano_anterior        numeric,                                                                                
                                    pago_per_anterior        numeric,                                                                                 
                                    liquidado_ano_anterior   numeric,                                                                                
                                    liquidado_per_anterior   numeric,                                                                                
                                    saldo_inicial_anterior   numeric,                                                                                
                                    suplementacoes_anterior  numeric,                                                                                
                                    reducoes_anterior        numeric,                                                                                
                                    total_creditos_anterior  numeric,                                                                                
                                    credito_suplementar_anterior  numeric,                                                                            
                                    credito_especial_anterior  numeric,                                                                            
                                    credito_extraordinario_anterior  numeric,
                                    num_programa    varchar,
                                    num_acao        varchar
                                    )
                                ON(retorno.classificacao = retorno_anterior.classificacao_anterior)
    ';
    END IF;
    
EXECUTE stSql;

--INSERT para separar as despesas orcamentarias por sua classificacao.
    IF (stExercicio::integer >= 2014) THEN
        INSERT INTO tmp_calculo_despesas(classificacao,valor,valor_anterior) VALUES('despesas_orcamentarias'
                                                                                    ,(SELECT sum(valor) as valor
                                                                                            FROM tmp_calculo_despesas
                                                                                            WHERE  classificacao like '3%'
                                                                                            OR classificacao like '4%')
                                                                                    ,(SELECT sum(valor_anterior) as valor_anterior
                                                                                            FROM tmp_calculo_despesas
                                                                                            WHERE  classificacao like '3%'
                                                                                            OR classificacao like '4%')
                                                                                    );
    
    
    ELSE
        INSERT INTO tmp_calculo_despesas(classificacao,valor) VALUES('despesas_orcamentarias'
                                                                    , (SELECT sum(valor) as valor
                                                                             FROM tmp_calculo_despesas
                                                                             WHERE  classificacao like '3%'
                                                                             OR classificacao like '4%')
                                                                    );
    END IF;


    IF (stTipoDespesa = 'E') THEN
    --INSERT para colocar inscricao_restos_pagar_processados e inscricao_restos_pagar_nao_processados
        INSERT INTO tmp_calculo_despesas(classificacao,valor) VALUES('inscricao_restos_pagar_processados'
                                                                    , (SELECT 
                                                                            ( 
                                                                            SUM(liquidado_ano) - SUM(pago_ano) 
                                                                            )
                                                                        FROM tmp_calculo_despesas)
                                                                    );
        INSERT INTO tmp_calculo_despesas(classificacao,valor) VALUES('inscricao_restos_pagar_nao_processados'
                                                                    , (SELECT 
                                                                            ( 
                                                                            (SUM(empenhado_ano) - SUM(anulado_ano)) - SUM(liquidado_ano)
                                                                            )
                                                                        FROM tmp_calculo_despesas)
                                                                    );
    END IF;
        
    IF (stTipoDespesa = 'L') THEN
    --INSERT para colocar inscricao_restos_pagar_processados
        INSERT INTO tmp_calculo_despesas(classificacao,valor) VALUES('inscricao_restos_pagar_processados'
                                                                    , (SELECT 
                                                                            ( 
                                                                            SUM(liquidado_ano) - SUM(pago_ano) 
                                                                            )
                                                                        FROM tmp_calculo_despesas)
                                                                    );
    END IF;

--CRIANDO TABELA PARA DESPESAS a partir da tmp_calculo_despesas
    stSql := ' CREATE TEMPORARY TABLE tmp_despesas AS
                SELECT 
                    CASE
                            WHEN num_recurso like ''0001''
                            THEN ''despesa_recurso_livre''
                            WHEN num_recurso not like ''0001''
                            THEN ''despesa_recurso_vinculado''
                            WHEN classificacao = ''despesas_orcamentarias''
                            THEN ''despesas_orcamentarias''
                            WHEN classificacao = ''inscricao_restos_pagar_processados''
                            THEN ''inscricao_restos_pagar_processados''
                            WHEN classificacao = ''inscricao_restos_pagar_nao_processados''
                            THEN ''inscricao_restos_pagar_nao_processados''
                    END as descricao
                    ,SUM(valor) as valor
    ';
    IF (stExercicio::integer >= 2014) THEN
        stSql := stSql || ',SUM(valor_anterior) as valor_anterior';
    END IF;
    stSql := stSql || '
                FROM tmp_calculo_despesas
                GROUP BY descricao
    ';
    EXECUTE stSql;
    
    
--Criando tabela para armazenar saldos referente ao cod_estrutural
    stSql := ' CREATE TEMPORARY TABLE fluxo_caixa_saldo AS
            SELECT 
            CASE
                WHEN cod_estrutural         like ''1.1.3%'' AND indicador_superavit = ''financeiro''
                    THEN ''depositos_restituiveis_valores_vinculados''
                WHEN cod_estrutural         like ''2.1.8%'' AND indicador_superavit = ''financeiro''
                    THEN ''valores_restituiveis''
                WHEN cod_estrutural         like ''1.1.1.0%''
                    THEN ''caixa_equivalentes''
                WHEN cod_estrutural          like ''4.5.1.1.0%''
                    THEN ''transferencias_recebidas_orcamentaria''
                WHEN cod_estrutural          like ''3.5.1.1.0%''
                    THEN ''tranferencias_concedidas_orcamentaria''
                WHEN cod_estrutural          like ''4.5.1.2.0%''                        
                    THEN ''transferencias_recebidas_independentes_orcamentaria''
                WHEN cod_estrutural          like ''3.5.1.2.0%''                        
                    THEN ''transferencias_concedidas_independentes_orcamentaria''
                WHEN cod_estrutural          like ''4.5.1.3.0%''
                    THEN ''transferencias_recebidas_cobertura''
                WHEN cod_estrutural          like ''3.5.1.3%''
                    THEN ''transferencias_concedidas_cobertura''
                WHEN cod_estrutural          like ''6.3.2.2.0%''
                    THEN ''pagamento_restos_pagar_processados''
                WHEN cod_estrutural          like ''6.3.1.4.0%''
                    THEN ''pagamento_restos_pagar_nao_processados''
            END as descricao
            ,CASE WHEN (select count(cod_lote) as lotes from contabilidade.lote where exercicio = '|| quote_literal(stExercicioAnterior) ||') > 0 THEN
                            ABS(sum(vl_saldo_anterior))
                  ELSE      0.00
             END AS vl_saldo_anterior
            ,ABS(sum(vl_saldo_debitos)) as vl_saldo_debitos
            ,ABS(sum(vl_saldo_creditos)) as vl_saldo_creditos
            ,ABS(sum(vl_saldo_atual)) as vl_saldo_atual
            FROM contabilidade.fn_rl_balancete_verificacao('|| quote_literal(stExercicio) ||'
                                                                ,''cod_entidade IN  ('|| stCodEntidade ||') ''
                                                                ,'|| quote_literal(dtInicial) ||'
                                                                ,'|| quote_literal(dtFinal) ||'
                                                                ,''A''::CHAR)
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
                GROUP BY descricao
    ';

EXECUTE stSql;

--SELECT para armazenar saldos referente ao cod_estrutural relativos às Transferências, pois não podem possuir histórico = 8
--Feito o update na tabela fluxo_caixa_saldo para armazenar todos os saldos corretamente

stSql := 'SELECT 
            CASE
		WHEN cod_estrutural          like ''4.5.1.1.0%''
                    THEN ''transferencias_recebidas_orcamentaria''
                WHEN cod_estrutural          like ''3.5.1.1.0%''
                    THEN ''tranferencias_concedidas_orcamentaria''
                WHEN cod_estrutural          like ''4.5.1.2.0%''                        
                    THEN ''transferencias_recebidas_independentes_orcamentaria''
                WHEN cod_estrutural          like ''3.5.1.2.0%''                        
                    THEN ''transferencias_concedidas_independentes_orcamentaria''
                WHEN cod_estrutural          like ''4.5.1.3.0%''
                    THEN ''transferencias_recebidas_cobertura''
                WHEN cod_estrutural          like ''3.5.1.3%''
                    THEN ''transferencias_concedidas_cobertura''
                WHEN cod_estrutural          like ''6.3.2.2.0%''
                    THEN ''pagamento_restos_pagar_processados''
                WHEN cod_estrutural          like ''6.3.1.4.0%''
                    THEN ''pagamento_restos_pagar_nao_processados''
            END as descricao
            ,ABS(sum(vl_saldo_anterior)) as vl_saldo_anterior
            ,ABS(sum(vl_saldo_debitos)) as vl_saldo_debitos
            ,ABS(sum(vl_saldo_creditos)) as vl_saldo_creditos
            ,ABS(sum(vl_saldo_atual)) as vl_saldo_atual
            FROM contabilidade.fn_rl_balancete_verificacao_transferencias('|| quote_literal(stExercicio) ||'
                                                                ,''cod_entidade IN  ('|| stCodEntidade ||') ''
                                                                ,'|| quote_literal(dtInicial) ||'
                                                                ,'|| quote_literal(dtFinal) ||'
                                                                ,''A''::CHAR)
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
            WHERE cod_estrutural SIMILAR TO ''4.5.1.1.0%|3.5.1.1.0%|4.5.1.2.0%|3.5.1.2.0%|4.5.1.3.0%|3.5.1.3%|6.3.2.2.0%|6.3.1.4.0%''
                GROUP BY descricao
    ';

FOR reRegistroAux IN EXECUTE stSql
LOOP
    UPDATE fluxo_caixa_saldo SET vl_saldo_anterior = reRegistroAux.vl_saldo_anterior,
                                 vl_saldo_debitos = reRegistroAux.vl_saldo_debitos,
                                 vl_saldo_creditos = reRegistroAux.vl_saldo_creditos,
                                 vl_saldo_atual = reRegistroAux.vl_saldo_atual
                            WHERE descricao = reRegistroAux.descricao;
END LOOP;


--Criando tabela para juntar todos os resultados
IF (stExercicio::integer >= 2014) THEN
    stSql :=' CREATE TEMPORARY TABLE resultado_financeiro AS
            SELECT * FROM(
                            SELECT 
                                    descricao
                                    ,vl_saldo_anterior 	        as valor_anterior
                                    ,vl_saldo_debitos 	        as valor_debito 
                                    ,vl_saldo_creditos	        as valor_credito
                                    ,vl_saldo_atual		as valor
                                    FROM fluxo_caixa_saldo
                    UNION
                            SELECT 
                                    descricao
                                    ,arrecadado_periodo_anterior as valor_anterior
                                    ,0		 		 as valor_debito 
                                    ,0				 as valor_credito
                                    ,arrecadado_periodo 	 as valor 
                                    FROM fluxo_caixa_receita
                    UNION
                            SELECT 
                                    descricao
                                    ,valor_anterior     as valor_anterior
                                    ,0		 	as valor_debito 
                                    ,0			as valor_credito
                                    ,valor 
                                    FROM tmp_despesas
                    )as tbl
            WHERE descricao <> ''''
            ORDER BY descricao
            ';
    EXECUTE stSql;
    
ELSE
    stSql :=' CREATE TEMPORARY TABLE resultado_financeiro AS
            SELECT * FROM(
                            SELECT 
                                    descricao
                                    ,vl_saldo_anterior 	as valor_anterior
                                    ,vl_saldo_debitos 	as valor_debito 
                                    ,vl_saldo_creditos	as valor_credito
                                    ,vl_saldo_atual		as valor
                                    FROM fluxo_caixa_saldo
                    UNION
                            SELECT 
                                    descricao
                                    ,0 				as valor_anterior
                                    ,0		 		as valor_debito 
                                    ,0				as valor_credito
                                    ,arrecadado_periodo 	as valor 
                                    FROM fluxo_caixa_receita
                    UNION
                            SELECT 
                                    descricao
                                    ,0 			as valor_anterior
                                    ,0		 	as valor_debito 
                                    ,0			as valor_credito
                                    ,valor 
                                    FROM tmp_despesas
                    )as tbl
            WHERE descricao <> ''''
            ORDER BY descricao
            ';
    EXECUTE stSql;
END IF;

--UPDATE para ajustar valores de acordo com a regra de negocio
    --Somando a Movimentacao dos restos a pagar
    UPDATE resultado_financeiro
        SET valor = (SELECT CASE WHEN (valor_debito - valor_credito) < 0.00 THEN (valor_debito - valor_credito) * -1
                                 ELSE (valor_debito - valor_credito)
                            END AS valor 
                       FROM resultado_financeiro 
                      WHERE descricao = 'pagamento_restos_pagar_processados')
    WHERE descricao = 'pagamento_restos_pagar_processados';

--CRIANDO TABELA PARA RESULTADO DO RELATORIO 
    stSql := 'CREATE TEMPORARY TABLE relatorio_financeiro
                (
                    ordem                           INTEGER
                    ,descricao_ingressos            VARCHAR
                    ,valor_ingresso                 NUMERIC
                    ,valor_ingresso_anterior        NUMERIC
                    ,descricao_dispendios           VARCHAR
                    ,valor_dispendios               NUMERIC
                    ,valor_dispendios_anterior      NUMERIC
                )
        '; 
    EXECUTE stSql;
    
    
--CRIANDO DESCRICOES
    --RECEITAS POR RECURSO
    arDescricao[0] := 'Receita Orçamentária(I)';
    arDescricao[1] := '';
    arDescricao[2] := 'Ordinária';
    arDescricao[3] := 'Vinculada';
    arDescricao[4] := '';
    arDescricao[5] := '';
    arDescricao[6] := '(-) Deduções da Receita Orçamentária';
    arDescricao[7] := 'Ordinária';
    arDescricao[8] := 'Vinculada';
    arDescricao[9] := '';
    arDescricao[10] := '';
    arDescricao[11] := 'Transferências Financeiras Recebidas (II)';
    arDescricao[12] := 'Transferências Recebidas para a Execução Orçamentária';
    arDescricao[13] := 'Transferências Recebidas Independentes de Execução Orçamentária - Inter OFSS';
    arDescricao[14] := 'Transferências Recebidas para Cobertura do Déficit  Financeiro do RPPS';
    arDescricao[15] := '';
    arDescricao[16] := '';
    arDescricao[17] := 'Recebimentos Extra-Orçamentários (III)';
    arDescricao[18] := 'Inscrição de Restos a Pagar Processados';
    arDescricao[19] := 'Inscrição de Restos a Pagar Não Processados';
    arDescricao[20] := 'Depósitos Restituíveis e Valores Vinculados';
    arDescricao[21] := 'Valores Restituiveis';
    arDescricao[22] := '';
    arDescricao[23] := 'Saldo em Espécie do Exercício Anterior (IV)';
    arDescricao[24] := 'Caixa e Equivalentes de Caixa';
    arDescricao[25] := 'Depósitos Restituíveis e Valores Vinculados';
    arDescricao[26] := 'Outros Recebimentos';
    arDescricao[27] := '';
    arDescricao[28] := 'TOTAL (V) = (I+II+III+IV)';
    
    --DESPESAS POR RECURSO
    arDescricaoDespesas[0] := 'Despesa Orçamentária(VI)';
    arDescricaoDespesas[1] := '';
    arDescricaoDespesas[2] := 'Ordinária';
    arDescricaoDespesas[3] := 'Vinculada';
    arDescricaoDespesas[4] := '';
    arDescricaoDespesas[5] := '';
    arDescricaoDespesas[6] := '';
    arDescricaoDespesas[7] := '';
    arDescricaoDespesas[8] := '';
    arDescricaoDespesas[9] := '';
    arDescricaoDespesas[10] := '';
    arDescricaoDespesas[11] := 'Transferências Financeiras Concedidas (VII)';
    arDescricaoDespesas[12] := 'Tranferências Concedidas para a Execução Orçamentária';
    arDescricaoDespesas[13] := 'Tranferências Concedidas Independentes de Execução Orçamentária';
    arDescricaoDespesas[14] := 'Transferências Concedidas para Cobertura do Déficit Financeiro do RPPS';
    arDescricaoDespesas[15] := '';
    arDescricaoDespesas[16] := '';
    arDescricaoDespesas[17] := 'Pagamentos Extra-Orçamentários (VIII)';
    arDescricaoDespesas[18] := 'Pagamentos de Restos a Pagar Processados';
    arDescricaoDespesas[19] := 'Pagamentos de Restos a Pagar Não Processados';
    arDescricaoDespesas[20] := 'Depósitos Restituíveis e Valores Vinculados';
    arDescricaoDespesas[21] := 'Valores Restituiveis';
    arDescricaoDespesas[22] := '';
    arDescricaoDespesas[23] := 'Saldo em Espécie para o Exercício Seguinte (IX)';
    arDescricaoDespesas[24] := 'Caixa e Equivalentes de Caixa';
    arDescricaoDespesas[25] := 'Depósitos Restituíveis e Valores Vinculados';
    arDescricaoDespesas[26] := 'Outros Recebimentos';
    arDescricaoDespesas[27] := '';
    arDescricaoDespesas[28] := 'TOTAL (X) = (VI+VII+VIII+IX)';
    
    --Armazenar valores da tabela resultado_financeiro em um array de acordo com a regra pra serem inseridos na tabela relatorio_financeiro
    arDescricaoValores[0] := 'receita_orcamentaria';
    arDescricaoValores[1] := '';
    arDescricaoValores[2] := 'recurso_livre';
    arDescricaoValores[3] := 'recurso_vinculado';
    arDescricaoValores[4] := '';
    arDescricaoValores[5] := '';
    arDescricaoValores[6] := '';
    arDescricaoValores[7] := 'deducoes_recurso_livre';
    arDescricaoValores[8] := 'deducoes_recurso_vinculado';
    arDescricaoValores[9] := '';
    arDescricaoValores[10] := '';
    arDescricaoValores[11] := '';
    arDescricaoValores[12] := 'transferencias_recebidas_orcamentaria';
    arDescricaoValores[13] := 'transferencias_recebidas_independentes_orcamentaria';
    arDescricaoValores[14] := 'transferencias_recebidas_cobertura';
    arDescricaoValores[15] := '';
    arDescricaoValores[16] := '';
    arDescricaoValores[17] := '';
    arDescricaoValores[18] := 'inscricao_restos_pagar_processados';
    arDescricaoValores[19] := 'inscricao_restos_pagar_nao_processados';
    arDescricaoValores[20] := 'depositos_restituiveis_valores_vinculados';
    arDescricaoValores[21] := 'valores_restituiveis';
    arDescricaoValores[22] := '';
    arDescricaoValores[23] := '';
    arDescricaoValores[24] := 'caixa_equivalentes';
    arDescricaoValores[25] := 'depositos_restituiveis_valores_vinculados';
    arDescricaoValores[26] := 'outros_recebimentos';
    arDescricaoValores[27] := '';
    arDescricaoValores[28] := '';
    
    
    --DESCRICAO DOS CAMPOS PARA CRIAR A RELACAO ENTRE AS TABELAS DESPESAS
    arDescricaoDespesasValores[0] := 'despesas_orcamentarias';
    arDescricaoDespesasValores[1] := '';
    arDescricaoDespesasValores[2] := 'despesa_recurso_livre';
    arDescricaoDespesasValores[3] := 'despesa_recurso_vinculado';
    arDescricaoDespesasValores[4] := '';
    arDescricaoDespesasValores[5] := '';
    arDescricaoDespesasValores[6] := '';
    arDescricaoDespesasValores[7] := '';
    arDescricaoDespesasValores[8] := '';
    arDescricaoDespesasValores[9] := '';
    arDescricaoDespesasValores[10] := '';
    arDescricaoDespesasValores[11] := '';
    arDescricaoDespesasValores[12] := 'tranferencias_concedidas_orcamentaria';
    arDescricaoDespesasValores[13] := 'transferencias_concedidas_independentes_orcamentaria';
    arDescricaoDespesasValores[14] := 'transferencias_concedidas_cobertura';
    arDescricaoDespesasValores[15] := '';
    arDescricaoDespesasValores[16] := '';
    arDescricaoDespesasValores[17] := '';
    arDescricaoDespesasValores[18] := 'pagamento_restos_pagar_processados';
    arDescricaoDespesasValores[19] := 'pagamento_restos_pagar_nao_processados';
    arDescricaoDespesasValores[20] := 'depositos_restituiveis_valores_vinculados';
    arDescricaoDespesasValores[21] := 'valores_restituiveis';
    arDescricaoDespesasValores[22] := '';
    arDescricaoDespesasValores[23] := '';
    arDescricaoDespesasValores[24] := 'caixa_equivalentes';
    arDescricaoDespesasValores[25] := 'depositos_restituiveis_valores_vinculados';
    arDescricaoDespesasValores[26] := 'outros_recebimentos';
    arDescricaoDespesasValores[27] := '';
    arDescricaoDespesasValores[28] := '';


--INSERIR Descricoes na Tabela
    FOR i IN 0..28 LOOP
        INSERT INTO relatorio_financeiro(   ordem                           
                                            ,descricao_ingressos            
                                            ,valor_ingresso                
                                            ,valor_ingresso_anterior        
                                            ,descricao_dispendios           
                                            ,valor_dispendios               
                                            ,valor_dispendios_anterior)
                                                                        VALUES( i
                                                                                ,arDescricao[i]
                                                                                ,COALESCE((SELECT valor FROM resultado_financeiro WHERE descricao = arDescricaoValores[i]),0.00)
                                                                                ,COALESCE((SELECT valor_anterior FROM resultado_financeiro WHERE descricao = arDescricaoValores[i]),0.00)
                                                                                ,arDescricaoDespesas[i]
                                                                                ,COALESCE((SELECT valor FROM resultado_financeiro WHERE descricao = arDescricaoDespesasValores[i]),0.00)
                                                                                ,COALESCE((SELECT valor_anterior FROM resultado_financeiro WHERE descricao = arDescricaoDespesasValores[i]),0.00)
                                        );
    END LOOP;

--UPDATE para inserir os valores de acordo com a regra de negocio.
    --Passando valor para o valor_anterior para ficar de acordo com a regra da conta
    UPDATE relatorio_financeiro
        SET valor_ingresso = (SELECT valor_ingresso_anterior FROM relatorio_financeiro WHERE ordem = 26)
    WHERE ordem = 26;  

    UPDATE relatorio_financeiro
    SET valor_ingresso = COALESCE((SELECT valor_debito FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
        ,valor_ingresso_anterior = COALESCE((SELECT valor_anterior FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
    WHERE ordem = 20;
    
    UPDATE relatorio_financeiro
    SET valor_dispendios = COALESCE((SELECT valor_credito FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
        ,valor_dispendios_anterior = COALESCE((SELECT valor_anterior FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
    WHERE ordem = 20;
    
    UPDATE relatorio_financeiro
    SET valor_ingresso = COALESCE((SELECT valor_anterior FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
    WHERE ordem = 25;
    
    UPDATE relatorio_financeiro
    SET valor_dispendios = COALESCE((SELECT valor FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
        ,valor_dispendios_anterior = COALESCE((SELECT valor_anterior FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
    WHERE ordem = 25;
    
    UPDATE relatorio_financeiro
    SET valor_ingresso = (SELECT valor_credito FROM resultado_financeiro WHERE descricao = 'valores_restituiveis')
        ,valor_ingresso_anterior = (SELECT valor_anterior FROM resultado_financeiro WHERE descricao = 'valores_restituiveis')
    WHERE ordem = 21;
    
    UPDATE relatorio_financeiro
    SET valor_dispendios = (SELECT valor_debito FROM resultado_financeiro WHERE descricao = 'valores_restituiveis')
        ,valor_dispendios_anterior = (SELECT valor_anterior FROM resultado_financeiro WHERE descricao = 'valores_restituiveis')
    WHERE ordem = 21; 
    
    UPDATE relatorio_financeiro
    SET valor_ingresso = (SELECT valor_anterior FROM resultado_financeiro WHERE descricao = 'caixa_equivalentes')
    WHERE ordem = 24; 
    
    UPDATE relatorio_financeiro
    SET valor_dispendios = (SELECT valor FROM resultado_financeiro WHERE descricao = 'caixa_equivalentes')
        ,valor_dispendios_anterior = (SELECT valor FROM resultado_financeiro WHERE descricao = 'caixa_equivalentes')
    WHERE ordem = 24
    AND (select count(cod_lote) as lotes from contabilidade.lote where exercicio = stExercicioAnterior) > 0; 
    
     --Adicionar Somatorio das deduções de receita orcamentarias
    UPDATE relatorio_financeiro
    SET
    valor_ingresso = (SELECT SUM(valor_ingresso)FROM relatorio_financeiro where ordem IN (7,8))
    ,valor_ingresso_anterior = (SELECT SUM(valor_ingresso_anterior) as valor FROM relatorio_financeiro where ordem IN (7,8))
    WHERE ordem IN (6);
    
    --Receitas Orcamentarias 1.0.0.0 + 2.0.0.0 + 7.0.0.0 - 9.0.0.0
    UPDATE relatorio_financeiro
    SET	    valor_ingresso = ( 	(SELECT SUM(valor_ingresso)as valor_ingresso FROM relatorio_financeiro WHERE ordem IN (2,3)) 
				-
				(SELECT valor_ingresso FROM relatorio_financeiro WHERE ordem IN (6))
                            )
            ,valor_ingresso_anterior = (    (SELECT SUM(valor_ingresso_anterior)as valor_ingresso_anterior FROM relatorio_financeiro WHERE ordem IN (2,3)) 
                                            -
                                            (SELECT valor_ingresso_anterior FROM relatorio_financeiro WHERE ordem IN (6))
                                        )
    WHERE ordem = 0;

--CALCULANDO OS TOTAIS DO EXERCICIO ATUAL dos INGRESSOS E FAZENDO UPDATE NA TABELA DO RELATORIO
    totalI  := (SELECT SUM(valor_ingresso) as valor_ingresso FROM relatorio_financeiro where ordem in (0));
    totalII := (SELECT SUM(valor_ingresso) as valor_ingresso FROM relatorio_financeiro where ordem in (12,13,14));
    totalIII:= (SELECT SUM(valor_ingresso) as valor_ingresso FROM relatorio_financeiro where ordem in (18,19,20,21));
    totalIV := (SELECT SUM(valor_ingresso) as valor_ingresso FROM relatorio_financeiro where ordem in (24,25,26));
    totalV  := totalI + totalII + totalIII + totalIV;

    UPDATE relatorio_financeiro
    SET valor_ingresso = totalV
    WHERE ordem  = 28;

--CALCULANDO OS TOTAIS DO EXERCICIO ANTERIOR dos INGRESSOS E FAZENDO UPDATE NA TABELA DO RELATORIO
    totalI  := (SELECT SUM(valor_ingresso_anterior) as valor_ingresso FROM relatorio_financeiro where ordem in (0));
    totalII := (SELECT SUM(valor_ingresso_anterior) as valor_ingresso FROM relatorio_financeiro where ordem in (12,13,14));
    totalIII:= (SELECT SUM(valor_ingresso_anterior) as valor_ingresso FROM relatorio_financeiro where ordem in (18,19,20,21));
    totalIV := (SELECT SUM(valor_ingresso_anterior) as valor_ingresso FROM relatorio_financeiro where ordem in (24,25,26));
    totalV  := totalI + totalII + totalIII + totalIV;
    
    UPDATE relatorio_financeiro
    SET valor_ingresso_anterior = totalV
    WHERE ordem  = 28;

--CALCULANDO OS TOTAIS DO EXERCICIO ATUAL dos DISPENDIOS E FAZENDO UPDATE NA TABELA DO RELATORIO
    totalI  := (SELECT SUM(valor_dispendios) as valor_ingresso FROM relatorio_financeiro where ordem in (0));
    totalII := (SELECT SUM(valor_dispendios) as valor_ingresso FROM relatorio_financeiro where ordem in (12,13,14));
    totalIII:= (SELECT SUM(valor_dispendios) as valor_ingresso FROM relatorio_financeiro where ordem in (18,19,20,21));
    totalIV := (SELECT SUM(valor_dispendios) as valor_ingresso FROM relatorio_financeiro where ordem in (24,25,26));
    totalV  := totalI + totalII + totalIII + totalIV;
    
    UPDATE relatorio_financeiro
    SET valor_dispendios = totalV
    WHERE ordem  = 28;

--CALCULANDO OS TOTAIS DO EXERCICIO ANTERIOR dos DISPENDIOS E FAZENDO UPDATE NA TABELA DO RELATORIO
    totalI  := (SELECT SUM(valor_dispendios_anterior) as valor_ingresso FROM relatorio_financeiro where ordem in (0));
    totalII := (SELECT SUM(valor_dispendios_anterior) as valor_ingresso FROM relatorio_financeiro where ordem in (12,13,14));
    totalIII:= (SELECT SUM(valor_dispendios_anterior) as valor_ingresso FROM relatorio_financeiro where ordem in (18,19,20,21));
    totalIV := (SELECT SUM(valor_dispendios_anterior) as valor_ingresso FROM relatorio_financeiro where ordem in (24,25,26));
    totalV  := totalI + totalII + totalIII + totalIV;
    
    UPDATE relatorio_financeiro
    SET valor_dispendios_anterior = totalV
    WHERE ordem  = 28;
    
--TRANTANDO COLUNAS PARA FICAR EM BRANCO
    UPDATE relatorio_financeiro
    SET
    valor_ingresso = null
    ,valor_ingresso_anterior = null
    WHERE descricao_ingressos = '';

    UPDATE relatorio_financeiro
    SET
    valor_dispendios = null
    ,valor_dispendios_anterior = null
    WHERE descricao_dispendios = '';
    
    UPDATE relatorio_financeiro
    SET
    valor_ingresso = null
    ,valor_ingresso_anterior = null
    ,valor_dispendios = null
    ,valor_dispendios_anterior = null
    WHERE ordem in (11,17,23);
    
stSql :='SELECT * FROM relatorio_financeiro ORDER by ordem';
    
FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN NEXT reRegistro;
END LOOP;

DROP TABLE tmp_despesas;
DROP TABLE relatorio_financeiro;
DROP TABLE resultado_financeiro;
DROP TABLE fluxo_caixa_receita;
DROP TABLE tmp_calculo_despesas;
DROP TABLE fluxo_caixa_saldo;

END;
$$ LANGUAGE 'PLPGSQL';

