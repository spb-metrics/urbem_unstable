<?php
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
?>
<?php
/**
    * Extensão da Classe de mapeamento

    * Data de Criação   : 16/05/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Alexandre Melo

    * @ignore

    * $Id: TTGOBLP.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOBLP extends Persistente
{

    public function TTGOBLP()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );

    }

    public function montaRecuperaTodos()
    {
        $stSql = "
            SELECT
                   '10' as TipoRegistro
                 , CASE WHEN cta_credito.tipo_valor = 'C'   THEN cta_credito.tipoLancamento
                        WHEN cta_debito.tipo_valor  = 'D'   THEN cta_debito.tipoLancamento
                      END AS tipoLancamento
                 , CASE WHEN cta_credito.tipo_valor = 'C'   THEN cta_credito.tipoConta
                        WHEN cta_debito.tipo_valor  = 'D'   THEN cta_debito.tipoConta
                   END AS tipoConta
                 , CASE WHEN cta_credito.tipo_valor = 'C'   THEN cta_credito.vlSaldoAnterior
                       WHEN cta_debito.tipo_valor  = 'D'   THEN cta_debito.vlSaldoAnterior
                      END AS vlSaldoAnterior
                  , cta_credito.vlSaldoAnterior + cta_credito.vlSomaCreditos - cta_debito.vlSomaDebitos as vlSaldoExercSeg
              FROM
                   contabilidade.plano_analitica
                   LEFT JOIN ( SELECT
                                      contas_pai.tipo_lancamento as tipoLancamento
                                    , contas_pai.tipo_conta as tipoConta
                                    , valor_lancamento.vl_lancamento as vlSaldoAnterior
                                    , soma_creditos.sum as vlSomaCreditos
                                    , plano_conta.cod_conta
                                    , soma_creditos.cod_plano
                                    , soma_creditos.exercicio
                                    , soma_creditos.tipo_valor
                                 FROM
                                       contabilidade.plano_conta
                                         LEFT JOIN ( SELECT
                                                       publico.fn_mascarareduzida(plano_conta.cod_estrutural) as mascara_reduzida
                                                       , balanco_blpaaaa.exercicio
                                                       , balanco_blpaaaa.tipo_lancamento
                                                       , balanco_blpaaaa.tipo_conta
                                                     FROM
                                                           contabilidade.plano_conta
                                                       , tcmgo.balanco_blpaaaa
                                                   WHERE
                                                           balanco_blpaaaa.exercicio = plano_conta.exercicio
                                                       AND balanco_blpaaaa.cod_conta = plano_conta.cod_conta ) as contas_pai
                                               ON ( contas_pai.exercicio = plano_conta.exercicio )
                                     , contabilidade.plano_analitica
                                     , contabilidade.conta_credito
                                         JOIN contabilidade.valor_lancamento
                                         ON (     conta_credito.exercicio     = valor_lancamento.exercicio
                                               AND conta_credito.cod_entidade  = valor_lancamento.cod_entidade
                                               AND conta_credito.tipo          = valor_lancamento.tipo
                                               AND conta_credito.cod_lote      = valor_lancamento.cod_lote
                                               AND conta_credito.sequencia     = valor_lancamento.sequencia
                                              AND conta_credito.tipo_valor    = valor_lancamento.tipo_valor )
                                      LEFT JOIN ( SELECT
                                                         conta_credito.cod_plano
                                                       , conta_credito.exercicio
                                                       , conta_credito.tipo_valor
                                                       , SUM(valor_lancamento.vl_lancamento)
                                                       , valor_lancamento.cod_entidade
                                                    FROM
                                                         contabilidade.conta_credito
                                                       , contabilidade.valor_lancamento
                                                   WHERE
                                                         conta_credito.exercicio    = valor_lancamento.exercicio
                                                     AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                                                     AND conta_credito.tipo         = valor_lancamento.tipo
                                                     AND conta_credito.cod_lote     = valor_lancamento.cod_lote
                                                     AND conta_credito.sequencia    = valor_lancamento.sequencia
                                                     AND conta_credito.tipo_valor   = valor_lancamento.tipo_valor
                                                      AND conta_credito.tipo        <> 'I'
                                                  GROUP BY conta_credito.cod_plano
                                                         , conta_credito.exercicio
                                                         , conta_credito.tipo_valor
                                                         , valor_lancamento.cod_entidade ) as soma_creditos
                                             ON (     soma_creditos.cod_plano     = conta_credito.cod_plano
                                                     AND soma_creditos.exercicio     = conta_credito.exercicio
                                                     AND soma_creditos.cod_entidade  = conta_credito.cod_entidade )
                                 WHERE
                                         plano_conta.cod_estrutural like contas_pai.mascara_reduzida||'%'
                                  AND plano_analitica.cod_conta   =   plano_conta.cod_conta
                                  AND plano_analitica.exercicio   =   plano_conta.exercicio
                                  AND conta_credito.exercicio      =   plano_analitica.exercicio
                                  AND conta_credito.cod_plano      =   plano_analitica.cod_plano
                                  AND conta_credito.exercicio      =   '".$this->getDado('exercicio')."'
                                  AND conta_credito.cod_entidade   IN (".$this->getDado('cod_entidade').")
                                  AND valor_lancamento.tipo       =   'I'
                                  ) as cta_credito
                          ON (     cta_credito.cod_plano = plano_analitica.cod_plano
                                  AND cta_credito.cod_conta = plano_analitica.cod_conta
                                  AND cta_credito.exercicio = plano_analitica.exercicio  )

                   LEFT JOIN ( SELECT
                                      contas_pai.tipo_lancamento as tipoLancamento
                                    , contas_pai.tipo_conta as tipoConta
                                    , valor_lancamento.vl_lancamento as vlSaldoAnterior
                                    , soma_debitos.sum as vlSomaDebitos
                                    , plano_conta.cod_conta
                                    , soma_debitos.cod_plano
                                    , soma_debitos.exercicio
                                    , soma_debitos.tipo_valor
                                   FROM
                                       contabilidade.plano_conta
                                         LEFT JOIN ( SELECT
                                                      publico.fn_mascarareduzida(plano_conta.cod_estrutural) as mascara_reduzida
                                                       , balanco_blpaaaa.exercicio
                                                       , balanco_blpaaaa.tipo_lancamento
                                                       , balanco_blpaaaa.tipo_conta
                                                     FROM
                                                           contabilidade.plano_conta
                                                       , tcmgo.balanco_blpaaaa
                                                   WHERE
                                                          balanco_blpaaaa.exercicio = plano_conta.exercicio
                                                       AND balanco_blpaaaa.cod_conta = plano_conta.cod_conta ) as contas_pai
                                               ON ( contas_pai.exercicio = plano_conta.exercicio )
                                     , contabilidade.plano_analitica
                                     , contabilidade.conta_debito
                                         JOIN contabilidade.valor_lancamento
                                         ON (     conta_debito.exercicio     = valor_lancamento.exercicio
                                               AND conta_debito.cod_entidade  = valor_lancamento.cod_entidade
                                               AND conta_debito.tipo          = valor_lancamento.tipo
                                               AND conta_debito.cod_lote      = valor_lancamento.cod_lote
                                               AND conta_debito.sequencia     = valor_lancamento.sequencia
                                               AND conta_debito.tipo_valor    = valor_lancamento.tipo_valor )
                                      LEFT JOIN ( SELECT
                                                         conta_debito.cod_plano
                                                       , conta_debito.exercicio
                                                       , conta_debito.tipo_valor
                                                       , SUM(valor_lancamento.vl_lancamento)
                                                       , valor_lancamento.cod_entidade
                                                    FROM
                                                         contabilidade.conta_debito
                                                       , contabilidade.valor_lancamento
                                                   WHERE
                                                         conta_debito.exercicio    = valor_lancamento.exercicio
                                                     AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                                                     AND conta_debito.tipo         = valor_lancamento.tipo
                                                     AND conta_debito.cod_lote     = valor_lancamento.cod_lote
                                                     AND conta_debito.sequencia    = valor_lancamento.sequencia
                                                     AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                                                     AND conta_debito.tipo        <> 'I'
                                                  GROUP BY conta_debito.cod_plano
                                                         , conta_debito.exercicio
                                                         , conta_debito.tipo_valor
                                                         , valor_lancamento.cod_entidade ) as soma_debitos
                                             ON (     soma_debitos.cod_plano     = conta_debito.cod_plano
                                                     AND soma_debitos.exercicio     = conta_debito.exercicio
                                                     AND soma_debitos.cod_entidade  = conta_debito.cod_entidade )
                                WHERE
                                         plano_conta.cod_estrutural like contas_pai.mascara_reduzida||'%'
                                  AND plano_analitica.cod_conta   =   plano_conta.cod_conta
                                  AND plano_analitica.exercicio   =   plano_conta.exercicio
                                  AND conta_debito.exercicio      =   plano_analitica.exercicio
                                  AND conta_debito.cod_plano      =   plano_analitica.cod_plano
                                  AND conta_debito.exercicio      =   '".$this->getDado('exercicio')."'
                                  AND conta_debito.cod_entidade   IN  (".$this->getDado('cod_entidade').")
                                  AND valor_lancamento.tipo       =   'I'
                                   ) as cta_debito
                          ON (     cta_debito.cod_plano = plano_analitica.cod_plano
                                  AND cta_debito.cod_conta = plano_analitica.cod_conta
                                  AND cta_debito.exercicio = plano_analitica.exercicio )
             WHERE
                   plano_analitica.exercicio = '".$this->getDado('exercicio')."'
               AND cta_credito.tipo_valor is not null
               AND cta_debito.tipo_valor is not null
        ";

        return $stSql;

    }
}
