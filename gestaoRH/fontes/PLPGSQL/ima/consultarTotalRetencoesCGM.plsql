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
/* consultar_total_retencoes_cgm
 * 
 * Data de Criação : 23/01/2009


 * @author Analista : Dagiane   
 * @author Desenvolvedor : Rafael Garbin
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION consultar_total_retencoes_cgm(INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS NUMERIC AS $$
DECLARE
    inExercicio                ALIAS FOR $1;
    inCodCGM                   ALIAS FOR $2;
    inCodEntidade              ALIAS FOR $3;
    inCodConta                 ALIAS FOR $4;
    stEntidade                 ALIAS FOR $5;
    nuValor                    NUMERIC := 0.00;
    stSql                      VARCHAR;
BEGIN

    stSql := '      SELECT COALESCE(sum(empenho.ordem_pagamento_retencao.vl_retencao),0.00)
                      FROM ima'||stEntidade||'.configuracao_dirf_prestador
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
                INNER JOIN empenho.empenho
                        ON pre_empenho.exercicio = empenho.exercicio
                       AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                INNER JOIN empenho.nota_liquidacao
                        ON empenho.exercicio = nota_liquidacao.exercicio 
                       AND empenho.cod_entidade = nota_liquidacao.cod_entidade 
                       AND empenho.cod_empenho = nota_liquidacao.cod_empenho
                INNER JOIN empenho.pagamento_liquidacao
                        ON nota_liquidacao.exercicio = pagamento_liquidacao.exercicio 
                       AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade 
                       AND nota_liquidacao.cod_nota = pagamento_liquidacao.cod_nota
                INNER JOIN empenho.nota_liquidacao_paga
                        ON nota_liquidacao.exercicio = nota_liquidacao_paga.exercicio
                       AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                       AND nota_liquidacao.cod_nota = nota_liquidacao_paga.cod_nota
                INNER JOIN ( SELECT exercicio
                                , cod_nota
                                , cod_entidade
                                , max(timestamp) as timestamp
                            FROM empenho.nota_liquidacao_paga
                        GROUP BY exercicio
                                , cod_nota
                                , cod_entidade ) as max_nota_liquidacao_paga
                        ON max_nota_liquidacao_paga.exercicio = nota_liquidacao_paga.exercicio
                       AND max_nota_liquidacao_paga.cod_entidade = nota_liquidacao_paga.cod_entidade
                       AND max_nota_liquidacao_paga.cod_nota = nota_liquidacao_paga.cod_nota
                       AND max_nota_liquidacao_paga.timestamp = nota_liquidacao_paga.timestamp 
                INNER JOIN empenho.ordem_pagamento
                        ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                       AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                       AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem
                INNER JOIN empenho.ordem_pagamento_retencao
                        ON ordem_pagamento.cod_ordem = ordem_pagamento_retencao.cod_ordem
                       AND ordem_pagamento.exercicio = ordem_pagamento_retencao.exercicio
                       AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
                INNER JOIN contabilidade.plano_analitica
                        ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                       AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio
                     WHERE ordem_pagamento_retencao.exercicio = '||inExercicio||'
                       AND ordem_pagamento_retencao.cod_entidade = '||inCodEntidade||'
                       AND contabilidade.plano_analitica.cod_conta = '||inCodConta||'
                       AND pre_empenho.cgm_beneficiario = '||inCodCGM||'';

    nuValor := selectIntoNumeric(stSql);

    IF nuValor IS NULL THEN
        nuValor := 0.00;
    END IF;

    RETURN nuValor;
END;
$$ LANGUAGE 'plpgsql';
