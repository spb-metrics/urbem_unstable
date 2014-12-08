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
                  SELECT sw_cgm.nom_cgm
                        , sw_cgm.numcgm
                        , sw_cgm_pessoa_fisica.cpf as beneficiario
                        , ( CASE WHEN trim(configuracao_dirf_prestador.tipo) = ''F''
                                    THEN 1
                                    ELSE 2
                            END) as ident_especie_beneficiario
                        , sum(( empenho.fn_consultar_valor_empenhado_pago(  configuracao_dirf_prestador.exercicio               
                                                                    , empenho.cod_empenho             
                                                                    , empenho.cod_entidade )

                            - empenho.fn_consultar_valor_empenhado_pago_anulado(  configuracao_dirf_prestador.exercicio               
                                                                    , empenho.cod_empenho             
                                                                    , empenho.cod_entidade )
                        )) AS vl_empenhado                                             
                        , sum(empenho.fn_consultar_valor_conta_retencao(configuracao_dirf_prestador.exercicio               
                                                                    , empenho.cod_empenho             
                                                                    , empenho.cod_entidade 
                                                                    , configuracao_dirf_irrf.cod_conta)) as vl_retencao_irrf
                        , sum(empenho.fn_consultar_valor_conta_retencao(configuracao_dirf_prestador.exercicio               
                                                                    , empenho.cod_empenho             
                                                                    , empenho.cod_entidade 
                                                                    , configuracao_dirf_inss.cod_conta)) as vl_retencao_inss
                        , configuracao_dirf_irrf.cod_conta
                        , (SELECT to_char(periodo_movimentacao.dt_final, ''mm'')::int
                             FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao 
                            WHERE nota_liquidacao_paga.timestamp::date between periodo_movimentacao.dt_inicial AND periodo_movimentacao.dt_final) as mes
                        , configuracao_dirf_prestador.cod_dirf
                        , configuracao_dirf_prestador.tipo
                    FROM ima'|| stEntidade ||'.configuracao_dirf_prestador
                INNER JOIN ima'|| stEntidade ||'.configuracao_dirf_inss
                        ON configuracao_dirf_prestador.exercicio = configuracao_dirf_inss.exercicio
                INNER JOIN ima'|| stEntidade ||'.configuracao_dirf_irrf
                        ON configuracao_dirf_prestador.exercicio = configuracao_dirf_irrf.exercicio
                INNER JOIN orcamento.conta_despesa
                        ON configuracao_dirf_prestador.exercicio = conta_despesa.exercicio
                    AND configuracao_dirf_prestador.cod_conta = conta_despesa.cod_conta
                INNER JOIN orcamento.despesa
                        ON conta_despesa.exercicio = despesa.exercicio
                    AND conta_despesa.cod_conta = despesa.cod_conta        
                INNER JOIN empenho.pre_empenho_despesa
                        ON despesa.exercicio = pre_empenho_despesa.exercicio
                    AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
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
                    WHERE configuracao_dirf_prestador.exercicio = '|| inExercicio ||'
                    AND configuracao_dirf_prestador.tipo = ''F''
                    AND empenho.cod_entidade = '|| inCodEntidade ||'
                    -- AND pre_empenho.cgm_beneficiario = 965
               GROUP BY sw_cgm.nom_cgm
                      , sw_cgm.numcgm
                      , beneficiario
                      , ident_especie_beneficiario
                      , configuracao_dirf_irrf.cod_conta
                      , mes
                      , configuracao_dirf_prestador.cod_dirf
                      , tipo
            UNION 
                    SELECT sw_cgm.nom_cgm
                        , sw_cgm.numcgm
                        , sw_cgm_pessoa_juridica.cnpj as beneficiario
                        , ( CASE WHEN trim(configuracao_dirf_prestador.tipo) = ''F''
                                    THEN 1
                                    ELSE 2
                            END) as ident_especie_beneficiario
                        , sum(( empenho.fn_consultar_valor_empenhado_pago(  configuracao_dirf_prestador.exercicio               
                                                                        , empenho.cod_empenho             
                                                                        , empenho.cod_entidade )  
                            - empenho.fn_consultar_valor_empenhado_pago_anulado(  configuracao_dirf_prestador.exercicio               
                                                                        , empenho.cod_empenho             
                                                                        , empenho.cod_entidade )
                            )) AS vl_empenhado                                             
                        , sum(empenho.fn_consultar_valor_conta_retencao(configuracao_dirf_prestador.exercicio               
                                                                    , empenho.cod_empenho             
                                                                    , empenho.cod_entidade 
                                                                    , configuracao_dirf_irrf.cod_conta)) as vl_retencao_irrf
                        , sum(empenho.fn_consultar_valor_conta_retencao(configuracao_dirf_prestador.exercicio               
                                                                    , empenho.cod_empenho             
                                                                    , empenho.cod_entidade 
                                                                    , configuracao_dirf_inss.cod_conta)) as vl_retencao_inss
                        , configuracao_dirf_irrf.cod_conta
                        , (SELECT to_char(periodo_movimentacao.dt_final, ''mm'')::int
                             FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao 
                            WHERE nota_liquidacao_paga.timestamp::date between periodo_movimentacao.dt_inicial AND periodo_movimentacao.dt_final) as mes
                        , configuracao_dirf_prestador.cod_dirf
                        , configuracao_dirf_prestador.tipo
                      FROM ima'|| stEntidade ||'.configuracao_dirf_prestador
                INNER JOIN ima'|| stEntidade ||'.configuracao_dirf_inss
                        ON configuracao_dirf_prestador.exercicio = configuracao_dirf_inss.exercicio
                INNER JOIN ima'|| stEntidade ||'.configuracao_dirf_irrf
                        ON configuracao_dirf_prestador.exercicio = configuracao_dirf_irrf.exercicio
                INNER JOIN orcamento.conta_despesa
                        ON configuracao_dirf_prestador.exercicio = conta_despesa.exercicio
                    AND configuracao_dirf_prestador.cod_conta = conta_despesa.cod_conta
                INNER JOIN orcamento.despesa
                        ON conta_despesa.exercicio = despesa.exercicio
                    AND conta_despesa.cod_conta = despesa.cod_conta        
                INNER JOIN empenho.pre_empenho_despesa
                        ON despesa.exercicio = pre_empenho_despesa.exercicio
                    AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
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
                WHERE configuracao_dirf_prestador.exercicio = '|| inExercicio ||'
                    AND configuracao_dirf_prestador.tipo = ''J''
                    AND empenho.cod_entidade = '|| inCodEntidade ||'
                    --AND pre_empenho.cgm_beneficiario = 965
               GROUP BY sw_cgm.nom_cgm
                      , sw_cgm.numcgm
                      , beneficiario
                      , ident_especie_beneficiario
                      , configuracao_dirf_irrf.cod_conta
                      , mes
                      , configuracao_dirf_prestador.cod_dirf
                      , tipo
            ORDER BY 2, 9
        )';      

    EXECUTE stSql;

    RETURN TRUE;
END;
$$ LANGUAGE plpgsql;
