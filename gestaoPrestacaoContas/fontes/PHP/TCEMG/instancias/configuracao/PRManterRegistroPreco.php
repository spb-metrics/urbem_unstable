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
  * Página de processamento para Configurar IDE
  * Data de criação : 07/01/2014
  * 
  * @author Analista:    Eduardo Paculski Schitz
  * @author Programador: Franver Sarmento de Moraes
  * 
  * @ignore
  * 
  * $Id: PRManterRegistroPreco.php 62158 2015-04-01 12:14:53Z franver $
  * $Date: 2015-04-01 09:14:53 -0300 (Qua, 01 Abr 2015) $
  * $Author: franver $
  * $Rev: 62158 $
  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRegistroPrecosOrgao.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGItemRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGEmpenhoRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGLoteRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRegistroPrecosOrgaoItem.class.php";


// Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroPreco";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTTCEMGRegistroPrecos = new TTCEMGRegistroPrecos();

Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento( $obTTCEMGRegistroPrecos );

if (empty($stAcao)) {
    $stAcao = 'incluir';
}

$obErro = new Erro;

switch ($stAcao) {

    case 'excluir' :
        $obTTCEMGRegistroPrecosOrgaoItem = new TTCEMGRegistroPrecosOrgaoItem();
        $obTTCEMGRegistroPrecosOrgaoItem->setDado('numero_registro_precos'    , $request->get('inNroRegistroPrecos'));
        $obTTCEMGRegistroPrecosOrgaoItem->setDado('exercicio_registro_precos' , $request->get('stExercicioRegistroPrecos'));
        $obTTCEMGRegistroPrecosOrgaoItem->setDado('cod_entidade'              , $request->get('inCodEntidade'));
        $obTTCEMGRegistroPrecosOrgaoItem->setDado('interno'                   , $request->get('boInterno'));
        $obTTCEMGRegistroPrecosOrgaoItem->setDado('numcgm_gerenciador'        , $request->get('numcgmGerenciador'));
        $obErro = $obTTCEMGRegistroPrecosOrgaoItem->exclusao();
        
        $obTTCEMGItemRegistroPrecos = new TTCEMGItemRegistroPrecos();
        $obTTCEMGItemRegistroPrecos->setDado('numero_registro_precos' , $request->get('inNroRegistroPrecos'));
        $obTTCEMGItemRegistroPrecos->setDado('exercicio'              , $request->get('stExercicioRegistroPrecos'));
        $obTTCEMGItemRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
        $obTTCEMGItemRegistroPrecos->setDado('interno'                , $request->get('boInterno'));
        $obTTCEMGItemRegistroPrecos->setDado('numcgm_gerenciador'     , $request->get('numcgmGerenciador'));
        $obErro = $obTTCEMGItemRegistroPrecos->exclusao();
        
        $obTTCEMGRegistroPrecosOrgao = new TTCEMGRegistroPrecosOrgao();
        $obTTCEMGRegistroPrecosOrgao->setDado('numero_registro_precos'   , $request->get('inNroRegistroPrecos'));
        $obTTCEMGRegistroPrecosOrgao->setDado('exercicio_registro_precos', $request->get('stExercicioRegistroPrecos'));
        $obTTCEMGRegistroPrecosOrgao->setDado('cod_entidade'             , $request->get('inCodEntidade'));
        $obTTCEMGRegistroPrecosOrgao->setDado('interno'                  , $request->get('boInterno'));
        $obTTCEMGRegistroPrecosOrgao->setDado('numcgm_gerenciador'       , $request->get('numcgmGerenciador'));
        $obErro = $obTTCEMGRegistroPrecosOrgao->exclusao();
        
        $obTTCEMGLoteRegistroPrecos = new TTCEMGLoteRegistroPrecos();
        $obTTCEMGLoteRegistroPrecos->setDado('numero_registro_precos' , $request->get('inNroRegistroPrecos'));
        $obTTCEMGLoteRegistroPrecos->setDado('exercicio'              , $request->get('stExercicioRegistroPrecos'));
        $obTTCEMGLoteRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
        $obTTCEMGLoteRegistroPrecos->setDado('interno'                , $request->get('boInterno'));
        $obTTCEMGLoteRegistroPrecos->setDado('numcgm_gerenciador'     , $request->get('numcgmGerenciador'));
        $obErro = $obTTCEMGLoteRegistroPrecos->exclusao();

        $obTTCEMGEmpenhoRegistroPrecos = new TTCEMGEmpenhoRegistroPrecos();
        $obTTCEMGEmpenhoRegistroPrecos->setDado('numero_registro_precos' , $request->get('inNroRegistroPrecos'));
        $obTTCEMGEmpenhoRegistroPrecos->setDado('exercicio'              , $request->get('stExercicioRegistroPrecos'));
        $obTTCEMGEmpenhoRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
        $obTTCEMGEmpenhoRegistroPrecos->setDado('interno'                , $request->get('boInterno'));
        $obTTCEMGEmpenhoRegistroPrecos->setDado('numcgm_gerenciador'     , $request->get('numcgmGerenciador'));
        $obErro = $obTTCEMGEmpenhoRegistroPrecos->exclusao();
        
        $obTTCEMGRegistroPrecos = new TTCEMGRegistroPrecos();
        $obTTCEMGRegistroPrecos->setDado('numero_registro_precos' , $request->get('inNroRegistroPrecos'));
        $obTTCEMGRegistroPrecos->setDado('exercicio'              , $request->get('stExercicioRegistroPrecos'));
        $obTTCEMGRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
        $obTTCEMGRegistroPrecos->setDado('interno'                , $request->get('boInterno'));
        $obTTCEMGRegistroPrecos->setDado('numcgm_gerenciador'     , $request->get('numcgmGerenciador'));
        $obErro = $obTTCEMGRegistroPrecos->exclusao();

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&inCodEntidade=".$request->get('inCodEntidade')."&stAcao=".$stAcao,'Registro de Preço - '.$request->get('inNroProcessoAdesao')."/".$request->get('stExercicioProcessoAdesao'),"excluir","excluir", Sessao::getId(), "../");
    break;

    default:

        $rsRegistroPrecos = new RecordSet();
        $arStCodigoProcesso = explode('/',$request->get('stCodigoProcesso'));

        $arItens = Sessao::read('arItens');
        $arEmpenhos = Sessao::read('arEmpenhos');
        $arOrgaos = Sessao::read('arOrgaos');
        $arOrgaoItemQuantitativos = Sessao::read('arOrgaoItemQuantitativos');
        
        if (!$obErro->ocorreu()) {
            $obTTCEMGRegistroPrecos->setDado('numero_registro_precos' , $arStCodigoProcesso[0]);
            $obTTCEMGRegistroPrecos->setDado('exercicio'              , $arStCodigoProcesso[1]);
            $obTTCEMGRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
            $obTTCEMGRegistroPrecos->setDado('interno'                , $request->get('boTipoRegPreco'));
            $obTTCEMGRegistroPrecos->setDado('numcgm_gerenciador'     , $request->get('inNumOrgaoGerenciador'));
            $obTTCEMGRegistroPrecos->recuperaPorChave( $rsRegistroPrecos, $boTransacao );
            
            $arProcessoLicitacao = explode('_', $request->get('stNroProcessoLicitacao'));

            $obTTCEMGRegistroPrecos->setDado('data_abertura_registro_precos'    , $request->get('dtAberturaProcesso'));
            $obTTCEMGRegistroPrecos->setDado('exercicio_licitacao'              , $request->get('stExercicioProcessoLicitacao'));
            $obTTCEMGRegistroPrecos->setDado('numero_processo_licitacao'        , $arProcessoLicitacao[0]);
            $obTTCEMGRegistroPrecos->setDado('codigo_modalidade_licitacao'      , $arProcessoLicitacao[1]);
            $obTTCEMGRegistroPrecos->setDado('numero_modalidade'                , $request->get('stNroModalidade'));
            $obTTCEMGRegistroPrecos->setDado('data_ata_registro_preco'          , $request->get('dtAtaRegistroPreco'));
            $obTTCEMGRegistroPrecos->setDado('data_ata_registro_preco_validade' , $request->get('dtValidadeAtaRegistroPreco'));
            $obTTCEMGRegistroPrecos->setDado('objeto'                           , $request->get('txtAreaObjeto'));
            $obTTCEMGRegistroPrecos->setDado('cgm_responsavel'                  , $request->get('inNumCGMResponsavel'));
            $obTTCEMGRegistroPrecos->setDado('desconto_tabela'                  , $request->get('inDescontoTabela'));
            $obTTCEMGRegistroPrecos->setDado('processo_lote'                    , $request->get('inProcessoPorLote'));

            if ($rsRegistroPrecos->getNumLinhas() < 0) {
                $obErro = $obTTCEMGRegistroPrecos->inclusao($boTransacao);
               
            } else {
                $obErro = $obTTCEMGRegistroPrecos->alteracao($boTransacao);
            }

            # Exclui sempre e inclui se necessário, lote e item.
            if (!$obErro->ocorreu()) {
                # Exclui todos os Quantitativos por Orgãos para o tipo de registro de preço.
                $obTTCEMGRegistroPrecosOrgaoItem = new TTCEMGRegistroPrecosOrgaoItem();
                $obTTCEMGRegistroPrecosOrgaoItem->setDado('cod_entidade'             , $request->get('inCodEntidade'));
                $obTTCEMGRegistroPrecosOrgaoItem->setDado('numero_registro_precos'   , $arStCodigoProcesso[0]);
                $obTTCEMGRegistroPrecosOrgaoItem->setDado('exercicio_registro_precos', $arStCodigoProcesso[1]);
                $obTTCEMGRegistroPrecosOrgaoItem->setDado('interno'                  , $request->get('boTipoRegPreco'));
                $obTTCEMGRegistroPrecosOrgaoItem->setDado('numcgm_gerenciador'       , $request->get('inNumOrgaoGerenciador'));
                $obErro = $obTTCEMGRegistroPrecosOrgaoItem->exclusao();
                
                # Exclui todos os Orgãos e as Unidades para o tipo de registro de preço.
                $obTTCEMGRegistroPrecosOrgao = new TTCEMGRegistroPrecosOrgao();
                $obTTCEMGRegistroPrecosOrgao->setDado('cod_entidade'             , $request->get('inCodEntidade'));
                $obTTCEMGRegistroPrecosOrgao->setDado('numero_registro_precos'   , $arStCodigoProcesso[0]);
                $obTTCEMGRegistroPrecosOrgao->setDado('exercicio_registro_precos', $arStCodigoProcesso[1]);
                $obTTCEMGRegistroPrecosOrgao->setDado('interno'                  , $request->get('boTipoRegPreco'));
                $obTTCEMGRegistroPrecosOrgao->setDado('numcgm_gerenciador'       , $request->get('inNumOrgaoGerenciador'));
                $obErro = $obTTCEMGRegistroPrecosOrgao->exclusao();
                
                # Exclui todos os Itens para o tipo de registro de preço.
                $obTTCEMGItemRegistroPrecos = new TTCEMGItemRegistroPrecos();
                $obTTCEMGItemRegistroPrecos->setDado('cod_entidade'             , $request->get('inCodEntidade'));
                $obTTCEMGItemRegistroPrecos->setDado('numero_registro_precos'   , $arStCodigoProcesso[0]);
                $obTTCEMGItemRegistroPrecos->setDado('exercicio'                , $arStCodigoProcesso[1]);
                $obTTCEMGItemRegistroPrecos->setDado('interno'                  , $request->get('boTipoRegPreco'));
                $obTTCEMGItemRegistroPrecos->setDado('numcgm_gerenciador'       , $request->get('inNumOrgaoGerenciador'));
                $obErro = $obTTCEMGItemRegistroPrecos->exclusao();
                
                # Exclui todos os Empenhos para o tipo de registro de preço.
                $obTTCEMGEmpenhoRegistroPrecos = new TTCEMGEmpenhoRegistroPrecos();
                $obTTCEMGEmpenhoRegistroPrecos->setDado('cod_entidade'             , $request->get('inCodEntidade'));
                $obTTCEMGEmpenhoRegistroPrecos->setDado('numero_registro_precos'   , $arStCodigoProcesso[0]);
                $obTTCEMGEmpenhoRegistroPrecos->setDado('exercicio'                , $arStCodigoProcesso[1]);
                $obTTCEMGEmpenhoRegistroPrecos->setDado('interno'                  , $request->get('boTipoRegPreco'));
                $obTTCEMGEmpenhoRegistroPrecos->setDado('numcgm_gerenciador'       , $request->get('inNumOrgaoGerenciador'));
                $obErro = $obTTCEMGEmpenhoRegistroPrecos->exclusao();

                # Exclui todos os Lotes para o tipo de registro de preço.
                $obTTCEMGLoteRegistroPrecos = new TTCEMGLoteRegistroPrecos();
                $obTTCEMGLoteRegistroPrecos->setDado('cod_entidade'             , $request->get('inCodEntidade'));
                $obTTCEMGLoteRegistroPrecos->setDado('numero_registro_precos'   , $arStCodigoProcesso[0]);
                $obTTCEMGLoteRegistroPrecos->setDado('exercicio'                , $arStCodigoProcesso[1]);
                $obTTCEMGLoteRegistroPrecos->setDado('interno'                  , $request->get('boTipoRegPreco'));
                $obTTCEMGLoteRegistroPrecos->setDado('numcgm_gerenciador'       , $request->get('inNumOrgaoGerenciador'));
                $obErro = $obTTCEMGLoteRegistroPrecos->exclusao();

                if (is_array($arOrgaos) && count($arOrgaos) > 0) {
                    foreach( $arOrgaos as $arOrgao ){
                        $arProcessoAdesao = explode('/',$arOrgao['stCodigoProcessoAdesao']);
                        
                        $arUnidadeOrcamentaria = explode('.',$arOrgao['stUnidadeOrcamentaria']);
                        
                        $obTTCEMGRegistroPrecosOrgao->setDado('exercicio_unidade'           ,$arOrgao['stExercicioOrgao']);
                        $obTTCEMGRegistroPrecosOrgao->setDado('num_orgao'                   ,(int)$arUnidadeOrcamentaria[0]);
                        $obTTCEMGRegistroPrecosOrgao->setDado('num_unidade'                 ,(int)$arUnidadeOrcamentaria[1]);
                        $obTTCEMGRegistroPrecosOrgao->setDado('participante'                ,($arOrgao['inNaturezaProcedimento'] == 1) ? true : false);
                        $obTTCEMGRegistroPrecosOrgao->setDado('numero_processo_adesao'      ,(int)$arProcessoAdesao[0]);
                        $obTTCEMGRegistroPrecosOrgao->setDado('exercicio_adesao'            ,$arProcessoAdesao[1]);
                        $obTTCEMGRegistroPrecosOrgao->setDado('dt_publicacao_aviso_intencao',$arOrgao['dtPublicacaoAvisoIntencao']);
                        $obTTCEMGRegistroPrecosOrgao->setDado('dt_adesao'                   ,$arOrgao['dtAdesao']);
                        $obTTCEMGRegistroPrecosOrgao->setDado('gerenciador'                 ,($arOrgao['inOrgaoGerenciador'] == 1)? true : false );
                        
                        $obTTCEMGRegistroPrecosOrgao->inclusao();
                    }
                }
                if (is_array($arItens) && count($arItens) > 0) {

                    $boProcessoPorLote = $request->get('inProcessoPorLote');
                    $inDescontoTabela  = $request->get('inDescontoTabela');
                    
                    foreach ($arItens as $item) {

                        # Cadastro de Lote quando necessário
                        $inCodLote = ((!empty($item['stCodigoLote']) && $item['stCodigoLote'] != 0) ? $item['stCodigoLote'] : 0);
                        $txtDescricaoLote = (!empty($item['txtDescricaoLote']) ? $item['txtDescricaoLote'] : '');

                        $obTTCEMGLoteRegistroPrecos->setDado('cod_lote' , $inCodLote);
                        $obTTCEMGLoteRegistroPrecos->recuperaPorChave( $rsLote );

                        $obTTCEMGLoteRegistroPrecos->setDado('descricao_lote' , $txtDescricaoLote);

                        $nuPercentualLote = ($boProcessoPorLote == true) ? $item['nuPercentualLote'] : 0;

                        $obTTCEMGLoteRegistroPrecos->setDado('percentual_desconto_lote' , $nuPercentualLote);
    
                        if ($rsLote->getNumLinhas() > 0) {
                            $obErro = $obTTCEMGLoteRegistroPrecos->alteracao();
                        } else {
                            $obErro = $obTTCEMGLoteRegistroPrecos->inclusao();
                        }

                        if ($inDescontoTabela == 2 || ($inDescontoTabela == 1 && $boProcessoPorLote == 1)) {
                            $nuPercentualItem = 0;
                        } else {
                            $nuPercentualItem = $item['nuPercentualItem'];
                        }
        
                        # Cadastro dos Itens do Registro de Preço, vinculação ao lote
                        $obTTCEMGItemRegistroPrecos->setDado('cod_lote'                       , $inCodLote);
                        $obTTCEMGItemRegistroPrecos->setDado('cod_item'                       , $item['inCodItem']);
                        $obTTCEMGItemRegistroPrecos->setDado('cgm_vencedor'                   , $item['inNumCGMVencedor']);
                        $obTTCEMGItemRegistroPrecos->setDado('num_item'                       , $item['inNumItemLote']);
                        $obTTCEMGItemRegistroPrecos->setDado('data_cotacao'                   , $item['dtCotacao']);
                        $obTTCEMGItemRegistroPrecos->setDado('vl_cotacao_preco_unitario'      , $item['nuVlReferencia']);
                        $obTTCEMGItemRegistroPrecos->setDado('quantidade_cotacao'             , $item['nuQuantidade']);
                        $obTTCEMGItemRegistroPrecos->setDado('preco_unitario'                 , $item['nuVlUnitario']);
                        $obTTCEMGItemRegistroPrecos->setDado('quantidade_licitada'            , $item['nuQtdeLicitada']);
                        $obTTCEMGItemRegistroPrecos->setDado('quantidade_aderida'             , $item['nuQtdeAderida']);
                        $obTTCEMGItemRegistroPrecos->setDado('percentual_desconto'            , $nuPercentualItem);
                        $obTTCEMGItemRegistroPrecos->setDado('cgm_fornecedor'                 , $item['inNumCGMVencedor']);
                        $obTTCEMGItemRegistroPrecos->setDado('ordem_classificacao_fornecedor' , $item['inOrdemClassifFornecedor']);
                        $obErro = $obTTCEMGItemRegistroPrecos->inclusao();

                    }
                }

                if (is_array($arOrgaoItemQuantitativos) && count($arOrgaoItemQuantitativos) > 0) {
                    foreach ( $arOrgaoItemQuantitativos as $arOrgaoItemQuantitativo ) {
        
                        $obTTCEMGRegistroPrecosOrgaoItem->setDado('exercicio_unidade',$arOrgaoItemQuantitativo['stExercicioOrgao']);
                        $obTTCEMGRegistroPrecosOrgaoItem->setDado('num_orgao'        ,$arOrgaoItemQuantitativo['inCodOrgaoQ']);
                        $obTTCEMGRegistroPrecosOrgaoItem->setDado('num_unidade'      ,$arOrgaoItemQuantitativo['inCodUnidadeQ']);
                        $obTTCEMGRegistroPrecosOrgaoItem->setDado('cod_lote'         ,$inCodLote);
                        $obTTCEMGRegistroPrecosOrgaoItem->setDado('cod_item'         ,$arOrgaoItemQuantitativo['inCodItemQ']);
                        $obTTCEMGRegistroPrecosOrgaoItem->setDado('cgm_fornecedor'   ,$arOrgaoItemQuantitativo['inCodFornecedorQ']);
                        $obTTCEMGRegistroPrecosOrgaoItem->setDado('quantidade'       ,$arOrgaoItemQuantitativo['nuQtdeOrgao']);
                        $obErro = $obTTCEMGRegistroPrecosOrgaoItem->inclusao();
                    }
                }
                if (is_array($arEmpenhos) && count($arEmpenhos) > 0) {
                    foreach ($arEmpenhos as $empenho) {

                        # Cadastro dos Empenhos do Registro de Preço
                        $obTTCEMGEmpenhoRegistroPrecos->setDado('cod_empenho'          , $empenho['cod_empenho']);
                        $obTTCEMGEmpenhoRegistroPrecos->setDado('exercicio_empenho'    , $empenho['exercicio']);
                        $obTTCEMGEmpenhoRegistroPrecos->setDado('cod_entidade_empenho' , $empenho['cod_entidade']);
                        $obErro = $obTTCEMGEmpenhoRegistroPrecos->inclusao();
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                # Limpa o array de empenhos que está na sessão.
                Sessao::remove('arEmpenhos');
                Sessao::remove('arItens');
                Sessao::remove('arOrgaos');
                Sessao::remove('arOrgaoItemQuantitativos');
        
                SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=alterar","Adesão a Registro de Preço.","incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }

    break;

}

Sessao::encerraExcecao();

?>