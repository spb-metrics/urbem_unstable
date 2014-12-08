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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 19/10/2007

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 19/10/2007

  * @author Analista: Gelson Wolvowski
  * @author Desenvolvedor: Henrique Girardi dos Santos

*/

class TTBAAdCont extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function TTBAAdCont() {}

    public function recuperaDadosAditivoContrato(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montarRecuperaDadosAditivoContrato().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montarRecuperaDadosAditivoContrato()
    {
        $stSql .= " SELECT 1 AS tipo_registro "
                  ."\n      , configuracao_entidade.valor AS unidade_gestora"
                  ."\n      , contrato.num_contrato "
                  ."\n      , contrato_aditivos.num_aditivo"
                  ."\n      , contrato_aditivos.objeto"
                  ."\n      , TO_CHAR(contrato_aditivos.dt_assinatura, 'dd/mm/yyyy') AS dt_assinatura_aditivo"
                  ."\n      , TO_CHAR(contrato_aditivos.dt_vencimento, 'dd/mm/yyyy') AS dt_vencimento_aditivo"
                  ."\n      , SUBSTR(TRIM(cgm_imprensa.nom_cgm), 1, 50) AS imprensa_oficial "
                  ."\n      , TO_CHAR(publicacao_contrato_aditivos.dt_publicacao, 'dd/mm/yyyy') AS dt_publicacao_aditivo "
                  ."\n      , contrato_aditivos.valor_contratado AS valor_contratado_aditivo"
                  ."\n      , TO_CHAR(contrato.dt_assinatura, 'yyyymm') AS competencia "
                  ."\n      , TO_CHAR(contrato.inicio_execucao, 'dd/mm/yyyy') AS inicio_execucao_contrato"
                  ."\n      , despesa.num_orgao"
                  ."\n      , despesa.num_unidade"
                  ."\n      , despesa.cod_programa"
                  ."\n      , SUBSTR(despesa.num_pao, 1, 1) AS tipo_projeto_atividade"
                  ."\n      , SUBSTR(despesa.num_pao, 2, 3) AS codigo_projeto_atividade"
                  ."\n      , contrato_aditivos.fundamentacao"
                  ."\n      , 'N' AS exame_previo"
                  ."\n      , contrato.exercicio AS ano"
                  ."\n      , fonte_recurso.descricao AS fonte_recurso"
                  ."\n      , despesa.cod_funcao"
                  ."\n		, despesa.cod_despesa"
                  ."\n      , despesa.cod_subfuncao"
                  ."\n  "
                  ."\n  FROM licitacao.contrato"
                  ."\n  "
                  ."\n  INNER JOIN licitacao.contrato_aditivos"
                  ."\n          ON contrato_aditivos.exercicio_contrato = contrato.exercicio"
                  ."\n      AND contrato_aditivos.cod_entidade = contrato.cod_entidade"
                  ."\n      AND contrato_aditivos.num_contrato = contrato.num_contrato"
                  ."\n  "
                  ."\n  INNER JOIN licitacao.publicacao_contrato_aditivos"
                  ."\n          ON contrato_aditivos.exercicio_contrato = publicacao_contrato_aditivos.exercicio_contrato"
                  ."\n      AND contrato_aditivos.cod_entidade = publicacao_contrato_aditivos.cod_entidade"
                  ."\n      AND contrato_aditivos.num_contrato = publicacao_contrato_aditivos.num_contrato"
                  ."\n      AND contrato_aditivos.exercicio = publicacao_contrato_aditivos.exercicio"
                  ."\n      AND contrato_aditivos.num_aditivo = publicacao_contrato_aditivos.num_aditivo"
                  ."\n  "
                  ."\n  INNER JOIN licitacao.publicacao_contrato"
                  ."\n          ON contrato.num_contrato = publicacao_contrato.num_contrato"
                  ."\n      AND contrato.exercicio = publicacao_contrato.exercicio"
                  ."\n      AND contrato.cod_entidade = publicacao_contrato.cod_entidade"
                  ."\n  "
                  ."\n  INNER JOIN sw_cgm AS cgm_imprensa"
                  ."\n          ON publicacao_contrato.numcgm = cgm_imprensa.numcgm"
                  ."\n  "
                  ."\n  INNER JOIN licitacao.licitacao"
                  ."\n          ON contrato.cod_licitacao = licitacao.cod_licitacao"
                  ."\n      AND contrato.cod_modalidade = licitacao.cod_modalidade"
                  ."\n      AND contrato.cod_entidade = licitacao.cod_entidade"
                  ."\n      AND contrato.exercicio = licitacao.exercicio"
                  ."\n  "
                  ."\n  INNER JOIN administracao.configuracao_entidade"
                  ."\n          ON licitacao.cod_entidade = configuracao_entidade.cod_entidade"
                  ."\n      AND licitacao.exercicio = configuracao_entidade.exercicio"
                  ."\n  "
                  ."\n  INNER JOIN compras.mapa"
                  ."\n          ON licitacao.exercicio_mapa = mapa.exercicio"
                  ."\n      AND licitacao.cod_mapa = mapa.cod_mapa"
                  ."\n  "
                  ."\n  INNER JOIN compras.objeto"
                  ."\n          ON mapa.cod_objeto = objeto.cod_objeto"
                  ."\n  "
                  ."\n  INNER JOIN compras.mapa_cotacao"
                  ."\n          ON mapa.exercicio = mapa_cotacao.exercicio_mapa"
                  ."\n      AND mapa.cod_mapa = mapa_cotacao.cod_mapa"
                  ."\n  "
                  ."\n  INNER JOIN compras.cotacao_item"
                  ."\n          ON mapa_cotacao.exercicio_cotacao = cotacao_item.exercicio"
                  ."\n      AND mapa_cotacao.cod_cotacao = cotacao_item.cod_cotacao"
                  ."\n  "
                  ."\n  INNER JOIN compras.cotacao_fornecedor_item"
                  ."\n          ON cotacao_item.exercicio = cotacao_fornecedor_item.exercicio"
                  ."\n      AND cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao"
                  ."\n      AND cotacao_item.cod_item = cotacao_fornecedor_item.cod_item"
                  ."\n      AND cotacao_item.lote = cotacao_fornecedor_item.lote"
                  ."\n  "
                  ."\n  INNER JOIN compras.julgamento_item"
                  ."\n          ON cotacao_fornecedor_item.exercicio = julgamento_item.exercicio"
                  ."\n      AND cotacao_fornecedor_item.cod_cotacao = julgamento_item.cod_cotacao"
                  ."\n      AND cotacao_fornecedor_item.cod_item = julgamento_item.cod_item"
                  ."\n      AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor"
                  ."\n      AND cotacao_fornecedor_item.lote = julgamento_item.lote"
                  ."\n  "
                  ."\n  INNER JOIN empenho.item_pre_empenho_julgamento"
                  ."\n          ON julgamento_item.exercicio = item_pre_empenho_julgamento.exercicio_julgamento"
                  ."\n      AND julgamento_item.cod_cotacao = item_pre_empenho_julgamento.cod_cotacao"
                  ."\n      AND julgamento_item.cod_item = item_pre_empenho_julgamento.cod_item"
                  ."\n      AND julgamento_item.lote = item_pre_empenho_julgamento.lote"
                  ."\n      AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor"
                  ."\n  "
                  ."\n  INNER JOIN empenho.item_pre_empenho"
                  ."\n          ON item_pre_empenho_julgamento.cod_pre_empenho = item_pre_empenho.cod_pre_empenho"
                  ."\n      AND item_pre_empenho_julgamento.exercicio = item_pre_empenho.exercicio"
                  ."\n      AND item_pre_empenho_julgamento.num_item = item_pre_empenho.num_item"
                  ."\n  "
                  ."\n  INNER JOIN empenho.pre_empenho"
                  ."\n          ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho"
                  ."\n      AND item_pre_empenho.exercicio = pre_empenho.exercicio"
                  ."\n  "
                  ."\n  INNER JOIN empenho.pre_empenho_despesa"
                  ."\n          ON pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho"
                  ."\n      AND pre_empenho.exercicio = pre_empenho_despesa.exercicio"
                  ."\n  "
                  ."\n  INNER JOIN orcamento.despesa"
                  ."\n          ON pre_empenho_despesa.exercicio = despesa.exercicio"
                  ."\n      AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa"
                  ."\n  "
                  ."\n  INNER JOIN orcamento.recurso"
                  ."\n          ON despesa.exercicio = recurso.exercicio"
                  ."\n      AND despesa.cod_recurso = recurso.cod_recurso"
                  ."\n  "
                  ."\n  INNER JOIN orcamento.fonte_recurso"
                  ."\n          ON recurso.cod_fonte = fonte_recurso.cod_fonte"
                  ."\n  "
                  ."\n  WHERE NOT EXISTS ("
                  ."\n                      SELECT 1"
                  ."\n                      FROM licitacao.contrato_anulado"
                  ."\n                      WHERE num_contrato = contrato_anulado.num_contrato"
                  ."\n                      AND contrato.exercicio = contrato_anulado.exercicio"
                  ."\n                      AND contrato.cod_entidade = contrato_anulado.cod_entidade"
                  ."\n                  )"
                  ."\n  AND NOT EXISTS ("
                  ."\n                      SELECT 1"
                  ."\n                      FROM licitacao.contrato_aditivos_anulacao"
                  ."\n                      WHERE exercicio_contrato = contrato_aditivos_anulacao.exercicio_contrato"
                  ."\n                      AND contrato_aditivos.cod_entidade = contrato_aditivos_anulacao.cod_entidade"
                  ."\n                      AND contrato_aditivos.num_contrato = contrato_aditivos_anulacao.num_contrato"
                  ."\n                      AND contrato_aditivos.exercicio = contrato_aditivos_anulacao.exercicio"
                  ."\n                      AND contrato_aditivos.num_aditivo = contrato_aditivos_anulacao.num_aditivo"
                  ."\n                  )"
                  ."\n  AND NOT EXISTS ("
                  ."\n                  SELECT 1"
                  ."\n                  FROM compras.cotacao_anulada"
                  ."\n                  WHERE cotacao_item.exercicio = cotacao_anulada.exercicio"
                  ."\n                      AND cotacao_item.cod_cotacao = cotacao_anulada.cod_cotacao"
                  ."\n                  )"
                  ."\n  AND julgamento_item.ordem = 1"
                  ."\n  AND configuracao_entidade.parametro = 'tcm_unidade_gestora'"
                  ."\n  AND NOT EXISTS ("
                  ."\n                  SELECT 1"
                  ."\n                  FROM empenho.empenho_anulado_item"
                  ."\n                  WHERE item_pre_empenho.exercicio = empenho_anulado_item.exercicio"
                  ."\n                      AND item_pre_empenho.cod_pre_empenho = empenho_anulado_item.cod_pre_empenho"
                  ."\n                      AND item_pre_empenho.num_item = empenho_anulado_item.num_item"
                  ."\n                  )";

        return $stSql;
    }

}
