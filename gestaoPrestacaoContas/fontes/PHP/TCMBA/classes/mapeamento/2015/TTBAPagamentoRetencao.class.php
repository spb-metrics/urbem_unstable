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
/*
 * Extensão da Classe de mapeamento Arquivo: PagRetencao.txt
 *
 * @package URBEM
 * @subpackage Mapeamento
 * @version $Id: TTBAPagamentoRetencao.class.php 63484 2015-09-01 17:16:10Z michel $
 * @author Michel Teixeira
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTBAPagamentoRetencao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        $this->setEstrutura( array() );
        $this->setEstruturaAuxiliar( array() );
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaDadosTribunal()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , opr.exercicio AS ano_criacao
                        , des.num_orgao AS cod_orgao
                        , des.num_unidade AS unidade_orcamentaria
                        , emp.cod_empenho AS num_empenho
                        , emp.cod_empenho AS num_sub_empenho
                        , conta_receita.descricao AS nome_conta_retencao
                        , REPLACE(conta_receita.cod_estrutural,'.','') AS conta_contabil
                        , COALESCE(SUM(vl.vl_lancamento),0.00) AS vl_pagamento_retencao
                        , ".$this->getDado('exercicio')."::VARCHAR||".$this->getDado('inMes')."::VARCHAR AS competencia
                        , to_char(nlp.timestamp,'dd/mm/yyyy') AS dt_pagamento_retencao
                        , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , REPLACE(plano_conta.cod_estrutural,'.','') AS conta_contabil_pagadora
                        , plano_conta.nom_conta AS nome_conta_pagadora
                        , ptdp.cod_tipo AS tipo_pagamento
                        , ptdp.num_documento AS detalhe_tipo_pagamento

                     FROM empenho.empenho                   AS emp

               INNER JOIN empenho.pre_empenho               AS pre
                       ON emp.exercicio       = pre.exercicio
                      AND emp.cod_pre_empenho = pre.cod_pre_empenho                   

               INNER JOIN empenho.nota_liquidacao           AS liq
                       ON emp.exercicio       = liq.exercicio_empenho
                      AND emp.cod_entidade    = liq.cod_entidade
                      AND emp.cod_empenho     = liq.cod_empenho                

               INNER JOIN empenho.pagamento_liquidacao      AS pli
                       ON liq.exercicio       = pli.exercicio
                      AND liq.cod_entidade    = pli.cod_entidade
                      AND liq.cod_nota        = pli.cod_nota

               INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS plnlp
                       ON plnlp.exercicio               = pli.exercicio
                      AND plnlp.cod_entidade            = pli.cod_entidade
                      AND plnlp.cod_ordem               = pli.cod_ordem
                      AND plnlp.exercicio_liquidacao    = pli.exercicio_liquidacao
                      AND plnlp.cod_nota                = pli.cod_nota

               INNER JOIN empenho.nota_liquidacao_paga      AS nlp
                       ON plnlp.cod_nota        = nlp.cod_nota
                      AND plnlp.cod_entidade    = nlp.cod_entidade
                      AND plnlp.exercicio       = nlp.exercicio
                      AND plnlp.timestamp       = nlp.timestamp

               INNER JOIN empenho.ordem_pagamento           AS opa
                       ON pli.exercicio       = opa.exercicio
                      AND pli.cod_entidade    = opa.cod_entidade
                      AND pli.cod_ordem       = opa.cod_ordem

               INNER JOIN empenho.ordem_pagamento_retencao  AS opr
                       ON opa.exercicio       = opr.exercicio
                      AND opa.cod_entidade    = opr.cod_entidade
                      AND opa.cod_ordem       = opr.cod_ordem          

               INNER JOIN contabilidade.lancamento_retencao AS lr
                       ON lr.exercicio      = opr.exercicio
                      AND lr.cod_entidade   = opr.cod_entidade
                      AND lr.cod_ordem      = opr.cod_ordem
                      AND lr.cod_plano      = opr.cod_plano
                      AND lr.sequencial     = opr.sequencial

               INNER JOIN contabilidade.lancamento          AS l
                       ON l.exercicio       = lr.exercicio
                      AND l.cod_entidade    = lr.cod_entidade
                      AND l.tipo            = lr.tipo
                      AND l.cod_lote        = lr.cod_lote
                      AND l.sequencia       = lr.sequencia

               INNER JOIN contabilidade.valor_lancamento    AS vl
                       ON l.exercicio       = vl.exercicio
                      AND l.cod_entidade    = vl.cod_entidade
                      AND l.cod_lote        = vl.cod_lote
                      AND l.tipo            = vl.tipo
                      AND l.sequencia       = vl.sequencia
                      AND vl.tipo_valor     = 'D'        

               INNER JOIN empenho.pre_empenho_despesa       AS ped
                       ON pre.exercicio       = ped.exercicio
                      AND pre.cod_pre_empenho = ped.cod_pre_empenho

               INNER JOIN orcamento.despesa                 AS des
                       ON des.exercicio       = ped.exercicio
                      AND des.cod_despesa     = ped.cod_despesa

               INNER JOIN orcamento.receita
                       ON receita.exercicio      = opr.exercicio
                      AND receita.cod_receita    = opr.cod_receita

               INNER JOIN orcamento.conta_receita
                       ON receita.exercicio      = conta_receita.exercicio
                      AND receita.cod_conta      = conta_receita.cod_conta

               INNER JOIN tesouraria.pagamento
                       ON pagamento.exercicio	    = nlp.exercicio
                      AND pagamento.cod_nota	    = nlp.cod_nota
                      AND pagamento.cod_entidade    = nlp.cod_entidade
                      AND pagamento.timestamp	    = nlp.timestamp

               INNER JOIN contabilidade.plano_analitica
                       ON pagamento.exercicio_plano = plano_analitica.exercicio
                      AND pagamento.cod_plano       = plano_analitica.cod_plano   

               INNER JOIN contabilidade.plano_conta
                       ON plano_conta.exercicio = plano_analitica.exercicio
                      AND plano_conta.cod_conta = plano_analitica.cod_conta

               INNER JOIN tcmba.pagamento_tipo_documento_pagamento AS ptdp
                       ON ptdp.cod_entidade = pagamento.cod_entidade
                      AND ptdp.exercicio    = pagamento.exercicio
                      AND ptdp.timestamp    = pagamento.timestamp
                      AND ptdp.cod_nota     = pagamento.cod_nota

                    WHERE to_char(nlp.timestamp,'yyyy')  = '".$this->getDado('exercicio')."'
                      AND to_date(to_char(nlp.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                      AND liq.cod_entidade IN (".$this->getDado('stEntidades').")

                 GROUP BY tipo_registro
                        , opr.exercicio
                        , des.num_orgao
                        , des.num_unidade
                        , emp.cod_empenho
                        , conta_receita.descricao
                        , conta_receita.cod_estrutural
                        , to_char(nlp.timestamp,'dd/mm/yyyy')
                        , plano_conta.cod_estrutural
                        , plano_conta.nom_conta
                        , ptdp.cod_tipo
                        , ptdp.num_documento

                 ORDER BY num_empenho
                        , dt_pagamento_retencao
                ";
        return $stSql;
    }

    public function __destruct() {}
}
