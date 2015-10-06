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
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU LICENCA.txt *
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

class TTBAAdCont extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct(){
	parent::Persistente();
    }

    public function recuperaDadosAditivoContrato(&$rsRecordSet, $stCondicao =  '', $stOrdem =  '', $boTransacao = '')
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
        $stSql .=  "SELECT 1 AS tipo_registro
	                , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , contrato.num_contrato 
                        , contrato_aditivos.num_aditivo
                        , contrato_aditivos.objeto
                        , TO_CHAR(contrato_aditivos.dt_assinatura, 'DDMMYYYY') AS dt_assinatura_aditivo
                        , TO_CHAR(contrato_aditivos.dt_vencimento, 'DDMMYYYY') AS dt_vencimento_aditivo
                        , SUBSTR(TRIM(cgm_imprensa.nom_cgm), 1, 50) AS imprensa_oficial 
                        , TO_CHAR(publicacao_contrato_aditivos.dt_publicacao, 'dd/mm/yyyy') AS dt_publicacao_aditivo 
                        , contrato_aditivos.valor_contratado AS valor_contratado_aditivo
                        , TO_CHAR(contrato.dt_assinatura, 'YYYYMM') AS competencia 
                        , TO_CHAR(contrato.inicio_execucao, 'DDMMYYYY') AS inicio_execucao_contrato
                        , despesa.num_orgao
                        , despesa.num_unidade
                        , despesa.cod_programa
                        , SUBSTR(despesa.num_pao::VARCHAR, 1, 1) AS tipo_projeto_atividade
                        , SUBSTR(despesa.num_pao::VARCHAR, 2, 3) AS codigo_projeto_atividade
                        , contrato_aditivos.fundamentacao
                        , 'N' AS exame_previo
                        , contrato.exercicio AS ano
                        , fonte_recurso.descricao AS fonte_recurso
                        , despesa.cod_funcao
                  	, despesa.cod_despesa
                        , despesa.cod_subfuncao
                    
                    FROM licitacao.contrato
                    
              INNER JOIN licitacao.contrato_aditivos
                      ON contrato_aditivos.exercicio_contrato = contrato.exercicio
                     AND contrato_aditivos.cod_entidade       = contrato.cod_entidade
                     AND contrato_aditivos.num_contrato       = contrato.num_contrato
                    
              INNER JOIN licitacao.publicacao_contrato_aditivos
                      ON contrato_aditivos.exercicio_contrato = publicacao_contrato_aditivos.exercicio_contrato
                     AND contrato_aditivos.cod_entidade       = publicacao_contrato_aditivos.cod_entidade
                     AND contrato_aditivos.num_contrato       = publicacao_contrato_aditivos.num_contrato
                     AND contrato_aditivos.exercicio          = publicacao_contrato_aditivos.exercicio
                     AND contrato_aditivos.num_aditivo        = publicacao_contrato_aditivos.num_aditivo
                    
              INNER JOIN licitacao.publicacao_contrato
                      ON contrato.num_contrato = publicacao_contrato.num_contrato
                     AND contrato.exercicio    = publicacao_contrato.exercicio
                     AND contrato.cod_entidade = publicacao_contrato.cod_entidade
                    
              INNER JOIN sw_cgm AS cgm_imprensa
                      ON publicacao_contrato.numcgm = cgm_imprensa.numcgm
                    
	      INNER JOIN licitacao.contrato_compra_direta
	              ON contrato_compra_direta.num_contrato  = contrato.num_contrato
		     AND contrato_compra_direta.cod_entidade  = contrato.cod_entidade  
		     AND contrato_compra_direta.exercicio     = contrato.exercicio
		     
	      INNER JOIN compras.compra_direta
	              ON compra_direta.cod_compra_direta  = contrato_compra_direta.cod_compra_direta
		     AND compra_direta.cod_entidade       = contrato_compra_direta.cod_entidade
		     AND compra_direta.exercicio_entidade = contrato_compra_direta.exercicio_compra_direta
		     AND compra_direta.cod_modalidade     = contrato_compra_direta.cod_modalidade
              
	      INNER JOIN compras.mapa
                      ON mapa.exercicio = compra_direta.exercicio_mapa
                     AND mapa.cod_mapa  = compra_direta.cod_mapa
                    
              INNER JOIN compras.objeto
                      ON mapa.cod_objeto = objeto.cod_objeto
                    
              INNER JOIN compras.mapa_cotacao
                      ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                     AND mapa.cod_mapa  = mapa_cotacao.cod_mapa
                    
              INNER JOIN compras.cotacao_item
                      ON mapa_cotacao.exercicio_cotacao = cotacao_item.exercicio
                     AND mapa_cotacao.cod_cotacao       = cotacao_item.cod_cotacao
                    
              INNER JOIN compras.cotacao_fornecedor_item
                      ON cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
                     AND cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                     AND cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
                     AND cotacao_item.lote        = cotacao_fornecedor_item.lote
                    
              INNER JOIN compras.julgamento_item
                      ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                     AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                     AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                     AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                     AND cotacao_fornecedor_item.lote           = julgamento_item.lote
                    
              INNER JOIN empenho.item_pre_empenho_julgamento
                      ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio_julgamento
                     AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao
                     AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                     AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                     AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
                    
              INNER JOIN empenho.item_pre_empenho
                      ON item_pre_empenho_julgamento.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                     AND item_pre_empenho_julgamento.exercicio       = item_pre_empenho.exercicio
                     AND item_pre_empenho_julgamento.num_item        = item_pre_empenho.num_item
                    
              INNER JOIN empenho.pre_empenho
                      ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND item_pre_empenho.exercicio       = pre_empenho.exercicio
                    
              INNER JOIN empenho.pre_empenho_despesa
                      ON pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                     AND pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                    
              INNER JOIN orcamento.despesa
                      ON pre_empenho_despesa.exercicio   = despesa.exercicio
                     AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                    
              INNER JOIN orcamento.recurso
                      ON despesa.exercicio   = recurso.exercicio
                     AND despesa.cod_recurso = recurso.cod_recurso
                    
	      INNER JOIN orcamento.recurso_direto
	              ON recurso_direto.exercicio   = recurso.exercicio   
		     AND recurso_direto.cod_recurso = recurso.cod_recurso 
		    
              INNER JOIN orcamento.fonte_recurso 
                      ON fonte_recurso.cod_fonte = recurso_direto.cod_fonte
                    
                    WHERE NOT EXISTS (
                                        SELECT 1
                                          FROM licitacao.contrato_anulado
                                         WHERE num_contrato          = contrato_anulado.num_contrato
                                           AND contrato.exercicio    = contrato_anulado.exercicio
                                           AND contrato.cod_entidade = contrato_anulado.cod_entidade
                                    )
                      AND NOT EXISTS (
                                        SELECT 1
                                          FROM licitacao.contrato_aditivos_anulacao
                                         WHERE exercicio_contrato             = contrato_aditivos_anulacao.exercicio_contrato
                                           AND contrato_aditivos.cod_entidade = contrato_aditivos_anulacao.cod_entidade
                                           AND contrato_aditivos.num_contrato = contrato_aditivos_anulacao.num_contrato
                                           AND contrato_aditivos.exercicio    = contrato_aditivos_anulacao.exercicio
                                           AND contrato_aditivos.num_aditivo  = contrato_aditivos_anulacao.num_aditivo
                                    )
                      AND NOT EXISTS (
                                        SELECT 1
                                          FROM compras.cotacao_anulada
                                         WHERE cotacao_item.exercicio   = cotacao_anulada.exercicio
                                           AND cotacao_item.cod_cotacao = cotacao_anulada.cod_cotacao
                                    )
                    AND julgamento_item.ordem = 1
                    AND NOT EXISTS (
                                        SELECT 1
                                          FROM empenho.empenho_anulado_item
                                         WHERE item_pre_empenho.exercicio       = empenho_anulado_item.exercicio
                                           AND item_pre_empenho.cod_pre_empenho = empenho_anulado_item.cod_pre_empenho
                                           AND item_pre_empenho.num_item        = empenho_anulado_item.num_item
                                    )
		    AND contrato.exercicio = '".$this->getDado('exercicio')."'
                    AND contrato.dt_assinatura BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','DD/MM/YYYY')
                                                   AND TO_DATE('".$this->getDado('dt_final')."','DD/MM/YYYY')
                    AND contrato.cod_entidade IN (".$this->getDado('entidades').") ";

        return $stSql;
    }

}

?>