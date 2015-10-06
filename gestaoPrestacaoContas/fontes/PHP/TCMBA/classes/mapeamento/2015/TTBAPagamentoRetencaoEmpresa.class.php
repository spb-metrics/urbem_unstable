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
    * Extensão da Classe de mapeamento Arquivo: PagRetEmpres.txt
    * Data de Criação: 02/09/2015

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Arthur Cruz

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: $
    $Name$
    $Author: $
    $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTBAPagamentoRetencaoEmpresa extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosTribunal()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , pagamento_liquidacao.cod_ordem AS num_pagamento
                        , ordem_pagamento_retencao.exercicio AS ano_criacao
                        , despesa.num_orgao AS cod_orgao
                        , despesa.num_unidade AS unidade_orcamentaria
                        , empenho.cod_empenho AS num_empenho
                        , empenho.cod_empenho AS num_sub_empenho
                        , conta_receita.descricao AS nome_conta_retencao
                        , REPLACE(conta_receita.cod_estrutural,'.','') AS conta_contabil
                        , COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) AS vl_pagamento_retencao
                        , ".$this->getDado('stExercicio').$this->getDado('inMes')." AS competencia
                        , to_char(nota_liquidacao_paga.timestamp,'dd/mm/yyyy') AS dt_pagamento_retencao
                        , ".$this->getDado('inCodGestora')." AS unidade_gestora
                        , REPLACE(plano_conta.cod_estrutural,'.','') AS conta_contabil_pagadora
                        , plano_conta.nom_conta AS nome_conta_pagadora
                        , pagamento_tipo_documento_pagamento.cod_tipo AS tipo_pagamento
                        , pagamento_tipo_documento_pagamento.num_documento AS detalhe_tipo_pagamento

                     FROM empenho.empenho

               INNER JOIN empenho.pre_empenho
                       ON empenho.exercicio       = pre_empenho.exercicio
                      AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho                   

               INNER JOIN empenho.nota_liquidacao
                       ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                      AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                      AND empenho.cod_empenho  = nota_liquidacao.cod_empenho                

               INNER JOIN empenho.pagamento_liquidacao
                       ON nota_liquidacao.exercicio    = pagamento_liquidacao.exercicio
                      AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade
                      AND nota_liquidacao.cod_nota     = pagamento_liquidacao.cod_nota

               INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                       ON pagamento_liquidacao_nota_liquidacao_paga.exercicio            = pagamento_liquidacao.exercicio
                      AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade         = pagamento_liquidacao.cod_entidade
                      AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem            = pagamento_liquidacao.cod_ordem
                      AND pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = pagamento_liquidacao.exercicio_liquidacao
                      AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota             = pagamento_liquidacao.cod_nota

               INNER JOIN empenho.nota_liquidacao_paga
                       ON pagamento_liquidacao_nota_liquidacao_paga.cod_nota     = nota_liquidacao_paga.cod_nota
                      AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = nota_liquidacao_paga.cod_entidade
                      AND pagamento_liquidacao_nota_liquidacao_paga.exercicio    = nota_liquidacao_paga.exercicio
                      AND pagamento_liquidacao_nota_liquidacao_paga.timestamp    = nota_liquidacao_paga.timestamp

               INNER JOIN empenho.ordem_pagamento
                       ON pagamento_liquidacao.exercicio    = ordem_pagamento.exercicio
                      AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                      AND pagamento_liquidacao.cod_ordem    = ordem_pagamento.cod_ordem

               INNER JOIN empenho.ordem_pagamento_retencao
                       ON ordem_pagamento.exercicio       = ordem_pagamento_retencao.exercicio
                      AND ordem_pagamento.cod_entidade    = ordem_pagamento_retencao.cod_entidade
                      AND ordem_pagamento.cod_ordem       = ordem_pagamento_retencao.cod_ordem          

               INNER JOIN contabilidade.lancamento_retencao 
                       ON lancamento_retencao.exercicio    = ordem_pagamento_retencao.exercicio
                      AND lancamento_retencao.cod_entidade = ordem_pagamento_retencao.cod_entidade
                      AND lancamento_retencao.cod_ordem    = ordem_pagamento_retencao.cod_ordem
                      AND lancamento_retencao.cod_plano    = ordem_pagamento_retencao.cod_plano
                      AND lancamento_retencao.sequencial   = ordem_pagamento_retencao.sequencial

               INNER JOIN contabilidade.lancamento
                       ON lancamento.exercicio    = lancamento_retencao.exercicio
                      AND lancamento.cod_entidade = lancamento_retencao.cod_entidade
                      AND lancamento.tipo         = lancamento_retencao.tipo
                      AND lancamento.cod_lote     = lancamento_retencao.cod_lote
                      AND lancamento.sequencia    = lancamento_retencao.sequencia

               INNER JOIN contabilidade.valor_lancamento
                       ON lancamento.exercicio    = valor_lancamento.exercicio
                      AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                      AND lancamento.cod_lote     = valor_lancamento.cod_lote
                      AND lancamento.tipo         = valor_lancamento.tipo
                      AND lancamento.sequencia    = valor_lancamento.sequencia
                      AND valor_lancamento.tipo_valor  = 'D'        

               INNER JOIN empenho.pre_empenho_despesa
                       ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                      AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

               INNER JOIN orcamento.despesa
                       ON despesa.exercicio    = pre_empenho_despesa.exercicio
                      AND despesa.cod_despesa  = pre_empenho_despesa.cod_despesa

               INNER JOIN orcamento.receita
                       ON receita.exercicio    = ordem_pagamento_retencao.exercicio
                      AND receita.cod_receita  = ordem_pagamento_retencao.cod_receita

               INNER JOIN orcamento.conta_receita
                       ON receita.exercicio = conta_receita.exercicio
                      AND receita.cod_conta = conta_receita.cod_conta

               INNER JOIN tesouraria.pagamento
                       ON pagamento.exercicio    = nota_liquidacao_paga.exercicio
                      AND pagamento.cod_nota     = nota_liquidacao_paga.cod_nota
                      AND pagamento.cod_entidade = nota_liquidacao_paga.cod_entidade
                      AND pagamento.timestamp    = nota_liquidacao_paga.timestamp

               INNER JOIN contabilidade.plano_analitica
                       ON pagamento.exercicio_plano = plano_analitica.exercicio
                      AND pagamento.cod_plano       = plano_analitica.cod_plano   

               INNER JOIN contabilidade.plano_conta
                       ON plano_conta.exercicio = plano_analitica.exercicio
                      AND plano_conta.cod_conta = plano_analitica.cod_conta

                LEFT JOIN tcmba.pagamento_tipo_documento_pagamento
                       ON pagamento_tipo_documento_pagamento.cod_entidade = pagamento.cod_entidade
                      AND pagamento_tipo_documento_pagamento.exercicio    = pagamento.exercicio
                      AND pagamento_tipo_documento_pagamento.timestamp    = pagamento.timestamp
                      AND pagamento_tipo_documento_pagamento.cod_nota     = pagamento.cod_nota
                
               INNER JOIN orcamento.conta_despesa
                       ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                      AND conta_despesa.exercicio = pre_empenho_despesa.exercicio

                    WHERE to_char(nota_liquidacao_paga.timestamp,'yyyy')  = '".$this->getDado('stExercicio')."'
                      AND to_date(to_char(nota_liquidacao_paga.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                                                                         AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                      AND nota_liquidacao.cod_entidade IN (".$this->getDado('stEntidade').")
                      AND conta_despesa.cod_estrutural NOT LIKE ('3.1.%')

                 GROUP BY tipo_registro
                        , num_pagamento
                        , ordem_pagamento_retencao.exercicio
                        , despesa.num_orgao
                        , despesa.num_unidade
                        , empenho.cod_empenho
                        , conta_receita.descricao
                        , conta_receita.cod_estrutural
                        , dt_pagamento_retencao
                        , plano_conta.cod_estrutural
                        , plano_conta.nom_conta
                        , pagamento_tipo_documento_pagamento.cod_tipo
                        , pagamento_tipo_documento_pagamento.num_documento

                 ORDER BY num_empenho
                        , dt_pagamento_retencao ";
        return $stSql;
    }

    public function __destruct() {}
}

?>