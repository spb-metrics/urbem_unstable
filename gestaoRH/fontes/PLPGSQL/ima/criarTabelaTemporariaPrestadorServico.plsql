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
/* recuperar_dirf_prestadores_servico
 * 
 * Data de Criação : 23/01/2009


 * @author Analista : Dagiane   
 * @author Desenvolvedor : Rafael Garbin
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION criar_tabela_temporaria_prestador_servico(VARCHAR, INTEGER, INTEGER) RETURNS BOOLEAN AS $$
DECLARE
    stEntidade          ALIAS FOR $1;
    inExercicio         ALIAS FOR $2;    
    inCodEntidade       ALIAS FOR $3;    
    stSql               VARCHAR := '';
BEGIN

     stSql := ' CREATE TEMPORARY TABLE tmp_prestador_servico AS (
                SELECT * FROM (
                SELECT REPLACE(sw_cgm.nom_cgm,''–'',''-'') as nom_cgm
                        , sw_cgm.numcgm
                        , sw_cgm_pessoa_fisica.cpf as beneficiario
                        , ( CASE WHEN trim(configuracao_dirf_prestador.tipo) = ''F''
                                    THEN 1
                                    ELSE 2
                            END) as ident_especie_beneficiario
                        , configuracao_dirf_irrf.cod_conta
                        , to_char(nota_liquidacao_paga.timestamp, ''mm'')::int as mes
                        , configuracao_dirf_prestador.cod_dirf
                        , configuracao_dirf_prestador.tipo
                        , empenho.fn_consultar_valor_conta_retencao(configuracao_dirf_prestador.exercicio               
                                                                    , empenho.cod_empenho             
                                                                    , empenho.cod_entidade 
                                                                    , to_char(nota_liquidacao_paga.timestamp, ''mm'')::int
                                                                    , configuracao_dirf_irrf.cod_conta )
                        AS vl_retencao_irrf
                        ,COALESCE(retencoes_inss.vl_retencao_inss,0.00) as vl_retencao_inss 
                        ,empenho.fn_consultar_valor_empenhado_pago_prestadores_dirf(  configuracao_dirf_prestador.exercicio               
                                                                    , empenho.cod_empenho             
                                                                    , empenho.cod_entidade 
                                                                    , to_char(nota_liquidacao_paga.timestamp, ''mm'')::int
                                                                    , configuracao_dirf_prestador.cod_conta )  
                        as vl_empenhado
                        ,empenho.fn_consultar_valor_empenhado_pago_anulado_prestadores_dirf(  configuracao_dirf_prestador.exercicio               
                                                                    , empenho.cod_empenho             
                                                                    , empenho.cod_entidade 
                                                                    , to_char(nota_liquidacao_paga.timestamp, ''mm'')::int
                                                                    , configuracao_dirf_prestador.cod_conta )    
                        AS vl_empenhado_anulado
                    FROM ima'||stEntidade||'.configuracao_dirf_prestador                                
                INNER JOIN (    SELECT * FROM ima'||stEntidade||'.configuracao_dirf_irrf_plano_conta
                                UNION 
                                SELECT * FROM ima'||stEntidade||'.configuracao_dirf_irrf_conta_receita
                            ) as configuracao_dirf_irrf
                        ON configuracao_dirf_prestador.exercicio = configuracao_dirf_irrf.exercicio
                INNER JOIN orcamento.conta_despesa
                        ON configuracao_dirf_prestador.exercicio = conta_despesa.exercicio
                        AND configuracao_dirf_prestador.cod_conta = conta_despesa.cod_conta                
                INNER JOIN empenho.pre_empenho_despesa
                        ON configuracao_dirf_prestador.exercicio = pre_empenho_despesa.exercicio
                        AND configuracao_dirf_prestador.cod_conta = pre_empenho_despesa.cod_conta
                INNER JOIN empenho.pre_empenho
                        ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                INNER JOIN sw_cgm
                        ON pre_empenho.cgm_beneficiario = sw_cgm.numcgm
                INNER JOIN sw_cgm_pessoa_fisica
                        ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
                INNER JOIN empenho.empenho
                        ON pre_empenho.exercicio = empenho.exercicio
                    AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                INNER JOIN empenho.nota_liquidacao
                        ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                    AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                    AND empenho.cod_empenho = nota_liquidacao.cod_empenho
                INNER JOIN empenho.nota_liquidacao_paga
                        ON nota_liquidacao.exercicio = nota_liquidacao_paga.exercicio
                    AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                    AND nota_liquidacao.cod_nota = nota_liquidacao_paga.cod_nota
                INNER JOIN ( SELECT exercicio
                                , cod_entidade
                                , cod_nota
                                , max(timestamp) as timestamp
                            FROM empenho.nota_liquidacao_paga
                        GROUP BY exercicio
                                , cod_entidade
                                , cod_nota ) as max_nota_liquidacao_paga
                        ON nota_liquidacao_paga.exercicio = max_nota_liquidacao_paga.exercicio
                    AND nota_liquidacao_paga.cod_entidade = max_nota_liquidacao_paga.cod_entidade
                    AND nota_liquidacao_paga.cod_nota = max_nota_liquidacao_paga.cod_nota
                    AND nota_liquidacao_paga.timestamp = max_nota_liquidacao_paga.timestamp

                LEFT JOIN ( SELECT   plano_analitica.exercicio 
                                    ,ordem_pagamento_retencao.cod_entidade 
                                    ,nota_liquidacao.cod_empenho
                                    ,to_char(nota_liquidacao_paga.timestamp, ''mm'')::int as mes                                    
                                    ,SUM(empenho.ordem_pagamento_retencao.vl_retencao) as vl_retencao_inss                     
                            FROM contabilidade.plano_analitica

                            INNER JOIN ima'||stEntidade||'.configuracao_dirf_inss
                                 ON configuracao_dirf_inss.exercicio = plano_analitica.exercicio
                                AND configuracao_dirf_inss.cod_conta = plano_analitica.cod_conta                        
                            INNER JOIN empenho.ordem_pagamento_retencao
                                ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                                AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio  
                    
                            INNER JOIN empenho.ordem_pagamento 
                                ON ordem_pagamento.cod_ordem = ordem_pagamento_retencao.cod_ordem
                                AND ordem_pagamento.exercicio = ordem_pagamento_retencao.exercicio
                                AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
           
                            INNER JOIN empenho.pagamento_liquidacao
                                ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                                AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                                AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem
           
                            INNER JOIN empenho.nota_liquidacao
                                ON nota_liquidacao.exercicio = pagamento_liquidacao.exercicio 
                                AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade 
                                AND nota_liquidacao.cod_nota = pagamento_liquidacao.cod_nota
    
                            INNER JOIN empenho.nota_liquidacao_paga
                                ON nota_liquidacao_paga.exercicio       = nota_liquidacao.exercicio
                                AND nota_liquidacao_paga.cod_entidade   = nota_liquidacao.cod_entidade
                                AND nota_liquidacao_paga.cod_nota       = nota_liquidacao.cod_nota

                            INNER JOIN ( SELECT exercicio
                                            , cod_entidade
                                            , cod_nota
                                            , max(timestamp) as timestamp
                                        FROM empenho.nota_liquidacao_paga                   
                                    GROUP BY exercicio
                                            , cod_entidade
                                            , cod_nota ) as max_nota_liquidacao_paga
                                ON nota_liquidacao_paga.exercicio    = max_nota_liquidacao_paga.exercicio
                                AND nota_liquidacao_paga.cod_entidade = max_nota_liquidacao_paga.cod_entidade
                                AND nota_liquidacao_paga.cod_nota     = max_nota_liquidacao_paga.cod_nota
                                AND nota_liquidacao_paga.timestamp    = max_nota_liquidacao_paga.timestamp
        
                            LEFT JOIN empenho.nota_liquidacao_paga_anulada
                                ON nota_liquidacao_paga_anulada.exercicio       = nota_liquidacao_paga.exercicio
                                AND nota_liquidacao_paga_anulada.cod_nota       = nota_liquidacao_paga.cod_nota
                                AND nota_liquidacao_paga_anulada.cod_entidade   = nota_liquidacao_paga.cod_entidade
                                AND nota_liquidacao_paga_anulada.timestamp      = nota_liquidacao_paga.timestamp

                            LEFT JOIN empenho.ordem_pagamento_anulada           
                                ON ordem_pagamento_anulada.exercicio        = ordem_pagamento.exercicio
                                AND ordem_pagamento_anulada.cod_entidade    = ordem_pagamento.cod_entidade
                                AND ordem_pagamento_anulada.cod_ordem       = ordem_pagamento.cod_ordem
    
                            WHERE nota_liquidacao_paga_anulada.cod_nota IS NULL
                            AND ordem_pagamento_anulada.cod_ordem IS NULL
                            GROUP BY plano_analitica.exercicio 
                                    ,ordem_pagamento_retencao.cod_entidade 
                                    ,nota_liquidacao.cod_empenho
                                    ,mes
                                    
                )as retencoes_inss
                    ON configuracao_dirf_irrf.exercicio = retencoes_inss.exercicio
                    AND empenho.cod_entidade = retencoes_inss.cod_entidade
                    AND nota_liquidacao.cod_empenho = retencoes_inss.cod_empenho
                    AND retencoes_inss.mes = to_char(nota_liquidacao_paga.timestamp, ''mm'')::int
                    
                    WHERE configuracao_dirf_prestador.exercicio = '''||inExercicio||'''
                    AND configuracao_dirf_prestador.tipo = ''F''
                    AND empenho.cod_entidade = '||inCodEntidade||'
                    --AND pre_empenho.cgm_beneficiario = 8405
               
            UNION 
                    SELECT REPLACE(sw_cgm.nom_cgm,''–'',''-'') as nom_cgm
                        , sw_cgm.numcgm
                        , sw_cgm_pessoa_juridica.cnpj as beneficiario
                        , ( CASE WHEN trim(configuracao_dirf_prestador.tipo) = ''F''
                                    THEN 1
                                    ELSE 2
                            END) as ident_especie_beneficiario
                        , configuracao_dirf_irrf.cod_conta
                        , to_char(nota_liquidacao_paga.timestamp, ''mm'')::int as mes
                        , configuracao_dirf_prestador.cod_dirf
                        , configuracao_dirf_prestador.tipo
                        , empenho.fn_consultar_valor_conta_retencao(configuracao_dirf_prestador.exercicio               
                                                                    , empenho.cod_empenho             
                                                                    , empenho.cod_entidade 
                                                                    , to_char(nota_liquidacao_paga.timestamp, ''mm'')::int
                                                                    , configuracao_dirf_irrf.cod_conta )
                        AS vl_retencao_irrf
                        ,COALESCE(retencoes_inss.vl_retencao_inss,0.00) as vl_retencao_inss 
                        ,empenho.fn_consultar_valor_empenhado_pago_prestadores_dirf(  configuracao_dirf_prestador.exercicio               
                                                                    , empenho.cod_empenho             
                                                                    , empenho.cod_entidade 
                                                                    , to_char(nota_liquidacao_paga.timestamp, ''mm'')::int
                                                                    , configuracao_dirf_prestador.cod_conta )  
                        as vl_empenhado
                        ,empenho.fn_consultar_valor_empenhado_pago_anulado_prestadores_dirf(  configuracao_dirf_prestador.exercicio               
                                                                    , empenho.cod_empenho             
                                                                    , empenho.cod_entidade 
                                                                    , to_char(nota_liquidacao_paga.timestamp, ''mm'')::int
                                                                    , configuracao_dirf_prestador.cod_conta )    
                        AS vl_empenhado_anulado

                      FROM ima'||stEntidade||'.configuracao_dirf_prestador                
                INNER JOIN (    SELECT * FROM ima'||stEntidade||'.configuracao_dirf_irrf_plano_conta
                                UNION 
                                SELECT * FROM ima'||stEntidade||'.configuracao_dirf_irrf_conta_receita
                            ) as configuracao_dirf_irrf
                        ON configuracao_dirf_prestador.exercicio = configuracao_dirf_irrf.exercicio
                INNER JOIN orcamento.conta_despesa
                        ON configuracao_dirf_prestador.exercicio = conta_despesa.exercicio
                        AND configuracao_dirf_prestador.cod_conta = conta_despesa.cod_conta                
                INNER JOIN empenho.pre_empenho_despesa
                        ON configuracao_dirf_prestador.exercicio = pre_empenho_despesa.exercicio
                        AND configuracao_dirf_prestador.cod_conta = pre_empenho_despesa.cod_conta
                INNER JOIN empenho.pre_empenho
                        ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                INNER JOIN sw_cgm
                        ON pre_empenho.cgm_beneficiario = sw_cgm.numcgm
                INNER JOIN sw_cgm_pessoa_juridica
                        ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm
                INNER JOIN empenho.empenho
                        ON pre_empenho.exercicio = empenho.exercicio
                    AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                INNER JOIN empenho.nota_liquidacao
                        ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                    AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                    AND empenho.cod_empenho = nota_liquidacao.cod_empenho
                INNER JOIN empenho.nota_liquidacao_paga
                        ON nota_liquidacao.exercicio = nota_liquidacao_paga.exercicio
                    AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                    AND nota_liquidacao.cod_nota = nota_liquidacao_paga.cod_nota
                INNER JOIN ( SELECT exercicio
                                , cod_entidade
                                , cod_nota
                                , max(timestamp) as timestamp
                            FROM empenho.nota_liquidacao_paga
                        GROUP BY exercicio
                                , cod_entidade
                                , cod_nota ) as max_nota_liquidacao_paga
                        ON nota_liquidacao_paga.exercicio = max_nota_liquidacao_paga.exercicio
                    AND nota_liquidacao_paga.cod_entidade = max_nota_liquidacao_paga.cod_entidade
                    AND nota_liquidacao_paga.cod_nota = max_nota_liquidacao_paga.cod_nota
                    AND nota_liquidacao_paga.timestamp = max_nota_liquidacao_paga.timestamp

                LEFT JOIN ( SELECT   plano_analitica.exercicio 
                                    ,ordem_pagamento_retencao.cod_entidade 
                                    ,nota_liquidacao.cod_empenho
                                    ,to_char(nota_liquidacao_paga.timestamp, ''mm'')::int as mes                                    
                                    ,SUM(empenho.ordem_pagamento_retencao.vl_retencao) as vl_retencao_inss                     
                            FROM contabilidade.plano_analitica

                            INNER JOIN ima'||stEntidade||'.configuracao_dirf_inss
                                  ON configuracao_dirf_inss.exercicio = plano_analitica.exercicio
                                 AND configuracao_dirf_inss.cod_conta = plano_analitica.cod_conta
                        
                            INNER JOIN empenho.ordem_pagamento_retencao
                                ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                                AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio  
                    
                            INNER JOIN empenho.ordem_pagamento 
                                ON ordem_pagamento.cod_ordem = ordem_pagamento_retencao.cod_ordem
                                AND ordem_pagamento.exercicio = ordem_pagamento_retencao.exercicio
                                AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
           
                            INNER JOIN empenho.pagamento_liquidacao
                                ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                                AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                                AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem
           
                            INNER JOIN empenho.nota_liquidacao
                                ON nota_liquidacao.exercicio = pagamento_liquidacao.exercicio 
                                AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade 
                                AND nota_liquidacao.cod_nota = pagamento_liquidacao.cod_nota
    
                            INNER JOIN empenho.nota_liquidacao_paga
                                ON nota_liquidacao_paga.exercicio       = nota_liquidacao.exercicio
                                AND nota_liquidacao_paga.cod_entidade   = nota_liquidacao.cod_entidade
                                AND nota_liquidacao_paga.cod_nota       = nota_liquidacao.cod_nota

                            INNER JOIN ( SELECT exercicio
                                            , cod_entidade
                                            , cod_nota
                                            , max(timestamp) as timestamp
                                        FROM empenho.nota_liquidacao_paga                   
                                    GROUP BY exercicio
                                            , cod_entidade
                                            , cod_nota ) as max_nota_liquidacao_paga
                                ON nota_liquidacao_paga.exercicio    = max_nota_liquidacao_paga.exercicio
                                AND nota_liquidacao_paga.cod_entidade = max_nota_liquidacao_paga.cod_entidade
                                AND nota_liquidacao_paga.cod_nota     = max_nota_liquidacao_paga.cod_nota
                                AND nota_liquidacao_paga.timestamp    = max_nota_liquidacao_paga.timestamp
        
                            LEFT JOIN empenho.nota_liquidacao_paga_anulada
                                ON nota_liquidacao_paga_anulada.exercicio       = nota_liquidacao_paga.exercicio
                                AND nota_liquidacao_paga_anulada.cod_nota       = nota_liquidacao_paga.cod_nota
                                AND nota_liquidacao_paga_anulada.cod_entidade   = nota_liquidacao_paga.cod_entidade
                                AND nota_liquidacao_paga_anulada.timestamp      = nota_liquidacao_paga.timestamp

                            LEFT JOIN empenho.ordem_pagamento_anulada           
                                ON ordem_pagamento_anulada.exercicio        = ordem_pagamento.exercicio
                                AND ordem_pagamento_anulada.cod_entidade    = ordem_pagamento.cod_entidade
                                AND ordem_pagamento_anulada.cod_ordem       = ordem_pagamento.cod_ordem
    
                            WHERE nota_liquidacao_paga_anulada.cod_nota IS NULL
                            AND ordem_pagamento_anulada.cod_ordem IS NULL
                            GROUP BY plano_analitica.exercicio 
                                    ,ordem_pagamento_retencao.cod_entidade 
                                    ,nota_liquidacao.cod_empenho
                                    ,mes
                                    
                )as retencoes_inss
                    ON configuracao_dirf_irrf.exercicio = retencoes_inss.exercicio
                    AND empenho.cod_entidade = retencoes_inss.cod_entidade
                    AND nota_liquidacao.cod_empenho = retencoes_inss.cod_empenho
                    AND retencoes_inss.mes = to_char(nota_liquidacao_paga.timestamp, ''mm'')::int
                    
                WHERE configuracao_dirf_prestador.exercicio = '''||inExercicio||'''
                    AND configuracao_dirf_prestador.tipo = ''J''
                    AND empenho.cod_entidade = '||inCodEntidade||'
                    --AND pre_empenho.cgm_beneficiario = 8405
            
        
        --UNION PARA RESTOS PESSOA JURIDICA 
        
        UNION 

                SELECT 
                        nom_cgm
                        ,numcgm
                        ,beneficiario
                        ,ident_especie_beneficiario        
                        ,cod_conta
                        ,mes
                        ,cod_dirf
                        ,tipo
                        ,vl_retencao_irrf
                        ,vl_retencao_inss
                        ,SUM(vl_empenhado) as vl_empenhado
                        ,SUM(vl_anulado) as vl_empenhado_anulado

                FROM(

                        SELECT  DISTINCT
                            REPLACE(sw_cgm.nom_cgm,''–'',''-'') as nom_cgm
                            , sw_cgm.numcgm
                            , sw_cgm_pessoa_juridica.cnpj as beneficiario
                            , ( CASE WHEN trim(configuracao_dirf_prestador.tipo) = ''F''
                                        THEN 1
                                        ELSE 2
                                END) as ident_especie_beneficiario
                            ,COALESCE(vl_retencao_irrf.valor,0.00) as vl_retencao_irrf
                            ,COALESCE(retencoes_inss.vl_retencao_inss,0.00) as vl_retencao_inss                         
                            , vl_retencao_irrf.cod_conta
                            , to_char(nota_liquidacao_paga.timestamp,''mm'' )::int as mes
                            , configuracao_dirf_prestador.cod_dirf
                            , configuracao_dirf_prestador.tipo    
                            , nota_liquidacao_paga.vl_pago as vl_empenhado
                            , nota_liquidacao_paga_anulada.vl_anulado as vl_anulado                        
                        
                        FROM ima'||stEntidade||'.configuracao_dirf_prestador                

                        INNER JOIN (    SELECT * FROM ima'||stEntidade||'.configuracao_dirf_irrf_plano_conta
                                        UNION 
                                        SELECT * FROM ima'||stEntidade||'.configuracao_dirf_irrf_conta_receita
                                ) as configuracao_dirf_irrf
                            ON configuracao_dirf_prestador.exercicio = configuracao_dirf_irrf.exercicio
                
                        INNER JOIN orcamento.conta_despesa
                            ON configuracao_dirf_prestador.exercicio = conta_despesa.exercicio
                            AND configuracao_dirf_prestador.cod_conta = conta_despesa.cod_conta                

                        INNER JOIN empenho.restos_pre_empenho
                            ON restos_pre_empenho.cod_estrutural = REPLACE(conta_despesa.cod_estrutural,''.'','''')
                        
                        INNER JOIN empenho.pre_empenho 
                            ON restos_pre_empenho.exercicio        = pre_empenho.exercicio
                            AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                        INNER JOIN sw_cgm
                            ON pre_empenho.cgm_beneficiario = sw_cgm.numcgm

                        INNER JOIN sw_cgm_pessoa_juridica
                            ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm

                        INNER JOIn empenho.empenho
                            ON empenho.cod_pre_empenho  = pre_empenho.cod_pre_empenho
                            AND empenho.exercicio       = pre_empenho.exercicio
                    
                        --Ligação EMPENHO : NOTA LIQUIDAÇÃO                          
                        INNER JOIN empenho.nota_liquidacao
                            ON empenho.exercicio          = nota_liquidacao.exercicio_empenho               
                            AND empenho.cod_entidade      = nota_liquidacao.cod_entidade                    
                            AND empenho.cod_empenho       = nota_liquidacao.cod_empenho

                        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA             
                        INNER JOIN empenho.nota_liquidacao_paga
                            ON nota_liquidacao.exercicio        = nota_liquidacao_paga.exercicio                      
                            AND nota_liquidacao.cod_nota         = nota_liquidacao_paga.cod_nota                       
                            AND nota_liquidacao.cod_entidade     = nota_liquidacao_paga.cod_entidade
    
                        --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA          
                        INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                            ON nota_liquidacao_paga.cod_entidade    = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade                 
                            AND nota_liquidacao_paga.cod_nota        = pagamento_liquidacao_nota_liquidacao_paga.cod_nota                     
                            AND nota_liquidacao_paga.exercicio       = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao         
                            AND nota_liquidacao_paga.timestamp       = pagamento_liquidacao_nota_liquidacao_paga.timestamp

                        --Ligação PAGAMENTO LIQUIDACAO : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA          
                        INNER JOIN empenho.pagamento_liquidacao
                            ON pagamento_liquidacao.cod_ordem             = pagamento_liquidacao_nota_liquidacao_paga.cod_ordem                    
                            AND pagamento_liquidacao.exercicio            = pagamento_liquidacao_nota_liquidacao_paga.exercicio                    
                            AND pagamento_liquidacao.cod_entidade         = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade                 
                            AND pagamento_liquidacao.exercicio_liquidacao = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao     
                            AND pagamento_liquidacao.cod_nota             = pagamento_liquidacao_nota_liquidacao_paga.cod_nota

                        --Ligação PAGAMENTO LIQUIDACAO : ORDEM PAGAMENTO             
                        INNER JOIN empenho.ordem_pagamento      
                            ON pagamento_liquidacao.cod_ordem        = ordem_pagamento.cod_ordem                       
                            AND pagamento_liquidacao.exercicio        = ordem_pagamento.exercicio                       
                            AND pagamento_liquidacao.cod_entidade     = ordem_pagamento.cod_entidade
                           AND TO_CHAR(ordem_pagamento.dt_emissao,''yyyy'') = '''||inExercicio||'''

                        LEFT JOIN empenho.nota_liquidacao_paga_anulada 
                            ON nota_liquidacao_paga.exercicio = nota_liquidacao_paga_anulada.exercicio                   
                            AND nota_liquidacao_paga.cod_nota = nota_liquidacao_paga_anulada.cod_nota                     
                            AND nota_liquidacao_paga.cod_entidade = nota_liquidacao_paga_anulada.cod_entidade             
                            AND nota_liquidacao_paga.timestamp = nota_liquidacao_paga_anulada.timestamp        
                               
                        INNER JOIN (
                                    SELECT 
                                        coalesce(  empenho.ordem_pagamento_retencao.vl_retencao,0.00) as valor
                                        ,ordem_pagamento.cod_ordem
                                        ,ordem_pagamento.exercicio
                                        ,ordem_pagamento.cod_entidade 
                                        ,configuracao_dirf_irrf.cod_conta
         
                                    FROM contabilidade.plano_analitica
                                    INNER JOIN empenho.ordem_pagamento_retencao
                                         ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                                        AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio
                     
                                    INNER JOIN ( SELECT * FROM ima'||stEntidade||'.configuracao_dirf_irrf_plano_conta
                                                UNION 
                                                SELECT * FROM ima'||stEntidade||'.configuracao_dirf_irrf_conta_receita
                                            ) as configuracao_dirf_irrf
                                        ON configuracao_dirf_irrf.exercicio = plano_analitica.exercicio
                                        AND configuracao_dirf_irrf.cod_conta = plano_analitica.cod_conta

                                    INNER JOIN empenho.ordem_pagamento
                                         ON ordem_pagamento.cod_ordem = ordem_pagamento_retencao.cod_ordem
                                        AND ordem_pagamento.exercicio = ordem_pagamento_retencao.exercicio
                                        AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
      
                                    INNER JOIN empenho.pagamento_liquidacao 
                                     ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                                    AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                                    AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem
      
                                    INNER JOIN empenho.nota_liquidacao 
                                     ON nota_liquidacao.exercicio = pagamento_liquidacao.exercicio_liquidacao
                                    AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade 
                                    AND nota_liquidacao.cod_nota = pagamento_liquidacao.cod_nota
                        ) as vl_retencao_irrf
                            ON  vl_retencao_irrf.exercicio      = ordem_pagamento.exercicio
                            AND vl_retencao_irrf.cod_entidade   = ordem_pagamento.cod_entidade
                            AND vl_retencao_irrf.cod_ordem      = ordem_pagamento.cod_ordem

                        --RETENCOES INSS
                        LEFT JOIN ( SELECT   
                                        plano_analitica.exercicio 
                                        ,ordem_pagamento_retencao.cod_entidade 
                                        ,nota_liquidacao.cod_empenho
                                        ,to_char(nota_liquidacao_paga.timestamp, ''mm'')::int as mes                                    
                                        ,SUM(empenho.ordem_pagamento_retencao.vl_retencao) as vl_retencao_inss                     
                            
                                    FROM contabilidade.plano_analitica

                                    INNER JOIN ima'||stEntidade||'.configuracao_dirf_inss
                                         ON configuracao_dirf_inss.exercicio = plano_analitica.exercicio
                                        AND configuracao_dirf_inss.cod_conta = plano_analitica.cod_conta
                        
                                    INNER JOIN empenho.ordem_pagamento_retencao
                                        ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                                        AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio  
                    
                                    INNER JOIN empenho.ordem_pagamento 
                                        ON ordem_pagamento.cod_ordem = ordem_pagamento_retencao.cod_ordem
                                        AND ordem_pagamento.exercicio = ordem_pagamento_retencao.exercicio
                                        AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
           
                                    INNER JOIN empenho.pagamento_liquidacao
                                        ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                                        AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                                        AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem
           
                                    INNER JOIN empenho.nota_liquidacao
                                        ON nota_liquidacao.exercicio = pagamento_liquidacao.exercicio 
                                        AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade 
                                        AND nota_liquidacao.cod_nota = pagamento_liquidacao.cod_nota
    
                                    INNER JOIN empenho.nota_liquidacao_paga
                                        ON nota_liquidacao_paga.exercicio       = nota_liquidacao.exercicio
                                        AND nota_liquidacao_paga.cod_entidade   = nota_liquidacao.cod_entidade
                                        AND nota_liquidacao_paga.cod_nota       = nota_liquidacao.cod_nota
    
                                    INNER JOIN ( SELECT exercicio
                                                    , cod_entidade
                                                    , cod_nota
                                                    , max(timestamp) as timestamp
                                                FROM empenho.nota_liquidacao_paga
                                                WHERE TO_CHAR(nota_liquidacao_paga.timestamp,''yyyy'') = '''||inExercicio||'''
                                                AND cod_entidade = '||inCodEntidade||'
                                                GROUP BY exercicio
                                                    , cod_entidade
                                                    , cod_nota 
                                    ) as max_nota_liquidacao_paga
                                        ON nota_liquidacao_paga.exercicio     = max_nota_liquidacao_paga.exercicio
                                        AND nota_liquidacao_paga.cod_entidade = max_nota_liquidacao_paga.cod_entidade
                                        AND nota_liquidacao_paga.cod_nota     = max_nota_liquidacao_paga.cod_nota
                                        AND nota_liquidacao_paga.timestamp    = max_nota_liquidacao_paga.timestamp
        
                                    LEFT JOIN empenho.nota_liquidacao_paga_anulada
                                        ON nota_liquidacao_paga_anulada.exercicio       = nota_liquidacao_paga.exercicio
                                        AND nota_liquidacao_paga_anulada.cod_nota       = nota_liquidacao_paga.cod_nota
                                        AND nota_liquidacao_paga_anulada.cod_entidade   = nota_liquidacao_paga.cod_entidade
                                        AND nota_liquidacao_paga_anulada.timestamp      = nota_liquidacao_paga.timestamp

                                    LEFT JOIN empenho.ordem_pagamento_anulada           
                                        ON ordem_pagamento_anulada.exercicio        = ordem_pagamento.exercicio
                                        AND ordem_pagamento_anulada.cod_entidade    = ordem_pagamento.cod_entidade
                                        AND ordem_pagamento_anulada.cod_ordem       = ordem_pagamento.cod_ordem
    
                                    WHERE nota_liquidacao_paga_anulada.cod_nota IS NULL
                                    AND ordem_pagamento_anulada.cod_ordem IS NULL
                                    GROUP BY plano_analitica.exercicio 
                                            ,ordem_pagamento_retencao.cod_entidade 
                                            ,nota_liquidacao.cod_empenho
                                            ,mes
                        )as retencoes_inss
                            ON conta_despesa.exercicio = retencoes_inss.exercicio
                            AND empenho.cod_entidade = retencoes_inss.cod_entidade
                            AND nota_liquidacao.cod_empenho = retencoes_inss.cod_empenho
                            AND retencoes_inss.mes = to_char(nota_liquidacao_paga.timestamp, ''mm'')::int
  
                        WHERE configuracao_dirf_prestador.tipo = ''J''
                        AND TO_CHAR(nota_liquidacao_paga.timestamp,''yyyy'') = '''||inExercicio||'''
                        AND empenho.cod_entidade = '||inCodEntidade||'
                        --AND pre_empenho.cgm_beneficiario = 7700
                ) as restos_pessoa_juridica
                GROUP BY 1,2,3,4,5,6,7,8,9,10

        UNION

                SELECT 
                        nom_cgm
                        ,numcgm
                        ,beneficiario
                        ,ident_especie_beneficiario        
                        ,cod_conta
                        ,mes
                        ,cod_dirf
                        ,tipo
                        ,vl_retencao_irrf
                        ,vl_retencao_inss
                        ,SUM(vl_empenhado) as vl_empenhado
                        ,SUM(vl_anulado) as vl_empenhado_anulado

                FROM(

                        SELECT  DISTINCT
                            REPLACE(sw_cgm.nom_cgm,''–'',''-'') as nom_cgm
                            , sw_cgm.numcgm
                            , sw_cgm_pessoa_fisica.cpf as beneficiario
                            , ( CASE WHEN trim(configuracao_dirf_prestador.tipo) = ''F''
                                        THEN 1
                                        ELSE 2
                                END) as ident_especie_beneficiario
                            ,COALESCE(vl_retencao_irrf.valor,0.00) as vl_retencao_irrf
                            ,COALESCE(retencoes_inss.vl_retencao_inss,0.00) as vl_retencao_inss                         
                            , vl_retencao_irrf.cod_conta
                            , to_char(nota_liquidacao_paga.timestamp,''mm'' )::int as mes
                            , configuracao_dirf_prestador.cod_dirf
                            , configuracao_dirf_prestador.tipo    
                            , nota_liquidacao_paga.vl_pago as vl_empenhado
                            , nota_liquidacao_paga_anulada.vl_anulado as vl_anulado                        
                        
                        FROM ima'||stEntidade||'.configuracao_dirf_prestador                

                        INNER JOIN ( SELECT * FROM ima'||stEntidade||'.configuracao_dirf_irrf_plano_conta
                                     UNION 
                                     SELECT * FROM ima'||stEntidade||'.configuracao_dirf_irrf_conta_receita
                                ) as configuracao_dirf_irrf
                            ON configuracao_dirf_irrf.exercicio = configuracao_dirf_prestador.exercicio                                                          
                                        
                        INNER JOIN orcamento.conta_despesa
                            ON configuracao_dirf_prestador.exercicio = conta_despesa.exercicio
                            AND configuracao_dirf_prestador.cod_conta = conta_despesa.cod_conta                

                        INNER JOIN empenho.restos_pre_empenho
                            ON restos_pre_empenho.cod_estrutural = REPLACE(conta_despesa.cod_estrutural,''.'','''')
                        
                        INNER JOIN empenho.pre_empenho 
                            ON restos_pre_empenho.exercicio        = pre_empenho.exercicio
                            AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                        INNER JOIN sw_cgm
                            ON pre_empenho.cgm_beneficiario = sw_cgm.numcgm

                        INNER JOIN sw_cgm_pessoa_fisica
                            ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

                        INNER JOIn empenho.empenho
                            ON empenho.cod_pre_empenho  = pre_empenho.cod_pre_empenho
                            AND empenho.exercicio       = pre_empenho.exercicio
                    
                        --Ligação EMPENHO : NOTA LIQUIDAÇÃO                          
                        INNER JOIN empenho.nota_liquidacao
                            ON empenho.exercicio          = nota_liquidacao.exercicio_empenho               
                            AND empenho.cod_entidade      = nota_liquidacao.cod_entidade                    
                            AND empenho.cod_empenho       = nota_liquidacao.cod_empenho

                        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA             
                        INNER JOIN empenho.nota_liquidacao_paga
                            ON nota_liquidacao.exercicio        = nota_liquidacao_paga.exercicio                      
                            AND nota_liquidacao.cod_nota         = nota_liquidacao_paga.cod_nota                       
                            AND nota_liquidacao.cod_entidade     = nota_liquidacao_paga.cod_entidade
    
                        --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA          
                        INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                            ON nota_liquidacao_paga.cod_entidade    = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade                 
                            AND nota_liquidacao_paga.cod_nota        = pagamento_liquidacao_nota_liquidacao_paga.cod_nota                     
                            AND nota_liquidacao_paga.exercicio       = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao         
                            AND nota_liquidacao_paga.timestamp       = pagamento_liquidacao_nota_liquidacao_paga.timestamp

                        --Ligação PAGAMENTO LIQUIDACAO : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA          
                        INNER JOIN empenho.pagamento_liquidacao
                            ON pagamento_liquidacao.cod_ordem             = pagamento_liquidacao_nota_liquidacao_paga.cod_ordem                    
                            AND pagamento_liquidacao.exercicio            = pagamento_liquidacao_nota_liquidacao_paga.exercicio                    
                            AND pagamento_liquidacao.cod_entidade         = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade                 
                            AND pagamento_liquidacao.exercicio_liquidacao = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao     
                            AND pagamento_liquidacao.cod_nota             = pagamento_liquidacao_nota_liquidacao_paga.cod_nota

                        --Ligação PAGAMENTO LIQUIDACAO : ORDEM PAGAMENTO             
                        INNER JOIN empenho.ordem_pagamento      
                            ON pagamento_liquidacao.cod_ordem        = ordem_pagamento.cod_ordem                       
                            AND pagamento_liquidacao.exercicio        = ordem_pagamento.exercicio                       
                            AND pagamento_liquidacao.cod_entidade     = ordem_pagamento.cod_entidade
                           AND TO_CHAR(ordem_pagamento.dt_emissao,''yyyy'') = '''||inExercicio||'''

                        LEFT JOIN empenho.nota_liquidacao_paga_anulada 
                            ON nota_liquidacao_paga.exercicio = nota_liquidacao_paga_anulada.exercicio                   
                            AND nota_liquidacao_paga.cod_nota = nota_liquidacao_paga_anulada.cod_nota                     
                            AND nota_liquidacao_paga.cod_entidade = nota_liquidacao_paga_anulada.cod_entidade             
                            AND nota_liquidacao_paga.timestamp = nota_liquidacao_paga_anulada.timestamp        
                               
                        INNER JOIN (
                                    SELECT 
                                        coalesce(  empenho.ordem_pagamento_retencao.vl_retencao,0.00) as valor
                                        ,ordem_pagamento.cod_ordem
                                        ,ordem_pagamento.exercicio
                                        ,ordem_pagamento.cod_entidade 
                                        ,configuracao_dirf_irrf.cod_conta
         
                                    FROM contabilidade.plano_analitica
                                    INNER JOIN empenho.ordem_pagamento_retencao
                                         ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                                        AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio

                                    INNER JOIN ( SELECT * FROM ima'||stEntidade||'.configuracao_dirf_irrf_plano_conta
                                                UNION 
                                                SELECT * FROM ima'||stEntidade||'.configuracao_dirf_irrf_conta_receita
                                            ) as configuracao_dirf_irrf
                                        ON configuracao_dirf_irrf.exercicio = plano_analitica.exercicio
                                        AND configuracao_dirf_irrf.cod_conta = plano_analitica.cod_conta
                     
                                    INNER JOIN empenho.ordem_pagamento
                                         ON ordem_pagamento.cod_ordem = ordem_pagamento_retencao.cod_ordem
                                        AND ordem_pagamento.exercicio = ordem_pagamento_retencao.exercicio
                                        AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
      
                                    INNER JOIN empenho.pagamento_liquidacao 
                                     ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                                    AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                                    AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem
      
                                    INNER JOIN empenho.nota_liquidacao 
                                     ON nota_liquidacao.exercicio = pagamento_liquidacao.exercicio_liquidacao
                                    AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade 
                                    AND nota_liquidacao.cod_nota = pagamento_liquidacao.cod_nota
                        ) as vl_retencao_irrf
                            ON  vl_retencao_irrf.exercicio      = ordem_pagamento.exercicio
                            AND vl_retencao_irrf.cod_entidade   = ordem_pagamento.cod_entidade
                            AND vl_retencao_irrf.cod_ordem      = ordem_pagamento.cod_ordem

                        --RETENCOES INSS
                        LEFT JOIN ( SELECT   
                                        plano_analitica.exercicio 
                                        ,ordem_pagamento_retencao.cod_entidade 
                                        ,nota_liquidacao.cod_empenho
                                        ,to_char(nota_liquidacao_paga.timestamp, ''mm'')::int as mes                                    
                                        ,SUM(empenho.ordem_pagamento_retencao.vl_retencao) as vl_retencao_inss                     
                            
                                    FROM contabilidade.plano_analitica

                                    INNER JOIN ima'||stEntidade||'.configuracao_dirf_inss
                                         ON configuracao_dirf_inss.exercicio = plano_analitica.exercicio
                                        AND configuracao_dirf_inss.cod_conta = plano_analitica.cod_conta
                        
                                    INNER JOIN empenho.ordem_pagamento_retencao
                                        ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                                        AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio  
                    
                                    INNER JOIN empenho.ordem_pagamento 
                                        ON ordem_pagamento.cod_ordem = ordem_pagamento_retencao.cod_ordem
                                        AND ordem_pagamento.exercicio = ordem_pagamento_retencao.exercicio
                                        AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
           
                                    INNER JOIN empenho.pagamento_liquidacao
                                        ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                                        AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                                        AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem
           
                                    INNER JOIN empenho.nota_liquidacao
                                        ON nota_liquidacao.exercicio = pagamento_liquidacao.exercicio 
                                        AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade 
                                        AND nota_liquidacao.cod_nota = pagamento_liquidacao.cod_nota
    
                                    INNER JOIN empenho.nota_liquidacao_paga
                                        ON nota_liquidacao_paga.exercicio       = nota_liquidacao.exercicio
                                        AND nota_liquidacao_paga.cod_entidade   = nota_liquidacao.cod_entidade
                                        AND nota_liquidacao_paga.cod_nota       = nota_liquidacao.cod_nota
    
                                    INNER JOIN ( SELECT exercicio
                                                    , cod_entidade
                                                    , cod_nota
                                                    , max(timestamp) as timestamp
                                                FROM empenho.nota_liquidacao_paga
                                                WHERE TO_CHAR(nota_liquidacao_paga.timestamp,''yyyy'') = '''||inExercicio||'''
                                                AND cod_entidade = '||inCodEntidade||'
                                                GROUP BY exercicio
                                                    , cod_entidade
                                                    , cod_nota 
                                    ) as max_nota_liquidacao_paga
                                        ON nota_liquidacao_paga.exercicio     = max_nota_liquidacao_paga.exercicio
                                        AND nota_liquidacao_paga.cod_entidade = max_nota_liquidacao_paga.cod_entidade
                                        AND nota_liquidacao_paga.cod_nota     = max_nota_liquidacao_paga.cod_nota
                                        AND nota_liquidacao_paga.timestamp    = max_nota_liquidacao_paga.timestamp
        
                                    LEFT JOIN empenho.nota_liquidacao_paga_anulada
                                        ON nota_liquidacao_paga_anulada.exercicio       = nota_liquidacao_paga.exercicio
                                        AND nota_liquidacao_paga_anulada.cod_nota       = nota_liquidacao_paga.cod_nota
                                        AND nota_liquidacao_paga_anulada.cod_entidade   = nota_liquidacao_paga.cod_entidade
                                        AND nota_liquidacao_paga_anulada.timestamp      = nota_liquidacao_paga.timestamp

                                    LEFT JOIN empenho.ordem_pagamento_anulada           
                                        ON ordem_pagamento_anulada.exercicio        = ordem_pagamento.exercicio
                                        AND ordem_pagamento_anulada.cod_entidade    = ordem_pagamento.cod_entidade
                                        AND ordem_pagamento_anulada.cod_ordem       = ordem_pagamento.cod_ordem
    
                                    WHERE nota_liquidacao_paga_anulada.cod_nota IS NULL
                                    AND ordem_pagamento_anulada.cod_ordem IS NULL
                                    GROUP BY plano_analitica.exercicio 
                                            ,ordem_pagamento_retencao.cod_entidade 
                                            ,nota_liquidacao.cod_empenho
                                            ,mes
                        )as retencoes_inss
                            ON conta_despesa.exercicio = retencoes_inss.exercicio
                            AND empenho.cod_entidade = retencoes_inss.cod_entidade
                            AND nota_liquidacao.cod_empenho = retencoes_inss.cod_empenho
                            AND retencoes_inss.mes = to_char(nota_liquidacao_paga.timestamp, ''mm'')::int
  
                        WHERE configuracao_dirf_prestador.tipo = ''F''
                        AND TO_CHAR(nota_liquidacao_paga.timestamp,''yyyy'') = '''||inExercicio||'''
                        AND empenho.cod_entidade = '||inCodEntidade||'
                        --AND pre_empenho.cgm_beneficiario = 7700
                ) as restos_pessoa_juridica
                GROUP BY 1,2,3,4,5,6,7,8,9,10

            ) as resultado              
            WHERE vl_retencao_irrf > 0            
        )';      

    EXECUTE stSql;

    RETURN TRUE;
END;
$$ LANGUAGE plpgsql;
