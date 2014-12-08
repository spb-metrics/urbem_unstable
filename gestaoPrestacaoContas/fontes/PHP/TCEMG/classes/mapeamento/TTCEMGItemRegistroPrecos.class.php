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
 **/
?>
<?php
/**
 * Classe de mapeamento da tabela tcemg.item_registro_precos
 * Data de Criação: 11/03/2014
 * 
 * @author Analista      : Eduardo Schitz
 * @author Desenvolvedor : Franver Sarmento de Moraes
 * 
 * @package URBEM
 * @subpackage Mapeamento
 * 
 * Casos de uso: uc-02.09.04
 *
 * $Id: TTCEMGItemRegistroPrecos.class.php 59719 2014-09-08 15:00:53Z franver $
 * $Revision: 59719 $
 * $Author: franver $
 * $Date: 2014-09-08 12:00:53 -0300 (Seg, 08 Set 2014) $
 * 
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGItemRegistroPrecos extends Persistente
{
    public function TTCEMGItemRegistroPrecos()
    {
        parent::Persistente();
        $this->setTabela('tcemg.item_registro_precos');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_entidade, numero_processo_adesao, exercicio_adesao, cod_lote, cod_item');
        
        $this->addCampo('cod_entidade'              , 'integer' , true , ''     , true  , true);
        $this->AddCampo('numero_processo_adesao'    , 'integer' , true , ''     , true  , true);
        $this->AddCampo('exercicio_adesao'          , 'varchar' , true ,'4'     , true  , true);
        $this->AddCampo('cod_lote'                  , 'integer' , true , ''     , true  , true);
        $this->AddCampo('cod_item'                  , 'integer' , true , ''     , true  , true);
        $this->AddCampo('num_item'                  , 'integer' , true , ''     , false , false);
        $this->AddCampo('data_cotacao'              , 'date'    , true , ''     , false , false);
        $this->AddCampo('vl_cotacao_preco_unitario' , 'numeric' , true , '14.4' , false , false);
        $this->AddCampo('quantidade_cotacao'        , 'numeric' , true , '14.4' , false , false);
        $this->AddCampo('preco_unitario'            , 'numeric' , true , '14.4' , false , false);
        $this->AddCampo('quantidade_licitada'       , 'numeric' , true , '14.4' , false , false);
        $this->AddCampo('quantidade_aderida'        , 'numeric' , true , '14.4' , false , false);
        $this->AddCampo('percentual_desconto'       , 'numeric' , true , '14.4' , false , false);
        $this->AddCampo('cgm_vencedor'              , 'integer' , true , ''     , false , true);
    }
    
    public function recuperaListaItem(&$rsRecordSet)
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaListaItem($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);

        return $obErro;
    }
    
    public function montaRecuperaListaItem()
    {
        $stSql = "
        SELECT irp.*
             , TO_CHAR(data_cotacao,'dd/mm/yyyy') AS data_cotacao
             , catalogo_item.descricao_resumida as descricao_resumida
             , sw_cgm.nom_cgm AS nomcgm_vencedor
             , sw_cgm.numcgm  AS numcgm_vencedor
             , unidade_medida.nom_unidade AS nom_unidade
             , lote_registro_precos.*
             , COALESCE(percentual_desconto, 0.00) AS percentual_desconto
             
          FROM tcemg.item_registro_precos irp

    INNER JOIN tcemg.lote_registro_precos                  
            ON lote_registro_precos.cod_entidade           = irp.cod_entidade
           AND lote_registro_precos.numero_processo_adesao = irp.numero_processo_adesao
           AND lote_registro_precos.exercicio_adesao       = irp.exercicio_adesao
           AND lote_registro_precos.cod_lote               = irp.cod_lote
          
    INNER JOIN almoxarifado.catalogo_item
            ON catalogo_item.cod_item = irp.cod_item

    INNER JOIN administracao.unidade_medida
            ON unidade_medida.cod_unidade  = catalogo_item.cod_unidade
           AND unidade_medida.cod_grandeza = catalogo_item.cod_grandeza

    INNER JOIN sw_cgm
            ON sw_cgm.numcgm = irp.cgm_vencedor

         WHERE irp.exercicio_adesao       = '".$this->getDado('exercicio_adesao')."'
           AND irp.numero_processo_adesao = ".$this->getDado('numero_processo_adesao')."
           AND irp.cod_entidade           = ".$this->getDado('cod_entidade')."
      
      ORDER BY irp.num_item";

        return $stSql;
    }
    
    public function __destruct(){}


}

?>