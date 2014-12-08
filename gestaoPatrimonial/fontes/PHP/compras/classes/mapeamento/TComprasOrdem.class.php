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
    * Classe de mapeamento da tabela compras.ordem
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TComprasOrdem.class.php 59612 2014-09-02 12:00:51Z gelson $
    *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.ordem
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasOrdem extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    public function TComprasOrdem()
    {
        parent::Persistente();
        $this->setTabela("compras.ordem");

        $this->setCampoCod('cod_ordem');
        $this->setComplementoChave('exercicio, cod_entidade, tipo');

        $this->AddCampo('exercicio'         ,'char'     ,true, '4'    ,true,  false );
        $this->AddCampo('cod_entidade'      ,'integer'  ,true, ''     ,true,  true  );
        $this->AddCampo('cod_ordem'         ,'integer'  ,true, ''     ,true,  false );
        $this->AddCampo('exercicio_empenho' ,'char'     ,true, '4'    ,false, true  );
        $this->AddCampo('cod_empenho'       ,'integer'  ,true, ''     ,false, true  );
        $this->AddCampo('observacao'        ,'char'     ,true, '200'  ,false, false );
        $this->AddCampo('tipo'              ,'char'     ,true, '1'    ,true , true  );
    }

    public function recuperaFornecedorOrdemCompra(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaFornecedorOrdemCompra().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaFornecedorOrdemCompra()
    {
        $stSql  ="SELECT                                                 \n";
        $stSql .="       emp.cod_empenho                                 \n";
        $stSql .="      ,pre.cgm_beneficiario                            \n";
        $stSql .="      ,pre.cod_pre_empenho                             \n";
        $stSql .="      ,emp.exercicio                                   \n";
        $stSql .="      ,cgm.nom_cgm                                     \n";
        $stSql .="FROM                                                   \n";
        $stSql .="      empenho.empenho as emp                           \n";
        $stSql .="     ,empenho.pre_empenho as pre                       \n";
        $stSql .="     ,sw_cgm as cgm                                    \n";
        $stSql .="WHERE                                                  \n";
        $stSql .="         emp.cod_pre_empenho = pre.cod_pre_empenho     \n";
        $stSql .="     AND emp.exercicio       = pre.exercicio           \n";
        $stSql .="     AND pre.cgm_beneficiario = cgm.numcgm             \n";

        if ($this->getDado('cod_entidade'))
            $stSql .="     AND emp.cod_entidade = ".$this->getDado('cod_entidade')."\n";

        if ($this->getDado('exercicio'))
            $stSql .="     AND emp.exercicio = '".$this->getDado('exercicio')."'    \n";

        if ($this->getDado('cod_empenho'))
            $stSql .="     AND emp.cod_empenho = ".$this->getDado('cod_empenho')."  \n";

        return $stSql;
    }

    public function recuperaEmpenhoLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaEmpenhoLicitacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaEmpenhoLicitacao()
    {
        $stSql .="
                SELECT
                        licitacao.cod_licitacao
                     ,  licitacao.cod_modalidade AS licitacao_cod_modalidade
                     ,  licitacao.cod_entidade AS licitacao_cod_entidade
                     ,  licitacao.exercicio AS licitacao_exercicio
                     ,	licitacao.cod_objeto as licitacao_cod_objeto
                     ,  compra_direta.cod_compra_direta
                     ,  compra_direta.cod_entidade AS compra_direta_cod_entidade
                     ,  compra_direta.cod_modalidade AS compra_direta_cod_modalidade
                     ,  compra_direta.exercicio_entidade AS compra_direta_exercicio
                     ,	compra_direta.cod_objeto as compra_direta_cod_objeto
                     ,  CASE
                            WHEN licitacao.cod_licitacao IS NOT NULL THEN
                                ( SELECT descricao FROM compras.objeto WHERE cod_objeto = licitacao.cod_objeto)::varchar
                               WHEN compra_direta.cod_compra_direta IS NOT NULL THEN
                                ( SELECT descricao FROM compras.objeto WHERE cod_objeto = compra_direta.cod_objeto)::varchar
                            ELSE
                                'Descricao Objeto'::varchar
                        END AS descricao_objeto
                     ,  fornecedor.numcgm
                     ,  fornecedor.nom_cgm
                     ,	empenho.cod_empenho
                FROM
                        empenho.empenho
                INNER JOIN
                        empenho.pre_empenho
                ON      (
                           pre_empenho.exercicio = empenho.exercicio AND
                           pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                        )
                INNER JOIN
                        empenho.item_pre_empenho_julgamento
                ON      (
                            item_pre_empenho_julgamento.exercicio       = empenho.exercicio AND
                            item_pre_empenho_julgamento.cod_pre_empenho = empenho.cod_pre_empenho AND
                            item_pre_empenho_julgamento.num_item        = 1
                        )
                INNER JOIN
                        compras.cotacao
                ON      (
                            cotacao.exercicio   = item_pre_empenho_julgamento.exercicio AND
                            cotacao.cod_cotacao = item_pre_empenho_julgamento.cod_cotacao
                        )
                INNER JOIN
                        compras.mapa_cotacao
                ON      (
                            mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao AND
                            mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                        )
                LEFT JOIN
                        compras.compra_direta
                ON      (
                            compra_direta.cod_mapa       = mapa_cotacao.cod_mapa AND
                            compra_direta.exercicio_mapa = mapa_cotacao.exercicio_mapa
                        )
                LEFT JOIN
                        licitacao.licitacao
                ON      (
                            licitacao.cod_mapa       = mapa_cotacao.cod_mapa AND
                            licitacao.exercicio_mapa = mapa_cotacao.exercicio_mapa
                        )
                INNER JOIN
                        sw_cgm AS fornecedor
                ON      (
                            pre_empenho.cgm_beneficiario = fornecedor.numcgm
                        )
                WHERE
                        empenho.cod_entidade = ".$this->getDado('cod_entidade')." AND
                        empenho.cod_empenho  = ".$this->getDado('cod_empenho')." AND
                        empenho.exercicio    = '".$this->getDado('exercicio')."'
    ";

        return $stSql;

    }

    public function recuperaItensEmpenho(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItensEmpenho().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaItensEmpenho()
    {
        $stSql.= "
            SELECT
               empenho.cod_empenho
             , pre_empenho.cod_pre_empenho
             , item_pre_empenho.num_item
             , item_pre_empenho.nom_item
             , item_pre_empenho.quantidade
             , item_pre_empenho.exercicio
             , ( item_pre_empenho.quantidade - COALESCE(ordem.quantidade,0) ) AS oc_saldo
             , COALESCE(ordem.quantidade,0) AS oc_quantidade_atendido
             , COALESCE(ordem.vl_total,0) AS oc_vl_atendido
             --, ROUND( (item_pre_empenho.vl_total - SUM(COALESCE(empenho_anulado_item.vl_anulado,0)) - (SUM(COALESCE(nota_liquidacao_item.vl_total,0)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0)))) / item_pre_empenho.quantidade,2 ) as vl_unitario
         , ROUND(item_pre_empenho.vl_total / item_pre_empenho.quantidade,2) AS vl_unitario
             , ( ROUND( (item_pre_empenho.vl_total - SUM(COALESCE(empenho_anulado_item.vl_anulado,0)) - SUM(COALESCE(nota_liquidacao_item.vl_total,0)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0)))  / item_pre_empenho.quantidade,2 ) * ( item_pre_empenho.quantidade - COALESCE(ordem.quantidade,0) ) ) AS oc_vl_total
/*
                    empenho.cod_empenho
                 ,  pre_empenho.cod_pre_empenho
                 ,  item_pre_empenho.num_item
                 ,  item_pre_empenho.nom_item
                 ,  item_pre_empenho.quantidade
                 ,  item_pre_empenho.exercicio
                 ,  COALESCE(ordem.quantidade,0) AS oc_quantidade_atendido
                 ,  COALESCE(ordem.vl_total,0) AS oc_vl_atendido
                 ,  ( item_pre_empenho.quantidade - COALESCE(ordem.quantidade,0) ) AS oc_saldo
                 ,  ROUND( (item_pre_empenho.vl_total - SUM(COALESCE(empenho_anulado_item.vl_anulado,0))) - SUM(COALESCE(nota_liquidacao_item.vl_total,0)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0)))  / item_pre_empenho.quantidade,2 ) as vl_unitario
                 ,  (ROUND( (item_pre_empenho.vl_total - SUM(COALESCE(empenho_anulado_item.vl_anulado,0)) - SUM(COALESCE(nota_liquidacao_item.vl_total,0)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0))) ) / item_pre_empenho.quantidade,2 ) * ( item_pre_empenho.quantidade - COALESCE(ordem.quantidade,0) ) ) AS oc_vl_total
*/
              FROM  empenho.empenho

        INNER JOIN  empenho.pre_empenho
                ON  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
               AND  pre_empenho.exercicio = empenho.exercicio

        INNER JOIN  empenho.item_pre_empenho
                ON  item_pre_empenho.exercicio = pre_empenho.exercicio
               AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

        INNER JOIN  empenho.item_pre_empenho_julgamento
                ON  item_pre_empenho_julgamento.exercicio = item_pre_empenho.exercicio
               AND  item_pre_empenho_julgamento.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
               AND  item_pre_empenho_julgamento.num_item = item_pre_empenho.num_item

         LEFT JOIN  empenho.empenho_anulado_item
                ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
               AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
               AND  empenho_anulado_item.num_item = item_pre_empenho.num_item

         LEFT JOIN  empenho.nota_liquidacao_item
                ON  nota_liquidacao_item.exercicio       = item_pre_empenho.exercicio
               AND  nota_liquidacao_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
               AND  nota_liquidacao_item.num_item        = item_pre_empenho.num_item

         LEFT JOIN  empenho.nota_liquidacao_item_anulado
                ON  nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
               AND  nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
               AND  nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
               AND  nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
               AND  nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
               AND  nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade

        LEFT JOIN  (	SELECT 	SUM( ordem_item.quantidade - COALESCE(ordem_item_anulacao.quantidade,0) ) AS quantidade
                            ,	SUM( ordem_item.vl_total - COALESCE(ordem_item_anulacao.vl_total,0) ) AS vl_total
                            ,  ordem.exercicio_empenho
                            ,  ordem.cod_empenho
                            ,  ordem_item.num_item
                        FROM  compras.ordem
                    INNER JOIN  compras.ordem_item
                            ON  ordem_item.exercicio = ordem.exercicio
                        AND  ordem_item.cod_entidade = ordem.cod_entidade
                        AND  ordem_item.cod_ordem = ordem.cod_ordem
                        AND  ordem_item.tipo = ordem.tipo
                        AND  ordem.tipo = '".$this->getDado('tipo')."'
                    LEFT JOIN  compras.ordem_item_anulacao
                            ON  ordem_item_anulacao.exercicio = ordem_item.exercicio
                        AND  ordem_item_anulacao.cod_entidade = ordem_item.cod_entidade
                        AND  ordem_item_anulacao.cod_ordem = ordem_item.cod_ordem
                        AND  ordem_item_anulacao.num_item = ordem_item.num_item
                        AND  ordem_item_anulacao.cod_pre_empenho = ordem_item.cod_pre_empenho
                        AND  ordem_item_anulacao.tipo = ordem_item.tipo
                        WHERE  NOT EXISTS 	(	SELECT 	1
                                                FROM  compras.ordem_anulacao
                                                WHERE  ordem_anulacao.exercicio = ordem.exercicio
                                                AND  ordem_anulacao.cod_entidade = ordem.cod_entidade
                                                AND  ordem_anulacao.cod_ordem = ordem.cod_ordem
                                                AND  ordem_anulacao.tipo = ordem.tipo
                                                AND  ordem.tipo = '".$this->getDado('tipo')."'
                                            )
                    GROUP BY 	ordem.exercicio_empenho, ordem.cod_empenho, ordem_item.num_item

                            ) AS ordem
                ON  ordem.exercicio_empenho = empenho.exercicio
               AND  ordem.cod_empenho = empenho.cod_empenho
               AND  ordem.num_item = item_pre_empenho.num_item
             WHERE  empenho.cod_empenho = ".$this->getDado('cod_empenho')."
               AND  empenho.exercicio = '".$this->getDado('exercicio')."'
               AND  empenho.cod_entidade = ".$this->getDado('cod_entidade')."
               AND  (item_pre_empenho.quantidade - COALESCE(ordem.quantidade,0)) > 0
               AND  (item_pre_empenho.vl_total - (COALESCE(nota_liquidacao_item.vl_total,0) - COALESCE(nota_liquidacao_item_anulado.vl_anulado,0)) > 0)
               AND  (ROUND( ( item_pre_empenho.vl_total - COALESCE(empenho_anulado_item.vl_anulado,0 ) ) / item_pre_empenho.quantidade,2 ) > 0)
--		       AND  NOT EXISTS 	( 	SELECT 	1
--		       						  FROM  empenho.empenho_anulado
--    	       						 WHERE  empenho_anulado.exercicio = empenho.exercicio
--    	       						   AND  empenho_anulado.cod_entidade = empenho.cod_entidade
--    	       						   AND  empenho_anulado.cod_empenho = empenho.cod_empenho
--		       					)

            GROUP BY
               empenho.cod_empenho
             , pre_empenho.cod_pre_empenho
             , item_pre_empenho.num_item
             , item_pre_empenho.nom_item
             , item_pre_empenho.quantidade
             , item_pre_empenho.exercicio
             , item_pre_empenho.vl_total
             , ordem.quantidade
             , ordem.vl_total
        ";

        return $stSql;
    }

    public function recuperaDetalheItem(&$rsRecordSet, $stFiltro='', $stOrdem='', $boTransacao='')
    {
        return $this->executaRecupera( 'montaRecuperaDetalheItem', $rsRecordSet,$stFiltro,$stOrdem,$boTransacao );
    }

    public function montaRecuperaDetalheItem()
    {
        $stSql = "
            SELECT 	item_pre_empenho.num_item
                 ,  item_pre_empenho.cod_pre_empenho
                 ,	item_pre_empenho.exercicio
                 ,  CASE WHEN ( julgada.descricao is null )
                         THEN empenho_diverso.descricao
                         ELSE julgada.descricao
                    END AS descricao
                 ,  CASE WHEN ( julgada.nom_unidade is null )
                         THEN empenho_diverso.nom_unidade
                         ELSE julgada.nom_unidade
                    END AS nom_unidade
                 ,  CASE WHEN ( julgada.nom_grandeza is null )
                         THEN empenho_diverso.nom_grandeza
                         ELSE julgada.nom_grandeza
                    END AS nom_grandeza
                 ,  CASE WHEN ( julgada.cod_item is null )
                        THEN null
                        ELSE julgada.cod_item
                    END AS cod_item
              FROM  empenho.pre_empenho
        INNER JOIN  empenho.item_pre_empenho
                ON  item_pre_empenho.exercicio = pre_empenho.exercicio
               AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
         LEFT JOIN  (	SELECT  item_pre_empenho_julgamento.exercicio
                             ,  item_pre_empenho_julgamento.cod_pre_empenho
                             ,  item_pre_empenho_julgamento.num_item
                             ,  item_pre_empenho_julgamento.cgm_fornecedor
                             ,  catalogo_item.cod_item
                             ,  catalogo_item.descricao
                             ,  unidade_medida.nom_unidade
                             ,  grandeza.nom_grandeza
                          FROM  empenho.item_pre_empenho_julgamento
                    INNER JOIN  almoxarifado.catalogo_item
                            ON	catalogo_item.cod_item = item_pre_empenho_julgamento.cod_item
                    INNER JOIN  administracao.unidade_medida
                            ON  unidade_medida.cod_unidade = catalogo_item.cod_unidade
                           AND  unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                    INNER JOIN  administracao.grandeza
                            ON  grandeza.cod_grandeza = catalogo_item.cod_grandeza
                    ) AS julgada
                ON  julgada.exercicio = item_pre_empenho.exercicio
               AND  julgada.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
               AND  julgada.num_item = item_pre_empenho.num_item
               AND  julgada.cgm_fornecedor = pre_empenho.cgm_beneficiario
         LEFT JOIN  (	SELECT 	item_pre_empenho.exercicio
                             ,  item_pre_empenho.cod_pre_empenho
                             ,  item_pre_empenho.num_item
                             ,  item_pre_empenho.nom_item AS descricao
                             ,  unidade_medida.nom_unidade
                             ,  grandeza.nom_grandeza
                          FROM  empenho.item_pre_empenho
                    INNER JOIN  administracao.unidade_medida
                            ON  unidade_medida.cod_unidade = item_pre_empenho.cod_unidade
                           AND  unidade_medida.cod_grandeza = item_pre_empenho.cod_grandeza
                    INNER JOIN  administracao.grandeza
                            ON  grandeza.cod_grandeza = item_pre_empenho.cod_grandeza
                    ) AS empenho_diverso
                ON  empenho_diverso.exercicio = item_pre_empenho.exercicio
               AND  empenho_diverso.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
               AND  empenho_diverso.num_item = item_pre_empenho.num_item
             WHERE  pre_empenho.exercicio = '".$this->getDado('exercicio')."'
               AND  pre_empenho.cod_pre_empenho = ".$this->getDado('cod_pre_empenho')."
               AND  item_pre_empenho.num_item = ".$this->getDado('num_item')."
        ";

        return $stSql;
    }

    public function recuperaItensOrdemCompra(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaItensOrdemCompra",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaItensOrdemCompra()
    {
        $stSql = "
        SELECT ordem.cod_ordem
             , item_pre_empenho.num_item
             , item_pre_empenho.nom_item
             , item_pre_empenho.cod_pre_empenho
             , item_pre_empenho.exercicio
             , item_pre_empenho.quantidade AS qtde_empenhada
             , COALESCE(quantidade_oc.quantidade,0) AS qtde_em_oc
             , (item_pre_empenho.quantidade - COALESCE(quantidade_oc.quantidade,0)) AS qtde_disponivel
             , ROUND(item_pre_empenho.vl_total / item_pre_empenho.quantidade,2) AS vl_unitario
             , ordem_item.quantidade AS qtde_da_oc
             , (ordem_item.quantidade * ROUND(item_pre_empenho.vl_total / item_pre_empenho.quantidade,2)) AS vl_total_item
             , (item_pre_empenho.quantidade - COALESCE(quantidade_oc.quantidade,0)) AS oc_saldo
          FROM compras.ordem
    INNER JOIN empenho.empenho
            ON empenho.cod_empenho = ordem.cod_empenho
           AND empenho.exercicio = ordem.exercicio_empenho
    INNER JOIN empenho.item_pre_empenho
            ON item_pre_empenho.exercicio = empenho.exercicio
           AND item_pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
     LEFT JOIN ( SELECT empenho_anulado_item.exercicio
                      , empenho_anulado_item.cod_pre_empenho
                      , empenho_anulado_item.num_item
                      , SUM(vl_anulado) AS vl_anulado
                   FROM empenho.empenho_anulado_item
               GROUP BY empenho_anulado_item.exercicio
                      , empenho_anulado_item.cod_pre_empenho
                      , empenho_anulado_item.num_item
               ) AS empenho_anulado_item
            ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
           AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
           AND empenho_anulado_item.num_item = item_pre_empenho.num_item
    INNER JOIN ( SELECT ordem_item.exercicio
                      , ordem_item.cod_entidade
                      , ordem_item.cod_ordem
                      , ordem_item.tipo
                      , ordem_item.num_item
                      , (ordem_item.vl_total - COALESCE(ordem_item_anulacao.vl_total,0)) AS vl_total
                      , (ordem_item.quantidade - COALESCE(ordem_item_anulacao.quantidade,0)) AS quantidade
                   FROM compras.ordem_item
              LEFT JOIN compras.ordem_item_anulacao
                     ON ordem_item_anulacao.exercicio = ordem_item.exercicio
                    AND ordem_item_anulacao.cod_entidade = ordem_item.cod_entidade
                    AND ordem_item_anulacao.cod_ordem = ordem_item.cod_ordem
                    AND ordem_item_anulacao.cod_pre_empenho = ordem_item.cod_pre_empenho
                    AND ordem_item_anulacao.num_item = ordem_item.num_item
               ) AS ordem_item
            ON ordem_item.exercicio = ordem.exercicio
           AND ordem_item.cod_entidade = ordem.cod_entidade
           AND ordem_item.cod_ordem = ordem.cod_ordem
           AND ordem_item.tipo = ordem.tipo
           AND ordem_item.num_item = item_pre_empenho.num_item
     LEFT JOIN ( SELECT ordem_item.exercicio
                      , ordem_item.cod_pre_empenho
                      , ordem_item.num_item
                      , SUM(ordem_item.quantidade) AS quantidade
                   FROM compras.ordem_item
                  WHERE NOT EXISTS ( SELECT 1
                                       FROM compras.ordem_item_anulacao
                                      WHERE ordem_item_anulacao.exercicio = ordem_item.exercicio
                                        AND ordem_item_anulacao.cod_entidade = ordem_item.cod_entidade
                                        AND ordem_item_anulacao.cod_ordem = ordem_item.cod_ordem
                                        AND ordem_item_anulacao.num_item = ordem_item.num_item
                                        AND ordem_item_anulacao.cod_pre_empenho = ordem_item.cod_pre_empenho
                                   )
               GROUP BY ordem_item.exercicio
                      , ordem_item.cod_pre_empenho
                      , ordem_item.num_item
               ) AS quantidade_oc
            ON quantidade_oc.exercicio = item_pre_empenho.exercicio
           AND quantidade_oc.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
           AND quantidade_oc.num_item = item_pre_empenho.num_item
            ";

            if ( $this->getDado('cod_ordem') ) {
                $stFiltro.= ' ordem.cod_ordem = '.$this->getDado('cod_ordem').' AND ';
            }
            if ( $this->getDado('exercicio') ) {
                $stFiltro.= ' ordem.exercicio = \''.$this->getDado('exercicio').'\' AND ';
            }
            if ( $this->getDado('cod_entidade') ) {
                $stFiltro.= ' ordem.cod_entidade = '.$this->getDado('cod_entidade').' AND ';
            }
            $stFiltro.= ' ordem.tipo = \''.$this->getDado('tipo').'\' AND ';

            if ($stFiltro != '') {
                $stSql.= ' WHERE '.substr( $stFiltro,0,strlen($stFiltro)-4 );
            }

        return $stSql;
    }

    public function recuperaOrdemCompraFornecedor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaOrdemCompraFornecedor().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaOrdemCompraFornecedor()
    {
        $stSql.= "
                  SELECT ordem.cod_ordem
                       , ordem.exercicio
                       , ordem.cod_autorizacao
                       , ordem.cod_empenho
                       , ordem.cod_entidade
                       , ordem.nom_entidade
                       , ordem.exercicio_empenho
                       , ordem.cgm_beneficiario
                       , ordem.nom_cgm
                       , TO_CHAR(ordem.timestamp,'dd/mm/yyyy') AS dt_ordem
                       , SUM(ordem.vl_total) as vl_total
                    FROM (	SELECT pre_empenho.cgm_beneficiario
                                 , ordem_item.exercicio
                                 , ordem_item.cod_entidade
                                 , entidade_cgm.nom_cgm AS nom_entidade
                                 , ordem_item.cod_ordem
                                 , autorizacao_empenho.cod_autorizacao
                                 , ordem.cod_empenho
                                 , ordem.exercicio_empenho
                                 , ordem.timestamp
                                 , ordem.tipo
                                 , fornecedor.nom_cgm
                                 , SUM(ordem_item.vl_total) as vl_total
                                 , SUM(ordem_item.quantidade) as quantidade

                              FROM compras.ordem

                        INNER JOIN compras.ordem_item
                                ON ordem_item.exercicio = ordem.exercicio
                               AND ordem_item.cod_entidade = ordem.cod_entidade
                               AND ordem_item.cod_ordem = ordem.cod_ordem
                               AND ordem_item.tipo = ordem.tipo

                        INNER JOIN orcamento.entidade
                                ON entidade.exercicio = ordem_item.exercicio
                               AND entidade.cod_entidade = ordem.cod_entidade

                        INNER JOIN sw_cgm AS entidade_cgm
                                ON entidade_cgm.numcgm = entidade.numcgm

                        INNER JOIN empenho.item_pre_empenho
                                   ON item_pre_empenho.exercicio = ordem.exercicio_empenho
                                  AND item_pre_empenho.cod_pre_empenho = ordem_item.cod_pre_empenho
                                  AND item_pre_empenho.num_item = ordem_item.num_item

                           INNER JOIN empenho.pre_empenho
                                ON pre_empenho.exercicio = item_pre_empenho.exercicio
                               AND pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho

                           INNER JOIN empenho.autorizacao_empenho
                                ON pre_empenho.exercicio = autorizacao_empenho.exercicio
                               AND pre_empenho.cod_pre_empenho = autorizacao_empenho.cod_pre_empenho

                        INNER JOIN empenho.empenho
                                ON empenho.exercicio       = pre_empenho.exercicio
                               AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                         LEFT JOIN empenho.empenho_anulado_item
                                ON empenho_anulado_item.exercicio       = item_pre_empenho.exercicio
                               AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                               AND empenho_anulado_item.num_item        = item_pre_empenho.num_item

                        INNER JOIN sw_cgm AS fornecedor
                                   ON fornecedor.numcgm = pre_empenho.cgm_beneficiario

                               WHERE ordem.tipo = '".$this->getDado('tipo')."'
                               AND (ROUND( ( item_pre_empenho.vl_total - COALESCE(empenho_anulado_item.vl_anulado,0 ) ) / item_pre_empenho.quantidade,2 ) > 0)

                    /*
                    -- Evita que seja feita a entrada de ordem de compra que tenha o empenho anulado.
                    AND NOT EXISTS
                                 (
                                    SELECT 1
                                      FROM empenho.empenho_anulado
                                     WHERE empenho_anulado.cod_empenho  = empenho.cod_empenho
                                       AND empenho_anulado.exercicio    = empenho.exercicio
                                       AND empenho_anulado.cod_entidade = empenho.cod_entidade
                                 )
                    */

                        GROUP BY pre_empenho.cgm_beneficiario
                               , ordem_item.exercicio
                               , ordem_item.cod_entidade
                               , entidade_cgm.nom_cgm
                               , ordem_item.cod_ordem
                               , ordem.cod_empenho
                               , autorizacao_empenho.cod_autorizacao
                               , ordem.exercicio_empenho
                               , ordem.timestamp
                               , ordem.tipo
                               , fornecedor.nom_cgm
                        ) AS ordem

                 WHERE NOT EXISTS ( SELECT 1
                                      FROM compras.ordem_anulacao
                                     WHERE ordem_anulacao.exercicio = ordem.exercicio
                                       AND ordem_anulacao.cod_entidade = ordem.cod_entidade
                                       AND ordem_anulacao.cod_ordem = ordem.cod_ordem
                                       AND ordem_anulacao.tipo = ordem.tipo
                                  )

--                 AND NOT EXISTS ( SELECT 1
--                                    FROM compras.nota_fiscal_fornecedor
--                                   WHERE nota_fiscal_fornecedor.exercicio_ordem = ordem.exercicio
--                                     AND nota_fiscal_fornecedor.cod_entidade = ordem.cod_entidade
--                                     AND nota_fiscal_fornecedor.cod_ordem = ordem.cod_ordem
--                                     AND nota_fiscal_fornecedor.tipo = ordem.tipo
--                                )

        ";
        if ($this->getDado('exercicio'))
            $stSql .=" AND ordem.exercicio = '".$this->getDado('exercicio')."' \n";
        if ($this->getDado('cod_entidade'))
            $stSql .=" AND ordem.cod_entidade IN ( ".$this->getDado('cod_entidade')." ) \n";
        if ($this->getDado('cod_ordem'))
            $stSql .=" AND ordem.cod_ordem = ".$this->getDado('cod_ordem')." \n";

        if ($this->getDado('cod_empenho')) {
            $empenho = explode('/',$this->getDado('cod_empenho'));
            $stSql .=" AND ordem.cod_empenho = ".trim($empenho[0])." \n";
            $stSql .=" AND ordem.exercicio_empenho  = '".trim($empenho[1])."' \n";
        }

            $stSql.= " GROUP BY ordem.cod_ordem
                              , ordem.exercicio
                              , ordem.cod_entidade
                              , ordem.tipo
                              , ordem.nom_entidade
                              , ordem.cod_autorizacao
                              , ordem.cod_empenho
                              , ordem.exercicio_empenho
                              , ordem.cgm_beneficiario
                              , ordem.nom_cgm
                              , ordem.timestamp

                        HAVING (
                                  SUM(ordem.quantidade)
                                 - coalesce( (
                                               SELECT SUM(lancamento_material.quantidade)
                                                 FROM compras.nota_fiscal_fornecedor
                                                    , almoxarifado.natureza_lancamento
                                                    , almoxarifado.lancamento_material
                                                    , compras.nota_fiscal_fornecedor_ordem

                                                WHERE nota_fiscal_fornecedor.exercicio_lancamento = natureza_lancamento.exercicio_lancamento
                                                  AND nota_fiscal_fornecedor.num_lancamento       = natureza_lancamento.num_lancamento
                                                  AND nota_fiscal_fornecedor.cod_natureza         = natureza_lancamento.cod_natureza
                                                  AND nota_fiscal_fornecedor.tipo_natureza        = natureza_lancamento.tipo_natureza

                                                  AND natureza_lancamento.exercicio_lancamento = lancamento_material.exercicio_lancamento
                                                  AND natureza_lancamento.num_lancamento       = lancamento_material.num_lancamento
                                                  AND natureza_lancamento.cod_natureza         = lancamento_material.cod_natureza
                                                  AND natureza_lancamento.tipo_natureza        = lancamento_material.tipo_natureza

                                                  AND nota_fiscal_fornecedor.cod_nota = nota_fiscal_fornecedor_ordem.cod_nota
                                                  AND nota_fiscal_fornecedor.cgm_fornecedor = nota_fiscal_fornecedor_ordem.cgm_fornecedor

                                                  AND nota_fiscal_fornecedor_ordem.exercicio = ordem.exercicio
                                                  AND nota_fiscal_fornecedor_ordem.tipo         = ordem.tipo
                                                  AND nota_fiscal_fornecedor_ordem.cod_entidade = ordem.cod_entidade
                                                  AND nota_fiscal_fornecedor_ordem.cod_ordem    = ordem.cod_ordem

                                            ),0.00)
                                ) > 0 ";

        return $stSql;

    }

    public function recuperaCentroCustoPorOrdemCompra(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaCentroCustoPorOrdemCompra().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaCentroCustoPorOrdemCompra()
    {
        $stSql = "
            SELECT DISTINCT centro_custo.cod_centro
                        ,   centro_custo.centro_custo
            FROM compras.ordem
            INNER JOIN  compras.ordem_item
                    ON  ordem.cod_entidade = ordem_item.cod_entidade
                    AND ordem.cod_ordem = ordem_item.cod_ordem
                    AND ordem.exercicio = ordem_item.exercicio
                    AND ordem.tipo = ordem_item.tipo

            INNER JOIN  empenho.item_pre_empenho
                    ON  item_pre_empenho.exercicio = ordem.exercicio_empenho
                    AND  item_pre_empenho.cod_pre_empenho = ordem_item.cod_pre_empenho

            INNER JOIN (SELECT  item_pre_empenho_julgamento.exercicio
                            ,  item_pre_empenho_julgamento.cod_pre_empenho
                            ,  item_pre_empenho_julgamento.num_item
                            ,  item_pre_empenho_julgamento.cod_item
                            ,  mapa_item.cod_centro
                            ,  centro_custo.descricao AS centro_custo
                         FROM  empenho.item_pre_empenho_julgamento

                        INNER JOIN  compras.julgamento_item
                                ON  julgamento_item.exercicio = item_pre_empenho_julgamento.exercicio_julgamento
                                AND  julgamento_item.cod_cotacao = item_pre_empenho_julgamento.cod_cotacao
                                AND  julgamento_item.cod_item = item_pre_empenho_julgamento.cod_item
                                AND  julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
                                AND  julgamento_item.lote = item_pre_empenho_julgamento.lote

                        INNER JOIN  compras.mapa_cotacao
                                ON  mapa_cotacao.cod_cotacao = julgamento_item.cod_cotacao
                                AND  mapa_cotacao.exercicio_cotacao = julgamento_item.exercicio

                        INNER JOIN  compras.mapa_item
                                ON  mapa_item.exercicio = mapa_cotacao.exercicio_mapa
                                AND  mapa_item.cod_mapa = mapa_cotacao.cod_mapa
                                AND  mapa_item.cod_item = julgamento_item.cod_item

                        INNER JOIN  almoxarifado.centro_custo
                                ON  centro_custo.cod_centro = mapa_item.cod_centro

                        WHERE  julgamento_item.ordem = 1 ) AS centro_custo
                    ON  centro_custo.exercicio = item_pre_empenho.exercicio
                    AND centro_custo.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                    AND centro_custo.num_item = item_pre_empenho.num_item
            WHERE ordem.cod_ordem = ".$this->getDado('cod_ordem')."
            AND   ordem.tipo =     '".$this->getDado('tipo')."' ";

        return $stSql;
    }

    public function recuperaMarcaPorOrdemCompra(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMarcaPorOrdemCompra().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaMarcaPorOrdemCompra()
    {
        $stSql = "
            SELECT DISTINCT centro_custo.cod_marca
                        ,   centro_custo.marca
            FROM compras.ordem
            INNER JOIN  compras.ordem_item
                    ON  ordem.cod_entidade = ordem_item.cod_entidade
                    AND ordem.cod_ordem = ordem_item.cod_ordem
                    AND ordem.exercicio = ordem_item.exercicio
                    AND ordem.tipo = ordem_item.tipo

            INNER JOIN  empenho.item_pre_empenho
                    ON  item_pre_empenho.exercicio = ordem.exercicio_empenho
                    AND  item_pre_empenho.cod_pre_empenho = ordem_item.cod_pre_empenho

            INNER JOIN (SELECT  item_pre_empenho_julgamento.exercicio
                            ,  item_pre_empenho_julgamento.cod_pre_empenho
                            ,  item_pre_empenho_julgamento.num_item
                            ,  item_pre_empenho_julgamento.cod_item
                            ,  cotacao_fornecedor_item.cod_marca
                            ,  marca.descricao AS marca

                        FROM  empenho.item_pre_empenho_julgamento

                        INNER JOIN  compras.julgamento_item
                                ON  julgamento_item.exercicio = item_pre_empenho_julgamento.exercicio_julgamento
                                AND  julgamento_item.cod_cotacao = item_pre_empenho_julgamento.cod_cotacao
                                AND  julgamento_item.cod_item = item_pre_empenho_julgamento.cod_item
                                AND  julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
                                AND  julgamento_item.lote = item_pre_empenho_julgamento.lote

                        INNER JOIN  compras.cotacao_fornecedor_item
                                ON  julgamento_item.exercicio = cotacao_fornecedor_item.exercicio
                                AND  julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                                AND  julgamento_item.cod_item = cotacao_fornecedor_item.cod_item
                                AND  julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                                AND  julgamento_item.lote = cotacao_fornecedor_item.lote

                        INNER JOIN  almoxarifado.catalogo_item_marca
                                ON  cotacao_fornecedor_item.cod_item = catalogo_item_marca.cod_item
                                AND cotacao_fornecedor_item.cod_marca = catalogo_item_marca.cod_marca

                        INNER JOIN  almoxarifado.marca
                                ON  marca.cod_marca = catalogo_item_marca.cod_marca

                        WHERE  julgamento_item.ordem = 1 ) AS centro_custo
                    ON  centro_custo.exercicio = item_pre_empenho.exercicio
                    AND centro_custo.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                    AND centro_custo.num_item = item_pre_empenho.num_item
                WHERE ordem.cod_ordem = ".$this->getDado('cod_ordem')."
                AND   ordem.tipo =     '".$this->getDado('tipo')."'" ;

        return $stSql;
    }

    /*
     * Retornar Valor Atendido
     *
     *
     */

    public function recuperaVlAtendidoOrdemCompra(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaVlAtendidoOrdemCompra().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaVlAtendidoOrdemCompra()
    {
        $stSql.= " SELECT SUM(lancamento_material.valor_mercado) as vl_total_atendido
                     FROM compras.ordem

               INNER JOIN compras.nota_fiscal_fornecedor_ordem
                       ON nota_fiscal_fornecedor_ordem.cod_ordem = ordem.cod_ordem
                      AND nota_fiscal_fornecedor_ordem.exercicio = ordem.exercicio_empenho
                      AND nota_fiscal_fornecedor_ordem.cod_entidade = ordem.cod_entidade
                      AND nota_fiscal_fornecedor_ordem.tipo = ordem.tipo

               INNER JOIN compras.nota_fiscal_fornecedor
                       ON nota_fiscal_fornecedor_ordem.cod_nota = nota_fiscal_fornecedor.cod_nota
                      AND nota_fiscal_fornecedor_ordem.cgm_fornecedor = nota_fiscal_fornecedor.cgm_fornecedor

               INNER JOIN almoxarifado.natureza_lancamento
                       ON natureza_lancamento.exercicio_lancamento = nota_fiscal_fornecedor.exercicio_lancamento
                      AND natureza_lancamento.num_lancamento = nota_fiscal_fornecedor.num_lancamento
                      AND natureza_lancamento.cod_natureza = nota_fiscal_fornecedor.cod_natureza
                      AND natureza_lancamento.tipo_natureza = nota_fiscal_fornecedor.tipo_natureza

               INNER JOIN almoxarifado.lancamento_material
                       ON lancamento_material.exercicio_lancamento = natureza_lancamento.exercicio_lancamento
                      AND lancamento_material.num_lancamento = natureza_lancamento.num_lancamento
                      AND lancamento_material.cod_natureza = natureza_lancamento.cod_natureza
                      AND lancamento_material.tipo_natureza = natureza_lancamento.tipo_natureza

               WHERE ordem.tipo = 'C' ";

        if ($this->getDado('exercicio'))
            $stSql .=" AND ordem.exercicio = '".$this->getDado('exercicio')."' \n";
        if ($this->getDado('cod_entidade'))
            $stSql .=" AND ordem.cod_entidade IN ( ".$this->getDado('cod_entidade')." ) \n";
        if ($this->getDado('cod_ordem'))
            $stSql .=" AND ordem.cod_ordem = ".$this->getDado('cod_ordem')." \n";

        if ($this->getDado('cod_empenho')) {
            $empenho = explode('/',$this->getDado('cod_empenho'));
            $stSql .=" AND ordem.cod_empenho = ".trim($empenho[0])." \n";
            $stSql .=" AND ordem.exercicio_empenho  = '".trim($empenho[1])."' \n";
        }

        $stSql .= " GROUP BY ordem.cod_ordem, ordem.exercicio";

        return $stSql;
    }

    /**
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
        * @author Henrique Girardi dos Santos
        * @date 29/10/2007
    */
    public function recuperaListagemEmpenho(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaListagemEmpenho",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
        * Monta o sql onde retorna a listagem dos dados para a inclusão de Ordem de Compra
        * @access Private
        * @return string $stSql
        * @author Henrique Girardi dos Santos
        * @date 29/10/2007
    */
    public function montaRecuperaListagemEmpenho()
    {
        $stSql  = "
          SELECT tabela.*
            FROM (
                SELECT /*DISTINCT*/ empenho.cod_empenho
                            , empenho.exercicio AS exercicio_empenho
                            , empenho.cod_entidade
                            , sw_cgm_pessoa_juridica.nom_fantasia AS entidade
                            , pre_empenho_despesa.cod_despesa
                            , TO_CHAR(empenho.dt_empenho, 'dd/mm/yyyy') AS dt_empenho
                            , TO_CHAR(empenho.dt_vencimento, 'dd/mm/yyyy') AS dt_vencimento
                            , (CASE WHEN licitacao.cod_licitacao IS NOT NULL THEN licitacao.cod_licitacao
                                ELSE compra_direta.cod_compra_direta END ) AS codigo
                            , (CASE WHEN licitacao.exercicio IS NOT NULL THEN licitacao.exercicio END ) as exercicio
                            , (CASE WHEN licitacao.cod_objeto IS NOT NULL THEN licitacao.cod_objeto
                                ELSE compra_direta.cod_objeto END) AS cod_objeto
                            , (CASE WHEN objeto_licitacao.descricao IS NOT NULL THEN objeto_licitacao.descricao
                                ELSE objeto_compra_direta.descricao END) AS descricao_objeto
                            , (CASE WHEN licitacao.cod_modalidade IS NOT NULL THEN licitacao.cod_modalidade
                                ELSE compra_direta.cod_modalidade END) AS cod_modalidade
                            , (CASE WHEN modalidade_licitacao.descricao IS NOT NULL THEN modalidade_licitacao.descricao
                                ELSE modalidade_compra_direta.descricao END) AS descricao_modalidade
                            , (CASE WHEN edital.condicoes_pagamento IS NOT NULL THEN edital.condicoes_pagamento
                                ELSE compra_direta.condicoes_pagamento END) AS condicoes_pagamento
                            , (CASE WHEN edital.local_entrega_material IS NOT NULL THEN edital.local_entrega_material
                                ELSE '' END) AS local_entrega_material
                            , (CASE WHEN licitacao.cod_licitacao IS NOT NULL THEN 'licitacao'
                                ELSE 'compra_direta' END ) AS tipo
                            , julgamento_item.cgm_fornecedor
                            , sw_cgm.nom_cgm AS fornecedor
                            , empenho.fn_consultar_valor_empenhado
                              (
                                 pre_empenho.exercicio
                                ,empenho.cod_empenho
                                ,empenho.cod_entidade
                              ) AS vl_empenhado,
                              empenho.fn_consultar_valor_empenhado_anulado
                              (
                                    pre_empenho.exercicio
                                   ,empenho.cod_empenho
                                   ,empenho.cod_entidade
                              ) AS vl_empenhado_anulado,
                              empenho.fn_consultar_valor_liquidado
                              (
                                    pre_empenho.exercicio
                                   ,empenho.cod_empenho
                                   ,empenho.cod_entidade
                              ) AS vl_liquidado,
                              empenho.fn_consultar_valor_liquidado_anulado
                              (
                                    pre_empenho.exercicio
                                   ,empenho.cod_empenho
                                   ,empenho.cod_entidade
                              ) AS vl_liquidado_anulado
                FROM empenho.empenho

                INNER JOIN orcamento.entidade
                        ON empenho.cod_entidade = entidade.cod_entidade
                        AND empenho.exercicio = entidade.exercicio

                INNER JOIN sw_cgm_pessoa_juridica
                        ON entidade.numcgm = sw_cgm_pessoa_juridica.numcgm

                INNER JOIN empenho.pre_empenho
                        ON empenho.exercicio = pre_empenho.exercicio
                        AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                INNER JOIN empenho.pre_empenho_despesa
                        ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                        AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                INNER JOIN empenho.autorizacao_empenho
                        ON  autorizacao_empenho.exercicio       = pre_empenho.exercicio
                        AND autorizacao_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                INNER JOIN empenho.item_pre_empenho
                        ON item_pre_empenho.exercicio = pre_empenho.exercicio
                        AND item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                INNER JOIN empenho.item_pre_empenho_julgamento
                        ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
                        AND item_pre_empenho.exercicio = item_pre_empenho_julgamento.exercicio
                        AND item_pre_empenho.num_item = item_pre_empenho_julgamento.num_item

                INNER JOIN almoxarifado.catalogo_item
                        ON item_pre_empenho_julgamento.cod_item = catalogo_item.cod_item
                        AND catalogo_item.cod_tipo ".( $this->getDado('tipo')=='C'?' <> 3 ':' = 3 ' )."

                INNER JOIN compras.julgamento_item
                        ON item_pre_empenho_julgamento.exercicio_julgamento = julgamento_item.exercicio
                        AND item_pre_empenho_julgamento.cod_cotacao = julgamento_item.cod_cotacao
                        AND item_pre_empenho_julgamento.cod_item = julgamento_item.cod_item
                        AND item_pre_empenho_julgamento.lote = julgamento_item.lote
                        AND item_pre_empenho_julgamento.cgm_fornecedor = julgamento_item.cgm_fornecedor

                INNER JOIN sw_cgm
                        ON julgamento_item.cgm_fornecedor = sw_cgm.numcgm

                INNER JOIN compras.cotacao
                        ON item_pre_empenho_julgamento.cod_cotacao = cotacao.cod_cotacao
                        AND item_pre_empenho_julgamento.exercicio = cotacao.exercicio

                INNER JOIN compras.mapa_cotacao
                        ON cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
                        AND cotacao.exercicio = mapa_cotacao.exercicio_cotacao

                INNER JOIN compras.mapa
                        ON mapa_cotacao.exercicio_mapa = mapa.exercicio
                        AND mapa_cotacao.cod_mapa = mapa.cod_mapa

                LEFT JOIN compras.compra_direta
                        ON mapa.exercicio = compra_direta.exercicio_mapa
                        AND mapa.cod_mapa = compra_direta.cod_mapa

                LEFT JOIN licitacao.licitacao
                        ON mapa.exercicio = licitacao.exercicio_mapa
                        AND mapa.cod_mapa = licitacao.cod_mapa

                LEFT JOIN licitacao.edital
                        ON  licitacao.cod_licitacao = edital.cod_licitacao
                        AND licitacao.cod_modalidade = edital.cod_modalidade
                        AND licitacao.cod_entidade = edital.cod_entidade
                        AND licitacao.exercicio = edital.exercicio_licitacao

                LEFT JOIN licitacao.participante
                        ON licitacao.cod_licitacao = participante.cod_licitacao
                        AND licitacao.cod_modalidade = participante.cod_modalidade
                        AND licitacao.cod_entidade = participante.cod_entidade
                        AND licitacao.exercicio = participante.exercicio

                LEFT JOIN compras.objeto AS objeto_licitacao
                        ON licitacao.cod_objeto = objeto_licitacao.cod_objeto

                LEFT JOIN compras.objeto AS objeto_compra_direta
                        ON compra_direta.cod_objeto = objeto_compra_direta.cod_objeto

                LEFT JOIN compras.modalidade AS modalidade_licitacao
                        ON licitacao.cod_modalidade = modalidade_licitacao.cod_modalidade

                LEFT JOIN compras.modalidade AS modalidade_compra_direta
                        ON compra_direta.cod_modalidade = modalidade_compra_direta.cod_modalidade

                WHERE
                    -- NÃO PODE HAVER UMA COTAÇÃO ANULADA
                    NOT EXISTS (
                                   SELECT 1
                                     FROM compras.cotacao_anulada
                                    WHERE cotacao.exercicio = cotacao_anulada.exercicio
                                      AND cotacao.cod_cotacao = cotacao_anulada.cod_cotacao
                               )

                    AND (
                        NOT EXISTS (
                                    SELECT 1
                                    FROM compras.compra_direta_anulacao
                                    WHERE compra_direta_anulacao.cod_modalidade = compra_direta.cod_modalidade
                                      AND compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                                      AND compra_direta_anulacao.cod_entidade = compra_direta.cod_entidade
                                      AND compra_direta_anulacao.cod_compra_direta = compra_direta.cod_compra_direta

                                    )
                        OR NOT EXISTS (
                                    SELECT 1
                                     FROM licitacao.licitacao_anulada
                                    WHERE licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                      AND licitacao_anulada.exercicio = licitacao.exercicio
                                      AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                      AND licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                                    )
                        )

                    AND julgamento_item.ordem = 1
                    ".$this->getDado('stFiltro')."

                    GROUP BY      item_pre_empenho.cod_pre_empenho
                                , empenho.cod_empenho
                                , empenho.exercicio
                                , empenho.cod_entidade
                                , sw_cgm_pessoa_juridica.nom_fantasia
                                , pre_empenho_despesa.cod_despesa
                                , TO_CHAR(empenho.dt_empenho, 'dd/mm/yyyy')
                                , TO_CHAR(empenho.dt_vencimento, 'dd/mm/yyyy')
                                , licitacao.cod_licitacao
                                , compra_direta.cod_compra_direta
                                , licitacao.exercicio
                                , licitacao.cod_objeto
                                , compra_direta.cod_objeto
                                , objeto_licitacao.descricao
                                , licitacao.cod_modalidade
                                , modalidade_licitacao.descricao
                                , edital.condicoes_pagamento
                                , edital.local_entrega_material
                                , licitacao.cod_licitacao
                                , julgamento_item.cgm_fornecedor
                                , objeto_compra_direta.descricao
                                , compra_direta.cod_modalidade
                                , modalidade_compra_direta.descricao
                                , compra_direta.condicoes_pagamento
                                , sw_cgm.nom_cgm
                                , pre_empenho.exercicio

                -- NÃO PODE LISTAR OS EMPENHOS QUE JÁ ESTÃO COM TODOS OS ITENS USADOS POR ALGUMA ORDEM DE COMPRA
                    HAVING (
                      SELECT  SUM(COALESCE(item_pre_empenho.quantidade,0))
                              -
                               COALESCE(( SELECT SUM(ordem_item.quantidade)
                                    FROM compras.ordem
                              INNER JOIN compras.ordem_item
                                      ON ordem_item.exercicio = ordem.exercicio
                                     AND ordem_item.cod_entidade = ordem.cod_entidade
                                     AND ordem_item.cod_ordem = ordem.cod_ordem
                                     AND ordem_item.tipo = ordem.tipo
                                   WHERE NOT EXISTS( SELECT 1
                                                       FROM compras.ordem_item_anulacao
                                                      WHERE ordem_item_anulacao.exercicio = ordem_item.exercicio
                                                        AND ordem_item_anulacao.cod_entidade = ordem_item.cod_entidade
                                                        AND ordem_item_anulacao.cod_ordem = ordem_item.cod_ordem
                                                        AND ordem_item_anulacao.num_item = ordem_item.num_item
                                                        AND ordem_item_anulacao.cod_pre_empenho = ordem_item.cod_pre_empenho
                                                        AND ordem_item_anulacao.tipo = ordem_item.tipo
                                                   )
                                     AND ordem.exercicio_empenho = empenho.exercicio
                                     AND ordem.cod_entidade = empenho.cod_entidade
                                     AND ordem.cod_empenho = empenho.cod_empenho
                                     AND ordem.tipo = '".$this->getDado('tipo')."'

                               ),0)

                    ) > 0

                -- PARA NÃO PEGAR OS EMPENHOS TOTALMENTE ANULADOS
                    AND (
                        sum(item_pre_empenho.vl_total)
                        - coalesce( (
                                    SELECT sum(empenho_anulado_item.vl_anulado)
                                    FROM  empenho.empenho_anulado_item
                                    WHERE empenho_anulado_item.exercicio        = empenho.exercicio
                                      AND empenho_anulado_item.cod_entidade     = empenho.cod_entidade
                                      AND empenho_anulado_item.cod_empenho      = empenho.cod_empenho
                                ),0.00)
                    ) > 0.00

                    ORDER BY empenho.cod_empenho DESC
                           , empenho.exercicio
                           , empenho.cod_entidade
                           , empenho.cod_empenho

             ) as tabela

             WHERE  ( tabela.vl_empenhado -  tabela.vl_empenhado_anulado ) > ( tabela.vl_liquidado - tabela.vl_liquidado_anulado );";

        return $stSql;
    }

    /**
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
        * @author Henrique Girardi dos Santos
        * @date 29/10/2007
    */
    public function recuperaListagemOrdemCompra(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaListagemOrdemCompra($stFiltro).$stOrder;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /**
        * Monta o sql onde retorna a listagem dos dados para a alteração e exclusão de Ordem de Compra
        * @access Private
        * @return string $stSql
        * @author Henrique Girardi dos Santos
        * @date 29/10/2007
    */
    public function montaRecuperaListagemOrdemCompra($stFiltro)
    {
        $stSql  = "
                 SELECT empenho.cod_empenho
                      , empenho.exercicio AS exercicio_empenho
                      , empenho.cod_entidade
                      , sw_cgm_pessoa_juridica.nom_fantasia AS entidade
                      , pre_empenho_despesa.cod_despesa
                      , TO_CHAR(empenho.dt_empenho, 'dd/mm/yyyy') AS dt_empenho
                      , (CASE WHEN licitacao.cod_licitacao IS NOT NULL THEN licitacao.cod_licitacao
                              ELSE compra_direta.cod_compra_direta END ) AS codigo
                      , (CASE WHEN licitacao.exercicio IS NOT NULL THEN licitacao.exercicio END ) as exercicio
                      , (CASE WHEN licitacao.cod_objeto IS NOT NULL THEN licitacao.cod_objeto
                              ELSE compra_direta.cod_objeto END) AS cod_objeto
                      , (CASE WHEN objeto_licitacao.descricao IS NOT NULL THEN objeto_licitacao.descricao
                              ELSE objeto_compra_direta.descricao END) AS descricao_objeto
                      , (CASE WHEN licitacao.cod_modalidade IS NOT NULL THEN licitacao.cod_modalidade
                              ELSE compra_direta.cod_modalidade END) AS cod_modalidade
                      , (CASE WHEN modalidade_licitacao.descricao IS NOT NULL THEN modalidade_licitacao.descricao
                              ELSE modalidade_compra_direta.descricao END) AS descricao_modalidade
                      , (CASE WHEN edital.condicoes_pagamento IS NOT NULL THEN edital.condicoes_pagamento
                              ELSE compra_direta.condicoes_pagamento END) AS condicoes_pagamento
                      , (CASE WHEN edital.local_entrega_material IS NOT NULL THEN edital.local_entrega_material
                              ELSE '' END) AS local_entrega_material
                      , (CASE WHEN licitacao.cod_licitacao IS NOT NULL THEN 'licitacao'
                              ELSE 'compra_direta' END ) AS tipo
                      , julgamento_item.cgm_fornecedor
                      , sw_cgm.nom_cgm AS fornecedor
                      , ordem.cod_ordem
                      , ordem.exercicio AS exercicio_ordem
                      , TO_CHAR(ordem.timestamp, 'dd/mm/yyyy') AS timestamp
                      , ordem.tipo as tipo_ordem
                 FROM empenho.empenho

                INNER JOIN compras.ordem
                        ON ordem.exercicio_empenho = empenho.exercicio
                       AND ordem.cod_entidade = empenho.cod_entidade
                       AND ordem.cod_empenho = empenho.cod_empenho

                INNER JOIN orcamento.entidade
                        ON empenho.cod_entidade = entidade.cod_entidade
                       AND empenho.exercicio = entidade.exercicio

                INNER JOIN sw_cgm_pessoa_juridica
                        ON entidade.numcgm = sw_cgm_pessoa_juridica.numcgm

                 LEFT JOIN empenho.autorizacao_empenho
                        ON autorizacao_empenho.exercicio = empenho.exercicio
                       AND autorizacao_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                INNER JOIN empenho.pre_empenho
                        ON empenho.exercicio = pre_empenho.exercicio
                       AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                INNER JOIN empenho.pre_empenho_despesa
                        ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                       AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                INNER JOIN empenho.item_pre_empenho
                        ON item_pre_empenho.exercicio = pre_empenho.exercicio
                       AND item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                INNER JOIN empenho.item_pre_empenho_julgamento
                        ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
                       AND item_pre_empenho.exercicio = item_pre_empenho_julgamento.exercicio
                       AND item_pre_empenho.num_item = item_pre_empenho_julgamento.num_item

                INNER JOIN compras.julgamento_item
                        ON item_pre_empenho_julgamento.exercicio_julgamento = julgamento_item.exercicio
                       AND item_pre_empenho_julgamento.cod_cotacao = julgamento_item.cod_cotacao
                       AND item_pre_empenho_julgamento.cod_item = julgamento_item.cod_item
                       AND item_pre_empenho_julgamento.lote = julgamento_item.lote
                       AND item_pre_empenho_julgamento.cgm_fornecedor = julgamento_item.cgm_fornecedor

                INNER JOIN sw_cgm
                        ON julgamento_item.cgm_fornecedor = sw_cgm.numcgm

                INNER JOIN compras.cotacao
                        ON item_pre_empenho_julgamento.cod_cotacao = cotacao.cod_cotacao
                        AND item_pre_empenho_julgamento.exercicio = cotacao.exercicio

                INNER JOIN compras.mapa_cotacao
                        ON cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
                        AND cotacao.exercicio = mapa_cotacao.exercicio_cotacao

                INNER JOIN compras.mapa
                        ON mapa_cotacao.exercicio_mapa = mapa.exercicio
                        AND mapa_cotacao.cod_mapa = mapa.cod_mapa

                LEFT JOIN compras.compra_direta
                        ON mapa.exercicio = compra_direta.exercicio_mapa
                        AND mapa.cod_mapa = compra_direta.cod_mapa

                LEFT JOIN licitacao.licitacao
                        ON mapa.exercicio = licitacao.exercicio_mapa
                        AND mapa.cod_mapa = licitacao.cod_mapa

                LEFT JOIN licitacao.edital
                        ON  licitacao.cod_licitacao = edital.cod_licitacao
                        AND licitacao.cod_modalidade = edital.cod_modalidade
                        AND licitacao.cod_entidade = edital.cod_entidade
                        AND licitacao.exercicio = edital.exercicio_licitacao

                LEFT JOIN licitacao.participante
                        ON licitacao.cod_licitacao = participante.cod_licitacao
                        AND licitacao.cod_modalidade = participante.cod_modalidade
                        AND licitacao.cod_entidade = participante.cod_entidade
                        AND licitacao.exercicio = participante.exercicio

                LEFT JOIN compras.objeto AS objeto_licitacao
                        ON licitacao.cod_objeto = objeto_licitacao.cod_objeto

                LEFT JOIN compras.objeto AS objeto_compra_direta
                        ON compra_direta.cod_objeto = objeto_compra_direta.cod_objeto

                LEFT JOIN compras.modalidade AS modalidade_licitacao
                        ON licitacao.cod_modalidade = modalidade_licitacao.cod_modalidade

                LEFT JOIN compras.modalidade AS modalidade_compra_direta
                        ON compra_direta.cod_modalidade = modalidade_compra_direta.cod_modalidade

                LEFT JOIN  empenho.empenho_anulado_item
                       ON  empenho_anulado_item.exercicio       = item_pre_empenho.exercicio
                      AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                      AND  empenho_anulado_item.num_item        = item_pre_empenho.num_item

                WHERE
                      NOT EXISTS (
                                      SELECT 1
                                        FROM compras.compra_direta_anulacao
                                       WHERE compra_direta_anulacao.cod_modalidade = compra_direta.cod_modalidade
                                        AND compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                                        AND compra_direta_anulacao.cod_entidade = compra_direta.cod_entidade
                                        AND compra_direta_anulacao.cod_compra_direta = compra_direta.cod_compra_direta
                                 )

                        AND NOT EXISTS (
                                        SELECT 1
                                        FROM licitacao.licitacao_anulada
                                        WHERE licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                                              AND licitacao_anulada.exercicio = licitacao.exercicio
                                                              AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                                              AND licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                                        )

                ";
                if ($this->getDado('acao') != 'consultar' && $this->getDado('acao') != 'reemitir') {
                    $stSql .= "
                    /*
                    AND NOT EXISTS (
                                    SELECT 1
                                    FROM empenho.empenho_anulado
                                    WHERE empenho.exercicio = empenho_anulado.exercicio
                                        AND empenho.cod_entidade = empenho_anulado.cod_entidade
                                        AND empenho.cod_empenho = empenho_anulado.cod_empenho
                                    )
                    */
                    AND NOT EXISTS (
                                    SELECT 1
                                    FROM compras.cotacao_anulada
                                    WHERE cotacao.exercicio = cotacao_anulada.exercicio
                                        AND cotacao.cod_cotacao = cotacao_anulada.cod_cotacao
                                    )

                    AND NOT EXISTS (
                                     SELECT 1
                                       FROM compras.nota_fiscal_fornecedor
                                      INNER JOIN compras.nota_fiscal_fornecedor_ordem
                                              ON nota_fiscal_fornecedor_ordem.cod_nota = nota_fiscal_fornecedor.cod_nota
                                             AND nota_fiscal_fornecedor_ordem.cgm_fornecedor = nota_fiscal_fornecedor.cgm_fornecedor
                                      WHERE ordem.exercicio = nota_fiscal_fornecedor_ordem.exercicio
                                        AND ordem.cod_entidade = nota_fiscal_fornecedor_ordem.cod_entidade
                                        AND ordem.cod_ordem = nota_fiscal_fornecedor_ordem.cod_ordem
                                        AND ordem.tipo = nota_fiscal_fornecedor_ordem.tipo
                                   )

                    ";
                }

        $stSql .= "AND julgamento_item.ordem = 1
            AND ordem.tipo = '".$this->getDado('tipo')."'";

        $stSql .= " AND  (ROUND( ( item_pre_empenho.vl_total - COALESCE(empenho_anulado_item.vl_anulado,0 ) ) / item_pre_empenho.quantidade,2 ) > 0) ";

        $stSql .= $stFiltro."
        GROUP BY empenho.cod_empenho
                , empenho.exercicio
                , empenho.cod_entidade
                , sw_cgm_pessoa_juridica.nom_fantasia
                , pre_empenho_despesa.cod_despesa
                , TO_CHAR(empenho.dt_empenho, 'dd/mm/yyyy')
                , (CASE WHEN licitacao.cod_licitacao IS NOT NULL THEN licitacao.cod_licitacao
                    ELSE compra_direta.cod_compra_direta END )
                , (CASE WHEN licitacao.exercicio IS NOT NULL THEN licitacao.exercicio END )
                , (CASE WHEN licitacao.cod_objeto IS NOT NULL THEN licitacao.cod_objeto
                    ELSE compra_direta.cod_objeto END)
                , (CASE WHEN objeto_licitacao.descricao IS NOT NULL THEN objeto_licitacao.descricao
                    ELSE objeto_compra_direta.descricao END)
                , (CASE WHEN licitacao.cod_modalidade IS NOT NULL THEN licitacao.cod_modalidade
                    ELSE compra_direta.cod_modalidade END)
                , (CASE WHEN modalidade_licitacao.descricao IS NOT NULL THEN modalidade_licitacao.descricao
                    ELSE modalidade_compra_direta.descricao END)
                , (CASE WHEN edital.condicoes_pagamento IS NOT NULL THEN edital.condicoes_pagamento
                      ELSE compra_direta.condicoes_pagamento END)
                , (CASE WHEN edital.local_entrega_material IS NOT NULL THEN edital.local_entrega_material
                    ELSE '' END)
                , (CASE WHEN licitacao.cod_licitacao IS NOT NULL THEN 'licitacao'
                    ELSE 'compra_direta' END )
                , julgamento_item.cgm_fornecedor
                , sw_cgm.nom_cgm
                , ordem.cod_ordem
                , ordem.exercicio
                , TO_CHAR(ordem.timestamp, 'dd/mm/yyyy')
                , ordem.tipo ";

        return $stSql;
    }

    public function recuperaTotaisOrdem(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaTotaisOrdem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaTotaisOrdem()
    {
        $stSql.= "
                SELECT coalesce(sum(ordem_item.quantidade),0.00) as total_oc
                     , coalesce(sum(ordem_item_anulacao.quantidade),0.00) as total_oc_anulado
                     , lancamento_material.quantidade as total_atendido
                  FROM compras.ordem_item
                  LEFT JOIN compras.ordem_item_anulacao
                         ON ( ordem_item.exercicio		  = ordem_item_anulacao.exercicio
                              AND ordem_item.cod_entidade     = ordem_item_anulacao.cod_entidade
                              AND ordem_item.cod_ordem        = ordem_item_anulacao.cod_ordem
                              AND ordem_item.cod_pre_empenho  = ordem_item_anulacao.cod_pre_empenho
                              AND ordem_item.num_item         = ordem_item_anulacao.num_item
                              AND ordem_item.tipo             = ordem_item_anulacao.tipo
                            )

                  LEFT JOIN ( SELECT SUM( lancamento_material.quantidade ) AS quantidade
                                   , nota_fiscal_fornecedor.exercicio_lancamento AS exercicio
                                   , nota_fiscal_fornecedor_ordem.cod_ordem
                                   , nota_fiscal_fornecedor_ordem.cod_entidade

                                FROM compras.nota_fiscal_fornecedor

                               INNER JOIN almoxarifado.lancamento_material
                                       ON lancamento_material.exercicio_lancamento = nota_fiscal_fornecedor.exercicio_lancamento
                                          AND lancamento_material.cod_natureza         = nota_fiscal_fornecedor.cod_natureza
                                          AND lancamento_material.cod_natureza         = nota_fiscal_fornecedor.cod_natureza
                                          AND lancamento_material.tipo_natureza        = nota_fiscal_fornecedor.tipo_natureza
                                          AND lancamento_material.num_lancamento       = nota_fiscal_fornecedor.num_lancamento

                               INNER JOIN compras.nota_fiscal_fornecedor_ordem
                                       ON nota_fiscal_fornecedor_ordem.cgm_fornecedor = nota_fiscal_fornecedor.cgm_fornecedor
                                          AND nota_fiscal_fornecedor_ordem.cod_nota         = nota_fiscal_fornecedor.cod_nota

                               GROUP BY nota_fiscal_fornecedor.exercicio_lancamento
                                      , nota_fiscal_fornecedor_ordem.cod_ordem
                                      , nota_fiscal_fornecedor_ordem.cod_entidade

                            ) AS lancamento_material

                        ON  lancamento_material.exercicio    = ordem_item.exercicio
                            AND lancamento_material.cod_ordem    = ordem_item.cod_ordem
                            AND lancamento_material.cod_entidade = ordem_item.cod_entidade
                            AND ordem_item.tipo         = '".$this->getDado('tipo')."'

                WHERE ordem_item.exercicio='".$this->getDado('exercicio')."'
                  AND ordem_item.cod_entidade=".$this->getDado('cod_entidade')."
                  AND ordem_item.cod_ordem=".$this->getDado('cod_ordem')."
                  AND ordem_item.tipo='".$this->getDado('tipo')."'
                GROUP BY lancamento_material.quantidade";

        return $stSql;
    }

    public function recuperaMotivo(&$rsRecordSet,$stFiltro,$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaMotivo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaMotivo()
    {
        $stSql.= "
                SELECT motivo
                FROM    compras.ordem_anulacao
                WHERE";

        return $stSql.$stFiltro;
    }
}
