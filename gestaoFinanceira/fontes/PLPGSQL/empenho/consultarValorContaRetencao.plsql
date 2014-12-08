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
/* empenho.fn_consultar_valor_conta_retencao
 * 
 * Data de Criação : 23/01/2009


 * @author Analista : Dagiane   
 * @author Desenvolvedor : Rafael Garbin
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valor_conta_retencao(VARCHAR,INTEGER,INTEGER,INTEGER) RETURNS NUMERIC AS $$
DECLARE
    stExercicio                ALIAS FOR $1;
    inCodEmpenho               ALIAS FOR $2;
    inCodEntidade              ALIAS FOR $3;
    inCodConta                 ALIAS FOR $4;
    nuValor                    NUMERIC := 0.00;
BEGIN

        SELECT coalesce(empenho.ordem_pagamento_retencao.vl_retencao,0.00)
          INTO nuValor
          FROM contabilidade.plano_analitica
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
         WHERE ordem_pagamento_retencao.exercicio = stExercicio
           AND ordem_pagamento_retencao.cod_entidade = inCodEntidade
           AND contabilidade.plano_analitica.cod_conta = inCodConta
           AND nota_liquidacao.cod_empenho = inCodEmpenho;

    IF nuValor IS NULL THEN
        nuValor := 0.00;
    END IF;

    RETURN nuValor;
END;
$$ LANGUAGE 'plpgsql';
