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
* $Id: $

* Casos de uso: uc-06.01.02
*/

CREATE OR REPLACE FUNCTION stn.fn_comparativoPe(stExercicio varchar, dtInicio varchar, dtFinal varchar, stEntidades varchar) RETURNS SETOF RECORD AS $$
DECLARE
  stSql         VARCHAR;
  reRegistro    RECORD;  
  inMes         INTEGER;
  arCodPlanos   INTEGER[];
  stCodContas   VARCHAR;
BEGIN

    inMes:= EXTRACT(month from TO_TIMESTAMP(dtInicio,'dd/mm/yyyy'));

    --Buscando contas configuradas como Antecipações de Receita Orçamentária - ARO
    stSql := '  SELECT cod_plano         
                FROM stn.vinculo_contas_rgf_2 
                WHERE cod_conta = 17 
                AND exercicio = '''||stExercicio||'''
        ';

    FOR reRegistro IN EXECUTE stSql 
    LOOP
        arCodPlanos := array_append(arCodPlanos, reRegistro.cod_plano);
    END LOOP;

    stCodContas := array_to_string(arCodPlanos, ',');

    stSql = ' --Valor da despesa liquida inativos e pensionistas
            SELECT  descricao
                    , liquidado as valor
            FROM(
                    SELECT  nivel
                            , cod_estrutural
                            , descricao
                            , liquidado
                            , restos
                    FROM stn.fn_rgf_anexo1_despesas ( '''||stExercicio||''', '''||dtInicio||''', '''||dtFinal||''', '''||stEntidades||''') 
                    as retorno(
                                nivel                   INTEGER,
                                cod_estrutural          VARCHAR,
                                descricao               VARCHAR,
                                liquidado               DECIMAL(14,2),  
                                restos                  DECIMAL(14,2)    
                            )
            ) as tbl1
            WHERE descricao ilike ''%Inativo e Pensio%''

            UNION 
            --Valor da divida consolidada
            --Valor da divida consolidada líquida
            --Valor da dívida mobiliária
            SELECT  descricao
                    , valor_quadrimestre_1 as valor 
            FROM(
                    SELECT  descricao
                            , ordem
                            , valor_exercicio_anterior
                            , valor_quadrimestre_1
                            , valor_quadrimestre_2
                            , valor_quadrimestre_3
                            , nivel
                    FROM stn.fn_rgf_anexo2mensal('''||stExercicio||''', ''Mensal'', 3, '''||stEntidades||''', '''||dtInicio||''', '''||dtFinal||''')
                    AS tbl(  
                            descricao                   varchar
		                    , ordem                     integer
		                    , valor_exercicio_anterior  numeric
		                    , valor_quadrimestre_1      numeric
		                    , valor_quadrimestre_2      numeric
		                    , valor_quadrimestre_3      numeric
		                    , nivel                     integer
                        )
			) as tbl
            WHERE descricao ilike ''%VIDA CONSOLIDADA%'' OR descricao ilike ''%Mobili%''

            UNION

            --Valor das concessões de garantia
            SELECT  ''VALOR das concessoes de garantia'' as descricao
                    , (valor1 + valor2 + valor3 + valor4) as valor
			FROM(
                    SELECT  coalesce(                                                                                           
                                        stn.fn_rgf_calcula_saldo_garantias_anexo3                                                        
                                        (                                                                                            
                                            '''||stExercicio||'''
                                            , '''||stEntidades||'''
                                            ,''and pc.cod_estrutural in (''''1.9.9.5.1.02.02.00.00.00'''', ''''1.9.9.5.2.01.02.00.00.00'''')'' 
                                            , '''||dtInicio||'''
                                            , '''||dtFinal||'''
                                        ),0.00
                            ) as valor1 --saldo_primeiro_quadrimestre_externo_aval
                            , coalesce(                                                                                           
                                        stn.fn_rgf_calcula_saldo_garantias_anexo3                                                        
                                        (                                                                                            
                                            '''||stExercicio||'''
                                            , '''||stEntidades||'''
                                            ,''and pc.cod_estrutural in (''''1.9.9.7.0.00.00.00.00.00'''')''
                                            , '''||dtInicio||'''
                                            , '''||dtFinal||'''
                                        ),0.00
                            ) as valor2 --saldo_primeiro_quadrimestre_externo_outro                                                                    
                            , coalesce(                                                                                           
                                        stn.fn_rgf_calcula_saldo_garantias_anexo3                                                        
                                        (                                                                                            
                                            '''||stExercicio||'''
                                            , '''||stEntidades||'''
                                            ,''and pc.cod_estrutural in (''''1.9.9.5.1.02.00.00.00.00'''',''''1.9.9.5.1.02.01.00.00.00''''
                                                                        ,''''1.9.9.5.2.00.00.00.00.00'''',''''1.9.9.5.2.01.00.00.00.00'''',''''1.9.9.5.2.01.01.00.00.00'''')''
                                            , '''||dtInicio||'''
                                            , '''||dtfinal||'''
                                        ),0.00
                            ) as valor3 --saldo_primeiro_quadrimestre_interno_aval                                          
                            ,coalesce(                                                                                           
                                        stn.fn_rgf_calcula_saldo_garantias_anexo3                                                        
                                        (                                                                                            
                                            '''||stExercicio||'''
                                            , '''||stEntidades||'''
                                            ,''and pc.cod_estrutural in (''''1.9.9.5.9.00.00.00.00.00'''')''                               
                                            , '''||dtInicio||'''
                                            , '''||dtFinal||'''
                                        ),0.00)
                             as valor4 --saldo_primeiro_quadrimestre_interno_outro
            ) as tbl2 
        
            UNION

            --Valor da antecipação da receita orçamentária
            SELECT  descricao
                    ,valor
            FROM(
                    SELECT  descricao
                            , valor_mes as valor
                    FROM stn.fn_rgf_anexo2_valores_nao_integrantes_mensal('''||stExercicio||''', ''Mes'', '||inMes||' , '''||stEntidades||''')
                    as tbl (  
                            descricao varchar 
                            , ordem integer 
                            , valor_exercicio_anterior numeric 
                            , valor_mes numeric 
                            , nivel integer 
                    )
            )as tbl3
            WHERE descricao ilike ''%ARO%''

            UNION

            --Valor das operações de crédito
            SELECT  ''VALOR das operacoes de credito'' as descricao
                    , (valor1 + valor2 + valor3 + valor4) as valor
            FROM(
                    SELECT  coalesce(                                                                                           
                                        stn.fn_rgf_calcula_saldo_garantias_anexo3                                                        
                                        (                                                                                            
                                            '''||stExercicio||'''
                                            , '''||stEntidades||'''
                                            ,'' AND pc.cod_estrutural in (''''1.9.9.5.6.01.02.00.00.00'''', ''''1.9.9.5.6.02.02.00.00.00'''')
                                                AND pa.cod_plano NOT IN ('||stCodContas||') '' 
                                            , '''||dtInicio||'''
                                            , '''||dtFinal||'''
                                        ),0.00
                            ) as valor1 --saldo_mes_recebido_externo_aval
                            , coalesce(                                                                                           
                                        stn.fn_rgf_calcula_saldo_garantias_anexo3                                                        
                                        (                                                                                            
                                            '''||stExercicio||'''
                                            , '''||stEntidades||'''
                                            ,'' AND pc.cod_estrutural in (''''1.9.9.6.9.00.00.00.00.00'''')
                                                AND pa.cod_plano NOT IN ('||stCodContas||') '' 
                                            , '''||dtInicio||'''
                                            , '''||dtFinal||'''
                                        ),0.00
                            ) as valor2 --saldo_mes_recebido_externo_outro                                                                    
                            , coalesce(                                                                                           
                                        stn.fn_rgf_calcula_saldo_garantias_anexo3                                                        
                                        (                                                                                            
                                            '''||stExercicio||'''
                                            , '''||stEntidades||'''
                                            ,'' AND pc.cod_estrutural in (''''1.9.9.5.6.00.00.00.00.00''''
                                                                            ,''''1.9.9.5.6.01.00.00.00.00''''
                                                                            ,''''1.9.9.5.6.01.01.00.00.00''''
                                                                            ,''''1.9.9.5.6.02.00.00.00.00''''
                                                                            ,''''1.9.9.5.6.02.01.00.00.00'''')
                                                AND pa.cod_plano NOT IN ('||stCodContas||') ''
                                            , '''||dtInicio||'''
                                            , '''||dtfinal||'''
                                        ),0.00
                            ) as valor3 --saldo_mes_recebido_interna_aval                                          
                            ,coalesce(                                                                                           
                                        stn.fn_rgf_calcula_saldo_garantias_anexo3                                                        
                                        (                                                                                            
                                            '''||stExercicio||'''
                                            , '''||stEntidades||'''
                                            ,'' AND pc.cod_estrutural in (''''1.9.9.5.9.00.00.00.00.00'''')
                                                AND pa.cod_plano NOT IN ('||stCodContas||') ''
                                            , '''||dtInicio||'''
                                            , '''||dtFinal||'''
                                        ),0.00)
                             as valor4 --saldo_mes_recebido_interna_outro
            ) as tbl4

        ';

  FOR reRegistro IN EXECUTE stSql
  LOOP
      RETURN next reRegistro;
  END LOOP;

END;

$$ language 'plpgsql';
